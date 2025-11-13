<?= $this->extend('layouts/admin') ?>

<!-- 2. เริ่ม Section Content -->
<?= $this->section('content') ?>

    <h1><?= esc($title ?? 'Create New Category') ?></h1>
    <p><a href="<?= base_url('admin/categories') ?>">
        <button>&laquo; Back to Category List</button>
    </a></p>

    <!-- 🛑 แสดง Validation Errors (ถ้ามี) 🛑 -->
    <?php $validation = session()->getFlashdata('validation'); ?>
    <?php if ($validation): ?>
        <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($validation->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- 🛠️ Form (Action ชี้ไปที่ 'admin/categories/create') 🛠️ -->
    <form action="<?= base_url('admin/categories/create') ?>" method="post">
        
        <?= csrf_field() ?>

        <div style="margin-bottom: 15px;">
            <label for="name">Category Name:</label>
            <input type="text" id="name" name="name" value="<?= old('name') ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <!-- ‼️ ลบช่อง "Description" ออกไปแล้ว ‼️ -->
        
        <div>
            <button type="submit" style="background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Save Category</button>
        </div>
    </form>

<!-- 3. จบ Section Content -->
<?= $this->endSection() ?>