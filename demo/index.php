<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use MusicBrainz\HttpAdapters\Psr18HttpAdapter;
use MusicBrainz\MusicBrainz;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$mbid = trim((string) ($_GET['mbid'] ?? ''));
$artist = null;
$error = null;

if ($mbid !== '') {
    try {
        $factory = new HttpFactory();
        $brainz = new MusicBrainz(new Psr18HttpAdapter(new Client(), $factory, $factory));
        $brainz->setUserAgent('MusicBrainz PHP Demo', '2.0.0', 'https://github.com/dehy/MusicBrainz');
        $artist = $brainz->lookup('artist', $mbid, ['aliases', 'tags']);
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

function escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MusicBrainz PHP Demo</title>
    <style>
        :root { color-scheme: light; font-family: Georgia, serif; color: #1d2835; background: #f4f0e8; }
        body { margin: 0; min-height: 100vh; background-image: repeating-linear-gradient(0deg, transparent, transparent 31px, #ded7c7 32px); }
        main { width: min(760px, calc(100% - 32px)); margin: 48px auto; }
        header { border-bottom: 3px solid #d45c2e; padding-bottom: 20px; }
        h1 { margin: 0; font-size: 2.5rem; letter-spacing: 0; }
        p { line-height: 1.55; }
        form { display: flex; gap: 10px; margin: 28px 0; }
        input { min-width: 0; flex: 1; box-sizing: border-box; border: 1px solid #4b5d66; padding: 12px; font: inherit; background: #fffdf8; }
        button { border: 0; background: #1d5f66; color: white; padding: 12px 18px; font: inherit; cursor: pointer; }
        .notice, article { background: #fffdf8; border: 1px solid #c9c1af; padding: 20px; }
        .error { border-left: 5px solid #b53b2d; }
        article { border-left: 5px solid #d45c2e; }
        dl { display: grid; grid-template-columns: 140px 1fr; gap: 8px 16px; margin-bottom: 0; }
        dt { font-weight: bold; }
        dd { margin: 0; overflow-wrap: anywhere; }
        code { font-family: ui-monospace, monospace; }
        @media (max-width: 560px) { form { flex-direction: column; } button { width: 100%; } dl { grid-template-columns: 1fr; gap: 2px; } }
    </style>
</head>
<body>
<main>
    <header>
        <h1>MusicBrainz PHP Demo</h1>
        <p>Look up an artist by MusicBrainz ID through the PSR-18 adapter.</p>
    </header>

    <form method="get">
        <input
            aria-label="Artist MusicBrainz ID"
            name="mbid"
            pattern="[0-9a-fA-F-]{36}"
            placeholder="Artist MBID"
            required
            value="<?= escape($mbid) ?>"
        >
        <button type="submit">Look Up</button>
    </form>

    <?php if ($error !== null): ?>
        <p class="notice error"><strong>Request failed:</strong> <?= escape($error) ?></p>
    <?php elseif ($artist !== null): ?>
        <article>
            <h2><?= escape($artist['name'] ?? 'Unnamed artist') ?></h2>
            <dl>
                <dt>MBID</dt><dd><code><?= escape($artist['id'] ?? '') ?></code></dd>
                <dt>Type</dt><dd><?= escape($artist['type'] ?? 'Unknown') ?></dd>
                <dt>Country</dt><dd><?= escape($artist['country'] ?? 'Unknown') ?></dd>
                <dt>Sort name</dt><dd><?= escape($artist['sort-name'] ?? '') ?></dd>
            </dl>
        </article>
    <?php else: ?>
        <p class="notice">Try <code>4dbf5678-7a31-406a-abbe-232f8ac2cd63</code> to look up Bryan Adams.</p>
    <?php endif; ?>
</main>
</body>
</html>
