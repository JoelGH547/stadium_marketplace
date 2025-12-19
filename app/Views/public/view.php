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
  <section class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3">
      <form method="get" action="<?= site_url('sport/view') ?>">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
          <!-- Search -->
          <div class="relative flex-1 group">
            <span class="pointer-events-none absolute inset-y-0 left-3 inline-flex items-center text-gray-400 group-focus-within:text-[var(--primary)] transition-colors">
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
              class="w-full rounded-full border border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-sm
                    text-gray-900
                    focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent focus:bg-white
                    placeholder:text-gray-400 transition-all shadow-sm"
              placeholder="ค้นหาชื่อสนาม, ทำเล หรือประเภทกีฬา..."
            >
          </div>

          <!-- Filter menu button -->
          <div class="flex items-center gap-2">
            <button
              id="filterToggle"
              type="button"
              class="inline-flex items-center gap-2 rounded-full border border-gray-200
                    bg-white px-5 py-2.5 text-sm font-medium text-gray-700
                    shadow-sm hover:bg-gray-50 hover:text-[var(--primary)] hover:border-[var(--primary)] transition-all active:scale-95"
            >
              <!-- Heroicons: Adjustments -->
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
              </svg>
              <span>ตัวกรอง</span>
            </button>

            <!-- Divider -->
            <div class="h-8 w-px bg-gray-200 mx-1 hidden sm:block"></div>

            <button type="submit"
              class="inline-flex items-center rounded-full bg-[var(--primary)] px-6 py-2.5 text-sm font-semibold text-white
                    shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all"
            >ค้นหา</button>
            
            <a href="<?= site_url('sport/view') ?>"
              class="inline-flex items-center justify-center rounded-full border border-transparent w-10 h-10 text-gray-400
                      hover:text-gray-600 hover:bg-gray-100 transition-all"
              title="ล้างการค้นหา"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </a>
          </div>
        </div>
        
        <!-- Filter dropdown -->
        <div id="filterPanel" class="hidden mt-4 animate-in fade-in slide-in-from-top-2 duration-200">
          <div class="rounded-2xl border border-gray-200 bg-white shadow-xl overflow-hidden ring-1 ring-black/5">
            
            <!-- Top Section: Booking Date/Time (Light Brand Background) -->
            <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-100">
              <div class="flex items-center gap-2 mb-4">
                  <div class="p-1.5 rounded-lg bg-[var(--primary)]/10 text-[var(--primary)]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <h3 class="text-base font-bold text-gray-900">ค้นหาตามวัน-เวลา</h3>
                  <span class="text-xs text-gray-400 font-normal ml-auto hidden sm:block">เลือกวันเวลาที่ต้องการเพื่อดูสนามที่ว่าง</span>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Booking Mode -->
                <div class="md:col-span-3">
                  <label class="block text-xs font-semibold text-gray-600 mb-1.5 ml-1">รูปแบบการจอง</label>
                  <div class="relative">
                      <select name="mode" id="viewMode"
                        class="w-full appearance-none rounded-xl border border-gray-200 bg-white pl-4 pr-10 py-2.5 text-sm text-gray-700
                              focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent cursor-pointer hover:border-gray-300 transition-colors">
                        <option value="" <?= $mode === '' ? 'selected' : '' ?>>ทั้งหมด</option>
                        <option value="hourly" <?= $mode === 'hourly' ? 'selected' : '' ?>>รายชั่วโมง (Hourly)</option>
                        <option value="daily"  <?= $mode === 'daily'  ? 'selected' : '' ?>>รายวัน (Daily)</option>
                      </select>
                      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                      </div>
                  </div>
                </div>

                <!-- Hourly Controls -->
                <div id="viewHourlyBox" class="md:col-span-9 grid grid-cols-1 sm:grid-cols-3 gap-4 w-full">
                  <div class="relative">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 ml-1">วันที่</label>
                    <input type="date" id="viewDate" name="date" min="<?= date('Y-m-d') ?>" value="<?= esc($dateVal) ?>"
                      class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[var(--primary)] hover:border-gray-300 transition-colors" />
                  </div>
                  <div class="relative">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 ml-1">เวลาเริ่ม</label>
                    <div class="relative">
                      <select id="viewStartTime" name="start_time" data-selected="<?= esc($startTimeVal) ?>"
                          class="w-full appearance-none rounded-xl border border-gray-200 bg-white pl-4 pr-10 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[var(--primary)] hover:border-gray-300 transition-colors cursor-pointer">
                          <option value="">— เริ่มต้น —</option>
                      </select>
                      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                      </div>
                    </div>
                  </div>
                  <div class="relative">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 ml-1">เวลาสิ้นสุด</label>
                    <div class="relative">
                      <select id="viewEndTime" name="end_time" data-selected="<?= esc($endTimeVal) ?>"
                          class="w-full appearance-none rounded-xl border border-gray-200 bg-white pl-4 pr-10 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[var(--primary)] hover:border-gray-300 transition-colors disabled:bg-gray-50 disabled:text-gray-400 cursor-pointer" disabled>
                          <option value="">— สิ้นสุด —</option>
                      </select>
                      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Daily Controls -->
                <div id="viewDailyBox" class="md:col-span-9 grid grid-cols-1 sm:grid-cols-2 gap-4 hidden w-full">
                  <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 ml-1">เริ่มวันที่</label>
                    <input type="date" id="viewStartDate" name="start_date" min="<?= date('Y-m-d') ?>" value="<?= esc($startDateVal) ?>"
                      class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[var(--primary)] hover:border-gray-300 transition-colors" />
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 ml-1">ถึงวันที่</label>
                    <input type="date" id="viewEndDate" name="end_date" min="<?= date('Y-m-d') ?>" value="<?= esc($endDateVal) ?>"
                      class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[var(--primary)] hover:border-gray-300 transition-colors" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Bottom Section: Attributes Filters -->
            <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-8 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
              
              <!-- Left Column: Primary Filters (Sport & Sort) -->
              <div class="lg:col-span-7 space-y-8 pr-0 lg:pr-8">
                
                <!-- Sport Category -->
                <div>
                  <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                      <span class="w-1 h-4 bg-[var(--primary)] rounded-full"></span>
                      ประเภทกีฬา
                  </h3>
                  <div class="flex flex-wrap gap-2.5" id="sport-filter-group">
                    <button type="button" 
                      class="filter-chip px-4 py-2 rounded-full border border-gray-200 bg-white text-gray-600 text-sm font-medium hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 [&.active]:bg-gray-900 [&.active]:text-white [&.active]:border-gray-900 [&.active]:shadow-md"
                      data-filter="sport" data-value="all">
                      ทั้งหมด
                    </button>
                    <?php foreach ($categories as $cat): ?>
                      <button type="button" 
                        class="filter-chip px-4 py-2 rounded-full border border-gray-200 bg-white text-gray-600 text-sm font-medium hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 [&.active]:bg-[var(--primary)] [&.active]:text-white [&.active]:border-[var(--primary)] [&.active]:shadow-md"
                        data-filter="sport" data-value="<?= $cat['id'] ?>">
                        <?= esc($cat['emoji'] . ' ' . $cat['name']) ?>
                      </button>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- Sort -->
                <div>
                  <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                      <span class="w-1 h-4 bg-orange-400 rounded-full"></span>
                      เรียงลำดับ
                  </h3>
                  <div class="flex flex-wrap gap-2.5" id="sort-group">
                    <button type="button" class="sort-chip px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-600 text-sm font-medium hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 [&.active]:bg-orange-50 [&.active]:text-orange-700 [&.active]:border-orange-200 [&.active]:ring-1 [&.active]:ring-orange-200" data-sort="popular">🔥 ยอดนิยม</button>
                    <button type="button" class="sort-chip px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-600 text-sm font-medium hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 [&.active]:bg-orange-50 [&.active]:text-orange-700 [&.active]:border-orange-200 [&.active]:ring-1 [&.active]:ring-orange-200" data-sort="rating">⭐ คะแนนรีวิว</button>
                    <button type="button" class="sort-chip px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-600 text-sm font-medium hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 [&.active]:bg-orange-50 [&.active]:text-orange-700 [&.active]:border-orange-200 [&.active]:ring-1 [&.active]:ring-orange-200" data-sort="price">💰 ราคาถูกสุด</button>
                    <button type="button" class="sort-chip px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-600 text-sm font-medium hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 [&.active]:bg-orange-50 [&.active]:text-orange-700 [&.active]:border-orange-200 [&.active]:ring-1 [&.active]:ring-orange-200" data-sort="nearby">📍 ใกล้ตัวฉัน</button>
                  </div>
                </div>
              </div>

              <!-- Right Column: Secondary Filters -->
              <div class="lg:col-span-5 space-y-8 pl-0 lg:pl-8 pt-8 lg:pt-0">
                
                <!-- Rating & Reviews -->
                <div class="space-y-6">
                  <!-- Stars -->
                  <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                      <span class="w-1 h-4 bg-yellow-400 rounded-full"></span>
                      ระดับดาว
                    </h3>
                    <div class="flex flex-wrap gap-2" id="star-filter-group">
                      <?php for ($i = 4; $i >= 1; $i--): ?>
                        <label class="cursor-pointer group">
                          <input type="radio" name="star_rating" class="filter-rb peer hidden" data-filter="star" value="<?= $i ?>">
                          <div class="px-3 py-1.5 rounded-md border border-gray-200 bg-white text-gray-500 text-sm font-medium group-hover:bg-gray-50 peer-checked:bg-yellow-50 peer-checked:text-yellow-700 peer-checked:border-yellow-200 peer-checked:ring-1 peer-checked:ring-yellow-200 transition-all flex items-center gap-1">
                            <span class="text-yellow-400">★</span>
                            <span><?= $i ?></span>
                          </div>
                        </label>
                      <?php endfor; ?>
                    </div>
                  </div>
                  
                  <!-- Reviews -->
                  <div id="review-filter-group">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2 mt-4 opacity-75">จำนวนรีวิว (ขั้นต่ำ)</h3>
                    <div class="flex flex-wrap gap-2">
                      <?php foreach([50, 20, 10, 1] as $rCount): ?>
                      <label class="cursor-pointer group">
                        <input type="radio" name="review_count" class="filter-rb peer hidden" data-filter="review" value="<?= $rCount ?>">
                        <div class="px-3 py-1.5 rounded-md border border-gray-200 bg-white text-gray-500 text-xs font-medium group-hover:bg-gray-50 peer-checked:bg-gray-100 peer-checked:text-gray-900 peer-checked:border-gray-400 transition-all">
                          <?= $rCount ?>+ รีวิว
                        </div>
                      </label>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>

                <!-- Facilities -->
                <div id="facility-filter-group">
                  <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                      <span class="w-1 h-4 bg-blue-400 rounded-full"></span>
                      สิ่งอำนวยความสะดวก
                  </h3>
                  <div class="flex flex-wrap gap-2">
                    <?php
                      /** @var array $facilityTypes */
                      $facilityTypes = $facilityTypes ?? [];
                    ?>
                    <?php foreach ($facilityTypes as $fac): ?>
                      <label class="cursor-pointer group relative">
                        <input type="checkbox" class="filter-cb peer hidden" data-filter="facility" value="<?= $fac['id'] ?>">
                        <div class="px-3 py-1.5 rounded-full border border-gray-200 bg-white text-gray-600 text-xs font-medium 
                                    peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:border-blue-200 
                                    hover:bg-gray-50 transition-all select-none">
                          <?= esc($fac['name']) ?>
                        </div>
                      </label>
                    <?php endforeach; ?>
                  </div>
                </div>

              </div>
            </div>
            
            <!-- Footer Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end gap-3 text-sm">
              <button type="button" onclick="document.getElementById('filterToggle').click()" class="text-gray-500 hover:text-gray-700 hover:underline px-2">ปิดหน้าต่าง</button>
            </div>
          </div>
        </div>
      </form>
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
              $facilityIds = implode(',', $v['facility_ids'] ?? []);

              // Favorite button data
              $sid = (int) ($v['id'] ?? 0);
              $isFav = !empty($favoriteMap[$sid]);
            ?>
            <li class="venue-item relative bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden"
                data-index="<?= $idx ?>"
                data-name="<?= esc($name) ?>"
                data-category-id="<?= esc($v['category_id'] ?? '') ?>"
                data-price="<?= esc($v['min_price'] ?? 0) ?>"
                data-lat="<?= esc($lat) ?>"
                data-lng="<?= esc($lng) ?>"
                data-rating="<?= $avgRating ?>"
                data-review-count="<?= $reviewCount ?>"
                data-facility-ids="<?= esc($facilityIds) ?>">

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
