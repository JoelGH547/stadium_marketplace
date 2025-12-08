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
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-1"></i> <?= session()->getFlashdata('error') ?>
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
                            <th width="5%" class="text-center text-nowrap">ID</th>
                            <th width="20%">ลูกค้า</th>
                            <th width="20%">สนาม / เจ้าของ</th>
                            <th width="18%">วันเวลา</th>
                            <th width="10%">ยอดเงิน</th>
                            <th width="10%" class="text-center">สถานะ</th>
                            <th width="5%" class="text-center">สลิป</th>
                            <th width="12%" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($bookings)): ?>
                            <?php foreach($bookings as $row): ?>
                            <tr>
                                <td class="text-center fw-bold">#<?= $row['id'] ?></td>
                                
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($row['customer_name'] ?? '-') ?></div>
                                    <small class="text-muted"><i class="fas fa-phone-alt fa-xs me-1"></i><?= esc($row['customer_phone'] ?? '-') ?></small>
                                </td>

                                <td>
                                    <div class="text-primary fw-bold"><?= esc($row['stadium_name']) ?></div>
                                    <?php if(!empty($row['field_name'])): ?>
                                        <span class="badge bg-info text-dark border">
                                            <i class="fas fa-map-marker me-1"></i> <?= esc($row['field_name']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <div class="small text-muted mt-1">By: <?= esc($row['vendor_name'] ?? '-') ?></div>
                                </td>

                                <td>
                                    <div><?= date('d/m/Y', strtotime($row['booking_start_time'])) ?></div>
                                    <small class="text-muted badge bg-light text-dark border">
                                        <?= date('H:i', strtotime($row['booking_start_time'])) ?> - 
                                        <?= date('H:i', strtotime($row['booking_end_time'])) ?>
                                    </small>
                                </td>

                                <td class="fw-bold text-success">฿<?= number_format($row['total_price'], 0) ?></td>
                                
                                <td class="text-center">
                                    <?php 
                                        $status = strtolower($row['status']);
                                        $badgeClass = 'bg-secondary';
                                        $statusText = $status;

                                        if($status == 'paid' || $status == 'confirmed') {
                                            $badgeClass = 'bg-success';
                                            $statusText = 'Paid';
                                        } elseif($status == 'pending') {
                                            $badgeClass = 'bg-warning text-dark';
                                            $statusText = 'Pending';
                                        } elseif($status == 'cancelled') {
                                            $badgeClass = 'bg-danger';
                                            $statusText = 'Cancelled';
                                        }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> rounded-pill" style="min-width: 80px;">
                                        <?= ucfirst($statusText) ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <?php if(!empty($row['slip_image'])): ?>
                                        <button type="button" class="btn btn-sm btn-outline-info btn-view-slip" 
                                                data-img="<?= base_url('assets/uploads/slips/' . $row['slip_image']) ?>"
                                                data-bs-toggle="modal" data-bs-target="#slipModal">
                                            <i class="fas fa-receipt"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center text-nowrap"> <div class="d-flex gap-1 justify-content-center">
                                        
                                        <button type="button" class="btn btn-warning btn-sm text-dark shadow-sm" 
                                                onclick="setEditData(<?= $row['id'] ?>, '<?= $status ?>')"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editStatusModal"
                                                title="แก้ไขสถานะ">
                                            <i class="fas fa-pen fa-xs"></i>
                                        </button>

                                        <?php if($status == 'pending'): ?>
                                            <a href="<?= base_url('admin/bookings/approve/' . $row['id']) ?>" 
                                               class="btn btn-success btn-sm btn-confirm-action shadow-sm"
                                               data-title="ยืนยันการอนุมัติ?" 
                                               data-text="ตรวจสอบยอดเงินแล้วใช่หรือไม่?"
                                               title="อนุมัติ (Approve)">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if($status != 'cancelled'): ?>
                                            <a href="<?= base_url('admin/bookings/cancel/' . $row['id']) ?>" 
                                               class="btn btn-danger btn-sm btn-confirm-action shadow-sm"
                                               data-title="ยืนยันการยกเลิก?"
                                               data-text="รายการนี้จะถูกเปลี่ยนสถานะเป็นยกเลิก"
                                               title="ยกเลิก (Cancel)">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">ไม่พบข้อมูลการจอง</td>
                            </tr>
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

<div class="modal fade" id="editStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>แก้ไขสถานะการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/bookings/updateStatus') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="booking_id" id="modal_booking_id">
                    
                    <div class="mb-3 text-center">
                        <label class="form-label fw-bold">เลือกสถานะใหม่:</label>
                        <select name="status" id="modal_status" class="form-select text-center">
                            <option value="pending">Pending (รอตรวจสอบ)</option>
                            <option value="confirmed">Paid (ชำระเงินแล้ว)</option>
                            <option value="cancelled">Cancelled (ยกเลิก)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        $('.btn-view-slip').on('click', function() {
            $('#slipImagePreview').attr('src', $(this).data('img'));
        });
        
       
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

    
    function setEditData(id, currentStatus) {
        document.getElementById('modal_booking_id').value = id;
        if(currentStatus === 'paid') currentStatus = 'confirmed';
        document.getElementById('modal_status').value = currentStatus;
    }
</script>

<?= $this->endSection() ?>