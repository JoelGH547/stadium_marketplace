<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>
<p><a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">&laquo; Back to Dashboard</a></p>

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

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Vendor Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>วันที่สมัคร (Created)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        
        <?php if (! empty($vendors) && is_array($vendors)): ?>
            <?php foreach ($vendors as $vendor): ?>
                <tr>
                    <td><strong><?= esc($vendor['id']) ?></strong></td>
                    <td><?= esc($vendor['username']) ?></td>
                    <td><?= esc($vendor['vendor_name']) ?></td>
                    <td><?= esc($vendor['email']) ?></td>
                    <td><?= esc($vendor['phone_number']) ?></td>
                    <td><?= esc($vendor['created_at']) ?></td>
                    <td>
                        
                        <a href="<?= base_url('admin/vendors/approve/' . $vendor['id']) ?>" 
                           class="btn btn-success btn-sm" 
                           style="background-color: #28a745; border-color: #28a745; color: white;"
                           onclick="return confirm('คุณแน่ใจหรือไม่ ว่าต้องการ \'อนุมัติ\' (Approve) Vendor นี้?')">
                            Approve
                        </a>
                        
                        
                        <a href="<?= base_url('admin/vendors/reject/' . $vendor['id']) ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('คุณแน่ใจหรือไม่ ว่าต้องการ \'ปฏิเสธ\' (Reject) Vendor นี้?')">
                            Reject
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">
                     ไม่มี Vendor ที่รอการอนุมัติ
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>