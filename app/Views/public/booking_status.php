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
                                            <button type="button" 
                                                onclick='openReviewModal(<?= json_encode([
                                                    "id" => $b["id"],
                                                    "stadium_name" => $b["stadium_name"],
                                                    "field_name" => $b["field_name"],
                                                    "booking_date" => $b["booking_start_time"] // Using start time for "Booked on"
                                                ]) ?>)'
                                                class="inline-flex items-center justify-center rounded-lg bg-[var(--primary)] px-3 py-2 text-xs font-semibold text-white hover:opacity-90">
                                                เขียนรีวิว
                                            </button>
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

    <!-- Review Modal -->
    <div id="reviewModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeReviewModal()"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="<?= site_url('sport/reviews/store') ?>" method="post" id="reviewForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="booking_id" id="modalBookingId">
                    <input type="hidden" name="rating" id="modalRating" value="5">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-teal-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    ให้คะแนนการใช้บริการ
                                </h3>
                                <div class="mt-2">
                                    <div class="text-sm text-gray-500 mb-4 bg-gray-50 p-3 rounded-md">
                                        <p class="font-semibold text-gray-700" id="modalStadiumName">-</p>
                                        <p id="modalFieldName">-</p>
                                        <p class="text-xs mt-1" id="modalBookingDate">-</p>
                                    </div>
                                    
                                    <!-- Star Rating -->
                                    <div class="mb-4 text-center sm:text-left">
                                        <p class="text-sm font-medium text-gray-700 mb-2">ความพึงพอใจ</p>
                                        <div class="flex items-center justify-center sm:justify-start gap-1" id="starContainer">
                                            <?php for($i=1; $i<=5; $i++): ?>
                                                <button type="button" class="star-btn focus:outline-none transition-colors duration-150" data-value="<?= $i ?>">
                                                    <svg class="w-8 h-8 text-gray-300 hover:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </button>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <!-- Comment -->
                                    <div>
                                        <label for="modalComment" class="block text-sm font-medium text-gray-700 mb-2">ความคิดเห็นเพิ่มเติม</label>
                                        <textarea id="modalComment" name="comment" rows="3" class="shadow-sm focus:ring-[var(--primary)] focus:border-[var(--primary)] mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="เล่าประสบการณ์ของคุณ..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[var(--primary)] text-base font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--primary)] sm:ml-3 sm:w-auto sm:text-sm">
                            ส่งรีวิว
                        </button>
                        <button type="button" onclick="closeReviewModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            ยกเลิก
                        </button>
                    </div>
                </form>
            </div>
        </div>
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