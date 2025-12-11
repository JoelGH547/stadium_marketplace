<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?php
  // ข้อมูลที่ส่งมาจาก Controller
/** @var array $cartItems */
/** @var float $subtotal */
/** @var float $serviceFee */
/** @var float $total */

$items = $cartItems ?? [];
?>

<section class="mx-auto max-w-6xl px-4 pt-4 pb-10 lg:px-0">
    <ol class="w-fit mx-auto mb-3 flex items-center gap-2 text-[11px] sm:text-xs">
        <li class="flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-gray-500">
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-gray-200 text-[10px]">1</span>
            <span>เลือกเวลาและบริการ</span>
        </li>
        <li class="flex items-center gap-1 rounded-full bg-[var(--primary)] px-3 py-1 text-white">
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-[10px]">2</span>
            <span>ตะกร้าการจอง</span>
        </li>
        <li class="flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-gray-400">
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-gray-200 text-[10px]">3</span>
            <span>ยืนยันการจอง</span>
        </li>
    </ol>
    <header class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">
                ตะกร้าการจองของคุณ
            </h1>
            <p class="text-xs text-gray-500">
                ตรวจสอบไอเทมและบริการที่คุณเลือกจากสนามกีฬา ก่อนดำเนินการจองขั้นถัดไป
            </p>
        </div>
    </header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1.1fr)]">
        <!-- รายการไอเทมในตะกร้า -->
        <div class="space-y-3">
            <?php if (empty($items)): ?>
                <div
                    class="rounded-2xl border border-dashed border-gray-300 bg-white px-4 py-6 text-center text-sm text-gray-500">
                    ยังไม่มีไอเทมในตะกร้า ลองกลับไปเลือกสนามและบริการที่ต้องการอีกครั้ง
                </div>
            <?php else: ?>
                <div class="rounded-2xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs font-medium text-gray-700">
                            รายการทั้งหมด (<?= count($items) ?>)
                        </span>
                        <?php if ((isset($fieldId) && $fieldId > 0) || (isset($stadiumId) && $stadiumId > 0)): ?>
                             <!-- Link back to stadium details for adding more items -->
                            <a href="<?= site_url('sport/show/' . ($fieldId > 0 ? $fieldId : $stadiumId) . '?restore=1') ?>"
                                class="text-[11px] font-medium text-[var(--primary)] hover:underline">
                                เลือกเพิ่ม
                            </a>
                        <?php endif; ?>
                    </div>

                    <ul class="divide-y divide-gray-100">
                        <?php foreach ($items as $row): ?>
                            <li class="flex items-start gap-3 py-3 text-xs sm:text-sm">
                                <!-- Item Image -->
                                <div
                                    class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-md border border-gray-100 bg-gray-50">
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="<?= esc($row['image']) ?>" alt="<?= esc($row['item_name']) ?>"
                                            class="h-full w-full object-cover">
                                    <?php else: ?>
                                        <div class="flex h-full w-full items-center justify-center text-gray-300">
                                            <i class="fas fa-image text-sm"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Item Info -->
                                <div class="flex-1 flex justify-between gap-3">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 line-clamp-2">
                                            <?= esc($row['item_name']) ?>
                                        </p>
                                        <?php if (!empty($row['stadium_name'])): ?>
                                            <p class="text-[11px] text-gray-500">
                                                สนาม: <?= esc($row['stadium_name']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="mt-1 text-[11px] text-gray-500">
                                            จำนวน: <?= (int) $row['qty'] ?> <?= esc($row['unit'] ?? '') ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">
                                            <?= number_format((float) $row['price'] * (int) $row['qty'], 2) ?>฿
                                        </p>
                                        <p class="text-[11px] text-gray-500">
                                            (<?= number_format((float) $row['price'], 2) ?>฿ /
                                            <?= esc($row['unit'] ?? 'ครั้ง') ?>)
                                        </p>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- สรุปราคา / ดำเนินการต่อ -->
        <aside class="space-y-3">
            <div class="rounded-2xl border border-gray-200 bg-white px-4 py-4 shadow-sm text-sm">
                <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    สรุปยอดการจอง
                </h2>
                <dl class="space-y-2">
                    <div class="flex items-center justify-between">
                        <dt class="text-xs text-gray-500">ยอดรวมบริการและไอเทม</dt>
                        <dd class="text-sm font-semibold text-gray-900">
                            <?= number_format($subtotal, 2) ?>฿
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-xs text-gray-500">ค่าบริการแพลตฟอร์ม (5%)</dt>
                        <dd class="text-sm font-semibold text-gray-900">
                            <?= number_format($serviceFee, 2) ?>฿
                        </dd>
                    </div>
                </dl>
                <div class="mt-3 border-t border-gray-100 pt-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-700">ยอดชำระทั้งหมด</span>
                        <span class="text-base font-semibold text-gray-900">
                            <?= number_format($total, 2) ?>฿
                        </span>
                    </div>
                </div>
            </div>

            <a href="<?= site_url('sport/checkout') ?>" class="inline-flex w-full items-center justify-center rounded-xl bg-[var(--primary)]
          px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-teal-600 transition text-center">
                ดำเนินการจองขั้นถัดไป
            </a>

            <p class="text-[11px] text-gray-500">
                ปุ่มนี้ยังเป็นเพียงตัวอย่างเบื้องต้นสำหรับหน้า Cart เท่านั้น
                เมื่อเชื่อมต่อระบบจองจริงแล้ว จะพาไปสู่หน้ากรอกข้อมูลผู้จอง / ยืนยันการชำระเงินต่อไป
            </p>
        </aside>
    </div>
</section>

<?= $this->endSection() ?>