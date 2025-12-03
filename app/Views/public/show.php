<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?php
// ข้อมูลหลักของสนาม
$name           = $stadium['name'] ?? 'ชื่อสนาม';
$price          = isset($stadium['price']) ? (float) $stadium['price'] : 0;
$categoryName   = $stadium['category_name']  ?? 'ประเภทกีฬา';
$categoryEmoji  = $stadium['category_emoji'] ?? '🏟️';
$description    = trim($stadium['description'] ?? '');
$lat            = $stadium['lat'] ?? null;
$lng            = $stadium['lng'] ?? null;
$hasMap         = !empty($lat) && !empty($lng);

$rating = isset($stadium['rating']) ? (float) $stadium['rating'] : 5.0;

$district = trim($stadium['district'] ?? '');
$province = trim($stadium['province'] ?? '');
$locationShort = trim($district . ($district && $province ? ', ' : '') . $province)
    ?: 'ยังไม่ระบุพื้นที่';

// ข้อมูลติดต่อ
$contactPhone = trim($stadium['contact_phone'] ?? '');
$contactEmail = trim($stadium['contact_email'] ?? '');

// เวลาเปิด-ปิดของสนาม (ใช้สำหรับ generate slot เวลา)
$openTimeRaw  = isset($stadium['open_time']) ? substr($stadium['open_time'], 0, 5) : '';
$closeTimeRaw = isset($stadium['close_time']) ? substr($stadium['close_time'], 0, 5) : '';

// วันที่วันนี้และวันที่จองล่วงหน้าได้สูงสุด (5 ปี)
$today      = date('Y-m-d');
$maxBooking = date('Y-m-d', strtotime('+5 years'));

// รูปหลัก (cover) สำหรับ fallback และ thumbnail แรก
$coverImage = trim($stadium['cover_image'] ?? '');
$coverUrl   = $coverImage !== ''
    ? base_url('assets/uploads/stadiums/' . $coverImage)
    : base_url('assets/uploads/home/1.jpg');

// เตรียมรูปสำหรับแกลเลอรี: รวม outside + inside images
$galleryImages = [];

if (!empty($stadium['outside_images'])) {
    $decoded = json_decode($stadium['outside_images'], true);
    if (is_array($decoded)) {
        foreach ($decoded as $img) {
            $img = trim((string) $img);
            if ($img !== '') {
                $galleryImages[] = base_url('assets/uploads/stadiums/' . $img);
            }
        }
    }
}

if (!empty($stadium['inside_images'])) {
    $decoded = json_decode($stadium['inside_images'], true);
    if (is_array($decoded)) {
        foreach ($decoded as $img) {
            $img = trim((string) $img);
            if ($img !== '') {
                $galleryImages[] = base_url('assets/uploads/stadiums/' . $img);
            }
        }
    }
}

if (empty($galleryImages)) {
    $galleryImages[] = $coverUrl;
}

