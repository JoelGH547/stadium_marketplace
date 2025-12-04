<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สนามย่อย - <?= esc($stadium['name']) ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f3fdfa;
}

/* Card หลัก */
.card-mint {
    border-left: 5px solid #00c389;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

/* ปุ่มหลัก */
.btn-mint {
    background: #00c389;
    color: white;
    border: none;
}
.btn-mint:hover {
    background: #00a577;
}

/* รายการสนามย่อย */
.sub-card {
    border: 1px solid #d9f7ee;
    border-radius: 10px;
    padding: 15px;
    background: #ffffff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
.sub-card:hover {
    background: #eafff5;
    transition: 0.2s;
}
</style>
</head>

<body>

<?= $this->include('owner/layout/header') ?>
<?= $this->include('owner/layout/sidebarfields') ?>

<div id="dashboard-wrapper" class="dashboard-wrapper">

<div class="container py-4" style="max-width: 900px;">

    <h3 class="fw-bold text-success mb-2">⚽ สนามย่อยของ <?= esc($stadium['name']) ?></h3>
    <p class="text-muted mb-4">จัดการสนามย่อยและราคาย่อยต่อสนาม</p>

    <!-- Flash message -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card p-4 mb-4 shadow-sm">

    <h5 class="fw-bold mb-3 text-success">➕ เพิ่มสนามย่อย</h5>

    <form method="post" 
          action="<?= base_url('owner/fields/subfields/'.$stadium['id'].'/create') ?>"
          enctype="multipart/form-data">

        <!-- ชื่อ -->
        <div class="mb-3">
            <label class="form-label">ชื่อสนามย่อย *</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <!-- ราคา -->
        <div class="mb-3">
            <label class="form-label">ราคา / ชั่วโมง *</label>
            <input type="number" name="price" class="form-control" min="0" required>
        </div>



        <!-- รายละเอียด -->
        <div class="mb-3">
            <label class="form-label">รายละเอียดสนาม</label>
            <textarea name="description" rows="3" class="form-control"></textarea>
        </div>

        <!-- UPLOAD -->
        <div class="mb-3">
            <label class="form-label">รูปภาพสนามย่อย (เลือกได้หลายรูป)</label>
            <input type="file" name="images[]" multiple class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-mint w-100">บันทึกสนามย่อย</button>
    </form>

</div>


    <!-- รายการสนามย่อย -->
    <h4 class="fw-bold text-success mb-3">📋 รายการสนามย่อย</h4>

    <?php if(empty($subfields)): ?>
        <div class="alert alert-info text-center">ยังไม่มีสนามย่อย</div>
    <?php else: ?>

        <?php foreach($subfields as $sf): ?>
            <div class="sub-card mb-3 d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold mb-1"><?= esc($sf['name']) ?></h5>
                    <p class="text-muted mb-0"><?= esc($sf['price']) ?> บาท/ชั่วโมง</p>
                </div>

                <a href="<?= base_url('owner/fields/subfields/'.$stadium['id'].'/delete/'.$sf['id']) ?>"
                   onclick="return confirm('ต้องการลบสนามย่อยนี้?')"
                   class="btn btn-outline-danger btn-sm">ลบ</a>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

</div>

</body>
</html>
