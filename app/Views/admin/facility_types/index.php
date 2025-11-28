<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <h3 class="h3 mb-3 text-gray-800">หมวดหมู่สิ่งอำนวยความสะดวก (Facility Types)</h3>
    <p class="text-muted">กำหนดหมวดหมู่หลัก เพื่อให้ Vendor เพิ่มรายการของเข้าไปในแต่ละหมวด</p>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-plus-circle me-2"></i> เพิ่มหมวดหมู่ใหม่
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/facility-types/create') ?>" method="post">
                        <div class="mb-3">
                            <label class="fw-bold text-dark">ชื่อหมวดหมู่</label>
                            <input type="text" name="name" class="form-control" placeholder="เช่น บริการ, อุปกรณ์กีฬา" required>
                            <small class="text-muted">ชื่อนี้จะแสดงให้ Vendor เลือก</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-dark">รายการหมวดหมู่ทั้งหมด</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" width="10%">#</th>
                                    <th width="70%">ชื่อหมวดหมู่</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($types)): ?>
                                    <?php foreach($types as $index => $t): ?>
                                    <tr>
                                        <td class="ps-4 text-muted"><?= $index + 1 ?></td>
                                        <td class="fw-bold text-primary"><?= esc($t['name']) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('admin/facility-types/delete/'.$t['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger btn-delete shadow-sm">
                                                <i class="fas fa-trash-alt"></i> ลบ
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center py-5 text-muted">ยังไม่มีหมวดหมู่</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "หมวดหมู่นี้จะหายไปจากระบบ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if(result.isConfirmed) window.location.href = href;
            });
        });
    });
</script>

<?= $this->endSection() ?>