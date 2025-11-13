<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>
<p><a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Back to User List</a></p>

<?php $validation = session()->getFlashdata('validation'); ?>
<?php if (isset($validation)): ?>
    <div class="alert alert-danger">
        <?= $validation->listErrors() ?? 'Please check your input.' ?>
    </div>
<?php endif; ?>
<?php $errors = session()->getFlashdata('errors'); ?>
<?php if ($errors): ?>
    <div class="alert alert-danger">
        <?= esc($errors) ?>
    </div>
<?php endif; ?>


<form action="<?= base_url('admin/users/update/' . $role . '/' . $user['id']) ?>" method="post">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="role">User Role:</label>
        <select id="role" name="role" class="form-control" disabled>
            <option value="admin" <?= ($role == 'admin') ? 'selected' : '' ?>>Admin</option>
            <option value="vendor" <?= ($role == 'vendor') ? 'selected' : '' ?>>Vendor</option>
            <option value="customer" <?= ($role == 'customer') ? 'selected' : '' ?>>Customer</option>
        </select>
        <small>Role cannot be changed after creation.</small>
    </div>

    <hr>

    <div id="base-fields">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= old('username', $user['username']) ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= old('email', $user['email']) ?>">
        </div>

        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" class="form-control">
            <small>Leave blank to keep the current password.</small>
        </div>
    </div>  
    <div id="vendor-fields" style="display: none; border-left: 3px solid #f39c12; padding-left: 15px; margin-top: 15px;">
    <h4>Vendor Details</h4>
    <div class="form-group">
        <label for="vendor_name">Vendor Name (ชื่อบริษัท/ชื่อเจ้าของ):</label>
        <input type="text" id="vendor_name" name="vendor_name" class="form-control" value="<?= old('vendor_name', $user['vendor_name'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label for="phone_number_vendor">Phone (เบอร์ติดต่อ Vendor):</label>
        <input type="text" id="phone_number_vendor" name="phone_number" class="form-control" value="<?= old('phone_number', $user['phone_number'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label for="tax_id">Tax ID (เลขผู้เสียภาษี):</label>
        <input type="text" id="tax_id" name="tax_id" class="form-control" value="<?= old('tax_id', $user['tax_id'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label for="bank_account">Bank Account (เลขบัญชี):</label>
        <input type="text" id="bank_account" name="bank_account" class="form-control" value="<?= old('bank_account', $user['bank_account'] ?? '') ?>">
    </div>
</div>
<div id="customer-fields" style="display: none; border-left: 3px solid #1abc9c; padding-left: 15px; margin-top: 15px;">
    <h4>Customer Details</h4>
    <div class="form-group">
        <label for="full_name">Full Name (ชื่อ-นามสกุลจริง):</label>
        <input type="text" id="full_name" name="full_name" class="form-control" value="<?= old('full_name', $user['full_name'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label for="phone_number_customer">Phone (เบอร์ติดต่อ Customer):</label>
        <input type="text" id="phone_number_customer" name="phone_number" class="form-control" value="<?= old('phone_number', $user['phone_number'] ?? '') ?>">
    </div>
</div>

<div class="form-group" style="margin-top: 20px;">
    <button type="submit" class="btn btn-primary">Update User</button>
</div>
</form>

<script>
    function showCorrectFields() {
        // 1. ดึงค่า Role ที่ถูกส่งมาจาก Controller (ซึ่งถูก "ปิด" (disabled) ไว้)
        var role = "<?= esc($role) ?>"; // (เช่น 'admin', 'vendor', 'customer')
        
        // 2. ดึง Element ของฟิลด์พิเศษ
        var vendorFields = document.getElementById('vendor-fields');
        var customerFields = document.getElementById('customer-fields');

        // 3. แสดงฟิลด์ตาม Role ที่ถูกส่งมา
        if (role === 'vendor') {
            vendorFields.style.display = 'block';
            customerFields.style.display = 'none';
        } else if (role === 'customer') {
            vendorFields.style.display = 'none';
            customerFields.style.display = 'block';
        } else {
            // (ถ้าเป็น Admin หรือ Role อื่น)
            vendorFields.style.display = 'none';
            customerFields.style.display = 'none';
        }
    }

    // 4. สั่งให้ JS ทำงาน 1 ครั้งตอนโหลดหน้าเว็บ
    document.addEventListener('DOMContentLoaded', function() {
        showCorrectFields();
    });
</script>

<?= $this->endSection() ?>