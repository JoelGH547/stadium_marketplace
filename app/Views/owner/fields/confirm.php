<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ยืนยันข้อมูลสนาม</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .img-thumb {
      width: 160px;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      margin: 5px;
      border: 1px solid #ddd;
    }
  </style>
</head>

<body class="bg-light">

<div class="container mt-5 mb-5" style="max-width: 900px;">

  <h3 class="fw-bold mb-3">ขั้นตอนที่ 5: ยืนยันข้อมูลสนาม</h3>
  <p class="text-muted">ตรวจสอบความถูกต้องก่อนบันทึก</p>

  <div class="card p-4 shadow-sm">

    <h5 class="fw-bold mb-3">ข้อมูลพื้นฐาน</h5>
    <p><strong>ประเภทสนาม (ID):</strong> <?= session('category_id') ?></p>
    <p><strong>ชื่อสนาม:</strong> <?= session('name') ?></p>
    <p><strong>ราคา/ชั่วโมง:</strong> <?= session('price') ?> บาท</p>
    <p><strong>เวลาเปิด:</strong> <?= session('open_time') ?></p>
    <p><strong>เวลาปิด:</strong> <?= session('close_time') ?></p>
    <p><strong>คำอธิบาย:</strong> <?= session('description') ?></p>

    <hr>

    <h5 class="fw-bold mb-3">ข้อมูลการติดต่อ</h5>
    <p><strong>Email สนาม:</strong> <?= session('contact_email') ?: '-' ?></p>
    <p><strong>เบอร์สนาม:</strong> <?= session('contact_phone') ?></p>

    <hr>

    <h5 class="fw-bold mb-3">ที่อยู่ + ตำแหน่งสนาม</h5>
    <p><strong>จังหวัด:</strong> <?= session('province') ?></p>
    <p><strong>ที่อยู่:</strong> <?= session('address') ?></p>
    <p><strong>Latitude:</strong> <?= session('lat') ?></p>
    <p><strong>Longitude:</strong> <?= session('lng') ?></p>

    <?php if(session('map_link')): ?>
      <p><strong>ลิ้ง Google Maps:</strong> <a href="<?= session('map_link') ?>" target="_blank">เปิดที่นี่</a></p>
    <?php endif; ?>

    <hr>

    <h5 class="fw-bold mb-3">รูปภายนอกสนาม</h5>
<div class="d-flex flex-wrap">
  <?php foreach(json_decode(session('outside_images')) as $img): ?>
    <img src="<?= base_url('uploads/stadiums/outside/'.$img) ?>" class="img-thumb">
  <?php endforeach; ?>
</div>

<h5 class="fw-bold mt-4 mb-3">รูปภายในสนาม</h5>
<div class="d-flex flex-wrap">
  <?php foreach(json_decode(session('inside_images')) as $img): ?>
    <img src="<?= base_url('uploads/stadiums/inside/'.$img) ?>" class="img-thumb">
  <?php endforeach; ?>
</div>


    <!-- ปุ่ม -->
    <div class="mt-4 d-flex justify-content-between">
      <a href="<?= base_url('owner/fields/step4') ?>" class="btn btn-secondary">⬅ ย้อนกลับ</a>

      <form method="post" action="<?= base_url('owner/fields/store') ?>">
        <button type="submit" class="btn btn-success">บันทึกสนาม ✔</button>
      </form>
    </div>

  </div>

</div>

</body>
</html>
