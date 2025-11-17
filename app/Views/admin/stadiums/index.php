<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<h1><?= esc($title ?? 'Stadiums') ?></h1>

<p>
    <a href="<?= base_url('admin/stadiums/create') ?>" class="btn btn-primary">
        Add New Stadium
    </a>
</p>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cover</th>
            <th>Name</th>
            <th>Category</th>
            <th>Vendor</th>
            <th>Price/Hour</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($stadiums)): ?>
            <?php foreach ($stadiums as $index => $stadium): ?>
                <?php
                    // 1. แกะกล่อง JSON ออกมาเป็น Array
                    $images = json_decode($stadium['outside_images'] ?? '[]', true);
                    // 2. ถ้าแกะไม่ได้ (เป็น null) ให้เป็น array ว่าง
                    if (!is_array($images)) {
                        $images = [];
                    }
                    // 3. เอาภาพแรกมาโชว์
                    $cover = $images[0] ?? null;
                ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <?php if ($cover): ?>
                            <img src="<?= base_url('assets/uploads/stadiums/' . $cover) ?>"
                                 alt="Cover"
                                 style="width: 100px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        <?php else: ?>
                            <span class="text-muted">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($stadium['name']) ?></td>
                    <td><?= esc($stadium['category_name'] ?? '-') ?></td>
                    <td><?= esc($stadium['vendor_name'] ?? '-') ?></td>
                    <td><?= number_format((float) $stadium['price'], 2) ?></td>
                    <td>
                        <a href="<?= base_url('admin/stadiums/edit/' . $stadium['id']) ?>"
                           class="btn btn-sm btn-warning">Edit</a>

                        <a href="<?= base_url('admin/stadiums/delete/' . $stadium['id']) ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure you want to delete this stadium?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center;">No stadiums found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>