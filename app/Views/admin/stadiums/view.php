<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="h3 mb-0 text-gray-800">รายละเอียดสนาม</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0 small">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/stadiums') ?>">จัดการสนาม</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($stadium['name']) ?></li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/stadiums') ?>" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> ย้อนกลับ
            </a>
            <a href="<?= base_url('admin/stadiums/edit/' . $stadium['id']) ?>" class="btn btn-warning btn-sm shadow-sm fw-bold">
                <i class="fas fa-edit me-1"></i> แก้ไข
            </a>
        </div>
    </div>

    <div class="row">
        
        <div class="col-lg-8 mb-4">
            
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-images me-2"></i>รูปภาพสนาม</h6>
                </div>
                <div class="card-body">
                    <?php 
                        $outsideArr = json_decode($stadium['outside_images'] ?? '[]', true) ?: [];
                        $cover = $outsideArr[0] ?? null;
                        $insideArr = json_decode($stadium['inside_images'] ?? '[]', true) ?: [];
                    ?>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="border rounded bg-light text-center" style="min-height: 300px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <?php if ($cover): ?>
                                    <img src="<?= base_url('assets/uploads/stadiums/' . $cover) ?>" class="img-fluid" style="max-height: 400px; width: 100%; object-fit: contain;">
                                <?php else: ?>
                                    <span class="text-muted">ไม่มีรูปปก</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-muted small mt-1 mb-0 text-center"><i class="fas fa-tag"></i> รูปปก (Cover)</p>
                        </div>

                        <?php if (!empty($insideArr)): ?>
                            <?php foreach ($insideArr as $img): ?>
                                <div class="col-6 col-sm-3 mb-2">
                                    <a href="<?= base_url('assets/uploads/stadiums/' . $img) ?>" target="_blank" class="d-block border rounded h-100">
                                        <img src="<?= base_url('assets/uploads/stadiums/' . $img) ?>" class="img-fluid rounded" style="height: 80px; width: 100%; object-fit: cover;">
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-align-left me-2"></i>รายละเอียดเพิ่มเติม</h6>
                </div>
                <div class="card-body text-dark" style="line-height: 1.7;">
                    <?= nl2br(esc($stadium['description'])) ?>
                </div>
            </div>

        </div>

        <div class="col-lg-4 mb-4">

            <div class="card shadow mb-4 border-0 border-top-primary">
                <div class="card-body">
                    <h4 class="font-weight-bold text-dark mb-1"><?= esc($stadium['name']) ?></h4>
                    <div class="mb-3">
                        <span class="badge bg-info text-white"><?= esc($stadium['category_name']) ?></span>
                        <span class="badge bg-secondary"><i class="fas fa-map-marker-alt me-1"></i><?= esc($stadium['province']) ?></span>
                    </div>
                    
                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">ราคา/ชั่วโมง</span>
                        <span class="h4 font-weight-bold text-success mb-0">฿<?= number_format($stadium['price'] ?? 0, 0) ?></span>
                    </div>

                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="far fa-clock me-2"></i>เวลาเปิด</span>
                            <span class="fw-bold text-dark"><?= substr($stadium['open_time'], 0, 5) ?> น.</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="far fa-clock me-2"></i>เวลาปิด</span>
                            <span class="fw-bold text-dark"><?= substr($stadium['close_time'], 0, 5) ?> น.</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted"><i class="fas fa-phone me-2"></i>เบอร์ติดต่อสนาม</span>
                            <span class="fw-bold text-dark"><?= esc($stadium['contact_phone'] ?? '-') ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-dark">ข้อมูลเจ้าของ (Vendor)</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-store text-secondary fa-lg"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold text-dark"><?= esc($stadium['vendor_name']) ?></div>
                            <div class="small text-muted">Vendor ID: <?= $stadium['vendor_id'] ?></div>
                        </div>
                    </div>
                    <div class="small text-muted">
                        <div class="mb-1"><i class="fas fa-envelope me-2 text-center" style="width:20px;"></i> <?= esc($stadium['vendor_email']) ?></div>
                        <div><i class="fas fa-phone me-2 text-center" style="width:20px;"></i> <?= esc($stadium['vendor_phone']) ?></div>
                    </div>
                    <hr>
                    <a href="<?= base_url('admin/users/vendors') ?>" class="btn btn-light btn-sm w-100 text-muted">ดูข้อมูล Vendor ทั้งหมด</a>
                </div>
            </div>

            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-dark">ที่ตั้ง (Location)</h6>
                </div>
                <div class="card-body p-0">
                    <div id="viewMap" style="width: 100%; height: 250px;"></div>
                    <div class="p-3">
                        <p class="small text-muted mb-2">
                            <i class="fas fa-map-pin me-1 text-danger"></i> <?= esc($stadium['address']) ?>
                        </p>
                        <?php if(!empty($stadium['map_link'])): ?>
                            <a href="<?= esc($stadium['map_link']) ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-external-link-alt me-1"></i> เปิด Google Maps
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var lat = <?= $stadium['lat'] ?? 'null' ?>;
        var lng = <?= $stadium['lng'] ?? 'null' ?>;

        if (lat && lng) {
            var map = L.map('viewMap').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);
            
            L.marker([lat, lng]).addTo(map).bindPopup("<b><?= esc($stadium['name']) ?></b>");
        } else {
            document.getElementById('viewMap').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 bg-light text-muted small">ไม่ระบุพิกัด</div>';
        }
    });
</script>

<style>
    .border-top-primary {
        border-top: 4px solid var(--mint-primary) !important;
    }
</style>

<?= $this->endSection() ?>