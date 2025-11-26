<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?php
// ใช้ mock data หาก Controller ยังไม่ส่งค่ามา
$booking  = $booking  ?? [
  'stadium_name' => 'Sport Arena A',
  'date_label'   => 'วันเสาร์ที่ 10 พฤษภาคม 2568',
  'time_label'   => '18:00 - 20:00 น.',
];

$cartItems = $cartItems ?? [
  [
    'item_name' => 'ไม้แบด Yonex Pro',
    'unit'      => 'ชม.',
    'qty'       => 1,
    'price'     => 50,
  ],
  [
    'item_name' => 'นวดนักกีฬา 60 นาที',
    'unit'      => 'ครั้ง',
    'qty'       => 1,
    'price'     => 300,
  ],
];

$subtotal = $subtotal ?? array_reduce($cartItems, static function ($carry, $row) {
  return $carry + (float) $row['price'] * (int) $row['qty'];
}, 0.0);

$serviceFee = $serviceFee ?? ($subtotal * 0.05);
$total      = $total ?? ($subtotal + $serviceFee);
?>

<section class="mx-auto max-w-5xl px-4 pt-4 pb-10 lg:px-0">
    <ol class="mb-3 flex items-center gap-2 text-[11px] sm:text-xs">
        <li class="flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-gray-500">
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-gray-200 text-[10px]">1</span>
            <span>เลือกเวลาและบริการ</span>
        </li>
        <li class="flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-gray-500">
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-gray-200 text-[10px]">2</span>
            <span>ตะกร้าการจอง</span>
        </li>
        <li class="flex items-center gap-1 rounded-full bg-[var(--primary)] px-3 py-1 text-white">
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-[10px]">3</span>
            <span>ยืนยันการจอง</span>
        </li>
    </ol>
    <header class="mb-4">
        <h1 class="text-lg font-semibold text-gray-900">
            ยืนยันการจอง
        </h1>
        <p class="text-xs text-gray-500">
            กรอกข้อมูลผู้จองให้ครบถ้วน และตรวจสอบรายละเอียดก่อนยืนยันการจอง
        </p>
    </header>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,1.2fr)]">
        <!-- ฟอร์มข้อมูลผู้จอง -->
        <div>
            <form action="#" method="post"
                class="space-y-4 rounded-2xl border border-gray-200 bg-white px-4 py-4 shadow-sm">
                <div>
                    <label for="customerName" class="block text-xs font-medium text-gray-700">
                        ชื่อ-นามสกุล ผู้จอง
                    </label>
                    <input type="text" id="customerName" name="customer_name"
                        class="mt-1 block w-full rounded-xl border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]"
                        placeholder="เช่น กิตติภพ ใจดี">
                </div>

                <div>
                    <label for="customerPhone" class="block text-xs font-medium text-gray-700">
                        เบอร์โทรศัพท์ที่ติดต่อได้
                    </label>
                    <input type="tel" id="customerPhone" name="customer_phone"
                        class="mt-1 block w-full rounded-xl border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]"
                        placeholder="เช่น 08x-xxx-xxxx">
                </div>

                <div>
                    <label for="customerEmail" class="block text-xs font-medium text-gray-700">
                        อีเมล (ถ้ามี)
                    </label>
                    <input type="email" id="customerEmail" name="customer_email"
                        class="mt-1 block w-full rounded-xl border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]"
                        placeholder="example@email.com">
                </div>

                <div>
                    <label for="customerNote" class="block text-xs font-medium text-gray-700">
                        หมายเหตุเพิ่มเติม (ถ้ามี)
                    </label>
                    <textarea id="customerNote" name="customer_note" rows="3"
                        class="mt-1 block w-full rounded-xl border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)]"
                        placeholder="เช่น ขอเตรียมลูกแบดเพิ่ม หรือแจ้งจำนวนผู้เล่นโดยประมาณ"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-[var(--primary)]
                         px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-teal-600 transition">
                        ยืนยันการจอง (ตัวอย่าง)
                    </button>
                    <p class="mt-2 text-[11px] text-gray-500">
                        ปุ่มนี้ยังเป็นตัวอย่างสำหรับหน้า Checkout เท่านั้น
                        เมื่อเชื่อมระบบจองจริงแล้ว ข้อมูลจะถูกบันทึกและสร้างเลขการจองให้โดยอัตโนมัติ
                    </p>
                </div>
            </form>
        </div>

        <!-- สรุปรายละเอียดการจอง -->
        <aside class="space-y-3">
            <div class="rounded-2xl border border-gray-200 bg-white px-4 py-4 shadow-sm text-sm">
                <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    รายละเอียดการจอง
                </h2>
                <dl class="space-y-1 text-xs text-gray-700">
                    <div class="flex gap-2">
                        <dt class="w-20 text-gray-500">สนาม</dt>
                        <dd class="flex-1 font-medium text-gray-900">
                            <?= esc($booking['stadium_name']) ?>
                        </dd>
                    </div>
                    <div class="flex gap-2">
                        <dt class="w-20 text-gray-500">วันเวลา</dt>
                        <dd class="flex-1">
                            <div><?= esc($booking['date_label']) ?></div>
                            <div><?= esc($booking['time_label']) ?></div>
                        </dd>
                    </div>
                </dl>

                <div class="mt-3 border-t border-gray-100 pt-3">
                    <p class="mb-2 text-xs font-semibold text-gray-700">
                        รายการบริการและไอเทม
                    </p>
                    <ul class="max-h-40 space-y-1 overflow-auto pr-1 text-xs text-gray-700">
                        <?php foreach ($cartItems as $row): ?>
                        <li class="flex items-center justify-between gap-2">
                            <span class="flex-1">
                                <?= esc($row['item_name']) ?>
                                <span class="text-[11px] text-gray-500">
                                    x<?= (int) $row['qty'] ?> <?= esc($row['unit'] ?? '') ?>
                                </span>
                            </span>
                            <span class="text-right">
                                <?= number_format((float) $row['price'] * (int) $row['qty'], 2) ?>฿
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <dl class="mt-3 space-y-1">
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
                        <div class="flex items-center justify-between border-t border-gray-100 pt-2">
                            <dt class="text-xs font-semibold text-gray-700">ยอดชำระทั้งหมด</dt>
                            <dd class="text-base font-semibold text-gray-900">
                                <?= number_format($total, 2) ?>฿
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </aside>
    </div>
</section>

<?= $this->endSection() ?>