<?= $this->extend('layouts/customer') ?>

<?= $this->section('extra-css') ?>
<style>
    .stadium-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }
    .stadium-card {
        border-radius: 12px;
        overflow: hidden;
    }
    .stadium-card-image {
        width: 100%;
        height: 200px;
        background-color: #f8f9fa;
    }
    .category {
        display: inline-block;
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 font-weight-bold text-dark"><?= esc($title) ?></h1>
            <p class="text-muted">ยินดีต้อนรับ! เลือกสนามที่คุณต้องการจอง:</p>
        </div>
    </div>

    <div class="stadium-grid mb-5">
        <?php if (! empty($stadiums) && is_array($stadiums)): ?>
            <?php foreach ($stadiums as $stadium): ?>
                <div class="stadium-card shadow-sm border-0 h-100 bg-white">
                    <?php 
                        $images = json_decode($stadium['outside_images'] ?? '[]', true);
                        $cover = !empty($images[0]) ? $images[0] : null;
                    ?>
                    <div class="stadium-card-image position-relative">
                        <?php if($cover): ?>
                            <img src="<?= base_url('assets/uploads/stadiums/'.$cover) ?>" 
                                 class="w-100 h-100 object-fit-cover">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                <i class="fas fa-image fa-3x text-muted opacity-25"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-3">
                        <span class="category mb-2"><?= esc($stadium['category_name'] ?? 'General') ?></span>
                        
                        <h3 class="h5 fw-bold mb-2 text-dark"><?= esc($stadium['name']) ?></h3>
                        <p class="small text-muted mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= esc($stadium['description']) ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="price">
                                <span class="h5 mb-0 text-primary font-weight-bold">฿<?= number_format($stadium['price'] ?? 0, 0) ?></span> 
                                <small class="text-muted">/ชม.</small>
                            </div>
                        </div>
                        
                        <a href="<?= base_url('customer/booking/stadium/' . $stadium['id']) ?>" 
                           class="btn btn-primary btn-block py-2 fw-bold shadow-sm rounded-pill">
                            ดูรายละเอียด และ จอง
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <p class="lead text-muted">ขออภัย, ยังไม่มีสนามกีฬาที่เปิดให้จองในขณะนี้</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Booking History Section -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-history mr-2 text-primary"></i>ประวัติการจองล่าสุด</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-muted small text-uppercase letter-spacing-1">
                        <tr>
                            <th class="border-0 pl-4">สนาม/พื้นที่</th>
                            <th class="border-0 text-center">วันที่เข้าใช้งาน</th>
                            <th class="border-0 text-center">เวลา</th>
                            <th class="border-0 text-center">ทำรายการเมื่อ</th>
                            <th class="border-0 text-right pr-4">ยอดรวม / สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($myBookings)): ?>
                            <?php foreach ($myBookings as $booking): ?>
                                <tr onclick="window.location='<?= base_url('customer/payment/checkout/' . $booking['id']) ?>'" style="cursor: pointer;">
                                    <td class="pl-4 py-3">
                                        <div class="fw-bold text-dark mb-0"><?= esc($booking['stadium_name']) ?></div>
                                        <div class="small text-muted"><?= esc($booking['field_name']) ?></div>
                                    </td>
                                    <td class="py-3 align-middle text-center">
                                        <div class="small font-weight-bold"><?= date('d M Y', strtotime($booking['booking_start_time'])) ?></div>
                                    </td>
                                    <td class="py-3 align-middle text-center">
                                        <span class="badge badge-light border rounded-pill px-3">
                                            <?= date('H:i', strtotime($booking['booking_start_time'])) ?> - <?= date('H:i', strtotime($booking['booking_end_time'])) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 align-middle text-center">
                                        <div class="small text-muted"><?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></div>
                                    </td>
                                    <td class="py-3 align-middle text-right pr-4">
                                        <div class="fw-bold text-primary mb-1">฿<?= number_format($booking['total_price']) ?></div>
                                        <?php 
                                            $statusClass = 'secondary';
                                            $statusText = $booking['status'];
                                            if ($booking['status'] == 'pending') { $statusClass = 'warning'; $statusText = 'รอชำระเงิน'; }
                                            elseif ($booking['status'] == 'paid') { $statusClass = 'success'; $statusText = 'จ่ายแล้ว'; }
                                            elseif ($booking['status'] == 'confirmed') { $statusClass = 'info'; $statusText = 'ยืนยันแล้ว'; }
                                            elseif ($booking['status'] == 'cancelled') { $statusClass = 'danger'; $statusText = 'ยกเลิก'; }
                                        ?>
                                        <span class="badge badge-<?= $statusClass ?> rounded-pill px-2" style="font-size: 0.7rem; font-weight: normal;"><?= $statusText ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted small">คุณยังไม่มีประวัติการจอง</div>
                                    <a href="#stadium-grid" class="btn btn-link btn-sm text-primary">เริ่มจองสนามแรกของคุณ</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($myBookings) && count($myBookings) >= 5): ?>
                <div class="p-3 text-center border-top">
                    <a href="#" class="small fw-bold text-primary">ดูประวัติการจองทั้งหมด</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
