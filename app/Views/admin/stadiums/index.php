<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<!-- Leaflet CSS (สำหรับแผนที่) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container-fluid p-0">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h3 mb-0 text-gray-800">จัดการสนามกีฬา (Stadiums)</h3>
        <a href="<?= base_url('admin/stadiums/create') ?>" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Stadium
        </a>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success fade show">
            <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold" style="color: var(--mint-primary);">รายชื่อสนามทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-datatable align-middle" width="100%">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Cover</th>
                            <th class="text-start">Name</th>
                            <th>Category</th>
                            <th>Vendor</th>
                            <th>Price/Hour</th>
                            <th width="5%">Map</th>
                            <th width="15%">Actions</th> <!-- จัดการความกว้างช่องนี้ -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($stadiums)): ?>
                            <?php foreach($stadiums as $stadium): ?>
                                <?php 
                                    $images = json_decode($stadium['outside_images'] ?? '[]', true);
                                    $cover = !empty($images) ? $images[0] : null;
                                ?>
                            <tr>
                                <td class="text-center"><?= $stadium['id'] ?></td>
                                <td class="text-center">
                                    <?php if($cover): ?>
                                        <img src="<?= base_url('assets/uploads/stadiums/' . $cover) ?>" 
                                             class="img-fluid rounded shadow-sm" 
                                             style="width: 60px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted text-xs"><i class="fas fa-image"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-dark"><?= esc($stadium['name']) ?></td>
                                
                                <!-- แสดง Emoji + ชื่อ Category -->
                                <td>
                                    <span class="me-1"><?= $stadium['category_emoji'] ?? '' ?></span>
                                    <?= esc($stadium['category_name']) ?>
                                </td>
                                
                                <td><small class="text-muted"><?= esc($stadium['vendor_name']) ?></small></td>
                                <td class="fw-bold text-success text-end">฿<?= number_format($stadium['price'], 0) ?></td>
                                
                                <!-- ปุ่ม Map -->
                                <td class="text-center">
                                    <?php if(!empty($stadium['lat']) && !empty($stadium['lng'])): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary btn-map border-0"
                                                data-lat="<?= $stadium['lat'] ?>"
                                                data-lng="<?= $stadium['lng'] ?>"
                                                data-name="<?= esc($stadium['name']) ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#mapModal"
                                                title="ดูแผนที่">
                                            <i class="fas fa-map-marker-alt fa-lg"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted text-xs">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- [แก้ไข] ส่วน Actions: จัดเรียงใหม่เป็นแนวนอน + ใช้ไอคอน -->
                              <td>
    <div class="d-flex justify-content-center gap-2">
        
        <a href="<?= base_url('admin/stadiums/fields/' . $stadium['id']) ?>" 
           class="btn btn-primary btn-sm shadow-sm" 
           title="จัดการสนามย่อย (เช่น สนาม 1, สนาม 2)">
            <i class="fas fa-layer-group"></i> สนามย่อย
        </a>

        <a href="<?= base_url('admin/stadiums/view/' . $stadium['id']) ?>" 
           class="btn btn-info btn-sm text-white shadow-sm" 
           title="ดูรายละเอียด">
            <i class="fas fa-eye"></i>
        </a>

        <a href="<?= base_url('admin/stadiums/edit/' . $stadium['id']) ?>" 
           class="btn btn-warning btn-sm shadow-sm"
           title="แก้ไข">
            <i class="fas fa-edit"></i>
        </a>
        
        <a href="<?= base_url('admin/stadiums/delete/' . $stadium['id']) ?>" 
           class="btn btn-danger btn-sm btn-delete shadow-sm"
           title="ลบ"
           onclick="return confirm('ยืนยันการลบสนามนี้? ข้อมูลสนามย่อยและการจองทั้งหมดจะถูกลบไปด้วย');">
            <i class="fas fa-trash"></i>
        </a>
    </div>
</td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted py-5">ไม่พบข้อมูลสนาม</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal แผนที่ -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 overflow-hidden">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-map-marked-alt me-2"></i><span id="mapModalTitle">Location</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="viewMap" style="width: 100%; height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let map = null;
    let marker = null;

    const mapModal = document.getElementById('mapModal');
    mapModal.addEventListener('shown.bs.modal', function (event) {
        const button = event.relatedTarget;
        const lat = button.getAttribute('data-lat');
        const lng = button.getAttribute('data-lng');
        const name = button.getAttribute('data-name');

        document.getElementById('mapModalTitle').innerText = name;

        if (!map) {
            map = L.map('viewMap');
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
        }

        const latLng = [parseFloat(lat), parseFloat(lng)];
        map.setView(latLng, 15);

        if (marker) map.removeLayer(marker);
        
        marker = L.marker(latLng).addTo(map)
            .bindPopup("<b>" + name + "</b><br>อยู่ที่นี่")
            .openPopup();

        setTimeout(function(){ map.invalidateSize();}, 10);
    });
</script>

<?= $this->endSection() ?>