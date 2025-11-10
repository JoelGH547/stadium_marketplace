<?= $this->extend('auth/layout') ?>

<!-- 2. บอกว่าเนื้อหาส่วนนี้ จะถูก "ฉีด" เข้าไปใน 'content' ของ Layout -->
<?= $this->section('content') ?>

<div class="auth-card">
    <h1>Login</h1>

    <!-- (แสดงข้อความ Success/Error) -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="message success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="message error">
            <?= session()->getFlashdata('error') ?>
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

    <!-- Form Login -->
    <form action="<?= base_url('login') ?>" method="post">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <div class="auth-links">
        <a href="<?= base_url('register') ?>">Don't have an account? Register</a>
    </div>
</div>

<!-- 3. จบ Section Content -->
<?= $this->endSection() ?>