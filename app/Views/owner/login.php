<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เข้าสู่ระบบเจ้าของสนาม</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        margin: 0;
        height: 100vh;
        overflow: hidden;
        background-color: #e8fff4; /* mint background */
        font-family: 'Prompt', sans-serif;
    }

    /* ซ้าย: ฟอร์ม */
    .login-container {
        background: white;
        padding: 40px;
        border-radius: 12px;
        width: 380px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .mint-btn {
        background-color: #28c7a5;
        border: none;
    }
    .mint-btn:hover {
        background-color: #22b094;
    }

    /* ขวา: ภาพ + ข้อความ */
    .right-box {
        background: url('https://images.pexels.com/photos/399187/pexels-photo-399187.jpeg') center/cover no-repeat;
        height: 100vh;
        color: white;
        padding: 80px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }


    /* Overlay สีเขียวมิ้นโปร่ง */
    .overlay {
        background: rgba(0, 161, 125, 0.55);
        position: absolute;
        inset: 0;
    }

    .right-text {
        position: relative;
        z-index: 2;
    }

    .green-title {
        font-size: 42px;
        font-weight: bold;
        line-height: 1.3;
    }

    @media (max-width: 992px) {
        .right-box { display: none; }
        body { overflow: auto; background: #e8fff4; }
    }
</style>
</head>

<body>

<div class="row g-0 h-100">

    <!-- Left: Form Login -->
    <div class="col-lg-4 d-flex align-items-center justify-content-center">
        <div class="login-container">

            <h3 class="text-center mb-4" style="color:#1e9b85;">เข้าสู่ระบบเจ้าของสนาม</h3>

            <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('owner/login') ?>">

                <div class="mb-3">
                    <label class="form-label">อีเมลหรือชื่อผู้ใช้</label>
                    <input type="text" name="login" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button class="btn mint-btn text-white w-100 py-2 mt-2">เข้าสู่ระบบ</button>

                <div class="text-center mt-3 d-flex flex-column gap-2">
                    <div>
                        <span>ยังไม่มีบัญชีใช่ไหม?</span>
                        <a href="<?= base_url('owner/register') ?>" style="color:#22b094;">สมัครสมาชิก</a>
                    </div>
                    <div class="mt-2 small">
                        <a href="<?= base_url('sport') ?>" class="text-muted text-decoration-none">กลับหน้าสำหรับลูกค้า</a>
                    </div>
                </div>

            </form>

        </div>
    </div>

    <!-- Right: Image + Text -->
    <div class="col-lg-8 position-relative right-box">
        <div class="overlay"></div>

        <div class="right-text">
            <h1 class="green-title">เข้าสู่ระบบเพื่อ<br>จัดการสนามของคุณ</h1>
            <p class="fs-5 mt-3">ระบบจองสนามของคุณจะถูกแสดงให้ลูกค้าเห็น<br>และสามารถจองได้ทันทีผ่านระบบของเรา</p>
        </div>

    </div>

</div>

</body>
</html>
