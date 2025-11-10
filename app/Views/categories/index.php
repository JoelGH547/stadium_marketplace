<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>

<p>
    <a href="<?= base_url('admin/categories/new') ?>" class="btn btn-primary">
        Add New Category
    </a>
</p>

<!-- (แสดงข้อความ Success) -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<!-- (แสดงข้อความ Error) -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>


<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <!-- 1. ลบ "Description" ออกจาก <thead> -->
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (! empty($categories) && is_array($categories)): ?>

            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= esc($category['id']) ?></td>
                    <td><?= esc($category['name']) ?></td>
                    <!-- 2. ลบ "description" ออกจาก <td> -->
                    <td>
                        <a href="<?= base_url('admin/categories/edit/' . $category['id']) ?>" 
                           class="btn btn-warning btn-sm">Edit</a>
                        
                        <a href="<?= base_url('admin/categories/delete/' . $category['id']) ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <!-- 3. แก้ไข colspan เป็น 3 -->
                <td colspan="3" style="text-align: center;">No categories found.</td>
            </tr>
        <?php endif ?>
    </tbody>
</table>

<?= $this->endSection() ?>