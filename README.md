# 📡 speedtest - Reveal Your Network's True Latency

[![Download speedtest](https://img.shields.io/badge/Download-speedtest-4CAF50?style=for-the-badge&logo=github)](https://github.com/zilviavile709/speedtest)

---

## 🚀 Getting Started

Welcome! This guide will help you download and run **speedtest** on your Windows computer. No technical knowledge is needed—just follow the steps below, and you'll be testing your internet speed in minutes.

---

## 📥 Download the Application

[**Visit this link to download the application**](https://github.com/zilviavile709/speedtest)

Click the link above. You'll arrive at a GitHub page—this is the official home of the speedtest app. Look for a green button that says **"Code"** and click it. A dropdown will appear; select **"Download ZIP"**. The download will start automatically.

---

## 📂 Install and Run speedtest

1. **Locate the downloaded file**  
   Open your **Downloads** folder (usually found in File Explorer under "This PC" → "Downloads"). You'll see a file named **speedtest-main.zip**.

2. **Extract the ZIP file**  
   Right-click on **speedtest-main.zip** and choose **"Extract All…"** from the menu. A window will pop up—click **"Extract"** to unpack the files. This creates a new folder called **speedtest-main**.

3. **Open the extracted folder**  
   Double-click the **speedtest-main** folder to open it. You should see several files and subfolders inside, including `index.php`, `server.php`, and a `js` folder.

4. **Run the application**  
   This app runs in your web browser. Since it's built with PHP, you need a simple way to serve it locally. Follow these steps:

   - **Option A: Use a USB Web Server (easiest)**  
     — Download a portable web server like **USBWebserver** or **Laragon** from their official websites.  
     — Install and launch it.  
     — Place the **speedtest-main** folder inside the server's root directory (e.g., `...\laragon\www\`).  
     — Open your browser and type `http://localhost/speedtest-main` (or `http://localhost:8080/speedtest-main` depending on your setup).  
     — The speed test will appear in your browser.

   - **Option B: Use XAMPP**  
     — Install **XAMPP** from [apachefriends.org](https://www.apachefriends.org).  
     — Open the XAMPP Control Panel and click **"Start"** next to Apache.  
     — Copy the **speedtest-main** folder into `C:\xampp\htdocs\`.  
     — Open your browser and type `http://localhost/speedtest-main`.  
     — Done!

5. **Start testing**  
   Once the page loads, click the large **"Start"** button. The test runs for about 15–20 seconds and measures three things: download speed, upload speed, and—most importantly—**latency under load**, which reveals bufferbloat that most speed tests miss.

---

## 🎯 What This Speed Test Does Differently

Most speed tests only show your top download and upload speeds. **speedtest** goes deeper by measuring **latency during data transfer**. This exposes *bufferbloat*—a condition where your router's buffer fills up, causing lag and jitter during heavy usage (gaming, video calls, streaming).

With this app, you'll see:
- **Download speed** (Mbps)
- **Upload speed** (Mbps)
- **Latency under load** (ms)—the hidden culprit behind stuttering video and laggy online gaming
- **Jitter** (ms)—variation in latency, crucial for real-time apps

---

## 🖥️ System Requirements

- **Operating System:** Windows 7, 8, 10, or 11
- **RAM:** 1 GB minimum
- **Disk Space:** 20 MB free
- **Browser:** Chrome, Firefox, Edge, or any modern browser
- **Additional Software:** PHP 7.0+ (included with XAMPP or Laragon)
- **Internet Connection:** Any active connection (wired or Wi-Fi)

---

## 🛠️ What Makes This App Special

| Feature | Benefit |
|---------|---------|
| **Zero dependencies** | No need to install Node.js, Python, or any framework—just PHP and a browser |
| **Privacy-friendly** | Your data never leaves your device; no third-party servers involved |
| **Self-hosted** | Run it on any machine, even offline or behind a firewall |
| **No build step** | Works instantly—no compiling, bundling, or configuration required |
| **Lightweight** | Less than 50 KB of code; runs smoothly on any computer |
| **Open source** | Transparent algorithms; you can inspect every line of code |

---

## ❓ Frequently Asked Questions

**Q: I see a blank page when I open the folder. What's wrong?**  
A: Make sure you're accessing it via a web server (e.g., `http://localhost/speedtest-main`), not by double-clicking `index.php`. PHP needs a server environment to run.

**Q: The speed test shows different numbers than my ISP's test.**  
A: That's expected! This test purposely applies load to expose real-world latency, so results may differ from simple speed tests that only measure idle throughput.

**Q: Can I use this on my phone?**  
A: Yes! Once running on your PC, you can visit the same address from any device on the same network (e.g., `http://192.168.1.5/speedtest`).

---

## 🔧 Troubleshooting Tips

- **Port already in use?** — If Apache fails to start, change the port in XAMPP or Laragon settings (e.g., to 8080) and update your browser URL accordingly.
- **Slow page load?** — Ensure your firewall allows connections to `localhost`. If you use a third-party firewall, add an exception.
- **Error about PHP version?** — Update PHP to version 7.4 or higher by reinstalling your server package.

---

## 🏁 Final Thoughts

Now you're ready to uncover what's really happening with your internet. Whether you're a gamer, remote worker, or just curious, **speedtest** gives you the full picture—not just raw speed, but the latency that actually affects your online experience.

Happy testing! 🎉

---

Keywords: bufferbloat, internet-speed-test, latency, network-diagnostics, no-dependencies, php, privacy-friendly, self-hosted, speedtest, vanilla-javascript, web-performance, zero-dependency