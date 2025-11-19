<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<h1><?= esc($title ?? 'Create New Category') ?></h1>

<p>
    <a href="<?= base_url('admin/categories') ?>">
        <button type="button" class="btn btn-secondary">&laquo; Back to Category List</button>
    </a>
</p>

<?php $validation = session()->getFlashdata('validation'); ?>
<?php if ($validation): ?>
    <div style="color:#b91c1c; border:1px solid #fecaca; background:#fef2f2; padding:10px; margin-bottom:15px; border-radius:4px;">
        <ul style="margin:0; padding-left:20px;">
            <?php foreach ($validation->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= base_url('admin/categories/create') ?>" method="post" style="max-width:480px;">
    <?= csrf_field() ?>

    <div style="margin-bottom: 15px;">
        <label for="name" style="display:block; font-weight:600; margin-bottom:4px;">Category Name:</label>
        <input
            type="text"
            id="name"
            name="name"
            value="<?= old('name') ?>"
            required
            style="width:100%; padding:8px 10px; box-sizing:border-box; border-radius:6px; border:1px solid #d1d5db;">
    </div>

    <div style="margin-bottom: 15px;">
        <label for="emoji" style="display:block; font-weight:600; margin-bottom:4px;">
            Emoji (ประเภทกีฬา):
        </label>
        <input
            type="text"
            id="emoji"
            name="emoji"
            maxlength="8"
            value="<?= old('emoji') ?>"
            placeholder=""
            style="width:120px; padding:6px 10px; box-sizing:border-box; border-radius:6px; border:1px solid #d1d5db; font-size:1.3rem; text-align:center;">
        <div style="margin-top:4px; font-size:12px; color:#6b7280;">
            แนะนำ: เลือก emoji จาก Emojipedia แล้ววางที่นี่ (ไม่บังคับ)
        </div>
    </div>

    <div style="margin-top: 20px;">
        <button type="submit"
                style="background-color:#0ea5a4; color:white; padding:8px 16px; border:none; border-radius:6px; cursor:pointer;">
            Save Category
        </button>
    </div>
</form>

<?= $this->endSection() ?>
