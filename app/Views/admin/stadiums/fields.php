<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
// เตรียมข้อมูล JSON สำหรับ JS
$fieldFacilities = [];
$fieldImages = [];
$fieldItems = []; 
$db = \Config\Database::connect();

if (!empty($fields)) {
    foreach ($fields as $field) {
        // 1. Facility
        $rawFac = $db->table('stadium_facilities')->where('field_id', $field['id'])->get()->getResultArray();
        $formattedFac = [];
        foreach($rawFac as $r) {
            $facName = $r['name'] ?? null;
            if($facName) $formattedFac[$r['type_id']][] = $facName;
        }
        $fieldFacilities[$field['id']] = json_encode($formattedFac);

        // 2. Items (สินค้าที่เลือกไว้)
        $rawItems = $db->table('field_items')->where('field_id', $field['id'])->get()->getResultArray();
        $formattedItems = [];
        foreach($rawItems as $item) {
            $formattedItems[$item['product_id']] = [
                'price' => $item['custom_price']
            ];
        }
        $fieldItems[$field['id']] = json_encode($formattedItems);
    }
}

$colName   = 'ชื่อสนาม';
$emptyText = 'ยังไม่มีข้อมูลสนามย่อย';
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('admin/stadiums/view/' . $stadium['id']) ?>" class="text-muted text-decoration-none small">
                <i class="fas fa-arrow-left"></i> กลับไปหน้ารายละเอียด
            </a>
            <h3 class="h3 mt-2 text-gray-800 font-weight-bold">
                จัดการสนามย่อย <span class="text-primary">(<?= esc($stadium['name']) ?>)</span>
            </h3>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addFieldModal">
            <i class="fas fa-plus-circle me-1"></i> เพิ่มสนามย่อยใหม่
        </button>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
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
                            <th class="ps-4" width="5%">#</th>
                            <th width="10%">รูปปก</th>
                            <th width="20%"><?= $colName ?></th>
                            <th width="20%">รายละเอียด</th>
                            <th width="15%" class="text-center">ราคา</th>
                            <th width="10%" class="text-center">สถานะ</th>
                            <th class="text-end pe-4">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($fields)): ?>
                            <?php foreach($fields as $index => $field): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <?php 
                                        $imgs = json_decode($field['outside_images'] ?? '[]', true); 
                                        $thumb = !empty($imgs[0]) ? $imgs[0] : null;
                                    ?>
                                    <?php if($thumb): ?>
                                        <img src="<?= base_url('assets/uploads/fields/'.$thumb) ?>" class="rounded border shadow-sm" style="width: 60px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded border text-center d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                            <i class="fas fa-image text-muted small"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-dark"><?= esc($field['name']) ?></td>
                                <td class="text-muted small text-truncate" style="max-width: 150px;"><?= esc($field['description']) ?></td>
                                <td class="text-center">
                                    <div class="text-success fw-bold small">HR: ฿<?= number_format($field['price']) ?></div>
                                    <?php if(!empty($field['price_daily'])): ?>
                                        <div class="text-info small">Day: ฿<?= number_format($field['price_daily']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($field['status'] == 'active'): ?>
                                        <span class="badge bg-success rounded-pill">พร้อมใช้งาน</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger rounded-pill">ปิดปรับปรุง</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-warning btn-sm btn-edit shadow-sm text-dark me-1"
                                            data-bs-toggle="modal" data-bs-target="#editFieldModal"
                                            data-id="<?= $field['id'] ?>"
                                            data-name="<?= esc($field['name']) ?>"
                                            data-price="<?= esc($field['price']) ?>"
                                            data-pricedaily="<?= esc($field['price_daily']) ?>"
                                            data-desc="<?= esc($field['description']) ?>"
                                            data-status="<?= esc($field['status']) ?>"
                                            data-items='<?= $fieldItems[$field['id']] ?? '{}' ?>'>
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <a href="<?= base_url('admin/stadiums/fields/delete/' . $field['id']) ?>" 
                                        class="btn btn-outline-danger btn-sm shadow-sm btn-delete">
                                            <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted"><?= $emptyText ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addFieldModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">เพิ่มสนามย่อยใหม่</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/stadiums/fields/create') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="stadium_id" value="<?= $stadium['id'] ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold"><?= $colName ?> <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="เช่น สนาม A, สนาม B">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">ราคา/ชม. <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">ราคา/วัน</label>
                            <input type="number" name="price_daily" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">รายละเอียดเพิ่มเติม</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="row mb-3 bg-light p-3 rounded mx-0">
                        <div class="col-md-6 mb-2">
                            <label class="fw-bold text-primary"><i class="fas fa-image me-1"></i> รูปปก</label>
                            <input type="file" name="outside_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="fw-bold text-primary"><i class="fas fa-images me-1"></i> รูปภายใน</label>
                            <input type="file" name="inside_images[]" class="form-control" multiple accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3 border p-3 rounded bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold text-success mb-0"><i class="fas fa-box-open me-1"></i> เลือกสินค้าที่วางขาย</label>
                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="collapse" data-bs-target="#quickCreateAdd">
                                <i class="fas fa-plus"></i> สร้างสินค้าใหม่
                            </button>
                        </div>

                        <div class="collapse mb-3 bg-light p-3 rounded border border-success" id="quickCreateAdd">
                            <h6 class="fw-bold text-success mb-3">เพิ่มสินค้าใหม่ลงคลัง</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="small text-muted">ชื่อสินค้า *</label>
                                    <input type="text" id="qc_name_add" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted">หมวดหมู่</label>
                                    <select id="qc_type_add" class="form-select form-select-sm">
                                        <?php if(!empty($facilityTypes)): ?>
                                            <?php foreach($facilityTypes as $t): ?>
                                                <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted">ราคาขาย (บาท) *</label>
                                    <input type="number" id="qc_price_add" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted">หน่วยนับ</label>
                                    <input type="text" id="qc_unit_add" class="form-control form-control-sm" placeholder="เช่น ขวด, ชิ้น">
                                </div>
                                <div class="col-md-5">
                                    <label class="small text-muted">รูปภาพสินค้า</label>
                                    <input type="file" id="qc_image_add" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <div class="col-md-4">
                                     <label class="small text-muted">รายละเอียด</label>
                                     <input type="text" id="qc_desc_add" class="form-control form-control-sm" placeholder="คำอธิบายสั้นๆ">
                                </div>
                                <div class="col-12 mt-2 text-end">
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="collapse" data-bs-target="#quickCreateAdd">ปิด</button>
                                    <button type="button" class="btn btn-sm btn-success px-4 btn-save-qc" data-context="add"><i class="fas fa-save me-1"></i> บันทึกสินค้า</button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-sm table-borderless align-middle mb-0">
                                <thead class="text-muted small border-bottom">
                                    <tr>
                                        <th width="5%">เลือก</th>
                                        <th width="10%">รูป</th>
                                        <th width="35%">สินค้า</th>
                                        <th width="20%">ราคามาตรฐาน</th>
                                        <th width="30%">ราคาขายจริง (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody id="prodBodyAdd">
                                    <?php if(!empty($products)): ?>
                                        <?php foreach($products as $p): ?>
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input chk-product" type="checkbox" 
                                                           name="items[<?= $p['id'] ?>][selected]" value="1"
                                                           id="add_p_<?= $p['id'] ?>"
                                                           data-target="add_price_<?= $p['id'] ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <?php if($p['image']): ?>
                                                    <img src="<?= base_url('assets/uploads/items/'.$p['image']) ?>" width="30" height="30" class="rounded object-fit-cover">
                                                <?php else: ?>
                                                    <div class="bg-light rounded text-center" style="width:30px;height:30px;"><i class="fas fa-image text-muted small"></i></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><label class="form-check-label small fw-bold mb-0" for="add_p_<?= $p['id'] ?>"><?= esc($p['name']) ?></label></td>
                                            <td class="text-muted small">฿<?= number_format($p['price']) ?></td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">฿</span>
                                                    <input type="number" step="0.01" name="items[<?= $p['id'] ?>][price]" 
                                                           id="add_price_<?= $p['id'] ?>" class="form-control form-control-sm" 
                                                           placeholder="<?= number_format($p['price']) ?>" disabled>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr id="row-no-data-add"><td colspan="5" class="text-center py-3 text-muted">ไม่มีสินค้าในคลัง</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="active">พร้อมใช้งาน (Active)</option>
                            <option value="maintenance">ปิดปรับปรุง (Maintenance)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editFieldModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">แก้ไขข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/stadiums/fields/update') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="stadium_id" value="<?= $stadium['id'] ?>">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold"><?= $colName ?></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">ราคา/ชม.</label>
                            <input type="number" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">ราคา/วัน</label>
                            <input type="number" name="price_daily" id="edit_price_daily" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">รายละเอียด</label>
                        <textarea name="description" id="edit_desc" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row mb-3 bg-light p-3 rounded mx-0">
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">เปลี่ยนรูปปก</label>
                            <input type="file" name="outside_image" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">เพิ่มรูปภายใน</label>
                            <input type="file" name="inside_images[]" class="form-control form-control-sm" multiple accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3 border p-3 rounded bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold mb-0 text-success"><i class="fas fa-box-open me-1"></i> สินค้าที่วางขาย</label>
                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="collapse" data-bs-target="#quickCreateEdit">
                                <i class="fas fa-plus"></i> สร้างสินค้าใหม่
                            </button>
                        </div>

                        <div class="collapse mb-3 bg-light p-3 rounded border border-success" id="quickCreateEdit">
                            <h6 class="fw-bold text-success mb-3">เพิ่มสินค้าใหม่ลงคลัง</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="small text-muted">ชื่อสินค้า *</label>
                                    <input type="text" id="qc_name_edit" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted">หมวดหมู่</label>
                                    <select id="qc_type_edit" class="form-select form-select-sm">
                                        <?php if(!empty($facilityTypes)): ?>
                                            <?php foreach($facilityTypes as $t): ?>
                                                <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted">ราคาขาย (บาท) *</label>
                                    <input type="number" id="qc_price_edit" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted">หน่วยนับ</label>
                                    <input type="text" id="qc_unit_edit" class="form-control form-control-sm" placeholder="เช่น ขวด, ชิ้น">
                                </div>
                                <div class="col-md-5">
                                    <label class="small text-muted">รูปภาพสินค้า</label>
                                    <input type="file" id="qc_image_edit" class="form-control form-control-sm" accept="image/*">
                                </div>
                                <div class="col-md-4">
                                     <label class="small text-muted">รายละเอียด</label>
                                     <input type="text" id="qc_desc_edit" class="form-control form-control-sm" placeholder="คำอธิบายสั้นๆ">
                                </div>
                                <div class="col-12 mt-2 text-end">
                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="collapse" data-bs-target="#quickCreateEdit">ปิด</button>
                                    <button type="button" class="btn btn-sm btn-success px-4 btn-save-qc" data-context="edit"><i class="fas fa-save me-1"></i> บันทึกสินค้า</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-sm table-borderless align-middle mb-0">
                                <thead class="text-muted small border-bottom">
                                    <tr>
                                        <th width="5%">เลือก</th>
                                        <th width="10%">รูป</th>
                                        <th width="35%">สินค้า</th>
                                        <th width="20%">ราคามาตรฐาน</th>
                                        <th width="30%">ราคาขายจริง (บาท)</th>
                                    </tr>
                                </thead>
                                <tbody id="prodBodyEdit">
                                    <?php if(!empty($products)): ?>
                                        <?php foreach($products as $p): ?>
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input chk-product-edit" type="checkbox" 
                                                           name="items[<?= $p['id'] ?>][selected]" value="1"
                                                           id="edit_p_<?= $p['id'] ?>"
                                                           data-prod-id="<?= $p['id'] ?>"
                                                           data-target="edit_price_val_<?= $p['id'] ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <?php if($p['image']): ?>
                                                    <img src="<?= base_url('assets/uploads/items/'.$p['image']) ?>" width="30" height="30" class="rounded object-fit-cover">
                                                <?php else: ?>
                                                    <div class="bg-light rounded text-center" style="width:30px;height:30px;"><i class="fas fa-image text-muted small"></i></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><label class="form-check-label small fw-bold mb-0" for="edit_p_<?= $p['id'] ?>"><?= esc($p['name']) ?></label></td>
                                            <td class="text-muted small">฿<?= number_format($p['price']) ?></td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">฿</span>
                                                    <input type="number" step="0.01" name="items[<?= $p['id'] ?>][price]" 
                                                           id="edit_price_val_<?= $p['id'] ?>" class="form-control form-control-sm" 
                                                           placeholder="<?= number_format($p['price']) ?>" disabled>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr id="row-no-data-edit"><td colspan="5" class="text-center py-3 text-muted">ไม่มีสินค้าในคลัง</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">สถานะ</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="active">พร้อมใช้งาน (Active)</option>
                            <option value="maintenance">ปิดปรับปรุง (Maintenance)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-warning">บันทึกแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stadiumId = <?= $stadium['id'] ?>;

    function toggleProductInput(chk) {
        // อัปเดตการค้นหา input ในกรณีที่เป็น Input Group
        const inputId = chk.dataset.target;
        const input = document.getElementById(inputId);

        if(input) {
            input.disabled = !chk.checked;
            if (!chk.checked) input.value = ''; 
        }
    }
    
    document.querySelectorAll('.chk-product, .chk-product-edit').forEach(chk => {
        chk.addEventListener('change', function() { toggleProductInput(this); });
    });

    // ✅ ฟังก์ชัน Quick Create (รองรับทั้ง Add และ Edit)
    document.querySelectorAll('.btn-save-qc').forEach(btn => {
        btn.addEventListener('click', function() {
            const context = this.dataset.context; // 'add' or 'edit'
            
            // รับค่าจาก input ตาม context
            const nameInput = document.getElementById('qc_name_' + context);
            const priceInput = document.getElementById('qc_price_' + context);
            const typeInput  = document.getElementById('qc_type_' + context);
            const unitInput  = document.getElementById('qc_unit_' + context);
            const descInput  = document.getElementById('qc_desc_' + context);
            const imageInput = document.getElementById('qc_image_' + context);

            if(!nameInput.value || !priceInput.value) { alert('กรุณากรอกชื่อและราคา'); return; }

            const formData = new FormData();
            formData.append('stadium_id', stadiumId);
            formData.append('name', nameInput.value);
            formData.append('price', priceInput.value);
            formData.append('type_id', typeInput.value);
            formData.append('unit', unitInput.value);
            formData.append('description', descInput.value);
            
            if(imageInput.files[0]) {
                formData.append('image', imageInput.files[0]);
            }

            // ส่ง AJAX
            fetch('<?= base_url('admin/vendor-items/quick-create') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // หาตารางปลายทาง (prodBodyAdd หรือ prodBodyEdit)
                    const tableBody = document.getElementById('prodBody' + (context === 'add' ? 'Add' : 'Edit')); 
                    if(tableBody) {
                        const noRow = document.getElementById('row-no-data-' + context);
                        if(noRow) noRow.remove();

                        let imgHtml = '<div class="bg-light rounded text-center" style="width:30px;height:30px;"><i class="fas fa-image text-muted small"></i></div>';
                        if(data.image) {
                            imgHtml = `<img src="<?= base_url('assets/uploads/items/') ?>${data.image}" width="30" height="30" class="rounded object-fit-cover">`;
                        }

                        // สร้าง ID ที่ไม่ซ้ำกัน
                        const chkId = `${context}_p_${data.id}`;
                        const priceInputId = (context === 'add') ? `add_price_${data.id}` : `edit_price_val_${data.id}`;
                        const chkClass = (context === 'add') ? 'chk-product' : 'chk-product-edit';

                        const newRow = `
                            <tr>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input ${chkClass}" type="checkbox" 
                                               name="items[${data.id}][selected]" value="1" checked
                                               id="${chkId}"
                                               data-target="${priceInputId}">
                                    </div>
                                </td>
                                <td>${imgHtml}</td>
                                <td><label class="form-check-label small fw-bold">${data.name}</label></td>
                                <td class="text-muted small">฿${data.price}</td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">฿</span>
                                        <input type="number" step="0.01" name="items[${data.id}][price]" 
                                               id="${priceInputId}" class="form-control form-control-sm" 
                                               value="${data.price}">
                                    </div>
                                </td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('afterbegin', newRow);
                        
                        // Bind Event ให้ Checkbox ใหม่ทำงานได้
                        const newChk = document.getElementById(chkId);
                        newChk.addEventListener('change', function() { toggleProductInput(this); });
                    }

                    // Reset Form
                    nameInput.value = ''; priceInput.value = ''; unitInput.value = '';
                    descInput.value = ''; imageInput.value = '';
                    
                    var bsCollapse = new bootstrap.Collapse(document.getElementById('quickCreate' + (context === 'add' ? 'Add' : 'Edit')), {toggle: false});
                    bsCollapse.hide();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            });
        });
    });

    // ... (ส่วนจัดการ Edit Modal: Fill Data) ...
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('edit_price_daily').value = this.dataset.pricedaily;
            document.getElementById('edit_desc').value = this.dataset.desc;
            document.getElementById('edit_status').value = this.dataset.status;

            // Reset Products Checkbox
            document.querySelectorAll('.chk-product-edit').forEach(chk => {
                chk.checked = false;
                toggleProductInput(chk);
            });

            const itemData = JSON.parse(this.dataset.items || '{}'); 
            for(const [prodId, info] of Object.entries(itemData)) {
                const chk = document.getElementById('edit_p_' + prodId);
                if(chk) {
                    chk.checked = true;
                    toggleProductInput(chk);
                    const input = document.getElementById('edit_price_val_' + prodId);
                    if(input && info.price) input.value = parseFloat(info.price); 
                }
            }
        });
    });

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