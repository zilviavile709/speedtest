<?php
$pageTitle = 'Mbps vs MB/s: The Difference Explained Simply';
$pageDesc = 'Mbps and MB/s are not the same thing — one is 8x bigger. Learn the difference between megabits and megabytes, with a simple conversion table and examples.';
$canonical = 'https://speedtest.scorelens.space/blog/mbps-vs-mbs.php';
$extraHead = <<<'HTML'
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Is 100 Mbps the same as 100 MB/s?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "No. 100 Mbps (megabits per second) equals only 12.5 MB/s (megabytes per second), because one byte contains eight bits. A connection that truly delivered 100 MB/s would be an 800 Mbps plan. Internet plans are sold in megabits; file downloads are usually displayed in megabytes."
      }
    },
    {
      "@type": "Question",
      "name": "How many Mbps is 1 MB/s?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "1 MB/s equals 8 Mbps. To convert megabytes per second to megabits per second, multiply by 8; to go the other way, divide by 8. So a download running at 25 MB/s is using about 200 Mbps of connection speed."
      }
    },
    {
      "@type": "Question",
      "name": "Why does my download show 12 MB/s on a 100 Mbps plan?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "That is your plan working correctly. 100 megabits per second divided by 8 equals 12.5 megabytes per second, which is exactly what download managers, browsers and game launchers display. Real-world protocol overhead typically trims another few percent, so 11–12 MB/s is a healthy result."
      }
    }
  ]
}
</script>
HTML;
include __DIR__ . '/../includes/header.php';
?>
<main class="article">
  <h1>Mbps vs MB/s: The Difference Explained Simply</h1>
  <p class="meta">Speed Score Guides · Updated July 2026</p>

  <p><strong>Mbps means megabits per second and measures connection speed; MB/s means megabytes per second and measures file transfer speed. One byte equals eight bits, so divide Mbps by 8 to get MB/s. A 100 Mbps connection therefore downloads files at about 12.5 MB/s — nothing is broken.</strong></p>

  <p>This single lowercase letter — the difference between Mb and MB — is responsible for an enormous share of "my internet is slow" complaints, angry calls to ISPs, and confused forum posts. You buy a "100 meg" plan, you start a download, Steam shows 12 MB/s, and it looks like you're getting one-eighth of what you paid for. You're not. You're getting exactly what you paid for, displayed in a different unit. Here's the whole story, simply.</p>

  <h2>Bits and Bytes: The 30-Second Version</h2>
  <p>A <strong>bit</strong> is the smallest unit of digital information — a single 1 or 0. A <strong>byte</strong> is a group of 8 bits, and it's the unit files are measured in: a photo might be 3 megabytes (MB), a game 80 gigabytes (GB).</p>
  <ul>
    <li><strong>Mbps</strong> = <strong>megabits</strong> per second, written with a lowercase b. This is how network speeds are measured — your internet plan, your Wi-Fi link, your speed test result.</li>
    <li><strong>MB/s</strong> = <strong>megabytes</strong> per second, written with a capital B. This is how file transfer speeds are usually displayed — browser downloads, Steam, file copies.</li>
  </ul>
  <p>Since 1 byte = 8 bits, the conversion is a single division: <strong>MB/s = Mbps ÷ 8</strong>. Going the other way, <strong>Mbps = MB/s × 8</strong>. That's genuinely all the math there is.</p>

  <h2>Why Two Units Exist at All</h2>
  <p>It's not a marketing trick, even if it works out nicely for marketers. Networking has always been measured in bits per second because network equipment transmits individual bits over a wire or radio signal — the byte grouping only matters once data is stored. Storage and files, meanwhile, have always been measured in bytes. So your ISP quotes the network unit (bits) and your download manager shows the storage unit (bytes), and both are being internally consistent. The confusion happens in the gap between them — and yes, "up to 1,000 Mbps!" does sound better than "up to 125 MB/s," which is why providers are in no hurry to clarify.</p>

  <h2>Conversion Table: Plan Speed vs Real Download Speed</h2>
  <p>Here's what common plan speeds actually look like as file downloads, plus how long each takes to pull down a 10 GB file (roughly one modern game update):</p>

  <table class="speed-table">
    <tr><th>Plan speed (Mbps)</th><th>Max download (MB/s)</th><th>Time for a 10 GB file</th></tr>
    <tr><td>25 Mbps</td><td>~3.1 MB/s</td><td>~55 minutes</td></tr>
    <tr><td>50 Mbps</td><td>~6.25 MB/s</td><td>~27 minutes</td></tr>
    <tr><td>100 Mbps</td><td>~12.5 MB/s</td><td>~14 minutes</td></tr>
    <tr><td>200 Mbps</td><td>~25 MB/s</td><td>~7 minutes</td></tr>
    <tr><td>300 Mbps</td><td>~37.5 MB/s</td><td>~4.5 minutes</td></tr>
    <tr><td>500 Mbps</td><td>~62.5 MB/s</td><td>~2.7 minutes</td></tr>
    <tr><td>1,000 Mbps (gigabit)</td><td>~125 MB/s</td><td>~80 seconds</td></tr>
  </table>

  <p>In practice you'll land a little under these ceilings — protocol overhead, server limits and Wi-Fi losses typically cost 5–15% — so a 100 Mbps plan showing 11 MB/s in Steam is healthy, not shortchanged. If the gap is much bigger than that, something else is going on; our guide to <a href="/blog/why-is-my-internet-slower-than-advertised.php">why your internet is slower than advertised</a> covers the legitimate reasons and the ones worth complaining about.</p>

  <h2>A Real Example, Start to Finish</h2>
  <p>Say you're on a 300 Mbps plan and downloading an 80 GB game. First convert the connection: 300 ÷ 8 = 37.5 MB/s maximum. Assume ~90% real-world efficiency: about 34 MB/s. Then the time: 80 GB is 80,000 MB, and 80,000 ÷ 34 ≈ 2,350 seconds — roughly 39 minutes. If your launcher shows 33–35 MB/s during that download, your connection is delivering its full advertised speed, even though "35" looks nothing like "300."</p>

  <h2>Quick Rules to Never Get Confused Again</h2>
  <ul>
    <li><strong>Lowercase b = bits, capital B = bytes.</strong> The capitalization is the entire signal.</li>
    <li><strong>Plans and speed tests speak Mbps.</strong> Downloads speak MB/s.</li>
    <li><strong>Divide by 8</strong> to go from plan speed to expected download speed. Divide by 10 for a quick mental estimate that bakes in overhead.</li>
    <li><strong>MB vs MiB:</strong> some technical tools use mebibytes (MiB, 1,048,576 bytes) instead of megabytes (1,000,000 bytes). The ~5% difference occasionally explains tiny mismatches between tools, and it's nothing to worry about.</li>
  </ul>
  <p>Once the units click, speed numbers everywhere start making sense — including how much you actually need. A 4K Netflix stream, for instance, uses about 15–25 Mbps (only ~2–3 MB/s), which is why even mid-tier plans stream 4K comfortably; see our breakdown of <a href="/blog/speed-for-streaming-4k.php">internet speed for Netflix and 4K streaming</a>. And to see what your own connection delivers in Mbps, <a href="/">run a free internet speed test</a> and try the ÷8 conversion on your result.</p>

  <div class="cta-box"><p><strong>Test your speed, divide by 8, and you'll know exactly what download speed to expect from your plan.</strong></p><p><a href="/">Run a free internet speed test →</a></p></div>

  <h2>Frequently asked questions</h2>
  <h3>Is 100 Mbps the same as 100 MB/s?</h3>
  <p>No. 100 Mbps (megabits per second) equals only 12.5 MB/s (megabytes per second), because one byte contains eight bits. A connection that truly delivered 100 MB/s would be an 800 Mbps plan. Internet plans are sold in megabits; file downloads are usually displayed in megabytes.</p>

  <h3>How many Mbps is 1 MB/s?</h3>
  <p>1 MB/s equals 8 Mbps. To convert megabytes per second to megabits per second, multiply by 8; to go the other way, divide by 8. So a download running at 25 MB/s is using about 200 Mbps of connection speed.</p>

  <h3>Why does my download show 12 MB/s on a 100 Mbps plan?</h3>
  <p>That is your plan working correctly. 100 megabits per second divided by 8 equals 12.5 megabytes per second, which is exactly what download managers, browsers and game launchers display. Real-world protocol overhead typically trims another few percent, so 11–12 MB/s is a healthy result.</p>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
