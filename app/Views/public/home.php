<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<!-- HERO -->
<div class="relative" id="home">
  <img src="<?= base_url('assets/uploads/home/batminton.webp') ?>" alt="Sports Arena"
       class="block h-[48vh] w-full object-cover select-none" fetchpriority="high">
  <div class="pointer-events-none absolute inset-x-0 bottom-0 h-40 sm:h-44 md:h-48 bg-gradient-to-t from-gray-100/90 via-gray-100/60 to-transparent"></div>
  <svg class="pointer-events-none absolute bottom-[-1px] left-0 w-full h-32 sm:h-36 md:h-40 text-[var(--primary)] z-[2]"
       viewBox="0 0 1440 320" preserveAspectRatio="none">
    <path fill="currentColor" d="M0,240 C240,200 480,140 720,170 C960,200 1200,280 1440,230 L1440,320 L0,320 Z"></path>
  </svg>
</div>

<main id="top">
<section id="hero" class="relative bg-[var(--primary)] text-white overflow-hidden">
  <div class="absolute inset-0 bg-gradient-to-b from-[var(--primary)]/70 via-[var(--primary)]/55 to-[var(--primary)]/45 z-0"></div>

  <!-- Balls canvas -->
  <canvas id="heroBalls" class="absolute inset-0 z-10 pointer-events-none"></canvas>

  <div class="relative z-20 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
      <div>
        <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight drop-shadow-md">จองสนามกีฬาใกล้คุณได้ง่ายๆ</h1>
        <p class="mt-4 text-lg text-white/90 text-center lg:text-left">ค้นหาและจองสนาม ฟุตซอล แบดมินตัน เทนนิส และสนามอื่นๆ พร้อมระบบนัดหมายและชำระเงินออนไลน์</p>
        <!-- ปุ่มค้นหา -->
        <div class="mt-6">
          <button id="openSearch" class="inline-flex items-center gap-3 bg-white text-[var(--primary)] border border-transparent px-6 py-3 rounded-full shadow-md hover:shadow-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/><circle cx="11" cy="11" r="6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="text-sm font-medium">ค้นหาสนาม</span>
          </button>
        </div>
      </div>

      <!-- Deck -->
      <div class="hidden lg:block relative z-30">
        <div class="deck-box border border-white/10 bg-white/5">
          <div id="heroCarousel" class="deck-stage" aria-roledescription="carousel">
            <?php
              $heroSlides = [
                'assets/uploads/home/1.jpg'
                ,'assets/uploads/home/2.jpg'
                ,'assets/uploads/home/3.jpg'
                ,'assets/uploads/home/4.jpg'
                ,'assets/uploads/home/5.jpg'
              ];
            ?>
            <?php foreach ($heroSlides as $i => $img): ?>
                <div class="deck-card <?= $i === 0 ? 'is-current' : 'is-next' ?>" data-slide="<?= $i ?>">
                <img src="<?= base_url($img) ?>" alt="Slide <?= $i+1 ?>">
                </div>
            <?php endforeach; ?>
          </div>
          <button id="prevSlide" class="deck-nav deck-prev" aria-label="Previous">‹</button>
          <button id="nextSlide" class="deck-nav deck-next" aria-label="Next">›</button>
        </div>
      </div>
    </div>
  </div>

  <!-- bottom gray wave -->
  <svg class="pointer-events-none absolute bottom-[-1px] left-0 w-full h-24 sm:h-28 md:h-32 z-[2]"
       viewBox="0 0 1440 320" preserveAspectRatio="none">
    <path fill="#f3f4f6" d="M0,90 C220,130 460,200 720,170 C980,140 1210,100 1440,140 L1440,320 L0,320 Z"></path>
  </svg>
  </div>
</section>

