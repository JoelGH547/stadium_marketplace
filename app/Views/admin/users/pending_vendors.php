<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h3 mb-0 text-gray-800 fw-bold">อนุมัติผู้ขาย (Pending Vendors)</h3>
        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-chevron-left me-1"></i> ย้อนกลับ
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-clock me-2"></i>รายชื่อ Vendor ที่รอการอนุมัติ</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-datatable w-100 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">ID</th>
                            <th>บัญชีผู้ใช้</th>
                            <th>ชื่อสนาม/ร้านค้า</th>
                            <th>อีเมล</th>
                            <th>เบอร์โทร</th>
                            <th>วันที่สมัคร</th>
                            <th width="180">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($vendors) && is_array($vendors)): ?>
                            <?php foreach ($vendors as $vendor): ?>
                                <tr>
                                    <td><span class="fw-bold"><?= esc($vendor['id']) ?></span></td>
                                    <td><code class="text-primary"><?= esc($vendor['username']) ?></code></td>
                                    <td><div class="fw-bold text-dark"><?= esc($vendor['vendor_name']) ?></div></td>
                                    <td><?= esc($vendor['email']) ?></td>
                                    <td><?= esc($vendor['phone_number']) ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($vendor['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group w-100">
                                            <a href="<?= base_url('admin/vendors/approve/' . $vendor['id']) ?>" 
                                               class="btn btn-mint btn-sm btn-approve" 
                                               title="อนุมัติ">
                                                <i class="fas fa-check me-1"></i> อนุมัติ
                                            </a>
                                            <a href="<?= base_url('admin/vendors/reject/' . $vendor['id']) ?>" 
                                               class="btn btn-outline-danger btn-sm btn-reject" 
                                               title="ปฏิเสธ">
                                                <i class="fas fa-times me-1"></i> ปฏิเสธ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-user-check fa-3x mb-3 d-block opacity-50"></i>
                                    ไม่มีรายการที่รอการอนุมัติในขณะนี้
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Override default btn-delete for Reject with custom message
    $('.btn-reject').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        Swal.fire({
            title: 'ยืนยันการปฏิเสธ?',
            text: "หากปฏิเสธ บัญชีผู้ใช้นี้จะถูกลบออกจากระบบทันทีและไม่สามารถกู้คืนได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ยืนยัน, ปฏิเสธและลบข้อมูล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });

    // Special handler for Approve
    $('.btn-approve').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        Swal.fire({
            title: 'ยืนยันการอนุมัติ?',
            text: "เมื่ออนุมัติแล้ว Vendor จะสามารถเข้าสู่ระบบเพื่อจัดการสนามได้",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#14b8a6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ตกลง, อนุมัติเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});
</script>

<?= $this->endSection() ?>