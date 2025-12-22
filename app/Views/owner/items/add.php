<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เพิ่มสินค้า / บริการ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    .card-mint {
        border-left: 5px solid #00c389;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }
</style>
</head>

<body class="bg-light">

<div class="container mt-4" style="max-width: 700px;">
    <div class="card card-mint p-4">

        <h4 class="fw-bold mb-3 text-success">➕ เพิ่มสินค้า / บริการ</h4>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('owner/items/store/' . $stadium_id) ?>" method="post" enctype="multipart/form-data">

            <!-- สินค้า -->
            <div class="mb-3">
                <label class="form-label">ชื่อสินค้า / บริการ *</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <!-- สนามย่อย (Field) -->
            <div class="mb-3">
                <label class="form-label">สนามย่อย *</label>
                <select name="field_id" class="form-select" required>
                    <option value="">เลือกสนามย่อย</option>
                    <?php if(!empty($subfields)): ?>
                        <?php foreach($subfields as $sf): ?>
                            <option value="<?= $sf['id'] ?>"><?= esc($sf['name']) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>ไม่มีสนามย่อย (กรุณาเพิ่มสนามย่อยก่อน)</option>
                    <?php endif; ?>
                </select>
                <div class="form-text text-muted">สินค้า/บริการนี้จะผูกกับสนามย่อยที่เลือกเท่านั้น</div>
            </div>

            <!-- ประเภท -->
            <div class="mb-3">
                <label class="form-label">ประเภท *</label>
                <select name="type_id" class="form-select" required>
                    <option value="">เลือกประเภท</option>
                    <?php foreach($facility_types as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- ราคา -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ราคา *</label>
                    <input type="number" name="price" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">หน่วย *</label>
                    <select name="unit" class="form-select" required>
                        <option value="ชิ้น">ชิ้น</option>
                        <option value="แพ็ก">แพ็ก</option>
                        <option value="ชม.">ชม.</option>
                        <option value="วัน">วัน</option>
                        <option value="คน">คน</option>
                    </select>
                </div>
            </div>



            <!-- รายละเอียด -->
            <div class="mb-3">
                <label class="form-label">รายละเอียด</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <!-- รูป -->
            <div class="mb-3">
                <label class="form-label">รูปสินค้า</label>
                <input type="file" name="image" class="form-control">
            </div>

            <button class="btn btn-success w-100 py-2">บันทึกสินค้า</button>
        </form>

    </div>
</div>

</body>
</html>
