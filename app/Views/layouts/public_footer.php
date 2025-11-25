<?php $siteName = $siteName ?? 'Stadium Marketplace'; ?>
<footer class="border-t border-[var(--line)] bg-[var(--panel)] text-[var(--primary)] mt-12">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 grid gap-6 sm:grid-cols-3">
    <div>
      <div class="font-semibold"><?= esc($siteName) ?></div>
      <p class="text-sm mt-2">เปิดบริการทุกวัน 06:00–22:00 น.</p>
    </div>
    <div>
      <div class="font-semibold">ติดต่อ</div>
      <p class="text-sm mt-2">โทร 02-123-4567<br>Line: @stadium_marketplace</p>
    </div>
    <div>
      <div class="font-semibold">ที่อยู่</div>
      <p class="text-sm mt-2">123 ถนนสปอร์ต แขวง/เขต เมืองของคุณ จังหวัดของคุณ</p>
    </div>
  </div>

  <div class="border-t border-[var(--line)] py-4 text-center text-sm">
    © <?= date('Y') ?> <?= esc($siteName) ?>. All rights reserved.
  </div>
</footer>