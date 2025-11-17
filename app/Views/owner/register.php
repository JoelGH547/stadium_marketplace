<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>สมัครสมาชิกเจ้าของสนาม</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f4f6f9;
    }
    .register-box {
      max-width: 650px;
      margin: 40px auto;
      padding: 30px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h3 {
      font-weight: bold;
    }
  </style>
</head>
<body>

<div class="register-box">

  <h3 class="text-center mb-4">สมัครสมาชิกเจ้าของสนาม</h3>

  <!-- แสดง Error -->
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

    <!-- บัญชี -->
    <h5 class="fw-bold mt-3">ข้อมูลบัญชี</h5>

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


    <!-- ข้อมูลเจ้าของสนาม -->
    <h5 class="fw-bold mt-4">ข้อมูลเจ้าของสนาม</h5>

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


    <!-- ข้อมูลธุรกิจ -->
    <h5 class="fw-bold mt-4">ข้อมูลเพิ่มเติม</h5>

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
            