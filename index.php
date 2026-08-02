<?php
$pageTitle = 'Free Internet Speed Test – Check WiFi Download & Upload';
$pageDesc = 'Run a free internet speed test in seconds. Check your WiFi download, upload, ping and jitter — no app, no signup. See if you\'re getting the speed you pay for.';
$canonical = 'https://speedtest.scorelens.space/';
$extraHead = <<<'HTML'
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Speed Score Internet Speed Test",
  "url": "https://speedtest.scorelens.space/",
  "applicationCategory": "UtilityApplication",
  "operatingSystem": "Any",
  "browserRequirements": "Requires JavaScript",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" },
  "description": "Free browser-based internet speed test measuring download speed, upload speed, ping and jitter."
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "How does this internet speed test work?",
      "acceptedAnswer": { "@type": "Answer", "text": "The test downloads and uploads random data between your browser and our server for several seconds each, measuring how many megabits per second (Mbps) your connection can sustain. It also sends a series of small requests to measure ping (latency) and jitter (latency variation)." }
    },
    {
      "@type": "Question",
      "name": "What is a good internet speed?",
      "acceptedAnswer": { "@type": "Answer", "text": "For most households, 25 Mbps download is enough for HD streaming and browsing, 50–100 Mbps is comfortable for families and 4K streaming, and 200+ Mbps suits heavy use with many devices. Upload speed matters most for video calls and cloud backups — 5–10 Mbps is a good baseline." }
    },
    {
      "@type": "Question",
      "name": "Why is my speed lower than what my provider advertises?",
      "acceptedAnswer": { "@type": "Answer", "text": "Advertised speeds are 'up to' figures measured over a wired connection. Wi-Fi signal loss, network congestion at peak times, older routers, and device limits all reduce real-world results. Testing on a wired connection at different times of day gives the clearest picture." }
    },
    {
      "@type": "Question",
      "name": "What are ping and jitter?",
      "acceptedAnswer": { "@type": "Answer", "text": "Ping is the time in milliseconds a small packet takes to reach the server and return — lower is better. Jitter is how much that time varies between packets. Low ping and jitter matter most for gaming, video calls and live streaming." }
    },
    {
      "@type": "Question",
      "name": "Is this speed test free and private?",
      "acceptedAnswer": { "@type": "Answer", "text": "Yes. The test is completely free, runs in your browser with no app or signup, and we don't store your results. Test data is random bytes that are discarded immediately after transfer." }
    }
  ]
}
</script>
HTML;
include __DIR__ . '/includes/header.php';
?>

