<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<!-- HERO -->
<div class="relative" id="home">
  <img src="<?= base_url($heroUrl ?? 'assets/images/1.jpg') ?>" alt="Sports Arena"
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
                'assets/images/1.jpg'
                ,'assets/images/2.jpg'
                ,'assets/images/3.jpg'
                ,'assets/images/4.jpg'
                ,'assets/images/5.jpg'
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
  <!-- 🟢 subtle dot grid -->
<div class="pointer-events-none absolute inset-0 -z-0"
     style="
      background-image:
        radial-gradient(rgba(255,255,255,0.25) 1px, transparent 1px);
      background-size: 20px 20px;
      background-position: 0 8px;
     ">
</div>
  <!-- ✨ BG decorations -->
<div class="pointer-events-none absolute inset-0 -z-0 overflow-hidden">
  <!-- Radial glow (จำเป็นต้องใช้ style สำหรับ radial) -->
  <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[900px] rounded-full opacity-25 blur-3xl"
       style="background: radial-gradient(ellipse at center, rgba(255,255,255,.45), rgba(14,165,164,0) 60%);"></div>

  <!-- Soft bottom wave -->
  <svg class="absolute bottom-0 left-0 w-full h-24 opacity-15"
       viewBox="0 0 1440 320" preserveAspectRatio="none" aria-hidden="true">
    <path fill="#ffffff" d="M0,160 C240,200 480,120 720,160 C960,200 1200,280 1440,240 L1440,320 L0,320 Z"></path>
  </svg>
</div>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-semibold text-white">สนามใกล้คุณ</h2>
      <a href="#search" class="text-sm text-white/90 hover:text-white transition">ดูทั้งหมด</a>
    </div>

    <?php
      /* ==== เพิ่ม/แก้การ์ดได้ที่นี่ ==== */
      $venues = $venues ?? [
      ['name'=>'Greenhill Badminton','img'=>'assets/images/6.jpg','stars'=>5,'distance'=>'0.7km','price'=>140],
      ['name'=>'ATK Badminton','img'=>'assets/images/7.jpg','stars'=>4.8,'distance'=>'3.6km','price'=>140],
      ['name'=>'700th Chiangmai','img'=>'assets/images/8.jpg','stars'=>4.6,'distance'=>'3.2km','price'=>250],
      ['name'=>'Central Court','img'=>'assets/images/9.jpg','stars'=>4.7,'distance'=>'2.1km','price'=>190],
      ['name'=>'CMU Sport Complex','img'=>'assets/images/10.jpg','stars'=>4.9,'distance'=>'1.2km','price'=>200],
      ['name'=>'North Gate Arena','img'=>'assets/images/11.jpg','stars'=>4.5,'distance'=>'5.0km','price'=>180],
      ];
    ?>

    <div class="relative z-[10]">
      <!-- Arrow Buttons -->
      <button id="nearLeft"  class="scroller-btn scroller-left text-white hover:text-white/90"  aria-label="Left">‹</button>
<button id="nearRight" class="scroller-btn scroller-right text-white hover:text-white/90" aria-label="Right">›</button>

      <!-- Scroller -->
      <div id="nearScroller"
           class="mt-2 -mx-4 px-4 flex gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory relative z-[15]">
           <!-- underline glow under cards -->
