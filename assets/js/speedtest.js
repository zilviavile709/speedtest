/*
 * Speed Score — self-hosted internet speed test engine.
 *
 * Measures, in order:
 *   1. Unloaded latency + jitter (idle round trips)
 *   2. Request loss (small-request failure rate over HTTP)
 *   3. Download throughput at 100 kB / 1 MB / 8 MB, with latency probed under load
 *   4. Upload throughput at 100 kB / 1 MB / 4 MB, with latency probed under load
 *
 * Headline throughput is the 90th percentile of the largest transfer size, which
 * is the least latency-dominated. All payloads are random bytes, discarded after
 * transfer. Vanilla JS, no dependencies.
 */
(function () {
  'use strict';

  var BACKEND = 'backend/';

  var IDLE_PINGS = 20;
  var LOSS_REQUESTS = 40;      // small requests used for the loss estimate
  var PROBE_INTERVAL = 350;    // ms between latency probes while loaded
  var OVERHEAD = 1.06;         // HTTP/TCP/IP framing compensation

  // Per-size plans. `budget` caps wall-clock so slow links still finish.
  var DL_PLAN = [
    { key: 'dl100k', bytes: 100000,  count: 10, budget: 6000 },
    { key: 'dl1m',   bytes: 1000000, count: 8,  budget: 9000 },
    { key: 'dl8m',   bytes: 8000000, count: 5,  budget: 12000 }
  ];
  var UL_PLAN = [
    { key: 'ul100k', bytes: 100000,  count: 8, budget: 6000 },
    { key: 'ul1m',   bytes: 1000000, count: 6, budget: 9000 },
    { key: 'ul4m',   bytes: 4000000, count: 4, budget: 12000 }
  ];

  var $ = function (id) { return document.getElementById(id); };
  var panel = $('testPanel');
  var startBtn = $('startBtn');
  var runStatus = $('runStatus');
  var progressBar = $('progressBar');
  var resultCard = $('resultSummary');
  var detailsBox = $('detailsBox');
  var copyBtn = $('copyBtn');
  var shareBtn = $('shareBtn');

  var testRunning = false;
  var lastResult = null;

  // ---------- formatting ----------
  function fmt(x) { return x >= 100 ? Math.round(x).toString() : x.toFixed(2); }
  function fmtMs(x) { return Math.round(x).toString(); }
  function setText(id, v) { var el = $(id); if (el) el.textContent = v; }

  function token(name, fallback) {
    try {
      var v = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
      return v || fallback;
    } catch (e) { return fallback; }
  }

  // ---------- stats ----------
  function percentile(arr, p) {
    if (!arr.length) return 0;
    var s = arr.slice().sort(function (a, b) { return a - b; });
    var i = Math.round((s.length - 1) * p);
    return s[Math.min(s.length - 1, Math.max(0, i))];
  }
  function median(a) { return percentile(a, 0.5); }
  function mean(a) {
    if (!a.length) return 0;
    var t = 0;
    for (var i = 0; i < a.length; i++) t += a[i];
    return t / a.length;
  }
  function minOf(a) { return a.length ? Math.min.apply(null, a) : 0; }
  function maxOf(a) { return a.length ? Math.max.apply(null, a) : 0; }

  /** Mean absolute difference between consecutive samples — RFC 3550 style. */
  function jitterOf(a) {
    if (a.length < 2) return 0;
    var t = 0;
    for (var i = 1; i < a.length; i++) t += Math.abs(a[i] - a[i - 1]);
    return t / (a.length - 1);
  }

  function stabilityOf(samples) {
    if (samples.length < 3) return 100;
    var m = mean(samples);
    if (m <= 0) return 0;
    var v = 0;
    for (var i = 0; i < samples.length; i++) v += Math.pow(samples[i] - m, 2);
    return Math.max(0, Math.min(100, 100 - (Math.sqrt(v / samples.length) / m) * 100));
  }

  // ---------- measurement store ----------
  var M;
  function resetStore() {
    M = {
      lat: { idle: [], dl: [], ul: [] },
      dl: { dl100k: [], dl1m: [], dl8m: [] },
      ul: { ul100k: [], ul1m: [], ul4m: [] },
      loss: { sent: 0, lost: 0 }
    };
  }
  resetStore();

  // ---------- animated counters ----------
  function makeCounter(el, format) {
    var f = format || fmt;
    var shown = 0, target = 0, from = 0, t0 = 0, raf = null;
    function frame(now) {
      var p = Math.min(1, (now - t0) / 320);
      shown = from + (target - from) * (1 - Math.pow(1 - p, 3));
      if (el) el.textContent = f(Math.max(0, shown));
      raf = p < 1 ? requestAnimationFrame(frame) : null;
    }
    return {
      set: function (v) {
        if (!isFinite(v)) return;
        from = shown; target = v; t0 = performance.now();
        if (raf === null) raf = requestAnimationFrame(frame);
      },
      reset: function (text) {
        if (raf !== null) { cancelAnimationFrame(raf); raf = null; }
        shown = 0; target = 0;
        if (el) el.textContent = text;
      }
    };
  }
  var cDown = makeCounter($('resDown'));
  var cUp = makeCounter($('resUp'));

  // ---------- sample charts ----------
  // One small column chart per measurement group, with a labelled y axis.
  function makeChart(canvasId, unit) {
    var canvas = $(canvasId);
    var ctx = canvas && canvas.getContext ? canvas.getContext('2d') : null;
    var data = [];
    var color = '#0891b2';

    function niceMax(v) {
      if (v <= 0) return 1;
      var exp = Math.pow(10, Math.floor(Math.log(v) / Math.LN10));
      var f = v / exp;
      var n = f <= 1 ? 1 : f <= 2 ? 2 : f <= 5 ? 5 : 10;
      return n * exp;
    }
    function label(v) {
      if (unit === 'bps') {
        if (v >= 1e9) return (v / 1e9).toFixed(v % 1e9 ? 1 : 0) + 'G';
        if (v >= 1e6) return (v / 1e6).toFixed(v % 1e6 ? 1 : 0) + 'M';
        if (v >= 1e3) return (v / 1e3).toFixed(0) + 'k';
        return String(Math.round(v));
      }
      return String(Math.round(v));
    }
    function resize() {
      if (!canvas || !ctx) return;
      var dpr = window.devicePixelRatio || 1;
      var w = canvas.clientWidth, h = canvas.clientHeight;
      if (!w || !h) return;
      canvas.width = Math.round(w * dpr);
      canvas.height = Math.round(h * dpr);
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      draw();
    }
    function draw() {
      if (!ctx) return;
      var w = canvas.clientWidth, h = canvas.clientHeight;
      ctx.clearRect(0, 0, w, h);
      var padL = 34, padB = 4, padT = 6;
      var plotW = Math.max(1, w - padL - 4);
      var plotH = Math.max(1, h - padT - padB);
      var top = niceMax(maxOf(data) * 1.1);

      // axis + gridlines
      ctx.strokeStyle = token('--chart-grid', 'rgba(100,116,139,0.16)');
      ctx.fillStyle = token('--chart-label', 'rgba(71,85,105,0.85)');
      ctx.font = '9px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
      ctx.textAlign = 'right';
      ctx.textBaseline = 'middle';
      ctx.lineWidth = 1;
      for (var g = 0; g <= 4; g++) {
        var val = (top / 4) * g;
        var y = Math.round(padT + plotH - (plotH * g / 4)) - 0.5;
        ctx.beginPath();
        ctx.moveTo(padL, y);
        ctx.lineTo(w - 4, y);
        ctx.stroke();
        ctx.fillText(label(val), padL - 6, y);
      }
      if (!data.length) return;

      // columns
      var slots = Math.max(data.length, 6);
      var slotW = plotW / slots;
      var barW = Math.max(3, Math.min(18, slotW * 0.62));
      ctx.fillStyle = color;
      for (var i = 0; i < data.length; i++) {
        var frac = top > 0 ? data[i] / top : 0;
        var bh = Math.max(2, plotH * frac);
        var x = padL + slotW * i + (slotW - barW) / 2;
        var y2 = padT + plotH - bh;
        var r = Math.min(2, barW / 2);
        ctx.beginPath();
        ctx.moveTo(x, y2 + bh);
        ctx.lineTo(x, y2 + r);
        ctx.quadraticCurveTo(x, y2, x + r, y2);
        ctx.lineTo(x + barW - r, y2);
        ctx.quadraticCurveTo(x + barW, y2, x + barW, y2 + r);
        ctx.lineTo(x + barW, y2 + bh);
        ctx.closePath();
        ctx.fill();
      }
    }
    if (canvas && ctx) {
      resize();
      window.addEventListener('resize', resize);
    }
    return {
      push: function (v, c) { if (c) color = c; data.push(v); draw(); },
      set: function (arr, c) { if (c) color = c; data = arr.slice(); draw(); },
      reset: function () { data = []; draw(); },
      resize: resize
    };
  }

  var charts = {
    latIdle: makeChart('cLatIdle', 'ms'),
    latDown: makeChart('cLatDown', 'ms'),
    latUp: makeChart('cLatUp', 'ms'),
    dl100k: makeChart('cDl100k', 'bps'),
    dl1m: makeChart('cDl1m', 'bps'),
    dl8m: makeChart('cDl8m', 'bps'),
    ul100k: makeChart('cUl100k', 'bps'),
    ul1m: makeChart('cUl1m', 'bps'),
    ul4m: makeChart('cUl4m', 'bps')
  };
  function chartColors() {
    return {
      idle: token('--chart-idle', '#059669'),
      dl: token('--chart-dl', '#0891b2'),
      ul: token('--chart-ul', '#4f46e5')
    };
  }
  function resizeAllCharts() {
    for (var k in charts) if (charts.hasOwnProperty(k)) charts[k].resize();
  }

  // ---------- progress / status ----------
  var progress = 0;
  function setStatus(text, pct) {
    if (runStatus) runStatus.textContent = text;
    if (typeof pct === 'number') {
      progress = Math.max(progress, pct);
      if (progressBar) progressBar.style.width = progress + '%';
    }
  }

  // ---------- latency ----------
  function ping() {
    return new Promise(function (resolve) {
      var t0 = performance.now();
      var done = function (ok) { resolve({ ms: performance.now() - t0, ok: ok }); };
      var xhr = new XMLHttpRequest();
      xhr.open('GET', BACKEND + 'empty.php?r=' + Math.random(), true);
      xhr.timeout = 5000;
      xhr.onload = function () { done(xhr.status >= 200 && xhr.status < 400); };
      xhr.onerror = xhr.ontimeout = function () { done(false); };
      xhr.send();
    });
  }

  function measureIdleLatency() {
    var i = 0;
    var C = chartColors();
    function step() {
      if (i >= IDLE_PINGS) return Promise.resolve();
      i++;
      return ping().then(function (r) {
        if (r.ok) {
          M.lat.idle.push(r.ms);
          charts.latIdle.push(r.ms, C.idle);
          setText('nLatIdle', '(' + M.lat.idle.length + ')');
          setText('latIdle', fmtMs(median(M.lat.idle)));
          setText('jitIdle', fmtMs(jitterOf(M.lat.idle)));
        }
        setStatus('Measuring unloaded latency…', 4 + (i / IDLE_PINGS) * 8);
        return new Promise(function (r2) { setTimeout(r2, 40); }).then(step);
      });
    }
    return step();
  }

  function measureLoss() {
    var i = 0;
    function step() {
      if (i >= LOSS_REQUESTS) return Promise.resolve();
      i++;
      return ping().then(function (r) {
        M.loss.sent++;
        if (!r.ok) M.loss.lost++;
        var pct = (M.loss.lost / M.loss.sent) * 100;
        setText('resLoss', pct === 0 ? '0' : pct.toFixed(1));
        setStatus('Measuring request loss…', 12 + (i / LOSS_REQUESTS) * 6);
        return step();
      });
    }
    return step();
  }

  // Background probes while a transfer saturates the link.
  function startProbes(phase) {
    var stopped = false;
    var C = chartColors();
    var chart = phase === 'ul' ? charts.latUp : charts.latDown;
    var nId = phase === 'ul' ? 'nLatUp' : 'nLatDown';
    var vId = phase === 'ul' ? 'latUp' : 'latDown';
    var jId = phase === 'ul' ? 'jitUp' : 'jitDown';
    var col = phase === 'ul' ? C.ul : C.dl;
    function loop() {
      if (stopped) return;
      ping().then(function (r) {
        if (stopped || !r.ok) { if (!stopped) setTimeout(loop, PROBE_INTERVAL); return; }
        M.lat[phase].push(r.ms);
        chart.push(r.ms, col);
        setText(nId, '(' + M.lat[phase].length + ')');
        setText(vId, fmtMs(median(M.lat[phase])));
        setText(jId, fmtMs(jitterOf(M.lat[phase])));
        setTimeout(loop, PROBE_INTERVAL);
      });
    }
    setTimeout(loop, PROBE_INTERVAL);
    return function () { stopped = true; };
  }

  // ---------- throughput ----------
  function downloadOnce(bytes) {
    return new Promise(function (resolve) {
      var chunks = Math.max(1, Math.round(bytes / 1048576));
      var url = BACKEND + (bytes < 1048576
        ? 'garbage.php?bytes=' + bytes
        : 'garbage.php?ckSize=' + chunks) + '&r=' + Math.random();
      var xhr = new XMLHttpRequest();
      var t0 = 0, firstByte = 0, loaded = 0;
      xhr.open('GET', url, true);
      xhr.responseType = 'arraybuffer';
      xhr.timeout = 20000;
      xhr.onprogress = function (e) {
        if (!firstByte) firstByte = performance.now();
        loaded = e.loaded;
      };
      xhr.onload = function () {
        var end = performance.now();
        var n = xhr.response ? xhr.response.byteLength : loaded;
        // Time from first byte excludes connection setup, isolating throughput.
        var secs = (end - (firstByte || t0)) / 1000;
        resolve(secs > 0 && n > 0 ? (n * 8 * OVERHEAD) / secs : 0);
      };
      xhr.onerror = xhr.ontimeout = function () { resolve(0); };
      t0 = performance.now();
      xhr.send();
    });
  }

  var uploadBlobs = {};
  function blobOf(bytes) {
    if (uploadBlobs[bytes]) return uploadBlobs[bytes];
    var unit = new Uint8Array(65536);
    if (window.crypto && crypto.getRandomValues) crypto.getRandomValues(unit);
    else for (var i = 0; i < unit.length; i++) unit[i] = (Math.random() * 256) | 0;
    var parts = [], remaining = bytes;
    while (remaining > 0) {
      if (remaining >= unit.length) { parts.push(unit); remaining -= unit.length; }
      else { parts.push(unit.subarray(0, remaining)); remaining = 0; }
    }
    uploadBlobs[bytes] = new Blob(parts, { type: 'application/octet-stream' });
    return uploadBlobs[bytes];
  }

  function uploadOnce(bytes) {
    return new Promise(function (resolve) {
      var blob = blobOf(bytes);
      var xhr = new XMLHttpRequest();
      var t0 = performance.now();
      xhr.open('POST', BACKEND + 'empty.php?r=' + Math.random(), true);
      xhr.timeout = 20000;
      xhr.onload = function () {
        var secs = (performance.now() - t0) / 1000;
        resolve(secs > 0 ? (bytes * 8 * OVERHEAD) / secs : 0);
      };
      xhr.onerror = xhr.ontimeout = function () { resolve(0); };
      xhr.send(blob);
    });
  }

  /** Run one plan entry, honouring its time budget. */
  function runPlan(step, direction, chart, color, nId, onSample, pctFrom, pctTo, label) {
    var started = performance.now();
    var i = 0;
    function next() {
      var overBudget = performance.now() - started > step.budget;
      if (i >= step.count || (overBudget && i > 0)) return Promise.resolve();
      i++;
      var run = direction === 'dl' ? downloadOnce(step.bytes) : uploadOnce(step.bytes);
      return run.then(function (bps) {
        if (bps > 0) {
          M[direction][step.key].push(bps);
          chart.push(bps, color);
          setText(nId, '(' + M[direction][step.key].length + '/' + step.count + ')');
          onSample();
        }
        setStatus(label, pctFrom + ((pctTo - pctFrom) * i) / step.count);
        return next();
      });
    }
    return next();
  }

  function bestOf(group) {
    // Largest size that produced samples; p90 of it is the headline figure.
    var keys = Object.keys(group);
    for (var i = keys.length - 1; i >= 0; i--) {
      if (group[keys[i]].length) return percentile(group[keys[i]], 0.9) / 1e6;
    }
    return 0;
  }

  function measureDownload() {
    var stop = startProbes('dl');
    var C = chartColors();
    var chain = Promise.resolve();
    DL_PLAN.forEach(function (step, idx) {
      chain = chain.then(function () {
        return runPlan(step, 'dl', charts[step.key], C.dl, 'n' + step.key.charAt(0).toUpperCase() + step.key.slice(1),
          function () { cDown.set(bestOf(M.dl)); },
          18 + idx * 14, 18 + (idx + 1) * 14, 'Measuring download…');
      });
    });
    return chain.then(function () { stop(); });
  }

  function measureUpload() {
    var stop = startProbes('ul');
    var C = chartColors();
    var chain = Promise.resolve();
    UL_PLAN.forEach(function (step, idx) {
      chain = chain.then(function () {
        return runPlan(step, 'ul', charts[step.key], C.ul, 'n' + step.key.charAt(0).toUpperCase() + step.key.slice(1),
          function () { cUp.set(bestOf(M.ul)); },
          60 + idx * 13, 60 + (idx + 1) * 13, 'Measuring upload…');
      });
    });
    return chain.then(function () { stop(); });
  }

  // ---------- connection info ----------
  function loadInfo() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', BACKEND + 'getip.php?r=' + Math.random(), true);
    xhr.onload = function () {
      try {
        var d = JSON.parse(xhr.responseText);
        var c = d.client || {}, s = d.server || {};
        setText('resIP', c.ip || '—');
        setText('netProto', c.ip && c.ip.indexOf(':') > -1 ? 'IPv6' : 'IPv4');
        setText('netServer', s.location ? s.location + ' · ' + s.host : (s.host || '—'));
        var isp = c.isp || '';
        if (isp && c.asn) isp += ' (' + c.asn + ')';
        setText('netIsp', isp || '—');
      } catch (e) {}
    };
    xhr.send();
  }

  // ---------- network quality score ----------
  // Thresholds follow published requirements — see /methodology.php.
  function rateStreaming(r) {
    if (r.dl >= 15 && r.stab >= 70) return ['Great', ''];
    if (r.dl >= 5) return ['Good', r.stab < 70 ? 'throughput varies between transfers' : 'meets 1080p, short of 4K (15 Mbps)'];
    if (r.dl >= 3) return ['Average', 'meets 720p HD only (Netflix: 5 Mbps for 1080p)'];
    if (r.dl >= 1) return ['Poor', 'below Netflix’s 3 Mbps HD recommendation'];
    return ['Bad', 'insufficient for streaming video'];
  }

  function rateGaming(r) {
    if (r.idle < 30 && r.jitter < 10 && r.loaded < 100) return ['Great', ''];
    if (r.idle < 60 && r.jitter < 20 && r.loaded < 150) {
      return ['Good', r.loaded >= 100 ? 'latency rises under load' : ''];
    }
    if (r.idle < 100 && r.jitter < 40 && r.loaded < 300) {
      return ['Average', r.loaded >= 150 ? 'noticeable delay when the link is busy' : 'latency high for fast-paced titles'];
    }
    if (r.loaded >= 800) return ['Bad', 'severe bufferbloat — far past ITU-T G.114 limits'];
    if (r.idle >= 300) return ['Bad', 'round-trip latency exceeds the ITU-T G.114 usable range'];
    return ['Poor', r.loaded >= 300 ? 'latency spikes when the link is saturated' : 'latency and jitter above playable thresholds'];
  }

  function rateCalls(r) {
    if (r.ul >= 3.8 && r.jitter < 15 && r.loaded < 150) return ['Great', ''];
    if (r.ul >= 2.6 && r.jitter < 30) {
      return ['Good', r.loaded >= 150 ? 'calls may degrade during concurrent uploads' : 'meets Zoom 720p, short of 1080p (3.8 Mbps)'];
    }
    if (r.ul >= 1) return ['Average', r.jitter >= 30 ? 'jitter will cause audio artefacts' : 'meets Zoom 360p group video only'];
    if (r.ul >= 0.6) return ['Poor', 'below Zoom’s 1 Mbps group-video requirement'];
    return ['Bad', 'insufficient upload for two-way video'];
  }

  function paintQuality(r) {
    [['qStream', rateStreaming], ['qGame', rateGaming], ['qCall', rateCalls]].forEach(function (p) {
      var card = $(p[0]);
      if (!card) return;
      var out = p[1](r);
      var rate = card.querySelector('.q-rating');
      var note = card.querySelector('.q-note');
      if (rate) { rate.textContent = out[0]; rate.className = 'q-rating q-' + out[0].toLowerCase(); }
      if (note) note.textContent = out[1] || 'no limiting factor detected';
    });
  }

  function verdict(dl) {
    if (dl >= 100) return 'sufficient for 4K on multiple screens, large transfers and real-time applications.';
    if (dl >= 50) return 'sufficient for 4K streaming, video conferencing and online gaming.';
    if (dl >= 15) return 'sufficient for 4K streaming and general use across several devices.';
    if (dl >= 5) return 'sufficient for 1080p streaming on one or two devices.';
    if (dl >= 3) return 'adequate for 720p streaming and browsing.';
    return 'below the 3 Mbps Netflix recommends for HD streaming.';
  }

  function allDl() { return M.dl.dl100k.concat(M.dl.dl1m, M.dl.dl8m).map(function (v) { return v / 1e6; }); }
  function allUl() { return M.ul.ul100k.concat(M.ul.ul1m, M.ul.ul4m).map(function (v) { return v / 1e6; }); }

  function paintDetails(r) {
    var d = allDl(), u = allUl();
    setText('dtIdleMin', fmtMs(minOf(M.lat.idle)) + ' ms');
    setText('dtIdleMed', fmtMs(median(M.lat.idle)) + ' ms');
    setText('dtIdleP75', fmtMs(percentile(M.lat.idle, 0.75)) + ' ms');
    setText('dtLoadDl', M.lat.dl.length ? fmtMs(median(M.lat.dl)) + ' ms' : '—');
    setText('dtLoadUl', M.lat.ul.length ? fmtMs(median(M.lat.ul)) + ' ms' : '—');
    var delta = r.loaded - median(M.lat.idle);
    setText('dtDelta', (delta > 0 ? '+' : '') + fmtMs(delta) + ' ms');
    setText('dtDeltaNote', delta > 100
      ? 'Your connection slows noticeably under load (bufferbloat).'
      : (delta > 40 ? 'Mild extra delay when the link is busy.' : 'Latency holds steady under load.'));
    setText('dtDlRange', d.length ? fmt(minOf(d)) + ' / ' + fmt(mean(d)) + ' / ' + fmt(maxOf(d)) + ' Mbps' : '—');
    setText('dtUlRange', u.length ? fmt(minOf(u)) + ' / ' + fmt(mean(u)) + ' / ' + fmt(maxOf(u)) + ' Mbps' : '—');
    setText('dtStability', Math.round(r.stab) + '%');
    setText('dtSamples', (M.lat.idle.length + M.lat.dl.length + M.lat.ul.length) + ' latency probes, ' +
      (d.length + u.length) + ' transfer measurements');
    setText('dtLoss', M.loss.lost + ' of ' + M.loss.sent + ' requests failed (HTTP-level estimate)');
  }

  // ---------- orchestration ----------
  function resetUI() {
    resetStore();
    progress = 0;
    if (progressBar) progressBar.style.width = '0%';
    cDown.reset('—');
    cUp.reset('—');
    ['latIdle', 'latDown', 'latUp', 'jitIdle', 'jitDown', 'jitUp', 'resLoss'].forEach(function (id) {
      setText(id, '—');
    });
    ['nLatIdle', 'nLatDown', 'nLatUp'].forEach(function (id) { setText(id, '(0)'); });
    ['nDl100k', 'nDl1m', 'nDl8m', 'nUl100k', 'nUl1m', 'nUl4m'].forEach(function (id) { setText(id, '(0)'); });
    for (var k in charts) if (charts.hasOwnProperty(k)) charts[k].reset();
    if (detailsBox) detailsBox.open = false;
  }

  function finish() {
    var dl = bestOf(M.dl), ul = bestOf(M.ul);
    var loadedAll = M.lat.dl.concat(M.lat.ul);
    var r = {
      dl: dl,
      ul: ul,
      idle: M.lat.idle.length ? median(M.lat.idle) : 0,
      loaded: loadedAll.length ? median(loadedAll) : (M.lat.idle.length ? median(M.lat.idle) : 0),
      jitter: jitterOf(M.lat.idle),
      stab: stabilityOf(allDl()),
      loss: M.loss.sent ? (M.loss.lost / M.loss.sent) * 100 : 0
    };
    lastResult = r;
    cDown.set(dl);
    cUp.set(ul);
    paintQuality(r);
    paintDetails(r);
    setText('verdictText', verdict(dl));
    setStatus('Test complete.', 100);
    if (panel) panel.dataset.state = 'done';
    testRunning = false;
    startBtn.disabled = false;
    startBtn.textContent = 'Test Again';
    document.body.classList.remove('testing');
    if (resultCard) resultCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function startTest() {
    if (testRunning) return;
    testRunning = true;
    startBtn.disabled = true;
    startBtn.textContent = 'Testing…';
    document.body.classList.add('testing');
    resetUI();
    if (panel) panel.dataset.state = 'testing';
    resizeAllCharts();
    loadInfo();
    setStatus('Starting…', 2);

    measureIdleLatency()
      .then(measureLoss)
      .then(measureDownload)
      .then(measureUpload)
      .then(finish)
      .catch(function () {
        setStatus('Test interrupted. Please try again.', 100);
        testRunning = false;
        startBtn.disabled = false;
        startBtn.textContent = 'Start Test';
        document.body.classList.remove('testing');
      });
  }

  // ---------- share ----------
  function buildShareText() {
    var r = lastResult;
    if (!r) return '';
    return 'My internet speed test result:\n' +
      'Download: ' + fmt(r.dl) + ' Mbps · Upload: ' + fmt(r.ul) + ' Mbps\n' +
      'Latency: ' + fmtMs(r.idle) + ' ms unloaded, ' + fmtMs(r.loaded) + ' ms under load\n' +
      'Jitter: ' + fmtMs(r.jitter) + ' ms · Stability: ' + Math.round(r.stab) + '%\n' +
      'Streaming: ' + rateStreaming(r)[0] + ' · Gaming: ' + rateGaming(r)[0] + ' · Calls: ' + rateCalls(r)[0] + '\n' +
      'Tested at https://speedtest.scorelens.space/';
  }

  function flashCopied(ok) {
    if (!copyBtn) return;
    copyBtn.textContent = ok ? 'Copied!' : 'Copy failed';
    copyBtn.classList.toggle('copied', ok);
    setTimeout(function () {
      copyBtn.textContent = 'Copy results';
      copyBtn.classList.remove('copied');
    }, 1800);
  }

  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var text = buildShareText();
      if (!text) return;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () { flashCopied(true); }, function () { flashCopied(false); });
      } else {
        flashCopied(false);
      }
    });
  }

  if (shareBtn && navigator.share) {
    shareBtn.hidden = false;
    shareBtn.addEventListener('click', function () {
      var text = buildShareText();
      if (!text) return;
      navigator.share({ title: 'Internet speed test result', text: text, url: 'https://speedtest.scorelens.space/' })
        .catch(function () {});
    });
  }

  if (startBtn) startBtn.addEventListener('click', startTest);
  window.addEventListener('themechange', resizeAllCharts);
  loadInfo();
})();
