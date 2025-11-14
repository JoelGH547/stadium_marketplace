<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= esc($title ?? $siteName ?? 'Sports Arena') ?></title>

<!-- Tailwind -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- CSS Custom -->
<style>
  :root{
  --primary:#0ea5a4; --primary-contrast:#fff; --panel:#fff;
  --line:#e5e7eb; --muted:#6b7280; --text:#111827;
}
.accent-bar{height:4px;background:var(--primary);}

/* ===== Deck (Ultra Smooth Base Styles — no extra morph here) ===== */
.deck-box{ border-radius:1rem; overflow:visible; box-shadow:0 10px 26px rgba(0,0,0,.10), inset 0 0 0 1px rgba(255,255,255,.08); }
.deck-stage{ position:relative; width:100%; height:20rem; }
.deck-card{
  position:absolute; inset:0; border-radius:1rem; overflow:hidden;
  contain: layout paint; backface-visibility:hidden;
  will-change: transform, opacity; transform: translate3d(0,0,0);
  transition: none; content-visibility: auto;
}
.deck-card img{ width:100%; height:100%; object-fit:cover; display:block; }
.is-current{ z-index:30; }
.is-prev,.is-next{ z-index:20; }
.is-hidden{ z-index:10; opacity:0; pointer-events:none; }
.deck-nav{
  position:absolute; top:50%; transform:translateY(-50%);
  width:40px; height:40px; display:grid; place-items:center;
  background:#fff; color:var(--primary); border-radius:999px;
  box-shadow:0 8px 20px rgba(0,0,0,.12); z-index:40;
}
.deck-prev{ left:.75rem; } .deck-next{ right:.75rem; }
@media (prefers-reduced-motion: reduce){ .deck-card{ transition-duration:0ms !important; } }

/* ===== Horizontal Scroller arrows (minimal custom) ===== */
.no-scrollbar{ scrollbar-width:none; }
.no-scrollbar::-webkit-scrollbar{ display:none; }
.scroller-btn{
  position:absolute; top:50%; transform:translateY(-50%);
  width:32px; height:32px; display:grid; place-items:center;
  color:var(--primary); font-size:1.8rem; z-index:20;
}
.scroller-left{ left:-60px; } .scroller-right{ right:-60px; }
@media (min-width:1024px){ .scroller-left{ left:-72px; } .scroller-right{ right:-72px; } }

/* pill for distance badge */
.dist-badge{display:inline-flex;align-items:center;gap:.25rem;padding:.125rem .5rem;border-radius:9999px;background:rgba(14,165,164,.10);color:var(--primary);line-height:1;}
html.dark .dist-badge{background:rgba(14,165,164,.18);}

</style>
</head>

<body class="bg-gray-50 text-[var(--text)] antialiased">

<?= $this->include('layouts/public_header') ?>

<main>
  <?= $this->renderSection('content') ?>
</main>

<?= $this->include('layouts/public_footer') ?>

<script src="<?= base_url('assets/ui.js') ?>"></script>
</body>
</html>
