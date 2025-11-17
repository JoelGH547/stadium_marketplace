<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2 class="text-center mb-4">Customer Login</h2>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php $errors = session()->getFlashdata('errors'); ?>
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <?= esc($errors) ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/login') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <hr>

        <div class="text-center">
            <p>ยังไม่มีบัญชี? <a href="<?= base_url('/register') ?>">สมัครสมาชิก (Register)</a></p>
            <p><a href="<?= base_url('/admin/login') ?>">ไปหน้า Admin Login</a></p>
        </div>
    </div>

</body>
</html>