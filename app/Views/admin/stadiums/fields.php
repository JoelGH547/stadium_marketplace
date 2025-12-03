<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
// -----------------------------------------------------------
// ⚙️ เตรียมข้อมูลสำหรับ JavaScript (JSON Data)
// -----------------------------------------------------------
$fieldFacilities = [];
$fieldImages = [];
$fieldItems = []; // ✅ เพิ่มตัวแปรสำหรับเก็บรายการสินค้าที่ขายในแต่ละสนาม
$db = \Config\Database::connect();

if (!empty($fields)) {
    foreach ($fields as $field) {
        // 1. ดึง Facility
        $rawFac = $db->table('stadium_facilities')->where('field_id', $field['id'])->get()->getResultArray();
        $formattedFac = [];
        foreach($rawFac as $r) $formattedFac[$r['type_id']][] = $r['name'];
        $fieldFacilities[$field['id']] = json_encode($formattedFac);

        // 2. ดึงรูปภาพ
        $outImgs = json_decode($field['outside_images'] ?? '[]', true);
        $inImgs = json_decode($field['inside_images'] ?? '[]', true);
        $fieldImages[$field['id']] = json_encode(['out' => $outImgs, 'in' => $inImgs]);

        // 3. ✅ ดึงสินค้าที่ขายในสนามนี้ (Field Items)
        $rawItems = $db->table('field_items')->where('field_id', $field['id'])->get()->getResultArray();
        $formattedItems = [];
        foreach($rawItems as $item) {
            // เก็บเป็น Key: product_id => Value: { price: ... } เพื่อให้ JS เช็คง่ายๆ
            $formattedItems[$item['product_id']] = [
                'price' => $item['custom_price']
            ];
        }
        $fieldItems[$field['id']] = json_encode($formattedItems);
    }
}

// เช็คประเภทสนามเพื่อเปลี่ยนคำพูด
$isComplex = ($stadium['booking_type'] ?? 'complex') == 'complex';
if ($isComplex) {
    $titleText = 'จัดการสนามย่อย (Sub-Fields)';
    $btnText   = 'เพิ่มสนามย่อยใหม่';
    $colName   = 'ชื่อสนาม';
    $emptyText = 'ยังไม่มีข้อมูลสนามย่อย';
    $namePlaceholder = 'เช่น สนาม A, สนาม B';
} else {
    $titleText = 'กำหนดราคาและรายละเอียด (Pricing & Info)';
    $btnText   = 'เพิ่มข้อมูลราคา';
    $colName   = 'ชื่อรายการ/แพ็กเกจ';
    $emptyText = 'ยังไม่ได้กำหนดราคาสำหรับสนามนี้ กรุณากดปุ่มเพิ่มข้อมูล';
    $namePlaceholder = 'เช่น ค่าเช่าเหมาวัน';
}
?>

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('admin/stadiums/view/' . $stadium['id']) ?>" class="text-muted text-decoration-none small">
                <i class="fas fa-arrow-left"></i> กลับไปหน้ารายละเอียด
            </a>
            <h3 class="h3 mt-2 text-gray-800 font-weight-bold">
                <?= $titleText ?> <span class="text-primary">(<?= esc($stadium['name']) ?>)</span>
            </h3>
        </div>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addFieldModal">
            <i class="fas fa-plus-circle me-1"></i> <?= $btnText ?>
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
                                    <button class="btn btn-warning btn-sm btn-edit shadow-sm text-dark"
                                            data-bs-toggle="modal" data-bs-target="#editFieldModal"
                                            data-id="<?= $field['id'] ?>"
                                            data-name="<?= esc($field['name']) ?>"
                                            data-price="<?= esc($field['price']) ?>"
                                            data-pricedaily="<?= esc($field['price_daily']) ?>"
                                            data-desc="<?= esc($field['description']) ?>"
                                            data-status="<?= esc($field['status']) ?>"
                                            data-facilities='<?= $fieldFacilities[$field['id']] ?? '{}' ?>'
                                            data-items='<?= $fieldItems[$field['id']] ?? '{}' ?>' <!-- ✅ ส่งข้อมูลสินค้าไป JS -->
                                    >
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <a href="<?= base_url('admin/stadiums/fields/delete/' . $field['id']) ?>" 
                                       class="btn btn-outline-danger btn-sm shadow-sm btn-delete"
                                       onclick="return confirm('ยืนยันลบ? ข้อมูลการขายสินค้าในสนามนี้จะหายไปด้วย')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-2"><i class="fas fa-clipboard-list fa-3x text-gray-300"></i></div>
                                    <?= $emptyText ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- Modal Add -->
