<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h3 mb-0 text-gray-800">จัดการสินค้า/บริการเสริม (Vendor Items)</h3>
        <button class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="fas fa-plus-circle me-1"></i> เพิ่มสินค้าใหม่
        </button>
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
                            <th class="ps-4" width="5%">#</th>
                            <th width="10%">รูปภาพ</th>
                            <th width="20%">ชื่อสินค้า</th>
                            <th width="15%" class="text-center">หมวดหมู่</th>
                            <th width="15%">ราคามาตรฐาน</th>
                            <th width="20%"><i class="fas fa-map-marker-alt me-1"></i> สนามกีฬา</th> 
                            <th width="10%" class="text-center">สถานะ</th>
                            <th class="text-end pe-4">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)) : ?>
                            <?php foreach ($items as $index => $item) : ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted"><?= $index + 1 ?></td>
                                    <td>
                                        <?php if ($item['image']) : ?>
                                            <img src="<?= base_url('assets/uploads/items/' . $item['image']) ?>" class="rounded border" width="50" height="50" style="object-fit: cover;">
                                        <?php else : ?>
                                            <div class="bg-light rounded border d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($item['name']) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border"><?= esc($item['type_name'] ?? 'ทั่วไป') ?></span>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">฿<?= number_format($item['price']) ?></span> 
                                        <small class="text-muted">/ <?= esc($item['unit']) ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($item['stadium_name'])): ?>
                                            <div class="d-flex align-items-center text-primary">
                                                <i class="fas fa-futbol me-2"></i>
                                                <?= esc($item['stadium_name']) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small">ไม่ระบุสนาม</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item['status'] == 'active') : ?>
                                            <span class="badge bg-success rounded-pill px-3">พร้อมขาย</span>
                                        <?php else : ?>
                                            <span class="badge bg-secondary rounded-pill px-3">ระงับ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-warning btn-sm btn-edit shadow-sm" 
                                                data-bs-toggle="modal" data-bs-target="#editItemModal"
                                                data-id="<?= $item['id'] ?>"
                                                data-stadium-id="<?= $item['stadium_id'] ?>" 
                                                data-type-id="<?= $item['facility_type_id'] ?>"
                                                data-name="<?= esc($item['name']) ?>"
                                                data-price="<?= esc($item['price']) ?>"
                                                data-unit="<?= esc($item['unit']) ?>"
                                                data-desc="<?= esc($item['description']) ?>"
                                                data-status="<?= esc($item['status']) ?>">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        
                                        <a href="<?= base_url('admin/vendor-items/delete/' . $item['id']) ?>" 
                                           class="btn btn-outline-danger btn-sm shadow-sm btn-delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
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

<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">เพิ่มสินค้าใหม่</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/vendor-items/store') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold">เลือกสนาม (Stadium)</label>
                        <select name="stadium_id" id="add_stadium_id" class="form-select" required>
                            <option value="">-- กรุณาเลือกสนาม --</option>
                            <?php foreach($stadiums as $stadium): ?>
                                <option value="<?= $stadium['id'] ?>"><?= esc($stadium['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">ชื่อสินค้า</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">หมวดหมู่</label>
                            <select name="facility_type_id" id="add_type_id" class="form-select" disabled>
                                <option value="">-- กรุณาเลือกสนามก่อน --</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">ราคาขาย</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">หน่วยนับ</label>
                            <input type="text" name="unit" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">รายละเอียด</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">รูปภาพ</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="active">พร้อมขาย</option>
                            <option value="inactive">ระงับ</option>
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

<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">แก้ไขสินค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/vendor-items/update') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="fw-bold">เลือกสนาม</label>
                        <select name="stadium_id" id="edit_stadium_id" class="form-select" required>
                            <option value="">-- เลือกสนาม --</option>
                            <?php foreach($stadiums as $stadium): ?>
                                <option value="<?= $stadium['id'] ?>"><?= esc($stadium['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">ชื่อสินค้า</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">หมวดหมู่</label>
                            <select name="facility_type_id" id="edit_type_id" class="form-select">
                                <option value="">-- รอโหลดข้อมูล --</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">ราคาขาย</label>
                            <input type="number" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">หน่วยนับ</label>
                            <input type="text" name="unit" id="edit_unit" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">รายละเอียด</label>
                        <textarea name="description" id="edit_desc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">เปลี่ยนรูปภาพ</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">สถานะ</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="active">พร้อมขาย</option>
                            <option value="inactive">ระงับ</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '<?= base_url('/') ?>';

    // ฟังก์ชันโหลดหมวดหมู่ตามสนาม (ใช้ร่วมกันทั้ง Add และ Edit)
    function loadCategories(stadiumId, targetSelectId, selectedValue = null) {
        const targetSelect = document.getElementById(targetSelectId);
        if(!targetSelect) return;
        
        targetSelect.innerHTML = '<option value="">⏳ กำลังโหลดหมวดหมู่...</option>';
        targetSelect.disabled = true;

        if (!stadiumId) {
            targetSelect.innerHTML = '<option value="">-- กรุณาเลือกสนามก่อน --</option>';
            return;
        }

        fetch(`${baseUrl}admin/get-stadium-facility-types/${stadiumId}`)
            .then(res => res.json())
            .then(data => {
                targetSelect.innerHTML = '<option value="">-- เลือกหมวดหมู่ --</option>';
                if (data.length > 0) {
                    data.forEach(type => {
                        const opt = document.createElement('option');
                        opt.value = type.id;
                        opt.textContent = type.name;
                        if (selectedValue && type.id == selectedValue) opt.selected = true;
                        targetSelect.appendChild(opt);
                    });
                    targetSelect.disabled = false;
                } else {
                    targetSelect.innerHTML = '<option value="">❌ สนามนี้ไม่มีหมวดหมู่บริการ</option>';
                    targetSelect.disabled = true;
                }
            })
            .catch(err => {
                console.error(err);
                targetSelect.innerHTML = '<option value="">โหลดข้อมูลล้มเหลว</option>';
            });
    }

    // Logic หน้า Add
    const addStadiumSelect = document.getElementById('add_stadium_id');
    if (addStadiumSelect) {
        addStadiumSelect.addEventListener('change', function() { 
            loadCategories(this.value, 'add_type_id'); 
        });
    }

    // Logic หน้า Edit
    var editModal = document.getElementById('editItemModal');
    if(editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            
            document.getElementById('edit_id').value = btn.getAttribute('data-id');
            document.getElementById('edit_name').value = btn.getAttribute('data-name');
            document.getElementById('edit_price').value = btn.getAttribute('data-price');
            document.getElementById('edit_unit').value = btn.getAttribute('data-unit');
            document.getElementById('edit_desc').value = btn.getAttribute('data-desc');
            document.getElementById('edit_status').value = btn.getAttribute('data-status');
            
            var stadiumId = btn.getAttribute('data-stadium-id');
            var typeId = btn.getAttribute('data-type-id');

            var editStadiumSelect = document.getElementById('edit_stadium_id');
            if(editStadiumSelect) editStadiumSelect.value = stadiumId;
            
            loadCategories(stadiumId, 'edit_type_id', typeId);
        });
    }
    
    const editStadiumSelect = document.getElementById('edit_stadium_id');
    if(editStadiumSelect) {
        editStadiumSelect.addEventListener('change', function() {
            loadCategories(this.value, 'edit_type_id');
        });
    }

    // ✅✅✅ ส่วนจัดการปุ่มลบ (SweetAlert2) ✅✅✅
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