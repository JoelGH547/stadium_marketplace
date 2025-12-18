<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<!-- HERO -->
<div class="relative overflow-hidden rounded-b-3xl" id="home">
    <img src="<?= base_url('assets/uploads/home/batminton.webp') ?>" alt="Sports Arena"
        class="block h-[48vh] w-full object-cover select-none rounded-b-6xl" fetchpriority="high">
</div>

<main id="top">
    <!-- Inline Search (Agoda-like) -->
    <section aria-label="ค้นหาสนามกีฬา" class="bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- ตรงนี้คือจุดที่ "ดันเข้าไปในรูป" -->
            <div class="relative -mt-16 sm:-mt-20 md:-mt-24 lg:-mt-28 z-[10]">

                <!-- Main Card Container -->
                <div class="relative bg-white rounded-3xl shadow-2xl border border-gray-100/80 pt-12 pb-12 px-6">
                    <div class="absolute -top-14 left-1/2 -translate-x-1/2">
                        <p class="text-xl text-white font-bold whitespace-nowrap" style="
                        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);">เพียงไม่กี่คลิก ก็พร้อมลงสนาม</p>
                    </div>
                    <!-- Floating Tabs (Top Center) -->
                    <div
                        class="absolute -top-6 left-1/2 -translate-x-1/2 flex shadow-lg rounded-xl overflow-hidden bg-white border border-gray-100">
                        <button type="button" id="tabHourly"
                            class="min-w-[140px] py-3 px-6 text-sm font-bold text-[var(--primary)] bg-white hover:bg-gray-50 transition-colors border-r border-gray-100">
                            จองรายชั่วโมง
                        </button>
                        <button type="button" id="tabDaily"
                            class="min-w-[140px] py-3 px-6 text-sm font-medium text-gray-500 bg-gray-50 hover:text-[var(--primary)] hover:bg-white transition-colors">
                            จองรายวัน
                        </button>
                    </div>

                    <!-- Search Forms Container -->
                    <div class="space-y-4">

                        <!-- Form: Hourly Booking -->
                        <form action="<?= site_url('sport/search') ?>" method="get" id="formHourly" class="block">
                            <input type="hidden" name="mode" value="hourly">

                            <div class="space-y-4">
                                <!-- Row 1: Stadium Name (Full Width) -->
                                <div class="flex flex-col gap-2">
                                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        ช่องกรอกชื่อสนาม
                                    </label>
                                    <input type="text" name="q" placeholder="ระบุชื่อสนามที่ต้องการค้นหา..."
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm sm:text-base focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]">
                                </div>

                                <!-- Row 2: Type | Date | Start | End -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-500">ประเภทกีฬา</label>
                                        <select name="category"
                                            class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm sm:text-base bg-white focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]">
                                            <option value="">ทั้งหมด</option>
                                            <option value="football">ฟุตบอล / ฟุตซอล</option>
                                            <option value="badminton">แบดมินตัน</option>
                                            <option value="tennis">เทนนิส</option>
                                            <option value="basketball">บาสเกตบอล</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-500">วันที่</label>
                                        <input type="date" name="date"
                                            class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm sm:text-base focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]">
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-500">เวลาเริ่มต้น</label>
                                        <input type="time" name="start_time"
                                            class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm sm:text-base focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]">
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-500">เวลาสิ้นสุด</label>
                                        <input type="time" name="end_time"
                                            class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm sm:text-base focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]">
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Form: Daily Booking -->
                        <form action="<?= site_url('sport/search') ?>" method="get" id="formDaily" class="hidden">
                            <input type="hidden" name="mode" value="daily">

                            <div class="space-y-4">
                                <!-- Row 1: Stadium Name (Full Width) -->
                                <div class="flex flex-col gap-2">
                                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        ช่องกรอกชื่อสนาม
                                    </label>
                                    <input type="text" name="q" placeholder="ระบุชื่อสนามที่ต้องการค้นหา..."
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm sm:text-base focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]">
                                </div>

                                <!-- Row 2: Type | Start Date | End Date -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-500">ประเภทกีฬา</label>
                                        <select name="category"
                                            class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm sm:text-base bg-white focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]">
                                            <option value="">ทั้งหมด</option>
                                            <option value="football">ฟุตบอล / ฟุตซอล</option>
                                            <option value="badminton">แบดมินตัน</option>
                                            <option value="tennis">เทนนิส</option>
                                            <option value="basketball">บาสเกตบอล</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-500">วันที่เริ่มต้น</label>
                                        <input type="date" name="start_date"
                                            class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm sm:text-base focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]">
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <label
                                            class="text-xs font-semibold uppercase tracking-wide text-gray-500">วันที่สิ้นสุด</label>
                                        <input type="date" name="end_date"
                                            class="w-full rounded-xl border border-gray-200 px-3 py-3 text-sm sm:text-base focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]">
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>

                    <!-- Floating Search Button (Bottom Center) -->
                    <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 w-full max-w-xs px-4">
                        <button type="button" id="mainSearchBtn"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-base font-bold text-white shadow-lg shadow-[var(--primary)]/30 hover:bg-emerald-600 hover:shadow-[var(--primary)]/40 hover:-translate-y-0.5 transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35" />
                                <circle cx="11" cy="11" r="6" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>ค้นหา</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Popular Stadiums Section -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">สนามยอดนิยมในประเทศไทย</h2>

            <div class="relative group">
                <!-- Left Button -->
                <button id="popularLeft"
                    class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-800 p-2 rounded-full shadow-lg border border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity disabled:opacity-0 hidden md:block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Scroll Container -->
                <div id="popularScroller"
                    class="flex gap-6 overflow-x-auto pb-4 -mx-4 px-4 scroll-smooth no-scrollbar snap-x snap-mandatory">
                    <?php
                    // Mock Data for 12 Popular Stadiums
                    $popularStadiums = [
                        ['name' => 'Thunder Dome', 'location' => 'กรุงเทพมหานคร', 'img' => '1.jpg'],
                        ['name' => 'Chiang Mai 700th', 'location' => 'เชียงใหม่', 'img' => '2.jpg'],
                        ['name' => 'Pattaya Stadium', 'location' => 'ชลบุรี', 'img' => '3.jpg'],
                        ['name' => 'Phuket City', 'location' => 'ภูเก็ต', 'img' => '4.jpg'],
                        ['name' => 'Korat Arena', 'location' => 'นครราชสีมา', 'img' => '5.jpg'],
                        ['name' => 'Khon Kaen Sport', 'location' => 'ขอนแก่น', 'img' => '6.jpg'],
                        ['name' => 'Songkhla Complex', 'location' => 'สงขลา', 'img' => '1.jpg'],
                        ['name' => 'Ayutthaya Park', 'location' => 'พระนครศรีอยุธยา', 'img' => '2.jpg'],
                        ['name' => 'Buriram Castle', 'location' => 'บุรีรัมย์', 'img' => '3.jpg'],
                        ['name' => 'Hua Hin Sport', 'location' => 'ประจวบคีรีขันธ์', 'img' => '4.jpg'],
                        ['name' => 'Rayong Stadium', 'location' => 'ระยอง', 'img' => '5.jpg'],
                        ['name' => 'Udon Thani Field', 'location' => 'อุดรธานี', 'img' => '6.jpg'],
                    ];

                    foreach ($popularStadiums as $stadium):
                    ?>
                    <div class="flex-none w-[200px] snap-start cursor-pointer group/card">
                        <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-gray-200 mb-3">
                            <img src="<?= base_url('assets/uploads/home/' . $stadium['img']) ?>"
                                alt="<?= esc($stadium['name']) ?>"
                                class="h-full w-full object-cover group-hover/card:scale-110 transition-transform duration-500">
                        </div>
                        <h3 class="font-bold text-gray-900 text-lg truncate"><?= esc($stadium['name']) ?></h3>
                        <p class="text-gray-500 text-sm"><?= esc($stadium['location']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Right Button -->
                <button id="popularRight"
                    class="absolute -right-4 top-1/2 -translate-y-1/2 z-10 bg-white/90 hover:bg-white text-gray-800 p-2 rounded-full shadow-lg border border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity hidden md:block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </section>



    <!-- Featured (Horizontal Cards with Arrow Buttons) -->
    <section id="results" class="relative isolate z-[10] bg-[var(--primary)] py-16 rounded-3xl overflow-hidden">
        <!-- พื้นหลังลายจุด + แสงฟุ้ง + คลื่นด้านล่าง (ของเดิม) -->
        <div class="pointer-events-none absolute inset-0 -z-0" style="
        background-image:
          radial-gradient(rgba(255,255,255,0.25) 1px, transparent 1px);
        background-size: 20px 20px;
        background-position: 0 8px;
       "></div>

        <div class="pointer-events-none absolute inset-0 -z-0 overflow-hidden">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[900px] rounded-full opacity-25 blur-3xl"
                style="background: radial-gradient(ellipse at center, rgba(255,255,255,.45), rgba(14,165,164,0) 60%);">
            </div>

        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-[5]">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-white">สนามใกล้คุณ</h2>
                <a href="#venueList" class="text-sm text-white/90 hover:text-white transition">ดูทั้งหมด</a>
            </div>

            <?php
            /** @var array $venueCards */
            $venueCards = $venueCards ?? [];
            $favoriteMap = $favoriteMap ?? [];
            $nearby = $venueCards;
            ?>

            <div class="relative z-[10]">
                <!-- ปุ่มเลื่อน -->
                <button id="nearLeft" class="scroller-btn scroller-left text-white hover:text-white/90"
                    aria-label="เลื่อนไปทางซ้าย">‹</button>
                <button id="nearRight" class="scroller-btn scroller-right text-white hover:text-white/90"
                    aria-label="เลื่อนไปทางขวา">›</button>

                <div id="nearScroller"
                    class="mt-2 -mx-4 px-4 flex gap-4 overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory relative z-[15]">

                    <?php if (!empty($nearby)): ?>
                    <?php foreach ($nearby as $i => $v): ?>
                    <?php
                            $id        = $v['id'] ?? null; // << เพิ่ม
                            $detailUrl = $id
                                ? site_url('sport/fields/' . $id) // ถ้าหน้า detail คือ /sport/stadium/{id}
                                : site_url('sport/fields'); // fallback ไปหน้า show รวมสนาม
                            $name  = $v['name'] ?? 'ชื่อสนาม';

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
                        class="relative snap-start flex-none min-w-[260px] sm:min-w-[280px] max-w-xs cursor-pointer"
                        <?php if (!empty($lat) && !empty($lng)): ?> data-lat="<?= esc($lat) ?>"
                        data-lng="<?= esc($lng) ?>" <?php endif; ?> <?php if (!empty($id) && !empty($detailUrl)): ?>
                        onclick="window.location.href='<?= esc($detailUrl) ?>'" <?php endif; ?>>
                        <div class="near-jelly-wrap">
                            <!-- พื้นหลังเบลอ -->
                            <div class="near-jelly-bg" style="background-image:url('<?= esc($coverUrl) ?>');"></div>

                            <!-- การ์ดด้านหน้า -->
                            <div class="near-jelly-card" style="background-image:url('<?= esc($coverUrl) ?>');">
                                <div class="near-jelly-blur"></div>
                                <div class="near-jelly-footer">
                                    <!-- SVG curve แบบ CodePen -->


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
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 2C8.134 2 5 5.134 5 9c0 4.5 4 9 7 11 3-2 7-6.5 7-11 0-3.866-3.134-7-7-7z" />
                                                    <circle cx="12" cy="9" r="2.5" />
                                                </svg>
                                                <span>-- km.</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Badge ประเภทกีฬา + emoji มุมขวาล่าง -->
                                    <!-- Badge ประเภทกีฬา -->
                                    <div class="near-jelly-sport">
                                        <span class="near-jelly-sport-emoji"><?= esc($typeIcon) ?></span>
                                        <span><?= esc($typeLabel) ?></span>
                                    </div>
                                </div>
                    </article>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Area with Sidebar -->
    <section class="bg-gray-50 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">

                <!-- Sidebar (Filter) -->
                <aside class="w-full lg:w-64 flex-shrink-0 space-y-6">

                    <!-- Filter: Price Range -->
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4">ช่วงราคา</h3>

                        <!-- Tabs -->
                        <div class="flex rounded-lg bg-gray-100 p-1 mb-6">
                            <button type="button" id="filterPriceHourly"
                                class="flex-1 py-1.5 text-sm font-semibold rounded-md bg-white text-gray-900 shadow-sm transition-all">รายชั่วโมง</button>
                            <button type="button" id="filterPriceDaily"
                                class="flex-1 py-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 transition-all">รายวัน</button>
                        </div>
                        <!-- Slider (Visual) -->
                        <div class="relative h-1.5 bg-gray-200 rounded-full mb-6 mx-1">
                            <div class="absolute left-0 right-0 top-0 bottom-0 bg-[var(--primary)] rounded-full"></div>
                            <div
                                class="absolute left-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-white border-2 border-[var(--primary)] rounded-full shadow cursor-pointer hover:scale-110 transition-transform">
                            </div>
                            <div
                                class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-4 bg-white border-2 border-[var(--primary)] rounded-full shadow cursor-pointer hover:scale-110 transition-transform">
                            </div>
                        </div>
                        <!-- Inputs -->
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="text-xs text-gray-500 mb-1 block">ราคาเริ่มต้น</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">฿</span>
                                    <input type="number" id="priceMin"
                                        class="w-full pl-6 pr-2 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]"
                                        value="0">
                                </div>
                            </div>
                            <div class="text-gray-300 pt-5">-</div>
                            <div class="flex-1">
                                <label class="text-xs text-gray-500 mb-1 block">สูงสุด</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">฿</span>
                                    <input type="number" id="priceMax"
                                        class="w-full pl-6 pr-2 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)]"
                                        value="5000">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Filter: Star Rating -->
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4">ระดับดาว</h3>
                        <div class="space-y-3">
                            <?php foreach ([4, 3, 2, 1] as $star): ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox"
                                    class="w-5 h-5 rounded border-gray-300 text-[var(--primary)] focus:ring-[var(--primary)] transition">
                                <span class="text-gray-600 group-hover:text-[var(--primary)] text-sm"><?= $star ?>
                                    ดาว</span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Filter: Review Score -->
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4">ยอดรีวิว</h3>
                        <div class="space-y-3">
                            <?php
                            $reviews = [
                                'ยอดรีวิว 9+',
                                'ยอดรีวิว 8+',
                                'ยอดรีวิว 7+',
                                'ยอดรีวิว 6+'
                            ];
                            foreach ($reviews as $review):
                            ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox"
                                    class="w-5 h-5 rounded border-gray-300 text-[var(--primary)] focus:ring-[var(--primary)] transition">
                                <span
                                    class="text-gray-600 group-hover:text-[var(--primary)] text-sm"><?= $review ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Filter: Sport Type -->
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4">ประเภทกีฬา</h3>
                        <div class="space-y-3">
                            <?php
                            $sports = [
                                'ฟุตบอล / ฟุตซอล',
                                'แบดมินตัน',
                                'เทนนิส',
                                'บาสเกตบอล',
                                'ว่ายน้ำ'
                            ];
                            foreach ($sports as $sport):
                            ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox"
                                    class="w-5 h-5 rounded border-gray-300 text-[var(--primary)] focus:ring-[var(--primary)] transition">
                                <span
                                    class="text-gray-600 group-hover:text-[var(--primary)] text-sm"><?= $sport ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Filter: Facilities -->
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4">สิ่งอำนวยความสะดวก</h3>
                        <div class="space-y-3">
                            <?php
                            $facilities = [
                                'ที่จอดรถ',
                                'ห้องอาบน้ำ',
                                'Wi-Fi',
                                'ร้านขายน้ำ/อาหาร',
                                'อุปกรณ์ให้เช่า'
                            ];
                            foreach ($facilities as $fac):
                            ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox"
                                    class="w-5 h-5 rounded border-gray-300 text-[var(--primary)] focus:ring-[var(--primary)] transition">
                                <span class="text-gray-600 group-hover:text-[var(--primary)] text-sm"><?= $fac ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>
                <!-- Main Content (Sort + List) -->
                <div class="flex-1 min-w-0">

                    <!-- Sort Menu -->
                    <div id="sortMenu" class="mb-6">
                        <div
                            class="bg-white rounded-2xl shadow-sm flex justify-between items-center overflow-hidden border border-gray-200">
                            <button
                                class="sort-btn flex-1 py-3 text-center text-sm font-semibold bg-[var(--primary)] text-white"
                                data-sort="popular" aria-selected="true">ยอดนิยม</button>
                            <div class="w-px h-8 bg-gray-200"></div>
                            <button
                                class="sort-btn flex-1 py-3 text-center text-sm font-semibold text-gray-700 hover:text-[var(--primary)] hover:bg-[var(--primary)]/10"
                                data-sort="price" aria-selected="false">ราคาถูกสุด</button>
                            <div class="w-px h-8 bg-gray-200"></div>
                            <button
                                class="sort-btn flex-1 py-3 text-center text-sm font-semibold text-gray-700 hover:text-[var(--primary)] hover:bg-[var(--primary)]/10"
                                data-sort="nearby" aria-selected="false">ราคาสุดหรู</button>
                            <div class="w-px h-8 bg-gray-200"></div>
                            <button
                                class="sort-btn flex-1 py-3 text-center text-sm font-semibold text-gray-700 hover:text-[var(--primary)] hover:bg-[var(--primary)]/10"
                                data-sort="rating" aria-selected="false">ได้ยอดรีวิวสูง</button>
                        </div>
                    </div>
                    <!-- Venue List -->
                    <div id="venueList">
                        <?php
                        /** @var array $venueCards */
                        $venueCards = $venueCards ?? [];
                        ?>
                        <ul id="venueItems" class="flex flex-col gap-4">
                            <?php if (empty($venueCards)): ?>
                            <div class="text-center py-10 text-gray-500">ไม่พบสนามที่ค้นหา</div>
                            <?php else: ?>
                                                            <?php
                                                            $limitedVenues = array_slice($venueCards, 0, 20);
                                                            foreach ($limitedVenues as $idx => $v):
                                                                $id = $v['id'] ?? null;
                                                                $detailUrl = $id ? site_url('sport/fields/' . $id) : site_url('sport/fields');
                                                                $name = $v['name'] ?? 'ชื่อสนาม';
                                                                $address = trim(($v['address'] ?? '') . ' ' . ($v['province'] ?? ''));
                                                                $address = $address !== '' ? $address : 'ยังไม่ระบุที่อยู่';
                                                                $open = $v['open_time'] ?? null;
                                                                $close = $v['close_time'] ?? null;
                                                                if ($open !== null && strlen($open) >= 5) $open = substr($open, 0, 5);
                                                                if ($close !== null && strlen($close) >= 5) $close = substr($close, 0, 5);
                                                                $timeLabel = ($open && $close) ? ($open . ' – ' . $close) : 'ยังไม่ระบุเวลา';
                                                                $typeIcon = $v['type_icon'] ?? '🏟️';
                                                                $typeLabel = $v['type_label'] ?? ($v['category_name'] ?? 'สนามกีฬา');
                                                                $cover = $v['cover_image'] ?? null;
                                                                $coverUrl = $cover ? base_url('assets/uploads/stadiums/' . $cover) : base_url('assets/uploads/home/1.jpg');
                                                                $lat = $v['lat'] ?? null;
                                                                $lng = $v['lng'] ?? null;
                                                                
                                                                $avgRating   = (float) ($v['avg_rating'] ?? $v['rating'] ?? 0);
                                                                $reviewCount = (int) ($v['review_count'] ?? 0);
                                                            ?>
                                                        <li class="relative bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden"
                                                            data-distance-km="" data-rating="<?= $avgRating ?>" data-popular="<?= 100 - (int) $idx ?>"
                                                            <?php if (!empty($lat) && !empty($lng)): ?> data-lat="<?= esc($lat) ?>"
                                                            data-lng="<?= esc($lng) ?>" <?php endif; ?>>
                            
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
                                                                    <?php $sid = (int) ($v['id'] ?? 0);
                                                                            $isFav = !empty($favoriteMap[$sid]); ?>
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
                                                                    </div>                                        <p class="text-sm text-gray-600 mb-3 line-clamp-1">
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
                                        class="flex flex-col items-end justify-end p-5 md:p-6 md:w-48 bg-gray-50 border-t md:border-t-0 md:border-l border-gray-100">

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
                            <?php endif; ?>
                        </ul>
                    </div>
                    <!-- See All Link -->
                    <div id="venueSeeAll" class="mt-6 flex justify-end">
                        <a href="<?= site_url('sport/view') ?>"
                            class="px-6 py-3 text-sm font-semibold text-[var(--primary)] hover:underline">
                            ดูทั้งหมด
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?= $this->endSection() ?>