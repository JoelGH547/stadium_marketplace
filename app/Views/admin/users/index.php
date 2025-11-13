<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>

<p>
    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
        Add New User
    </a>
</p>

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

<h3 style="margin-top: 30px;">Admins</h3>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (! empty($admins) && is_array($admins)): ?>
            <?php foreach ($admins as $user): ?>
                <tr>
                    <td><?= esc($user['id']) ?></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td>
                        <a href="<?= base_url('admin/users/edit/admin/' . $user['id']) ?>" 
                           class="btn btn-warning btn-sm">Edit</a>
                        
                        <?php if (session()->get('user_id') != $user['id'] || session()->get('role') != 'admin'): ?>
                            <a href="<?= base_url('admin/users/delete/admin/' . $user['id']) ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this ADMIN?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">No Admins found.</td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
<h3 style="margin-top: 30px;">Vendors (เจ้าของสนาม)</h3>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Vendor Name</th>
            <th>Phone</th>
            <th>Tax ID</th>
            <th>Bank Account</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (! empty($vendors) && is_array($vendors)): ?>
            <?php foreach ($vendors as $user): ?>
                <tr>
                    <td><?= esc($user['id']) ?></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td><?= esc($user['vendor_name']) ?></td>
                    <td><?= esc($user['phone_number']) ?></td>
                    <td><?= esc($user['tax_id']) ?></td>
                    <td><?= esc($user['bank_account']) ?></td>
                    <td>
                        <a href="<?= base_url('admin/users/edit/vendor/' . $user['id']) ?>" 
                           class="btn btn-warning btn-sm">Edit</a>
                        
                        <a href="<?= base_url('admin/users/delete/vendor/' . $user['id']) ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this VENDOR?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align: center;">No Vendors found.</td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
<h3 style="margin-top: 30px;">Customers (ลูกค้า)</h3>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Full Name</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (! empty($customers) && is_array($customers)): ?>
            <?php foreach ($customers as $user): ?>
                <tr>
                    <td><?= esc($user['id']) ?></td>
                    <td><?= esc($user['username']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td><?= esc($user['full_name']) ?></td>
                    <td><?= esc($user['phone_number']) ?></td>
                    <td>
                        <a href="<?= base_url('admin/users/edit/customer/' . $user['id']) ?>" 
                           class="btn btn-warning btn-sm">Edit</a>
                        
                        <a href="<?= base_url('admin/users/delete/customer/' . $user['id']) ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this CUSTOMER?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center;">No Customers found.</td>
            </tr>
        <?php endif ?>
    </tbody>
</table>

<?= $this->endSection() ?>