<!-- Backdrop/Search -->
<div id="searchBackdrop" class="hidden fixed inset-0 bg-black/50 z-40"></div>
<div id="searchPanel" class="hidden fixed left-1/2 transform -translate-x-1/2 top-28 z-50 w-[92%] max-w-md max-h-[75vh] overflow-auto bg-white rounded-xl shadow-2xl p-6">
  <div class="flex items-center justify-between mb-3">
    <div class="flex items-center gap-3">
      <button id="closeSearch" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="flex-1">
        <input id="searchInput" type="text" placeholder="ค้นหาชื่อสนาม" class="w-[350px] rounded-lg border border-[var(--line)] px-4 py-3 text-sm text-[var(--primary)] placeholder-[var(--primary)] focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]" />
      </div>
    </div>
  </div>
  <div class="mb-3">
    <div class="text-sm font-medium mb-2 text-[var(--primary)]">เลือกวันที่และเวลา</div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <input type="date" class="rounded-lg border border-[var(--line)] px-4 py-3 text-[var(--primary)] focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]" />
      <input type="time" class="rounded-lg border border-[var(--line)] px-4 py-3 text-[var(--primary)] focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]" />
      <input type="time" class="rounded-lg border border-[var(--line)] px-4 py-3 text-[var(--primary)] focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]" />
    </div>
  </div>
  <div class="mb-3">
    <div class="flex items-center justify-between mb-2">
      <div class="text-sm font-medium text-[var(--primary)]">เลือกประเภทกีฬา</div>
      <button id="clearSports" class="text-sm text-[var(--primary)] hover:underline">Clear</button>
    </div>
    <div class="grid grid-cols-4 gap-3">
      <?php $sports=['Football','Basketball','Tennis','Swimming','Running','Badminton']; foreach($sports as $s): ?>
      <button type="button" class="sport-btn flex flex-col items-center gap-2 p-3 rounded-lg border border-[var(--line)] bg-gray-50 hover:bg-[var(--panel)] text-sm text-[var(--primary)] transition">
        <div class="h-10 w-10 rounded-md bg-white flex items-center justify-center text-[var(--primary)]">⚽</div>
        <div class="truncate w-20 text-xs"><?= htmlspecialchars($s) ?></div>
      </button>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="mt-4">
    <button id="doSearch" class="w-full bg-[var(--primary)] text-[var(--primary-contrast)] py-3 rounded-lg text-lg font-medium hover:opacity-90 transition">ค้นหา</button>
  </div>
</div>

