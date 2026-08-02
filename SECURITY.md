# Security Policy

## Reporting a vulnerability

Please report security issues privately — **do not open a public GitHub issue.**

Email **info@riocloudsolutions.com** with:

- A description of the issue and its impact
- Steps to reproduce, or a proof of concept
- Any suggested fix

You can expect an acknowledgement within 3 working days and an assessment within 10.
If the report is valid, we'll agree a disclosure timeline with you and credit you in the
release notes unless you'd rather stay anonymous.

## Scope

The areas most worth your attention in a self-hosted deployment:

- **`backend/garbage.php`** — streams attacker-influenced byte counts. The size parameters
  are clamped (100 MB ceiling); a bypass that lets a request stream unbounded data is a
  denial-of-service issue and in scope.
- **`backend/empty.php`** — accepts and discards arbitrary POST bodies.
- **`backend/lib/geo.php`** — makes outbound lookups and writes to the on-disk cache.
  Cache-poisoning or path-traversal via the cache key would be in scope.
- **`data/` and `includes/`** — must not be reachable over HTTP. Both ship with an
  `.htaccess` denying access. A configuration that exposes them is in scope; note that
  **nginx and Caddy ignore `.htaccess`**, so those deployments need explicit deny rules.

## Out of scope

- Missing rate limiting on the test endpoints. Speed tests are bandwidth-intensive by
  design; capacity control belongs at your web server or CDN.
- Reports that the site "allows" high bandwidth use — that is the product.
- Findings against the hosted demo that only reproduce because of its specific hosting
  configuration rather than this code.

## For self-hosters

The test endpoints will happily saturate your uplink, which is what they're for. On shared
hosting or a metered connection, put a rate limit or a concurrent-connection cap in front
of `/backend/` before making your instance public.
