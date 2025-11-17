<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>
<p><a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">&laquo; Back to Dashboard</a></p>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>ลูกค้า (Customer)</th>
            <th>สนาม (Stadium)</th>
            <th>วันที่จอง</th>
            <th>เวลา</th>
            <th>สถานะ (Status)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (! empty($bookings) && is_array($bookings)): ?>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><strong><?= esc($booking['id']) ?></strong></td>
                    <td><?= esc($booking['customer_name'] ?? 'N/A') ?></td>
                    <td><?= esc($booking['stadium_name'] ?? 'N/A') ?></td>
                    <td><?= esc($booking['booking_date']) ?></td>
                    <td><?= esc($booking['start_time']) ?></td>
                    <td>
                        <?php if ($booking['status'] == 'confirmed'): ?>
                            <span class="badge badge-success"><?= esc($booking['status']) ?></span>
                        <?php elseif ($booking['status'] == 'pending'): ?>
                            <span class="badge badge-warning"><?= esc($booking['status']) ?></span>
                        <?php else: ?>
                            <span class="badge badge-secondary"><?= esc($booking['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($booking['status'] != 'cancelled'): ?>
                            
                            <a href="<?= base_url('admin/bookings/cancel/' . $booking['id']) ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('คุณแน่ใจหรือไม่ ว่าต้องการ ยกเลิก (Cancel) การจองนี้?')">
                                Cancel Booking
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">ไม่พบข้อมูลการจองในส่วนนี้</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>