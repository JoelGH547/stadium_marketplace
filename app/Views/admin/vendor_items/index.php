<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h3 mb-0 text-gray-800">จัดการสินค้า/บริการเสริม (Vendor Items)</h3>
        <!-- Add Button Removed as requested -->
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="5%">ID</th>
                            <th width="10%">รูปภาพ</th>
                            <th width="20%">ชื่อสินค้า</th>
                            <th width="25%">รายละเอียด</th>
                            <th width="10%">ราคา</th>
                            <th width="10%">หน่วย</th>
                            <th width="15%"><i class="fas fa-map-marker-alt me-1"></i> พื้นที่สนาม (หมวดหมู่)</th> 
                            <th width="10%" class="text-center">สถานะ</th>
                            <th class="text-end pe-4">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)) : ?>
                            <?php foreach ($items as $item) : ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted"><?= $item['id'] ?></td>
                                    <td>
                                        <?php if ($item['image']) : ?>
                                            <img src="<?= base_url('assets/uploads/items/' . $item['image']) ?>" class="rounded border shadow-sm" width="50" height="50" style="object-fit: cover;">
                                        <?php else : ?>
                                            <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted small" style="width: 50px; height: 50px;">
                                                No Pic
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($item['name']) ?></div>
                                    </td>
                                    <td>
                                        <div class="text-muted small text-truncate" style="max-width: 200px;">
                                            <?= esc($item['description']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">฿<?= number_format($item['price']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= esc($item['unit']) ?></span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="fw-bold text-primary"><?= esc($item['field_name']) ?></div>
                                            <div class="text-muted"><i class="fas fa-tag me-1"></i><?= esc($item['facility_type_name']) ?></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item['status'] == 'active') : ?>
                                            <span class="badge bg-success rounded-pill">Active</span>
                                        <?php else : ?>
                                            <span class="badge bg-secondary rounded-pill">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= base_url('admin/vendor-items/delete/' . $item['id']) ?>" 
                                           class="btn btn-outline-danger btn-sm shadow-sm btn-delete"
                                           title="ลบรายการ">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <p>ยังไม่มีข้อมูลสินค้าในคลัง</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handling Delete Confirmation
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "ข้อมูลและรูปภาพทั้งหมดจะหายไป กู้คืนไม่ได้!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = href;
            });
        });
    });
});
</script>
<?= $this->endSection() ?>