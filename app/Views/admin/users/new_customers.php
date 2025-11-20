<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="h3 mb-1 text-gray-800">ลูกค้าใหม่ (New Customers)</h3>
            <p class="text-muted small mb-0"><i class="far fa-clock me-1"></i> ข้อมูลการสมัครในช่วง 24 ชั่วโมงที่ผ่านมา</p>
        </div>
        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> กลับ Dashboard
        </a>
    </div>

    <div class="card shadow mb-4 border-0 border-start-primary"> <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">รายชื่อลูกค้าใหม่</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>Email</th>
                            <th>เบอร์โทรศัพท์</th>
                            <th>เวลาที่สมัคร</th> <th width="10%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($users)): ?>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td class="text-center"><?= $user['id'] ?></td>
                                <td class="fw-bold"><?= esc($user['full_name']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['phone_number'] ?? '-') ?></td>
                                
                                <td>
                                    <?= esc($user['created_at']) ?>
                                    <span class="badge bg-success ms-1">New!</span>
                                </td>
                                
                                <td>
                                    <a href="<?= base_url('admin/users/edit/customers/' . $user['id']) ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-user-clock fa-3x mb-3 d-block opacity-50"></i>
                                    ไม่มีลูกค้าสมัครใหม่ในช่วง 24 ชม.
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