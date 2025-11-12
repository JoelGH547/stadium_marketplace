<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <h1><?= esc($title) ?></h1>
    <p><a href="<?= base_url('admin/stadiums') ?>">
        <button class="btn btn-secondary">&laquo; Back to Stadium List</button>
    </a></p>

    <?php $validation = session()->getFlashdata('validation'); ?>
    <?php if ($validation): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?? 'Please check your input.' ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/stadiums') ?>" method="post">
        
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="name">Stadium Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= old('name') ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>" <?= (old('category_id') == $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="vendor_id">Assign to Vendor (มอบหมายให้ Vendor):</label>
            <select id="vendor_id" name="vendor_id" class="form-control" required>
                <option value="">-- Select Vendor --</option>
                <?php foreach ($vendors as $vendor): ?>
                    <option value="<?= esc($vendor['id']) ?>" <?= (old('vendor_id') == $vendor['id']) ? 'selected' : '' ?>>
                        <?= esc($vendor['username']) ?> (<?= esc($vendor['vendor_name'] ?? 'N/A') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" class="form-control" value="<?= old('price') ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?= old('description') ?></textarea>
        </div>
        
        <div class="form-group" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">Save Stadium</button>
        </div>
    </form>

<?= $this->endSection() ?>