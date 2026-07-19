# Domain Genie 🧞 — How to run it

Domain Genie helps you find clever domain names. You type your idea, the AI invents
witty names, and the app instantly checks which ones are actually free to register.

## Running it on your computer (easiest)

1. Open the `domain-genie` folder.
2. Double-click **index.html**. It opens in your web browser — that's the whole app.
3. The first time, a settings box appears asking for your **Anthropic API key**.
   - Your key is in the file **"Claude API key.txt"** in your Claude projects folder
     (the long code on the first line that starts with `sk-ant-`).
   - Copy it and paste it into the box. It's saved in your browser, so you only do this once.
4. Type your idea in the big box (one sentence is plenty) and click **✨ Summon names**.
5. Wait about 15–30 seconds. Names appear and get checked one by one.
   - ✅ **Available** — verified free at the official registry. Go get it!
   - 🤔 **Probably available** — looks free, but that domain ending couldn't be fully
     verified. Click "Register" to double-check.
   - ❌ **Taken** — shown at the bottom, just for inspiration.
6. Not happy? Click **➕ More names** and it invents a fresh batch (no repeats).
7. Click **Register ↗** next to a name to open it on Namecheap, or **Copy** to copy it.

## What it costs

- Each brainstorm round costs a **fraction of a cent** of API credit (it asks Claude's
  cheapest model, Haiku, for 20 names). The availability checks are completely free.
- Registering a domain costs money at the registrar — the 💲 symbols hint at price:
  💲 ≈ $10/year (like .com), 💲💲 ≈ $20–35, 💲💲💲 ≈ $35–70, 💲💲💲💲 = $70+.

## Putting it online (optional)

You can host it on your iWebFusion site so it works from any device:

1. Log in to cPanel (details in "iWebFusion login.txt").
2. Open **File Manager** → go into **public_html**.
3. Create a folder called `domain-genie` and upload **index.html** into it.
4. Visit `https://your-site.com/domain-genie/` in any browser.

Note: your API key is **not** inside the uploaded file — each browser/device asks for
the key once and keeps it locally. So it's safe to put online, but anyone you share
the link with would need their own API key to use it.

## If something goes wrong

- **"API key was rejected"** — re-copy the key carefully from "Claude API key.txt"
  (no spaces before/after), click ⚙️ and paste it again.
- **Names appear but all say "Couldn't check"** — your internet may be blocking the
  registry lookups; try again in a minute.
- **Nothing happens on double-click** — right-click index.html → Open with → Chrome or Edge.
