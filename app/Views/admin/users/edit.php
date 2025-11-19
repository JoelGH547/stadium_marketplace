<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <!-- Controller จะส่ง $title มาให้เอง -->
        <h6 class="m-0 font-weight-bold text-primary"><?= esc($title) ?> (ID: <?= $user['id'] ?>)</h6>
    </div>
    <div class="card-body">

        <!-- แสดง Error (ถ้ามี) -->
        <?php $validation = session()->getFlashdata('validation'); ?>
        <?php if (isset($validation)): ?>
            <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
        <?php endif; ?>
        
        <!-- Form Action: ส่งไปที่ update/role/id -->
        <form action="<?= base_url('admin/users/update/' . $role . '/' . $user['id']) ?>" method="post">
            <?= csrf_field() ?>

            <!-- ============================== -->
            <!-- 1. ข้อมูลพื้นฐาน (มีทุก Role) -->
            <!-- ============================== -->
            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" class="form-control" value="<?= old('email', $user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" class="form-control">
                <small class="text-muted">ปล่อยว่างไว้ ถ้าไม่ต้องการเปลี่ยนรหัสผ่าน</small>
            </div>

            <hr>

            <!-- ============================== -->
            <!-- 2. ข้อมูลเฉพาะ Admin -->
            <!-- ============================== -->
            <?php if ($role == 'admins'): ?>
                <div class="alert alert-secondary"><i class="fas fa-user-shield me-1"></i> ข้อมูลสำหรับ Admin</div>
                <div class="form-group">
                    <label for="username">Username <span class="text-danger">*</span></label>
                    <input type="text" id="username" name="username" class="form-control" value="<?= old('username', $user['username'] ?? '') ?>" required>
                </div>
            <?php endif; ?>

            <!-- ============================== -->
            <!-- 3. ข้อมูลเฉพาะ Vendor -->
            <!-- ============================== -->
            <?php if ($role == 'vendors'): ?>
                <div class="alert alert-warning"><i class="fas fa-store me-1"></i> ข้อมูลสำหรับ Vendor</div>
                <div class="form-group">
                    <label for="vendor_name">Vendor Name <span class="text-danger">*</span></label>
                    <input type="text" id="vendor_name" name="vendor_name" class="form-control" value="<?= old('vendor_name', $user['vendor_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">เบอร์โทรศัพท์</label>
                    <!-- [แก้ไข] เปลี่ยน name="phone_number_vendor" เป็น "phone" -->
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= old('phone', $user['phone'] ?? $user['phone_number'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="tax_id">Tax ID</label>
                    <input type="text" id="tax_id" name="tax_id" class="form-control" value="<?= old('tax_id', $user['tax_id'] ?? '') ?>">
                </div>
                 <div class="form-group">
                    <label for="bank_account">Bank Account (เลขบัญชี):</label>
                    <input type="text" id="bank_account" name="bank_account" class="form-control" value="<?= old('bank_account', $user['bank_account'] ?? '') ?>">
                </div>
            <?php endif; ?>

            <!-- ============================== -->
            <!-- 4. ข้อมูลเฉพาะ Customer -->
            <!-- ============================== -->
            <?php if ($role == 'customers'): ?>
                <div class="alert alert-info"><i class="fas fa-user me-1"></i> ข้อมูลสำหรับ Customer</div>
                <div class="form-group">
                    <label for="full_name">Full Name <span class="text-danger">*</span></label>
                    <input type="text" id="full_name" name="full_name" class="form-control" value="<?= old('full_name', $user['full_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">เบอร์โทรศัพท์</label>
                    <!-- [แก้ไข] เปลี่ยน name="phone_number_customer" เป็น "phone" -->
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= old('phone', $user['phone'] ?? $user['phone_number'] ?? '') ?>">
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-1"></i> อัปเดตข้อมูล
                </button>
                <a href="<?= base_url("admin/users/$role") ?>" class="btn btn-secondary">ยกเลิก</a>
            </div>
        </form>
    </div>
</div>

<!-- ไม่ต้องใช้ Javascript อีกต่อไป! -->

<?= $this->endSection() ?>