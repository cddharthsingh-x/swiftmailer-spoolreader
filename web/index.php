<?php
require_once '../config/config.php';
$cssVer = @filemtime(__DIR__ . '/css/app.css') ?: time();
$jsVer  = @filemtime(__DIR__ . '/js/app.js')  ?: time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Spool Reader</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="css/app.css?v=<?php echo $cssVer; ?>" rel="stylesheet">
</head>
<body>

<div id="progress"><div id="progress-fill"></div></div>

<div id="app">

  <!-- ── SIDEBAR ── -->
  <aside id="sidebar" class="panel">
    <div class="logo">
      <div class="logo-mark">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/>
          <path d="M3 9.5l9 6 9-6"/>
          <path d="M8 13v6"/>
          <path d="M16 13v6"/>
        </svg>
      </div>
      <div>
        <div class="logo-text">Spool<em>Reader</em></div>
      </div>
    </div>

    <button class="btn-primary" id="btn-refresh">
      <svg id="ico-refresh" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9
             m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
      Refresh Spool
    </button>

    <div class="nav-section">
      <div class="nav-label">Filter by sender</div>
      <div id="senders-list">
        <div class="nav-item active" data-sender="">
          <span class="dot" style="background:var(--accent)"></span>
          <span class="name">All messages</span>
          <span class="count" id="nav-count-all">0</span>
        </div>
      </div>
    </div>

    <div class="info-card">
      <div class="info-row">
        <span class="info-key">Spool Directory</span>
        <span class="info-val mono"><?php echo htmlspecialchars(SPOOL_DIR); ?></span>
      </div>
      <div class="info-row">
        <span class="info-key">Last Sync</span>
        <span class="info-val" id="info-sync">—</span>
      </div>
    </div>

    <div class="sidebar-spacer"></div>

    <button class="btn-danger" id="btn-clear">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7
             m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
      </svg>
      Clear Spool
    </button>
  </aside>

  <!-- ── EMAIL LIST PANEL ── -->
  <section id="list-panel" class="panel">
    <div id="list-header">
      <div class="search-wrap">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
        </svg>
        <input id="search" type="text"
               placeholder="Search messages…"
               autocomplete="off" spellcheck="false">
      </div>
    </div>

    <div id="email-list">
      <div id="no-emails"></div>
    </div>

    <div id="list-footer">
      <span id="last-sync"><span class="pulse-dot"></span><span>synced just now</span></span>
      <span>auto · 30s</span>
    </div>
  </section>

  <!-- ── EMAIL VIEWER PANEL ── -->
  <main id="email-panel" class="panel">

    <div id="email-empty">
      <div class="empty-box">
        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0
               01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25
               2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0
               01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32
               8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
        </svg>
      </div>
      <div class="empty-title">No message selected</div>
      <div class="empty-hint">Pick an email from the list to preview it here</div>
    </div>

    <div id="email-viewer">
      <div id="meta-header">
        <div class="meta-top">
          <div class="meta-actor">
            <span class="who" id="meta-actor-from">—</span>
            <span class="to-label">to</span>
            <span class="who" id="meta-actor-to">—</span>
          </div>
          <span class="meta-time" id="meta-time">—</span>
        </div>

        <h1 id="meta-subject">—</h1>

        <div class="meta-grid">
          <div class="meta-row">
            <span class="meta-key">From</span>
            <span class="meta-val" id="m-from">—</span>
          </div>
          <div class="meta-row">
            <span class="meta-key">To</span>
            <span class="meta-val" id="m-to">—</span>
          </div>
          <div class="meta-row">
            <span class="meta-key">Reply-To</span>
            <span class="meta-val" id="m-replyto">—</span>
          </div>
        </div>

        <button class="btn-print" id="btn-print" title="Print this email">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
          </svg>
          Print
        </button>
      </div>

      <iframe id="email-frame" sandbox="allow-same-origin allow-popups allow-modals"
              title="Email preview"></iframe>
    </div>
  </main>

</div>

<script src="js/app.js?v=<?php echo $jsVer; ?>"></script>
</body>
</html>
