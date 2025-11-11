<?= $this->extend('auth/layout') ?>
<?= $this->section('content') ?>

<div class="auth-card">
    <!-- 1. เปลี่ยน Title และ Action -->
    <h1>Vendor Login</h1>
    <p>Please log in to access the Vendor Panel.</p>

    <!-- (แสดงข้อความ Success/Error) -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="message success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php $validation = session()->getFlashdata('validation'); ?>
    <?php if ($validation): ?>
        <div class="message error">
            <?= $validation->listErrors() ?? 'Please check your input.' ?>
        </div>
    <?php endif; ?>
    <?php $errors = session()->getFlashdata('errors'); ?>
    <?php if ($errors): ?>
        <div class="message error">
            <?= esc($errors) ?>
        </div>
    <?php endif; ?>

    <!-- 2. Form Action ชี้ไปที่ 'vendor/login' -->
    <form action="<?= base_url('vendor/login') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Login as Vendor</button>
    </form>
    
    <div class="auth-links">
        <a href="<?= base_url('login') ?>">Are you a Customer?</a>
    </div>
</div>

<?= $this->endSection() ?>