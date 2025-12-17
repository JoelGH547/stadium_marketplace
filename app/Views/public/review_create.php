<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">เขียนรีวิว</h1>
        <p class="text-sm text-gray-600 mt-1">ให้คะแนนและเขียนความคิดเห็นหลังใช้งานจริง</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="text-sm text-gray-600">Booking ID</div>
            <div class="font-semibold text-gray-900">#<?= esc($booking['id'] ?? '') ?></div>
            <?php if (!empty($booking['booking_start_time']) && !empty($booking['booking_end_time'])): ?>
                <div class="mt-1 text-xs text-gray-500">
                    <?= esc($booking['booking_start_time']) ?> → <?= esc($booking['booking_end_time']) ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="<?= site_url('sport/reviews/store') ?>" method="post" class="p-5 space-y-5">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= esc($booking['id'] ?? '') ?>">

            <div>
                <label class="block text-sm font-medium text-gray-800 mb-2">ให้คะแนนดาว</label>
                <div class="flex items-center gap-2">
                    <select name="rating"
                        class="w-40 rounded-lg border-gray-300 focus:border-[var(--primary)] focus:ring-[var(--primary)]">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= old('rating') == (string)$i ? 'selected' : '' ?>>
                                <?= $i ?> ดาว
                            </option>
                        <?php endfor; ?>
                    </select>
                    <span class="text-xs text-gray-500">1 = แย่, 5 = ดีมาก</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-800 mb-2">ความคิดเห็น (ไม่บังคับ)</label>
                <textarea name="comment" rows="5"
                    class="w-full rounded-lg border-gray-300 focus:border-[var(--primary)] focus:ring-[var(--primary)]"
                    placeholder="เล่าประสบการณ์การใช้งาน..."><?= esc(old('comment') ?? '') ?></textarea>
                <div class="mt-1 text-xs text-gray-500">ไม่เกิน 2000 ตัวอักษร</div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="<?= site_url('sport/booking_history') ?>"
                    class="inline-flex items-center rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    ย้อนกลับ
                </a>
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-[var(--primary)] px-5 py-2 text-sm font-semibold text-white hover:opacity-90">
                    ส่งรีวิว
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
