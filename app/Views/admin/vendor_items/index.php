<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="h3 text-gray-800 font-weight-bold">จัดการสินค้า/บริการเสริม (Vendor Items)</h3>
            <p class="text-muted small mb-0">รายการสินค้าที่จะแสดงให้ลูกค้าเลือกซื้อเพิ่มตอนจองสนาม</p>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="fas fa-plus-circle me-1"></i> เพิ่มสินค้าใหม่
        </button>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-circle me-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="5%">#</th>
                            <th width="10%">รูปภาพ</th>
                            <th width="20%">ชื่อสินค้า</th>
                            <th width="15%">หมวดหมู่</th>
                            <th width="15%">ราคา</th>
                            <th width="15%">เจ้าของร้าน (Vendor)</th>
                            <th width="10%">สถานะ</th>
                            <th class="text-end pe-4" width="10%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($items)): ?>
                            <?php foreach($items as $index => $item): ?>
                            <tr>
                                <td class="ps-4 text-muted fw-bold"><?= $index + 1 ?></td>
                                
                                <td>
                                    <?php if($item['image']): ?>
                                        <img src="<?= base_url('assets/uploads/items/'.$item['image']) ?>" 
                                             class="rounded border shadow-sm" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="fw-bold text-dark"><?= esc($item['name']) ?></div>
                                    <div class="small text-muted text-truncate" style="max-width: 150px;">
                                        <?= esc($item['description']) ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= esc($item['type_name'] ?? 'ไม่ระบุ') ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="text-success fw-bold">฿<?= number_format($item['price']) ?></span>
                                    <span class="text-muted small">/ <?= esc($item['unit']) ?></span>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 24px; height: 24px; font-size: 10px;">
                                            <i class="fas fa-store"></i>
                                        </div>
                                        <span class="small fw-bold"><?= esc($item['vendor_name']) ?></span>
                                    </div>
                                </td>

                                <td>
                                    <?php if($item['status'] == 'active'): ?>
                                        <span class="badge bg-success rounded-pill">พร้อมขาย</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill">ไม่พร้อม</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end pe-4">
                                    <button class="btn btn-warning btn-sm btn-edit shadow-sm text-dark" 
                                            data-bs-toggle="modal" data-bs-target="#editItemModal"
                                            data-id="<?= $item['id'] ?>"
                                            data-name="<?= esc($item['name']) ?>"
                                            data-vendor="<?= $item['vendor_id'] ?>"
                                            data-type="<?= $item['facility_type_id'] ?>"
                                            data-price="<?= $item['price'] ?>"
                                            data-unit="<?= $item['unit'] ?>"
                                            data-desc="<?= esc($item['description']) ?>"
                                            data-status="<?= $item['status'] ?>">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <a href="<?= base_url('admin/vendor-items/delete/'.$item['id']) ?>" 
                                       class="btn btn-outline-danger btn-sm shadow-sm btn-delete"
                                       onclick="return confirm('ยืนยันที่จะลบสินค้านี้? ข้อมูลจะกู้คืนไม่ได้!');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 text-gray-300"></i><br>
                                    ยังไม่มีข้อมูลสินค้า
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('admin/vendor-items/store') ?>" method="post" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">เพิ่มสินค้าใหม่</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold">เจ้าของร้าน (Vendor) <span class="text-danger">*</span></label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="">-- กรุณาเลือกร้านค้า --</option>
                            <?php foreach($vendors as $v): ?>
                                <option value="<?= $v['id'] ?>"><?= $v['vendor_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">เลือก Vendor ที่เป็นเจ้าของสินค้านี้</small>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">ชื่อสินค้า <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="เช่น น้ำดื่มสิงห์ 600ml">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">หมวดหมู่</label>
                            <select name="facility_type_id" class="form-select">
                                <option value="">-- เลือกหมวดหมู่ (ถ้ามี) --</option>
                                <?php foreach($types as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">ราคาขาย (บาท) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" required min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">หน่วยนับ <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control" required placeholder="เช่น ขวด, ชิ้น, คู่">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">รายละเอียดเพิ่มเติม</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="เช่น น้ำดื่มเย็นเจี๊ยบ หรือ ให้เช่าไม้แบดพร้อมลูก"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">รูปภาพสินค้า</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="active" selected>พร้อมขาย (Active)</option>
                            <option value="inactive">ไม่พร้อมขาย/ของหมด (Inactive)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?= base_url('admin/vendor-items/update') ?>" method="post" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">แก้ไขสินค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label class="fw-bold">เจ้าของร้าน (Vendor)</label>
                        <select name="vendor_id" id="edit_vendor" class="form-select" required>
                            <?php foreach($vendors as $v): ?>
                                <option value="<?= $v['id'] ?>"><?= $v['vendor_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">ชื่อสินค้า</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">หมวดหมู่</label>
                            <select name="facility_type_id" id="edit_type" class="form-select">
                                <option value="">-- เลือกหมวดหมู่ --</option>
                                <?php foreach($types as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">ราคา</label>
                            <input type="number" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">หน่วยนับ</label>
                            <input type="text" name="unit" id="edit_unit" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">รายละเอียด</label>
                        <textarea name="description" id="edit_desc" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3 bg-light p-3 rounded border">
                        <label class="fw-bold mb-2">เปลี่ยนรูปภาพ (ถ้ามี)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted d-block mt-1">* หากไม่อัปโหลดรูปใหม่ ระบบจะใช้รูปเดิม</small>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">สถานะ</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="active">พร้อมขาย</option>
                            <option value="inactive">ไม่พร้อมขาย</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning">บันทึกการแก้ไข</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script ดึงข้อมูลจากปุ่ม Edit มาใส่ใน Modal แก้ไข
    const editModal = document.getElementById('editItemModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        
        // ดึงค่าจาก data attributes ใส่ input
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_name').value = btn.dataset.name;
        document.getElementById('edit_vendor').value = btn.dataset.vendor;
        document.getElementById('edit_type').value = btn.dataset.type || ''; 
        document.getElementById('edit_price').value = btn.dataset.price;
        document.getElementById('edit_unit').value = btn.dataset.unit;
        document.getElementById('edit_desc').value = btn.dataset.desc;
        document.getElementById('edit_status').value = btn.dataset.status;
    });
});
</script>
<?= $this->endSection() ?>