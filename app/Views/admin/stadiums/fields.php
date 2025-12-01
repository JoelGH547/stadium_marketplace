<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
// เตรียมข้อมูล JSON
$fieldFacilities = [];
$fieldImages = [];
$db = \Config\Database::connect();

if (!empty($fields)) {
    foreach ($fields as $field) {
        // Facility
        $raw = $db->table('stadium_facilities')->where('field_id', $field['id'])->get()->getResultArray();
        $formatted = [];
        foreach($raw as $r) $formatted[$r['type_id']][] = $r['name'];
        $fieldFacilities[$field['id']] = json_encode($formatted);

        // Images (ส่งไปโชว์ใน View Modal)
        $outImgs = json_decode($field['outside_images'] ?? '[]', true);
        $inImgs = json_decode($field['inside_images'] ?? '[]', true);
        $fieldImages[$field['id']] = json_encode(['out' => $outImgs, 'in' => $inImgs]);
    }
}
?>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('admin/stadiums') ?>" class="text-muted text-decoration-none">
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

    <div class="card shadow mb-4 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" width="5%">#</th>
                            <th width="10%">รูปปก</th>
                            <th width="20%">ชื่อสนาม</th>
                            <th width="15%" class="text-center">ราคา (ชม./วัน)</th>
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
                                        <img src="<?= base_url('assets/uploads/fields/'.$thumb) ?>" class="rounded border" style="width: 60px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="fw-bold text-dark"><?= esc($field['name']) ?></td>
                                
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
                            <tr><td colspan="6" class="text-center py-5 text-muted">ไม่มีข้อมูลสนามย่อย</td></tr>
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
                <div class="mb-3">
                    <h6 class="fw-bold text-primary">รูปปก</h6>
                    <div id="viewCover" class="mb-2"></div>
                    
                    <h6 class="fw-bold text-primary">รูปภายใน</h6>
                    <div id="viewInside" class="d-flex gap-2 overflow-auto pb-2"></div>
                </div>
                <hr>
                <div class="mb-3">
                    <h6 class="fw-bold text-primary">รายละเอียด</h6>
                    <p id="viewDesc" class="text-muted"></p>
                </div>
                <hr>
                <div>
                    <h6 class="fw-bold text-primary">สิ่งอำนวยความสะดวก</h6>
                    <div id="viewFac" class="row"></div>
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
                            <label class="fw-bold">ชื่อสนาม *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">ราคา/ชม. *</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">ราคา/วัน</label>
                            <input type="number" name="price_daily" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">รายละเอียด</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">รูปปก (1 รูป)</label>
                            <input type="file" name="outside_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">รูปภายใน (หลายรูป)</label>
                            <input type="file" name="inside_images[]" class="form-control" multiple accept="image/*">
                        </div>
                    </div>

                    <div class="mb-3 border p-3 rounded bg-light">
                        <label class="fw-bold mb-2">สิ่งอำนวยความสะดวก</label>
                        <?php foreach($facilityTypes as $type): ?>
                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input type-chk" type="checkbox" id="add_t_<?= $type['id'] ?>" data-target="add_b_<?= $type['id'] ?>">
                                    <label class="form-check-label" for="add_t_<?= $type['id'] ?>"><?= $type['name'] ?></label>
                                </div>
                                <div id="add_b_<?= $type['id'] ?>" class="ms-4 mt-1 d-none item-container">
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="text" name="facilities[<?= $type['id'] ?>][]" class="form-control" placeholder="ระบุชื่อ...">
                                        <button type="button" class="btn btn-success btn-add-item" data-type="<?= $type['id'] ?>">+</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="active">พร้อมใช้งาน</option>
                            <option value="maintenance">ปิดปรับปรุง</option>
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
                    
                    <div class="row mb-3 bg-light p-2 rounded border">
                        <div class="col-md-6">
                            <label class="fw-bold">เปลี่ยนรูปปก</label>
                            <input type="file" name="outside_image" class="form-control form-control-sm" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">เพิ่มรูปภายใน</label>
                            <input type="file" name="inside_images[]" class="form-control form-control-sm" multiple accept="image/*">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="delete_old_inside" value="1" id="del_in">
                                <label class="form-check-label text-danger small" for="del_in">ลบรูปภายในเก่าทั้งหมด</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 border p-3 rounded bg-light">
                        <label class="fw-bold mb-2">แก้ไขสิ่งอำนวยความสะดวก</label>
                        <?php foreach($facilityTypes as $type): ?>
                            <div class="mb-2">
                                <div class="form-check">
                                    <input class="form-check-input edit-type-chk" type="checkbox" id="edit_t_<?= $type['id'] ?>" data-target="edit_b_<?= $type['id'] ?>" data-type-id="<?= $type['id'] ?>">
                                    <label class="form-check-label" for="edit_t_<?= $type['id'] ?>"><?= $type['name'] ?></label>
                                </div>
                                <div id="edit_b_<?= $type['id'] ?>" class="ms-4 mt-1 d-none edit-item-wrapper"></div>
                                <div id="edit_btn_<?= $type['id'] ?>" class="ms-4 d-none">
                                    <small class="text-primary btn-add-row-edit" style="cursor:pointer;" data-type-id="<?= $type['id'] ?>">+ เพิ่ม</small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">สถานะ</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="active">Active</option>
                            <option value="maintenance">Maintenance</option>
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

    // 1. View Detail
    document.querySelectorAll('.btn-view-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('viewTitle').innerText = this.dataset.name;
            document.getElementById('viewDesc').innerText = this.dataset.desc;

            const images = JSON.parse(this.dataset.images || '{}');
            const facilities = JSON.parse(this.dataset.facilities || '{}');

            // Show Cover
            const coverBox = document.getElementById('viewCover');
            coverBox.innerHTML = (images.out && images.out.length > 0) 
                ? `<img src="/assets/uploads/fields/${images.out[0]}" class="rounded border shadow-sm" style="height:200px; object-fit:cover;">`
                : '<span class="text-muted">ไม่มีรูปปก</span>';

            // Show Inside
            const insideBox = document.getElementById('viewInside');
            insideBox.innerHTML = '';
            if(images.in && images.in.length > 0) {
                images.in.forEach(img => {
                    insideBox.innerHTML += `<img src="/assets/uploads/fields/${img}" class="rounded border" style="height:100px;">`;
                });
            } else {
                insideBox.innerHTML = '<span class="text-muted">ไม่มีรูปภายใน</span>';
            }

            // Show Facility
            const facBox = document.getElementById('viewFac');
            facBox.innerHTML = '';
            facilityTypes.forEach(type => {
                if(facilities[type.id]) {
                    let itemsHtml = facilities[type.id].map(i => `<span class="badge bg-light text-dark border me-1">${i}</span>`).join('');
                    facBox.innerHTML += `<div class="col-md-6 mb-2"><strong>${type.name}</strong><br>${itemsHtml}</div>`;
                }
            });
        });
    });

    // 2. Edit Logic
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            // Basic Info
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('edit_price_daily').value = this.dataset.pricedaily;
            document.getElementById('edit_desc').value = this.dataset.desc;
            document.getElementById('edit_status').value = this.dataset.status;

            // Facility Logic
            document.querySelectorAll('.edit-type-chk').forEach(chk => {
                chk.checked = false;
                document.getElementById(chk.dataset.target).innerHTML = '';
                document.getElementById(chk.dataset.target).classList.add('d-none');
                document.getElementById('edit_btn_'+chk.dataset.typeId).classList.add('d-none');
            });

            const facData = JSON.parse(this.dataset.facilities || '{}');
            for(const [typeId, items] of Object.entries(facData)) {
                const chk = document.getElementById('edit_t_'+typeId);
                if(chk) {
                    chk.checked = true;
                    document.getElementById('edit_b_'+typeId).classList.remove('d-none');
                    document.getElementById('edit_btn_'+typeId).classList.remove('d-none');
                    items.forEach(val => createEditInput(document.getElementById('edit_b_'+typeId), typeId, val));
                }
            }
        });
    });

    // Helper Functions for Dynamic Inputs
    function createEditInput(container, typeId, value = '') {
        const div = document.createElement('div');
        div.className = 'input-group input-group-sm mb-1 item-row';
        div.innerHTML = `
            <input type="text" name="facilities[${typeId}][]" class="form-control" value="${value}">
            <button type="button" class="btn btn-danger btn-remove-row">-</button>
        `;
        container.appendChild(div);
    }

    // Add/Remove Listeners
    document.addEventListener('click', function(e) {
        // Add in Edit
        if(e.target.classList.contains('btn-add-row-edit')) {
            createEditInput(document.getElementById('edit_b_'+e.target.dataset.typeId), e.target.dataset.typeId);
        }
        // Add in Create
        if(e.target.classList.contains('btn-add-item')) {
            const typeId = e.target.dataset.type;
            const container = document.getElementById('add_b_'+typeId).querySelector('.item-container');
            if(!container) { // First item logic for create modal structure
                 const wrapper = document.createElement('div');
                 wrapper.className = 'input-group input-group-sm mb-1 item-row';
                 wrapper.innerHTML = `<input type="text" name="facilities[${typeId}][]" class="form-control"><button type="button" class="btn btn-success btn-add-item" data-type="${typeId}">+</button>`;
                 e.target.closest('.mb-2').querySelector('.item-container').appendChild(wrapper);
                 e.target.className = 'btn btn-danger btn-remove-row';
                 e.target.innerText = '-';
            } else {
                 // Logic for adding more
                 const div = document.createElement('div');
                 div.className = 'input-group input-group-sm mb-1 item-row';
                 div.innerHTML = `<input type="text" name="facilities[${typeId}][]" class="form-control"><button type="button" class="btn btn-success btn-add-item" data-type="${typeId}">+</button>`;
                 e.target.closest('.item-container').appendChild(div);
                 e.target.className = 'btn btn-danger btn-remove-row';
                 e.target.innerText = '-';
            }
        }
        // Remove
        if(e.target.classList.contains('btn-remove-row')) {
            e.target.closest('.item-row').remove();
        }
    });
    
    // Toggle Checkboxes
    document.querySelectorAll('.type-chk, .edit-type-chk').forEach(chk => {
        chk.addEventListener('change', function() {
            const target = document.getElementById(this.dataset.target);
            if(this.checked) {
                target.classList.remove('d-none');
                // For Edit modal, show add button
                if(this.classList.contains('edit-type-chk')) {
                    document.getElementById('edit_btn_'+this.dataset.typeId).classList.remove('d-none');
                    if(target.children.length === 0) createEditInput(target, this.dataset.typeId);
                }
                // For Add modal
                else {
                    if(target.querySelector('.item-container')) { // Adjust selector if needed
                        // Add initial input
                         const container = target.querySelector('.item-container') || target;
                         if(container.children.length === 0) {
                             container.innerHTML = `<div class="input-group input-group-sm mb-1 item-row"><input type="text" name="facilities[${this.id.replace('add_t_','')}][]" class="form-control"><button type="button" class="btn btn-success btn-add-item" data-type="${this.id.replace('add_t_','')}">+</button></div>`;
                         }
                    }
                }
            } else {
                target.classList.add('d-none');
                target.innerHTML = ''; // Clear inputs
                if(this.classList.contains('edit-type-chk')) {
                    document.getElementById('edit_btn_'+this.dataset.typeId).classList.add('d-none');
                }
            }
        });
    });

    // Delete Button
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: 'ยืนยันลบ?', text: "กู้คืนไม่ได้นะ!", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'ลบเลย'
            }).then((r) => { if(r.isConfirmed) window.location.href = href; });
        });
    });
});
</script>
<?= $this->endSection() ?>