<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8 max-w-7xl">
    
    <div class="flex items-center gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">รายการจองของฉัน</h1>
    </div>

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
                                        $start = strtotime($b['booking_start_time']);
                                        $end   = strtotime($b['booking_end_time']);
                                        
                                        // ตรวจสอบว่าเป็นรายวันหรือรายชั่วโมง
                                        // ถ้ารายวัน production code อาจดูจาก field type หรือระยะเวลา
                                        // แต่เบื้องต้นโชว์ start - end
                                        
                                        $dateStart = date('d/m/Y', $start);
                                        $dateEnd   = date('d/m/Y', $end);
                                        
                                        $timeStart = date('H:i', $start);
                                        $timeEnd   = date('H:i', $end);
                                    ?>
                                    
                                    <?php if ($dateStart === $dateEnd): ?>
                                        <!-- วันเดียวกัน (รายชั่วโมง) -->
                                        <div>
                                            <span class="block text-gray-900"><?= $dateStart ?></span>
                                            <span class="text-xs text-gray-500"><?= $timeStart ?> - <?= $timeEnd ?> น.</span>
                                        </div>
                                    <?php else: ?>
                                        <!-- ข้ามวัน (รายวัน) -->
                                        <div>
                                            <span class="block text-gray-900"><?= $dateStart ?></span>
                                            <span class="text-xs text-gray-500">ถึง <?= $dateEnd ?></span>
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
                                        
                                        if ($status === 'approved' || $status === 'paid') { // paid might be from payment gateway
                                            $statusLabel = 'อนุมัติแล้ว';
                                            $statusColor = 'bg-green-100 text-green-800 border-green-200';
                                        } elseif ($status === 'cancelled' || $status === 'rejected') {
                                            $statusLabel = 'ยกเลิก';
                                            $statusColor = 'bg-red-100 text-red-800 border-red-200';
                                        }
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $statusColor ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 align-top text-center">
                                    <?php if (!empty($b['slip_image'])): ?>
                                        <a href="<?= base_url($b['slip_image']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs underline">
                                            ดูสลิป
                                        </a>
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
