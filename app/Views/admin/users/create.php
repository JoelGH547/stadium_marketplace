<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?= esc($title) ?></h6>
    </div>
    <div class="card-body">
        
        <?php $validation = session()->getFlashdata('validation'); ?>
        <?php if (isset($validation)): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/users/store/' . $role) ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password <span class="text-danger">*</span></label>
                <input type="password" id="password" name="password" class="form-control" required>
                <small class="text-muted">กำหนดรหัสผ่านอย่างน้อย 6 ตัวอักษร</small>
            </div>

            <hr>

            <?php if ($role == 'admins'): ?>
                <div class="alert alert-secondary">
                    <i class="fas fa-user-shield mr-1"></i> ข้อมูลสำหรับ Admin
                </div>
                <div class="form-group">
                    <label for="username">Username <span class="text-danger">*</span></label>
                    <input type="text" id="username" name="username" class="form-control" value="<?= old('username') ?>" required>
                </div>
            <?php endif; ?>


            <?php if ($role == 'vendors'): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-store mr-1"></i> ข้อมูลสำหรับ Vendor
                </div>
                <div class="form-group">
                    <label for="vendor_name">Vendor Name (ชื่อสนาม/ร้านค้า) <span class="text-danger">*</span></label>
                    <input type="text" id="vendor_name" name="vendor_name" class="form-control" value="<?= old('vendor_name') ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">เบอร์โทรศัพท์</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= old('phone') ?>">
                </div>
                <div class="form-group">
                    <label for="tax_id">Tax ID (เลขผู้เสียภาษี)</label>
                    <input type="text" id="tax_id" name="tax_id" class="form-control" value="<?= old('tax_id') ?>">
                </div>
            <?php endif; ?>


            <?php if ($role == 'customers'): ?>
                <div class="alert alert-info">
                    <i class="fas fa-user mr-1"></i> ข้อมูลสำหรับ Customer
                </div>
                <div class="form-group">
                    <label for="full_name">Full Name (ชื่อ-นามสกุล) <span class="text-danger">*</span></label>
                    <input type="text" id="full_name" name="full_name" class="form-control" value="<?= old('full_name') ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">เบอร์โทรศัพท์</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= old('phone') ?>">
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> บันทึกข้อมูล
                </button>
                <a href="<?= base_url("admin/users/$role") ?>" class="btn btn-secondary">ยกเลิก</a>
            </div>

        </form>
    </div>
</div>

<?= $this->endSection() ?>