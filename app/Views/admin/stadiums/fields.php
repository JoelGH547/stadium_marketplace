<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
// เตรียมข้อมูล JSON สำหรับใช้ใน JavaScript (Modal Edit & View)
$fieldFacilities = [];
$fieldImages = [];
$db = \Config\Database::connect();

if (!empty($fields)) {
    foreach ($fields as $field) {
        // ดึง Facility
        $raw = $db->table('stadium_facilities')->where('field_id', $field['id'])->get()->getResultArray();
        $formatted = [];
        foreach($raw as $r) {
            // จัดรูปแบบ: [ type_id => [name1, name2, ...] ]
            $formatted[$r['type_id']][] = $r['name'];
        }
        $fieldFacilities[$field['id']] = json_encode($formatted);

        // ดึงรูปภาพ
        $outImgs = json_decode($field['outside_images'] ?? '[]', true);
        $inImgs = json_decode($field['inside_images'] ?? '[]', true);
        $fieldImages[$field['id']] = json_encode(['out' => $outImgs, 'in' => $inImgs]);
    }
}
?>

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('admin/stadiums') ?>" class="text-muted text-decoration-none small">
                <i class="fas fa-arrow-left"></i> กลับไปหน้ารวม
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
                            <th width="10%">รูปปก</th>
                            <th width="20%">ชื่อสนาม</th>
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
                                    <button class="btn btn-info btn-sm text-white shadow-sm btn-view-detail" 
                                            data-id="<?= $field['id'] ?>"
                                            data-name="<?= esc($field['name']) ?>"
                                            data-desc="<?= esc($field['description']) ?>"
                                            data-images='<?= $fieldImages[$field['id']] ?? '{}' ?>'
                                            data-facilities='<?= $fieldFacilities[$field['id']] ?? '{}' ?>'
                                            data-bs-toggle="modal" data-bs-target="#viewDetailModal">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <button class="btn btn-warning btn-sm btn-edit shadow-sm text-dark"
                                            data-bs-toggle="modal" data-bs-target="#editFieldModal"
                                            data-id="<?= $field['id'] ?>"
                                            data-name="<?= esc($field['name']) ?>"
                                            data-price="<?= esc($field['price']) ?>"
                                            data-pricedaily="<?= esc($field['price_daily']) ?>"
                                            data-desc="<?= esc($field['description']) ?>"
                                            data-status="<?= esc($field['status']) ?>"
                                            data-facilities='<?= $fieldFacilities[$field['id']] ?? '{}' ?>'>
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
                            <tr><td colspan="7" class="text-center py-5 text-muted">ยังไม่มีข้อมูลสนามย่อย</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="viewTitle">รายละเอียด</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <div id="viewCover" class="mb-2 w-100 text-center bg-light rounded" style="min-height:200px; display:flex; align-items:center; justify-content:center;"></div>
                        <h6 class="fw-bold small text-muted mt-3">รูปภายใน</h6>
                        <div id="viewInside" class="d-flex gap-2 overflow-auto pb-2 border-bottom"></div>
                    </div>
                    <div class="col-md-7">
                        <h6 class="fw-bold text-primary">รายละเอียดสนาม</h6>
                        <p id="viewDesc" class="text-muted small"></p>
                        
                        <h6 class="fw-bold text-primary mt-3">สิ่งอำนวยความสะดวก</h6>
                        <div id="viewFac" class="row g-2"></div>
                    </div>
                </div>
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
                            <label class="fw-bold">ชื่อสนาม <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="เช่น สนามแบด 1">
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
                                <div id="add_box_<?= $type['id'] ?>" class="ms-4 mt-1 d-none fac-input-group">
                                    </div>
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

