<?php
$pageTitle = 'How Speed Score Measures and Scores Your Connection';
$pageDesc = 'The full methodology behind our speed test: what we measure, how throughput and latency are calculated, and the published standards our quality scores are based on.';
$canonical = 'https://speedtest.scorelens.space/methodology.php';
include __DIR__ . '/includes/header.php';
?>
<main class="article">
  <h1>How we measure and score your connection</h1>
  <p class="meta">Speed Score · Updated July 2026</p>

  <p><strong>Every number on this site comes from transfers between your browser and our own server, and every rating threshold is taken from a published requirement rather than invented.</strong> This page documents both, so you can check our work or reproduce it yourself.</p>

  <h2>What the test measures</h2>
  <p>A run takes roughly 30 seconds and proceeds in four stages:</p>
  <ol>
    <li><strong>Unloaded latency and jitter.</strong> Twenty small requests are timed while the connection is otherwise quiet. We report the median round-trip time, and jitter as the mean absolute difference between consecutive samples.</li>
    <li><strong>Request loss.</strong> Forty further small requests are issued and failures counted. This is an HTTP-level estimate, not true IP packet loss — see the caveat below.</li>
    <li><strong>Download.</strong> Transfers at 100&nbsp;kB, 1&nbsp;MB and 8&nbsp;MB, repeated several times each. Throughput is timed from the first byte received, so connection setup is excluded.</li>
    <li><strong>Upload.</strong> The same approach in reverse, at 100&nbsp;kB, 1&nbsp;MB and 4&nbsp;MB.</li>
  </ol>
  <p>Throughout the download and upload stages we keep issuing latency probes in the background. Those samples are your <strong>loaded latency</strong> — how the connection behaves while it is actually busy, which is when problems occur.</p>

  <h2>How the headline figures are derived</h2>
  <p>The Download and Upload numbers are the <strong>90th percentile of the largest transfer size</strong> that completed. Small transfers are dominated by latency rather than bandwidth, so using the largest size gives a truer picture of capacity; taking the 90th percentile rather than the maximum discards one-off outliers without punishing a connection for a single slow sample.</p>
  <p>Measured throughput is multiplied by 1.06 to compensate for HTTP, TCP and IP framing, which is transferred over the wire but does not appear in the payload byte count.</p>
  <p><strong>Stability</strong> is 100 minus the coefficient of variation of all download samples, clamped to 0–100. A connection that delivers the same speed on every transfer scores near 100; one that swings wildly scores low even if its peak is high.</p>

  <h2>Latency under load, and why it matters</h2>
  <p>The gap between unloaded and loaded latency is <strong>bufferbloat</strong>: routers and modems queue packets they cannot forward fast enough, and everything interactive waits behind that queue. It is why a video call breaks up the moment someone starts a large upload, even on a fast plan.</p>
  <p>Our latency thresholds derive from <a href="https://www.itu.int/rec/T-REC-G.114" target="_blank" rel="noopener">ITU-T Recommendation G.114</a>, which sets one-way transmission time guidance: below 150&nbsp;ms most applications are essentially unaffected; 150–400&nbsp;ms is usable if the impact is understood; above 400&nbsp;ms is unacceptable for general network planning. Because we measure round-trip time rather than one-way, we apply those figures doubled — a 300&nbsp;ms round trip corresponds to G.114's 150&nbsp;ms one-way guidance. G.114 also notes that highly interactive tasks are affected at much lower delays, which is why our gaming thresholds are considerably stricter.</p>

  <h2>Where the quality-score thresholds come from</h2>

  <h3>Video streaming</h3>
  <p>Based on <a href="https://help.netflix.com/en/node/306" target="_blank" rel="noopener">Netflix's published connection-speed recommendations</a>: 3&nbsp;Mbps or higher for HD (720p), 5&nbsp;Mbps or higher for Full HD (1080p), and 15&nbsp;Mbps or higher for Ultra HD (4K).</p>
  <table class="speed-table">
    <tr><th>Rating</th><th>Requirement</th></tr>
    <tr><td>Great</td><td>15 Mbps or more, with stable throughput — meets Netflix's 4K recommendation</td></tr>
    <tr><td>Good</td><td>5 Mbps or more — meets the 1080p recommendation</td></tr>
    <tr><td>Average</td><td>3 Mbps or more — meets the 720p HD recommendation only</td></tr>
    <tr><td>Poor</td><td>1–3 Mbps — below the HD recommendation</td></tr>
    <tr><td>Bad</td><td>Under 1 Mbps</td></tr>
  </table>

  <h3>Video chatting</h3>
  <p>Based on <a href="https://library.zoom.com/admin-corner/network-management/quality-of-service-and-network-best-practices-explainer/calculating-bandwidth-usage-for-zoom-meetings-and-phone" target="_blank" rel="noopener">Zoom's published bandwidth requirements</a> for group meetings: 1&nbsp;Mbps upstream for 360p high-quality video, 2.6&nbsp;Mbps for 720p, and 3.8&nbsp;Mbps for 1080p. Because calls are two-way and interactive, jitter and loaded latency are weighted alongside upload throughput.</p>
  <table class="speed-table">
    <tr><th>Rating</th><th>Requirement</th></tr>
    <tr><td>Great</td><td>3.8 Mbps upload (Zoom 1080p), jitter under 15 ms, loaded latency under 150 ms</td></tr>
    <tr><td>Good</td><td>2.6 Mbps upload (Zoom 720p), jitter under 30 ms</td></tr>
    <tr><td>Average</td><td>1 Mbps upload — Zoom 360p group video only</td></tr>
    <tr><td>Poor</td><td>0.6–1 Mbps — below Zoom's group-video requirement</td></tr>
    <tr><td>Bad</td><td>Under 0.6 Mbps upload</td></tr>
  </table>

  <h3>Online gaming</h3>
  <p>Competitive gaming is bound by latency and its variability, not bandwidth. There is no single published standard equivalent to Netflix's or Zoom's tables, so these thresholds derive from G.114's observation that highly interactive tasks degrade well below the general 150&nbsp;ms one-way limit, combined with the round-trip budgets that game networking engineers commonly design against. We state that openly rather than implying a standard exists where it does not.</p>
  <table class="speed-table">
    <tr><th>Rating</th><th>Requirement</th></tr>
    <tr><td>Great</td><td>Unloaded latency under 30 ms, jitter under 10 ms, loaded latency under 100 ms</td></tr>
    <tr><td>Good</td><td>Under 60 ms, jitter under 20 ms, loaded latency under 150 ms</td></tr>
    <tr><td>Average</td><td>Under 100 ms, jitter under 40 ms, loaded latency under 300 ms</td></tr>
    <tr><td>Poor</td><td>Above those figures but within G.114's usable range</td></tr>
    <tr><td>Bad</td><td>Unloaded latency at or above 300 ms round trip, or loaded latency at or above 800 ms</td></tr>
  </table>

  <h2>Known limitations</h2>
  <ul>
    <li><strong>Request loss is not packet loss.</strong> True packet loss is measured at the IP layer. A browser cannot see that, so we count failed HTTP requests instead. It will detect a badly broken connection but will read 0% on a link with mild loss that TCP silently retransmits. We label it "request loss" rather than "packet loss" for that reason.</li>
    <li><strong>One server, one location.</strong> Every test runs against our server, so your result reflects the path between you and it. A test against a nearer server will usually show lower latency. Results are best compared against your own previous runs on this same test.</li>
    <li><strong>Your device and Wi-Fi are part of the measurement.</strong> An old laptop, a distant access point or a congested channel will cap the result below what your line can deliver. Testing over Ethernet isolates the connection itself.</li>
    <li><strong>Browser limits apply.</strong> Transfers run through the browser's normal HTTP stack and are subject to its connection limits and scheduling, which is also what real websites experience.</li>
  </ul>

  <h2>What we store</h2>
  <p>Test results are not saved and are not tied to you. IP addresses are used transiently to run the test and to look up your network name and city for display; the geographic cache stores only a coarsened prefix, never a full address. See the <a href="/privacy.php">privacy policy</a> for the complete picture.</p>

  <div class="cta-box">
    <p><strong>Ready to see where your connection stands?</strong></p>
    <p><a href="/">Run a free internet speed test →</a></p>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
