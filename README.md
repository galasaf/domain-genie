# Domain Genie 🧞

Describe your idea → get clever, punny domain names → instantly see which ones are
actually free to register. Availability is checked live against domain registries
(RDAP) with a DNS fallback for exotic endings.

**▶ Live app:** https://galasaf.github.io/domain-genie/

## How it works

- **Front end** — a single static `index.html` (hosted free on GitHub Pages). It holds
  **no API key**.
- **Names** come from Claude (Haiku). The app never talks to Anthropic directly;
  instead it POSTs your idea to a tiny `proxy.php` on a private web host, which adds the
  secret API key server-side. That way the public site is keyless and your key is never
  exposed. The proxy is origin-locked, rate-limited, and forces the cheap Haiku model so
  a stranger can't run up the bill.
- **Availability** is checked entirely in the browser (RDAP + Cloudflare DNS), no key
  needed.

## Hosting your own copy

1. Copy `proxy.example.php` → `proxy.php`, paste your Anthropic key into it, and upload it
   to your web host at `public_html/domain-genie/proxy.php`.
2. Edit the `PROXY_URL` line near the top of `index.html`'s script to point at your
   `proxy.php`, and set `$ALLOWED_ORIGINS` in the proxy to your site's URL.
3. Serve `index.html` anywhere static (GitHub Pages, or the same web host).

`proxy.php` is git-ignored on purpose — it contains your secret key and must never be
committed to a public repo.
