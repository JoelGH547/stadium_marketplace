<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ข้อมูลสนาม: <?= esc($stadium['name']) ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url('assets/vendors_css/owner_stadium_view.css') ?>" rel="stylesheet">
</head>

<body class="bg-light">
<?= $this->include('owner/layout/header') ?>
<?= $this->include('owner/layout/sidebarfields') ?>

<div id="dashboard-wrapper" class="dashboard-wrapper">

    <div class="container mt-4">
        
        <!-- Hero Banner -->
        <?= $this->include('owner/fields/components/hero') ?>

        <!-- ข้อมูลหลักสนาม -->
        <div class="card card-mint p-4 mb-4">
            <h5 class="fw-bold mb-3">ข้อมูลพื้นฐาน</h5>

            <p><strong>ประเภท:</strong> <?= esc($stadium['category_name']) ?></p>
            <p><strong>จังหวัด:</strong> <?= esc($stadium['province']) ?></p>

            <p><strong>เวลาเปิด:</strong> <?= esc($stadium['open_time']) ?> น.</p>
            <p><strong>เวลาปิด:</strong> <?= esc($stadium['close_time']) ?> น.</p>
            <p><strong>ที่อยู่:</strong> <?= esc($stadium['address']) ?></p>

            <a href="<?= base_url('owner/fields/edit/'.$stadium['id']) ?>" 
               class="btn btn-mint mt-3">✏️ แก้ไขข้อมูลสนาม</a>
        </div>

        <!-- สนามย่อย -->
        <?= $this->include('owner/fields/components/subfield_list') ?>
        
        <!-- บริการ/สินค้าเพิ่มเติม -->
        <?= $this->include('owner/fields/components/item_list') ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Define SITE_URL for external JS
    const SITE_URL = '<?= base_url() ?>/';
</script>
<script src="<?= base_url('assets/vendors_js/owner_stadium_view.js') ?>"></script>

</body>
</html>
