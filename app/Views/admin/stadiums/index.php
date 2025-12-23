<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h3 mb-0 text-gray-800">จัดการสนาม (Stadiums)</h3>
        <div>
            <button id="btnBulkDelete" class="btn btn-danger shadow-sm me-2 d-none">
                <i class="fas fa-trash-alt me-1"></i> ลบที่เลือก (<span id="selectedCount">0</span>)
            </button>
            <a href="<?= base_url('admin/stadiums/create') ?>" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 me-1"></i> เพิ่มสนามใหม่
            </a>
        </div>
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
                <div class="d-flex align-items-center gap-2"> 
                    <div style="width: 200px;">
                        <select name="category_id" class="form-select form-select-sm bg-light border-0" onchange="this.form.submit()">
                            <option value="all">-- ทุกประเภทกีฬา --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($sport_filter) && $sport_filter == $cat['id']) ? 'selected' : '' ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="input-group" style="width: 300px;">
                        <input type="text" id="searchInput" name="search" class="form-control form-control-sm bg-light border-0 small" 
                               placeholder="ค้นหาชื่อสนาม..." aria-label="Search" 
                               value="<?= esc($search ?? '') ?>">
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                        <?php if(!empty($search) || (!empty($sport_filter) && $sport_filter != 'all')): ?> 
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
                <table class="table table-bordered table-hover align-middle" id="stadiumTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">
                                <input type="checkbox" id="checkAll" class="form-check-input">
                            </th>
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
                            
                            <tr data-type="complex">
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input check-item" value="<?= $stadium['id'] ?>">
                                </td>
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

                                <td data-search="<?= esc($stadium['name']) ?>">
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
                                        <a href="<?= base_url('admin/stadiums/fields/' . $stadium['id']) ?>" 
                                           class="btn btn-success btn-sm text-white shadow-sm" 
                                           title="ตั้งค่าราคาและข้อมูล">
                                            <i class="fas fa-tag"></i> ตั้งค่าสนาม
                                        </a>

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
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {

        // ยังคงบรรทัดนี้ไว้ตามที่ขอ (เผื่อกันเหนียว)
        $.fn.dataTable.ext.errMode = 'none';
        
        var table = $('#stadiumTable').DataTable({
            "dom": 'lrtip', // Hide default search box (f)
            "searching": true, 
            "lengthMenu": [ [10, 50, 100, -1], [10, 50, 100, "ทั้งหมด"] ],
            "language": {
                "lengthMenu": "แสดง _MENU_ รายการ",
                "zeroRecords": `<div class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block text-gray-300"></i>
                                    ไม่พบข้อมูลสนาม
                                </div>`,
                "info": "หน้า _PAGE_ จาก _PAGES_",
                "infoEmpty": "ไม่มีข้อมูล",
                "infoFiltered": "(กรองจาก _MAX_ รายการ)",
                "paginate": {
                    "first": "หน้าแรก",
                    "last": "สุดท้าย",
                    "next": "ถัดไป",
                    "previous": "ก่อนหน้า"
                }
            },
            "ordering": false // ปรับให้สอดคล้องกับหน้าจัดการรีวิว (ถ้าต้องการให้เรียงตาม ID ล่าสุดจาก Server)
        });

        // Custom "Starts With" filter logic for Stadium Table
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                // Apply only to this specific table
                if (settings.nTable.id !== 'stadiumTable') return true;
                
                var searchTerm = $('#searchInput').val().toLowerCase().trim();
                if (!searchTerm) return true;

                // Column 1 is ID (#), Column 3 is Stadium Name
                var idValue = data[1].toLowerCase().trim();
                var stadiumName = data[3].toLowerCase().trim();

                // Match if either column STARTS WITH the search term
                return idValue.indexOf(searchTerm) === 0 || stadiumName.indexOf(searchTerm) === 0;
            }
        );

        // Bind keyup event to trigger redraw
        $('#searchInput').on('keyup', function() {
            table.draw();
        });

        // Prevent form submission on enter in search box
        $('#searchInput').on('keypress', function(e) {
            if(e.which == 13) {
                e.preventDefault();
                table.draw();
                return false;
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        let map = null;
        let marker = null;
        const mapModal = document.getElementById('mapModal');

        document.querySelectorAll('.btn-view-map').forEach(btn => {
            btn.addEventListener('click', function() {
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const name = this.getAttribute('data-name');
                document.getElementById('mapModalTitle').textContent = name;
                var myModal = new bootstrap.Modal(mapModal);
                myModal.show();

                mapModal.addEventListener('shown.bs.modal', function () {
                    if (!map) {
                        map = L.map('leafletMap');
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);
                    }
                    map.setView([lat, lng], 15);
                    if (marker) map.removeLayer(marker);
                    marker = L.marker([lat, lng]).addTo(map).bindPopup(`<b>${name}</b><br>Lat: ${lat}, Lng: ${lng}`).openPopup();
                    map.invalidateSize();
                }, { once: true });
            });
        });

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

        // --- Bulk Delete Logic ---
        const checkAll = document.getElementById('checkAll');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const selectedCountSpan = document.getElementById('selectedCount');
        
        // Listen ONLY to checkboxes with class 'check-item'
        $(document).on('change', '.check-item', function() {
            updateBulkButton();
        });

        if(checkAll) {
            checkAll.addEventListener('change', function() {
                $('.check-item').prop('checked', this.checked);
                updateBulkButton();
            });
        }

        function updateBulkButton() {
            const checkedCount = $('.check-item:checked').length;
            selectedCountSpan.textContent = checkedCount;
            if (checkedCount > 0) {
                btnBulkDelete.classList.remove('d-none');
            } else {
                btnBulkDelete.classList.add('d-none');
            }
        }

        if(btnBulkDelete) {
            btnBulkDelete.addEventListener('click', function() {
                const selectedIds = $('.check-item:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) return;

                Swal.fire({
                    title: 'ยืนยันลบ ' + selectedIds.length + ' รายการ?',
                    text: "ข้อมูลและรูปภาพทั้งหมดจะหายไปและกู้คืนไม่ได้!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบทิ้งเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '<?= base_url("admin/stadiums/deleteBatch") ?>',
                            type: 'POST',
                            data: {
                                ids: selectedIds,
                                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('สำเร็จ!', response.message, 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('ผิดพลาด!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                            }
                        });
                    }
                });
            });
        }
    });
</script>

<?= $this->endSection() ?>