<div class="modal fade" id="editFieldModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">แก้ไขข้อมูลสนามย่อย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/stadiums/fields/update') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="stadium_id" value="<?= $stadium['id'] ?>">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">ชื่อสนาม</label>
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

    // --- Helper: Create Input Row ---
    function createInputRow(container, typeId, value = '') {
        const div = document.createElement('div');
        div.className = 'input-group input-group-sm mb-1 item-row';
        div.innerHTML = `
            <input type="text" name="facilities[${typeId}][]" class="form-control" value="${value}" placeholder="ระบุชื่อ...">
            <button type="button" class="btn btn-outline-danger btn-remove-row"><i class="fas fa-minus"></i></button>
        `;
        container.appendChild(div);
        
        // Add Event Listener for Remove button immediately
        div.querySelector('.btn-remove-row').addEventListener('click', function() {
            div.remove();
        });
    }

    // =========================================
    // 1. ADD MODAL LOGIC
    // =========================================
    document.querySelectorAll('.chk-facility').forEach(chk => {
        chk.addEventListener('change', function() {
            const target = document.getElementById(this.dataset.target);
            const typeId = this.dataset.typeId;

            if(this.checked) {
                target.classList.remove('d-none');
                // เพิ่มแถวแรกให้อัตโนมัติถ้ายังไม่มี
                if(target.children.length === 0) {
                    createInputRow(target, typeId);
                }
                // เพิ่มปุ่ม + ด้านล่าง
                if(!target.nextElementSibling || !target.nextElementSibling.classList.contains('add-more-wrapper')) {
                    const btnDiv = document.createElement('div');
                    btnDiv.className = 'ms-4 add-more-wrapper';
                    btnDiv.innerHTML = `<button type="button" class="btn btn-sm btn-link text-decoration-none p-0 btn-add-more-row">+ เพิ่มรายการ</button>`;
                    target.parentNode.insertBefore(btnDiv, target.nextSibling);
                    
                    // Event สำหรับปุ่ม +
                    btnDiv.querySelector('.btn-add-more-row').addEventListener('click', () => {
                        createInputRow(target, typeId);
                    });
                }
            } else {
                target.classList.add('d-none');
                target.innerHTML = ''; // เคลียร์ input
                if(target.nextElementSibling && target.nextElementSibling.classList.contains('add-more-wrapper')) {
                    target.nextElementSibling.remove(); // ลบปุ่ม +
                }
            }
        });
    });

    // =========================================
    // 2. VIEW DETAIL LOGIC
    // =========================================
    document.querySelectorAll('.btn-view-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('viewTitle').innerText = this.dataset.name;
            document.getElementById('viewDesc').innerText = this.dataset.desc || '-';

            const images = JSON.parse(this.dataset.images || '{}');
            const facilities = JSON.parse(this.dataset.facilities || '{}');

            // Show Cover
            const coverBox = document.getElementById('viewCover');
            coverBox.innerHTML = (images.out && images.out.length > 0) 
                ? `<img src="/assets/uploads/fields/${images.out[0]}" class="rounded shadow-sm" style="max-height:200px; max-width:100%;">`
                : '<span class="text-muted"><i class="fas fa-image fa-2x mb-2"></i><br>ไม่มีรูปปก</span>';

            // Show Inside
            const insideBox = document.getElementById('viewInside');
            insideBox.innerHTML = '';
            if(images.in && images.in.length > 0) {
                images.in.forEach(img => {
                    insideBox.innerHTML += `<img src="/assets/uploads/fields/${img}" class="rounded border" style="height:80px; width:auto;">`;
                });
            } else {
                insideBox.innerHTML = '<span class="text-muted small">ไม่มีรูปภายใน</span>';
            }

            // Show Facilities
            const facBox = document.getElementById('viewFac');
            facBox.innerHTML = '';
            facilityTypes.forEach(type => {
                if(facilities[type.id] && facilities[type.id].length > 0) {
                    let itemsHtml = facilities[type.id].map(i => `<span class="badge bg-light text-dark border me-1 mb-1">${i}</span>`).join('');
                    facBox.innerHTML += `
                        <div class="col-12">
                            <small class="fw-bold text-secondary">${type.name}</small><br>
                            ${itemsHtml}
                        </div>`;
                }
            });
        });
    });

    // =========================================
    // 3. EDIT MODAL LOGIC
    // =========================================
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            // Fill Basic Info
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('edit_price_daily').value = this.dataset.pricedaily;
            document.getElementById('edit_desc').value = this.dataset.desc;
            document.getElementById('edit_status').value = this.dataset.status;

            // Clear Old Facility Checkboxes & Inputs
            document.querySelectorAll('.chk-facility-edit').forEach(chk => {
                chk.checked = false;
                const target = document.getElementById(chk.dataset.target);
                target.innerHTML = ''; 
                target.classList.add('d-none');
                document.getElementById('edit_btn_row_' + chk.dataset.typeId).classList.add('d-none');
            });

            // Populate Facilities
            const facData = JSON.parse(this.dataset.facilities || '{}');
            for(const [typeId, items] of Object.entries(facData)) {
                const chk = document.getElementById('edit_t_' + typeId);
                if(chk) {
                    chk.checked = true;
                    const target = document.getElementById('edit_box_' + typeId);
                    target.classList.remove('d-none');
                    document.getElementById('edit_btn_row_' + typeId).classList.remove('d-none');

                    // Loop create inputs with values
                    items.forEach(val => createInputRow(target, typeId, val));
                }
            }
        });
    });

    // Helper for "Add Row" button inside Edit Modal
    document.querySelectorAll('.btn-add-row').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            createInputRow(target, this.dataset.typeId);
        });
    });

    // Checkbox Toggle logic for Edit Modal
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

    // =========================================
    // 4. DELETE BUTTON
    // =========================================
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