<!-- ========================================================= -->
<div class="modal fade" id="addFieldModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><?= $btnText ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/stadiums/fields/create') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="stadium_id" value="<?= $stadium['id'] ?>">
                    
                    <!-- ส่วนข้อมูลพื้นฐาน (เหมือนเดิม) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold"><?= $colName ?> <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="<?= $namePlaceholder ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">ราคา/ชม. <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">ราคา/วัน (ถ้ามี)</label>
                            <input type="number" name="price_daily" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">รายละเอียดเพิ่มเติม</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row mb-3 bg-light p-3 rounded mx-0">
                        <div class="col-md-6 mb-2">
                            <label class="fw-bold text-primary"><i class="fas fa-image me-1"></i> รูปปก (1 รูป)</label>
                            <input type="file" name="outside_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="fw-bold text-primary"><i class="fas fa-images me-1"></i> รูปภายใน (หลายรูป)</label>
                            <input type="file" name="inside_images[]" class="form-control" multiple accept="image/*">
                        </div>
                    </div>

                    <!-- ✅ ส่วนเลือกสินค้าจากคลัง (Products) -->
                    <div class="mb-3 border p-3 rounded bg-white shadow-sm">
                        <label class="fw-bold mb-2 text-success"><i class="fas fa-box-open me-1"></i> เลือกสินค้าที่วางขายในสนามนี้</label>
                        <?php if(!empty($products)): ?>
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
                                    <tbody>
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
                                            <td>
                                                <label class="form-check-label small fw-bold mb-0" for="add_p_<?= $p['id'] ?>">
                                                    <?= esc($p['name']) ?>
                                                </label>
                                            </td>
                                            <td class="text-muted small">
                                                ฿<?= number_format($p['base_price']) ?>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" 
                                                       name="items[<?= $p['id'] ?>][price]" 
                                                       id="add_price_<?= $p['id'] ?>"
                                                       class="form-control form-control-sm" 
                                                       placeholder="฿<?= number_format($p['base_price']) ?>" 
                                                       disabled> <!-- Disabled ไว้ก่อน จนกว่าจะติ๊ก -->
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-3 text-muted border rounded bg-light">
                                <i class="fas fa-exclamation-circle"></i> ยังไม่มีสินค้าในคลัง <br>
                                <a href="<?= base_url('admin/vendor-items') ?>" class="text-decoration-none small">ไปเพิ่มสินค้าก่อน</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ส่วนสิ่งอำนวยความสะดวก (Facilities) -->
                    <div class="mb-3 border p-3 rounded">
                        <label class="fw-bold mb-2"><i class="fas fa-check-square me-1"></i> สิ่งอำนวยความสะดวก</label>
                        <?php foreach($facilityTypes as $type): ?>
                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input chk-facility" type="checkbox" 
                                           id="add_t_<?= $type['id'] ?>" 
                                           data-target="add_box_<?= $type['id'] ?>"
                                           data-type-id="<?= $type['id'] ?>">
                                    <label class="form-check-label" for="add_t_<?= $type['id'] ?>"><?= $type['name'] ?></label>
                                </div>
                                <div id="add_box_<?= $type['id'] ?>" class="ms-4 mt-1 d-none fac-input-group"></div>
                            </div>
                        <?php endforeach; ?>
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

