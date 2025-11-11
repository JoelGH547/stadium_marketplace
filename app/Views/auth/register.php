<?= $this->extend('auth/layout') ?>

<!-- 2. เริ่ม Section Content -->
<?= $this->section('content') ?>

<div class="auth-card">
    <h1>Register Customer Account</h1>
    <p>Sign up to start making reservations.</p>

    <!-- (แสดงข้อความ Success/Error) -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="message success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php $validation = session()->getFlashdata('validation'); ?>
    <?php if ($validation): ?>
        <div class="message error">
            <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($validation->getErrors() as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form Register -->
    <form action="<?= base_url('register') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" value="<?= old('username') ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="pass_confirm">Confirm Password:</label>
            <input type="password" id="pass_confirm" name="pass_confirm" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    
    <div class="auth-links">
        <a href="<?= base_url('login') ?>">Already have an account? Login</a>
    </div>
</div>

<!-- 3. จบ Section Content -->
<?= $this->endSection() ?>