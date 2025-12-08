<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>
สนามย่อย — <?= esc($stadium['name']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- เนื้อหาหลัก: รายการสนามย่อย -->

<section class="pt-8 pb-10 sm:pt-10 sm:pb-14 bg-gray-50">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

        <!-- Unified Card -->
        <div class="relative rounded-2xl bg-white shadow-lg shadow-black/5 ring-1 ring-black/5">

            <!-- Share & Favorite Buttons -->
            <div class="absolute top-6 right-6 z-10 flex items-center gap-2">
                <button type="button" title="แชร์หน้านี้"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-700 transition hover:bg-gray-200 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                        <path
                            d="M13 4.5a2.5 2.5 0 11.702 4.281l-5.523 3.08a2.502 2.502 0 010 1.278l5.523 3.08a2.5 2.5 0 11-.498 1.782l-5.523-3.08a2.5 2.5 0 110-4.842l5.523-3.08A2.5 2.5 0 0113 4.5z" />
                    </svg>
                </button>
                <button type="button" title="เพิ่มในรายการโปรด"
                    class="group/favorite flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-gray-700 transition hover:bg-rose-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="h-6 w-6 text-gray-600 transition group-hover/favorite:text-rose-600">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                </button>
            </div>

            <!-- Section 1: Main Info -->
            <div class="px-4 py-5 sm:px-6 sm:py-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-semibold text-gray-900">
                            <?= esc($stadium['name']) ?>
                        </h1>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 border border-emerald-100">
                            <?= esc($stadium['sport_emoji']) ?> <?= esc($stadium['sport_name']) ?>
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs sm:text-sm text-gray-600">
                        <!-- ดาวรีวิว (mock) -->
                        <span class="inline-flex items-center gap-1">
                            ⭐ <strong class="text-gray-900">4.8</strong>
                        </span>

                        <!-- ระยะทางจากผู้ใช้ (จะให้ JS อัปเดต) -->
                        <span class="inline-flex items-center gap-1 dist-badge"
                            data-lat="<?= esc($stadium['lat'] ?? '') ?>" data-lng="<?= esc($stadium['lng'] ?? '') ?>">
                            📍 <span>-- km.</span>
                        </span>

                        <!-- ที่อยู่ -->
                        <span class="inline-flex items-center gap-1">
                            📌 <span><?= esc($stadium['location'] ?? '') ?></span>
                        </span>

                        <!-- เวลาเปิดปิด (ถ้าคุณมีตัวแปรนี้แล้ว) -->
                        <?php if (!empty($stadium['open_label'])): ?>
                            <span class="inline-flex items-center gap-1">
                                ⏰ <span><?= esc($stadium['open_label']) ?></span>
                            </span>
                        <?php endif; ?>
                    </div>


                </div>
            </div>
            <!-- Separator -->
            <div class="border-t border-gray-200"></div>

            <!-- Section 2: Sub-fields -->
            <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">
                            เลือกสนามย่อยที่ต้องการจอง
                        </h2>
                        <p class="mt-1 text-xs sm:text-sm text-gray-600">
                            สนามย่อยแต่ละสนามมีขนาด ราคา และบรรยากาศที่แตกต่างกัน เลือกให้เหมาะกับทีมของคุณ
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    <?php foreach ($fields as $field): ?>
                        <?php
                        $detailUrl = site_url('sport/show/' . $field['id']);
                        ?>
                        <article
                            class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm shadow-black/5 ring-1 ring-gray-100 hover:-translate-y-0.5 hover:shadow-md hover:ring-gray-200 transition">
                            <div class="relative h-40 w-full overflow-hidden">
                                <img src="<?= esc($field['image']) ?>" alt="<?= esc($field['name']) ?>"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy">
                            </div>
                            <div class="flex flex-1 flex-col p-4">
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-gray-900">
                                        <a href="<?= esc($detailUrl) ?>" class="focus:outline-none">
                                            <span class="absolute inset-0" aria-hidden="true"></span>
                                            <?= esc($field['name']) ?>
                                        </a>
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                                        <?= esc($field['short_desc']) ?>
                                    </p>
                                </div>
                                <div class="mt-4 space-y-3">
                                    <?php
                                    $priceHour  = $field['price_hour']  ?? null;
                                    $priceDaily = $field['price_daily'] ?? null;
                                    ?>
                                    <div class="space-y-1 text-sm">
                                        <?php if ($priceHour !== null && $priceHour > 0): ?>
                                            <p class="flex justify-between font-medium text-gray-700">
                                                <span class="text-gray-500">ราคา/ชม.</span>
                                                <span class="text-emerald-600">
                                                    <?= number_format($priceHour, 0) ?> บาท
                                                </span>
                                            </p>
                                        <?php endif; ?>

                                        <?php if ($priceDaily !== null && $priceDaily > 0): ?>
                                            <p class="flex justify-between font-medium text-gray-700">
                                                <span class="text-gray-500">ราคา/วัน</span>
                                                <span class="text-emerald-600">
                                                    <?= number_format($priceDaily, 0) ?> บาท
                                                </span>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                    $isActive = ($field['status'] ?? 'active') === 'active';
                                    ?>

                                    <?php if ($isActive): ?>
                                        <a href="<?= esc($detailUrl) ?>"
                                            class="relative z-10 block w-full text-center rounded-lg bg-[var(--primary)] px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:ring-offset-2">
                                            ดูรายละเอียด / จอง
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= esc($detailUrl) ?>"
                                            class="relative z-10 block w-full text-center rounded-lg bg-gray-200 px-4 py-2.5 text-sm font-medium text-gray-500 shadow-sm cursor-pointer">
                                            ปิดปรับปรุง
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Separator -->
            <div class="border-t border-gray-200"></div>

            <!-- Section 3: Image Gallery -->
            <?php
            /** @var array $stadiumImages มาจาก Controller */
            $stadiumImages = $stadiumImages ?? [];
            $mainImage     = $stadiumImages[0] ?? ($stadium['hero_image'] ?? '');
            $thumbImages   = array_slice($stadiumImages, 1, 6); // เอารูปย่อยไม่เกิน 6 รูป
            ?>
            <div class="p-4 sm:p-6 space-y-4">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">
                    รูปภาพสนาม
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <!-- Large Image -->
                    <div class="relative w-full h-80">
                        <?php if ($mainImage): ?>
                            <img src="<?= esc($mainImage) ?>" alt="รูปสนามหลัก"
                                class="h-full w-full object-cover rounded-2xl">
                        <?php else: ?>
                            <div
                                class="h-full w-full rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400">
                                ไม่มีรูปภาพสนาม
                            </div>
                        <?php endif; ?>

                        <?php if (count($stadiumImages) > 1): ?>
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
                    </div>

                    <!-- Small Images Grid -->
                    <?php if (!empty($thumbImages)): ?>
                        <div class="grid grid-cols-3 grid-rows-2 gap-2">
                            <?php foreach ($thumbImages as $img): ?>
                                <div class="w-full h-full">
                                    <img src="<?= esc($img) ?>" alt="รูปสนาม" class="h-full w-full object-cover rounded-xl">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center justify-center text-sm text-gray-400">
                            ยังไม่มีรูปภาพเพิ่มเติม
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            // ใช้รูปจาก controller: $stadiumImages = [url1, url2, ...]
            $stadiumImages = $stadiumImages ?? [];
            ?>
            <div id="stadiumGalleryOverlay"
                class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center px-4"
                role="dialog" aria-modal="true" aria-labelledby="galleryModalTitle">
                <div
                    class="relative max-w-4xl w-full bg-gray-800 rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700 text-white">
                        <h3 id="galleryModalTitle" class="text-base sm:text-lg font-semibold">
                            รูปภาพทั้งหมดของสนาม
                        </h3>
                        <button type="button" data-gallery-close
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full text-gray-300 hover:bg-gray-700 hover:text-white transition">
                            <span class="sr-only">ปิด</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body: Gallery -->
                    <div class="p-2 sm:p-4 flex-1 flex flex-col gap-4 overflow-hidden">
                        <?php if (!empty($stadiumImages)): ?>
                            <!-- Main Image Display -->
                            <div id="galleryImageContainer"
                                class="relative w-full flex-1 bg-black rounded-lg overflow-hidden cursor-grab touch-none">
                                <img id="galleryMainImage" src="<?= esc($stadiumImages[0]) ?>" alt="รูปสนามหลัก"
                                    class="h-full w-full object-contain transition-transform duration-150 ease-out">

                                <!-- Prev/Next Buttons -->
                                <button id="galleryPrevBtn" type="button"
                                    class="absolute top-1/2 left-2 sm:left-4 -translate-y-1/2 h-10 w-10 rounded-full bg-black/40 text-white flex items-center justify-center hover:bg-black/60 transition disabled:opacity-50 disabled:cursor-not-allowed z-10">
                                    <span class="sr-only">รูปก่อนหน้า</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button id="galleryNextBtn" type="button"
                                    class="absolute top-1/2 right-2 sm:right-4 -translate-y-1/2 h-10 w-10 rounded-full bg-black/40 text-white flex items-center justify-center hover:bg-black/60 transition disabled:opacity-50 disabled:cursor-not-allowed z-10">
                                    <span class="sr-only">รูปถัดไป</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Thumbnails -->
                            <div id="galleryThumbnails" class="flex space-x-3 overflow-x-auto p-1 -mx-1">
                                <?php foreach ($stadiumImages as $index => $img): ?>
                                    <div class="flex-shrink-0">
                                        <button type="button" data-index="<?= $index ?>"
                                            class="gallery-thumb block h-20 w-28 rounded-md ring-2 ring-offset-2 ring-offset-gray-800 ring-transparent focus:outline-none focus:ring-blue-500 transition">
                                            <img src="<?= esc($img) ?>" class="h-full w-full object-cover rounded"
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
            <!-- Separator -->
            <div class="border-t border-gray-200"></div>

            <!-- Section: แผนที่สนาม -->
            <div class="p-4 sm:p-6 space-y-3">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">สนามบนแผนที่</h2>

                <?php if (!empty($stadium['lat']) && !empty($stadium['lng'])): ?>
                    <div id="stadium-map" data-lat="<?= esc($stadium['lat']) ?>" data-lng="<?= esc($stadium['lng']) ?>"
                        class="relative z-10 w-full h-80 rounded-xl overflow-hidden border border-gray-200">
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-sm">ยังไม่ระบุพิกัดแผนที่</p>
                <?php endif; ?>
            </div>
        </div> <!-- End of Unified Card -->
    </div>
</section>

<?= $this->endSection() ?>