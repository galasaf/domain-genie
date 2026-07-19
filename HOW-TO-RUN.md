# Domain Genie 🧞 — How to run it

Domain Genie helps you find clever domain names. You type your idea, the AI invents
witty names, and the app instantly checks which ones are actually free to register.

It now runs as a **public website with no API key to type in** — you just open it and
go. Your Anthropic key is hidden safely on your own web host, so nobody using the site
ever sees it.

---

## Using it (the easy part)

Just open the live site in any browser, on any device:

**https://galasaf.github.io/domain-genie/**

1. Type your idea in the big box (one sentence is plenty) and click **✨ Summon names**.
2. Wait about 15–30 seconds. Names appear and get checked one by one.
   - ✅ **Available** — verified free at the official registry. Go get it!
   - 🤔 **Probably available** — looks free, but that ending couldn't be fully verified.
   - ❌ **Taken** — shown at the bottom, just for inspiration.
3. Not happy? Click **➕ More names** for a fresh batch (no repeats).
4. Click **Register ↗** next to a name to open it on Namecheap, or **Copy** to copy it.

---

## One-time setup (already done, but here's how it works)

The website itself contains **no key**. When you ask for names, it quietly calls a small
helper file called **`proxy.php`** that lives on your iWebFusion web host. That helper
holds your Anthropic key and talks to Claude on the site's behalf. This is what lets the
site be public without exposing your key.

If you ever need to set it up again (new key, new host, etc.):

1. Open **`proxy.example.php`** (in this folder), copy it to a file named **`proxy.php`**,
   and paste your Anthropic key (from **"Claude API key.txt"**) into the line that says
   `$ANTHROPIC_KEY = '...';`.
   *(If you're on this computer, `proxy.php` already exists with your key filled in.)*
2. Log in to iWebFusion cPanel: **https://uniform.iwebfusion.net:2083/**
3. **File Manager** → open **public_html** → create a folder named **`domain-genie`**.
4. **Upload** `proxy.php` into `public_html/domain-genie/`.
5. That's it. The website already knows to call `https://asafgal.com/domain-genie/proxy.php`.

> ⚠️ **Never** upload or commit `proxy.php` to GitHub — it has your secret key inside.
> Only the `proxy.example.php` (with a fake placeholder key) belongs in the public repo.

---

## What it costs

- Each brainstorm round costs a **fraction of a cent** of API credit (Claude's cheapest
  model, Haiku, for 20 names). Availability checks are completely free.
- The proxy limits each visitor to 40 requests/hour and forces the cheap model, so even
  if someone finds the site they can't run up a big bill.
- Registering a domain costs money at the registrar — the 💲 symbols hint at price:
  💲 ≈ $10/year (like .com), 💲💲 ≈ $20–35, 💲💲💲 ≈ $35–70, 💲💲💲💲 = $70+.

---

## If something goes wrong

- **Names never load / "request failed"** — the site can't reach `proxy.php`. Check that:
  - `proxy.php` is uploaded to `public_html/domain-genie/` on iWebFusion.
  - Your site is reachable over **https** (the GitHub site can only call an `https://`
    address). In cPanel, make sure **SSL/TLS Status → AutoSSL** covers `asafgal.com`.
  - The `PROXY_URL` line at the top of `index.html`'s script matches your real address
    (try `www.asafgal.com` if `asafgal.com` doesn't work, or vice-versa).
- **"Rate limit reached"** — you (or someone) made 40+ requests in an hour; wait a bit.
- **Names appear but all say "Couldn't check"** — the free registry lookups were blocked;
  try again in a minute.