<!-- Featured (Horizontal Cards with Arrow Buttons) -->
<section id="results" class="relative isolate z-[10] bg-[var(--primary)] py-16">
  <!-- พื้นหลังลายจุด + แสงฟุ้ง + คลื่นด้านล่าง (ของเดิม) -->
  <div class="pointer-events-none absolute inset-0 -z-0"
       style="
        background-image:
          radial-gradient(rgba(255,255,255,0.25) 1px, transparent 1px);
        background-size: 20px 20px;
        background-position: 0 8px;
       "></div>

  <div class="pointer-events-none absolute inset-0 -z-0 overflow-hidden">
    <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[900px] rounded-full opacity-25 blur-3xl"
         style="background: radial-gradient(ellipse at center, rgba(255,255,255,.45), rgba(14,165,164,0) 60%);"></div>
    <svg class="absolute bottom-0 left-0 w-full h-24 opacity-15"
         viewBox="0 0 1440 320" preserveAspectRatio="none" aria-hidden="true">
      <path fill="#ffffff" d="M0,160 C240,200 480,120 720,160 C960,200 1200,280 1440,240 L1440,320 L0,320 Z"></path>
    </svg>
  </div>

  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-[5]">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-semibold text-white">สนามใกล้คุณ</h2>
      <a href="#venueList" class="text-sm text-white/90 hover:text-white transition">ดูทั้งหมด</a>
    </div>

    <?php
      /** @var array $venueCards */
      $venueCards = $venueCards ?? [];
      $nearby = array_slice($venueCards, 0, 8);
    ?>

    <div class="relative z-[10]">
      <!-- ปุ่มเลื่อน -->
      <button id="nearLeft"
              class="scroller-btn scroller-left text-white hover:text-white/90"
              aria-label="เลื่อนไปทางซ้าย">‹</button>
      <button id="nearRight"
              class="scroller-btn scroller-right text-white hover:text-white/90"
              aria-label="เลื่อนไปทางขวา">›</button>

      <div id="nearScroller"
           class="mt-2 -mx-4 px-4 flex gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory relative z-[15]">

        <?php if (!empty($nearby)): ?>
          <?php foreach ($nearby as $i => $v): ?>
            <?php
              $name  = $v['name'] ?? 'ชื่อสนาม';
              $price = isset($v['price']) ? (float) $v['price'] : 0;

              $typeLabel = $v['type_label'] ?? ($v['category_name'] ?? 'สนามกีฬา');
              $typeIcon  = $v['type_icon']  ?? ($v['category_emoji'] ?? '🏟️');

              $cover    = $v['cover_image'] ?? null;
              $coverUrl = $cover
                ? base_url('assets/uploads/stadiums/' . $cover)
                : base_url('assets/uploads/home/1.jpg');

              $lat = $v['lat'] ?? null;
              $lng = $v['lng'] ?? null;

              $stars = isset($v['rating']) ? (float) $v['rating'] : 0.0;

              // ใช้ id ไม่ซ้ำในแต่ละการ์ด เพื่อให้ SVG animation แยกกันได้
              $uid = 'nearCard' . $i;
            ?>

            <article
              class="relative snap-start flex-none min-w-[260px] sm:min-w-[280px] max-w-xs"
              <?php if (!empty($lat) && !empty($lng)): ?>
                data-lat="<?= esc($lat) ?>"
                data-lng="<?= esc($lng) ?>"
              <?php endif; ?>
            >
              <div class="near-jelly-wrap">
                <!-- พื้นหลังเบลอ -->
                <div class="near-jelly-bg"
                     style="background-image:url('<?= esc($coverUrl) ?>');"></div>

                <!-- การ์ดด้านหน้า -->
                <div class="near-jelly-card"
                     style="background-image:url('<?= esc($coverUrl) ?>');">
                  <div class="near-jelly-footer">
  <!-- SVG curve แบบ CodePen -->
  <svg class="near-jelly-curve"
       xmlns="http://www.w3.org/2000/svg">
    <path id="<?= $uid ?>-p"
          d="M0,200 Q80,100 400,200 V150 H0 V50"
          transform="translate(0 300)"/>
    <rect id="<?= $uid ?>-dummy"
          x="0" y="0" height="450" width="400"
          fill="transparent" />

    <animate xlink:href="#<?= $uid ?>-p"
             attributeName="d"
             to="M0,50 Q80,100 400,50 V150 H0 V50"
             fill="freeze"
             begin="<?= $uid ?>-dummy.mouseover"
             end="<?= $uid ?>-dummy.mouseout"
             dur="0.1s" />
    <animate xlink:href="#<?= $uid ?>-p"
             attributeName="d"
             to="M0,50 Q80,0 400,50 V150 H0 V50"
             fill="freeze"
             begin="prev.end;<?= $uid ?>-dummy.mouseover"
             end="<?= $uid ?>-dummy.mouseout"
             dur="0.15s" />
    <animate xlink:href="#<?= $uid ?>-p"
             attributeName="d"
             to="M0,50 Q80,80 400,50 V150 H0 V50"
             fill="freeze"
             begin="prev.end;<?= $uid ?>-dummy.mouseover"
             end="<?= $uid ?>-dummy.mouseout"
             dur="0.15s" />
    <animate xlink:href="#<?= $uid ?>-p"
             attributeName="d"
             to="M0,50 Q80,45 400,50 V150 H0 V50"
             fill="freeze"
             begin="prev.end;<?= $uid ?>-dummy.mouseover"
             end="<?= $uid ?>-dummy.mouseout"
             dur="0.1s" />
    <animate xlink:href="#<?= $uid ?>-p"
             attributeName="d"
             to="M0,50 Q80,50 400,50 V150 H0 V50"
             fill="freeze"
             begin="prev.end;<?= $uid ?>-dummy.mouseover"
             end="<?= $uid ?>-dummy.mouseout"
             dur="0.05s" />
    <animate xlink:href="#<?= $uid ?>-p"
             attributeName="d"
             to="M0,200 Q80,100 400,200 V150 H0 V50"
             fill="freeze"
             begin="<?= $uid ?>-dummy.mouseout"
             dur="0.15s" />
  </svg>

  <!-- ข้อมูลสนาม -->
  <div class="near-jelly-info">
    <div class="near-jelly-name">
      <?= esc($name) ?>
    </div>
    <div class="near-jelly-meta">
      <span class="stars">
        <span>⭐</span>
        <span><?= number_format($stars, 1) ?></span>
      </span>
      <span class="dist-badge">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-3 w-3"
             viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 2C8.134 2 5 5.134 5 9c0 4.5 4 9 7 11 3-2 7-6.5 7-11 0-3.866-3.134-7-7-7z" />
          <circle cx="12" cy="9" r="2.5" />
        </svg>
        <span>-- km.</span>
      </span>
    </div>
    <div class="near-jelly-price">
      ฿<?= number_format($price, 0) ?>/hr.
    </div>
  </div>

  <!-- Badge ประเภทกีฬา + emoji มุมขวาล่าง -->
  <div class="near-jelly-sport">
    <span class="near-jelly-sport-emoji"><?= esc($typeIcon) ?></span>
    <span><?= esc($typeLabel) ?></span>
  </div>
