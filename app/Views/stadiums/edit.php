<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

    <h1><?= esc($title) ?></h1>
    <p><a href="<?= base_url('admin/stadiums') ?>">
        <button>&laquo; Back to Stadium List</button>
    </a></p>

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

    <form action="<?= base_url('admin/stadiums/update/' . $stadium['id']) ?>" method="post">
        
        <?= csrf_field() ?>
        
        <div style="margin-bottom: 15px;">
            <label for="name">Stadium Name:</label>
            <input type="text" id="name" name="name" value="<?= old('name', $stadium['name']) ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= esc($category['id']) ?>" <?= (old('category_id', $stadium['category_id']) == $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="price">Price (per hour):</label>
            <input type="number" id="price" name="price" step="0.01" value="<?= old('price', $stadium['price']) ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="description">Description:</label>
            <textarea id="description" name="description" style="width: 100%; padding: 8px; box-sizing: border-box; min-height: 100px;"><?= old('description', $stadium['description']) ?></textarea>
        </div>
        
        <div>
            <button type="submit" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Update Stadium</button>
        </div>
    </form>

<?= $this->endSection() ?>
