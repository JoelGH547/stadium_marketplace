<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ขั้นตอนที่ 2: ข้อมูลสนาม</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 700px;">
  <a href="<?= base_url('owner/fields/step1') ?>" class="btn btn-secondary mb-3">
    ⬅ ย้อนกลับ
</a>


  <h3 class="fw-bold mb-3">ขั้นตอนที่ 2: ข้อมูลพื้นฐานของสนาม</h3>
  <p class="text-muted">กรอกข้อมูลสนามของคุณให้ครบถ้วน</p>

  <?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
  <?php endif; ?>

  <form method="post" action="<?= base_url('owner/fields/step2') ?>">

    <!-- ชื่อสนาม -->
    <div class="mb-3">
      <label class="form-label">ชื่อสนาม *</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <!-- ราคา -->
    <div class="mb-3">
      <label class="form-label">ราคา / ชั่วโมง *</label>
      <input type="number" name="price" class="form-control" min="0" required>
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

    <button type="submit" class="btn btn-primary w-100">ถัดไป</button>
  </form>

</div>

</body>
</html>
