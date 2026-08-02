/* Theme toggle. The initial theme is applied inline in <head> to avoid a flash;
   this only handles switching and persistence. */
(function () {
  'use strict';
  var root = document.documentElement;
  var btn = document.getElementById('themeToggle');
  var KEY = 'speedscore-theme';

  function isDark() { return root.getAttribute('data-theme') === 'dark'; }

  function apply(dark) {
    if (dark) root.setAttribute('data-theme', 'dark');
    else root.removeAttribute('data-theme');
    if (btn) {
      btn.setAttribute('aria-label', dark ? 'Switch to light theme' : 'Switch to dark theme');
      btn.setAttribute('title', dark ? 'Switch to light theme' : 'Switch to dark theme');
    }
    // Let the speed-test charts repaint in the new palette.
    window.dispatchEvent(new CustomEvent('themechange'));
  }

  apply(isDark());

  if (btn) {
    btn.addEventListener('click', function () {
      var dark = !isDark();
      apply(dark);
      try { localStorage.setItem(KEY, dark ? 'dark' : 'light'); } catch (e) {}
    });
  }

  // Follow the OS only while the visitor hasn't made an explicit choice.
  if (window.matchMedia) {
    var mq = window.matchMedia('(prefers-color-scheme: dark)');
    var onChange = function (e) {
      var stored = null;
      try { stored = localStorage.getItem(KEY); } catch (err) {}
      if (!stored) apply(e.matches);
    };
    if (mq.addEventListener) mq.addEventListener('change', onChange);
    else if (mq.addListener) mq.addListener(onChange);
  }
})();
