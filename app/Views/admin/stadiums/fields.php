<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

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
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100 border-top-primary">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-plus-circle me-2"></i>เพิ่มสนามย่อยใหม่
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/stadiums/fields/create') ?>" method="post">
                        <input type="hidden" name="stadium_id" value="<?= $stadium['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="fw-bold">ชื่อสนาม / เลขที่ <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="เช่น สนาม 1, Court A" required>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">รายละเอียดเบื้องต้น</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="เช่น หญ้าเทียม 7 คน"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold">สถานะเริ่มต้น</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>✅ เปิดใช้งาน (Active)</option>
                                <option value="maintenance">🛠️ ปิดปรับปรุง (Maintenance)</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                            <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-dark">รายการสนามย่อยทั้งหมด</h6>
                    <span class="badge bg-light text-dark border"><?= count($fields) ?> รายการ</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th width="30%">ชื่อสนาม</th>
                                    <th width="30%">รายละเอียด</th>
                                    <th width="20%">สถานะ</th>
                                    <th class="text-end pe-4">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($fields)): ?>
                                    <?php foreach($fields as $index => $field): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted"><?= $index + 1 ?></td>
                                        <td><span class="fw-bold text-dark"><?= esc($field['name']) ?></span></td>
                                        <td>
                                            <span class="text-muted small">
                                                <?= esc($field['description'] ?? '-') ?>
                                            </span>
                                        </td>
                                        
                                        <td>
                                            <?php if($field['status'] == 'active'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">
                                                    <i class="fas fa-check-circle me-1"></i> Active
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">
                                                    <i class="fas fa-tools me-1"></i> Maintenance
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-end pe-4">
                                            <button type="button" class="btn btn-warning btn-sm btn-edit me-1 shadow-sm text-dark"
                                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                                    data-id="<?= $field['id'] ?>"
                                                    data-name="<?= esc($field['name']) ?>"
                                                    data-desc="<?= esc($field['description'] ?? '') ?>"
                                                    data-status="<?= esc($field['status']) ?>"> <i class="fas fa-pen"></i>
                                            </button>

                                            <a href="<?= base_url('admin/stadiums/fields/delete/' . $field['id']) ?>" 
                                               class="btn btn-outline-danger btn-sm shadow-sm btn-delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">ไม่มีข้อมูล</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit me-2"></i>แก้ไขข้อมูล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/stadiums/fields/update') ?>" method="post">
                <div class="modal-body p-4">
                    <input type="hidden" name="stadium_id" value="<?= $stadium['id'] ?>">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-3">
                        <label class="fw-bold">ชื่อสนามย่อย</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">รายละเอียด</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">สถานะ</label>
                        <select name="status" id="edit_status" class="form-select">
                            <option value="active">✅ เปิดใช้งาน (Active)</option>
                            <option value="maintenance">🛠️ ปิดปรับปรุง (Maintenance)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.getAttribute('data-id');
                document.getElementById('edit_name').value = this.getAttribute('data-name');
                document.getElementById('edit_description').value = this.getAttribute('data-desc');
                document.getElementById('edit_status').value = this.getAttribute('data-status'); // ดึงสถานะมาใส่
            });
        });
    });
</script>

<?= $this->endSection() ?>