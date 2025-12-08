<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f8f9fa;
        }
        .register-card {
            width: 100%;
            max-width: 450px;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="register-card">
        <h2 class="text-center mb-4">Customer Register</h2>

        <!-- (แสดงข้อความ Error ถ้ามี) -->
        <?php $errors = session()->getFlashdata('errors'); ?>
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <strong>พบข้อผิดพลาด:</strong>
                <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

      
        <form action="<?= base_url('/register') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <small>ต้องมีอย่างน้อย 6 ตัวอักษร</small>
            </div>

            <div class="form-group">
                <label for="pass_confirm">Confirm Password:</label>
                <input type="password" class="form-control" id="pass_confirm" name="pass_confirm" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>

        <hr>

        <div class="text-center">
            <p>มีบัญชีอยู่แล้ว? <a href="<?= base_url('/login') ?>">Login ที่นี่</a></p>
        </div>
    </div>  

</body>
</html>