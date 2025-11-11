<?= $this->extend('admin/layout') ?>
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


<form action="<?= base_url('admin/users') ?>" method="post">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="role">Select Role:</label>
        <select id="role" name="role" class="form-control" required onchange="toggleRoleFields()">
            <option value="">-- Select Role --</option>
            <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="vendor" <?= old('role') == 'vendor' ? 'selected' : '' ?>>Vendor (เจ้าของสนาม)</option>
            <option value="customer" <?= old('role') == 'customer' ? 'selected' : '' ?>>Customer (ลูกค้า)</option>
        </select>
    </div>

    <hr>

    <div id="base-fields">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= old('username') ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control">
            <small>Min 6 characters. (Required for new user)</small>
        </div>
    </div>

    <div id="vendor-fields" style="display: none; border-left: 3px solid #f39c12; padding-left: 15px; margin-top: 15px;">
        <h4>Vendor Details (Required)</h4>
        <div class="form-group">
            <label for="vendor_name">Vendor Name (ชื่อบริษัท/ชื่อเจ้าของ):</label>
            <input type="text" id="vendor_name" name="vendor_name" class="form-control" value="<?= old('vendor_name') ?>">
        </div>
        <div class="form-group">
            <label for="phone_number_vendor">Phone (เบอร์ติดต่อ Vendor):</label>
            <input type="text" id="phone_number_vendor" name="phone_number" class="form-control" value="<?= old('phone_number') ?>">
        </div>
        <div class="form-group">
            <label for="tax_id">Tax ID (เลขผู้เสียภาษี):</label>
            <input type="text" id="tax_id" name="tax_id" class="form-control" value="<?= old('tax_id') ?>">
        </div>
        <div class="form-group">
            <label for="bank_account">Bank Account (เลขบัญชี):</label>
            <input type="text" id="bank_account" name="bank_account" class="form-control" value="<?= old('bank_account') ?>">
        </div>
    </div>

    <div id="customer-fields" style="display: none; border-left: 3px solid #1abc9c; padding-left: 15px; margin-top: 15px;">
        <h4>Customer Details (Optional)</h4>
        <div class="form-group">
            <label for="full_name">Full Name (ชื่อ-นามสกุลจริง):</label>
            <input type="text" id="full_name" name="full_name" class="form-control" value="<?= old('full_name') ?>">
        </div>
        <div class="form-group">
            <label for="phone_number_customer">Phone (เบอร์ติดต่อ Customer):</label>
            <input type="text" id="phone_number_customer" name="phone_number" class="form-control" value="<?= old('phone_number') ?>">
        </div>
    </div>

    <div class="form-group" style="margin-top: 20px;">
        <button type="submit" class="btn btn-primary">Create User</button>
    </div>
</form>

<script>
    function toggleRoleFields() {
        // 1. ดึงค่า Role ที่เลือก
        var role = document.getElementById('role').value;
        
        // 2. ดึง Element ของฟิลด์พิเศษ
        var vendorFields = document.getElementById('vendor-fields');
        var customerFields = document.getElementById('customer-fields');
        var baseFields = document.getElementById('base-fields'); // (ฟิลด์พื้นฐาน)

        // 3. ซ่อนทุกอย่างก่อน
        vendorFields.style.display = 'none';
        customerFields.style.display = 'none';
        baseFields.style.display = 'none'; // ซ่อนฟิลด์พื้นฐาน (ถ้ายังไม่เลือก Role)

        // 4. แสดงฟิลด์ตาม Role ที่เลือก
        if (role === 'admin') {
            baseFields.style.display = 'block';
            
        } else if (role === 'vendor') {
            baseFields.style.display = 'block';
            vendorFields.style.display = 'block';
            
        } else if (role === 'customer') {
            baseFields.style.display = 'block';
            customerFields.style.display = 'block';
        }
    }

    // 5. สั่งให้ JS ทำงาน 1 ครั้งตอนโหลดหน้าเว็บ (เผื่อมีการ 'old' ค่า Role ค้างไว้)
    document.addEventListener('DOMContentLoaded', function() {
        toggleRoleFields();
    });
</script>


<?= $this->endSection() ?>