// สนามย่อย (สำหรับ dropdown)
$fieldsRaw      = isset($fields) && is_array($fields) ? $fields : [];
$hasAnyField    = !empty($fieldsRaw);
$hasActiveField = false;
foreach ($fieldsRaw as $f) {
    if (($f['status'] ?? 'active') === 'active') {
        $hasActiveField = true;
        break;
    }
}
?>
<main class="bg-gray-50 min-h-screen pb-10">
    <section class="mx-auto max-w-6xl px-4 pt-4 lg:px-0">
        <ol class="flex items-center justify-center gap-2 text-[11px] sm:text-xs">
            <li class="flex items-center gap-1 rounded-full bg-[var(--primary)] px-3 py-1 text-white">
                <span
                    class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-[10px]">1</span>
                <span>เลือกเวลาและบริการ</span>
            </li>
            <li class="flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-gray-500">
                <span
                    class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-gray-200 text-[10px]">2</span>
                <span>ตะกร้าการจอง</span>
            </li>
            <li class="flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-gray-400">
                <span
                    class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-gray-200 text-[10px]">3</span>
                <span>ยืนยันการจอง</span>
            </li>
        </ol>
    </section>

    <section class="relative mx-auto max-w-5xl">
        <article id="stadiumDetail" class="bg-white shadow-sm sm:shadow-md sm:rounded-3xl overflow-hidden"
            data-lat="<?= esc($lat ?? '') ?>" data-lng="<?= esc($lng ?? '') ?>" data-price-hour="<?= esc($price) ?>"
            data-open-time="<?= esc($openTimeRaw) ?>" data-close-time="<?= esc($closeTimeRaw) ?>">

            <!-- เนื้อหาหลัก -->
            <section class="px-4 sm:px-8 pb-8 pt-6">

                <!-- ชื่อ + meta แถวบน -->
                <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold text-[color:var(--ink)]">
                            <?= esc($name) ?>
                        </h1>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-600">
                            <!-- ดาว -->
                            <span class="inline-flex items-center gap-1">
                                ⭐ <span class="font-semibold"><?= number_format($rating, 1) ?></span>
                            </span>

                            <!-- ระยะห่าง (JS จะมาเขียนทับตรงนี้) -->
                            <span class="inline-flex items-center gap-1 rounded-full dist-badge px-2.5 py-0.5">
                                📍 <span>-- km.</span>
                            </span>

                            <!-- ที่ตั้งคร่าว ๆ -->
                            <span class="inline-flex items-center gap-1">
                                📌 <span><?= esc($locationShort) ?></span>
                            </span>

                            <!-- เวลาเปิดปิด / label เวลา -->
                            <span class="inline-flex items-center gap-1">
                                ⏰ <span><?= esc($timeLabel) ?></span>
                            </span>
                        </div>
                    </div>
                </header>

                <!-- เนื้อหา 2 คอลัมน์: เงื่อนไข + ข้อมูลเพิ่มเติม -->
                <section class="mt-8 grid gap-8 md:grid-cols-[minmax(0,2fr)_minmax(0,1.3fr)]">
                    <!-- Booking Conditions / รายละเอียด -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Booking Conditions</h2>
                        <div class="mt-3 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                            <?= $description !== ''
                                ? nl2br(esc($description))
                                : 'ยังไม่ได้ระบุเงื่อนไขการจอง' ?>
                        </div>
                    </div>

                    <!-- ข้อมูลสนามสรุปด้านขวา -->
                    <aside class="space-y-4">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">รายละเอียดสนาม</h3>
                            <dl class="space-y-2 text-sm text-gray-700">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">ประเภทกีฬา</dt>
                                    <dd class="font-medium"><?= esc($categoryEmoji) ?> <?= esc($categoryName) ?></dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">ราคาโดยประมาณ</dt>
                                    <dd class="font-medium">
                                        <?= $price > 0 ? '฿' . number_format($price, 0) . '/ชั่วโมง' : 'ยังไม่ระบุราคา' ?>
                                    </dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">เวลาเปิด–ปิด</dt>
                                    <dd class="font-medium"><?= esc($timeLabel) ?></dd>
                                </div>
                            </dl>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">ติดต่อสนาม</h3>
                            <dl class="space-y-2 text-sm text-gray-700">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">โทรศัพท์</dt>
                                    <dd class="font-medium">
                                        <?= $contactPhone !== '' ? esc($contactPhone) : 'ยังไม่ระบุ' ?>
                                    </dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">อีเมล</dt>
                                    <dd class="font-medium truncate">
                                        <?= $contactEmail !== '' ? esc($contactEmail) : 'ยังไม่ระบุ' ?>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </aside>
                </section>

                <!-- Divider -->
                <hr class="my-8 border-t border-gray-200">

                <!-- ปุ่ม Select courts / show schedule -->
                <section class="pb-8">
                    <div class="grid gap-6 md:grid-cols-[minmax(0,2fr)_minmax(220px,1fr)] md:items-start">
                        <div class="space-y-6">

                            <!-- เลือกวันที่และเวลา -->
                            <div class="space-y-3">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">เลือกวันที่และเวลา</h3>
                                <!-- ประเภทการจอง -->
                                <div class="space-y-1 mb-4">
                                    <label for="bookingTypeSelect" class="block text-xs font-medium text-gray-700">
                                        ประเภทการจอง
                                    </label>
                                    <select id="bookingTypeSelect" name="booking_type"
                                        class="block w-full rounded-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]">
                                        <option value="hourly">จองรายชั่วโมง</option>
                                        <option value="daily">จองรายวัน</option>
                                    </select>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <!-- วันที่ -->
                                    <div class="space-y-1">
                                        <label for="bookingDate" class="block text-xs font-medium text-gray-700">
                                            วันที่ต้องการจอง
                                        </label>
                                        <input type="date" id="bookingDate" name="booking_date"
                                            class="block w-full rounded-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]"
                                            min="<?= esc($today) ?>" max="<?= esc($maxBooking) ?>">
                                    </div>

                                    <!-- เวลาเริ่มต้น -->
                                    <div class="space-y-1">
                                        <label for="startTimeSelect" class="block text-xs font-medium text-gray-700">
                                            เวลาเริ่มต้น
                                        </label>
                                        <select id="startTimeSelect" name="start_time"
                                            class="block w-full rounded-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]">
                                            <option value="">— เลือกเวลาเริ่มต้น —</option>
                                        </select>
                                    </div>

                                    <!-- เวลาสิ้นสุด -->
                                    <div class="space-y-1">
                                        <label for="endTimeSelect" class="block text-xs font-medium text-gray-700">
                                            เวลาสิ้นสุด
                                        </label>
                                        <select id="endTimeSelect" name="end_time"
                                            class="block w-full rounded-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]">
                                            <option value="">— เลือกเวลาสิ้นสุด —</option>
                                        </select>
                                    </div>
                                </div>
                                <p id="timeHelpText" class="text-xs text-gray-500">
                                    สามารถจองได้เป็นช่วงชั่วโมงเต็ม เช่น 11:00–12:00
                                    และไม่สามารถเลือกเวลาที่ผ่านมาแล้วในวันนี้ได้
                                </p>
                                <p id="timeErrorText" class="text-xs font-semibold text-amber-600 hidden"></p>
                            </div>
                        </div>

                        <!-- ปุ่มไปหน้าตารางจอง -->
                        <div class="flex h-full md:items-end md:justify-end">
                            <div class="flex w-full max-w-xs flex-col items-stretch gap-4 h-full justify-between">
                                <?php if ($hasAnyField && $hasActiveField): ?>
                                    <!-- มีสนามย่อยและเปิดให้จองอย่างน้อย 1 -->
                                    <button type="button" id="btnShowSchedule"
                                        data-base-url="<?= base_url('customer/booking/stadium/' . $stadium['id']) ?>" class="inline-flex items-center justify-center rounded-full
                       bg-[var(--primary)] px-8 py-3 text-sm sm:text-base
                       font-semibold text-white shadow-md shadow-[var(--primary)]/40
                       hover:bg-teal-600 focus-visible:outline-none
                       focus-visible:ring-2 focus-visible:ring-[var(--primary)]
                       focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                                        <span class="mr-2 text-lg">📅</span>
                                        <span>show schedule</span>
                                    </button>
                                <?php elseif ($hasAnyField && !$hasActiveField): ?>
                                    <!-- มีสนามย่อยแต่ทุกสนามปิดปรับปรุง -->
                                    <button type="button" class="inline-flex cursor-not-allowed items-center justify-center rounded-full
                       bg-gray-200 px-8 py-3 text-sm sm:text-base
                       font-semibold text-gray-500 shadow-sm" disabled>
                                        สนามกำลังปิดปรับปรุง
                                    </button>
                                <?php else: ?>
                                    <!-- ไม่มีสนามย่อย: ใช้ลิงก์เดิม -->
                                    <a href="<?= base_url('customer/booking/stadium/' . $stadium['id']) ?>" class="inline-flex items-center justify-center rounded-full
                      bg-[var(--primary)] px-8 py-3 text-sm sm:text-base
                      font-semibold text-white shadow-md shadow-[var(--primary)]/40
                      hover:bg-teal-600 focus-visible:outline-none
                      focus-visible:ring-2 focus-visible:ring-[var(--primary)]
                      focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                                        <span class="mr-2 text-lg">📅</span>
                                        <span>show schedule</span>
                                    </a>
                                <?php endif; ?>

                                <?php if (!$hasAnyField || $hasActiveField): ?>
                                    <!-- ฟอร์มส่งข้อมูลการจองไปหลังบ้าน -->
                                    <form id="bookingSubmitForm" action="<?= route_to('customer.booking.add') ?>"
                                        method="post" class="mt-0">
                                        <?= csrf_field() ?>

                                        <!-- hidden ส่งข้อมูลหลัก -->
                                        <input type="hidden" name="stadium_id"
                                            value="<?= isset($stadium['id']) ? (int) $stadium['id'] : 0 ?>">
                                        <input type="hidden" name="stadium_name"
                                            value="<?= esc($stadium['name'] ?? $name) ?>">

                                        <input type="hidden" name="booking_date" id="bookingDateField">
                                        <input type="hidden" name="time_start" id="bookingTimeStartField">
                                        <input type="hidden" name="time_end" id="bookingTimeEndField">
                                        <input type="hidden" name="hours" id="bookingHoursField">
                                        <input type="hidden" name="items" id="bookingItemsField">
                                        <input type="hidden" name="field_price_per_hour" id="bookingPricePerHourField">
                                        <input type="hidden" name="field_base_price" id="bookingBasePriceField">
                                        <!-- กล่องสรุปราคา + ปุ่มจองเลย -->
                                        <aside id="bookingSummaryCard"
                                            class="rounded-2xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
                                            <div class="space-y-2 text-sm">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-gray-500">
                                                        ค่าจองสนาม (<span id="bookingHoursLabel">ต่อชั่วโมง</span>)
                                                    </span>
                                                    <span id="bookingFieldPrice"
                                                        class="text-sm font-semibold text-gray-900">--฿</span>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-gray-500">
                                                        ค่าบริการ (5%)
                                                    </span>
                                                    <span id="bookingServiceFee"
                                                        class="text-sm font-semibold text-gray-900">--฿</span>
                                                </div>
                                            </div>
                                            <div class="mt-3 border-t border-gray-100 pt-2">
                                                <p id="bookingItemsSummary" class="text-xs text-gray-700">
                                                    ยังไม่ได้เลือกไอเทม
                                                </p>
                                                <ul id="bookingItemsList" class="mt-1 space-y-1 text-xs">
                                                    <!-- JS จะมาสร้าง <li> เองทั้งหมด -->
                                                </ul>
                                            </div>

                                            <button type="button" id="btnBookNow" class="mt-3 inline-flex w-full items-center justify-center rounded-xl
                           bg-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-600 shadow-sm
                           transition cursor-not-allowed opacity-50 hover:bg-gray-300">
                                                จองเลย
                                            </button>
                                        </aside>
                                    </form>
                                <?php endif; ?>

                            </div>
                        </div>

                    </div>
                </section>

                <!-- Divider for items -->
                <hr class="my-8 border-t border-gray-200">

                <!-- รายการไอเทม / บริการของสนาม -->
                <?php if (!empty($items)): ?>
                    <section>
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">
                                บริการและไอเทมของสนาม
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-500">
                                เลือกไอเทมที่ต้องการใช้งานร่วมกับการจองสนาม เช่น ไม้แบด, ห้องพัก, นวด ฯลฯ
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <?php foreach ($items as $item): ?>
                                <article
                                    class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                                    <div class="space-y-1">
                                        <h3 class="text-sm font-semibold text-gray-900">
                                            <?= esc($item['name']) ?>
                                        </h3>
                                        <?php if (!empty($item['category'])): ?>
                                            <p class="text-xs text-gray-500">
                                                <?= esc($item['category']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if (!empty($item['desc'])): ?>
                                            <p class="mt-1 text-xs text-gray-600 line-clamp-2">
                                                <?= esc($item['desc']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="text-sm font-semibold text-gray-900">
                                            <?= number_format((float) $item['price'], 2) ?>฿
                                            <span class="text-xs font-normal text-gray-500">
                                                / <?= esc($item['unit'] ?? 'ครั้ง') ?>
                                            </span>
                                        </div>

                                        <button type="button" class="inline-flex items-center rounded-xl bg-[var(--primary)] px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-teal-600 transition
                                           add-item-btn" data-item-id="<?= (int) $item['id'] ?>"
                                            data-item-name="<?= esc($item['name']) ?>"
                                            data-item-price="<?= (float) $item['price'] ?>">
                                            + เพิ่มลงตะกร้า
                                        </button>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Divider -->
                <hr class="my-8 border-t border-gray-200">

                <!-- New Image Gallery Section -->
                <section class="mt-8" id="stadiumGallery">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">รูปภาพสนาม</h2>
                    <div class="grid grid-cols-2 gap-2"
                        data-images='<?= json_encode($galleryImages ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>'>
                        <!-- Main Image -->
                        <div class="relative col-span-1 group">
                            <img src="<?= esc($galleryImages[0] ?? $coverUrl) ?>" alt="Main stadium image"
                                class="h-full w-full object-cover rounded-lg cursor-pointer" data-gallery-item="0">
                            <button type="button" data-gallery-open
                                class="inline-flex items-center gap-2 rounded-lg bg-black/60 px-3 py-1.5 text-xs sm:text-sm font-semibold text-white backdrop-blur-sm hover:bg-black/80 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-5 h-5">
                                    <path
                                        d="M3.75 3.75A.75.75 0 0 1 4.5 3h11a.75.75 0 0 1 .75.75v11a.75.75 0 0 1-.75.75h-11A.75.75 0 0 1 3.75 14.75v-11zM5 5v8h10V5H5z" />
                                </svg>
                                <span>ดูรูปทั้งหมด</span>
                            </button>
                            <?php
                            // ใช้รูปจาก controller: $stadiumImages = [url1, url2, ...]
                            $stadiumImages = $stadiumImages ?? [];
                            ?>
                            <div id="stadiumGalleryOverlay"
                                class="fixed inset-0 z-40 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center px-4">
                                <div class="relative max-w-5xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                                            รูปภาพทั้งหมดของสนาม
                                        </h3>
                                        <button type="button" data-gallery-close
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800">
                                            <span class="sr-only">ปิด</span>
                                            ✕
                                        </button>
                                    </div>

                                    <!-- Body: รูปทั้งหมด -->
                                    <div class="p-4 max-h-[70vh] overflow-y-auto">
                                        <?php if (!empty($stadiumImages)): ?>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                                <?php foreach ($stadiumImages as $img): ?>
                                                    <div class="w-full h-36 sm:h-40">
                                                        <img src="<?= esc($img) ?>" alt="รูปสนาม"
                                                            class="h-full w-full object-cover rounded-xl">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-center text-sm text-gray-500">
                                                ยังไม่มีรูปภาพสนามให้แสดง
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- Thumbnail Grid -->
                        <div class="col-span-1 grid grid-cols-3 grid-rows-2 gap-2">
                            <?php for ($i = 1; $i < 7; $i++): ?>
                                <div class="cursor-pointer">
                                    <?php if (isset($galleryImages[$i])): ?>
                                        <img src="<?= esc($galleryImages[$i]) ?>" alt="Stadium thumbnail <?= $i ?>"
                                            class="h-full w-full object-cover rounded-lg" data-gallery-item="<?= $i ?>">
                                    <?php else: ?>
                                        <div class="h-full w-full bg-gray-200 rounded-lg"></div>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </section>

            </section>
        </article>
    </section>
</main>

<?= $this->endSection() ?>