<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h3 mb-0 text-gray-800">จัดการสนาม (Stadiums)</h3>
        <a href="<?= base_url('admin/stadiums/create') ?>" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> เพิ่มสนามใหม่
        </a>
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

    <div class="card shadow mb-3 border-0">
        <div class="card-body py-2 d-flex align-items-center">
            <label class="fw-bold me-2 mb-0"><i class="fas fa-filter text-muted me-1"></i> กรองตามรูปแบบ:</label>
            <select id="filterType" class="form-select w-auto shadow-sm border-secondary">
                <option value="all">ทั้งหมด (All)</option>
                <option value="complex">🏢 มีสนามย่อย</option>
                <option value="single">🏟️ ไม่มีสนามย่อย</option>
            </select>
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">รายชื่อสนามทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">#</th>
                            <th width="10%">รูปปก</th>
                            <th width="20%">ชื่อสนาม</th>
                            <th width="15%">ประเภทกีฬา</th>
                            <th width="15%">เจ้าของ (Vendor)</th>
                            <th width="10%" class="text-center">แผนที่</th>
                            <th width="25%" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($stadiums)): ?>
                            <?php foreach($stadiums as $stadium): ?>
                            
                            <tr data-type="<?= $stadium['booking_type'] ?? 'complex' ?>">
                                
                                <td class="text-center fw-bold"><?= $stadium['id'] ?></td>
                                
                                <td class="text-center">
                                    <?php 
                                        $images = json_decode($stadium['outside_images'], true);
                                        $coverImage = !empty($images[0]) ? $images[0] : null;
                                    ?>
                                    <?php if($coverImage): ?>
                                        <img src="<?= base_url('assets/uploads/stadiums/' . $coverImage) ?>" 
                                             class="rounded shadow-sm" 
                                             style="width: 60px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fas fa-image"></i> ไม่มีรูป</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if(($stadium['booking_type'] ?? 'complex') == 'complex'): ?>
                                        <span class="badge bg-primary mb-1" style="font-size: 0.65rem;">มีสนามย่อย</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark mb-1" style="font-size: 0.65rem;">ไม่มีสนามย่อย</span>
                                    <?php endif; ?>
                                    
                                    <div class="fw-bold text-dark"><?= esc($stadium['name']) ?></div>
                                    <div class="small text-muted text-truncate" style="max-width: 150px;">
                                        <?= esc($stadium['description']) ?>
                                    </div>
                                </td>

                                <td>
                                    <?php if(!empty($stadium['category_emoji'])): ?>
                                        <span class="me-1"><?= $stadium['category_emoji'] ?></span>
                                    <?php endif; ?>
                                    <?= esc($stadium['category_name']) ?>
                                </td>

                                <td>
                                    <div class="small fw-bold"><?= esc($stadium['vendor_name']) ?></div>
                                </td>

                                <td class="text-center">
                                    <?php if(!empty($stadium['lat']) && !empty($stadium['lng'])): ?>
                                        <a href="https://www.google.com/maps?q=<?= $stadium['lat'] ?>,<?= $stadium['lng'] ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary border-0">
                                            <i class="fas fa-map-marker-alt"></i> Map
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        
                                        <?php if(($stadium['booking_type'] ?? 'complex') == 'complex'): ?>
                                            <a href="<?= base_url('admin/stadiums/fields/' . $stadium['id']) ?>" 
                                               class="btn btn-info btn-sm text-white shadow-sm" 
                                               title="จัดการสนามย่อย">
                                                <i class="fas fa-list-ul"></i> สนามย่อย
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('admin/stadiums/fields/' . $stadium['id']) ?>" 
                                               class="btn btn-success btn-sm text-white shadow-sm" 
                                               title="ตั้งค่าราคาและข้อมูล">
                                                <i class="fas fa-tag"></i> ตั้งค่าสนาม
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?= base_url('admin/stadiums/view/' . $stadium['id']) ?>" 
                                           class="btn btn-secondary btn-sm shadow-sm" title="ดูรายละเอียด">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="<?= base_url('admin/stadiums/edit/' . $stadium['id']) ?>" 
                                           class="btn btn-warning btn-sm text-dark shadow-sm" title="แก้ไข">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        
                                        <a href="<?= base_url('admin/stadiums/delete/' . $stadium['id']) ?>" 
                                           class="btn btn-danger btn-sm shadow-sm btn-delete" 
                                           title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block text-gray-300"></i>
                                    ยังไม่มีข้อมูลสนาม
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Script กรองตาราง
        const filterDropdown = document.getElementById('filterType');
        if(filterDropdown) {
            filterDropdown.addEventListener('change', function() {
                let filterValue = this.value;
                let rows = document.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    let rowType = row.getAttribute('data-type');
                    if (filterValue === 'all' || rowType === filterValue) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Script ปุ่มลบ
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); 
                const href = this.getAttribute('href'); 

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: "หากลบสนามนี้ ข้อมูลทั้งหมดจะหายไป!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        });
    });
</script>

<?= $this->endSection() ?>