<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>

<p>
    <!-- (ใช้ .btn-primary) -->
    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
        Add New User
    </a>
</p>

<!-- (แสดงข้อความ Success/Error) -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>


<!-- (ใช้ .table) -->
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (! empty($users) && is_array($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= esc($user['id']) ?></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td>
                        <!-- ⬇️ --- 1. อัปเดตตรรกะ 'Role' ทั้งหมด --- ⬇️ -->
                        <?php if ($user['role'] == 'admin'): ?>
                            <span class="badge badge-admin">admin</span>
                        <?php elseif ($user['role'] == 'vendor'): ?>
                            <span class="badge badge-vendor">vendor</span>
                        <?php elseif ($user['role'] == 'customer'): ?>
                            <span class="badge badge-customer">customer</span>
                        <?php else: ?>
                            <!-- (กันเหนียวเผื่อมี Role เก่าเช่น staff) -->
                            <span class="badge badge-staff"><?= esc($user['role']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- (ใช้ .btn และ .btn-sm) -->
                        <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" 
                           class="btn btn-warning btn-sm">Edit</a>
                        
                        <!-- (ป้องกัน Admin ลบตัวเอง) -->
                        <?php if (session()->get('user_id') != $user['id']): ?>
                            <a href="<?= base_url('admin/users/delete/' . $user['id']) ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">No users found.</td>
            </tr>
        <?php endif ?>
    </tbody>
</table>

<?= $this->endSection() ?>