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

/* Jelly card effect สำหรับ section สนามใกล้คุณ */
  .near-card-group {
    perspective: 1200px;
  }

  .near-card {
    position: relative;
    border-radius: 1.25rem;
    background: rgba(255,255,255,0.96);
    box-shadow:
      0 18px 45px rgba(15,23,42,0.55),
      0 0 0 1px rgba(15,23,42,0.06);
    overflow: hidden;
    transform-origin: center center;
    transition:
      transform 260ms cubic-bezier(0.22, 0.61, 0.36, 1),
      box-shadow 260ms cubic-bezier(0.22, 0.61, 0.36, 1),
      background 260ms ease-out;
  }

  .near-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top left,
                rgba(14,165,164,0.22),
                transparent 55%);
    opacity: 0;
    transition: opacity 280ms ease-out;
    pointer-events: none;
  }

  .near-card:hover {
    transform: translateY(-6px) rotateX(4deg) rotateY(-3deg);
    box-shadow:
      0 26px 60px rgba(15,23,42,0.65),
      0 0 0 1px rgba(15,23,42,0.08);
    background: rgba(255,255,255,0.98);
  }

  .near-card:hover::before {
    opacity: 1;
  }

  .near-card-img {
    transition: transform 380ms cubic-bezier(0.22, 0.61, 0.36, 1);
  }

  .near-card:hover .near-card-img {
    transform: scale(1.04) translateY(-2px);
  }

  .near-card-wave {
    position: absolute;
    inset-inline: 0;
    bottom: -26px;
    height: 52px;
    background:
      radial-gradient(ellipse at top,
        rgba(15,23,42,0.18),
        transparent 60%);
    opacity: 0;
    transition: opacity 260ms ease-out, transform 260ms ease-out;
    transform: translateY(10px);
    pointer-events: none;
  }

  .near-card:hover .near-card-wave {
    opacity: 1;
    transform: translateY(0);
  }
  /* Jelly curve card (ดัดจาก CodePen zebateira) สำหรับ "สนามใกล้คุณ" */

 
  .near-jelly-wrap {
    position: relative;
    height: 230px; /* การ์ดเตี้ยลง */
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 40px -10px rgba(0,0,0,0.7);
  }

  .near-jelly-bg {
    position: absolute;
    top: -40px;
    left: -40px;
    width: 120%;
    height: 120%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    filter: blur(24px);
    -webkit-filter: blur(24px);
  }

  .near-jelly-card {
    position: absolute;
    inset: 0;
    margin: auto;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
  }

  /* แถบเบลอครึ่งล่าง (เข้มขึ้น) */
  .near-jelly-blur {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 80%; /* ครึ่งล่าง + นิดหน่อย */
    background:
      linear-gradient(to top, rgba(0,0,0,0.92), rgba(0,0,0,0.3) 55%, transparent),
      radial-gradient(ellipse at bottom, rgba(0,0,0,0.85), transparent 65%);
    opacity: 0;
    transition: opacity 0.18s ease-in;
    pointer-events: none;
  }

  .near-jelly-card:hover .near-jelly-blur {
    opacity: 1;
  }

  .near-jelly-footer {
    z-index: 1;
    position: absolute;
    height: 100px;
    width: 100%;
    bottom: 0;
  }

  .near-jelly-curve {
    position: absolute;
    fill: white;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 230px;
  }

  .near-jelly-info {
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    padding: 0 18px 14px 18px;
    transform: translateY(210px);
    transition: transform 1s cubic-bezier(.31,1.21,.64,1.02);
    color: #e5e7eb;
  }

  .near-jelly-card:hover .near-jelly-info {
    transform: translateY(0px);
  }

  .near-jelly-name {
    font-weight: 700;
    font-size: 0.98rem;
    padding-top: 4px;
    color: #f9fafb; /* ชื่อสนามเป็นสีขาว */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-shadow: 0 1px 3px rgba(0,0,0,0.7);
  }

  .near-jelly-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    margin-top: 6px;
    gap: 6px;
    font-size: 0.72rem;
  }

  .near-jelly-meta .stars {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    color: #fbbf24;
    font-weight: 500;
    text-shadow: 0 1px 2px rgba(0,0,0,0.7);
  }

  .near-jelly-meta .dist-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 999px;
    background: rgba(15,23,42,0.95);
    color: #fff;
    box-shadow: 0 4px 10px rgba(15,23,42,0.6);
  }

  .near-jelly-price {
    margin-top: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    color: #f9fafb; /* ราคาเป็นสีขาว */
    text-shadow: 0 1px 3px rgba(0,0,0,0.7);
  }

  /* Badge ประเภทกีฬา + emoji มุมขวาล่าง — มีอนิเมชันเด้งขึ้น */
  .near-jelly-sport {
    position: absolute;
    right: 18px;
    bottom: 18px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(15,23,42,0.98);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 600;
    box-shadow: 0 8px 18px rgba(15,23,42,0.75);
    transform: translateY(220px); /* เริ่มซ่อนอยู่ด้านล่างเหมือนข้อความ */
    transition: transform 1s cubic-bezier(.31,1.21,.64,1.02);
  }

  .near-jelly-card:hover .near-jelly-sport {
    transform: translateY(0); /* เด้งขึ้นมาพร้อม ๆ กับชื่อสนาม/ข้อมูล */
  }

  .near-jelly-sport-emoji {
    font-size: 0.95rem;
    line-height: 1;
  }
</style>
</head>

<body class="bg-gray-50 text-[var(--text)] antialiased">

<?= $this->include('layouts/public_header') ?>

<main>
  <?= $this->renderSection('content') ?>
</main>

<?= $this->include('layouts/public_footer') ?>

<script src="<?= base_url('assets/home.js') ?>"></script>
<script src="<?= base_url('assets/view.js') ?>"></script>
<script src="<?= base_url('assets/show.js') ?>"></script>
</body>
</html>
