<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แดชบอร์ดเจ้าของสนาม</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url('assets/vendors_css/owner_dashboard.css') ?>" rel="stylesheet">
</head>

<body>
<?= $this->include('owner/layout/header') ?>
<?= $this->include('owner/layout/sidebarfields') ?>

<div id="dashboard-wrapper" class="dashboard-wrapper">

    <div class="container py-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 page-header">
            <div>
                <h3 class="fw-bold text-mint mb-0">🏟 สนามของคุณ</h3>
                <p class="text-muted mb-0">จัดการข้อมูลสนามและรายการจองได้ที่นี่</p>
            </div>
            <a href="<?= base_url('owner/fields/step1') ?>" class="btn btn-mint shadow-sm">
                ➕ เพิ่มสนามใหม่
            </a>
        </div>

        <!-- Search Filters -->
        <div class="search-box">
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

                <!-- ค้นหาชื่อสนาม -->
                <div class="col-md-4">
                    <label class="form-label">ค้นหาชื่อสนาม</label>
                    <input type="text" name="keyword" class="form-control"
                        value="<?= esc($_GET['keyword'] ?? '') ?>" placeholder="ระบุชื่อสนาม...">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-mint w-100">🔍 ค้นหา</button>
                </div>

            </form>
        </div>

        <!-- Result Count -->
        <div class="d-flex align-items-center mb-3">
            <h5 class="fw-bold text-secondary mb-0">ผลการค้นหา: <span class="text-mint"><?= count($stadiums) ?></span> รายการ</h5>
        </div>

        <!-- No Data -->
        <?php if (empty($stadiums)): ?>
            <div class="alert alert-info text-center p-5 rounded-4 shadow-sm">
                <h4 class="fw-bold text-muted">ยังไม่มีสนามในระบบ</h4>
                <p>เริ่มสร้างสนามแรกของคุณเพื่อเปิดรับการจองได้เลย!</p>
                <a href="<?= base_url('owner/fields/step1') ?>" class="btn btn-mint mt-2">สร้างสนามใหม่</a>
            </div>
        <?php endif; ?>

        <!-- Stadium List -->
        <?php foreach ($stadiums as $s): ?>
            <?php
                $imgList = json_decode($s['outside_images'], true);
                $thumbnail = $imgList[0] ?? 'no-image.jpg';
            ?>

            <div class="facility-card">

                <!-- รูป -->
                <div class="flex-shrink-0">
                    <img src="<?= base_url('uploads/stadiums/outside/'.$thumbnail) ?>" alt="<?= esc($s['name']) ?>">
                </div>

                <div class="flex-grow-1 ms-4">

                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-1 fw-bold text-dark"><?= esc($s['name']) ?></h4>
                            <span class="badge bg-light text-dark border mb-2">
                                <?= esc($s['category_name'] ?? 'สนามทั่วไป') ?>
                            </span>
                        </div>

                    </div>

                    <p class="mb-2 text-muted">
                        📍 <?= esc($s['province']) ?>, <?= esc($s['address']) ?>
                    </p>

                    <div class="d-flex gap-3 text-muted small mb-3">
                        <span>✉️ <?= esc($s['contact_email']) ?></span>
                        <span>📞 <?= esc($s['contact_phone']) ?></span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="<?= base_url('owner/fields/view/'.$s['id']) ?>"
                            class="btn btn-mint btn-sm px-3">
                            🔍 รายละเอียด
                        </a>

                        <a href="<?= base_url('owner/fields/edit/'.$s['id']) ?>"
                           class="btn btn-outline-secondary btn-sm px-3">
                           ✏️ แก้ไข
                        </a>

                        <a href="<?= base_url('owner/fields/delete/'.$s['id']) ?>" 
                            class="btn btn-outline-danger btn-sm px-3"
                            onclick="return confirm('ยืนยันการลบสนามนี้? การลบนี้ไม่สามารถย้อนกลับได้!')">
                            🗑️ ลบ
                        </a>

                    </div>

                </div>

            </div>
        <?php endforeach; ?>

    </div>
</div>
</body>
</html>