</div>

<!-- ชั้นเบลอครึ่งล่าง (ปรับแล้วด้านบน) -->
<div class="near-jelly-blur"></div>

                </div>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>




<section id="sortMenu" class="bg-gray-50 py-6">
  <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-sm flex justify-between items-center overflow-hidden border border-gray-200">

      <!-- ปุ่มยอดนิยม (active เริ่มต้น) -->
      <button class="sort-btn flex-1 py-3 text-center text-sm font-semibold bg-[var(--primary)] text-white"
              data-sort="popular" aria-selected="true">ยอดนิยม</button>

      <div class="w-px h-8 bg-gray-200"></div>

      <button class="sort-btn flex-1 py-3 text-center text-sm font-semibold text-gray-700 hover:text-[var(--primary)] hover:bg-[var(--primary)]/10"
              data-sort="price" aria-selected="false">ราคาถูกสุด</button>

      <div class="w-px h-8 bg-gray-200"></div>

      <button class="sort-btn flex-1 py-3 text-center text-sm font-semibold text-gray-700 hover:text-[var(--primary)] hover:bg-[var(--primary)]/10"
              data-sort="nearby" aria-selected="false">ใกล้ตัวเมืองที่สุด</button>

      <div class="w-px h-8 bg-gray-200"></div>

      <button class="sort-btn flex-1 py-3 text-center text-sm font-semibold text-gray-700 hover:text-[var(--primary)] hover:bg-[var(--primary)]/10"
              data-sort="rating" aria-selected="false">ได้คะแนนรีวิวสูง</button>
    </div>
  </div>
</section>

