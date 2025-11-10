<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>
<p><a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Back to User List</a></p>

<?php $validation = session()->getFlashdata('validation'); ?>
<?php if (isset($validation)): ?>
    <!-- 1. ใช้ CSS Class 'alert' 'alert-danger' -->
    <div class="alert alert-danger">
        <?= $validation->listErrors() ?? 'Please check your input.' ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="post">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="username">Username:</label>
        <!-- 2. เพิ่ม CSS Class 'form-control' -->
        <input type="text" id="username" name="username" class="form-control" value="<?= old('username', $user['username']) ?>" required>
    </div>

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= old('email', $user['email']) ?>" required>
    </div>

    <div class="form-group">
        <label for="role">Role:</label>
        <!-- 3. อัปเดต Dropdown ให้ตรงกับโจทย์ใหม่ -->
        <select id="role" name="role" class="form-control" required>
            <option value="admin" <?= old('role', $user['role']) == 'admin' ? 'selected' : '' ?>>Admin (ผู้ดูแลสูงสุด)</option>
            <option value="vendor" <?= old('role', $user['role']) == 'vendor' ? 'selected' : '' ?>>Vendor (เจ้าของสนาม)</option>
            <option value="customer" <?= old('role', $user['role']) == 'customer' ? 'selected' : '' ?>>Customer (ลูกค้า)</option>
        </select>
    </div>

    <hr style="margin:20px 0;">

    <div class="form-group">
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" class="form-control">
        <small>Leave blank to keep the current password.</small>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Update User</button>
    </div>
</form>

<?= $this->endSection() ?>