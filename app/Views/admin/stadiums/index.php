<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

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


    <div class="card shadow mb-4 border-0">
        <div class="card-body py-3">
            <form action="<?= base_url('admin/stadiums') ?>" method="get">
                <div class="d-flex justify-content-between align-items-center">
                    
                    <div class="d-flex align-items-center">
                        <i class="fas fa-filter text-gray-400 me-2"></i>
                        <span class="me-2 fw-bold text-gray-700">กรองตามรูปแบบ:</span>
                        <select name="booking_type" class="form-select form-select-sm border-0 bg-light" 
                                style="width: auto;" onchange="this.form.submit()">
                            <option value="all" <?= ($filter ?? '') == 'all' ? 'selected' : '' ?>>ทั้งหมด (All)</option>
                            <option value="complex" <?= ($filter ?? '') == 'complex' ? 'selected' : '' ?>>🏢 มีสนามย่อย (Complex)</option>
                            <option value="single" <?= ($filter ?? '') == 'single' ? 'selected' : '' ?>>🏟️ ไม่มีสนามย่อย (Single)</option>
                        </select>
                    </div>

                    <div class="input-group" style="width: 300px;">
                        <input type="text" name="search" class="form-control form-control-sm bg-light border-0 small" 
                               placeholder="ค้นหาชื่อสนาม..." aria-label="Search" 
                               value="<?= esc($search ?? '') ?>">
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                        <?php if(!empty($search)): ?>
                            <a href="<?= base_url('admin/stadiums') ?>" class="btn btn-secondary btn-sm" title="ล้างค่า">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </form>
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
                                    <div class="fw-bold text-dark"><?= esc($stadium['name']) ?></div>
                                    <div class="small text-muted text-truncate" style="max-width: 150px;">
                                        <?= esc($stadium['description']) ?>
                                    </div>
                                    <?php if(($stadium['booking_type'] ?? 'complex') == 'complex'): ?>
                                        <span class="badge bg-primary mb-1" style="font-size: 0.65rem;">มีสนามย่อย</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark mb-1" style="font-size: 0.65rem;">ไม่มีสนามย่อย</span>
                                    <?php endif; ?>
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
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary border-0 btn-view-map"
                                                data-lat="<?= $stadium['lat'] ?>"
                                                data-lng="<?= $stadium['lng'] ?>"
                                                data-name="<?= esc($stadium['name']) ?>">
                                            <i class="fas fa-map-marker-alt"></i> Map
                                        </button>
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

<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-map-marker-alt text-danger me-2"></i>ตำแหน่ง: <span id="mapModalTitle" class="fw-bold"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="leafletMap" style="width: 100%; height: 450px;"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- ส่วนจัดการแผนที่ (Map) ---
        let map = null;
        let marker = null;
        const mapModal = document.getElementById('mapModal');

        // เมื่อกดปุ่ม Map ให้เปิด Modal
        document.querySelectorAll('.btn-view-map').forEach(btn => {
            btn.addEventListener('click', function() {
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const name = this.getAttribute('data-name');

                // ตั้งชื่อหัวข้อ Modal
                document.getElementById('mapModalTitle').textContent = name;

                // เปิด Modal (ใช้ Bootstrap 5)
                var myModal = new bootstrap.Modal(mapModal);
                myModal.show();

                // รอ Modal เปิดเสร็จค่อยวาดแผนที่ (กันแผนที่เพี้ยน)
                mapModal.addEventListener('shown.bs.modal', function () {
                    if (!map) {
                        map = L.map('leafletMap');
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);
                    }
                    
                    // set view ไปที่พิกัด
                    map.setView([lat, lng], 15);

                    // ปักหมุด
                    if (marker) map.removeLayer(marker);
                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup(`<b>${name}</b><br>Lat: ${lat}, Lng: ${lng}`)
                        .openPopup();
                    
                    // บังคับคำนวณขนาดใหม่
                    map.invalidateSize();
                }, { once: true }); // event นี้รันแค่ครั้งเดียวต่อการเปิด
            });
        });


        // --- ส่วน Filter (คงเดิมตามที่คุณมี) ---
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

        // --- ส่วน Delete (คงเดิมตามที่คุณมี) ---
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