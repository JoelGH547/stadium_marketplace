<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php helper('form'); ?>

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800"><?= esc($title ?? 'Edit Stadium') ?></h1>
        <a href="<?= base_url('admin/stadiums') ?>" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <?php $validation = session()->getFlashdata('validation'); ?>
    <?php if ($validation): ?>
        <div class="alert alert-danger shadow-sm">
            <?= $validation->listErrors() ?? 'Please check your input.' ?>
        </div>
    <?php endif; ?>

    <?php
        $outsideArr = json_decode($stadium['outside_images'] ?? '[]', true) ?: [];
        $insideArr  = json_decode($stadium['inside_images'] ?? '[]', true) ?: [];
    ?>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">แก้ไขข้อมูลสนาม</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/stadiums/update/' . $stadium['id']) ?>"
                  method="post"
                  enctype="multipart/form-data">

                <?= csrf_field() ?>

                <div class="form-group mb-3">
                    <label for="name" class="fw-bold">Stadium Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name"
                           class="form-control"
                           value="<?= old('name', $stadium['name']) ?>" required>
                </div>

                <div class="row">
                    
                    
                    <div class="col-md-4 mb-3">
                        <label for="category_id" class="fw-bold">Category <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat['id']) ?>"
                                        <?= set_select('category_id', $cat['id'], $cat['id'] == $stadium['category_id']) ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="vendor_id" class="fw-bold">Vendor (Owner) <span class="text-danger">*</span></label>
                        <select id="vendor_id" name="vendor_id" class="form-select" required>
                            <option value="">-- Select Vendor --</option>
                            <?php if (!empty($vendors)): ?>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?= esc($vendor['id']) ?>"
                                        <?= set_select('vendor_id', $vendor['id'], $vendor['id'] == $stadium['vendor_id']) ?>>
                                        <?= esc($vendor['vendor_name']) ?> (<?= esc($vendor['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <hr>

                
                    <textarea id="description"
                              name="description"
                              class="form-control"
                              rows="4"><?= old('description', $stadium['description']) ?></textarea>
                </div>

                <div class="row">
                    <div class="form-group col-md-6 mb-3">
                        <label for="open_time" class="fw-bold">Open Time</label>
                        <input type="time" id="open_time" name="open_time"
                               class="form-control"
                               value="<?= old('open_time', $stadium['open_time']) ?>">
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <label for="close_time" class="fw-bold">Close Time</label>
                        <input type="time" id="close_time" name="close_time"
                               class="form-control"
                               value="<?= old('close_time', $stadium['close_time']) ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6 mb-3">
                        <label for="contact_email" class="fw-bold">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email"
                               class="form-control"
                               value="<?= old('contact_email', $stadium['contact_email']) ?>">
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <label for="contact_phone" class="fw-bold">Contact Phone</label>
                        <input type="text" id="contact_phone" name="contact_phone"
                               class="form-control"
                               value="<?= old('contact_phone', $stadium['contact_phone']) ?>"
                               maxlength="10"
                               inputmode="numeric"
                               pattern="\d{10}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="province" class="fw-bold">Province</label>
                    <input type="text"
                           id="province"
                           name="province"
                           class="form-control"
                           value="<?= old('province', $stadium['province']) ?>"
                           placeholder="เช่น กรุงเทพมหานคร, เชียงใหม่">
                </div>

                <div class="form-group mb-3">
                    <label for="address" class="fw-bold">Address (Detail)</label>
                    <textarea id="address" name="address"
                              class="form-control"
                              rows="3"><?= old('address', $stadium['address']) ?></textarea>
                </div>

                <hr>

                <div class="form-group mb-3">
                    <label class="fw-bold">Stadium Location (คลิกบนแผนที่เพื่อปักหมุด)</label>
                    <div id="stadiumMap" style="width:100%; height:350px; border-radius:6px; border:1px solid #ddd;"></div>
                    <input type="hidden" id="lat" name="lat" value="<?= old('lat', $stadium['lat']) ?>">
                    <input type="hidden" id="lng" name="lng" value="<?= old('lng', $stadium['lng']) ?>">
                </div>

                <div class="form-group mb-3">
                    <label for="map_link" class="fw-bold">Custom Map Link (optional)</label>
                    <input type="text" id="map_link" name="map_link"
                           class="form-control"
                           value="<?= old('map_link', $stadium['map_link']) ?>">
                </div>

                <hr>

                <div class="form-group mb-3">
                    <label class="fw-bold d-block">Current Outside Cover Image</label>
                    <?php if (!empty($outsideArr)): ?>
                        <div class="border p-2 d-inline-block rounded bg-light">
                            <img src="<?= base_url('assets/uploads/stadiums/' . $outsideArr[0]) ?>"
                                 alt="Current Cover"
                                 style="width: 200px; height: 130px; object-fit: cover; border-radius: 4px; display:block; margin-bottom:5px;">
                            
                            <div class="form-check text-danger mt-2">
                                <input type="checkbox" class="form-check-input" id="delete_outside" name="delete_outside" value="1">
                                <label class="form-check-label" for="delete_outside">
                                    <i class="fas fa-trash-alt"></i> ลบรูปปกนี้
                                </label>
                            </div>
                        </div>
                    <?php else: ?>
                        <span class="text-muted">No cover image</span>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="outside_image" class="fw-bold">Replace Outside Cover Image (optional)</label>
                    <input type="file" id="outside_image" name="outside_image"
                           class="form-control" accept="image/*">
                </div>

                <div class="form-group mb-3">
                    <label class="fw-bold">Current Inside Images</label>
                    <div class="row">
                        <?php if (!empty($insideArr)): ?>
                            <?php foreach ($insideArr as $img): ?>
                                <div class="col-md-3 col-sm-4 mb-3">
                                    <div class="border p-2 rounded h-100 text-center bg-light">
                                        <img src="<?= base_url('assets/uploads/stadiums/' . $img) ?>"
                                             alt="Inside"
                                             class="img-fluid mb-2"
                                             style="height: 120px; object-fit: cover; border-radius: 4px;">
                                        
                                        <div class="form-check text-danger text-start">
                                            <input type="checkbox" class="form-check-input" 
                                                   id="del_<?= md5($img) ?>" 
                                                   name="delete_inside[]" 
                                                   value="<?= esc($img) ?>">
                                            <label class="form-check-label" for="del_<?= md5($img) ?>">
                                                <small>ลบรูปนี้</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12"><span class="text-muted">No inside images</span></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="inside_images" class="fw-bold">Add More Inside Images</label>
                    <input type="file" id="inside_images" name="inside_images[]"
                           class="form-control" accept="image/*" multiple>
                </div>

                <div class="form-group mt-4 text-end">
                    <button type="submit" class="btn btn-warning px-4 py-2 shadow-sm">
                        <i class="fas fa-save me-1"></i> Update Stadium
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
  crossorigin=""
/>
<script
  src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
  integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
  crossorigin=""
></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var latInput = document.getElementById('lat');
        var lngInput = document.getElementById('lng');

        // ถ้ามีค่าเดิม ใช้ค่าเดิม ถ้าไม่มีใช้พิกัดกรุงเทพฯ
        var defaultLat = latInput.value ? parseFloat(latInput.value) : 13.736717;
        var defaultLng = lngInput.value ? parseFloat(lngInput.value) : 100.523186;

        var map = L.map('stadiumMap').setView([defaultLat, defaultLng], latInput.value && lngInput.value ? 14 : 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker = null;
        if (latInput.value && lngInput.value) {
            marker = L.marker([defaultLat, defaultLng]).addTo(map);
        }

        map.on('click', function (e) {
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