<div class="pointer-events-none absolute left-4 right-4 -bottom-2 h-8 rounded-full opacity-25 blur-xl"
     style="background: radial-gradient(ellipse at center, rgba(0,0,0,.35), rgba(0,0,0,0) 70%);"></div>

        <?php foreach($venues as $v): ?>
          <article class="relative snap-start flex-none min-w-[260px] sm:min-w-[300px] rounded-xl overflow-hidden bg-white shadow-lg hover:shadow-xl hover:-translate-y-1 transform-gpu transition-all duration-300 ease-out z-20 after:absolute after:inset-x-6 after:-bottom-3 after:h-4 after:bg-black/20 after:blur-lg after:rounded-full after:content-['']">
            <!-- รูปภาพเต็มการ์ด (ใช้ Tailwind ล้วน: aspect-[16/9]) -->
            <div class="relative w-full aspect-[16/9]">
              <img src="<?= base_url($v['img']) ?>"
                    alt="<?= esc($v['name']) ?>"
                    class="absolute inset-0 w-full h-full object-cover select-none" draggable="false">

              <!-- เบลอครึ่งล่าง -->
              <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/60 via-black/40 to-transparent backdrop-blur-md"></div>
            </div>

            <!-- เนื้อหา -->
            <div class="absolute inset-x-0 bottom-0 p-6 text-white">
              <h3 class="font-semibold text-lg drop-shadow line-clamp-1"><?= htmlspecialchars($v['name']) ?></h3>
              <div class="mt-2 flex items-center gap-2">
                <span class="inline-flex items-center gap-1 text-sm">
                  <span>⭐</span><span><?= htmlspecialchars($v['stars']) ?></span>
                </span>
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-md bg-white/25 backdrop-blur">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1 1 12 0c0 4.314-6 10-6 10z"/><circle cx="12" cy="11" r="2.5"/>
                  </svg>
                  <span><?= htmlspecialchars($v['distance']) ?></span>
                </span>
              </div>
              <div class="mt-1 text-base font-semibold drop-shadow">฿<?= number_format((float)$v['price']) ?>/hr.</div>
            </div>

            <div class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-black/5"></div>
          </article>
        <?php endforeach; ?>
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

      <button class="sort-btn flex-1 py-3 text-center text-sm font-medium text-gray-700 hover:text-[var(--primary)] hover:bg-[var(--primary)]/10"
              data-sort="price" aria-selected="false">ราคาถูกสุด</button>

      <div class="w-px h-8 bg-gray-200"></div>

      <button class="sort-btn flex-1 py-3 text-center text-sm font-medium text-gray-700 hover:text-[var(--primary)] hover:bg-[var(--primary)]/10"
              data-sort="nearby" aria-selected="false">ใกล้ตัวเมืองที่สุด</button>

      <div class="w-px h-8 bg-gray-200"></div>

      <button class="sort-btn flex-1 py-3 text-center text-sm font-medium text-gray-700 hover:text-[var(--primary)] hover:bg-[var(--primary)]/10"
              data-sort="rating" aria-selected="false">ได้คะแนนรีวิวสูง</button>

    </div>
  </div>
