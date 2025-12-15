<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="fixed inset-0 z-[9999] flex items-center justify-center">
  <div class="absolute inset-0 bg-black/60"></div>

  <div class="relative w-[92vw] max-w-md rounded-2xl bg-white p-6 shadow-xl">
    <div class="flex items-start gap-3">
      <div class="mt-1 h-10 w-10 shrink-0 rounded-full bg-emerald-100 flex items-center justify-center">
        <svg class="h-6 w-6 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 0 1.42l-7.5 7.5a1 1 0 0 1-1.42 0l-3.5-3.5a1 1 0 1 1 1.42-1.42l2.79 2.79 6.79-6.79a1 1 0 0 1 1.42 0Z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="flex-1">
        <h2 class="text-lg font-semibold text-gray-900">จองสำเร็จแล้ว</h2>
        <p class="mt-1 text-sm text-gray-600">ระบบบันทึกการจองเรียบร้อย กำลังพากลับไปหน้าหลัก…</p>
      </div>
    </div>

    <div class="mt-5 flex items-center justify-end gap-2">
      <a href="<?= esc($redirectUrl ?? site_url('sport')) ?>"
         class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
        กลับหน้าหลัก
      </a>
    </div>
  </div>
</div>

<script>
  setTimeout(function () {
    window.location.href = <?= json_encode($redirectUrl ?? site_url('sport')) ?>;
  }, 1200);
</script>

<?= $this->endSection() ?>
