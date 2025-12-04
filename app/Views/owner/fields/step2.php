<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ขั้นตอนที่ 2: ข้อมูลสนาม</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f1faf8;
    }

    .form-box {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      border-top: 5px solid #4cb7a5;
    }

    h3 {
      color: #2a8f7a;
      font-weight: 700;
    }

    .btn-primary {
      background: #4cb7a5;
      border: none;
    }

    .btn-primary:hover {
      background: #3aa18e;
    }

    .btn-back {
      background: #d8f7ef;
      color: #2a8f7a;
      border: 1px solid #bfeee4;
    }

    .btn-back:hover {
      background: #c8f1e6;
      color: #237968;
    }
  </style>

</head>

<body>
<?= $this->include('owner/layout/header') ?>
<div class="container mt-5" style="max-width: 700px;">

  <!-- Back -->
  <a href="<?= base_url('owner/fields/step1') ?>" class="btn btn-back mb-3">
    ⬅ ย้อนกลับ
  </a>

  <div class="form-box">

    <h3 class="mb-3">ขั้นตอนที่ 2: ข้อมูลพื้นฐานของสนาม</h3>
    <p class="text-muted mb-4">กรอกข้อมูลสนามของคุณให้ครบถ้วน</p>

    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('owner/fields/step2') ?>">

      <!-- ชื่อสนาม -->
      <div class="mb-3">
        <label class="form-label">ชื่อสนาม *</label>
        <input type="text" name="name" class="form-control" required>
      </div>



      <!-- เวลาเปิดปิด -->
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">เวลาเปิด *</label>
          <input type="time" name="open_time" class="form-control" required>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">เวลาปิด *</label>
          <input type="time" name="close_time" class="form-control" required>
        </div>
      </div>

      <!-- อีเมลติดต่อ -->
      <div class="mb-3">
        <label class="form-label">อีเมลสำหรับติดต่อ (ถ้ามี)</label>
        <input type="email" name="contact_email" class="form-control">
      </div>

      <!-- เบอร์โทรศัพท์ -->
      <div class="mb-3">
        <label class="form-label">เบอร์ติดต่อ *</label>
        <input 
            type="tel"
            name="contact_phone"
            class="form-control"
            required
            maxlength="10"
            pattern="[0-9]{10}"
            title="กรุณากรอกเบอร์โทร 10 หลัก"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
        >
      </div>

      <!-- รายละเอียด -->
      <div class="mb-3">
        <label class="form-label">รายละเอียดสนาม (ถ้ามี)</label>
        <textarea name="description" class="form-control" rows="4"></textarea>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2">ถัดไป</button>

    </form>

  </div>

</div>

</body>
</html>