<!-- ========================================================= -->
<!-- Modal Edit -->
<!-- ========================================================= -->
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
                            <label class="fw-bold small text-muted">เปลี่ยนรูปปก (ถ้าไม่อัป ใช้รูปเดิม)</label>
                            <input type="file" name="outside_image" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">เพิ่มรูปภายใน (รูปเก่าจะไม่หาย)</label>
                            <input type="file" name="inside_images[]" class="form-control form-control-sm" multiple accept="image/*">
                        </div>
                    </div>

                    <!-- ✅ ส่วนเลือกสินค้าจากคลัง (Edit Mode) -->
                    <div class="mb-3 border p-3 rounded bg-white shadow-sm">
                        <label class="fw-bold mb-2 text-success"><i class="fas fa-box-open me-1"></i> สินค้าที่วางขาย (แก้ไข)</label>
                        <?php if(!empty($products)): ?>
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
                                    <tbody>
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
                                            <td>
                                                <label class="form-check-label small fw-bold mb-0" for="edit_p_<?= $p['id'] ?>">
                                                    <?= esc($p['name']) ?>
                                                </label>
                                            </td>
                                            <td class="text-muted small">
                                                ฿<?= number_format($p['base_price']) ?>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" 
                                                       name="items[<?= $p['id'] ?>][price]" 
                                                       id="edit_price_val_<?= $p['id'] ?>"
                                                       class="form-control form-control-sm" 
                                                       placeholder="฿<?= number_format($p['base_price']) ?>" 
                                                       disabled>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-3 text-muted bg-light rounded">ไม่มีสินค้าในคลัง</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3 border p-3 rounded">
                        <label class="fw-bold mb-2">แก้ไขสิ่งอำนวยความสะดวก</label>
                        <?php foreach($facilityTypes as $type): ?>
                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input chk-facility-edit" type="checkbox" 
                                           id="edit_t_<?= $type['id'] ?>" 
                                           data-target="edit_box_<?= $type['id'] ?>" 
                                           data-type-id="<?= $type['id'] ?>">
                                    <label class="form-check-label" for="edit_t_<?= $type['id'] ?>"><?= $type['name'] ?></label>
                                </div>
                                <div id="edit_box_<?= $type['id'] ?>" class="ms-4 mt-1 d-none fac-input-group"></div>
                                <div id="edit_btn_row_<?= $type['id'] ?>" class="ms-4 d-none">
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 btn-add-row" data-type-id="<?= $type['id'] ?>" data-target="edit_box_<?= $type['id'] ?>">+ เพิ่มรายการ</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
    const facilityTypes = <?= json_encode($facilityTypes) ?>;

    // Helper: Create Input Row
    function createInputRow(container, typeId, value = '') {
        const div = document.createElement('div');
        div.className = 'input-group input-group-sm mb-1 item-row';
        div.innerHTML = `
            <input type="text" name="facilities[${typeId}][]" class="form-control" value="${value}" placeholder="ระบุชื่อ...">
            <button type="button" class="btn btn-outline-danger btn-remove-row"><i class="fas fa-minus"></i></button>
        `;
        container.appendChild(div);
        div.querySelector('.btn-remove-row').addEventListener('click', () => div.remove());
    }

    // ✅ JS สำหรับ Checkbox สินค้า (ถ้าติ๊ก -> ให้กรอกราคาได้)
    function toggleProductInput(chk) {
        const input = document.getElementById(chk.dataset.target);
        if(input) {
            input.disabled = !chk.checked;
            if (!chk.checked) input.value = ''; // เคลียร์ค่าเมื่อยกเลิกติ๊ก
        }
    }
    
    // Bind Event ให้ Checkbox สินค้า (Add & Edit)
    document.querySelectorAll('.chk-product, .chk-product-edit').forEach(chk => {
        chk.addEventListener('change', function() { toggleProductInput(this); });
    });

    // =========================================
    // EDIT MODAL LOGIC
    // =========================================
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            // 1. Fill Basic Info
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('edit_price_daily').value = this.dataset.pricedaily;
            document.getElementById('edit_desc').value = this.dataset.desc;
            document.getElementById('edit_status').value = this.dataset.status;

            // 2. Clear Old Facilities
            document.querySelectorAll('.chk-facility-edit').forEach(chk => {
                chk.checked = false;
                const target = document.getElementById(chk.dataset.target);
                target.innerHTML = ''; 
                target.classList.add('d-none');
                document.getElementById('edit_btn_row_' + chk.dataset.typeId).classList.add('d-none');
            });

            // 3. Populate Facilities
            const facData = JSON.parse(this.dataset.facilities || '{}');
            for(const [typeId, items] of Object.entries(facData)) {
                const chk = document.getElementById('edit_t_' + typeId);
                if(chk) {
                    chk.checked = true;
                    const target = document.getElementById('edit_box_' + typeId);
                    target.classList.remove('d-none');
                    document.getElementById('edit_btn_row_' + typeId).classList.remove('d-none');
                    items.forEach(val => createInputRow(target, typeId, val));
                }
            }

            // 4. ✅ Populate Products (สินค้า)
            // รีเซ็ต Checkbox สินค้าทั้งหมดก่อน
            document.querySelectorAll('.chk-product-edit').forEach(chk => {
                chk.checked = false;
                toggleProductInput(chk);
            });

            const itemData = JSON.parse(this.dataset.items || '{}'); // { prod_id: {price: '...'} }
            for(const [prodId, info] of Object.entries(itemData)) {
                const chk = document.getElementById('edit_p_' + prodId);
                if(chk) {
                    chk.checked = true;
                    toggleProductInput(chk);
                    // เติมราคาขายจริง (ถ้ามี)
                    const input = document.getElementById('edit_price_val_' + prodId);
                    if(input && info.price) input.value = parseFloat(info.price); 
                }
            }
        });
    });

    // --- ส่วน Facilities Logic อื่นๆ (เหมือนเดิม) ---
    document.querySelectorAll('.chk-facility').forEach(chk => {
        chk.addEventListener('change', function() {
            const target = document.getElementById(this.dataset.target);
            const typeId = this.dataset.typeId;
            if(this.checked) {
                target.classList.remove('d-none');
                if(target.children.length === 0) createInputRow(target, typeId);
                if(!target.nextElementSibling || !target.nextElementSibling.classList.contains('add-more-wrapper')) {
                    const btnDiv = document.createElement('div');
                    btnDiv.className = 'ms-4 add-more-wrapper';
                    btnDiv.innerHTML = `<button type="button" class="btn btn-sm btn-link text-decoration-none p-0 btn-add-more-row">+ เพิ่มรายการ</button>`;
                    target.parentNode.insertBefore(btnDiv, target.nextSibling);
                    btnDiv.querySelector('.btn-add-more-row').addEventListener('click', () => createInputRow(target, typeId));
                }
            } else {
                target.classList.add('d-none');
                target.innerHTML = '';
                if(target.nextElementSibling && target.nextElementSibling.classList.contains('add-more-wrapper')) target.nextElementSibling.remove();
            }
        });
    });

    document.querySelectorAll('.chk-facility-edit').forEach(chk => {
        chk.addEventListener('change', function() {
            const target = document.getElementById(this.dataset.target);
            const btnRow = document.getElementById('edit_btn_row_' + this.dataset.typeId);
            if(this.checked) {
                target.classList.remove('d-none');
                btnRow.classList.remove('d-none');
                if(target.children.length === 0) createInputRow(target, this.dataset.typeId);
            } else {
                target.classList.add('d-none');
                target.innerHTML = '';
                btnRow.classList.add('d-none');
            }
        });
    });

    document.querySelectorAll('.btn-add-row').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            createInputRow(target, this.dataset.typeId);
        });
    });
});
</script>
<?= $this->endSection() ?>