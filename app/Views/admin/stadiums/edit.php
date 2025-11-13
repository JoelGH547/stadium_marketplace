<?= $this->extend('layouts/admin') ?>

<!-- 2. เริ่ม Section Content -->
<?= $this->section('content') ?>

    <h1><?= esc($title) ?></h1>
    <p><a href="<?= base_url('admin/stadiums') ?>">
        <button class="btn btn-secondary">&laquo; Back to Stadium List</button>
    </a></p>

    <!-- (แสดง Validation Errors) -->
    <?php $validation = session()->getFlashdata('validation'); ?>
    <?php if ($validation): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?? 'Please check your input.' ?>
        </div>
    <?php endif; ?>

    <!-- 1. ⬇️ Form (Action ชี้ไปที่ 'admin/stadiums/update/[ID]') ⬇️ -->
    <form action="<?= base_url('admin/stadiums/update/' . $stadium['id']) ?>" method="post">
        
        <?= csrf_field() ?>
        
        <div class="form-group">
            <label for="name">Stadium Name:</label>
            <!-- (แสดง "ค่าเก่า") -->
            <input type="text" id="name" name="name" class="form-control" value="<?= old('name', $stadium['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $category): ?>
                    <!-- (เช็ก "ค่าเก่า" Selected) -->
                    <option value="<?= esc($category['id']) ?>" <?= (old('category_id', $stadium['category_id']) == $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- 2. ⬇️ (เพิ่ม) "Dropdown (ตัวเลือก) ...สำหรับ 'เลือก' (Assign) ...Vendor" ⬇️ -->
        <div class="form-group">
            <label for="vendor_id">Assign to Vendor (มอบหมายให้ Vendor):</label>
            <select id="vendor_id" name="vendor_id" class="form-control" required>
                <option value="">-- Select Vendor --</option>
                <!-- (วนลูป $vendors ที่ Controller ส่งมา) -->
                <?php foreach ($vendors as $vendor): ?>
                    <!-- (เช็ก "ค่าเก่า" Selected ...จาก $stadium['vendor_id']) -->
                    <option value="<?= esc($vendor['id']) ?>" <?= (old('vendor_id', $stadium['vendor_id']) == $vendor['id']) ? 'selected' : '' ?>>
                        <?= esc($vendor['username']) ?> (<?= esc($vendor['vendor_name'] ?? 'N/A') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- ⬆️ (จบส่วนที่เพิ่ม) ⬆️ -->

        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" class="form-control" value="<?= old('price', $stadium['price']) ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?= old('description', $stadium['description']) ?></textarea>
        </div>
        
        <div class="form-group" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Update Stadium</button>
        </div>
    </form>

<!-- 3. จบ Section Content -->
<?= $this->endSection() ?>