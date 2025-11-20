    <?= $this->extend('layouts/admin') ?>

    <?= $this->section('content') ?>
    <?php helper('form'); ?>

    <h1><?= esc($title ?? 'Add New Stadium') ?></h1>

    <p>
        <a href="<?= base_url('admin/stadiums') ?>" class="btn btn-secondary">
            &laquo; Back to Stadium List
        </a>
    </p>

    <?php $validation = session()->getFlashdata('validation'); ?>
    <?php if ($validation): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?? 'Please check your input.' ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/stadiums') ?>"
        method="post"
        enctype="multipart/form-data">

        <?= csrf_field() ?>

        <div class="form-group">
            <label for="name">Stadium Name</label>
            <input type="text" id="name" name="name"
                class="form-control"
                value="<?= old('name') ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">-- Select Category --</option>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['id']) ?>"
                            <?= set_select('category_id', $cat['id']) ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="vendor_id">Vendor</label>
            <select id="vendor_id" name="vendor_id" class="form-control" required>
                <option value="">-- Select Vendor --</option>
                <?php if (!empty($vendors)): ?>
                    <?php foreach ($vendors as $vendor): ?>
                        <option value="<?= esc($vendor['id']) ?>"
                            <?= set_select('vendor_id', $vendor['id']) ?>>
                            <?= esc($vendor['vendor_name']) ?> (<?= esc($vendor['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="price">Price (per hour)</label>
            <input type="number" step="0.01"
                id="price" name="price"
                class="form-control"
                value="<?= old('price') ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description (Stadium Condition)</label>
            <textarea id="description"
                    name="description"
                    class="form-control"
                    rows="4"><?= old('description') ?></textarea>
        </div>

        <hr>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="open_time">Open Time</label>
                <input type="time" id="open_time" name="open_time"
                    class="form-control"
                    value="<?= old('open_time') ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="close_time">Close Time</label>
                <input type="time" id="close_time" name="close_time"
                    class="form-control"
                    value="<?= old('close_time') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="contact_email">Contact Email</label>
            <input type="email" id="contact_email" name="contact_email"
                class="form-control"
                value="<?= old('contact_email') ?>">
        </div>

        <div class="form-group">
            <label for="contact_phone">Contact Phone</label>
            <input type="text" id="contact_phone" name="contact_phone"
                class="form-control"
                value="<?= old('contact_phone') ?>"
                maxlength="10"
                inputmode="numeric"
                pattern="\d{10}"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
            <small class="form-text text-muted">
                กรอกได้เฉพาะตัวเลข 10 หลัก เช่น 0891234567
            </small>
        </div>

        <div class="form-group">
        <label for="province">Province</label>
        <input type="text"
            id="province"
            name="province"
            class="form-control"
            value="<?= old('province') ?>"
            placeholder="เช่น กรุงเทพมหานคร, เชียงใหม่">
        <small class="form-text text-muted">
            พิมพ์ชื่อจังหวัด
        </small>
    </div>


        <div class="form-group">
            <label for="address">Address (Detail)</label>
            <textarea id="address" name="address"
                    class="form-control"
                    rows="3"><?= old('address') ?></textarea>
        </div>

        <hr>

        <!-- แผนที่ปักหมุด -->
        <div class="form-group">
            <label>Stadium Location (คลิกบนแผนที่เพื่อปักหมุด)</label>
            <div id="stadiumMap" style="width:100%; height:350px; border-radius:6px; border:1px solid #ddd;"></div>
            <small class="form-text text-muted">
                คลิกบนแผนที่เพื่อเลือกตำแหน่งสนาม ระบบจะบันทึก Latitude / Longitude ให้อัตโนมัติ
            </small>
        </div>

        <!-- ซ่อนไว้ แต่ Controller ยังรับค่าเหมือนเดิม -->
        <input type="hidden" id="lat" name="lat" value="<?= old('lat') ?>">
        <input type="hidden" id="lng" name="lng" value="<?= old('lng') ?>">

        <hr>

        <div class="form-group">
            <label for="map_link">Custom Map Link (optional)</label>
            <input type="text" id="map_link" name="map_link"
                class="form-control"
                value="<?= old('map_link') ?>">
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-sm btn-outline-info" onclick="testMapPreview()">
                ทดสอบเปิดแผนที่จากหมุดที่เลือก
            </button>
        </div>

        <hr>

        <div class="form-group">
            <label for="outside_image">Outside Cover Image (1 รูป)</label>
            <input type="file" id="outside_image" name="outside_image"
                class="form-control-file" accept="image/*">
            <small class="form-text text-muted">
                รูปนี้จะใช้เป็นรูปหน้าปกของสนาม
            </small>
        </div>

        <div class="form-group">
            <label for="inside_images">Inside Images (Multiple)</label>
            <input type="file" id="inside_images" name="inside_images[]"
                class="form-control-file" accept="image/*" multiple>
            <small class="form-text text-muted">
                สามารถอัปโหลดรูปภายในสนามได้หลายรูป
            </small>
        </div>

        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">
                Save Stadium
            </button>
        </div>
    </form>

    <!-- Leaflet CSS & JS -->
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
        // แผนที่ + ปักหมุด
        document.addEventListener('DOMContentLoaded', function () {
            var latInput = document.getElementById('lat');
            var lngInput = document.getElementById('lng');

            // Default กลางประเทศไทย
            var defaultLat = latInput.value ? parseFloat(latInput.value) : 13.736717;
            var defaultLng = lngInput.value ? parseFloat(lngInput.value) : 100.523186;

            var map = L.map('stadiumMap').setView([defaultLat, defaultLng], 6);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
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

        function testMapPreview() {
            var lat = document.getElementById('lat').value.trim();
            var lng = document.getElementById('lng').value.trim();
            if (!lat || !lng) {
                alert('กรุณาคลิกปักหมุดบนแผนที่ก่อน');
                return;
            }
            var url = 'https://www.google.com/maps?q=' + encodeURIComponent(lat + ',' + lng) + '&hl=th&z=16';
            window.open(url, '_blank');
        }
    </script>

    <?= $this->endSection() ?>
