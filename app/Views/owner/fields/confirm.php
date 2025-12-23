<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ขั้นตอนที่ 5: ยืนยันข้อมูลสนาม</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f1faf8;
    }

    h3 {
      color: #2a8f7a;
      font-weight: 700;
    }

    .confirm-box {
      background: white;
      padding: 30px;
      border-radius: 15px;
      border-top: 5px solid #4cb7a5;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .section-title {
      font-weight: 700;
      color: #2a8f7a;
      border-left: 4px solid #4cb7a5;
      padding-left: 10px;
      margin-bottom: 12px;
      margin-top: 25px;
    }

    .info-line strong {
      color: #256e5d;
    }

    .img-thumb {
      width: 160px;
      height: 120px;
      object-fit: cover;
      border-radius: 10px;
      margin: 6px;
      border: 2px solid #cdeee7;
      box-shadow: 0 2px 5px rgba(0,0,0,0.08);
    }

    .btn-back {
      background: #d8f7ef;
      color: #2a8f7a;
      border: 1px solid #bfeee4;
    }
    .btn-back:hover {
      background: #c2f0e5;
      color: #1f6f5f;
    }

    .btn-save {
      background: #4cb7a5;
      border: none;
    }
    .btn-save:hover {
      background: #3aa18e;
    }
  </style>
</head>

<body>
<?= $this->include('owner/layout/header') ?>
<div class="container mt-5 mb-5" style="max-width: 900px;">

  <h3 class="fw-bold mb-4">ขั้นตอนที่ 5: ยืนยันข้อมูลสนาม</h3>

  <div class="confirm-box">

    <!-- ข้อมูลพื้นฐาน -->
    <h5 class="section-title">ข้อมูลพื้นฐาน</h5>

    <p class="info-line"><strong>ประเภทสนาม (ID):</strong> <?= session('category_id') ?></p>
    <p class="info-line"><strong>ชื่อสนาม:</strong> <?= session('name') ?></p>
    <p class="info-line"><strong>ราคา/ชั่วโมง:</strong> <?= session('price') ?> บาท</p>
    <p class="info-line"><strong>เวลาเปิด:</strong> <?= session('open_time') ?></p>
    <p class="info-line"><strong>เวลาปิด:</strong> <?= session('close_time') ?></p>
    <p class="info-line"><strong>รายละเอียด:</strong> <?= session('description') ?></p>

    <!-- การติดต่อ -->
    <h5 class="section-title">ข้อมูลการติดต่อ</h5>

    <p class="info-line"><strong>Email สนาม:</strong> <?= session('contact_email') ?: '-' ?></p>
    <p class="info-line"><strong>เบอร์โทร:</strong> <?= session('contact_phone') ?></p>

    <!-- Address -->
    <h5 class="section-title">ที่อยู่ + พิกัดสนาม</h5>

    <p class="info-line"><strong>จังหวัด:</strong> <?= session('province') ?></p>
    <p class="info-line"><strong>ที่อยู่:</strong> <?= session('address') ?></p>
    <p class="info-line"><strong>Latitude:</strong> <?= session('lat') ?></p>
    <p class="info-line"><strong>Longitude:</strong> <?= session('lng') ?></p>

    <?php if(session('map_link')): ?>
      <p class="info-line">
        <strong>Google Maps:</strong>
        <a href="<?= session('map_link') ?>" target="_blank" class="text-primary">เปิดแผนที่</a>
      </p>
    <?php endif; ?>


    <!-- รูปภาพ -->
    <h5 class="section-title">รูปภายนอกสนาม</h5>
    <div class="d-flex flex-wrap">
      <?php foreach(json_decode(session('outside_images')) as $img): ?>
        <img src="<?= base_url('assets/uploads/stadiums/'.$img) ?>" class="img-thumb">
      <?php endforeach; ?>
    </div>

    <h5 class="section-title">รูปภายในสนาม</h5>
    <div class="d-flex flex-wrap">
      <?php foreach(json_decode(session('inside_images')) as $img): ?>
        <img src="<?= base_url('assets/uploads/stadiums/'.$img) ?>" class="img-thumb">
      <?php endforeach; ?>
    </div>


    <!-- ปุ่ม -->
    <div class="mt-4 d-flex justify-content-between">

      <a href="<?= base_url('owner/fields/step4') ?>" class="btn btn-back px-4">
        ⬅ ย้อนกลับ
      </a>

      <form method="post" action="<?= base_url('owner/fields/store') ?>">
        <button type="submit" class="btn btn-save px-4 text-white">
          บันทึกสนาม ✔
        </button>
      </form>

    </div>

  </div>

</div>

</body>
</html>
