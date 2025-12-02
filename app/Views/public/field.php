<?php

/**
 * field.php — หน้าแสดงรายการสนามย่อยของสนามหลัก (UX/UI เท่านั้น ใช้ข้อมูลจำลอง)
 *
 * หมายเหตุ:
 * - ตอนนี้ยังใช้ dummy data ในไฟล์ view เลย เพื่อโฟกัสแค่ UX/UI
 * - เวลาเชื่อมกับ Controller จริง ให้ลบส่วน dummy ออก แล้วส่งตัวแปร $stadium และ $fields มาจาก Controller แทน
 */

// -------------------- DUMMY DATA (ลบออกทีหลังเมื่อเชื่อม Controller) --------------------
$stadium = $stadium ?? [
    'name'        => 'Arena Sport Complex',
    'sport_emoji' => '⚽',
    'sport_name'  => 'ฟุตบอล',
    'location'    => 'เขตห้วยขวาง, กรุงเทพฯ',
    'hero_image'  => base_url('assets/uploads/home/batminton.webp'),
];

$stadiumId = $stadiumId ?? 1;

$fields = $fields ?? [
    [
        'id'          => 1,
        'name'        => 'สนามฟุตซอลในร่ม 1',
        'price_hour'  => 450,
        'open_time'   => '10:00 - 23:00 น.',
        'image'       => base_url('assets/uploads/home/batminton.webp'),
        'short_desc'  => 'หลังคาสูง โปร่ง ลมโกรกดี มีห้องน้ำและล็อกเกอร์ให้บริการ',
    ],
    [
        'id'          => 2,
        'name'        => 'สนามหญ้าเทียม 7 คน A',
        'price_hour'  => 650,
        'open_time'   => '09:00 - 01:00 น.',
        'image'       => base_url('assets/uploads/home/batminton.webp'),
        'short_desc'  => 'ไฟสว่างทั่วสนาม เหมาะกับทีมซ้อมจริงจังและเตะกระชับมิตร',
    ],
    [
        'id'          => 3,
        'name'        => 'สนามฟุตซอลในร่ม 2',
        'price_hour'  => 450,
        'open_time'   => '10:00 - 23:00 น.',
        'image'       => base_url('assets/uploads/home/batminton.webp'),
        'short_desc'  => 'พื้นนุ่มเล่นสบาย เหมาะสำหรับเตะชิล ๆ กับเพื่อน',
    ],
];
// --------------------------------------------------------------------------------------
?>

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
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="h-4 w-4">
                                <path fill-rule="evenodd"
                                    d="M10 2a6 6 0 00-6 6c0 4.418 6 10 6 10s6-5.582 6-10a6 6 0 00-6-6zM8 8a2 2 0 114 0 2 2 0 01-4 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span><?= esc($stadium['location']) ?></span>
                        </div>
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
                        $detailUrl = site_url('sport/show/' . $stadiumId);
                        ?>
                    <article
                        class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm shadow-black/5 ring-1 ring-gray-100 hover:-translate-y-0.5 hover:shadow-md hover:ring-gray-200 transition">
                        <div class="relative h-40 w-full overflow-hidden">
                            <img src="<?= esc($field['image']) ?>" alt="<?= esc($field['name']) ?>"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy">
                        </div>
                        <div class="flex flex-1 flex-col gap-2.5 px-3.5 py-3 sm:px-4 sm:py-3.5">
                            <div class="space-y-1">
                                <h3 class="text-sm sm:text-base font-semibold text-gray-900">
                                    <?= esc($field['name']) ?>
                                </h3>
                                <p class="mt-1 text-xs sm:text-sm text-gray-500 line-clamp-2">
                                    <?= esc($field['short_desc']) ?>
                                </p>
                            </div>
                            <div class="mt-auto flex items-end justify-between gap-2 pt-1">
                                <div class="space-y-0.5">
                                    <p class="text-[11px] sm:text-xs text-gray-500">ราคาเริ่มต้นต่อชั่วโมง</p>
                                    <p class="text-sm sm:text-base font-semibold text-[var(--primary)]">
                                        <?= number_format($field['price_hour']) ?>฿
                                        <span class="text-[11px] sm:text-xs font-normal text-gray-500">/ ชั่วโมง</span>
                                    </p>
                                    <p class="text-[11px] sm:text-xs text-gray-500">
                                        เวลาเปิด: <?= esc($field['open_time']) ?>
                                    </p>
                                </div>
                                <a href="<?= esc($detailUrl) ?>"
                                    class="inline-flex items-center justify-center rounded-xl bg-[var(--primary)] px-3 py-2 text-xs sm:text-sm font-medium text-white shadow-sm hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:ring-offset-1">
                                    ดูรายละเอียด / จอง
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Separator -->
            <div class="border-t border-gray-200"></div>

            <!-- Section 3: Image Gallery -->
            <div class="p-4 sm:p-6 space-y-4">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">
                    รูปภาพสนาม
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <!-- Large Image -->
                    <div class="relative w-full h-80">
                        <img src="https://placehold.co/800x600/000000/FFFFFF/png" alt="Main stadium image"
                            class="h-full w-full object-cover rounded-2xl">
                        <div class="absolute bottom-0 right-0 p-3">
                            <button type="button"
                                class="inline-flex items-center gap-2 rounded-lg bg-black/60 px-3 py-2 text-xs font-semibold text-white backdrop-blur-sm hover:bg-black/80 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-5 h-5">
                                    <path
                                        d="M3.75 3.75A.75.75 0 003 4.5v11a.75.75 0 00.75.75h13.5a.75.75 0 00.75-.75v-11a.75.75 0 00-.75-.75h-13.5zM13.5 4.5v3h3V4.5h-3zM4.5 4.5h3v3h-3V4.5zM4.5 8.25h3v3h-3v-3zm9 0v3h-3v-3h3zm-4.5 0h3v3h-3v-3zm-4.5 3.75h3v3h-3v-3zm4.5 0h3v3h-3v-3zm4.5 0h3v3h-3v-3z" />
                                </svg>
                                <span>ดูรูปทั้งหมด</span>
                            </button>
                        </div>
                    </div>
                    <!-- Small Images Grid -->
                    <div class="grid grid-cols-3 grid-rows-2 gap-2">
                        <div class="w-full h-full">
                            <img src="https://placehold.co/400x300/e2e8f0/334155/png" alt="Stadium image 1"
                                class="h-full w-full object-cover rounded-xl">
                        </div>
                        <div class="w-full h-full">
                            <img src="https://placehold.co/400x300/e2e8f0/334155/png" alt="Stadium image 2"
                                class="h-full w-full object-cover rounded-xl">
                        </div>
                        <div class="w-full h-full">
                            <img src="https://placehold.co/400x300/e2e8f0/334155/png" alt="Stadium image 3"
                                class="h-full w-full object-cover rounded-xl">
                        </div>
                        <div class="w-full h-full">
                            <img src="https://placehold.co/400x300/e2e8f0/334155/png" alt="Stadium image 4"
                                class="h-full w-full object-cover rounded-xl">
                        </div>
                        <div class="w-full h-full">
                            <img src="https://placehold.co/400x300/e2e8f0/334155/png" alt="Stadium image 5"
                                class="h-full w-full object-cover rounded-xl">
                        </div>
                        <div class="w-full h-full">
                            <img src="https://placehold.co/400x300/e2e8f0/334155/png" alt="Stadium image 6"
                                class="h-full w-full object-cover rounded-xl">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Separator -->
            <div class="border-t border-gray-200"></div>

            <!-- Section 4: Map -->
            <div class="p-4 sm:p-6 space-y-4">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900">
                    สนามบนแผนที่
                </h2>
                <div
                    class="relative rounded-2xl bg-white shadow-lg shadow-black/5 ring-1 ring-black/5 overflow-hidden aspect-[16/7]">
                    <div
                        class="absolute top-0 left-0 z-10 p-3 bg-black/40 backdrop-blur-sm rounded-br-2xl rounded-tl-2xl">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="h-5 w-5 text-white">
                                <path fill-rule="evenodd"
                                    d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.757.433c.12.059.233.109.34.15l.001.001zM10 2a7 7 0 00-7 7c0 3.866 3.134 7 7 7s7-3.134 7-7a7 7 0 00-7-7zM8 9a2 2 0 114 0 2 2 0 01-4 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm font-medium text-white"><?= esc($stadium['location']) ?></p>
                        </div>
                    </div>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d123995.89527263919!2d100.48204239726562!3d13.780883000000003!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29e9c336b33a5%3A0x47e81d620583a45e!2sHuai%20Khwang%2C%20Bangkok%2010310%2C%20Thailand!5e0!3m2!1sen!2sus!4v1732509157297!5m2!1sen!2sus"
                        class="w-full h-full" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

        </div> <!-- End of Unified Card -->
    </div>
</section>

<?= $this->endSection() ?>