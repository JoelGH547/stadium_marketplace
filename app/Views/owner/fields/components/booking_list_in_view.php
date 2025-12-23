<?php
// app/Views/owner/fields/components/booking_list_in_view.php
?>
<div class="card shadow-lg border-0 rounded-4 mt-4 mb-4">
    <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="fas fa-list-alt me-2 text-primary"></i> รายการจองสำหรับสนามนี้
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>ลูกค้า</th>
                        <th>สนามย่อย</th>
                        <th>วัน/เวลา</th>
                        <th>ราคา</th>
                        <th>สถานะ</th>
                        <th class="text-end pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">ยังไม่มีรายการจองสำหรับสนามนี้</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td class="ps-4">#<?= $booking['id'] ?></td>
                            <td>
                                <div class="fw-bold"><?= esc($booking['customer_name'] ?? 'ไม่ระบุ') ?></div>
                                <small class="text-muted"><?= esc($booking['customer_phone'] ?? '') ?></small>
                            </td>
                            <td>
                                <div class="fw-bold"><?= esc($booking['subfield_name'] ?? '-') ?></div>
                                <!-- <small class="text-muted">ID: <?= esc($booking['subfield_id'] ?? '') ?></small> -->
                            </td>
                            <td>
                                <?php 
                                    $fieldId = $booking['field_id'] ?? null;
                                    $fieldName = esc($booking['subfield_name'] ?? 'สนามย่อย'); 
                                    $bookingDate = date('Y-m-d', strtotime($booking['booking_start_time']));
                                    $showDate = date('d/m/Y', strtotime($booking['booking_start_time']));
                                ?>
                                <?php if($fieldId): ?>
                                    <a href="javascript:void(0)" class="text-decoration-none fw-bold text-dark" 
                                       onclick="openSubfieldCalendar(<?= $fieldId ?>, '<?= $fieldName ?>', '<?= $bookingDate ?>')"
                                       title="ดูปฏิทิน">
                                        <?= $showDate ?> <i class="fas fa-calendar-alt small text-muted ms-1"></i>
                                    </a>
                                <?php else: ?>
                                    <?= $showDate ?>
                                <?php endif; ?>

                                <small class="text-muted d-block mt-1">
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
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <?php if(!empty($booking['slip_image'])): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary px-3 shadow-sm fw-bold" 
                                                onclick="viewSlip('<?= base_url('uploads/' . $booking['slip_image']) ?>')"
                                                title="ดูสลิปโอนเงิน">
                                            <i class="fas fa-file-invoice me-1"></i> ดูสลิป
                                        </button>
                                    <?php else: ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary px-3 fw-bold" 
                                                disabled
                                                title="ไม่มีหลักฐานการโอน">
                                            <i class="fas fa-times-circle me-1"></i> ไม่มีสลิป
                                        </button>
                                    <?php endif; ?>

                                    <?php if($booking['status'] == 'pending'): ?>
                                        <a href="<?= base_url('owner/bookings/approve/' . $booking['id']) ?>" 
                                           class="btn btn-sm btn-success px-3 fw-bold shadow-sm" 
                                           onclick="return confirm('ยืนยันการอนุมัติการจองนี้?');"
                                           title="อนุมัติ">
                                            <i class="fas fa-check me-1"></i> อนุมัติ
                                        </a>
                                        <a href="<?= base_url('owner/bookings/reject/' . $booking['id']) ?>" 
                                           class="btn btn-sm btn-danger px-3 fw-bold shadow-sm" 
                                           onclick="return confirm('ยืนยันการปฏิเสธการจองนี้?');"
                                           title="ปฏิเสธ">
                                            <i class="fas fa-times me-1"></i> ปฏิเสธ
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fas fa-check-circle"></i> ดำเนินการแล้ว</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Slip Modal -->
<div class="modal fade" id="slipModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>หลักฐานการชำระเงิน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center bg-light">
                <img id="slipImage" src="" class="img-fluid rounded shadow-sm" alt="Slip" style="max-height: 80vh;">
            </div>
        </div>
    </div>
</div>

<script>
function viewSlip(url) {
    document.getElementById('slipImage').src = url;
    new bootstrap.Modal(document.getElementById('slipModal')).show();
}
</script>
