<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?php
  $filters = $filters ?? [];
  $mode = (string)($filters['mode'] ?? '');
  $qVal = (string)($filters['q'] ?? '');
  $dateVal = (string)($filters['date'] ?? '');
  $startTimeVal = (string)($filters['start_time'] ?? '');
  $endTimeVal = (string)($filters['end_time'] ?? '');
  $startDateVal = (string)($filters['start_date'] ?? '');
  $endDateVal = (string)($filters['end_date'] ?? '');
?>
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
  
      </form>
</section>

  <!-- Search + Filter bar -->
  <section class="bg-white border-b border-gray-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
      <form method="get" action="<?= site_url('sport/view') ?>" class="flex flex-col gap-3 sm:flex-row sm:items-center">
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
            name="q"
            value="<?= esc($qVal) ?>"
            autocomplete="off"
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
            <!-- Heroicons: Funnel -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.572a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
            </svg>
            <span>ฟิลเตอร์</span>
          </button>

          <button type="submit"
            class="inline-flex items-center rounded-full bg-[var(--primary)] px-5 py-2.5 text-sm font-semibold text-white
                   shadow-sm hover:opacity-95 transition"
          >ค้นหา</button>
          <a href="<?= site_url('sport/view') ?>"
             class="inline-flex items-center rounded-full border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700
                    hover:bg-gray-50 transition"
          >ล้าง</a>
        </div>
      </div>

      <!-- Filter dropdown -->
      <div
        id="filterPanel"
        class="mt-3 hidden rounded-2xl border border-gray-200 bg-gray-50/80 p-4 text-sm"
      >
        <!-- Search Filter: Mode + Date/Time -->
        <div class="mb-4 rounded-2xl border border-gray-200 bg-white p-4">
          <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div class="md:col-span-2">
              <label class="block text-[11px] font-semibold uppercase tracking-wide text-gray-600 mb-1">รูปแบบการจอง</label>
              <select name="mode" id="viewMode"
                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]">
                <option value="" <?= $mode === '' ? 'selected' : '' ?>>ทั้งหมด</option>
                <option value="hourly" <?= $mode === 'hourly' ? 'selected' : '' ?>>รายชั่วโมง</option>
                <option value="daily"  <?= $mode === 'daily'  ? 'selected' : '' ?>>รายวัน</option>
              </select>
            </div>

            <div id="viewHourlyBox" class="md:col-span-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wide text-gray-600 mb-1">วันที่</label>
                <input type="date" id="viewDate" name="date" min="<?= date('Y-m-d') ?>" value="<?= esc($dateVal) ?>"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
              </div>
              <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wide text-gray-600 mb-1">เวลาเริ่มต้น</label>
                <select id="viewStartTime" name="start_time" data-selected="<?= esc($startTimeVal) ?>"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]">
                  <option value="">— เลือกเวลาเริ่มต้น —</option>
                </select>
              </div>
              <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wide text-gray-600 mb-1">เวลาสิ้นสุด</label>
                <select id="viewEndTime" name="end_time" data-selected="<?= esc($endTimeVal) ?>"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" disabled>
                  <option value="">— เลือกเวลาสิ้นสุด —</option>
                </select>
              </div>
            </div>

            <div id="viewDailyBox" class="md:col-span-4 grid grid-cols-1 sm:grid-cols-2 gap-3 hidden">
              <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wide text-gray-600 mb-1">วันที่เริ่มต้น</label>
                <input type="date" id="viewStartDate" name="start_date" min="<?= date('Y-m-d') ?>" value="<?= esc($startDateVal) ?>"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
              </div>
              <div>
                <label class="block text-[11px] font-semibold uppercase tracking-wide text-gray-600 mb-1">วันที่สิ้นสุด</label>
                <input type="date" id="viewEndDate" name="end_date" min="<?= date('Y-m-d') ?>" value="<?= esc($endDateVal) ?>"
                  class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
              </div>
            </div>
          </div>

          <p class="mt-3 text-xs text-gray-500">
            * ถ้าเลือกเวลารายชั่วโมง ระบบจะกรองเฉพาะสนามที่ “คิวว่าง” และ “เวลาเปิด-ปิด” ครอบคลุมช่วงเวลาที่เลือก
          </p>
        </div>

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
              // --- Block copy-pasted from home.php with adjustments ---
              $id = $v['id'] ?? null;
              $detailUrl = $id ? site_url('sport/fields/' . $id) : null;
              $name = $v['name'] ?? 'ชื่อสนาม';
              
              $addressFull = trim(($v['address'] ?? '') . ' ' . ($v['province'] ?? ''));
              $address     = $addressFull !== '' ? $addressFull : 'ยังไม่ระบุที่อยู่';

              $open = $v['open_time'] ?? null;
              $close = $v['close_time'] ?? null;
              if ($open !== null && strlen($open) >= 5) $open = substr($open, 0, 5);
              if ($close !== null && strlen($close) >= 5) $close = substr($close, 0, 5);
              $timeLabel = ($open && $close) ? ($open . ' – ' . $close) : 'ยังไม่ระบุเวลา';

              $typeIcon = $v['type_icon'] ?? '🏟️';
              $typeLabel = $v['type_label'] ?? 'สนามกีฬา';

              $cover = null;
              if (!empty($v['outside_images'])) {
                  $decoded = json_decode($v['outside_images'], true);
                  if (is_array($decoded) && !empty($decoded[0])) {
                      $cover = $decoded[0];
                  }
              }
              $coverUrl = $cover ? base_url('assets/uploads/stadiums/' . $cover) : base_url('assets/uploads/home/1.jpg');
              
              $lat = $v['lat'] ?? null;
              $lng = $v['lng'] ?? null;
              
              $avgRating   = (float) ($v['avg_rating'] ?? 0);
              $reviewCount = (int) ($v['review_count'] ?? 0);

              // Favorite button data
              $sid = (int) ($v['id'] ?? 0);
              $isFav = !empty($favoriteMap[$sid]);

              // tag ทำเลแบบง่ายจาก address / province
              $areaTag = 'suburb';
              $addrLower = mb_strtolower($addressFull, 'UTF-8');
              if (mb_stripos($addrLower, 'มหาวิทยาลัย') !== false || mb_stripos($addrLower, 'โรงเรียน') !== false) {
                  $areaTag = 'near-school';
              } elseif (mb_stripos($addrLower, 'อำเภอเมือง') !== false || mb_stripos($addrLower, 'เทศบาล') !== false || mb_stripos($addrLower, 'เขต') !== false) {
                  $areaTag = 'near-city';
              }
            ?>
            <li class="venue-item relative bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden"
                data-index="<?= $idx ?>"
                data-name="<?= esc($name) ?>"
                data-address="<?= esc($addressFull) ?>"
                data-category-id="<?= esc($v['category_id'] ?? '') ?>"
                data-price-hourly="<?= $v['price'] ?? 0 ?>"
                data-lat="<?= esc($lat) ?>"
                data-lng="<?= esc($lng) ?>"
                data-area="<?= esc($areaTag) ?>"
                data-rating="<?= $avgRating ?>">

                <div class="flex flex-col md:flex-row">
                    <!-- Image Section -->
                    <div class="relative w-full md:w-80 h-56 flex-shrink-0">
                        <?php if (!empty($detailUrl)): ?>
                        <a href="<?= esc($detailUrl) ?>" class="absolute inset-0 z-[5]">
                            <span class="sr-only">ดูรายละเอียดสนาม</span>
                        </a>
                        <?php endif; ?>
                        <img src="<?= esc($coverUrl) ?>" class="w-full h-full object-cover"
                            alt="<?= esc($name) ?>">

                        <!-- Sport Type Badge -->
                        <div
                            class="absolute bottom-3 left-3 z-[6] inline-flex items-center gap-1 text-[var(--primary)] text-xs font-semibold px-3 py-1.5 rounded-full bg-white/90 shadow-md backdrop-blur-sm border border-white/60">
                            <span class="text-sm"><?= esc($typeIcon) ?></span>
                            <span><?= esc($typeLabel) ?></span>
                        </div>

                        <!-- Heart Icon (Favorite) -->
                        <button type="button"
                            class="js-fav-toggle absolute top-3 right-3 z-[6] w-10 h-10 rounded-full flex items-center justify-center shadow-md transition-colors <?= $isFav ? 'bg-rose-50 ring-2 ring-rose-200' : 'bg-white/90 hover:bg-white' ?>"
                            data-stadium-id="<?= $sid ?>" data-favorited="<?= $isFav ? '1' : '0' ?>"
                            title="<?= $isFav ? 'ลบออกจากรายการโปรด' : 'เพิ่มในรายการโปรด' ?>">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 transition <?= $isFav ? 'text-rose-600' : 'text-gray-600' ?>"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                    <!-- Content Section -->
                    <div class="flex-1 p-5 md:p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                            <?= esc($name) ?>
                        </h3>

                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                            <span class="inline-flex items-center gap-1">
                                ⭐ <strong class="text-gray-900"><?= $reviewCount > 0 ? number_format($avgRating, 1) : '0.0' ?></strong>
                                <?php if ($reviewCount > 0): ?>
                                    <span class="text-gray-500">(<?= $reviewCount ?> รีวิว)</span>
                                <?php else: ?>
                                    <span class="text-gray-400">(ยังไม่มีรีวิว)</span>
                                <?php endif; ?>
                            </span>
                            <span class="text-gray-400">•</span>
                            <span class="inline-flex items-center gap-1 dist-badge">
                                📍 <span>-- km.</span>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-1">
                            <?= esc($address) ?>
                        </p>
                        <div class="flex flex-wrap items-center gap-2 text-sm">
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border border-gray-200 text-gray-600">
                                ⏰ <?= esc($timeLabel) ?>
                            </span>
                        </div>
                    </div>
                    <!-- CTA Section -->
                    <div
                        class="flex flex-col items-end justify-between p-5 md:p-6 md:w-60 bg-gray-50/70 border-t md:border-t-0 md:border-l border-gray-100">

                        <!-- Price Range -->
                        <div class="w-full flex flex-col items-end mt-1 mb-4">
                            <?= $v['price_range_html'] ?? '' ?>
                        </div>

                        <?php if (!empty($detailUrl)): ?>
                        <a href="<?= esc($detailUrl) ?>"
                            class="relative z-[6] w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[var(--primary)] text-white font-semibold hover:bg-emerald-600 transition-colors shadow-md whitespace-nowrap">
                            <span>ดูรายละเอียด</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </section>
</main>

<?= $this->endSection() ?>
