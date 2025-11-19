<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<main class="bg-gray-50 min-h-screen">
  <!-- Header / Title -->
  <section class="bg-[var(--primary)] text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="text-sm uppercase tracking-wide text-white/70">Stadium Marketplace</p>
          <h1 class="mt-1 text-3xl sm:text-4xl font-extrabold leading-tight drop-shadow-sm">
            สนามทั้งหมด
          </h1>
          <p class="mt-1 text-sm sm:text-base text-white/85">
            เลือกสนามที่เหมาะกับคุณจากรายการทั้งหมด ค้นหาและกรองได้ตามประเภทกีฬา ทำเล และช่วงราคา
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Search + Filter bar -->
  <section class="bg-white border-b border-gray-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <!-- Search -->
        <div class="relative flex-1">
          <span class="pointer-events-none absolute inset-y-0 left-3 inline-flex items-center text-gray-400">
            <!-- Heroicons: Magnifying Glass -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z" />
            </svg>
          </span>
          <input
            id="venueSearch"
            type="text"
            class="w-full rounded-full border border-[var(--line)] bg-gray-50/60 pl-10 pr-4 py-2.5 text-sm
                   text-[var(--text)]
                   focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-[var(--primary)]
                   placeholder:text-gray-400"
            placeholder="ค้นหาชื่อสนาม พื้นที่ หรือประเภทกีฬา..."
          >
        </div>

        <!-- Filter menu button -->
        <div class="flex items-center gap-2">
          <button
            id="filterToggle"
            type="button"
            class="inline-flex items-center gap-2 rounded-full border border-[var(--primary)]
                   bg-white px-4 py-2.5 text-sm font-medium text-[var(--primary)]
                   shadow-sm hover:bg-[var(--primary)] hover:text-white transition"
          >
            <!-- Heroicons: Adjustments Horizontal -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 7h12m4 0h2M6 7v10m9-4h6M3 17h2m4 0h8m-4-4V5" />
            </svg>
            <span>ฟิลเตอร์</span>
          </button>
        </div>
      </div>

      <!-- Filter dropdown -->
      <div
        id="filterPanel"
        class="mt-3 hidden rounded-2xl border border-gray-200 bg-gray-50/80 p-4 text-sm"
      >
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- ประเภทกีฬา ดึงจาก categories -->
          <div>
            <p class="font-semibold text-[var(--primary)] mb-2">ประเภทกีฬา</p>
            <?php
              /** @var array $categories */
              $categories = $categories ?? [];
            ?>
            <div class="flex flex-wrap gap-2">
              <!-- ปุ่มทั้งหมด -->
              <button
                type="button"
                class="filter-chip inline-flex items-center gap-1 rounded-full border border-gray-300
                       bg-white px-3 py-1.5 text-xs font-medium text-gray-700
                       hover:border-[var(--primary)] hover:text-[var(--primary)] hover:bg-[var(--primary)]/5
                       transition"
                data-filter-type="sport"
                data-filter-value="all"
              >
                <span>⭐</span>
                <span>ทั้งหมด</span>
              </button>

              <!-- loop หมวดหมู่จาก DB -->
              <?php foreach ($categories as $cat): ?>
                <?php
                  $label = $cat['name']  ?? 'ประเภทกีฬา';
                  $emoji = $cat['emoji'] ?? '🏟️';
                  $id    = $cat['id']    ?? '';
                ?>
                <button
                  type="button"
                  class="filter-chip inline-flex items-center gap-1 rounded-full border border-gray-300
                         bg-white px-3 py-1.5 text-xs font-medium text-gray-700
                         hover:border-[var(--primary)] hover:text-[var(--primary)] hover:bg-[var(--primary)]/5
                         transition"
                  data-filter-type="sport"
                  data-filter-value="<?= esc($id) ?>"
                >
                  <span><?= esc($emoji) ?></span>
                  <span><?= esc($label) ?></span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- ทำเล (คำนวณ tag แบบง่ายจาก address/province) -->
          <div>
            <p class="font-semibold text-[var(--primary)] mb-2">ทำเล</p>
            <div class="space-y-2">
              <label class="flex items-center gap-2 text-xs sm:text-sm text-gray-700">
                <input type="checkbox" class="area-filter rounded border-gray-300 text-[var(--primary)]"
                       data-filter-type="area" value="near-city">
                <span>ใกล้ตัวเมือง</span>
              </label>
              <label class="flex items-center gap-2 text-xs sm:text-sm text-gray-700">
                <input type="checkbox" class="area-filter rounded border-gray-300 text-[var(--primary)]"
                       data-filter-type="area" value="suburb">
                <span>ชานเมือง</span>
              </label>
              <label class="flex items-center gap-2 text-xs sm:text-sm text-gray-700">
                <input type="checkbox" class="area-filter rounded border-gray-300 text-[var(--primary)]"
                       data-filter-type="area" value="near-school">
                <span>ใกล้โรงเรียน / มหาวิทยาลัย</span>
              </label>
            </div>
          </div>

          <!-- เรียงลำดับ -->
          <div>
            <p class="font-semibold text-[var(--primary)] mb-2">เรียงลำดับ</p>
            <div class="grid grid-cols-1 gap-2">
              <button
                type="button"
                class="sort-chip inline-flex items-center justify-between rounded-full border border-gray-300
                       bg-white px-3 py-1.5 text-xs sm:text-sm text-gray-700
                       hover:border-[var(--primary)] hover:text-[var(--primary)] hover:bg-[var(--primary)]/5
                       transition"
                data-sort="popular"
              >
                <span>ยอดนิยม</span>
                <span class="text-[10px] uppercase tracking-wide text-gray-400">default</span>
              </button>
              <button
                type="button"
                class="sort-chip inline-flex items-center justify-between rounded-full border border-gray-300
                       bg-white px-3 py-1.5 text-xs sm:text-sm text-gray-700
                       hover:border-[var(--primary)] hover:text-[var(--primary)] hover:bg-[var(--primary)]/5
                       transition"
                data-sort="price"
              >
                <span>ราคาถูกสุด</span>
                <span class="text-[10px] uppercase tracking-wide text-gray-400">฿ → ฿฿฿</span>
              </button>
              <button
                type="button"
                class="sort-chip inline-flex items-center justify-between rounded-full border border-gray-300
                       bg-white px-3 py-1.5 text-xs sm:text-sm text-gray-700
                       hover:border-[var(--primary)] hover:text-[var(--primary)] hover:bg-[var(--primary)]/5
                       transition"
                data-sort="nearby"
              >
                <span>ใกล้ตัวฉัน</span>
                <span class="text-[10px] uppercase tracking-wide text-gray-400">📍</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- List -->
  <section class="py-6 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <?php
        /** @var array $venueCards */
        $venueCards = $venueCards ?? [];
      ?>

      <?php if (empty($venueCards)): ?>
        <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-4 py-10 text-center text-sm text-gray-500">
          ยังไม่มีข้อมูลสนามให้แสดง ลองเพิ่มข้อมูลจากหลังบ้านก่อนนะ
        </div>
      <?php else: ?>
        <!-- 1 การ์ดต่อแถว -->
        <ul id="allVenueList" class="grid grid-cols-1 gap-4">
          <?php foreach ($venueCards as $idx => $v): ?>
            <?php
              $name    = $v['name'] ?? 'ชื่อสนาม';
              $price   = isset($v['price']) ? (float) $v['price'] : 0;

              $addressFull = trim(($v['address'] ?? '') . ' ' . ($v['province'] ?? ''));
              $address     = $addressFull !== '' ? $addressFull : 'ยังไม่ระบุที่อยู่';

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

              $categoryId = $v['category_id'] ?? '';
              $lat        = $v['lat'] ?? null;
              $lng        = $v['lng'] ?? null;

              // tag ทำเลแบบง่ายจาก address / province
              $areaTag = 'suburb';
              $addrLower = mb_strtolower($addressFull, 'UTF-8');
              if (mb_stripos($addrLower, 'มหาวิทยาลัย') !== false || mb_stripos($addrLower, 'โรงเรียน') !== false) {
                  $areaTag = 'near-school';
              } elseif (mb_stripos($addrLower, 'อำเภอเมือง') !== false || mb_stripos($addrLower, 'เทศบาล') !== false || mb_stripos($addrLower, 'เขต') !== false) {
                  $areaTag = 'near-city';
              }
            ?>
            <li
              class="venue-item relative flex items-center gap-4 bg-white rounded-2xl
                     border border-gray-200 shadow-sm hover:shadow-md
                     transition-shadow duration-200 overflow-hidden"
              data-index="<?= $idx ?>"
              data-name="<?= esc($name) ?>"
              data-address="<?= esc($addressFull) ?>"
              data-category-id="<?= esc($categoryId) ?>"
              data-price="<?= esc($price) ?>"
              data-lat="<?= esc($lat) ?>"
              data-lng="<?= esc($lng) ?>"
              data-area="<?= esc($areaTag) ?>"
            >
              <img
                src="<?= esc($coverUrl) ?>"
                alt="<?= esc($name) ?>"
                class="h-24 w-24 rounded-2xl object-cover flex-shrink-0"
              >

              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <h2 class="text-base font-extrabold text-[color:var(--ink)] truncate">
                    <?= esc($name) ?>
                  </h2>
                  <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px]
                               bg-white text-[var(--primary)] border border-[var(--primary)]
                               shadow-sm">
                    <span><?= esc($typeIcon) ?></span>
                    <span class="truncate max-w-[120px]"><?= esc($typeLabel) ?></span>
                  </span>
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

              <!-- ปุ่มดูรายละเอียด -->
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-[10]
                       h-9 w-9 rounded-full flex items-center justify-center
                       bg-white text-[var(--primary)]
                       border border-[var(--primary)]
                       shadow-md shadow-black/10
                       hover:bg-[var(--primary)] hover:text-white
                       transition-colors"
              >
                &rsaquo;
              </button>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </section>
</main>

<?= $this->endSection() ?>
