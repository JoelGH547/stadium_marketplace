<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสนามย่อย</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f1faf8; }
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .btn-mint {
            background-color: #4cb7a5;
            color: white;
            border: none;
        }
        .btn-mint:hover {
            background-color: #3aa18e;
            color: white;
        }
    </style>
</head>
<body>

<?= $this->include('owner/layout/header') ?>

<div class="container mt-5 mb-5" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>✏️ แก้ไขสนามย่อย</h3>
        <a href="<?= base_url('owner/fields/view/'.$sub['stadium_id']) ?>" class="btn btn-secondary">⬅ ย้อนกลับ</a>
    </div>

    <div class="card card-custom p-4">
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('owner/subfields/update/'.$sub['id']) ?>" method="post" enctype="multipart/form-data">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ชื่อสนามย่อย *</label>
                    <input type="text" name="name" class="form-control" value="<?= esc($sub['name']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ราคา (บาท/ชั่วโมง) *</label>
                    <input type="number" name="price" class="form-control" value="<?= esc($sub['price']) ?>" required min="0">
                </div>
            </div>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">สถานะ</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= $sub['status'] == 'active' ? 'selected' : '' ?>>ใช้งานปกติ</option>
                        <option value="maintenance" <?= $sub['status'] == 'maintenance' ? 'selected' : '' ?>>ปิดปรับปรุง</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">รายละเอียดเพิ่มเติม</label>
                <textarea name="description" class="form-control" rows="3"><?= esc($sub['description']) ?></textarea>
            </div>

            <hr>

            <div class="mb-3">
                <label class="form-label fw-bold">รูปภาพปัจจุบัน</label>
                <div class="row g-2">
                    <?php 
                        $images = json_decode($sub['outside_images'] ?? '[]', true);
                        if(empty($images)):
                    ?>
                        <p class="text-muted">ไม่มีรูปภาพ</p>
                    <?php else: ?>
                        <?php foreach($images as $img): ?>
                            <div class="col-6 col-md-3 text-center">
                                <img src="<?= base_url('assets/uploads/fields/'.$img) ?>" class="img-thumbnail mb-2" style="height: 100px; object-fit: cover;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="delete_images[]" value="<?= $img ?>" id="del_<?= $img ?>">
                                    <label class="form-check-label text-danger" for="del_<?= $img ?>">ลบรูปนี้</label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">เพิ่มรูปภาพใหม่</label>
                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                <small class="text-muted">สามารถเลือกได้หลายรูป</small>
            </div>

            <button type="submit" class="btn btn-mint w-100 py-2 mt-3">บันทึกการเปลี่ยนแปลง</button>

        </form>
    </div>
</div>

</body>
</html>
