<?php
$pageTitle = 'Why Is My Internet Slower Than What I Pay For? | Speed Score';
$pageDesc = 'Paying for 300 Mbps but getting far less? Here are the real reasons — Wi-Fi loss, congestion, old routers, Mbps vs MB/s — and how to test and fix it properly.';
$canonical = 'https://speedtest.scorelens.space/blog/why-is-my-internet-slower-than-advertised.php';
include __DIR__ . '/../includes/header.php';
?>
<main class="article">
  <h1>Why Is My Internet Slower Than What I Pay For?</h1>
  <p class="meta">Speed Score Guides · Updated July 2026</p>

  <p>You pay for 300 Mbps. Your speed test says 140. Are you being ripped off? Sometimes, yes — but more often the gap has a mundane, fixable explanation somewhere between the street and your phone. This guide walks through the real reasons your measured speed falls short of the advertised number, how to test properly so you know which reason applies to you, and exactly what to say to your ISP if the problem really is on their end.</p>

  <h2>First, Rule Out the Classic Misunderstanding: Mbps vs MB/s</h2>
  <p>Before anything else, make sure you're comparing the same units. Internet plans are sold in <strong>megabits per second (Mbps)</strong>, but download managers, browsers and game launchers usually display <strong>megabytes per second (MB/s)</strong>. One byte is 8 bits, so:</p>
  <ul>
    <li>A 100 Mbps plan downloads at about <strong>12.5 MB/s</strong></li>
    <li>A 300 Mbps plan downloads at about <strong>37.5 MB/s</strong></li>
    <li>A 1000 Mbps (gigabit) plan downloads at about <strong>125 MB/s</strong></li>
  </ul>
  <p>Countless "my internet is slow" complaints are really just this: a Steam download showing 35 MB/s on a 300 Mbps plan is actually running at nearly full speed. If your numbers line up once you divide by 8, there's nothing to fix. (Also note: a single download server may itself be the bottleneck — your connection can only go as fast as the server sending the file.)</p>

  <h2>The Real Reasons for a Genuine Gap</h2>

  <h3>1. "Up to" means exactly that</h3>
  <p>Advertised speeds are maximums under ideal conditions, and the fine print says so. Regulators in many countries only require ISPs to deliver a percentage of the advertised figure. Consistently getting 85–95% of your plan speed on a wired connection is normal and generally considered fine. Getting 40% is not.</p>

  <h3>2. Wi-Fi overhead eats a big slice</h3>
  <p>This is the most common cause of a large gap. Wi-Fi loses speed to distance, walls, interference and protocol overhead — losing 30–60% of your plan speed on a phone two rooms from the router is completely typical, and it's not your ISP's fault. The tell-tale sign: a wired test at the router shows full speed while wireless tests don't. If that's your situation, the fix is better Wi-Fi, not a bigger plan — our guide to <a href="/blog/how-to-make-wifi-faster.php">15 ways to make Wi-Fi faster at home</a> covers the options, most of them free.</p>

  <h3>3. Peak-time congestion</h3>
  <p>Your neighborhood shares network capacity, especially on cable internet, where a whole street can sit on the same segment. Between roughly 7 p.m. and 11 p.m., when everyone streams at once, speeds can sag noticeably. Fiber suffers less; cable and DSL more. If your speed is great at 7 a.m. and poor at 9 p.m. every day, congestion is your answer — and it's one of the few problems on this list that only your ISP can fix (or a plan/technology change can route around).</p>

  <h3>4. Your router or modem is the bottleneck</h3>
  <p>Hardware has ceilings. A router from 2016 may max out around 100–200 Mbps over Wi-Fi regardless of your plan. An old modem (for cable users, one using the DOCSIS 2.0 or early 3.0 standard) can cap a fast plan all by itself. If you upgraded your plan but kept your old equipment, this is a prime suspect — especially if your speed plateaus at a suspiciously round number no matter what you do.</p>

  <h3>5. Too many devices sharing the pipe</h3>
  <p>Your plan speed is a total for the whole household, not a per-device promise. If a 4K stream, a cloud backup and a game update are running while you test, your result will be whatever's left over. Smart TVs, cameras and phones quietly downloading updates in the background are frequent hidden culprits.</p>

  <h3>6. Throttling and data policies</h3>
  <p>Some ISPs slow you down deliberately. Common versions: mobile and fixed-wireless plans that reduce speeds after a monthly data threshold, "deprioritization" during network congestion on budget plans, and (less commonly) slowing of specific traffic types like video or peer-to-peer. Check your plan's terms for phrases like "fair use policy," "data cap" or "deprioritization." A tell-tale sign of traffic-type throttling is normal speed-test results but consistently poor performance on one kind of service.</p>

  <h3>7. The device doing the testing</h3>
  <p>An older laptop or budget phone may not have the Wi-Fi hardware to reach high speeds, and a browser loaded with extensions or a VPN left running can drag results down. Testing on a gigabit plan with an old device will never show gigabit.</p>

  <h2>How to Test Properly (So Your Numbers Mean Something)</h2>
  <p>A single test on your phone from the couch proves almost nothing. To find out where the speed is going, do this:</p>
  <ol>
    <li><strong>Test wired first.</strong> Connect a computer to the router with an ethernet cable and <a href="/">run a speed test</a>. This measures what actually enters your home, with Wi-Fi taken out of the equation.</li>
    <li><strong>Quiet the network.</strong> Pause downloads, streams and backups on other devices, and turn off any VPN, before testing.</li>
    <li><strong>Test several times.</strong> Run three tests and take the middle result — any single run can be skewed by a momentary blip.</li>
    <li><strong>Test at different times of day.</strong> Morning, afternoon and 8–10 p.m. If the wired speed is fine off-peak but collapses every evening, you've diagnosed congestion.</li>
    <li><strong>Then test over Wi-Fi</strong> next to the router, and again in your worst room. The differences between wired, near-router and far-room results show you exactly where speed is being lost.</li>
    <li><strong>Write everything down</strong> — date, time, wired or wireless, and the download, upload and ping numbers. This record is your evidence if you need to escalate. (If your speeds look fine but things still feel laggy, the problem may be latency rather than bandwidth — see our <a href="/blog/ping-jitter-latency-explained.php">guide to ping, jitter and latency</a>.)</li>
  </ol>

  <h2>What to Say to Your ISP</h2>
  <p>If wired tests consistently show well under your plan speed — say, below 70–80% across multiple days and times — the problem is on the ISP's side of the wall, and you have a legitimate complaint. When you call:</p>
  <ul>
    <li><strong>Lead with the specifics:</strong> "I pay for 300 Mbps. Wired tests directly on the router over the past week average 110 Mbps, at multiple times of day. I have the dated results."</li>
    <li><strong>Pre-empt the script:</strong> Mention up front that you've already tested wired, rebooted the modem and router, and tested with no other devices active. This skips twenty minutes of first-tier troubleshooting.</li>
    <li><strong>Ask concrete questions:</strong> Is my modem on their approved list and current standard? Is there a known issue or congestion in my area? Can they check the line quality/signal levels from their end? Can they send a technician if it doesn't resolve?</li>
    <li><strong>Know your leverage:</strong> Many ISPs offer bill credits for sustained underperformance, and persistent shortfalls documented in writing strengthen a complaint to your national telecom regulator if it comes to that. Sometimes the most effective phrase is simply asking what it would take to cancel — retention departments can fix problems and prices that support lines can't.</li>
  </ul>

  <h2>The Bottom Line</h2>
  <p>A gap between advertised and actual speed usually breaks down like this: unit confusion (no problem at all), Wi-Fi losses (fix your network, not your plan), local bottlenecks like old hardware or busy devices (cheap fixes), and genuine ISP shortfalls (documented evidence gets results). Twenty minutes of methodical testing tells you which one you're dealing with — and whether it's also worth checking that your plan actually matches your household's needs in the first place; our guide to <a href="/blog/what-is-a-good-internet-speed.php">what counts as a good internet speed</a> can help with that.</p>

  <div class="cta-box"><p><strong>Curious how your connection measures up?</strong></p><p><a href="/">Run a free speed test now →</a></p></div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
