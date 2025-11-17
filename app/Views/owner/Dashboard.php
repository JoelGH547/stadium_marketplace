<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แดชบอร์ดเจ้าของสนาม</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .card-menu {
        transition: 0.2s;
        cursor: pointer;
    }
    .card-menu:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
  </style>

</head>

<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark px-4">
  <span class="navbar-brand mb-0 h4">
    👋 สวัสดี, <?= session()->get('owner_name'); ?>
  </span>
  <a href="<?= base_url('owner/logout') ?>" class="btn btn-danger btn-sm">ออกจากระบบ</a>
</nav>

<div class="container mt-4">

  <h3 class="fw-bold">แดชบอร์ดเจ้าของสนาม</h3>
  <p class="text-muted">เลือกเมนูที่คุณต้องการใช้งาน</p>

  <div class="row mt-4">

    <!-- เพิ่มสนาม -->
    <div class="col-md-4 mb-4">
      <a href="<?= base_url('owner/fields/step1') ?>" class="text-decoration-none">
        <div class="card card-menu p-4 text-center shadow-sm">
          <h4>➕ เพิ่มสนาม</h4>
          <p class="text-muted">สร้างสนามใหม่เพื่อให้ลูกค้าสามารถจองได้</p>
        </div>
      </a>
    </div>

    <!-- รายการจอง -->
    <div class="col-md-4 mb-4">
      <a href="#" class="text-decoration-none">
        <div class="card card-menu p-4 text-center shadow-sm">
          <h4>📅 การจองสนาม</h4>
          <p class="text-muted">ดูการจองทั้งหมดตามวันและเวลา</p>
        </div>
      </a>
    </div>

    <!-- จัดการข้อมูลส่วนตัว -->
    <div class="col-md-4 mb-4">
      <a href="#" class="text-decoration-none">
        <div class="card card-menu p-4 text-center shadow-sm">
          <h4>👤 ข้อมูลส่วนตัว</h4>
          <p class="text-muted">แก้ไขข้อมูลเจ้าของสนาม</p>
        </div>
      </a>
    </div>

  </div>

</div>

</body>
</html>
