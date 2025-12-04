<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= base_url('admin/stadiums') ?>" class="text-muted text-decoration-none">
                <i class="fas fa-arrow-left"></i> กลับไปหน้ารวม
            </a>
            <h3 class="h3 mt-2 text-gray-800 font-weight-bold">รายละเอียดสนาม</h3>
        </div>
        <div>
            <?php if(($stadium['booking_type'] ?? 'complex') == 'complex'): ?>
                <a href="<?= base_url('admin/stadiums/fields/' . $stadium['id']) ?>" class="btn btn-info text-white shadow-sm me-1">
                    <i class="fas fa-list-ul"></i> จัดการราคา/สถานะ
                </a>
            <?php else: ?>
                <a href="<?= base_url('admin/stadiums/fields/' . $stadium['id']) ?>" class="btn btn-success text-white shadow-sm me-1">
                    <i class="fas fa-tag"></i> ตั้งค่าสนาม
                </a>
            <?php endif; ?>

            <a href="<?= base_url('admin/vendor-items') ?>" class="btn btn-primary text-white shadow-sm me-1">
                <i class="fas fa-box-open"></i> สินค้า/บริการ
            </a>

            <a href="<?= base_url('admin/stadiums/edit/' . $stadium['id']) ?>" class="btn btn-warning text-dark shadow-sm">
                <i class="fas fa-pen"></i> แก้ไข
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-image me-2"></i>รูปภาพสนาม</h6>
                </div>
                <div class="card-body">
                    <?php 
                        $outsideImages = json_decode($stadium['outside_images'] ?? '[]', true);
                        $coverImage = !empty($outsideImages[0]) ? $outsideImages[0] : null;
                    ?>
                    <?php if($coverImage): ?>
                        <div class="mb-3 text-center bg-light rounded p-2">
                            <img src="<?= base_url('assets/uploads/stadiums/' . $coverImage) ?>" 
                                 class="img-fluid rounded shadow-sm" style="max-height: 400px;">
                            <p class="small text-muted mt-2 mb-0"><i class="fas fa-tag me-1"></i>รูปปก (Cover)</p>
                        </div>
                    <?php endif; ?>

                    <?php 
                        $insideImages = json_decode($stadium['inside_images'] ?? '[]', true);
                    ?>
                    <?php if(!empty($insideImages)): ?>
                        <h6 class="font-weight-bold mt-4">รูปภาพเพิ่มเติม</h6>
                        <div class="row g-2">
                            <?php foreach($insideImages as $img): ?>
                                <div class="col-md-3 col-6">
                                    <img src="<?= base_url('assets/uploads/stadiums/' . $img) ?>" 
                                         class="img-fluid rounded border shadow-sm w-100" 
                                         style="height: 120px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-concierge-bell me-2"></i>สิ่งอำนวยความสะดวก & บริการ</h6>
                </div>
                <div class="card-body">
                    <?php 
                        $allServices = [];

                        // 2.1 ดึงประเภทจากสิ่งอำนวยความสะดวกที่ติ๊กเลือก (Facilities)
                        if (!empty($facilities)) {
                            foreach ($facilities as $type => $items) {
                                // เช็คว่าในหมวดนั้นมีรายการจริงๆ ไหม
                                if (!empty(array_filter($items))) {
                                     if (!in_array($type, $allServices)) {
                                         $allServices[] = $type; 
                                     }
                                }
                            }
                        }

                        // 2.2 ดึงประเภทจากสินค้าที่มีขาย (Vendor Items)
                        if (!empty($vendor_items)) {
                            // ดึงชื่อ type_name ของสินค้าแต่ละชิ้น
                            $itemTypes = array_column($vendor_items, 'type_name');
                            $uniqueTypes = array_unique($itemTypes);
                            
                            foreach ($uniqueTypes as $type) {
                                if (!empty($type) && !in_array($type, $allServices)) {
                                    $allServices[] = $type;
                                }
                            }
                        }
                    ?>

                    <?php if (!empty($allServices)): ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($allServices as $service): ?>
                                <span class="badge bg-info text-white border px-3 py-2 shadow-sm rounded-pill" style="font-size: 0.9rem;">
                                    <i class="fas fa-check-circle me-1"></i> <?= esc($service) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-2 text-muted small">
                            * รายการข้างต้นรวมสิ่งอำนวยความสะดวกพื้นฐานและประเภทบริการเสริมที่มีจำหน่าย
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-box-open fa-2x mb-2 text-gray-300"></i><br>
                            ยังไม่มีข้อมูลสิ่งอำนวยความสะดวก
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-box-open me-2"></i>รายละเอียดสินค้า/บริการเสริม
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($vendor_items)) : ?>
                        <div class="row g-3">
                            <?php foreach ($vendor_items as $item) : ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center border rounded p-2 h-100 bg-light">
                                        <div class="flex-shrink-0">
                                            <?php if($item['image']): ?>
                                                <img src="<?= base_url('assets/uploads/items/'.$item['image']) ?>" 
                                                     class="rounded bg-white border" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-white rounded border d-flex align-items-center justify-content-center text-muted" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex-grow-1 ms-3">
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                                <?= esc($item['name']) ?>
                                            </div>
                                            <div class="badge bg-secondary text-white small" style="font-size: 0.7rem;">
                                                <?= esc($item['type_name'] ?? 'ทั่วไป') ?>
                                            </div>
                                            <div class="text-success fw-bold small mt-1">
                                                ฿<?= number_format($item['price'], 2) ?> / <?= esc($item['unit']) ?>
                                            </div>
                                        </div>

                                        <div class="ms-2">
                                            <?php if($item['status'] == 'active'): ?>
                                                <span class="badge bg-success rounded-pill" title="พร้อมขาย"><i class="fas fa-check"></i></span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary rounded-pill" title="ไม่พร้อม">Inactive</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-shopping-basket fa-2x mb-3 text-gray-300"></i><br>
                            <p class="mb-3">ไม่มีสินค้า/บริการเสริมสำหรับ Vendor รายนี้</p>
                            
                            <a href="<?= base_url('admin/vendor-items') ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus-circle"></i> ไปหน้าจัดการสินค้า
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if(($stadium['booking_type'] ?? 'complex') == 'complex'): ?>
            <div class="card shadow mb-4 border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-futbol me-2"></i>สนามย่อยภายใน
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($fields)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" width="10%">รูป</th>
                                        <th width="25%">ชื่อสนาม</th>
                                        <th width="25%">รายละเอียด</th>
                                        <th width="15%">ราคา/ชม.</th>
                                        <th width="15%">ราคา/วัน</th>
                                        <th class="text-center" width="10%">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fields as $field): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <?php 
                                                    $f_imgs = json_decode($field['outside_images'] ?? '[]', true); 
                                                    $f_thumb = !empty($f_imgs[0]) ? $f_imgs[0] : null;
                                                ?>
                                                <?php if($f_thumb): ?>
                                                    <img src="<?= base_url('assets/uploads/fields/'.$f_thumb) ?>" class="rounded border shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded border text-center pt-2" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-dark"><?= esc($field['name']) ?></td>
                                            <td class="text-muted small"><?= esc($field['description']) ?></td>
                                            <td><span class="text-success fw-bold">฿<?= number_format($field['price']) ?></span></td>
                                            <td>
                                                <?php if(!empty($field['price_daily'])): ?>
                                                    <span class="text-info fw-bold">฿<?= number_format($field['price_daily']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($field['status'] == 'active'): ?>
                                                    <span class="badge bg-success rounded-pill">พร้อมใช้</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill">ปิด</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            ยังไม่มีสนามย่อย
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?> 
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">ข้อมูลทั่วไป</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4 class="font-weight-bold text-dark mb-1"><?= esc($stadium['name']) ?></h4>
                        <span class="badge bg-primary me-1"><?= esc($stadium['category_name']) ?></span>
                        <span class="badge bg-secondary">
                            <i class="fas fa-map-marker-alt me-1"></i><?= esc($stadium['province']) ?>
                        </span>
                        <div class="mt-2">
                            <?php if(($stadium['booking_type'] ?? 'complex') == 'complex'): ?>
                                <span class="badge bg-light text-dark border">🏢 มีสนามย่อย (Complex)</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border">🏟️ จองเหมา (Single)</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

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
                            <span class="text-muted"><i class="fas fa-phone me-2"></i>เบอร์ติดต่อ</span>
                            <span class="fw-bold text-dark"><?= esc($stadium['contact_phone'] ?? '-') ?></span>
                        </li>
                    </ul>

                    <div class="mt-4">
                        <h6 class="font-weight-bold small text-muted text-uppercase mb-2">เจ้าของสนาม (Vendor)</h6>
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <div class="bg-white rounded-circle p-2 shadow-sm me-3 text-primary">
                                <i class="fas fa-store fa-lg"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark"><?= esc($stadium['vendor_name']) ?></div>
                                <div class="small text-muted">
                                    <i class="fas fa-envelope me-1"></i> <?= esc($stadium['vendor_email'] ?? '-') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">ที่ตั้ง (Location)</h6>
                </div>
                <div class="card-body p-0">
                    <?php if(!empty($stadium['lat']) && !empty($stadium['lng'])): ?>
                        <iframe 
                            width="100%" 
                            height="300" 
                            style="border:0;" 
                            loading="lazy" 
                            allowfullscreen
                            src="https://maps.google.com/maps?q=<?= $stadium['lat'] ?>,<?= $stadium['lng'] ?>&z=15&output=embed">
                        </iframe>
                        <div class="p-3 bg-light small">
                            <i class="fas fa-map-pin me-1 text-danger"></i> <?= esc($stadium['address']) ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-map-marked-alt fa-3x mb-2 text-gray-300"></i><br>
                            ไม่มีข้อมูลพิกัดแผนที่
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection() ?>