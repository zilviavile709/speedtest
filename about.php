<?php
$pageTitle = 'About Speed Score | Speed Score';
$pageDesc = 'Learn what Speed Score is, how our browser-based internet speed test measures ping, jitter, download and upload, and why we built a free, privacy-friendly tool.';
$canonical = 'https://speedtest.scorelens.space/about.php';
include __DIR__ . '/includes/header.php';
?>
<main class="article">
  <h1>About Speed Score</h1>
  <p>Speed Score is a free internet speed test that runs entirely in your web browser. Open the page, press the button, and within a few seconds you get a clear picture of how your connection is actually performing right now &mdash; no app to install, no Flash, no plugins, and no account to create.</p>

  <h2>How the test works</h2>
  <p>When you start a test, your browser talks directly to our own servers &mdash; we do not bounce your traffic through third-party test networks. The test measures four things:</p>
  <ul>
    <li><strong>Ping (latency):</strong> we send a series of small requests and time how long each round trip takes. Lower is better, and it matters most for gaming, video calls, and anything interactive.</li>
    <li><strong>Jitter:</strong> the variation between those ping times. A connection with low ping but high jitter can still feel choppy on a call.</li>
    <li><strong>Download speed:</strong> your browser fetches chunks of test data from our server and we measure how fast the data arrives, in megabits per second.</li>
    <li><strong>Upload speed:</strong> the same idea in reverse &mdash; your browser sends test data to our server and we time it.</li>
  </ul>
  <p>Everything happens over standard HTTPS, exactly the way real websites load, so the numbers reflect the kind of performance you actually experience day to day.</p>

  <h2>Why we built it</h2>
  <p>Most speed test sites have become heavy, cluttered, and nosy. Many want you to sign up, install something, or hand over data before you can run a simple test. We wanted the opposite: a tool that loads fast, works on any modern device, and respects your privacy. Speed Score does not require registration, does not store your test results, and does not build a profile of you. You run a test, you see the result, and that is the end of it.</p>
  <p>The site is offered as a free resource and is supported by advertising, which keeps it free for everyone without paywalls or sign-up walls.</p>

  <h2>Who it is for</h2>
  <p>Anyone who wants a quick, honest answer to the question "how fast is my internet right now?" That includes:</p>
  <ul>
    <li>People troubleshooting a slow connection or a laggy video call</li>
    <li>Anyone checking whether their ISP is delivering the speeds they pay for</li>
    <li>Remote workers and gamers who care about latency and jitter, not just raw bandwidth</li>
    <li>Users comparing Wi-Fi performance in different rooms, or Wi-Fi versus a wired connection</li>
  </ul>
  <p>Because results can vary with Wi-Fi conditions, network congestion, and your device, we recommend running the test a few times at different hours to get a realistic picture.</p>
  <h2>Who is behind Speed Score</h2>
  <p>Speed Score is built and maintained by the team at <a href="https://riocloudsolutions.com" target="_blank" rel="noopener">RioCloud Solutions</a>, a technology company based in Chandigarh, India, established in 2020. RioCloud has worked with 100+ brands across 12+ countries on cloud infrastructure, web development, AI automation, SEO, and performance marketing &mdash; and Speed Score is one of the free tools we publish for the wider web community.</p>
  <p>Running cloud and web projects for clients every day means we constantly deal with real-world network performance, so building a clean, honest speed test was a natural fit. The same engineering standards we apply to client work &mdash; fast load times, no bloat, privacy by default &mdash; are the standards this tool is built on.</p>

  <p>Questions or suggestions? We would love to hear from you &mdash; see our <a href="/contact.php">contact page</a>.</p>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
