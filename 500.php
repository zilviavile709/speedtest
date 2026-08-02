<?php
/**
 * 500 error page.
 *
 * Deliberately standalone — no includes, no config, no database. This page is
 * served precisely when the rest of the application is failing, so anything it
 * depends on is something that can take it down with it. Styles are inlined for
 * the same reason: a broken deploy shouldn't leave the user staring at unstyled
 * text. Keep it that way.
 */
http_response_code(500);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Something went wrong | Speed Score</title>
<meta name="robots" content="noindex, follow">
<link rel="icon" href="/favicon.ico" sizes="32x32">
<link rel="icon" href="/assets/img/logo-mark.svg" type="image/svg+xml">
<meta name="theme-color" content="#0e7490" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#070b14" media="(prefers-color-scheme: dark)">
<style>
  :root {
    --bg: #ffffff; --surface: #fbfcfe; --border: #dde5ef;
    --text: #0f172a; --text-dim: #5a6b83; --accent: #0e7490;
    color-scheme: light dark;
  }
  @media (prefers-color-scheme: dark) {
    :root {
      --bg: #070b14; --surface: #101a2c; --border: #22304d;
      --text: #eef3fb; --text-dim: #9db0cd; --accent: #38d9f0;
    }
  }
  * { box-sizing: border-box; }
  body {
    margin: 0; min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    padding: 24px;
    background: var(--bg); color: var(--text);
    font: 16px/1.6 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
          "Helvetica Neue", Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
  }
  .wrap { width: 100%; max-width: 520px; text-align: center; }
  .mark { width: 56px; height: 56px; border-radius: 15px; margin: 0 auto 22px; display: block; }
  .code {
    display: inline-block; margin-bottom: 12px;
    font-size: 0.78rem; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;
    color: var(--accent);
  }
  h1 { margin: 0 0 12px; font-size: 1.75rem; line-height: 1.25; letter-spacing: -0.02em; }
  p { margin: 0 0 18px; color: var(--text-dim); }
  .box {
    margin: 26px 0 20px; padding: 20px;
    background: var(--surface); border: 1px solid var(--border); border-radius: 16px;
  }
  .btn {
    display: inline-block; padding: 12px 26px; border-radius: 999px;
    background: var(--accent); color: #ffffff;
    font-weight: 650; text-decoration: none;
  }
  .btn:hover { opacity: 0.9; }
  .links { margin-top: 20px; font-size: 0.9rem; }
  .links a { color: var(--text-dim); text-decoration: none; margin: 0 9px; }
  .links a:hover { color: var(--text); text-decoration: underline; }
</style>
</head>
<body>
  <div class="wrap">
    <img class="mark" src="/assets/img/logo-mark.svg" width="56" height="56" alt="Speed Score">
    <div class="code">Error 500</div>
    <h1>Something went wrong on our end</h1>
    <p>The server hit an unexpected problem. This one is on us, not your connection — nothing is wrong with your internet.</p>

    <div class="box">
      <p style="margin:0 0 14px;"><strong>Try again in a moment</strong> — these are usually short-lived.</p>
      <a class="btn" href="/">Back to the speed test</a>
    </div>

    <div class="links">
      <a href="/blog/">Guides</a>
      <a href="/about.php">About</a>
      <a href="/contact.php">Report this</a>
    </div>
  </div>
</body>
</html>
