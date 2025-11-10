<?= $this->extend('admin/layout') ?>

<!-- 2. เริ่ม Section Content -->
<?= $this->section('content') ?>

    <h1><?= esc($title) ?></h1>
    <p><a href="<?= base_url('admin/stadiums') ?>">
        <button>&laquo; Back to Stadium List</button>
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

    <!-- 🛠️ Form (Action ชี้ไปที่ 'admin/stadiums') 🛠️ -->
    <form action="<?= base_url('admin/stadiums') ?>" method="post">
        
        <?= csrf_field() ?>

        <div style="margin-bottom: 15px;">
            <label for="name">Stadium Name:</label>
            <input type="text" id="name" name="name" value="<?= old('name') ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>" <?= (old('category_id') == $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="price">Price (per hour):</label>
            <input type="number" id="price" name="price" step="0.01" value="<?= old('price') ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <!-- ‼️ ลบช่อง "Stock" ออกไปแล้ว ‼️ -->

        <div style="margin-bottom: 15px;">
            <label for="description">Description:</label>
            <textarea id="description" name="description" style="width: 100%; padding: 8px; box-sizing: border-box; min-height: 100px;"><?= old('description') ?></textarea>
        </div>
        
        <div>
            <button type="submit" style="background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Save Stadium</button>
        </div>
    </form>

<!-- 3. จบ Section Content -->
<?= $this->endSection() ?>