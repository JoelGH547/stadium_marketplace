<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<h1 class="mb-4"><?= esc($title ?? 'Categories') ?></h1>

<p class="mb-3">
    <a href="<?= base_url('admin/categories/new') ?>" class="btn btn-primary">
        + Add New Category
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

<table class="table table-striped align-middle">
    <thead>
        <tr>
            <th style="width: 70px;">ID</th>
            <th style="width: 90px;">Emoji</th>
            <th>Name</th>
            <th style="width: 200px;">Created At</th>
            <th style="width: 180px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= esc($category['id']) ?></td>
                    <td>
                        <span style="font-size: 1.4rem;">
                            <?= esc($category['emoji'] ?? '') ?>
                        </span>
                    </td>
                    <td><?= esc($category['name']) ?></td>
                    <td><?= esc($category['created_at'] ?? '-') ?></td>
                    <td>
                        <a href="<?= base_url('admin/categories/edit/' . $category['id']) ?>"
                        class="btn btn-sm btn-warning">
                        Edit
                        </a>
                        <a href="<?= base_url('admin/categories/delete/' . $category['id']) ?>"
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this category?');">
                        Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No categories found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
