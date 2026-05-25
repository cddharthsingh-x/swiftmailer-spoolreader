<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Spool Reader</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:           #eef2f7;
      --bg-2:         #e6ecf3;
      --surface:      #ffffff;
      --surface-2:    #f7f9fc;
      --surface-hi:   #ffffff;

      --text-1:       #1a2138;
      --text-2:       #5b6379;
      --text-3:       #9098ab;
      --text-4:       #b5bcce;

      --accent:       #3b5cff;
      --accent-hover: #2d4ae5;
      --accent-soft:  #e8edff;
      --accent-text:  #2d4ae5;

      --red:          #ff5a73;
      --red-soft:     #ffe8ec;
      --orange:       #ff9d4a;
      --cyan:         #4ed3e8;
      --pink:         #ff7eb6;
      --green:        #4cd084;
      --violet:       #a78bfa;
      --amber:        #ffc24a;

      --border:       #e4e9f0;
      --border-2:     #eef2f7;

      --shadow-xs:    0 1px 2px rgba(46, 58, 89, 0.04);
      --shadow-sm:    0 2px 8px rgba(46, 58, 89, 0.05);
      --shadow:       0 4px 16px rgba(46, 58, 89, 0.06);
      --shadow-md:    0 6px 20px rgba(46, 58, 89, 0.08);
      --shadow-lg:    0 12px 32px rgba(46, 58, 89, 0.10);
      --shadow-accent:0 8px 20px rgba(59, 92, 255, 0.25);

      --r-sm:         8px;
      --r:            12px;
      --r-lg:         16px;
      --r-pill:       999px;

      --sidebar-w:    240px;
      --list-w:       360px;
      --gutter:       16px;

      --font:         'Plus Jakarta Sans', sans-serif;
      --mono:         'JetBrains Mono', monospace;
    }

    html, body {
      height: 100%;
      overflow: hidden;
      background: var(--bg);
      color: var(--text-1);
      font-family: var(--font);
      font-size: 14px;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
      letter-spacing: -0.005em;
    }

    /* atmosphere: subtle radial wash */
    body::before {
      content: '';
      position: fixed;
      inset: -200px;
      pointer-events: none;
      background:
        radial-gradient(ellipse 800px 600px at 15% 10%, rgba(59,92,255,0.05), transparent 60%),
        radial-gradient(ellipse 700px 500px at 85% 95%, rgba(255,126,182,0.04), transparent 60%);
      z-index: 0;
    }

    /* ── APP SHELL ────────────────────── */
    #app {
      display: flex;
      height: 100vh;
      padding: var(--gutter);
      gap: var(--gutter);
      position: relative;
      z-index: 1;
    }

    /* ── PANELS (shared) ──────────────── */
    .panel {
      background: var(--surface);
      border-radius: var(--r-lg);
      box-shadow: var(--shadow-sm);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      position: relative;
    }

    /* ── SIDEBAR ──────────────────────── */
    #sidebar {
      width: var(--sidebar-w);
      min-width: var(--sidebar-w);
      padding: 20px 18px;
      gap: 20px;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 4px 4px 0;
    }

    .logo-mark {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: linear-gradient(135deg, var(--accent), #6b7eff);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      box-shadow: var(--shadow-accent);
      flex-shrink: 0;
    }

    .logo-mark svg { width: 19px; height: 19px; }

    .logo-text {
      font-size: 17px;
      font-weight: 800;
      color: var(--text-1);
      letter-spacing: -0.4px;
      line-height: 1;
    }

    .logo-text em {
      font-style: normal;
      font-weight: 400;
      color: var(--text-3);
    }

    /* primary CTA button */
    .btn-primary {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 12px 16px;
      background: var(--accent);
      color: #fff;
      border: none;
      border-radius: var(--r);
      font-family: var(--font);
      font-size: 13.5px;
      font-weight: 600;
      cursor: pointer;
      box-shadow: var(--shadow-accent);
      transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .btn-primary:hover {
      background: var(--accent-hover);
      transform: translateY(-1px);
      box-shadow: 0 10px 24px rgba(59, 92, 255, 0.32);
    }

    .btn-primary:active { transform: translateY(0); }
    .btn-primary svg { width: 15px; height: 15px; }

    /* secondary nav-like items */
    .nav-section {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .nav-label {
      font-size: 10.5px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: var(--text-3);
      padding: 0 10px 6px;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 10px;
      border-radius: var(--r-sm);
      cursor: pointer;
      transition: background 0.12s ease, color 0.12s ease;
      color: var(--text-2);
      font-weight: 500;
      font-size: 13px;
      user-select: none;
    }

    .nav-item:hover { background: var(--surface-2); color: var(--text-1); }

    .nav-item.active {
      background: var(--surface-hi);
      color: var(--text-1);
      box-shadow: var(--shadow);
      font-weight: 600;
    }

    .nav-item .dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
      box-shadow: 0 0 0 3px rgba(255,255,255,0);
      transition: box-shadow 0.15s ease;
    }

    .nav-item.active .dot {
      box-shadow: 0 0 0 3px rgba(0,0,0,0.04);
    }

    .nav-item .name {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .nav-item .count {
      font-size: 11px;
      color: var(--text-3);
      font-weight: 600;
      font-family: var(--mono);
    }

    .nav-item.active .count { color: var(--text-2); }

    /* spool info card */
    .info-card {
      padding: 14px;
      background: var(--surface-2);
      border-radius: var(--r);
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .info-row {
      display: flex;
      flex-direction: column;
      gap: 3px;
    }

    .info-key {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1.2px;
      text-transform: uppercase;
      color: var(--text-3);
    }

    .info-val {
      font-size: 12px;
      color: var(--text-1);
      font-weight: 500;
      word-break: break-all;
    }

    .info-val.mono {
      font-family: var(--mono);
      font-size: 10.5px;
      font-weight: 400;
      color: var(--text-2);
    }

    .sidebar-spacer { flex: 1; }

    .btn-danger {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 11px 14px;
      background: var(--surface);
      color: var(--red);
      border: 1.5px solid var(--red-soft);
      border-radius: var(--r);
      font-family: var(--font);
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .btn-danger:hover {
      background: var(--red);
      color: #fff;
      border-color: var(--red);
      box-shadow: 0 6px 16px rgba(255, 90, 115, 0.25);
    }

    .btn-danger svg { width: 14px; height: 14px; }

    /* ── LIST PANEL ───────────────────── */
    #list-panel {
      width: var(--list-w);
      min-width: var(--list-w);
    }

    #list-header {
      padding: 18px 18px 14px;
      flex-shrink: 0;
    }

    .search-wrap {
      position: relative;
    }

    .search-wrap svg {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      width: 14px;
      height: 14px;
      color: var(--text-3);
      pointer-events: none;
    }

    #search {
      width: 100%;
      padding: 11px 14px 11px 38px;
      background: var(--surface-2);
      border: 1.5px solid transparent;
      border-radius: var(--r-pill);
      color: var(--text-1);
      font-size: 13px;
      font-family: var(--font);
      outline: none;
      transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
    }

    #search::placeholder { color: var(--text-3); }
    #search:focus {
      background: var(--surface);
      border-color: var(--accent);
      box-shadow: 0 0 0 4px var(--accent-soft);
    }

    #email-list {
      flex: 1;
      overflow-y: auto;
      padding: 4px 14px 14px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    #email-list::-webkit-scrollbar { width: 6px; }
    #email-list::-webkit-scrollbar-track { background: transparent; }
    #email-list::-webkit-scrollbar-thumb {
      background: var(--border);
      border-radius: 999px;
    }
    #email-list::-webkit-scrollbar-thumb:hover { background: var(--text-4); }

    /* ── EMAIL CARDS ──────────────────── */
    .e-item {
      background: var(--surface);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r);
      padding: 12px 14px;
      cursor: pointer;
      transition: all 0.16s ease;
      position: relative;
      animation: card-in 0.3s ease both;
      overflow: hidden;
    }

    @keyframes card-in {
      from { opacity: 0; transform: translateY(6px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .e-item::after {
      content: '';
      position: absolute;
      right: 0;
      top: 14px;
      bottom: 14px;
      width: 3px;
      border-radius: 3px 0 0 3px;
      background: var(--strip);
      opacity: 0.85;
    }

    .e-item:hover {
      box-shadow: var(--shadow);
      transform: translateY(-1px);
      border-color: var(--border);
    }

    .e-item.sel {
      background: var(--surface);
      border-color: var(--accent);
      box-shadow:
        0 0 0 3px var(--accent-soft),
        0 8px 20px rgba(59, 92, 255, 0.15);
      transform: translateY(-1px);
    }

    .e-item.sel .e-from   { color: var(--accent-text); }
    .e-item.sel .e-subject{ color: var(--text-1); }

    .e-row1 {
      display: flex;
      align-items: flex-start;
      gap: 11px;
      margin-bottom: 6px;
    }

    .avatar {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 700;
      font-size: 14px;
      letter-spacing: -0.3px;
      background: var(--strip);
      box-shadow: inset 0 -8px 14px rgba(0,0,0,0.08);
    }

    .e-meta {
      flex: 1;
      min-width: 0;
    }

    .e-from {
      font-weight: 700;
      font-size: 13.5px;
      color: var(--text-1);
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      letter-spacing: -0.2px;
      margin-bottom: 1px;
    }

    .e-ts {
      font-size: 11px;
      color: var(--text-3);
      font-weight: 500;
    }

    .e-subject {
      font-size: 13px;
      font-weight: 600;
      color: var(--text-1);
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      margin-bottom: 3px;
      padding-right: 8px;
    }

    .e-to {
      font-size: 11.5px;
      color: var(--text-3);
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      padding-right: 8px;
      font-weight: 500;
    }

    .e-to .arrow {
      color: var(--strip);
      font-weight: 700;
      margin-right: 4px;
    }

    /* ── LIST FOOTER ──────────────────── */
    #list-footer {
      padding: 10px 18px;
      border-top: 1px solid var(--border-2);
      font-size: 11px;
      color: var(--text-3);
      display: flex;
      justify-content: space-between;
      flex-shrink: 0;
      background: var(--surface-2);
    }

    #last-sync { display: flex; align-items: center; gap: 5px; }

    .pulse-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--green);
      box-shadow: 0 0 0 3px rgba(76, 208, 132, 0.2);
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { box-shadow: 0 0 0 3px rgba(76, 208, 132, 0.2); }
      50%      { box-shadow: 0 0 0 6px rgba(76, 208, 132, 0.05); }
    }

    #no-emails {
      display: none;
      padding: 60px 16px 40px;
      text-align: center;
      color: var(--text-3);
      font-size: 13px;
    }

    /* ── EMAIL VIEWER PANEL ───────────── */
    #email-panel {
      flex: 1;
      min-width: 0;
    }

    /* empty state */
    #email-empty {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 14px;
    }

    .empty-box {
      width: 72px;
      height: 72px;
      background: var(--surface-2);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: var(--shadow-sm);
      margin-bottom: 8px;
    }

    .empty-box svg { width: 32px; height: 32px; color: var(--text-4); }

    .empty-title {
      font-size: 18px;
      font-weight: 700;
      color: var(--text-1);
      letter-spacing: -0.3px;
    }

    .empty-hint {
      font-size: 13px;
      color: var(--text-3);
    }

    /* viewer */
    #email-viewer {
      display: none;
      flex: 1;
      flex-direction: column;
      overflow: hidden;
    }

    #email-viewer.open {
      display: flex;
      animation: viewer-in 0.25s ease both;
    }

    @keyframes viewer-in {
      from { opacity: 0; transform: translateX(8px); }
      to   { opacity: 1; transform: translateX(0); }
    }

    #meta-header {
      padding: 24px 28px 20px;
      flex-shrink: 0;
      border-bottom: 1px solid var(--border-2);
    }

    .meta-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 14px;
    }

    .meta-actor {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      color: var(--text-2);
      font-weight: 500;
    }

    .meta-actor .who {
      font-weight: 700;
      color: var(--text-1);
    }

    .meta-actor .to-label {
      color: var(--text-3);
      font-weight: 500;
    }

    .meta-time {
      font-size: 12px;
      color: var(--text-3);
      font-weight: 500;
      font-family: var(--mono);
    }

    #meta-subject {
      font-size: 22px;
      font-weight: 800;
      color: var(--text-1);
      letter-spacing: -0.6px;
      line-height: 1.25;
      margin-bottom: 16px;
    }

    .meta-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 8px 24px;
    }

    .meta-row {
      display: flex;
      align-items: baseline;
      gap: 8px;
      min-width: 0;
    }

    .meta-key {
      font-size: 9.5px;
      font-weight: 700;
      letter-spacing: 1.4px;
      text-transform: uppercase;
      color: var(--text-3);
      flex-shrink: 0;
    }

    .meta-val {
      font-size: 12.5px;
      color: var(--text-1);
      font-weight: 500;
    }

    .meta-val .addr {
      font-family: var(--mono);
      font-size: 10.5px;
      color: var(--text-3);
      margin-left: 6px;
      font-weight: 400;
    }

    .meta-val .addr::before { content: '<'; }
    .meta-val .addr::after  { content: '>'; }

    .via-tag {
      display: inline-block;
      font-family: var(--mono);
      font-size: 9.5px;
      background: var(--accent-soft);
      color: var(--accent-text);
      padding: 2px 7px;
      border-radius: 999px;
      margin-left: 6px;
      vertical-align: middle;
      font-weight: 500;
      letter-spacing: 0.2px;
    }

    /* iframe */
    #email-frame {
      flex: 1;
      border: none;
      background: #fff;
      display: block;
      width: 100%;
    }

    /* ── PROGRESS BAR ─────────────────── */
    #progress {
      position: fixed;
      top: 0; left: 0; right: 0;
      height: 3px;
      z-index: 999;
      pointer-events: none;
    }

    #progress-fill {
      height: 100%;
      width: 0;
      background: linear-gradient(90deg, var(--accent), #6b7eff, var(--accent));
      background-size: 200% 100%;
      opacity: 0;
      transition: width 0.3s ease, opacity 0.25s ease;
    }

    #progress-fill.loading {
      opacity: 1;
      width: 85%;
      animation: shimmer 1.2s linear infinite;
    }

    @keyframes shimmer {
      from { background-position: 200% center; }
      to   { background-position: -200% center; }
    }

    #progress-fill.done {
      width: 100%;
      opacity: 0;
      transition: width 0.18s ease, opacity 0.35s ease 0.15s;
    }

    /* ── SPIN ─────────────────────────── */
    .spin { animation: spin 0.7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── SCROLL ───────────────────────── */
    * { scrollbar-width: thin; scrollbar-color: var(--border) transparent; }

    /* ── RESPONSIVE ───────────────────── */
    @media (max-width: 1100px) {
      :root { --sidebar-w: 200px; --list-w: 320px; }
    }
    @media (max-width: 900px) {
      #sidebar { display: none; }
    }
  </style>
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
      </div>

      <iframe id="email-frame" sandbox="allow-same-origin allow-popups"
              title="Email preview"></iframe>
    </div>
  </main>

</div>

<script>
/* ── STATE ──────────────────────────────── */
let messages    = [];
let selected    = null;
let syncedAt    = null;
let filterText  = '';
let senderFilter = '';

/* ── DOM ────────────────────────────────── */
const $ = id => document.getElementById(id);
const elList      = $('email-list');
const elNone      = $('no-emails');
const elSync      = $('last-sync').querySelector('span:last-child');
const elInfoSync  = $('info-sync');
const elEmpty     = $('email-empty');
const elViewer    = $('email-viewer');
const elSubject   = $('meta-subject');
const elActorFrom = $('meta-actor-from');
const elActorTo   = $('meta-actor-to');
const elTime      = $('meta-time');
const elFrom      = $('m-from');
const elTo        = $('m-to');
const elReplyTo   = $('m-replyto');
const elFrame     = $('email-frame');
const elFill      = $('progress-fill');
const elIco       = $('ico-refresh');
const elSenders   = $('senders-list');
const elCountAll  = $('nav-count-all');

/* ── HELPERS ────────────────────────────── */
function esc(s) {
  return String(s ?? '')
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function addrText(a) {
  if (!a) return '';
  if (typeof a === 'string') return a;
  const k = Object.keys(a)[0];
  const v = Object.values(a)[0];
  return v || k;
}

function addrEmail(a) {
  if (!a) return '';
  if (typeof a === 'string') return a;
  return Object.keys(a)[0] || '';
}

function addrHtml(a) {
  if (!a) return '<span style="color:var(--text-3)">—</span>';
  if (typeof a === 'string') {
    return `<span class="addr" style="margin-left:0">${esc(a)}</span>`;
  }
  const addr = Object.keys(a)[0];
  const name = Object.values(a)[0];
  if (name) return `${esc(name)}<span class="addr">${esc(addr)}</span>`;
  return `<span class="addr" style="margin-left:0">${esc(addr)}</span>`;
}

const palette = ['#ff5a73','#ff9d4a','#4ed3e8','#ff7eb6','#4cd084','#a78bfa','#3b5cff','#ffc24a'];

function hashIdx(str, n) {
  let h = 0;
  for (let i = 0; i < str.length; i++) {
    h = ((h << 5) - h + str.charCodeAt(i)) | 0;
  }
  return Math.abs(h) % n;
}

function senderColor(name) {
  return palette[hashIdx(name || '?', palette.length)];
}

function initials(name) {
  if (!name) return '?';
  const parts = name.trim().split(/[\s\.@]+/).filter(Boolean);
  if (parts.length === 0) return '?';
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
  return (parts[0][0] + parts[1][0]).toUpperCase();
}

function toDate(ts) {
  if (ts == null) return null;
  if (typeof ts === 'number')      return new Date(ts * 1000);
  if (typeof ts === 'string')      return new Date(ts);
  if (typeof ts === 'object' && ts.date) {
    return new Date(ts.date.replace(' ', 'T') + (ts.timezone === 'UTC' ? 'Z' : ''));
  }
  return null;
}

function fmtShort(ts) {
  const d = toDate(ts);
  if (!d || isNaN(d.getTime())) return '—';
  const now = new Date();
  if (d.toDateString() === now.toDateString())
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  return d.toLocaleDateString([], { month: 'short', day: 'numeric' });
}

function fmtFull(ts) {
  const d = toDate(ts);
  if (!d || isNaN(d.getTime())) return 'Unknown';
  const now = new Date();
  const day = d.toDateString() === now.toDateString()
    ? 'Today'
    : d.toLocaleDateString([], { weekday: 'short', month: 'short', day: 'numeric' });
  return `${day} · ${d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
}

function timeAgo(ms) {
  if (!ms) return '—';
  const s = Math.floor((Date.now() - ms) / 1000);
  if (s < 10)   return 'just now';
  if (s < 60)   return `${s}s ago`;
  if (s < 3600) return `${Math.floor(s / 60)}m ago`;
  return `${Math.floor(s / 3600)}h ago`;
}

/* ── PROGRESS ───────────────────────────── */
function startLoad() {
  elFill.className = 'loading';
  elIco.classList.add('spin');
}
function endLoad() {
  elFill.className = 'done';
  elIco.classList.remove('spin');
  setTimeout(() => { elFill.className = ''; }, 500);
}

/* ── FETCH ──────────────────────────────── */
async function fetchMessages(clear = false) {
  startLoad();
  try {
    const r = await fetch('fetch.php' + (clear ? '?clear=1' : ''));
    messages = await r.json();
    syncedAt = Date.now();
    if (clear) { selected = null; showEmpty(); }
    renderSenders();
    renderList();
    updateSync();
  } catch (e) {
    console.error('Fetch failed', e);
  } finally {
    endLoad();
  }
}

/* ── SENDERS SIDEBAR ────────────────────── */
function renderSenders() {
  const counts = new Map();
  for (const m of messages) {
    const n = addrText(m.headers['From']) || 'Unknown';
    counts.set(n, (counts.get(n) || 0) + 1);
  }
  elCountAll.textContent = messages.length;

  elSenders.querySelectorAll('.nav-item[data-sender]:not([data-sender=""])').forEach(n => n.remove());

  const sorted = [...counts.entries()].sort((a, b) => b[1] - a[1]);
  for (const [name, count] of sorted) {
    const el = document.createElement('div');
    el.className = 'nav-item' + (senderFilter === name ? ' active' : '');
    el.dataset.sender = name;
    el.innerHTML = `
      <span class="dot" style="background:${senderColor(name)}"></span>
      <span class="name">${esc(name)}</span>
      <span class="count">${count}</span>`;
    el.addEventListener('click', () => {
      senderFilter = senderFilter === name ? '' : name;
      renderSenders();
      renderList();
    });
    elSenders.appendChild(el);
  }

  elSenders.querySelectorAll('.nav-item[data-sender=""]').forEach(el => {
    el.classList.toggle('active', senderFilter === '');
    el.onclick = () => {
      if (senderFilter === '') return;
      senderFilter = '';
      renderSenders();
      renderList();
    };
  });
}

/* ── LIST ───────────────────────────────── */
function visible() {
  const q = filterText.toLowerCase();
  return messages.filter(m => {
    const h = m.headers;
    if (senderFilter && addrText(h['From']) !== senderFilter) return false;
    if (!q) return true;
    return [h['Subject'], addrText(h['From']), addrText(h['To'])]
      .some(v => (v || '').toLowerCase().includes(q));
  });
}

function renderList() {
  document.title = `(${messages.length}) Spool Reader`;
  elList.querySelectorAll('.e-item').forEach(n => n.remove());

  const items = visible();
  if (items.length === 0) {
    elNone.style.display = 'block';
    elNone.textContent = messages.length === 0
      ? 'The spool is empty.'
      : (filterText ? `No matches for "${filterText}".` : 'No messages from this sender.');
    return;
  }
  elNone.style.display = 'none';

  items.forEach((msg, i) => {
    const h       = msg.headers;
    const idx     = messages.indexOf(msg);
    const fromN   = addrText(h['From']) || '—';
    const toAddr  = h['X-Swift-To'] || h['To'];
    const toN     = addrText(toAddr) || '—';
    const subj    = h['Subject'] || '(No subject)';
    const ts      = fmtShort(h['Date']);
    const color   = senderColor(fromN);
    const inits   = initials(fromN);

    const el = document.createElement('div');
    el.className = 'e-item' + (idx === selected ? ' sel' : '');
    el.style.setProperty('--strip', color);
    el.style.animationDelay = `${i * 28}ms`;
    el.dataset.idx = idx;
    el.innerHTML = `
      <div class="e-row1">
        <div class="avatar">${esc(inits)}</div>
        <div class="e-meta">
          <div class="e-from">${esc(fromN)}</div>
          <div class="e-ts">${esc(ts)}</div>
        </div>
      </div>
      <div class="e-subject">${esc(subj)}</div>
      <div class="e-to"><span class="arrow">→</span>${esc(toN)}</div>`;
    el.addEventListener('click', () => selectEmail(idx));
    elList.appendChild(el);
  });
}

/* ── VIEWER ─────────────────────────────── */
function showEmpty() {
  elEmpty.style.display = 'flex';
  elViewer.classList.remove('open');
}

function selectEmail(idx) {
  selected = idx;
  elList.querySelectorAll('.e-item.sel').forEach(n => n.classList.remove('sel'));
  const next = elList.querySelector(`.e-item[data-idx="${idx}"]`);
  if (next) next.classList.add('sel');
  openEmail(idx);
}

function openEmail(idx) {
  const msg = messages[idx];
  if (!msg) return showEmpty();
  const h = msg.headers;

  elEmpty.style.display = 'none';
  elViewer.classList.remove('open');
  void elViewer.offsetWidth;
  elViewer.classList.add('open');

  elSubject.textContent = h['Subject'] || '(No subject)';
  elActorFrom.textContent = addrText(h['From']) || '—';
  const toAddr = h['X-Swift-To'] || h['To'];
  elActorTo.textContent = addrText(toAddr) || '—';
  elTime.textContent = fmtFull(h['Date']);

  elFrom.innerHTML    = addrHtml(h['From']);
  elReplyTo.innerHTML = h['Reply-To']
    ? addrHtml(h['Reply-To'])
    : '<span style="color:var(--text-3)">—</span>';

  if (h['X-Swift-To']) {
    const realTo = esc(addrText(h['To']));
    elTo.innerHTML = addrHtml(h['X-Swift-To'])
      + `<span class="via-tag">via ${realTo}</span>`;
  } else {
    elTo.innerHTML = addrHtml(h['To']);
  }

  const doc = elFrame.contentDocument || elFrame.contentWindow.document;
  doc.open();
  doc.write(msg.body || '<p style="padding:24px;color:#666;font-family:sans-serif">No content</p>');
  doc.close();
}

/* ── SYNC ───────────────────────────────── */
function updateSync() {
  const txt = 'synced ' + timeAgo(syncedAt);
  elSync.textContent = txt;
  elInfoSync.textContent = timeAgo(syncedAt);
}

/* ── EVENTS ─────────────────────────────── */
$('btn-refresh').addEventListener('click', () => fetchMessages());
$('btn-clear').addEventListener('click', () => {
  if (confirm('Delete all messages from the spool?')) fetchMessages(true);
});
$('search').addEventListener('input', e => {
  filterText = e.target.value;
  renderList();
});

/* ── INIT ───────────────────────────────── */
fetchMessages();
setInterval(fetchMessages, 30_000);
setInterval(updateSync,    15_000);
</script>
</body>
</html>
