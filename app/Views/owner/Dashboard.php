<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แดชบอร์ดเจ้าของสนาม</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    .facility-card img {
        width: 180px;
        height: 130px;
        object-fit: cover;
        border-radius: 6px;
    }
    .facility-card {
        border: 1px solid #eee;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
        transition: 0.2s;
    }
    .facility-card:hover {
        background: #f8f9fc;
    }
</style>
</head>

<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2">
    <h4 class="mb-0 fw-bold">eBooking • Owner</h4>

    <div>
        <span class="me-3">👋 สวัสดี <?= session()->get('owner_name'); ?></span>
        <a href="<?= base_url('owner/logout') ?>" class="btn btn-danger btn-sm">ออกจากระบบ</a>
    </div>
</nav>

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">สนามของคุณ</h4>
        <a href="<?= base_url('owner/fields/step1') ?>" class="btn btn-primary">➕ เพิ่มสนามใหม่</a>
    </div>

    <hr>

    <!-- Search Filters -->
<form method="get" action="<?= base_url('owner/dashboard') ?>" class="row g-3 mb-4">

    <!-- ชนิดสนาม -->
    <div class="col-md-3">
        <label class="form-label">ชนิดสนาม</label>
        <select name="category" class="form-select">
            <option value="">ทั้งหมด</option>
            <?php foreach($categories as $c): ?>
                <option value="<?= $c['id'] ?>"
                    <?= (isset($_GET['category']) && $_GET['category'] == $c['id']) ? 'selected' : '' ?>>
                    <?= esc($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- จังหวัด -->
    <div class="col-md-3">
        <label class="form-label">จังหวัด</label>
        <select name="province" class="form-select">
            <option value="">ทั้งหมด</option>
            <?php foreach($provinces as $p): ?>
                <option value="<?= $p ?>" 
                    <?= (isset($_GET['province']) && $_GET['province'] == $p) ? 'selected' : '' ?>>
                    <?= $p ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- ราคาเริ่มต้น -->
    <div class="col-md-2">
        <label class="form-label">ราคาเริ่มต้น</label>
        <input type="number" name="price_min" class="form-control"
               value="<?= $_GET['price_min'] ?? '' ?>">
    </div>

    <!-- ราคาสูงสุด -->
    <div class="col-md-2">
        <label class="form-label">ราคาสูงสุด</label>
        <input type="number" name="price_max" class="form-control"
               value="<?= $_GET['price_max'] ?? '' ?>">
    </div>

    <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">ค้นหา</button>
    </div>
</form>



    <!-- List -->
    <h5 class="fw-bold mb-3"><?= count($stadiums) ?> สนาม</h5>

    <?php if (empty($stadiums)): ?>
        <div class="alert alert-info text-center">
            ยังไม่มีสนามในระบบ 
        </div>
    <?php endif; ?>

    <?php foreach ($stadiums as $s): ?>

        <?php
            $imgList = json_decode($s['outside_images'], true);
            $thumbnail = $imgList[0] ?? 'no-image.jpg';
        ?>

        <div class="facility-card d-flex">

            <!-- รูป -->
            <img src="<?= base_url('uploads/stadiums/outside/'.$thumbnail) ?>" class="me-3">

            <div class="w-100">

                <!-- ชื่อสนาม -->
                <h5 class="mb-1"><?= esc($s['name']) ?></h5>

                <!-- ที่อยู่ -->
                <p class="mb-1 text-muted">
                    📍 <?= esc($s['province']) ?>, <?= esc($s['address']) ?>
                </p>

                <!-- อีเมล & เบอร์ -->
                <p class="mb-2 text-muted">
                    ✉️ <?= esc($s['contact_email']) ?>  
                    • 📞 <?= esc($s['contact_phone']) ?>
                </p>

                <p class="mb-3 text-muted">
                    <?= esc($s['price']) ?>/ชั่วโมง  
                </p>

                <div>
                    <a href="<?= base_url('owner/fields/edit/'.$s['id']) ?>" class="btn btn-primary btn-sm me-2">แก้ไข</a>
                    <a href="#" class="btn btn-outline-danger btn-sm">ลบ</a>
                </div>

            </div>

        </div>

    <?php endforeach; ?>

</div>

</body>
</html>