<main>
  <section class="hero container">
    <h1>Internet Speed Test</h1>
    <p class="sub">Measure your download and upload throughput, latency while idle and under load, jitter, and connection stability.</p>

    <div class="test-panel" id="testPanel" data-state="idle">

      <div class="speed-head">
        <h2 class="block-title">Your internet speed</h2>
        <div class="speed-grid">
          <div class="sp sp-key">
            <div class="sp-label">Download</div>
            <div class="sp-row"><span class="sp-val" id="resDown">—</span><span class="sp-unit">Mbps</span></div>
          </div>
          <div class="sp sp-key">
            <div class="sp-label">Upload</div>
            <div class="sp-row"><span class="sp-val" id="resUp">—</span><span class="sp-unit">Mbps</span></div>
          </div>
          <div class="sp">
            <div class="sp-label">Latency</div>
            <div class="sp-trio">
              <div><span class="sp-sub" id="latIdle">—</span><em>unloaded</em></div>
              <div><span class="sp-sub" id="latDown">—</span><em>download</em></div>
              <div><span class="sp-sub" id="latUp">—</span><em>upload</em></div>
            </div>
          </div>
          <div class="sp">
            <div class="sp-label">Jitter</div>
            <div class="sp-trio">
              <div><span class="sp-sub" id="jitIdle">—</span><em>unloaded</em></div>
              <div><span class="sp-sub" id="jitDown">—</span><em>download</em></div>
              <div><span class="sp-sub" id="jitUp">—</span><em>upload</em></div>
            </div>
          </div>
          <div class="sp">
            <div class="sp-label">Request loss</div>
            <div class="sp-row"><span class="sp-val sp-sm" id="resLoss">—</span><span class="sp-unit">%</span></div>
          </div>
        </div>

        <div class="run-row">
          <button class="btn-start" id="startBtn">Start Test</button>
          <p class="run-status" id="runStatus">Runs for about 30 seconds. No app or sign-in required.</p>
          <div class="progress-track" aria-hidden="true"><div class="progress-bar" id="progressBar"></div></div>
        </div>
      </div>

      <div class="result-summary reveal-done" id="resultSummary" role="status" aria-live="polite">
        <strong>Summary —</strong> your connection is <span id="verdictText"></span>
      </div>

      <div class="quality-block reveal-done">
        <h2 class="block-title">Network quality score</h2>
        <div class="quality-row" id="qualityRow">
          <div class="quality-card" id="qStream">
            <span class="q-emoji" aria-hidden="true">📺</span>
            <div class="q-name">Video streaming</div>
            <div class="q-rating">—</div>
            <div class="q-note"></div>
          </div>
          <div class="quality-card" id="qGame">
            <span class="q-emoji" aria-hidden="true">🎮</span>
            <div class="q-name">Online gaming</div>
            <div class="q-rating">—</div>
            <div class="q-note"></div>
          </div>
          <div class="quality-card" id="qCall">
            <span class="q-emoji" aria-hidden="true">📞</span>
            <div class="q-name">Video chatting</div>
            <div class="q-rating">—</div>
            <div class="q-note"></div>
          </div>
        </div>
        <p class="block-note">Ratings follow published requirements — see <a href="/methodology.php">how we score</a>.</p>
      </div>

      <div class="netinfo">
        <h2 class="block-title">Server &amp; network</h2>
        <dl class="net-list">
          <div><dt>Connected via</dt><dd id="netProto">—</dd></div>
          <div><dt>Server location</dt><dd id="netServer">—</dd></div>
          <div><dt>Your network</dt><dd id="netIsp">—</dd></div>
          <div><dt>Your IP address</dt><dd id="resIP">—</dd></div>
        </dl>
      </div>

      <div class="meas-block reveal">
        <h2 class="block-title">Latency measurements</h2>
        <div class="meas-grid">
          <figure class="meas">
            <figcaption>Unloaded latency <span class="meas-n" id="nLatIdle">(0)</span></figcaption>
            <canvas class="meas-canvas" id="cLatIdle" aria-hidden="true"></canvas>
          </figure>
          <figure class="meas">
            <figcaption>Latency during download <span class="meas-n" id="nLatDown">(0)</span></figcaption>
            <canvas class="meas-canvas" id="cLatDown" aria-hidden="true"></canvas>
          </figure>
          <figure class="meas">
            <figcaption>Latency during upload <span class="meas-n" id="nLatUp">(0)</span></figcaption>
            <canvas class="meas-canvas" id="cLatUp" aria-hidden="true"></canvas>
          </figure>
        </div>
      </div>

      <div class="meas-block reveal">
        <h2 class="block-title">Download measurements</h2>
        <div class="meas-grid">
          <figure class="meas">
            <figcaption>100 kB download test <span class="meas-n" id="nDl100k">(0)</span></figcaption>
            <canvas class="meas-canvas" id="cDl100k" aria-hidden="true"></canvas>
          </figure>
          <figure class="meas">
            <figcaption>1 MB download test <span class="meas-n" id="nDl1m">(0)</span></figcaption>
            <canvas class="meas-canvas" id="cDl1m" aria-hidden="true"></canvas>
          </figure>
          <figure class="meas">
            <figcaption>8 MB download test <span class="meas-n" id="nDl8m">(0)</span></figcaption>
            <canvas class="meas-canvas" id="cDl8m" aria-hidden="true"></canvas>
          </figure>
        </div>
      </div>

      <div class="meas-block reveal">
        <h2 class="block-title">Upload measurements</h2>
        <div class="meas-grid">
          <figure class="meas">
            <figcaption>100 kB upload test <span class="meas-n" id="nUl100k">(0)</span></figcaption>
            <canvas class="meas-canvas" id="cUl100k" aria-hidden="true"></canvas>
          </figure>
          <figure class="meas">
            <figcaption>1 MB upload test <span class="meas-n" id="nUl1m">(0)</span></figcaption>
            <canvas class="meas-canvas" id="cUl1m" aria-hidden="true"></canvas>
          </figure>
          <figure class="meas">
            <figcaption>4 MB upload test <span class="meas-n" id="nUl4m">(0)</span></figcaption>
            <canvas class="meas-canvas" id="cUl4m" aria-hidden="true"></canvas>
          </figure>
        </div>
      </div>

      <div class="share-row reveal-done" id="shareRow">
        <button class="btn-ghost" id="copyBtn" type="button">Copy results</button>
        <button class="btn-ghost" id="shareBtn" type="button" hidden>Share</button>
      </div>

      <details class="details-box reveal-done" id="detailsBox">
        <summary>Measurement details</summary>
        <table class="detail-table">
          <tr><th>Unloaded latency (min / median / 75th)</th>
              <td><span id="dtIdleMin">—</span> · <span id="dtIdleMed">—</span> · <span id="dtIdleP75">—</span></td></tr>
          <tr><th>Latency while downloading (median)</th><td id="dtLoadDl">—</td></tr>
          <tr><th>Latency while uploading (median)</th><td id="dtLoadUl">—</td></tr>
          <tr><th>Bufferbloat (loaded − unloaded)</th><td><strong id="dtDelta">—</strong> <span id="dtDeltaNote" class="dt-note"></span></td></tr>
          <tr><th>Download min / avg / peak</th><td id="dtDlRange">—</td></tr>
          <tr><th>Upload min / avg / peak</th><td id="dtUlRange">—</td></tr>
          <tr><th>Throughput stability</th><td id="dtStability">—</td></tr>
          <tr><th>Samples collected</th><td id="dtSamples">—</td></tr>
          <tr><th>Request loss</th><td id="dtLoss">—</td></tr>
        </table>
      </details>
    </div>
  </section>

  <section class="section container">
    <h2>How the test works</h2>
    <div class="cards-3">
      <div class="card">
        <span class="emoji">📡</span>
        <h3>1. Ping &amp; jitter</h3>
        <p>We send a burst of tiny requests to our server and time each round trip. The fastest round trip is your ping; the variation between them is your jitter.</p>
      </div>
      <div class="card">
        <span class="emoji">⬇️</span>
        <h3>2. Download</h3>
        <p>Your browser pulls random data over several parallel connections for about 12 seconds, measuring how many megabits per second your line can sustain.</p>
      </div>
      <div class="card">
        <span class="emoji">⬆️</span>
        <h3>3. Upload</h3>
        <p>Then the direction flips: your device pushes random data to our server so we can measure your real-world upload capacity the same way.</p>
      </div>
    </div>
    <p style="margin-top:16px;">Throughout the download and upload stages we keep sending small timing probes in the background, so you also see <strong>how your latency behaves while the connection is busy</strong> — the number that decides whether a video call survives someone else starting a download.</p>
  </section>

  <section class="section container">
    <h2>Latency under load: the metric most speed tests hide</h2>
    <p>A speed test that only reports idle ping tells you how your connection behaves when nothing is happening — which is not when problems occur. Speed Score measures latency <strong>continuously</strong>, including while the line is saturated, and reports both numbers.</p>
    <p>The gap between them is <strong>bufferbloat</strong>: your router or modem queues up packets it can't send fast enough, and everything interactive gets stuck behind that queue. It's why a game lags the moment someone starts a large upload, even on a fast plan.</p>
    <table class="speed-table">
      <tr><th>Latency under load</th><th>What it means in practice</th></tr>
      <tr><td>Under 100 ms</td><td>Excellent — calls and games stay smooth even while other devices are downloading</td></tr>
      <tr><td>100–300 ms</td><td>Noticeable — occasional stutter in calls and lag spikes in fast-paced games</td></tr>
      <tr><td>Over 300 ms</td><td>Severe bufferbloat — expect dropped calls and unplayable lag whenever the line is busy</td></tr>
    </table>
    <p>If your loaded latency is far above your idle latency, the fix usually isn't a faster plan — it's enabling Smart Queue Management (SQM/fq_codel) on your router, or replacing an old modem. Our guide on <a href="/blog/ping-jitter-latency-explained.php">ping, jitter and latency</a> covers what to change.</p>
    <h3>Stability and connection quality scores</h3>
    <p>We also track how steady your throughput stays across the test (<strong>stability</strong>), then combine speed, latency, jitter and stability into plain-English ratings for the three things people actually care about: <strong>video streaming, online gaming and video calls</strong>. When a rating isn't "Great", we name the specific bottleneck rather than just showing you a number.</p>
  </section>

  <section class="section container">
    <h2>What do my results mean?</h2>
    <p>Speed is measured in <strong>Mbps (megabits per second)</strong>. Here's a quick guide to what different download speeds can handle:</p>
    <table class="speed-table">
      <tr><th>Download speed</th><th>What it handles comfortably</th></tr>
      <tr><td>0–5 Mbps</td><td>Email, basic browsing, music streaming on one device</td></tr>
      <tr><td>5–25 Mbps</td><td>HD video streaming, video calls, browsing on a few devices</td></tr>
      <tr><td>25–50 Mbps</td><td>4K streaming on one screen, online gaming, small households</td></tr>
      <tr><td>50–100 Mbps</td><td>Multiple 4K streams, large downloads, families with many devices</td></tr>
      <tr><td>100+ Mbps</td><td>Heavy simultaneous use, fast large-file downloads, smart homes</td></tr>
    </table>
    <p>Upload speed matters most for <strong>video calls, cloud backups and live streaming</strong> — aim for at least 5–10 Mbps. Ping below <strong>50 ms</strong> feels responsive; competitive gamers want under 30 ms. Read more in our guide: <a href="/blog/what-is-a-good-internet-speed.php">What is a good internet speed?</a></p>
  </section>

  <section class="section container">
    <h2>Tips for an accurate result</h2>
    <ul>
      <li><strong>Use a cable if you can.</strong> Wi-Fi almost always tests slower than a wired connection — that gap is your Wi-Fi, not your ISP.</li>
      <li><strong>Close heavy apps and downloads.</strong> Streaming, cloud sync and system updates share your bandwidth and lower the result.</li>
      <li><strong>Test more than once.</strong> Run the test at different times of day — evening slowdowns usually mean network congestion.</li>
      <li><strong>Sit near the router for Wi-Fi tests.</strong> Distance and walls can halve your throughput. See our <a href="/blog/how-to-make-wifi-faster.php">Wi-Fi speed guide</a>.</li>
    </ul>
  </section>

  <section class="section container">
    <h2>Frequently asked questions</h2>
    <details class="faq-item">
      <summary>Is this speed test really free?</summary>
      <div class="faq-body">Yes — completely free, unlimited tests, no signup, no app to install. It runs entirely in your browser.</div>
    </details>
    <details class="faq-item">
      <summary>Do you store my results or personal data?</summary>
      <div class="faq-body">No. Test data is random bytes discarded immediately after transfer, and we don't keep a history of your results. See our <a href="/privacy.php">privacy policy</a> for details.</div>
    </details>
    <details class="faq-item">
      <summary>Why do I get different results from other speed tests?</summary>
      <div class="faq-body">Every test measures the path between you and that test's server. Different server locations, routing and load produce different numbers — each is a valid measurement of a different path. For the clearest picture, compare trends over time on the same test.</div>
    </details>
    <details class="faq-item">
      <summary>Why is my Wi-Fi slower than my plan?</summary>
      <div class="faq-body">Wi-Fi adds overhead and loses signal with distance, walls and interference. An older router or crowded channel can cap you well below your plan. Our guide on <a href="/blog/why-is-my-internet-slower-than-advertised.php">why your internet is slower than advertised</a> walks through the usual causes.</div>
    </details>
    <details class="faq-item">
      <summary>What's more important — speed, ping or jitter?</summary>
      <div class="faq-body">It depends on what you do. Streaming needs download speed; gaming and video calls care much more about <a href="/blog/ping-jitter-latency-explained.php">ping and jitter</a>. A 500 Mbps line with 150 ms ping will still feel laggy in a game.</div>
    </details>
  </section>

  <section class="section container">
    <h2>Guides &amp; resources</h2>
    <div class="article-grid">
      <a class="article-card" href="/blog/what-is-a-good-internet-speed.php">
        <h3>What Is a Good Internet Speed?</h3>
        <p>Mbps explained for real life — how much speed you actually need for streaming, gaming and working from home.</p>
      </a>
      <a class="article-card" href="/blog/how-to-make-wifi-faster.php">
        <h3>15 Ways to Make Your Wi-Fi Faster</h3>
        <p>Practical fixes from router placement to channel congestion that genuinely improve your wireless speed.</p>
      </a>
      <a class="article-card" href="/blog/ping-jitter-latency-explained.php">
        <h3>Ping, Jitter &amp; Latency Explained</h3>
        <p>Why these three numbers matter more than raw speed for gaming, video calls and live streaming.</p>
      </a>
      <a class="article-card" href="/blog/why-is-my-internet-slower-than-advertised.php">
        <h3>Why Is My Internet Slower Than Advertised?</h3>
        <p>The real reasons behind the gap between your plan and your results — and how to test properly.</p>
      </a>
    </div>
  </section>
</main>

<script src="/assets/js/speedtest.js"></script>
<?php include __DIR__ . '/includes/footer.php'; ?>
