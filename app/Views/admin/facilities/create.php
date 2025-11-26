<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h1 class="h3 mb-4 text-gray-800">เพิ่มสิ่งอำนวยความสะดวก</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= base_url('admin/facilities/store') ?>" method="post">
            
            <div class="form-group">
                <label>ชื่อสิ่งอำนวยความสะดวก <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="เช่น ไดร์เป่าผม, ตู้กดน้ำ">
            </div>
            
            <div class="form-group">
                <label>Icon Class (Optional)</label>
                <input type="text" name="icon" class="form-control" placeholder="เช่น fas fa-wind">
                <small class="text-muted">ใช้ Class จาก FontAwesome หรือ <a href="https://fontawesome.com/v5/search?m=free" target="_blank">คลิกที่นี่เพื่อค้นหาไอคอน</a></small>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> บันทึก
                </button>
                <a href="<?= base_url('admin/facilities') ?>" class="btn btn-secondary">
                    ยกเลิก
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>