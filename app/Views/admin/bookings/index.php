<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <h3 class="h3 mb-3 text-gray-800">จัดการการจองทั้งหมด (Global Bookings)</h3>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold" style="color: var(--mint-primary);">รายการจองล่าสุด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-datatable align-middle" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>ลูกค้า</th>
                            <th>สนาม / เจ้าของ</th>
                            <th>วันเวลา</th>
                            <th>ยอดเงิน</th>
                            <th>สถานะ</th>
                            <th>สลิป</th>
                            <th width="12%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($bookings)): ?>
                            <?php foreach($bookings as $row): ?>
                            <tr>
                                <td class="text-center fw-bold">#<?= $row['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($row['customer_name']) ?></div>
                                    <small class="text-muted"><i class="fas fa-phone-alt fa-xs me-1"></i><?= esc($row['customer_phone'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <div class="text-primary fw-bold"><?= esc($row['stadium_name']) ?></div>
                                    <small class="text-muted">By: <?= esc($row['vendor_name']) ?></small>
                                </td>
                                <td>
                                    <div><?= date('d/m/Y', strtotime($row['booking_start_time'])) ?></div>
                                    <small class="text-muted badge bg-light text-dark border">
                                        <?= date('H:i', strtotime($row['booking_start_time'])) ?> - <?= date('H:i', strtotime($row['booking_end_time'])) ?>
                                    </small>
                                </td>
                                <td class="fw-bold text-success">฿<?= number_format($row['total_price'], 0) ?></td>
                                
                                <td>
                                    <?php 
                                        $status = $row['status'];
                                        $badge = 'bg-secondary';
                                        
                                        if($status == 'paid' || $status == 'confirmed') $badge = 'bg-success';
                                        if($status == 'pending') $badge = 'bg-warning text-dark';
                                        if($status == 'cancelled') $badge = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badge ?> rounded-pill"><?= ucfirst($status) ?></span>
                                </td>

                                <td class="text-center">
                                    <?php if(!empty($row['slip_image'])): ?>
                                        <button type="button" class="btn btn-sm btn-outline-info btn-view-slip" 
                                                data-img="<?= base_url('uploads/slips/' . $row['slip_image']) ?>"
                                                data-bs-toggle="modal" data-bs-target="#slipModal">
                                            <i class="fas fa-receipt"></i> ดู
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if($status == 'pending'): ?>
                                            <a href="<?= base_url('admin/bookings/approve/' . $row['id']) ?>" 
                                               class="btn btn-success btn-sm btn-confirm-action"
                                               data-title="ยืนยันการอนุมัติ?" 
                                               data-text="ตรวจสอบยอดเงินแล้วใช่หรือไม่?"
                                               title="Approve Payment">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if($status != 'cancelled'): ?>
                                            <a href="<?= base_url('admin/bookings/cancel/' . $row['id']) ?>" 
                                               class="btn btn-danger btn-sm btn-confirm-action"
                                               data-title="ยืนยันการยกเลิก?"
                                               data-text="รายการนี้จะถูกยกเลิกทันที"
                                               title="Cancel">
                                                <i class="fas fa-times"></i>
                                            </a>
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
</div>

<div class="modal fade" id="slipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>หลักฐานการโอนเงิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center bg-dark p-0">
                <img id="slipImagePreview" src="" class="img-fluid" style="max-height: 80vh;">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. ดูสลิป
        $('.btn-view-slip').on('click', function() {
            $('#slipImagePreview').attr('src', $(this).data('img'));
        });
        
        // 2. SweetAlert ยืนยัน
        $('.btn-confirm-action').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            const title = $(this).data('title');
            const text = $(this).data('text');

            Swal.fire({
                title: title, text: text, icon: 'question',
                showCancelButton: true, confirmButtonColor: '#10b981', cancelButtonColor: '#6b7280',
                confirmButtonText: 'ยืนยัน'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = href;
            });
        });
    });
</script>
<?= $this->endSection() ?>