<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php helper('form'); ?>

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Add New Stadium</h1>
        <a href="<?= base_url('admin/stadiums') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <?php if (session()->getFlashdata('validation')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('validation')->listErrors() ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">กรอกข้อมูลสนามใหม่</h6>
        </div>
        <div class="card-body">
            
            <form action="<?= base_url('admin/stadiums') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="form-group mb-3">
                    <label class="fw-bold">Stadium Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required placeholder="ชื่อสนาม">
                </div>

                <div class="form-group mb-3">
                    <label class="fw-bold">Booking Type (รูปแบบการจอง) <span class="text-danger">*</span></label>
                    <select name="booking_type" class="form-select shadow-sm" required>
                        <option value="complex" selected>🏢 มีสนามย่อย (Complex) - เช่น มี Court 1, 2, 3</option>
                        <option value="single">🏟️ จองเหมาทั้งสนาม (Single) - ไม่มีสนามย่อย</option>
                    </select>
                    <small class="text-muted">
                        * ถ้าเลือก "มีสนามย่อย" คุณจะต้องไปเพิ่มสนามย่อยและราคาในภายหลัง
                    </small>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="fw-bold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat['id']) ?>" <?= set_select('category_id', $cat['id']) ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="fw-bold">Vendor (Owner) <span class="text-danger">*</span></label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="">-- Select Vendor --</option>
                            <?php if (!empty($vendors)): ?>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?= esc($vendor['id']) ?>" <?= set_select('vendor_id', $vendor['id']) ?>>
                                        <?= esc($vendor['vendor_name']) ?> (<?= esc($vendor['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="รายละเอียดสนาม..."><?= old('description') ?></textarea>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="fw-bold">Open Time</label>
                        <input type="time" name="open_time" class="form-control" value="<?= old('open_time') ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="fw-bold">Close Time</label>
                        <input type="time" name="close_time" class="form-control" value="<?= old('close_time') ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="fw-bold">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="<?= old('contact_email') ?>">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="fw-bold">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" value="<?= old('contact_phone') ?>" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                    </div>
                </div>

                <hr>

                <div class="form-group mb-3">
                    <label class="fw-bold">Province</label>
                    <input type="text" name="province" class="form-control" value="<?= old('province') ?>" placeholder="เช่น กรุงเทพมหานคร, เชียงใหม่">
                </div>

                <div class="form-group mb-3">
                    <label class="fw-bold">Address (Detail)</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="ที่อยู่เลขที่..."><?= old('address') ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label class="fw-bold">Stadium Location <small class="text-muted fw-normal">(คลิกบนแผนที่เพื่อปักหมุด)</small></label>
                    <div id="stadiumMap" style="width:100%; height:350px; border-radius:6px; border:1px solid #ddd;"></div>
                </div>

                <input type="hidden" id="lat" name="lat" value="<?= old('lat') ?>">
                <input type="hidden" id="lng" name="lng" value="<?= old('lng') ?>">

                <div class="form-group mb-3">
                    <label class="fw-bold">Custom Map Link (Optional)</label>
                    <input type="text" name="map_link" class="form-control" value="<?= old('map_link') ?>" placeholder="https://maps.google.com/...">
                </div>

                <hr>

                <div class="form-group mb-3">
                    <label class="fw-bold">Outside Cover Image <small class="text-danger">* (1 รูป)</small></label>
                    <input type="file" name="outside_image" class="form-control" accept="image/*">
                </div>

                <div class="form-group mb-3">
                    <label class="fw-bold">Inside Images <small class="text-muted">(Multiple)</small></label>
                    <input type="file" name="inside_images[]" class="form-control" accept="image/*" multiple>
                </div>

                <div class="form-group mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="fas fa-save me-1"></i> Save Stadium
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var latInput = document.getElementById('lat');
        var lngInput = document.getElementById('lng');

        // Default: Bangkok coordinates if empty
        var defaultLat = latInput.value ? parseFloat(latInput.value) : 13.7563;
        var defaultLng = lngInput.value ? parseFloat(lngInput.value) : 100.5018;

        var map = L.map('stadiumMap').setView([defaultLat, defaultLng], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker;
        if (latInput.value && lngInput.value) {
            marker = L.marker([defaultLat, defaultLng]).addTo(map);
        }

        map.on('click', function(e) {
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
            latInput.value = e.latlng.lat.toFixed(6);
            lngInput.value = e.latlng.lng.toFixed(6);
        });
    });
</script>

<?= $this->endSection() ?>