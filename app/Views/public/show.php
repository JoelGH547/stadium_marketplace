<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?php
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
  $locationShort = trim($district . ($district && $province ? ', ' : '') . $province);

  $contactPhone = trim($stadium['contact_phone'] ?? '');
  $contactEmail = trim($stadium['contact_email'] ?? '');

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
?>
<main class="bg-gray-50 min-h-screen pb-10">
  <section class="relative mx-auto max-w-5xl">
    <!-- ปุ่ม Back อยู่นอกการ์ด ชิดซ้ายบนของรูป -->
    <button type="button"
            onclick="history.back()"
            class="absolute -left-12 top-4 z-20 inline-flex items-center justify-center
                   w-10 h-10 rounded-full bg-gray-800/85 text-white text-xl shadow-md
                   hover:bg-gray-800 transition">
      ‹
    </button>

    <article id="stadiumDetail" class="bg-white shadow-sm sm:shadow-md sm:rounded-3xl overflow-hidden"
             data-lat="<?= esc($lat ?? '') ?>"
             data-lng="<?= esc($lng ?? '') ?>">
    <!-- Hero รูปสนาม + แกลเลอรี -->
    <section class="relative" id="stadiumHero"
             data-images='<?= json_encode($galleryImages ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>'>
      <div class="relative">
        <img id="heroImage"
             src="<?= esc($galleryImages[0] ?? $coverUrl) ?>"
             alt="<?= esc($name) ?>"
             class="h-64 sm:h-80 md:h-96 w-full object-cover transition-all duration-300 ease-out">

        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-28
                    bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>

        <!-- ปุ่มเลื่อนรูป ซ้าย / ขวา -->
        <button type="button"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-20 text-white text-3xl md:text-4xl drop-shadow-lg"
                data-hero-prev>
          ‹
        </button>
        <button type="button"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-20 text-white text-3xl md:text-4xl drop-shadow-lg"
                data-hero-next>
          ›
        </button>

        <!-- ปุ่ม share / favorite -->
        <div class="absolute inset-x-0 top-0 flex justify-end items-start p-4">
          <div class="flex gap-2">
            <button type="button"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-full
                           bg-black/60 text-white text-sm shadow-md backdrop-blur
                           hover:bg-black/80 transition"
                    title="แชร์ลิงก์สนามนี้">
              ⤴
            </button>
            <button type="button"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-full
                           bg-black/60 text-white text-sm shadow-md backdrop-blur
                           hover:bg-black/80 transition"
                    title="เพิ่มในรายการโปรด">
              ❤
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- เนื้อหาหลัก --><!-- เนื้อหาหลัก -->
    <section class="px-4 sm:px-8 pb-8 pt-6">
      <!-- ชื่อ + meta แถวบน -->
      <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
  <div>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-[color:var(--ink)]">
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

      <!-- ที่อยู่ย่อ -->
      <span class="inline-flex items-center gap-1">
        📍 <span><?= $locationShort !== '' ? esc($locationShort) : 'ยังไม่ระบุพื้นที่' ?></span>
      </span>

      <!-- เวลาเปิดปิด / label เวลา -->
      <span class="inline-flex items-center gap-1">
        ⏰ <span><?= esc($timeLabel) ?></span>
      </span>
    </div>
  </div>

  <div class="inline-flex items-center gap-2 rounded-full bg-[var(--primary)]
              text-white px-4 py-2 text-sm font-semibold shadow-md shadow-[var(--primary)]/30">
    <span class="text-lg leading-none"><?= esc($categoryEmoji) ?></span>
    <span class="leading-none"><?= esc($categoryName) ?></span>
  </div>
</header>


      <!-- แผนที่ -->
      <section class="mt-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-2">Location</h2>
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-100">
          <?php if ($hasMap): ?>
            <iframe
              src="https://www.google.com/maps?q=<?= urlencode($lat . ',' . $lng) ?>&output=embed"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              class="w-full h-56 md:h-64 border-0">
            </iframe>
          <?php else: ?>
            <div class="h-40 flex items-center justify-center text-gray-400 text-sm">
              ยังไม่มีแผนที่สำหรับสนามนี้
            </div>
          <?php endif; ?>
        </div>
        <p class="mt-2 text-xs sm:text-sm text-gray-600">
          <?= esc($addressFull) ?>
        </p>
      </section>

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

      <!-- ปุ่ม Select courts / show schedule -->
      <section class="mt-10 border-t border-dashed border-gray-200 pt-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">Select courts</h2>
            <p class="text-sm text-gray-600">
              เลือกช่วงเวลาการจองและดูตารางสนามในขั้นตอนถัดไป
            </p>
          </div>
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
        </div>
      </section>
    </section>
  </article>
  </section>
</main>

<?= $this->endSection() ?>
