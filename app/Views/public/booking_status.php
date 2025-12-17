<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?php helper('booking_format'); ?>
<div class="container mx-auto px-4 py-8 max-w-7xl">

    <div class="flex items-center gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">รายการจองของฉัน</h1>
    </div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-sm">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">

        <?php if (empty($bookings)): ?>
        <div class="p-8 text-center text-gray-500">
            <p>คุณยังไม่มีรายการจอง</p>
            <a href="<?= site_url('sport') ?>" class="inline-block mt-4 text-blue-600 hover:underline">
                จองสนามเลย
            </a>
        </div>
        <?php else: ?>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-900 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold">สนาม</th>
                        <th class="px-6 py-4 font-semibold">เจ้าของสนาม</th>
                        <th class="px-6 py-4 font-semibold">วันและเวลา</th>
                        <th class="px-6 py-4 font-semibold text-right">ยอดชำระ</th>
                        <th class="px-6 py-4 font-semibold text-center">สถานะ</th>
                        <th class="px-6 py-4 font-semibold text-center">หลักฐาน</th>
                        <th class="px-6 py-4 font-semibold text-center">รีวิว</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($bookings as $b): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 align-top">
                            <div class="font-medium text-gray-900"><?= esc($b['stadium_name']) ?></div>
                            <div class="text-xs text-gray-500"><?= esc($b['field_name']) ?></div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            <?= esc($b['vendor_name'] ?? '-') ?>
                        </td>
                        <td class="px-6 py-4 align-top whitespace-nowrap">
                            <?php
                                $range = booking_format_range($b['booking_start_time'] ?? null, $b['booking_end_time'] ?? null);
                            ?>

                            <?php if ($range['type'] === 'daily'): ?>
                                <div>
                                    <span class="block text-gray-900"><?= esc($range['startDate']) ?></span>
                                    <?php if ((int) $range['days'] > 1): ?>
                                        <span class="text-xs text-gray-500">ถึง <?= esc($range['endDate']) ?> (<?= (int) $range['days'] ?> วัน)</span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-500">(1 วัน)</span>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($range['type'] === 'hourly'): ?>
                                <div>
                                    <span class="block text-gray-900"><?= esc($range['startDate']) ?></span>
                                    <span class="text-xs text-gray-500"><?= esc($range['startTime']) ?> - <?= esc($range['endTime']) ?> น.</span>
                                </div>
                            <?php else: ?>
                                <div>
                                    <span class="block text-gray-900"><?= esc($range['label']) ?></span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 align-top text-right font-medium text-gray-900">
                            <?= number_format($b['total_price'], 2) ?> ฿
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <?php
                                        $status      = $b['status'];
                                        $statusLabel = 'รอตรวจสอบ';
                                        $statusColor = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                        
                                        if ($status === 'approved' || $status === 'paid' || $status === 'confirmed') { // paid might be from payment gateway
                                            $statusLabel = 'อนุมัติแล้ว';
                                            $statusColor = 'bg-green-100 text-green-800 border-green-200';
                                        } elseif ($status === 'cancelled' || $status === 'rejected') {
                                            $statusLabel = 'ยกเลิก';
                                            $statusColor = 'bg-red-100 text-red-800 border-red-200';
                                        }
                                    ?>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusColor ?>">
                                <?= $statusLabel ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <?php if (!empty($b['slip_image'])): ?>
                                <?php
                                $slipPath = FCPATH . ltrim($b['slip_image'], '/');
                                if (is_file($slipPath)) :
                                ?>
                                    <a href="<?= base_url($b['slip_image']) ?>" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 text-xs underline">
                                        ดูสลิป
                                    </a>
                                <?php else: ?>
                                    <button onclick="alert('สลิปหาย กรุณาแจ้งแอดมินหรือเจ้าของสนาม')"
                                        class="text-red-500 hover:text-red-700 text-xs underline focus:outline-none">
                                        สลิปหาย
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 align-top text-center">
                            <?php if (!empty($b['can_review'])): ?>
                                <a href="<?= site_url('sport/reviews/create/' . (int) $b['id']) ?>"
                                   class="inline-flex items-center justify-center rounded-lg bg-[var(--primary)] px-3 py-2 text-xs font-semibold text-white hover:opacity-90">
                                    เขียนรีวิว
                                </a>
                            <?php elseif (!empty($b['reviewed'])): ?>
                                <span class="text-emerald-700 text-xs font-semibold">รีวิวแล้ว</span>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>