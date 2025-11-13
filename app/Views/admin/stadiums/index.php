<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>

<p>
    <!-- 1. เปลี่ยน Link และ Text -->
    <a href="<?= base_url('admin/stadiums/create') ?>" class="btn btn-primary">
        Add New Stadium
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
            <th>No.</th> 
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <!-- 2. ลบคอลัมน์ Stock -->
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <!-- 3. เปลี่ยนตัวแปร $products เป็น $stadiums -->
        <?php if (! empty($stadiums) && is_array($stadiums)): ?>
            
            <?php $i = 1; ?> 
            
            <!-- 4. เปลี่ยนตัวแปร $product เป็น $stadium -->
            <?php foreach ($stadiums as $stadium): ?>
                <tr>
                    <td><?= $i++ ?></td> 
                    
                    <td><?= esc($stadium['id']) ?></td>
                    <td><?= esc($stadium['name']) ?></td>
                    <td><?= esc($stadium['category_name'] ?? 'N/A') ?></td>
                    <td><?= esc(number_format($stadium['price'], 2)) ?></td>
                    <!-- 5. ลบ 'stock' ออกจาก cell -->
                    <td><?= esc($stadium['description']) ?></td>
                    <td>
                        <!-- 6. เปลี่ยน Link (แก้ไขแล้ว) -->
                        <a href="<?= base_url('admin/stadiums/edit/' . $stadium['id']) ?>" 
                           class="btn btn-warning btn-sm">Edit</a>
                        
                        <!-- 7. เปลี่ยน Link และ ข้อความ Confirm -->
                        <a href="<?= base_url('admin/stadiums/delete/' . $stadium['id']) ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Are you sure you want to delete this stadium?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <!-- 8. เปลี่ยน colspan เป็น 7 (เพราะลบ Stock) และ Text -->
                <td colspan="7" style="text-align: center;">No stadiums found.</td>
            </tr>
        <?php endif ?>
    </tbody>
</table>

<?= $this->endSection() ?>