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
            <h3 class="h3 mt-2 text-gray-800 font-weight-bold">
                จัดการสนามย่อย <span class="text-primary">(<?= esc($stadium['name']) ?>)</span>
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
                                            $productCount = count($productList);
                                        ?>
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <div class="form-check form-check-sm">
                                                <input class="form-check-input field-facility-checkbox"
                                                    type="checkbox"
                                                    data-field-id="<?= $fieldId ?>"
                                                    data-facility-type-id="<?= $typeId ?>"
                                                    data-stadium-facility-id="<?= $sfId ?>"
                                                    <?= $hasFac ? 'checked' : '' ?>>
                                                <label class="form-check-label small">
                                                    <?= esc($ft['emoji'] ?? '') ?> <?= esc($ft['name']) ?>
                                                </label>
                                            </div>
                                            <?php if ($productCount > 0): ?>
                                                <span class="badge bg-light text-muted border small">
                                                    <?= $productCount ?> สินค้า
                                                </span>
                                            <?php endif; ?>
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

                        <hr>

                        <div>
                            <h6 class="fw-bold mb-2">สินค้า/บริการในหมวดที่ใช้งาน</h6>
                            <?php
                                $hasAnyActive = !empty($activeFacilitiesByType);
                            ?>
                            <?php if (!$hasAnyActive): ?>
                                <div class="alert alert-light border small mb-0">
                                    ยังไม่มีการเลือกหมวดหมู่สำหรับสนามย่อยนี้<br>
                                    กรุณาเลือกหมวดหมู่ด้านบนก่อนจึงจะสามารถเพิ่มสินค้าได้
                                </div>
                            <?php else: ?>
                                <?php foreach ($facilityTypes as $ft): ?>
                                    <?php
                                        $typeId   = $ft['id'];
                                        $sfRow    = $activeFacilitiesByType[$typeId] ?? null;
                                        if (!$sfRow) {
                                            continue;
                                        }
                                        $sfId        = $sfRow['id'] ?? null;
                                        $productList = (!empty($fieldProducts[$fieldId][$typeId]))
                                            ? $fieldProducts[$fieldId][$typeId]
                                            : [];
                                    ?>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div class="fw-semibold small">
                                                หมวด: <?= esc($ft['emoji'] ?? '') ?> <?= esc($ft['name']) ?>
                                            </div>
                                            <button type="button"
                                                class="btn btn-outline-primary btn-xs disabled"
                                                title="TODO: เชื่อมหน้าจัดการไอเทม">
                                                <i class="fas fa-plus-circle"></i> เพิ่มไอเทมในหมวดนี้
                                            </button>
                                        </div>
                                        <?php if (!empty($productList)): ?>
                                            <ul class="list-unstyled small mb-0">
                                                <?php foreach ($productList as $prod): ?>
                                                    <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                                        <span>
                                                            <?= esc($prod['name'] ?? $prod['product_name'] ?? 'ไม่ระบุชื่อ') ?>
                                                        </span>
                                                        <span class="text-muted">
                                                            <?php if (!empty($prod['price'])): ?>
                                                                ฿<?= number_format($prod['price']) ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <div class="small text-muted fst-italic">
                                                ยังไม่มีสินค้าในหมวดนี้
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
        // จัดการ checkbox หมวดหมู่ของสนามย่อย (stadium_facilities)
        document.querySelectorAll('.field-facility-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const fieldId = this.dataset.fieldId;
                const typeId  = this.dataset.facilityTypeId;
                const checked = this.checked ? '1' : '0';

                fetch('<?= base_url('admin/stadiums/fields/toggle-facility') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        'field_id': fieldId,
                        'facility_type_id': typeId,
                        'checked': checked,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire('เกิดข้อผิดพลาด', data.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                        this.checked = !this.checked;
                        return;
                    }
                    if (data.stadium_facility_id) {
                        this.dataset.stadiumFacilityId = data.stadium_facility_id;
                    } else if (!this.checked) {
                        this.dataset.stadiumFacilityId = '';
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
                    this.checked = !this.checked;
                });
            });
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