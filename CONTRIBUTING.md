# Contributing to Speed Score

Thanks for taking the time. This project deliberately has no build step and no
dependencies — please keep it that way.

## Getting set up

```bash
git clone https://github.com/iampopye/speedtest.git
cd speedtest
cp includes/config.example.php includes/config.php
php -S localhost:8000
```

PHP's built-in server is single-threaded, so parallel download streams queue behind each
other and reported speeds will be far below reality. That's fine for UI work — verify
anything measurement-related against Apache, nginx or Caddy.

## Ground rules

**No dependencies.** No Composer, no npm, no CDN links. The whole point is that this drops
onto any PHP host and runs. A PR that adds a package manager will be declined regardless of
how good the feature is.

**No build step.** Edit `assets/css/style.css` and `assets/js/speedtest.js` directly.

**Match the surrounding style.** Two-space indent in JS, CSS and HTML; four-space in PHP.
Comments explain *why*, not *what* — the existing code is a good guide to the density
expected.

**Don't break measurement accuracy.** Before touching anything under `backend/` or in
`speedtest.js`, read the "How the measurement works" section of the README. Two rules in
particular:

- Test payloads must stay incompressible (`random_bytes()`), and compression must remain
  disabled for `/backend/`. Compressing them measures your CPU, not the connection.
- Nothing from a test run gets written to disk. Not results, not payloads, not IPs.

**Privacy is a feature, not a default to be relaxed.** Anything that stores per-visitor
data, adds a third-party script, or widens the geo cache beyond `/24` and `/48` needs a
strong justification in the PR description.

## Before you open a PR

- Lint every file you touched: `php -l path/to/file.php`
- Run a full test in the browser and confirm the numbers are still sane
- Check both light and dark themes
- Check the layout at 380 px, 640 px and desktop widths
- If you changed the logo or palette, re-run `php tools/generate-assets.php`

## Commit messages

Short imperative subject lines: `Fix jitter calculation on slow connections`. Explain the
reasoning in the body if it isn't obvious from the diff.

## Reporting bugs

Measurement bugs are the hardest to reproduce, so please include:

- Browser and OS versions
- Connection type (fibre / cable / DSL / 5G / satellite) and advertised plan speed
- Wired or Wi-Fi
- What Speed Score reported, and what you expected
- Results from another speed test for comparison, if you have them

## Security

Do **not** open a public issue for a security problem — see [SECURITY.md](SECURITY.md).
