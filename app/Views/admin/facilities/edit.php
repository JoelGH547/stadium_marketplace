<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h1 class="h3 mb-4 text-gray-800">แก้ไขสิ่งอำนวยความสะดวก</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= base_url('admin/facilities/update/' . $facility['id']) ?>" method="post">
            
            <div class="form-group">
                <label>ชื่อสิ่งอำนวยความสะดวก <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= esc($facility['name']) ?>" required>
            </div>

            <div class="form-group">
                <label>Icon Class (Optional)</label>
                <input type="text" name="icon" class="form-control" value="<?= esc($facility['icon']) ?>">
                <small class="text-muted">ตัวอย่าง: fas fa-wifi</small>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> อัปเดต
                </button>
                <a href="<?= base_url('admin/facilities') ?>" class="btn btn-secondary">
                    ยกเลิก
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>