<section id="venueList" class="py-4 bg-gray-50">
  <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
    <?php
      /** @var array $venueCards */
      $venueCards = $venueCards ?? [];
    ?>
    <ul id="venueItems" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <?php if (empty($venueCards)): ?>
        <!-- fallback: ถ้าไม่มีข้อมูลจาก DB ตอนนี้ยังไม่แสดงอะไร -->
      <?php else: ?>
        <?php foreach ($venueCards as $idx => $v): ?>
          <?php
            $name    = $v['name'] ?? 'ชื่อสนาม';
            $price   = isset($v['price']) ? (float) $v['price'] : 0;

            $address = trim(($v['address'] ?? '') . ' ' . ($v['province'] ?? ''));
            $address = $address !== '' ? $address : 'ยังไม่ระบุที่อยู่';

            $open  = $v['open_time']  ?? null;
            $close = $v['close_time'] ?? null;
            if ($open  !== null && strlen($open)  >= 5) $open  = substr($open, 0, 5);
            if ($close !== null && strlen($close) >= 5) $close = substr($close, 0, 5);
            $timeLabel = ($open && $close) ? ($open . ' – ' . $close) : 'ยังไม่ระบุเวลา';

            $typeIcon  = $v['type_icon']  ?? '🏟️';
            $typeLabel = $v['type_label'] ?? ($v['category_name'] ?? 'สนามกีฬา');

            $cover    = $v['cover_image'] ?? null;
            $coverUrl = $cover
              ? base_url('assets/uploads/stadiums/' . $cover)
              : base_url('assets/uploads/home/1.jpg');

            $lat = $v['lat'] ?? null;
            $lng = $v['lng'] ?? null;
          ?>
          <li class="relative flex items-center gap-4 bg-white rounded-2xl
           p-4 pb-8 sm:p-5 sm:pb-10 pr-16
           transition-all duration-200 hover:shadow-lg"
    data-price="<?= esc($price) ?>"
    data-distance-km=""
    data-rating="0"
    data-popular="<?= 100 - (int) $idx ?>"
    <?php if (!empty($lat) && !empty($lng)): ?>
      data-lat="<?= esc($lat) ?>"
      data-lng="<?= esc($lng) ?>"
    <?php endif; ?>
>
  <img src="<?= esc($coverUrl) ?>" class="h-24 w-24 rounded-2xl object-cover" alt="">
  <div class="flex-1 min-w-0">
    <div class="flex items-center gap-2">
      <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">
        <?= esc($name) ?>
      </h3>
    </div>

    <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
      <span class="inline-flex items-center gap-1 rounded-full dist-badge px-2.5 py-0.5">
        📍 <span>-- km.</span>
      </span>
      <span class="text-gray-500 truncate"><?= esc($address) ?></span>
    </div>

    <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
      <span>⭐ <strong>0</strong></span>
      <span class="inline-flex items-center gap-1 rounded-full border border-gray-200 px-2.5 py-0.5 text-gray-600">
        ⏰ <?= esc($timeLabel) ?>
      </span>
      <span class="text-[var(--primary)] font-semibold">
        ฿<?= number_format($price, 0) ?>/hr.
      </span>
    </div>
  </div>

  <!-- ปุ่มลูกศร: ชิดขวากลางการ์ด -->
  <button
    class="absolute right-4 sm:right-5 top-1/2 -translate-y-1/2 z-[6]
           flex h-9 w-9 items-center justify-center
           rounded-full border border-[var(--primary)]
           bg-white/90 text-[var(--primary)]
           shadow-md shadow-black/10
           hover:bg-[var(--primary)] hover:text-white
           transition-colors"
  >
    &rsaquo;
  </button>

  <div class="pointer-events-none absolute bottom-2 left-1/2 -translate-x-1/2 z-[5]
              inline-flex items-center justify-center gap-1
              text-[var(--primary)] text-[11px] font-semibold
              px-3 py-1 rounded-full
              bg-white/80
              shadow-md shadow-black/15
              backdrop-blur-md
              border border-white/60
              transition-all duration-300">
    <span class="text-sm leading-none drop-shadow-sm"><?= esc($typeIcon) ?></span>
    <span class="leading-none"><?= esc($typeLabel) ?></span>
  </div>
</li>

        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>
</section>
<section id="venueSeeAll" class="bg-gray-50 py-6">
  <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
    <div class="flex justify-end">
      <a href="<?= site_url('sport/view') ?>"
         class="px-6 py-3 text-sm font-semibold text-[var(--primary)] hover:underline">
        ดูทั้งหมด
      </a>
    </div>
  </div>
</section>


</main>

<?= $this->endSection() ?>
