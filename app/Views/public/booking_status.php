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

    <!-- Tabs Header -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('pending')" id="tab-pending"
                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium border-[var(--primary)] text-[var(--primary)]">
                รออนุมัติ (<?= count($pending) ?>)
            </button>
            <button onclick="switchTab('confirmed')" id="tab-confirmed"
                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700">
                ยืนยันแล้ว (<?= count($confirmed) ?>)
            </button>
            <button onclick="switchTab('cancelled')" id="tab-cancelled"
                class="whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700">
                ยกเลิก (<?= count($cancelled) ?>)
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div id="content-pending" class="block">
        <?php if (empty($pending)): ?>
            <div class="p-8 text-center text-gray-500 bg-white rounded-xl border border-gray-200">
                <p>ไม่มีรายการที่รออนุมัติ</p>
            </div>
        <?php else: ?>
            <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($pending as $b): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-medium text-gray-900"><?= esc($b['stadium_name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= esc($b['field_name']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 align-top"><?= esc($b['vendor_name'] ?? '-') ?></td>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-yellow-100 text-yellow-800 border-yellow-200">
                                            รอตรวจสอบ
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
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div id="content-confirmed" class="hidden">
        <?php if (empty($confirmed)): ?>
            <div class="p-8 text-center text-gray-500 bg-white rounded-xl border border-gray-200">
                <p>ไม่มีรายการที่ยืนยันแล้ว</p>
            </div>
        <?php else: ?>
            <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
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
                            <?php foreach ($confirmed as $b): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-medium text-gray-900"><?= esc($b['stadium_name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= esc($b['field_name']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 align-top"><?= esc($b['vendor_name'] ?? '-') ?></td>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-green-100 text-green-800 border-green-200">
                                            อนุมัติแล้ว
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
            </div>
        <?php endif; ?>
    </div>

    <div id="content-cancelled" class="hidden">
        <?php if (empty($cancelled)): ?>
            <div class="p-8 text-center text-gray-500 bg-white rounded-xl border border-gray-200">
                <p>ไม่มีรายการที่ยกเลิก</p>
            </div>
        <?php else: ?>
            <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($cancelled as $b): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-medium text-gray-900"><?= esc($b['stadium_name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= esc($b['field_name']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 align-top"><?= esc($b['vendor_name'] ?? '-') ?></td>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-red-100 text-red-800 border-red-200">
                                            ยกเลิก
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
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="<?= base_url('assets/booking_status.js') ?>"></script>
<?= $this->endSection() ?>