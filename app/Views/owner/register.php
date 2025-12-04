<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>สมัครสมาชิกเจ้าของสนาม</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background:
        linear-gradient(rgba(0, 180, 150, 0.4), rgba(0, 180, 150, 0.4)),
        url('https://images.pexels.com/photos/399187/pexels-photo-399187.jpeg') center/cover no-repeat;
      background-attachment: fixed;
    }
    body::before {
      content: "";
      position: fixed;
      inset: 0;
      background: rgba(0, 255, 180, 0.25);
      backdrop-filter: blur(2px);
      z-index: 0;
    }
    .register-box {
      position: relative;
      z-index: 2;
      max-width: 750px;
      margin: 40px auto;
      padding: 30px;
      background: rgba(255, 255, 255, 0.88);
      border-radius: 12px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.25);
    }
    h3, h5 {
      font-weight: bold;
      color: #097969;
    }
  </style>
</head>

<body>

<div class="register-box">

  <h3 class="text-center mb-4">สมัครสมาชิกเจ้าของสนาม</h3>

  <?php if (session()->getFlashdata('errors')) : ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach (session()->getFlashdata('errors') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="<?= base_url('owner/register') ?>" enctype="multipart/form-data">

    <!-- Account -->
    <h5 class="mt-3">ข้อมูลบัญชี</h5>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Username *</label>
        <input type="text" name="username" class="form-control" required>
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">อีเมล *</label>
        <input type="email" name="email" class="form-control" required>
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
        <input type="text" name="vendor_name" class="form-control" required>
      </div>

      <div class="col-md-6 mb-3">
        <label class="form-label">นามสกุล</label>
        <input type="text" name="lastname" class="form-control">
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label">เพศ</label>
        <select name="gender" class="form-control">
          <option value="">ไม่ระบุ</option>
          <option value="male">ชาย</option>
          <option value="female">หญิง</option>
          <option value="other">อื่น ๆ</option>
        </select>
      </div>

      <div class="col-md-4 mb-3">
        <label class="form-label">วันเกิด</label>
        <input type="date" name="birthday" class="form-control">
      </div>

      <div class="col-md-4 mb-3">
        <label class="form-label">รูปโปรไฟล์</label>
        <input type="file" name="profile_image" class="form-control">
      </div>
    </div>

    <!-- Address -->
    <h5 class="mt-4">ที่อยู่</h5>

    <div class="mb-3">
      <label class="form-label">ที่อยู่</label>
      <input type="text" name="address" class="form-control">
    </div>

    <div class="row">
      <div class="col-md-4 mb-3">
        <label class="form-label">ตำบล</label>
        <input type="text" name="subdistrict" class="form-control">
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">อำเภอ</label>
        <input type="text" name="district" class="form-control">
      </div>
      <div class="col-md-4 mb-3">
        <label class="form-label">จังหวัด *</label>
        <select name="province" class="form-control" required>
          <option value="">เลือกจังหวัด</option>
          <?php foreach ($provinces as $p): ?>
            <option value="<?= $p ?>"><?= $p ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">รหัสไปรษณีย์</label>
      <input type="text" name="zipcode" class="form-control">
    </div>

    <!-- Contact -->
    <h5 class="mt-4">ข้อมูลติดต่อ</h5>

    <div class="mb-3">
      <label class="form-label">เบอร์โทร *</label>
      <input type="text" name="phone_number" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">LINE ID</label>
      <input type="text" name="line_id" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">Facebook URL</label>
      <input type="text" name="facebook_url" class="form-control">
    </div>

    <!-- Verification -->
    <h5 class="mt-4">ยืนยันตัวตน</h5>

    <div class="mb-3">
      <label class="form-label">เลขบัตรประชาชน</label>
      <input type="text" name="citizen_id" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">บัตรประชาชน (ภาพ)</label>
      <input type="file" name="id_card_image" class="form-control">
    </div>

    <div class="mb-3">
      <label class="form-label">สมุดบัญชี (ภาพ)</label>
      <input type="file" name="bank_book_image" class="form-control">
    </div>

    <!-- Bank -->
    <h5 class="mt-4">ธนาคาร</h5>

    <div class="mb-3">
      <label class="form-label">เลขบัญชีธนาคาร</label>
      <input type="text" name="bank_account" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 mt-3">
      สมัครสมาชิก
    </button>

  </form>

</div>

</body>
</html>
