<footer class="site-footer">
  <div class="container">
    <div>© <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars(parse_url(SITE_URL, PHP_URL_HOST) ?: '', ENT_QUOTES, 'UTF-8'); ?></div>
    <nav class="footer-nav">
      <a href="/">Speed Test</a>
      <a href="/blog/">Guides</a>
      <a href="/about.php">About</a>
      <a href="/methodology.php">Methodology</a>
      <a href="/privacy.php">Privacy Policy</a>
      <a href="/terms.php">Terms of Use</a>
      <a href="/contact.php">Contact</a>
<?php if (REPO_URL !== ''): ?>
      <a href="<?php echo htmlspecialchars(REPO_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Source on GitHub</a>
<?php endif; ?>
    </nav>
<?php if (REPO_URL !== ''): ?>
    <p class="footer-oss">
      <?php echo htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8'); ?> is free and open source under the MIT licence —
      <a href="<?php echo htmlspecialchars(REPO_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">self-host it or star it on GitHub</a>.
    </p>
<?php endif; ?>
  </div>
</footer>
<script src="/assets/js/theme.js"></script>
</body>
</html>
