<style>
.mg-app, .mg-page { --mg-accent: var(--ds-accent, var(--accent, #1DB87A)); --mg-border:#E2E8F0; --mg-text:#0F172A; --mg-mute:#64748B; --mg-bg:#F8FAFC;
    font-family: 'Inter', system-ui, sans-serif; color: var(--mg-text); }
.mg-page h1 { font: 700 22px/1.2 'Sora','Inter',sans-serif; margin: 0 0 4px; }
.mg-page .mg-sub { color: var(--mg-mute); font-size: 13px; margin: 0 0 18px; }

/* tons par type d'expéditeur */
.tone-admin   { --t:#0B1E3D; } .tone-hopital { --t:#0E9F6E; } .tone-police { --t:#2563EB; }
.tone-pompier { --t:#DC2626; } .tone-service { --t:#7C3AED; } .tone-citoyen { --t:#64748B; }

.mg-av { width:34px; height:34px; border-radius:50%; flex:none; display:flex; align-items:center; justify-content:center;
    background:var(--t,#64748B); color:#fff; font:700 12px/1 'Inter',sans-serif; }
.mg-av.lg { width:44px; height:44px; font-size:15px; }
.mg-badge { display:inline-block; padding:1px 7px; border-radius:99px; font-size:10.5px; font-weight:600;
    color:var(--t); background:color-mix(in srgb, var(--t) 12%, white); }
.mg-tag { display:inline-block; padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:#F1F5F9; color:#475569; }
.mg-tag.warn { background:#FEF3C7; color:#92400E; } .mg-tag.ok { background:#DCFCE7; color:#166534; }

/* ── liste des groupes ── */
.mg-list { background:#fff; border:1px solid var(--mg-border); border-radius:12px; overflow:hidden; }
.mg-row { display:flex; gap:12px; align-items:center; padding:12px 16px; border-bottom:1px solid var(--mg-border); text-decoration:none; color:inherit; }
.mg-row:last-child { border-bottom:0; }
a.mg-row:hover { background:var(--mg-bg); }
.mg-row-main { flex:1; min-width:0; }
.mg-row-title { display:flex; gap:8px; align-items:center; font-weight:600; font-size:14px; }
.mg-row-title span:first-child { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.mg-row-prev { font-size:12.5px; color:var(--mg-mute); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-top:2px; }
.mg-row-side { text-align:right; font-size:11.5px; color:var(--mg-mute); display:flex; flex-direction:column; gap:6px; align-items:flex-end; }
.mg-unread { background:var(--mg-accent); color:#fff; border-radius:99px; min-width:20px; padding:1px 7px; font-weight:700; font-size:11px; text-align:center; }
.mg-h2 { font:700 13px/1 'Inter',sans-serif; color:var(--mg-mute); margin:22px 0 8px; }

/* ── fenêtre de discussion ── */
.mg-app { display:grid; grid-template-columns:minmax(0,1fr) 290px; height:calc(100dvh - 150px); min-height:460px;
    background:#fff; border:1px solid var(--mg-border); border-radius:12px; overflow:hidden; }
.mg-chat { display:flex; flex-direction:column; min-width:0; min-height:0; position:relative; }
.mg-top { display:flex; align-items:center; gap:12px; padding:10px 14px; border-bottom:1px solid var(--mg-border); background:#fff; }
.mg-top-t { flex:1; min-width:0; } .mg-top-t b { display:block; font:700 15px/1.2 'Sora','Inter',sans-serif; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.mg-top-t small { color:var(--mg-mute); font-size:12px; }
.mg-btn { border:1px solid var(--mg-border); background:#fff; color:var(--mg-text); border-radius:8px; padding:7px 12px; font:600 12.5px/1 'Inter',sans-serif; cursor:pointer; text-decoration:none; display:inline-block; }
.mg-btn:hover { background:var(--mg-bg); }
.mg-btn.primary { background:var(--mg-accent); border-color:var(--mg-accent); color:#fff; }
.mg-btn.danger { color:#B91C1C; }
.mg-btn:focus-visible, .mg-more:focus-visible, textarea:focus-visible { outline:2px solid var(--mg-accent); outline-offset:1px; }

.mg-scroll { flex:1; overflow-y:auto; padding:14px 16px; background:#F1F5F9; scroll-behavior:auto; }
.mg-older { display:block; margin:0 auto 12px; }
.mg-day { text-align:center; margin:14px 0 8px; }
.mg-day span { background:#E2E8F0; color:#475569; font-size:11.5px; font-weight:600; padding:3px 10px; border-radius:99px; }
.mg-empty { text-align:center; color:var(--mg-mute); padding:48px 16px; font-size:13.5px; }

.mg-msg { display:flex; gap:8px; align-items:flex-end; margin-top:10px; max-width:100%; }
.mg-msg.cont { margin-top:2px; }
.mg-msg.cont .mg-av { visibility:hidden; height:0; }
.mg-msg.cont .mg-head { display:none; }
.mg-msg.mine { flex-direction:row-reverse; }
.mg-msg.mine .mg-av { display:none; }
.mg-bubble { max-width:min(74%, 560px); background:#fff; border:1px solid var(--mg-border); border-radius:14px 14px 14px 4px;
    padding:7px 11px 6px; position:relative; min-width:84px; }
.mg-msg.mine .mg-bubble { background:color-mix(in srgb, var(--mg-accent) 15%, white); border-color:color-mix(in srgb, var(--mg-accent) 30%, white); border-radius:14px 14px 4px 14px; }
.mg-msg.flash .mg-bubble { box-shadow:0 0 0 3px color-mix(in srgb, var(--mg-accent) 40%, transparent); }
.mg-head { display:flex; gap:6px; align-items:center; margin-bottom:2px; flex-wrap:wrap; }
.mg-name { font-weight:700; font-size:12.5px; color:var(--t,#334155); }
.mg-text { font-size:14px; line-height:1.45; overflow-wrap:anywhere; }
.mg-text a { color:#1D4ED8; }
.mg-meta { display:flex; justify-content:flex-end; align-items:center; gap:6px; font-size:10.5px; color:var(--mg-mute); margin-top:3px; }
.mg-quote { border-left:3px solid var(--t,#94A3B8); background:rgba(15,23,42,.05); border-radius:6px; padding:4px 8px; margin:2px 0 5px; font-size:12px; cursor:pointer; }
.mg-quote b { display:block; color:var(--t,#334155); font-size:11.5px; }
.mg-quote span { color:#475569; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:36ch; }
.mg-msg.deleted .mg-text { color:var(--mg-mute); font-style:italic; }
.mg-more { border:0; background:transparent; color:var(--mg-mute); cursor:pointer; font-size:16px; line-height:1; padding:0 2px; border-radius:4px; }
.mg-more:hover { background:rgba(15,23,42,.08); }
.mg-menu { position:absolute; right:6px; bottom:26px; z-index:5; background:#fff; border:1px solid var(--mg-border); border-radius:10px;
    box-shadow:0 8px 24px rgba(15,23,42,.14); padding:4px; min-width:150px; }
.mg-menu[hidden] { display:none; }
.mg-menu button { display:block; width:100%; text-align:left; border:0; background:transparent; padding:8px 10px; border-radius:6px; font:500 13px 'Inter',sans-serif; cursor:pointer; }
.mg-menu button:hover { background:var(--mg-bg); } .mg-menu button.del { color:#B91C1C; }
.mg-edit textarea { width:100%; box-sizing:border-box; min-height:60px; border:1px solid var(--mg-border); border-radius:8px; padding:6px 8px; font:14px 'Inter',sans-serif; resize:vertical; }
.mg-edit div { display:flex; gap:6px; justify-content:flex-end; margin-top:6px; }

.mg-newpill { position:absolute; left:50%; transform:translateX(-50%); bottom:92px; z-index:4; }

.mg-compose { border-top:1px solid var(--mg-border); background:#fff; padding:10px 12px; }
.mg-reply { display:flex; align-items:center; gap:10px; background:var(--mg-bg); border-left:3px solid var(--mg-accent); border-radius:6px; padding:6px 10px; margin-bottom:8px; font-size:12.5px; }
.mg-reply[hidden] { display:none; }
.mg-reply div { flex:1; min-width:0; } .mg-reply b { display:block; } .mg-reply span { color:#475569; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.mg-form { display:flex; gap:8px; align-items:flex-end; }
.mg-form textarea { flex:1; resize:none; border:1px solid var(--mg-border); border-radius:12px; padding:9px 12px; font:14px/1.4 'Inter',sans-serif; max-height:140px; min-height:40px; box-sizing:border-box; }
.mg-form .mg-btn { min-height:40px; padding:0 18px; }
.mg-readonly { text-align:center; font-size:13px; color:var(--mg-mute); padding:6px 0; }
.mg-counter { text-align:right; font-size:11px; color:var(--mg-mute); margin-top:3px; }
.mg-toast { position:absolute; left:50%; top:62px; transform:translateX(-50%); background:#0F172A; color:#fff; font-size:12.5px; padding:8px 14px; border-radius:8px; z-index:6; }

/* ── panneau latéral ── */
.mg-side { border-left:1px solid var(--mg-border); overflow-y:auto; padding:16px; background:#fff; font-size:13px; }
.mg-side h3 { font:700 12px/1 'Inter',sans-serif; color:var(--mg-mute); margin:18px 0 8px; } .mg-side h3:first-child { margin-top:0; }
.mg-side p { margin:0 0 6px; color:#475569; line-height:1.45; }
.mg-who { display:flex; gap:9px; align-items:center; padding:6px 0; }
.mg-who .mg-av { width:30px; height:30px; font-size:11px; }
.mg-who div { min-width:0; } .mg-who b { display:block; font-size:13px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.mg-close-side { display:none; }

@media (max-width: 900px) {
    .mg-app { grid-template-columns:1fr; height:calc(100dvh - 120px); }
    .mg-side { position:absolute; inset:0 0 0 auto; width:min(320px,88%); z-index:8; box-shadow:-8px 0 24px rgba(15,23,42,.18); display:none; }
    .mg-app.side-open .mg-side { display:block; }
    .mg-close-side { display:inline-block; float:right; }
    .mg-bubble { max-width:86%; }
}
@media (prefers-reduced-motion: reduce) { .mg-scroll { scroll-behavior:auto; } }
</style>
