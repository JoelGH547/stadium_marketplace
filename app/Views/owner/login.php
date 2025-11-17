<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เข้าสู่ระบบเจ้าของสนาม</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

  <div class="card p-4 shadow" style="width:400px;">
    <h3 class="text-center mb-3">เข้าสู่ระบบเจ้าของสนาม</h3>

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
      <div class="text-center mt-3">
            <span>ยังไม่มีบัญชีใช่ไหม?</span>
            <a href="<?= base_url('owner/register') ?>">สมัครสมาชิก</a>
          </div><br>

      <button type="submit" class="btn btn-primary w-100">เข้าสู่ระบบ</button>
    </form>

    
  </div>

</body>
</html>
