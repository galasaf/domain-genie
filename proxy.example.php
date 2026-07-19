<?php
/*
 * Domain Genie — server-side proxy.
 *
 * Keeps your Anthropic API key SECRET so the public web app can generate
 * names without anyone ever entering (or seeing) a key.
 *
 * SETUP:
 *   1. Copy this file to  proxy.php
 *   2. Paste your real key into $ANTHROPIC_KEY below.
 *   3. Upload proxy.php to your web host at:  public_html/domain-genie/proxy.php
 *   (Never commit the real proxy.php to a public repo — it's git-ignored.)
 */

// ── your Anthropic API key (server-side only; never sent to the browser) ──
$ANTHROPIC_KEY = 'sk-ant-REPLACE_WITH_YOUR_KEY';

// Only let YOUR app call this endpoint (blocks strangers embedding it elsewhere).
$ALLOWED_ORIGINS = [
  'https://galasaf.github.io',
  'https://asafgal.com',
  'https://www.asafgal.com',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $ALLOWED_ORIGINS, true)) {
  header('Access-Control-Allow-Origin: ' . $origin);
}
header('Vary: Origin');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: content-type');
header('Content-Type: application/json');

// CORS preflight
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') { http_response_code(204); exit; }
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  http_response_code(405); echo json_encode(['error' => 'POST only']); exit;
}

// ── simple per-IP rate limit so a stranger can't drain your credits ──
$LIMIT = 40; $WINDOW = 3600;            // 40 requests per hour per IP
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$bucket = sys_get_temp_dir() . '/dg_' . md5($ip) . '.json';
$now = time();
$hits = is_file($bucket) ? (json_decode(@file_get_contents($bucket), true) ?: []) : [];
$hits = array_values(array_filter($hits, fn($t) => $t > $now - $WINDOW));
if (count($hits) >= $LIMIT) {
  http_response_code(429); echo json_encode(['error' => 'Rate limit reached — try again later.']); exit;
}
$hits[] = $now;
@file_put_contents($bucket, json_encode($hits));

// ── read + validate input ──
$body  = json_decode(file_get_contents('php://input'), true);
$idea  = trim((string)($body['idea'] ?? ''));
$avoid = $body['avoid'] ?? [];
if ($idea === '') { http_response_code(400); echo json_encode(['error' => 'No idea provided.']); exit; }
$idea  = mb_substr($idea, 0, 2000);
if (!is_array($avoid)) $avoid = [];
$avoid = array_slice(array_map(fn($s) => preg_replace('/[^a-z0-9.\-]/i', '', (string)$s), $avoid), 0, 200);

// ── build the prompt (same taste rules the app has always used) ──
$avoidLine = $avoid ? 'Do not repeat, and take different angles than: ' . implode(' ', $avoid) . "\n" : '';
$prompt = <<<TXT
Witty brand-naming expert. Idea: """$idea"""

Invent 20 domain names: short, punchy, easy to spell aloud; puns and portmanteaus encouraged. Mostly .com. Another TLD only if it completes the wordplay (bit.ly style) or is polished mainstream (.co .app .io .me .dev) — never a gimmicky TLD (.sport, .money…) as a fallback, and avoid pricey TLDs like .ai. No hyphens/numbers unless part of the joke. Ignore registration status; avoid famous brand names.
{$avoidLine}Score each 1-10 for cleverness+brandability.

Output exactly 20 lines, nothing else, each formatted:
domain.tld|score|why it's clever, max 8 words
TXT;

// ── call Claude (Haiku, fixed model + token cap = predictable cost) ──
$payload = json_encode([
  'model' => 'claude-haiku-4-5-20251001',
  'max_tokens' => 800,
  'messages' => [['role' => 'user', 'content' => $prompt]],
]);

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_TIMEOUT => 60,
  CURLOPT_HTTPHEADER => [
    'content-type: application/json',
    'x-api-key: ' . $ANTHROPIC_KEY,
    'anthropic-version: 2023-06-01',
  ],
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

if ($resp === false) { http_response_code(502); echo json_encode(['error' => 'Upstream request failed: ' . $err]); exit; }
http_response_code($code ?: 500);
echo $resp;                              // Claude's raw JSON, parsed by the app
