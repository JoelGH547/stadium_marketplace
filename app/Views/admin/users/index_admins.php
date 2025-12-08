<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h3 mb-0 text-gray-800">จัดการผู้ดูแลระบบ (Admins)</h3>
        <a href="<?= base_url('admin/users/create/admins') ?>" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> เพิ่ม Admin ใหม่
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
            <h6 class="m-0 font-weight-bold" style="color: var(--mint-primary);">รายชื่อ Admins ทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-datatable align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th width="18%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($users)): ?>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td class="text-center"><?= $user['id'] ?></td>
                                <td class="fw-bold text-primary"><?= esc($user['username']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="<?= base_url('admin/users/edit/admins/' . $user['id']) ?>" class="btn btn-warning btn-sm" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="<?= base_url('admin/users/delete/admins/' . $user['id']) ?>" 
                                           class="btn btn-danger btn-sm btn-delete" title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </a>

                                        <button type="button" class="btn btn-info btn-sm text-white btn-view-details" 
                                                title="ดูรายละเอียด"
                                                data-username="<?= esc($user['username']) ?>"
                                                data-email="<?= esc($user['email']) ?>"
                                                data-created="<?= esc($user['created_at'] ?? '-') ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fas fa-user-shield fa-3x mb-3 d-block opacity-50"></i>
                                    ไม่พบข้อมูล Admin
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-user-shield me-2"></i>ข้อมูลผู้ดูแลระบบ (Admin)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <table class="table table-borderless mb-0">
                    <tbody>
                        <tr>
                            <th width="35%" class="text-muted">Username:</th>
                            <td class="fw-bold text-primary" id="view_username"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Email:</th>
                            <td id="view_email"></td>
                        </tr>
                        <tr>
                            <th class="text-muted">สถานะ:</th>
                            <td><span class="badge bg-danger">Administrator</span></td>
                        </tr>
                        <tr><td colspan="2"><hr class="my-1"></td></tr>
                        <tr>
                            <th class="text-muted">วันที่สร้างบัญชี:</th>
                            <td id="view_created" class="small text-secondary"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        $(document).on('click', '.btn-view-details', function() {
            const username = $(this).data('username');
            const email = $(this).data('email');
            const created = $(this).data('created');

            $('#view_username').text(username);
            $('#view_email').text(email);
            $('#view_created').text(created);

            var myModal = new bootstrap.Modal(document.getElementById('viewAdminModal'));
            myModal.show();
        });
    });
</script>

<?= $this->endSection() ?>