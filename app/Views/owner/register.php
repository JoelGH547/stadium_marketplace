<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>สมัครสมาชิกเจ้าของสนาม</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* ===== BG + Green Mint Overlay ===== */
    body {
      background: 
        linear-gradient(rgba(0, 180, 150, 0.4), rgba(0, 180, 150, 0.4)),
      url('https://images.pexels.com/photos/399187/pexels-photo-399187.jpeg') center/cover no-repeat;
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }


    body::before {
      content: "";
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 255, 180, 0.25); /* เขียวมิ้นแบบใส */
      backdrop-filter: blur(2px);
      z-index: 0;
    }

    .register-box {
      position: relative;
      z-index: 2;
      max-width: 650px;
      margin: 40px auto;
      padding: 30px;
      background: rgba(255, 255, 255, 0.88);
      border-radius: 12px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.25);
      backdrop-filter: blur(4px);
    }

    h3, h5 {
      font-weight: bold;
      color: #097969; /* เขียวมิ้นเข้ม */
    }

    button.btn-primary {
      background: #11c293;
      border-color: #11c293;
    }

    button.btn-primary:hover {
      background: #0ea57d;
    }
  </style>
</head>

<body>

<div class="register-box">

  <h3 class="text-center mb-4">สมัครสมาชิกเจ้าของสนาม</h3>

  <!-- ERROR -->
  <?php if (session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="<?= base_url('owner/register') ?>">

    <!-- Account -->
    <h5 class="mt-3">ข้อมูลบัญชี</h5>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Username *</label>
        <input type="text" name="username" class="form-control" value="<?= old('username') ?>" required>
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">อีเมล *</label>
        <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">รหัสผ่าน *</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">ยืนยันรหัสผ่าน *</label>
        <input type="password" name="password_confirm" class="form-control" required>
      </div>
    </div>

    <!-- Owner Info -->
    <h5 class="mt-4">ข้อมูลเจ้าของสนาม</h5>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">ชื่อเจ้าของ *</label>
        <input type="text" name="vendor_name" class="form-control" value="<?= old('vendor_name') ?>" required>
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">นามสกุล</label>
        <input type="text" name="lastname" class="form-control" value="<?= old('lastname') ?>">
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">วันเกิด</label>
        <input type="date" name="birthday" class="form-control" value="<?= old('birthday') ?>">
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">จังหวัด *</label>
        <select name="province" class="form-control" required>
          <option value="">-- เลือกจังหวัด --</option>
          <?php foreach ($provinces as $p): ?>
          <option value="<?= $p ?>" <?= old('province') == $p ? 'selected' : '' ?>>
            <?= $p ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- More Info -->
    <h5 class="mt-4">ข้อมูลเพิ่มเติม</h5>

    <div class="mb-3">
      <label class="form-label">เบอร์โทรศัพท์ *</label>
      <input type="number" name="phone_number" class="form-control" value="<?= old('phone_number') ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">เลขประจำตัวผู้เสียภาษี (ถ้ามี)</label>
      <input type="number" name="tax_id" class="form-control" value="<?= old('tax_id') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">บัญชีธนาคาร (ถ้ามี)</label>
      <input type="number" name="bank_account" class="form-control" value="<?= old('bank_account') ?>">
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 mt-3">
      สมัครสมาชิก
    </button>

  </form>

</div>

</body>
</html>
