/* ===========================================================
   Spool Reader · App logic
   =========================================================== */

/* ── STATE ─────────────────────────── */
let messages     = [];
let selected     = null;
let syncedAt     = null;
let filterText   = '';
let senderFilter = '';

/* ── DOM ───────────────────────────── */
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
const elPrint     = $('btn-print');

/* ── HELPERS ───────────────────────── */
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

/* ── PROGRESS ──────────────────────── */
function startLoad() {
  elFill.className = 'loading';
  elIco.classList.add('spin');
}
function endLoad() {
  elFill.className = 'done';
  elIco.classList.remove('spin');
  setTimeout(() => { elFill.className = ''; }, 500);
}

/* ── FETCH ─────────────────────────── */
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

/* ── SENDERS SIDEBAR ───────────────── */
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

/* ── LIST ──────────────────────────── */
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
    const h      = msg.headers;
    const idx    = messages.indexOf(msg);
    const fromN  = addrText(h['From']) || '—';
    const toAddr = h['X-Swift-To'] || h['To'];
    const toN    = addrText(toAddr) || '—';
    const subj   = h['Subject'] || '(No subject)';
    const ts     = fmtShort(h['Date']);
    const color  = senderColor(fromN);
    const inits  = initials(fromN);

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

/* ── VIEWER ────────────────────────── */
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

  elSubject.textContent   = h['Subject'] || '(No subject)';
  elActorFrom.textContent = addrText(h['From']) || '—';
  const toAddr            = h['X-Swift-To'] || h['To'];
  elActorTo.textContent   = addrText(toAddr) || '—';
  elTime.textContent      = fmtFull(h['Date']);

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

/* ── PRINT ─────────────────────────── */
function printEmail() {
  const win = elFrame.contentWindow;
  if (!win) return;
  win.focus();
  win.print();
}

/* ── SYNC ──────────────────────────── */
function updateSync() {
  elSync.textContent = 'synced ' + timeAgo(syncedAt);
  elInfoSync.textContent = timeAgo(syncedAt);
}

/* ── EVENTS ────────────────────────── */
$('btn-refresh').addEventListener('click', () => fetchMessages());
$('btn-clear').addEventListener('click', () => {
  if (confirm('Delete all messages from the spool?')) fetchMessages(true);
});
$('search').addEventListener('input', e => {
  filterText = e.target.value;
  renderList();
});
elPrint.addEventListener('click', printEmail);

/* ── INIT ──────────────────────────── */
fetchMessages();
setInterval(fetchMessages, 30_000);
setInterval(updateSync,    15_000);
