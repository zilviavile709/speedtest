# Security Policy

## Reporting a vulnerability

Please report security issues privately — **do not open a public GitHub issue.**

Email **info@riocloudsolutions.com** with a description of the issue, its impact, and
steps to reproduce.

You can expect an acknowledgement within 3 working days and an assessment within 10.
If the report is valid, we'll agree a disclosure timeline with you and credit you in the
release notes unless you'd rather stay anonymous.

## Out of scope

- Missing rate limiting on the test endpoints. Speed tests are bandwidth-intensive by
  design; capacity control belongs at the web server or CDN layer.
- Reports that the site "allows" high bandwidth use — that is the product.
- Findings that only reproduce because of a specific hosting configuration rather than
  this code.

## For self-hosters

The test endpoints will saturate your uplink by design. On shared hosting or a metered
connection, put a rate limit or a concurrent-connection cap in front of `/backend/`
before making an instance public.

Serve `/data/` and `/includes/` as non-public directories. The bundled `.htaccess` files
handle this on Apache; **nginx and Caddy do not read `.htaccess`**, so those deployments
need equivalent rules written into the server config.
