<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$colName   = 'ชื่อสนาม';
$emptyText = 'ยังไม่มีข้อมูลสนามย่อย';
?>

<?php
// เตรียมข้อมูล mapping สำหรับสิ่งอำนวยความสะดวกและสินค้าในสนามย่อย
$fieldFacilities = $fieldFacilities ?? [];
$fieldProducts   = $fieldProducts ?? [];
$facilityTypes   = $facilityTypes ?? [];

$facilityTypesById = [];
foreach ($facilityTypes as $ft) {
    $facilityTypesById[$ft['id']] = $ft;
}
?>

<style>
    .field-actions-wrapper {
        position: relative;
        gap: 0.25rem;
    }

    .field-category-wrapper {
        position: relative;
        display: inline-block;
    }

    .field-category-panel {
        position: absolute;
        top: 110%;
        right: 0;
        width: 260px;
        background: #ffffff;
        border-radius: 0.5rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
        box-shadow: 0 0.75rem 1.5rem rgba(15, 23, 42, 0.18);
        padding: 0.75rem;
        opacity: 0;
        pointer-events: none;
        transform: translateY(4px);
        transition: opacity 0.15s ease, transform 0.15s ease;
        z-index: 50;
    }

    .field-category-wrapper:hover .field-category-panel {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
    }

    .field-category-panel .small-scroll {
        max-height: 220px;
        overflow-y: auto;
    }
