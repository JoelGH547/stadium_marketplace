<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แดชบอร์ดเจ้าของสนาม</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: #f3fdfa; /* พื้นหลังมิ้นอ่อน */
    }
    /* Navbar */
    .navbar {
        background: #00c389 !important;
        padding: 12px 25px;
        color: white;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
    }
    .navbar h4 {
        font-weight: bold;
    }

    /* ปุ่ม */
    .btn-mint {
        background: #00c389;
        border-color: #00c389;
        color: white;
    }
    .btn-mint:hover {
        background: #00af77;
        border-color: #00af77;
    }

    /* Card สนาม */
    .facility-card {
        border-left: 6px solid #00c389;
        border-radius: 12px;
        background: white;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        transition: 0.2s;
    }
    .facility-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 14px rgba(0,0,0,0.10);
    }
    .facility-card img {
        width: 180px;
        height: 130px;
        object-fit: cover;
        border-radius: 10px;
    }

    /* ฟอร์มค้นหา */
    .search-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border-left: 5px solid #00c389;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }

</style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar">
    <h4 class="mb-0">eBooking • Owner</h4>

    <div>
        <span class="me-3">👋 สวัสดี <?= session()->get('owner_name'); ?></span>
        <a href="<?= base_url('owner/logout') ?>" class="btn btn-light btn-sm">ออกจากระบบ</a>
    </div>
</nav>

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-success">สนามของคุณ</h4>
        <a href="<?= base_url('owner/fields/step1') ?>" class="btn btn-mint">➕ เพิ่มสนามใหม่</a>
    </div>

    <!-- Search Filters -->
    <div class="search-box mb-4">
        <form method="get" action="<?= base_url('owner/dashboard') ?>" class="row g-3">

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

            <!-- ราคา -->
            <div class="col-md-2">
                <label class="form-label">ราคาเริ่มต้น</label>
                <input type="number" name="price_min" class="form-control"
                       value="<?= $_GET['price_min'] ?? '' ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label">ราคาสูงสุด</label>
                <input type="number" name="price_max" class="form-control"
                       value="<?= $_GET['price_max'] ?? '' ?>">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-mint w-100">ค้นหา</button>
            </div>

        </form>
    </div>

    <!-- Result Count -->
    <h5 class="fw-bold text-success mb-3"><?= count($stadiums) ?> สนามที่พบ</h5>

    <!-- No Data -->
    <?php if (empty($stadiums)): ?>
        <div class="alert alert-info text-center">
            ยังไม่มีสนามในระบบ
        </div>
    <?php endif; ?>

    <!-- Stadium List -->
    <?php foreach ($stadiums as $s): ?>
        <?php
            $imgList = json_decode($s['outside_images'], true);
            $thumbnail = $imgList[0] ?? 'no-image.jpg';
        ?>

        <div class="facility-card d-flex">

            <!-- รูป -->
            <img src="<?= base_url('uploads/stadiums/outside/'.$thumbnail) ?>" class="me-3">

            <div class="w-100">

                <h5 class="mb-1 fw-bold text-success"><?= esc($s['name']) ?></h5>

                <p class="mb-1 text-muted">
                    📍 <?= esc($s['province']) ?>, <?= esc($s['address']) ?>
                </p>

                <p class="mb-1 text-muted">
                    ✉️ <?= esc($s['contact_email']) ?>
                    • 📞 <?= esc($s['contact_phone']) ?>
                </p>

                <p class="fw-bold text-success">
                    <?= esc($s['price']) ?> บาท / ชั่วโมง
                </p>

                <div>
                    <a href="<?= base_url('owner/fields/edit/'.$s['id']) ?>"
                       class="btn btn-mint btn-sm me-2">แก้ไข</a>

                    <a href="#" class="btn btn-outline-danger btn-sm">ลบ</a>
                </div>

            </div>

        </div>
    <?php endforeach; ?>

</div>

</body>
</html>
