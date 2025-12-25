<?= $this->extend('owner/layout/toptitle') ?>

<?= $this->section('content') ?>
<div class="card shadow-lg border-0 rounded-4">
    <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="fas fa-calendar-check me-2 text-primary"></i> รายการจองสนาม
        </h5>
        <div>
            <a href="<?= base_url('owner/bookings') ?>" class="btn btn-outline-secondary btn-sm me-1 <?= !request()->getGet('status') ? 'active' : '' ?>">ทั้งหมด</a>
            <a href="<?= base_url('owner/bookings?status=pending') ?>" class="btn btn-outline-warning btn-sm me-1 <?= request()->getGet('status') == 'pending' ? 'active' : '' ?>">รอตรวจสอบ</a>
            <a href="<?= base_url('owner/bookings?status=paid') ?>" class="btn btn-outline-success btn-sm me-1 <?= request()->getGet('status') == 'paid' ? 'active' : '' ?>">อนุมัติแล้ว/จ่ายเงินแล้ว</a>
            <a href="<?= base_url('owner/bookings?status=rejected') ?>" class="btn btn-outline-danger btn-sm <?= request()->getGet('status') == 'rejected' ? 'active' : '' ?>">ปฏิเสธ/ยกเลิก</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>ลูกค้า</th>
                        <th>สนาม</th>
                        <th>วัน/เวลา</th>
                        <th>ราคา</th>
                        <th>สถานะ</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">ไม่พบรายการจอง</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td class="ps-4">#<?= $booking['id'] ?></td>
                            <td>
                                <div class="fw-bold"><?= esc($booking['customer_name'] ?? 'ไม่ระบุ') ?></div>
                                <small class="text-muted"><?= esc($booking['customer_phone'] ?? '') ?></small>
                            </td>
                            <td><?= esc($booking['stadium_name'] ?? 'ไม่ระบุ') ?></td>
                            <td>
                                <div><?= date('d/m/Y', strtotime($booking['booking_start_time'])) ?></div>
                                <small class="text-muted">
                                    <?= date('H:i', strtotime($booking['booking_start_time'])) ?> - 
                                    <?= date('H:i', strtotime($booking['booking_end_time'])) ?>
                                </small>
                            </td>
                            <td class="fw-bold text-success">฿<?= number_format($booking['total_price'], 2) ?></td>
                            <td>
                                <?php
                                    $statusBadge = 'secondary';
                                    $statusText = $booking['status'];
                                    
                                    if ($booking['status'] == 'approved' || $booking['status'] == 'paid') {
                                        $statusBadge = 'success';
                                        $statusText = 'อนุมัติแล้ว';
                                    }
                                    if ($booking['status'] == 'pending') {
                                        $statusBadge = 'warning text-dark';
                                        $statusText = 'รอตรวจสอบ';
                                    }
                                    if ($booking['status'] == 'rejected') {
                                        $statusBadge = 'danger';
                                        $statusText = 'ปฏิเสธ';
                                    }
                                    if ($booking['status'] == 'cancelled') {
                                        $statusBadge = 'danger';
                                        $statusText = 'ยกเลิก';
                                    }
                                ?>
                                <span class="badge bg-<?= $statusBadge ?>"><?= $statusText ?></span>
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-info text-white" onclick="viewBooking(<?= $booking['id'] ?>)" title="ดูรายละเอียด">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($booking['status'] == 'pending'): ?>
                                    <form action="<?= base_url('owner/bookings/approve/' . $booking['id']) ?>" method="post" class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('ยืนยันการอนุมัติรายการนี้?')" title="อนุมัติ">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="<?= base_url('owner/bookings/reject/' . $booking['id']) ?>" method="post" class="d-inline">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการปฏิเสธรายการนี้?')" title="ปฏิเสธ">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">รายละเอียดการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingModalBody">
                <div class="text-center"><i class="fas fa-spinner fa-spin"></i> กำลังโหลด...</div>
            </div>
        </div>
    </div>
</div>

<script>
function viewBooking(id) {
    var modal = new bootstrap.Modal(document.getElementById('bookingModal'));
    modal.show();
    
    fetch('<?= base_url('owner/bookings/detail/') ?>' + id)
        .then(response => response.json())
        .then(data => {
            let slipHtml = '';
            if (data.slip_image) {
                slipHtml = `<div class="mt-3">
                    <strong>รูปหลักฐานการโอน:</strong><br>
                    <img src="<?= base_url() ?>${data.slip_image}" class="img-fluid rounded border mt-1" alt="Payment Slip">
                </div>`;
            }

            let statusText = data.status;
            if(data.status == 'approved' || data.status == 'paid') statusText = '<span class="text-success">อนุมัติแล้ว</span>';
            if(data.status == 'pending') statusText = '<span class="text-warning">รอตรวจสอบ</span>';
            if(data.status == 'rejected') statusText = '<span class="text-danger">ปฏิเสธ</span>';
            
            let html = `
                <p><strong>ID:</strong> #${data.id}</p>
                <p><strong>สถานะ:</strong> ${statusText}</p>
                <p><strong>วันเวลาที่จอง:</strong> ${data.booking_start_time} - ${data.booking_end_time}</p>
                <p><strong>ราคารวม:</strong> ฿${data.total_price}</p>
                ${slipHtml}
            `;
            document.getElementById('bookingModalBody').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('bookingModalBody').innerHTML = 'เกิดข้อผิดพลาดในการโหลดข้อมูล';
        });
}
</script>

<?= $this->endSection() ?>