</style>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('admin/stadiums/view/' . $stadium['id']) ?>"
                class="text-muted text-decoration-none small">
                <i class="fas fa-arrow-left"></i> กลับไปหน้ารายละเอียด
            </a>
            <a href="<?= base_url('admin/stadiums') ?>" class="text-muted ms-4" style="text-decoration: none;">
            <i class="fas fa-home"></i> กลับไปหน้าหลัก
        </a>
    </div>


            <h3 class="h3 mt-2 text-gray-800 font-weight-bold">
                จัดการสนาม <span class="text-primary">(<?= esc($stadium['name']) ?>)</span>
            </h3>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addFieldModal">
            <i class="fas fa-plus-circle me-1"></i> เพิ่มสนามย่อยใหม่
        </button>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
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
                        <?php if (!empty($fields)): ?>
                            <?php foreach ($fields as $index => $field): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted"><?= $index + 1 ?></td>
                                    <td>
                                        <?php
                                        $imgs = json_decode($field['outside_images'] ?? '[]', true);
                                        $thumb = !empty($imgs[0]) ? $imgs[0] : null;
                                        ?>
                                        <?php if ($thumb): ?>
                                            <img src="<?= base_url('assets/uploads/fields/' . $thumb) ?>"
                                                class="rounded border shadow-sm"
                                                style="width: 60px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded border text-center d-flex align-items-center justify-content-center"
                                                style="width: 60px; height: 40px;">
                                                <i class="fas fa-image text-muted small"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold text-dark"><?= esc($field['name']) ?></td>
                                    <td class="text-muted small text-truncate" style="max-width: 150px;">
                                        <?= esc($field['description']) ?></td>
                                    <td class="text-center">
                                        <div class="text-success fw-bold small">HR: ฿<?= number_format($field['price']) ?></div>
                                        <?php if (!empty($field['price_daily'])): ?>
                                            <div class="text-info small">Day: ฿<?= number_format($field['price_daily']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($field['status'] == 'active'): ?>
                                            <span class="badge bg-success rounded-pill">พร้อมใช้งาน</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger rounded-pill">ปิดปรับปรุง</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex align-items-center">
                                            <button type="button"
                                                class="btn btn-outline-info btn-sm shadow-sm me-1 field-facility-manage-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#fieldFacilitiesModal_<?= $field['id'] ?>"
                                                title="จัดการหมวดหมู่และสินค้า">
                                                <i class="fas fa-box"></i>
                                            </button>

                                            <button class="btn btn-warning btn-sm btn-edit shadow-sm text-dark me-1"
                                                data-bs-toggle="modal" data-bs-target="#editFieldModal"
                                                data-id="<?= $field['id'] ?>" data-name="<?= esc($field['name']) ?>"
                                                data-price="<?= esc($field['price']) ?>"
                                                data-pricedaily="<?= esc($field['price_daily']) ?>"
                                                data-desc="<?= esc($field['description']) ?>"
                                                data-status="<?= esc($field['status']) ?>">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <a href="<?= base_url('admin/stadiums/fields/delete/' . $field['id']) ?>"
                                                class="btn btn-outline-danger btn-sm shadow-sm btn-delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted"><?= $emptyText ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php if (!empty($fields)): ?>
    <?php foreach ($fields as $field): ?>
        <?php
        $fieldId = $field['id'];
        $facilitiesForField = $fieldFacilities[$fieldId] ?? [];
        $activeFacilitiesByType = [];
        if (!empty($facilitiesForField)) {
            foreach ($facilitiesForField as $sfRow) {
                $activeFacilitiesByType[$sfRow['facility_type_id']] = $sfRow;
            }
        }
        ?>
        <div class="modal fade" id="fieldFacilitiesModal_<?= $fieldId ?>" tabindex="-1"
            aria-labelledby="fieldFacilitiesModalLabel_<?= $fieldId ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="fieldFacilitiesModalLabel_<?= $fieldId ?>">
                            จัดการหมวดหมู่และสินค้า — <?= esc($field['name']) ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <h6 class="fw-bold mb-2">เลือกหมวดหมู่สำหรับสนามย่อยนี้</h6>
                            <div class="border rounded p-2 bg-light">
                                <?php if (!empty($facilityTypes)): ?>
                                    <?php foreach ($facilityTypes as $ft): ?>
                                        <?php
                                        $typeId   = $ft['id'];
                                        $sfRow    = $activeFacilitiesByType[$typeId] ?? null;
                                        $hasFac   = $sfRow !== null;
                                        $sfId     = $sfRow['id'] ?? null;
                                        $productList = (!empty($fieldProducts[$fieldId][$typeId]))
                                            ? $fieldProducts[$fieldId][$typeId]
                                            : [];
                                        ?>
                                        <div class="mb-2 facility-type-block" data-field-id="<?= $fieldId ?>"
                                            data-type-id="<?= $typeId ?>">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="form-check form-check-sm">
                                                    <input class="form-check-input field-facility-checkbox" type="checkbox"
                                                        data-field-id="<?= $fieldId ?>" data-facility-type-id="<?= $typeId ?>"
                                                        data-stadium-facility-id="<?= $sfId ?>" <?= $hasFac ? 'checked' : '' ?>>
                                                    <label class="form-check-label small">
                                                        <?= esc($ft['emoji'] ?? '') ?> <?= esc($ft['name']) ?>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="facility-items-container mt-2 ms-4 border-start border-3 border-info ps-3"
                                                data-field-id="<?= $fieldId ?>"
                                                data-type-id="<?= $typeId ?>"
                                                <?= $hasFac ? '' : 'style="display:none;"' ?>>
                                                
                                                <div class="products-list-wrapper">
                                                    <?php if (!empty($productList)): ?>
                                                        <?php foreach ($productList as $prod): ?>
                                                            <div class="card mb-2 shadow-sm border product-item-card" data-id="<?= $prod['id'] ?>">
                                                                <div class="card-body p-2">
                                                                    <div class="row g-2 align-items-center">
                                                                        <div class="col-8">
                                                                            <input type="text" class="form-control form-control-sm mb-1 item-name" placeholder="ชื่อสินค้า/บริการ" value="<?= esc($prod['name']) ?>">
                                                                            <textarea class="form-control form-control-sm mb-1 item-desc" rows="1" placeholder="รายละเอียด"><?= esc($prod['description']) ?></textarea>
                                                                            <div class="d-flex gap-1">
                                                                                <input type="number" class="form-control form-control-sm item-price" placeholder="ราคา" value="<?= esc($prod['price']) ?>">
                                                                                <input type="text" class="form-control form-control-sm item-unit" placeholder="หน่วย (เช่น ขวด, ชิ้น)" value="<?= esc($prod['unit']) ?>">
                                                                                <select class="form-select form-select-sm item-status">
                                                                                    <option value="active" <?= $prod['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                                                                    <option value="inactive" <?= $prod['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4 text-center">
                                                                            <?php if (!empty($prod['image'])): ?>
                                                                                <img src="<?= base_url('assets/uploads/items/' . $prod['image']) ?>" class="item-img-preview rounded mb-1" style="width:50px; height:50px; object-fit:cover;">
                                                                            <?php else: ?>
                                                                                <div class="item-img-preview border rounded d-flex align-items-center justify-content-center bg-light text-muted small mb-1" style="width:50px; height:50px;">No Pic</div>
                                                                            <?php endif; ?>
                                                                            <input type="file" class="form-control form-control-sm item-image" accept="image/*" style="display:none;">
                                                                            <button type="button" class="btn btn-outline-secondary btn-sm btn-trigger-upload text-xs w-100 mb-1">Upload</button>
                                                                            
                                                                            <div class="d-flex gap-1 justify-content-center">
                                                                                <button type="button" class="btn btn-success btn-sm btn-save-item"><i class="fas fa-save"></i></button>
                                                                                <button type="button" class="btn btn-danger btn-sm btn-delete-item"><i class="fas fa-trash"></i></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>

                                                <button type="button" class="btn btn-outline-primary btn-sm w-100 facility-add-item-btn">
                                                    <i class="fas fa-plus"></i> เพิ่มไอเทมใหม่
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="small text-muted">
                                        ยังไม่มีการตั้งค่าหมวดหมู่ (facility types) ในระบบ
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mt-1 text-muted small">
                                <i class="fas fa-info-circle"></i>
                                การติ๊กจะผูกหมวดหมู่กับสนามย่อยนี้ผ่านตาราง stadium_facilities
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

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
                            <input type="text" name="name" class="form-control" required
                                placeholder="เช่น สนาม A, สนาม B">
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
                            <input type="number" name="price" id="edit_price" class="form-control">
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
                            <input type="file" name="outside_image" class="form-control form-control-sm"
                                accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">เพิ่มรูปภายใน</label>
                            <input type="file" name="inside_images[]" class="form-control form-control-sm" multiple
                                accept="image/*">
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
        
        // 1. Logic Checkbox Facilities (Toggle + Warning)
        document.querySelectorAll('.field-facility-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const self = this;
                const wasChecked = !self.checked; // state before click (if clicked to uncheck, wasChecked=true)
                
                // ถ้ากำลังจะ "เอาออก" ให้เตือนก่อน
                if (wasChecked) { 
                    Swal.fire({
                        title: 'ยืนยันปิดหมวดหมู่นี้?',
                        text: "หากปิด รายการสินค้า/บริการในหมวดนี้จะหายไปทั้งหมด!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ยืนยันปิด',
                        cancelButtonText: 'ยกเลิก',
                        confirmButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            processToggle(self); // ยืนยัน -> ยิง AJAX
                        } else {
                            self.checked = true; // ยกเลิก -> ติ๊กกลับเหมือนเดิม
                        }
                    });
                } else {
                    // ถ้ากำลังจะ "เอาเข้า" (เปิด) -> ทำเลยไม่ต้องเตือน
                    processToggle(self);
                }
            });
        });

        function processToggle(checkbox) {
            const fieldId = checkbox.dataset.fieldId;
            const typeId = checkbox.dataset.facilityTypeId;
            const checked = checkbox.checked ? '1' : '0';
            const itemsBox = checkbox.closest('.facility-type-block')?.querySelector('.facility-items-container');

            fetch('<?= base_url('admin/stadiums/fields/toggle-facility') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8', 'X-Requested-With': 'XMLHttpRequest'},
                body: new URLSearchParams({'field_id': fieldId, 'facility_type_id': typeId, 'checked': checked, '<?= csrf_token() ?>': '<?= csrf_hash() ?>'})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.stadium_facility_id) checkbox.dataset.stadiumFacilityId = data.stadium_facility_id;
                    if (itemsBox) {
                        itemsBox.style.display = checked === '1' ? '' : 'none';
                        if(checked === '0') itemsBox.querySelector('.products-list-wrapper').innerHTML = ''; // Clear items visual
                    }
                } else {
                    checkbox.checked = !checkbox.checked; // Revert
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }

        // 2. Add New Item UI
        document.body.addEventListener('click', function(e) {
            if (e.target.closest('.facility-add-item-btn')) {
                const btn = e.target.closest('.facility-add-item-btn');
                const container = btn.previousElementSibling; // .products-list-wrapper
                
                const card = document.createElement('div');
                card.className = 'card mb-2 shadow-sm border product-item-card bg-light';
                card.innerHTML = `
                   <div class="card-body p-2">
                        <div class="row g-2 align-items-center">
                            <div class="col-8">
                                <input type="text" class="form-control form-control-sm mb-1 item-name" placeholder="ชื่อสินค้า/บริการ">
                                <textarea class="form-control form-control-sm mb-1 item-desc" rows="1" placeholder="รายละเอียด"></textarea>
                                <div class="d-flex gap-1">
                                    <input type="number" class="form-control form-control-sm item-price" placeholder="ราคา" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                    <input type="text" class="form-control form-control-sm item-unit" placeholder="หน่วย">
                                    <select class="form-select form-select-sm item-status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="item-img-preview border rounded d-flex align-items-center justify-content-center bg-white text-muted small mb-1" style="width:50px; height:50px;">No Pic</div>
                                <input type="file" class="form-control form-control-sm item-image" accept="image/*" style="display:none;">
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-trigger-upload text-xs w-100 mb-1">Upload</button>
                                
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" class="btn btn-success btn-sm btn-save-item"><i class="fas fa-save"></i> Save</button>
                                    <button type="button" class="btn btn-outline-danger btn-remove-unsaved"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                   </div>
                `;
                container.appendChild(card);
            }
            
            
            // Trigger File Upload (Fixed listener placement)
            if (e.target.closest('.btn-trigger-upload')) {
                const card = e.target.closest('.product-item-card');
                if(card) card.querySelector('.item-image').click();
            }
        });

        // 2.1 Separate Listener for Unsaved Item Removal (Safety)
        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-remove-unsaved');
            if (btn) {
                const card = btn.closest('.product-item-card');
                if(card) card.remove();
            }
        });

        // Preview Image on Select
        document.body.addEventListener('change', function(e) {
            if (e.target.classList.contains('item-image')) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    const card = e.target.closest('.product-item-card');
                    reader.onload = function(evt) {
                        card.querySelector('.item-img-preview').innerHTML = `<img src="${evt.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        // 3. Save Item Logic
        document.body.addEventListener('click', function(e) {
            if (e.target.closest('.btn-save-item')) {
                const btn = e.target.closest('.btn-save-item');
                const card = btn.closest('.product-item-card');
                const block = card.closest('.facility-type-block');
                const checkbox = block.querySelector('.field-facility-checkbox');
                
                // ต้องมี stadium_facility_id ก่อน (ถ้ายังไม่ติ๊ก checkbox ต้องติ๊กก่อน แต่ตาม UI มันซ่อนอยู่ถ้าไม่ติ๊ก)
                const sfId = checkbox.dataset.stadiumFacilityId;
                if(!sfId) {
                    Swal.fire('Error', 'กรุณากดเปิดใช้งานหมวดหมู่นี้ก่อน', 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('stadium_facility_id', sfId);
                if(card.dataset.id) formData.append('id', card.dataset.id);

                const name = card.querySelector('.item-name').value.trim();
                const desc = card.querySelector('.item-desc').value.trim();
                const price = card.querySelector('.item-price').value.trim();
                const unit = card.querySelector('.item-unit').value.trim();

                // Validation: Check empty fields
                if (!name || !price || !unit) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'กรุณากรอกข้อมูลให้ครบถ้วน',
                        text: 'ชื่อสินค้า, ราคา, และหน่วยนับ ห้ามเว้นว่าง',
                        confirmButtonText: 'ตกลง'
                    });
                    return;
                }

                // Append Data
                formData.append('name', name);
                formData.append('description', desc);
                formData.append('price', price);
                formData.append('unit', unit);
                formData.append('status', card.querySelector('.item-status').value);
                
                const fileInput = card.querySelector('.item-image');
                if(fileInput.files[0]) {
                    formData.append('image', fileInput.files[0]);
                }
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                // Loading state
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch('<?= base_url('admin/stadiums/fields/product/save') ?>', {
                    method: 'POST',
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;

                    if(data.success) {
                        card.dataset.id = data.id; // Assign ID
                        // Update buttons to "Delete Mode" if it was new
                        const delBtn = card.querySelector('.btn-remove-unsaved');
                        if(delBtn) {
                            delBtn.className = 'btn btn-danger btn-sm btn-delete-item';
                            delBtn.innerHTML = '<i class="fas fa-trash"></i>';
                        }
                        
                        Swal.fire({
                            title: 'Saved!',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Save failed', 'error');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    console.error(err);
                });
            }
        });

        // 4. Delete Item Logic
        document.body.addEventListener('click', function(e) {
             if (e.target.closest('.btn-delete-item')) {
                const btn = e.target.closest('.btn-delete-item');
                const card = btn.closest('.product-item-card');
                const id = card.dataset.id;
                
                Swal.fire({
                    title: 'ลบรายการนี้?',
                    text: 'ไม่สามารถกู้คืนได้',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบ',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('<?= base_url('admin/stadiums/fields/product/delete/') ?>' + id)
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) {
                                card.remove();
                            } else {
                                Swal.fire('Error', 'Delete failed', 'error');
                            }
                        });
                    }
                });
             }
        });



        const stadiumId = <?= $stadium['id'] ?>;

        // ... (ส่วนจัดการ Edit Modal: Fill Data) ...
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_price').value = this.dataset.price;
                document.getElementById('edit_price_daily').value = this.dataset.pricedaily;
                document.getElementById('edit_desc').value = this.dataset.desc;
                document.getElementById('edit_status').value = this.dataset.status;
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