</section>
<section id="venueList" class=" py-4  bg-gray-50">
  <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
    <ul id="venueItems" class="space-y-3">
      <li class="flex items-center gap-4 bg-white rounded-2xl p-6 transition-all duration-200 hover:shadow-lg"
          data-price="140" data-distance-km="0.7" data-rating="5" data-popular="98">
        <img src="https://picsum.photos/seed/greenhill/300/200" class="h-24 w-24 rounded-2xl object-cover" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">Greenhill Badminton</h3>
            <button class="flex-none h-9 w-9 rounded-full border border-[var(--primary)] text-[var(--primary)] grid place-items-center">&rsaquo;</button>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] px-2.5 py-0.5 dist-badge">📍 <span>0.7km.</span></span>
            <span class="text-gray-500 truncate">Mueang Chiang Mai, Chiang Mai</span>
          </div>
          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span>⭐ <strong>5</strong></span>
            <span class="inline-flex items-center gap-1 rounded-xl border px-2.5 py-0.5 text-gray-600 border-gray-200">⏰ 09:00 – 22:00</span>
            <span class="text-[var(--primary)] font-semibold">฿140/hr.</span>
          </div>
        </div>
      </li>

      <li class="flex items-center gap-4 bg-white rounded-2xl p-6 transition-all duration-200 hover:shadow-lg"
          data-price="140" data-distance-km="3.6" data-rating="4.8" data-popular="90">
        <img src="https://picsum.photos/seed/atk/300/200" class="h-24 w-24 rounded-2xl object-cover" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">ATK Badminton</h3>
            <button class="flex-none h-9 w-9 rounded-full border border-[var(--primary)] text-[var(--primary)] grid place-items-center">&rsaquo;</button>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] px-2.5 py-0.5 dist-badge">📍 <span>3.6km.</span></span>
            <span class="text-gray-500 truncate">Mae Rim, Chiang Mai</span>
          </div>
          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span>⭐ <strong>4.8</strong></span>
            <span class="inline-flex items-center gap-1 rounded-xl border px-2.5 py-0.5 text-gray-600 border-gray-200">⏰ 09:00 – 02:00</span>
            <span class="text-[var(--primary)] font-semibold">฿140/hr.</span>
          </div>
        </div>
      </li>

      <li class="flex items-center gap-4 bg-white rounded-2xl p-6 transition-all duration-200 hover:shadow-lg"
          data-price="150" data-distance-km="3.9" data-rating="0" data-popular="75">
        <img src="https://picsum.photos/seed/cnx/300/200" class="h-24 w-24 rounded-2xl object-cover" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">@CNX Badminton Center</h3>
            <button class="flex-none h-9 w-9 rounded-full border border-[var(--primary)] text-[var(--primary)] grid place-items-center">&rsaquo;</button>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] px-2.5 py-0.5 dist-badge">📍 <span>3.9km.</span></span>
            <span class="text-gray-500 truncate">Mae Rim, Chiang Mai</span>
          </div>
          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span>⭐ <strong>0</strong></span>
            <span class="inline-flex items-center gap-1 rounded-xl border px-2.5 py-0.5 text-gray-600 border-gray-200">⏰ 09:00 – 00:00</span>
            <span class="text-[var(--primary)] font-semibold">฿150/hr.</span>
          </div>
        </div>
      </li>

      <li class="flex items-center gap-4 bg-white rounded-2xl p-6 transition-all duration-200 hover:shadow-lg"
          data-price="140" data-distance-km="10.9" data-rating="0" data-popular="70">
        <img src="https://picsum.photos/seed/spirit/300/200" class="h-24 w-24 rounded-2xl object-cover" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">SpiritArena</h3>
            <button class="flex-none h-9 w-9 rounded-full border border-[var(--primary)] text-[var(--primary)] grid place-items-center">&rsaquo;</button>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] px-2.5 py-0.5 dist-badge">📍 <span>10.9km.</span></span>
            <span class="text-gray-500 truncate">Hang Dong, Chiang Mai</span>
          </div>
          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span>⭐ <strong>0</strong></span>
            <span class="inline-flex items-center gap-1 rounded-xl border px-2.5 py-0.5 text-gray-600 border-gray-200">⏰ 08:00 – 22:00</span>
            <span class="text-[var(--primary)] font-semibold">฿140/hr.</span>
          </div>
        </div>
      </li>

      <li class="flex items-center gap-4 bg-white rounded-2xl p-6 transition-all duration-200 hover:shadow-lg"
          data-price="140" data-distance-km="10.9" data-rating="0" data-popular="70">
        <img src="https://picsum.photos/seed/spirit/300/200" class="h-24 w-24 rounded-2xl object-cover" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">SpiritArena</h3>
            <button class="flex-none h-9 w-9 rounded-full border border-[var(--primary)] text-[var(--primary)] grid place-items-center">&rsaquo;</button>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] px-2.5 py-0.5 dist-badge">📍 <span>10.9km.</span></span>
            <span class="text-gray-500 truncate">Hang Dong, Chiang Mai</span>
          </div>
          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span>⭐ <strong>0</strong></span>
            <span class="inline-flex items-center gap-1 rounded-xl border px-2.5 py-0.5 text-gray-600 border-gray-200">⏰ 08:00 – 22:00</span>
            <span class="text-[var(--primary)] font-semibold">฿140/hr.</span>
          </div>
        </div>
      </li>

      <li class="flex items-center gap-4 bg-white rounded-2xl p-6 transition-all duration-200 hover:shadow-lg"
          data-price="140" data-distance-km="10.9" data-rating="0" data-popular="70">
        <img src="https://picsum.photos/seed/spirit/300/200" class="h-24 w-24 rounded-2xl object-cover" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">SpiritArena</h3>
            <button class="flex-none h-9 w-9 rounded-full border border-[var(--primary)] text-[var(--primary)] grid place-items-center">&rsaquo;</button>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] px-2.5 py-0.5 dist-badge">📍 <span>10.9km.</span></span>
            <span class="text-gray-500 truncate">Hang Dong, Chiang Mai</span>
          </div>
          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span>⭐ <strong>0</strong></span>
            <span class="inline-flex items-center gap-1 rounded-xl border px-2.5 py-0.5 text-gray-600 border-gray-200">⏰ 08:00 – 22:00</span>
            <span class="text-[var(--primary)] font-semibold">฿140/hr.</span>
          </div>
        </div>
      </li>

      <li class="flex items-center gap-4 bg-white rounded-2xl p-6 transition-all duration-200 hover:shadow-lg"
          data-price="140" data-distance-km="10.9" data-rating="0" data-popular="70">
        <img src="https://picsum.photos/seed/spirit/300/200" class="h-24 w-24 rounded-2xl object-cover" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">SpiritArena</h3>
            <button class="flex-none h-9 w-9 rounded-full border border-[var(--primary)] text-[var(--primary)] grid place-items-center">&rsaquo;</button>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] px-2.5 py-0.5 dist-badge">📍 <span>10.9km.</span></span>
            <span class="text-gray-500 truncate">Hang Dong, Chiang Mai</span>
          </div>
          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span>⭐ <strong>0</strong></span>
            <span class="inline-flex items-center gap-1 rounded-xl border px-2.5 py-0.5 text-gray-600 border-gray-200">⏰ 08:00 – 22:00</span>
            <span class="text-[var(--primary)] font-semibold">฿140/hr.</span>
          </div>
        </div>
      </li>

      <li class="flex items-center gap-4 bg-white rounded-2xl p-6 transition-all duration-200 hover:shadow-lg"
          data-price="140" data-distance-km="10.9" data-rating="0" data-popular="70">
        <img src="https://picsum.photos/seed/spirit/300/200" class="h-24 w-24 rounded-2xl object-cover" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">SpiritArena</h3>
            <button class="flex-none h-9 w-9 rounded-full border border-[var(--primary)] text-[var(--primary)] grid place-items-center">&rsaquo;</button>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] px-2.5 py-0.5 dist-badge">📍 <span>10.9km.</span></span>
            <span class="text-gray-500 truncate">Hang Dong, Chiang Mai</span>
          </div>
          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span>⭐ <strong>0</strong></span>
            <span class="inline-flex items-center gap-1 rounded-xl border px-2.5 py-0.5 text-gray-600 border-gray-200">⏰ 08:00 – 22:00</span>
            <span class="text-[var(--primary)] font-semibold">฿140/hr.</span>
          </div>
        </div>
      </li>

      <li class="flex items-center gap-4 bg-white rounded-2xl p-6 transition-all duration-200 hover:shadow-lg"
          data-price="800" data-distance-km="25.6" data-rating="0" data-popular="65">
        <img src="https://picsum.photos/seed/kickgaze/300/200" class="h-24 w-24 rounded-2xl object-cover" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">Kickgaze Stadium Lamphun</h3>
            <button class="flex-none h-9 w-9 rounded-full border border-[var(--primary)] text-[var(--primary)] grid place-items-center">&rsaquo;</button>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] px-2.5 py-0.5 dist-badge">📍 <span>25.6km.</span></span>
            <span class="text-gray-500 truncate">Mueang Lamphun, Lamphun</span>
          </div>
          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span>⭐ <strong>0</strong></span>
            <span class="inline-flex items-center gap-1 rounded-xl border px-2.5 py-0.5 text-gray-600 border-gray-200">⏰ 08:00 – 01:00</span>
            <span class="text-[var(--primary)] font-semibold">฿800/hr.</span>
          </div>
        </div>
      </li>

      <li class="flex items-center gap-4 bg-white rounded-2xl p-6 transition-all duration-200 hover:shadow-lg"
          data-price="180" data-distance-km="496.3" data-rating="0" data-popular="50">
        <img src="https://picsum.photos/seed/good/300/200" class="h-24 w-24 rounded-2xl object-cover" alt="">
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2">
            <h3 class="text-base font-extrabold text-[color:var(--ink)] truncate">Goodminton</h3>
            <button class="flex-none h-9 w-9 rounded-full border border-[var(--primary)] text-[var(--primary)] grid place-items-center">&rsaquo;</button>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
            <span class="inline-flex items-center gap-1 rounded-full bg-[var(--primary)]/10 text-[var(--primary)] px-2.5 py-0.5 dist-badge">📍 <span>496.3km.</span></span>
            <span class="text-gray-500 truncate">Sawang Daen Din, Sakon Nakhon</span>
          </div>
          <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
            <span>⭐ <strong>0</strong></span>
            <span class="inline-flex items-center gap-1 rounded-xl border px-2.5 py-0.5 text-gray-600 border-gray-200">⏰ 09:00 – 23:00</span>
            <span class="text-[var(--primary)] font-semibold">฿180/hr.</span>
          </div>
        </div>
      </li>
    </ul>
  </div>
</section>
<section id="venueSeeAll" class="bg-gray-50 py-6">
  <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
    <div class="flex justify-end">
      <a href="all-venues.php"
         class="px-6 py-3 text-sm font-semibold text-[var(--primary)] hover:underline">
        ดูทั้งหมด
      </a>
    </div>
  </div>
</section>


</main>

<?= $this->endSection() ?>
