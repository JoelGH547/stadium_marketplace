<?= $this->extend('layouts/public') ?>

<?= $this->section('title') ?>
<?= esc($title ?? 'รายการโปรดของฉัน') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
/** @var array $favorites */
/** @var array $favoriteMap */
$favorites   = $favorites ?? [];
$favoriteMap = $favoriteMap ?? [];
?>

<section class="bg-gray-50 py-10 sm:py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">รายการโปรดของฉัน</h1>
                <p class="text-sm text-gray-600 mt-1">สนามที่คุณกดหัวใจไว้จะอยู่ที่นี่</p>
            </div>
            <a href="<?= base_url('sport') ?>"
               class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                กลับไปค้นหาสนาม
            </a>
        </div>

        <?php if (empty($favorites)): ?>
            <div class="rounded-2xl bg-white p-8 ring-1 ring-black/5">
                <p class="text-gray-700">ยังไม่มีรายการโปรด</p>
                <p class="text-sm text-gray-500 mt-1">ลองกดหัวใจจากหน้า Home หรือหน้าสนาม แล้วกลับมาดูที่หน้านี้ได้เลย</p>
            </div>
        <?php else: ?>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <?php foreach ($favorites as $v): ?>
                    <?php
                        $sid = (int)($v['id'] ?? 0);
                        $isFav = !empty($favoriteMap[$sid]);
                        $name = (string)($v['name'] ?? '');
                        $typeIcon  = (string)($v['category_emoji'] ?? '🏟️');
                        $typeLabel = (string)($v['category_name'] ?? 'สนามกีฬา');

                        $cover = $v['cover_image'] ?? null;
                        $coverUrl = $cover ? base_url('assets/uploads/stadiums/' . $cover) : base_url('assets/uploads/home/1.jpg');

                        $avg = (float)($v['rating_avg'] ?? 0);
                        $cnt = (int)($v['rating_count'] ?? 0);

                        $detailUrl = $sid > 0 ? base_url('sport/fields/' . $sid) : '#';
                    ?>

                    <li class="relative rounded-2xl bg-white shadow-sm hover:shadow-md transition overflow-hidden ring-1 ring-black/5">
                        <div class="flex flex-col sm:flex-row">
                            <div class="relative w-full sm:w-72 h-44 sm:h-auto flex-shrink-0">
                                <a href="<?= esc($detailUrl) ?>" class="absolute inset-0 z-[5]">
                                    <span class="sr-only">ดูรายละเอียดสนาม</span>
                                </a>

                                <img src="<?= esc($coverUrl) ?>" alt="<?= esc($name) ?>" class="h-full w-full object-cover">

                                <div class="absolute bottom-3 left-3 z-[6] inline-flex items-center gap-1 text-[var(--primary)] text-xs font-semibold px-3 py-1.5 rounded-full bg-white/90 shadow-md backdrop-blur-sm border border-white/60">
                                    <span class="text-sm"><?= esc($typeIcon) ?></span>
                                    <span><?= esc($typeLabel) ?></span>
                                </div>

                                <button type="button"
                                        class="js-fav-toggle absolute top-3 right-3 z-[6] w-10 h-10 rounded-full flex items-center justify-center shadow-md transition-colors <?= $isFav ? 'bg-rose-50 ring-2 ring-rose-200' : 'bg-white/90 hover:bg-white' ?>"
                                        data-stadium-id="<?= $sid ?>"
                                        data-favorited="<?= $isFav ? '1' : '0' ?>"
                                        title="<?= $isFav ? 'ลบออกจากรายการโปรด' : 'เพิ่มในรายการโปรด' ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition <?= $isFav ? 'text-rose-600' : 'text-gray-600' ?>"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex-1 p-5">
                                <h2 class="text-lg font-bold text-gray-900 line-clamp-2">
                                    <a href="<?= esc($detailUrl) ?>" class="hover:underline"><?= esc($name) ?></a>
                                </h2>

                                <div class="flex items-center gap-2 text-sm text-gray-600 mt-2">
                                    <span class="inline-flex items-center gap-1">
                                        ⭐ <strong class="text-gray-900"><?= $cnt > 0 ? number_format($avg, 1) : '0.0' ?></strong>
                                        <span class="text-gray-500">(<?= $cnt ?>)</span>
                                    </span>
                                </div>

                                <div class="mt-4">
                                    <a href="<?= esc($detailUrl) ?>"
                                       class="inline-flex items-center justify-center rounded-xl bg-[var(--primary)] px-4 py-2 text-sm font-semibold text-white hover:bg-[var(--primary)]/90">
                                        ดูรายละเอียดสนาม
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
