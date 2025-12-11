<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?php
// ข้อมูลหลักของสนาม
$name           = $stadium['name'] ?? '';
$price          = isset($stadium['price']) ? (float) $stadium['price'] : 0;
$categoryName   = $stadium['category_name']  ?? '';
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
$timeLabel    = ($openTimeRaw && $closeTimeRaw) ? ($openTimeRaw . ' – ' . $closeTimeRaw) : 'ยังไม่ระบุเวลา';

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

// Get the first field for initial display, regardless of status.
// If no fields exist, $initialField will be null.
$initialField = $fieldsRaw[0] ?? null;

// ✅ ตรวจสอบสถานะของ "สนามย่อย" ที่กำลังดูอยู่
$stadiumStatus = strtolower((string) ($stadium['status'] ?? 'active'));
$isMaintenance = ($stadiumStatus === 'maintenance');
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
                            <dl id="stadiumInfoBox" class="space-y-2 text-sm text-gray-700">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">ชื่อสนาม</dt>
                                    <dd class="font-medium text-right"><?= esc($name) ?></dd>
                                </div>
                                <?php if ($initialField): ?>
                                    <div class="flex justify-between gap-3">
                                        <dt class="text-gray-500">สนามย่อย</dt>
                                        <dd id="infoBoxFieldName" class="font-medium text-right">
                                            <?= esc($initialField['name']) ?></dd>
                                    </div>
                                <?php endif; ?>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">ประเภทกีฬา</dt>
                                    <dd class="font-medium text-right"><?= esc($categoryEmoji) ?>
                                        <?= esc($categoryName) ?></dd>
                                </div>

                                <?php
                                // Note: Assuming 'price_day' is the key for daily price from the controller
                                $priceHour  = $initialField['price_hour'] ?? null;
                                $priceDaily = $initialField['price_day'] ?? null;
                                // ✅ สถานะสนามย่อย (active / maintenance)
                                $fieldStatusRaw   = $initialField['status'] ?? 'active';
                                $fieldStatusKey   = strtolower((string) $fieldStatusRaw);
                                $fieldStatusLabel = ($fieldStatusKey === 'maintenance') ? 'Maintenance' : 'Active';
                                ?>

                                <?php if ($priceHour !== null && $priceHour > 0): ?>
                                    <div class="flex justify-between gap-3" data-info-price="hour">
                                        <dt class="text-gray-500">ราคา/ชม.</dt>
                                        <dd id="infoBoxPriceHour" class="font-medium text-right">
                                            <?= number_format($priceHour, 0) ?> บาท
                                        </dd>
                                    </div>
                                <?php endif; ?>

                                <?php if ($priceDaily !== null && $priceDaily > 0): ?>
                                    <div class="flex justify-between gap-3" data-info-price="day">
                                        <dt class="text-gray-500">ราคา/วัน</dt>
                                        <dd id="infoBoxPriceDay" class="font-medium text-right">
                                            <?= number_format($priceDaily, 0) ?> บาท
                                        </dd>
                                    </div>
                                <?php endif; ?>

                                <?php if (($priceHour === null || $priceHour == 0) && ($priceDaily === null || $priceDaily == 0)): ?>
                                    <div class="flex justify-between gap-3" data-info-price="none">
                                        <dt class="text-gray-500">ราคา/ชม.</dt>
                                        <dd class="font-medium text-right">
                                            ยังไม่ระบุราคา
                                        </dd>
                                    </div>
                                <?php endif; ?>

                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">เวลาเปิด–ปิด</dt>
                                    <dd class="font-medium text-right"><?= esc($timeLabel) ?></dd>
                                </div>
                                <?php if ($initialField): ?>
                                    <!-- ✅ แถวแสดงสถานะสนามย่อย -->
                                    <div class="flex justify-between gap-3">
                                        <dt class="text-gray-500">สถานะ</dt>
                                        <dd class="font-medium text-right">
                                            <?php if ($fieldStatusKey === 'maintenance'): ?>
                                                <span
                                                    class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-600">
                                                    <?= esc($fieldStatusLabel) ?>
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-600">
                                                    <?= esc($fieldStatusLabel) ?>
                                                </span>
                                            <?php endif; ?>
                                        </dd>
                                    </div>
                                <?php endif; ?>
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
                <section class="relative pb-8">
                    <?php if ($isMaintenance): ?>
                        <div
                            class="absolute inset-0 z-10 flex items-center justify-center rounded-2xl bg-gray-100/60 backdrop-blur-[2px]">
                            <div class="text-center">
                                <p class="text-base font-semibold text-gray-800">ปิดปรับปรุง</p>
                                <p class="text-sm text-gray-600">ไม่สามารถจองสนามนี้ได้ในขณะนี้</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div
                        class="grid gap-6 md:grid-cols-[minmax(0,2fr)_minmax(220px,1fr)] md:items-start <?php if ($isMaintenance) echo 'pointer-events-none'; ?>">
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
                                <div id="hourlyBookingFields">
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
                                </div>
                                <div id="dailyBookingFields" class="hidden">
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <!-- วันที่เริ่มต้น -->
                                        <div class="space-y-1">
                                            <label for="startDate" class="block text-xs font-medium text-gray-700">
                                                วันที่เริ่มต้น
                                            </label>
                                            <input type="date" id="startDate" name="start_date"
                                                class="block w-full rounded-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]">
                                        </div>

                                        <!-- วันที่สิ้นสุด -->
                                        <div class="space-y-1">
                                            <label for="endDate" class="block text-xs font-medium text-gray-700">
                                                วันที่สิ้นสุด
                                            </label>
                                            <input type="date" id="endDate" name="end_date"
                                                class="block w-full rounded-full border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]">
                                        </div>
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
                                <?php if ($hasAnyField): ?>
                                    <!-- "Show schedule" button -->
                                    <button type="button" id="btnShowSchedule"
                                        data-base-url="<?= base_url('customer/booking/stadium/' . $stadium['id']) ?>"
                                        class="inline-flex items-center justify-center rounded-full
                                                                   bg-[var(--primary)] px-8 py-3 text-sm sm:text-base
                                                                   font-semibold text-white shadow-md shadow-[var(--primary)]/40
                                                                   hover:bg-teal-600 focus-visible:outline-none
                                                                   focus-visible:ring-2 focus-visible:ring-[var(--primary)]
                                                                   focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                                        <span class="mr-2 text-lg">📅</span>
                                        <span>show schedule</span>
                                    </button>

                                    <!-- Booking form -->
                                    <form id="bookingSubmitForm" action="<?= site_url('sport/cart/add') ?>"
                                        method="post" class="mt-0">
                                        <?= csrf_field() ?>

                                        <!-- hidden fields -->
                                        <input type="hidden" name="stadium_id"
                                            value="<?= isset($stadium['id']) ? (int) $stadium['id'] : 0 ?>">
                                        <input type="hidden" name="stadium_name"
                                            value="<?= esc($stadium['name'] ?? $name) ?>">
                                        <input type="hidden" name="field_id" 
                                            value="<?= isset($fields[0]['id']) ? (int) $fields[0]['id'] : 0 ?>">
                                        <input type="hidden" name="stadium_image" 
                                            value="<?= esc($stadium['outside_images'] ? (json_decode($stadium['outside_images'])[0] ?? '') : ($stadium['cover_image'] ?? '')) ?>">

                                        <input type="hidden" name="booking_type" id="bookingTypeField">
                                        
                                        <!-- Hourly Fields -->
                                        <input type="hidden" name="booking_date" id="bookingDateField">
                                        <input type="hidden" name="time_start" id="bookingTimeStartField">
                                        <input type="hidden" name="time_end" id="bookingTimeEndField">
                                        <input type="hidden" name="hours" id="bookingHoursField">
                                        
                                        <!-- Daily Fields -->
                                        <input type="hidden" name="start_date" id="bookingStartDateField">
                                        <input type="hidden" name="end_date" id="bookingEndDateField">
                                        <input type="hidden" name="days" id="bookingDaysField">

                                        <!-- Prices & Items -->
                                        <input type="hidden" name="items" id="bookingItemsField">
                                        <input type="hidden" name="field_price_per_hour" id="bookingPricePerHourField">
                                        <input type="hidden" name="field_price_per_day" id="bookingPricePerDayField">
                                        <input type="hidden" name="field_base_price" id="bookingBasePriceField">

                                        <!-- Price summary card -->
                                        <aside id="bookingSummaryCard"
                                            class="rounded-2xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
                                            <div class="space-y-2 text-sm">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-gray-500">
                                                        ค่าจองสนาม
                                                        <span id="bookingDurationWrapper" class="hidden">
                                                            (<span id="bookingHoursLabel"></span>)
                                                        </span>
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
                                                    <!-- JS will populate this -->
                                                </ul>
                                            </div>

                                            <button type="button" id="btnBookNow"
                                                class="mt-3 inline-flex w-full items-center justify-center rounded-xl
                                                                       bg-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-600 shadow-sm
                                                                       transition cursor-not-allowed opacity-50 hover:bg-gray-300">
                                                จองเลย
                                            </button>
                                        </aside>
                                    </form>
                                <?php else: ?>
                                    <!-- Fallback for when there are no fields -->
                                    <a href="<?= base_url('customer/booking/stadium/' . $stadium['id']) ?>"
                                        class="inline-flex items-center justify-center rounded-full
                                                                  bg-[var(--primary)] px-8 py-3 text-sm sm:text-base
                                                                  font-semibold text-white shadow-md shadow-[var(--primary)]/40
                                                                  hover:bg-teal-600 focus-visible:outline-none
                                                                  focus-visible:ring-2 focus-visible:ring-[var(--primary)]
                                                                  focus-visible:ring-offset-2 focus-visible:ring-offset-white">
                                        <span class="mr-2 text-lg">📅</span>
                                        <span>show schedule</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Divider for items -->
                <hr class="my-8 border-t border-gray-200">

                <!-- รายการไอเทม / บริการของสนาม -->
                <?php if (!empty($groupedItems)): ?>
                    <section class="mt-8">
                        <div class="flex items-center justify-between gap-3 mb-6 pb-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">
                                    บริการและไอเทมเสริม
                                </h2>
                                <p class="text-sm text-gray-500 mt-1">
                                    เลือกอุปกรณ์หรือบริการเพิ่มเติมเพื่อความสะดวกสบายของคุณ
                                </p>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <?php foreach ($groupedItems as $category => $items): ?>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                        <span class="w-1 h-6 bg-[var(--primary)] rounded-full"></span>
                                        <?= esc($category) ?>
                                    </h3>
                                    
                                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        <?php foreach ($items as $item): ?>
                                            <article class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition-all duration-300">
                                                
                                                <!-- Image -->
                                                <div class="relative h-32 w-full bg-gray-100 overflow-hidden">
                                                    <?php if (!empty($item['image'])): ?>
                                                        <img src="<?= base_url('assets/uploads/items/' . $item['image']) ?>" 
                                                             alt="<?= esc($item['name']) ?>" 
                                                             class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                                                    <?php else: ?>
                                                        <div class="flex h-full items-center justify-center text-gray-400">
                                                            <i class="fas fa-image text-3xl opacity-50"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Price Tag Removed as per request -->
                                                </div>

                                                <!-- Content -->
                                                <div class="p-4 flex flex-col flex-1">
                                                    <div class="flex-1 space-y-1">
                                                        <h4 class="font-bold text-gray-900 line-clamp-1" title="<?= esc($item['name']) ?>">
                                                            <?= esc($item['name']) ?>
                                                        </h4>
                                                        <?php if (!empty($item['description'])): ?>
                                                            <p class="text-xs text-gray-500 line-clamp-2 min-h-[2.5em]">
                                                                <?= esc($item['description']) ?>
                                                            </p>
                                                        <?php else: ?>
                                                            <p class="text-xs text-gray-400 italic">ไม่มีรายละเอียด</p>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="mt-4 flex items-center justify-between gap-2">
                                                        <span class="text-sm font-semibold text-[var(--primary)]">
                                                            ฿<?= number_format((float) $item['price']) ?> <span class="text-xs text-gray-500 font-normal">/ <?= esc($item['unit'] ?? 'ชิ้น') ?></span>
                                                        </span>
                                                        <button type="button" 
                                                                class="add-item-btn inline-flex items-center gap-1.5 rounded-xl bg-[var(--primary)] px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-teal-700 transition-colors"
                                                                data-item-id="<?= (int) $item['id'] ?>"
                                                                data-item-name="<?= esc($item['name']) ?>"
                                                                data-item-price="<?= (float) $item['price'] ?>"
                                                                data-item-image="<?= esc($item['image'] ?? '') ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                                            </svg>
                                                            เพิ่ม
                                                        </button>
                                                    </div>
                                                </div>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
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

                            <?php if (count($galleryImages) > 1): ?>
                                <div class="absolute bottom-0 right-0 p-3">
                                    <button type="button" data-gallery-open
                                        class="inline-flex items-center gap-2 rounded-lg bg-black/60 px-3 py-1.5 text-xs sm:text-sm font-semibold text-white backdrop-blur-sm hover:bg-black/80 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-5 h-5">
                                            <path
                                                d="M3.75 3.75A.75.75 0 0 1 4.5 3h11a.75.75 0 0 1 .75.75v11a.75.75 0 0 1-.75.75h-11A.75.75 0 0 1 3.75 14.75v-11zM5 5v8h10V5H5z" />
                                        </svg>
                                        <span>ดูรูปทั้งหมด</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div id="stadiumGalleryOverlay"
                                class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center px-4"
                                role="dialog" aria-modal="true" aria-labelledby="galleryModalTitle">
                                <div
                                    class="relative max-w-4xl w-full bg-gray-800 rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
                                    <!-- Header -->
                                    <div
                                        class="flex items-center justify-between px-4 py-3 border-b border-gray-700 text-white">
                                        <h3 id="galleryModalTitle" class="text-base sm:text-lg font-semibold">
                                            รูปภาพทั้งหมดของสนาม
                                        </h3>
                                        <button type="button" data-gallery-close
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-full text-gray-300 hover:bg-gray-700 hover:text-white transition">
                                            <span class="sr-only">ปิด</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Body: Gallery -->
                                    <div class="p-2 sm:p-4 flex-1 flex flex-col gap-4 overflow-hidden">
                                        <?php if (!empty($galleryImages)): ?>
                                            <!-- Main Image Display -->
                                            <div id="galleryImageContainer"
                                                class="relative w-full flex-1 bg-black rounded-lg overflow-hidden cursor-grab touch-none">
                                                <img id="galleryMainImage" src="<?= esc($galleryImages[0]) ?>"
                                                    alt="รูปสนามหลัก"
                                                    class="h-full w-full object-contain transition-transform duration-150 ease-out">

                                                <!-- Prev/Next Buttons -->
                                                <button id="galleryPrevBtn" type="button"
                                                    class="absolute top-1/2 left-2 sm:left-4 -translate-y-1/2 h-10 w-10 rounded-full bg-black/40 text-white flex items-center justify-center hover:bg-black/60 transition disabled:opacity-50 disabled:cursor-not-allowed z-10">
                                                    <span class="sr-only">รูปก่อนหน้า</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                </button>
                                                <button id="galleryNextBtn" type="button"
                                                    class="absolute top-1/2 right-2 sm:right-4 -translate-y-1/2 h-10 w-10 rounded-full bg-black/40 text-white flex items-center justify-center hover:bg-black/60 transition disabled:opacity-50 disabled:cursor-not-allowed z-10">
                                                    <span class="sr-only">รูปถัดไป</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Thumbnails -->
                                            <div id="galleryThumbnails" class="flex space-x-3 overflow-x-auto p-1 -mx-1">
                                                <?php foreach ($galleryImages as $index => $img): ?>
                                                    <div class="flex-shrink-0">
                                                        <button type="button" data-index="<?= $index ?>"
                                                            class="gallery-thumb block h-20 w-28 rounded-md ring-2 ring-offset-2 ring-offset-gray-800 ring-transparent focus:outline-none focus:ring-blue-500 transition">
                                                            <img src="<?= esc($img) ?>"
                                                                class="h-full w-full object-cover rounded"
                                                                alt="รูปย่อย <?= $index + 1 ?>">
                                                        </button>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex-1 flex items-center justify-center">
                                                <p class="text-center text-gray-400 py-10">
                                                    ยังไม่มีรูปภาพสนามให้แสดง
                                                </p>
                                            </div>
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



<script>
    // Pass PHP data to JS
    window.cartData = <?= json_encode($cartData ?? null) ?>;
</script>


<?= $this->endSection() ?>