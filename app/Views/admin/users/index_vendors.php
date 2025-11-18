<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h3 mb-0 text-gray-800">จัดการเจ้าของสนาม (Vendors)</h3>
        <a href="<?= base_url('admin/users/create/vendors') ?>" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> เพิ่ม Vendor ใหม่
        </a>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold" style="color: var(--mint-primary);">รายชื่อ Vendors ทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th>ชื่อสนาม/ร้านค้า</th>
                            <th>Email</th>
                            <th>เบอร์โทร</th>
                            <th>Tax ID</th>
                            <th width="15%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($users)): ?>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td class="text-center"><?= $user['id'] ?></td>
                                <td class="fw-bold text-primary"><?= esc($user['vendor_name']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['phone_number'] ?? '-') ?></td>
                                <td><?= esc($user['tax_id'] ?? '-') ?></td>
                                <td>
                                    <a href="<?= base_url('admin/users/edit/vendors/' . $user['id']) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('admin/users/delete/vendors/' . $user['id']) ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('ยืนยันที่จะลบ Vendor รายนี้?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-store-slash fa-3x mb-3 d-block opacity-50"></i>
                                    ไม่พบข้อมูล Vendor
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>