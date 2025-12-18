<?= $this->extend('layouts/LoginAdmin_auth') ?>
<?= $this->section('content') ?>

<div class="auth-card">
    <h1>Admin Login</h1>
    <p>Please log in to access the Admin Panel.</p>

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

    <form action="<?= base_url('admin/login') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Login as Admin</button>
    </form>

    <div class="auth-links text-sm text-gray-500 text-center">
        <a href="<?= base_url('/login') ?>">ไปหน้าเข้าสู่ระบบสำหรับลูกค้า (Customer Login)</a>
    </div>
</div>

<?= $this->endSection() ?>