<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?php
  // เตรียมข้อมูล mock เผื่อยังไม่มีการเชื่อม Controller จริง
/** @var array|null $cartItems */
$items = $cartItems ?? [
  [
    'stadium_name' => 'Sport Arena A',
    'item_name'    => 'ไม้แบด Yonex Pro',
    'unit'         => 'ชม.',
    'qty'          => 1,
    'price'        => 50,
  ],
  [
    'stadium_name' => 'Sport Arena A',
    'item_name'    => 'นวดนักกีฬา 60 นาที',
    'unit'         => 'ครั้ง',
    'qty'          => 1,
    'price'        => 300,
  ],
];

$subtotal = $subtotal ?? array_reduce($items, static function ($carry, $row) {
  return $carry + (float) $row['price'] * (int) $row['qty'];
}, 0.0);

$serviceFee = $serviceFee ?? ($subtotal * 0.05);
$total      = $total ?? ($subtotal + $serviceFee);
?>

<section class="mx-auto max-w-6xl px-4 pt-4 pb-10 lg:px-0">
  <header class="mb-4 flex items-center justify-between gap-3">
    <div>
      <h1 class="text-lg font-semibold text-gray-900">
        ตะกร้าการจองของคุณ
      </h1>
      <p class="text-xs text-gray-500">
        ตรวจสอบไอเทมและบริการที่คุณเลือกจากสนามกีฬา ก่อนดำเนินการจองขั้นถัดไป
      </p>
    </div>
    <a href="<?= base_url('customer/home') ?>"
      class="hidden text-xs font-medium text-[var(--primary)] hover:underline sm:inline">
      ‹ เลือกสนามเพิ่ม
    </a>
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
            <a href="<?= base_url('customer/home') ?>"
              class="text-[11px] font-medium text-[var(--primary)] hover:underline">
              เลือกเพิ่ม
            </a>
          </div>

          <ul class="divide-y divide-gray-100">
            <?php foreach ($items as $row): ?>
              <li class="flex items-start justify-between gap-3 py-3 text-xs sm:text-sm">
                <div class="flex-1">
                  <p class="font-semibold text-gray-900">
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
                    (<?= number_format((float) $row['price'], 2) ?>฿ / <?= esc($row['unit'] ?? 'ครั้ง') ?>)
                  </p>
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

      <a href="<?= route_to('customer.checkout') ?>" class="inline-flex w-full items-center justify-center rounded-xl bg-[var(--primary)]
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