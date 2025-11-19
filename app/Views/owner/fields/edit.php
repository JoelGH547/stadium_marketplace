<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสนาม</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    />

    <style>
        .img-thumb {
            width: 150px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            margin: 5px;
            border: 1px solid #ddd;
        }
        #map {
            width: 100%;
            height: 350px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        .image-box {
            position: relative;
            display: inline-block;
            margin: 10px 10px 0 0;
        }
        .image-box img {
            width: 150px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .remove-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            padding: 0 7px;
            background: red;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            line-height: 1.2;
        }
    </style>
</head>

<body class="bg-light">

<div class="container mt-4 mb-5" style="max-width:900px;">
    <button type="button" class="btn btn-secondary mb-3" onclick="history.back()">
    ⬅ ย้อนกลับ
</button>


    <h3 class="fw-bold mb-3">แก้ไขข้อมูลสนาม: <?= esc($stadium['name']) ?></h3>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('owner/fields/update/'.$stadium['id']) ?>" enctype="multipart/form-data">

        <!-- Category -->
        <div class="mb-3">
            <label class="form-label">ประเภทสนาม</label>
            <select name="category_id" class="form-select">
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $stadium['category_id']==$cat['id']?'selected':'' ?>>
                        <?= esc($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Name -->
        <div class="mb-3">
            <label class="form-label">ชื่อสนาม</label>
            <input type="text" name="name" value="<?= esc($stadium['name']) ?>" class="form-control">
        </div>

        <!-- Price -->
        <div class="mb-3">
            <label class="form-label">ราคา / ชั่วโมง</label>
            <input type="number" name="price" value="<?= esc($stadium['price']) ?>" class="form-control" min="0">
        </div>

        <!-- Open close -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">เวลาเปิด</label>
                <input type="time" name="open_time" value="<?= esc($stadium['open_time']) ?>" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">เวลาปิด</label>
                <input type="time" name="close_time" value="<?= esc($stadium['close_time']) ?>" class="form-control">
            </div>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label class="form-label">รายละเอียด</label>
            <textarea name="description" class="form-control" rows="3"><?= esc($stadium['description']) ?></textarea>
        </div>

        <hr>

        <!-- Contact -->
        <h5 class="mt-3">ข้อมูลติดต่อ</h5>

        <div class="mb-3">
            <label class="form-label">Email สนาม</label>
            <input type="email" name="contact_email" class="form-control" value="<?= esc($stadium['contact_email']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">เบอร์โทรสนาม</label>
            <input type="text" name="contact_phone" class="form-control" value="<?= esc($stadium['contact_phone']) ?>">
        </div>

        <hr>

        <!-- Location -->
        <h5 class="mt-3">ที่อยู่สนาม + ตำแหน่งบนแผนที่</h5>

        <div class="mb-3">
            <label class="form-label">จังหวัด</label>
            <input type="text" name="province" class="form-control" value="<?= esc($stadium['province']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">ที่อยู่</label>
            <input type="text" name="address" class="form-control" value="<?= esc($stadium['address']) ?>">
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Latitude</label>
                <input type="text" name="lat" id="latInput" class="form-control" value="<?= esc($stadium['lat']) ?>">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Longitude</label>
                <input type="text" name="lng" id="lngInput" class="form-control" value="<?= esc($stadium['lng']) ?>">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">ลิงก์แผนที่ (Google Maps)</label>
                <input type="text" name="map_link" id="mapLinkInput" class="form-control" value="<?= esc($stadium['map_link']) ?>">
            </div>
        </div>

        <div class="mb-3">
            <small class="text-muted">
                🔹 คลิกบนแผนที่เพื่อปักหมุด ระบบจะกรอก Latitude/Longitude และลิงก์ Google Maps ให้เอง
            </small>
            <div id="map" class="mt-2"></div>
        </div>

        <hr>

        <!-- รูปภาพเก่า + เช็คบ็อกซ์ลบ -->
        <h5 class="mt-3">รูปภายนอก (เก่า)</h5>
        <div class="d-flex flex-wrap">
            <?php 
                $outsideOld = json_decode($stadium['outside_images'], true) ?: [];
                foreach($outsideOld as $img): 
            ?>
                <div class="text-center me-3 mb-3">
                    <img src="<?= base_url('uploads/stadiums/outside/'.$img) ?>" class="img-thumb"><br>
                    <label class="mt-2">
                        <input type="checkbox" name="delete_outside[]" value="<?= esc($img) ?>"> ลบรูปนี้
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mb-3 mt-2">
            <label class="form-label">เพิ่มรูปภายนอกใหม่ (เลือกได้หลายรูป)</label>
            <input type="file" id="outsideInput" name="outside_images[]" multiple class="form-control" accept="image/*">
            <div id="outsidePreview" class="d-flex flex-wrap mt-2"></div>
        </div>

        <h5 class="mt-4">รูปภายใน (เก่า)</h5>
        <div class="d-flex flex-wrap">
            <?php 
                $insideOld = json_decode($stadium['inside_images'], true) ?: [];
                foreach($insideOld as $img): 
            ?>
                <div class="text-center me-3 mb-3">
                    <img src="<?= base_url('uploads/stadiums/inside/'.$img) ?>" class="img-thumb"><br>
                    <label class="mt-2">
                        <input type="checkbox" name="delete_inside[]" value="<?= esc($img) ?>"> ลบรูปนี้
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mb-3 mt-2">
            <label class="form-label">เพิ่มรูปภายในใหม่ (เลือกได้หลายรูป)</label>
            <input type="file" id="insideInput" name="inside_images[]" multiple class="form-control" accept="image/*">
            <div id="insidePreview" class="d-flex flex-wrap mt-2"></div>
        </div>

        <button class="btn btn-primary w-100 mt-4">บันทึกการเปลี่ยนแปลง</button>

    </form>

</div>

<!-- Leaflet JS -->
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin="">
</script>

<script>
// ====================== แผนที่ Leaflet + ปักหมุด ======================
const latInput     = document.getElementById('latInput');
const lngInput     = document.getElementById('lngInput');
const mapLinkInput = document.getElementById('mapLinkInput');

// ถ้ามี lat/lng แล้ว ใช้ค่าจริง ไม่งั้น default = กทม.
let lat = parseFloat(latInput.value);
let lng = parseFloat(lngInput.value);

if (isNaN(lat) || isNaN(lng)) {
    lat = 13.736717;  // Bangkok
    lng = 100.523186;
}

const map = L.map('map').setView([lat, lng], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
}).addTo(map);

let marker = null;

// ถ้ามีค่า lat/lng เดิม → วาง marker ให้เลย
if (!isNaN(parseFloat(latInput.value)) && !isNaN(parseFloat(lngInput.value))) {
    marker = L.marker([lat, lng]).addTo(map);
}

// ฟังก์ชันตั้งค่า marker + อัปเดต input
function setMarker(lat, lng) {
    if (marker) {
        marker.setLatLng([lat, lng]);
    } else {
        marker = L.marker([lat, lng]).addTo(map);
    }

    latInput.value = lat.toFixed(6);
    lngInput.value = lng.toFixed(6);

    // auto fill ลิงก์ Google Maps
    mapLinkInput.value = `https://www.google.com/maps?q=${lat},${lng}`;
}

// คลิกบนแผนที่ → ขยับ marker + เปลี่ยนค่า input
map.on('click', function(e) {
    setMarker(e.latlng.lat, e.latlng.lng);
});

// ถ้าพิมพ์ lat/lng เอง → กด Enter จะขยับ map ตาม
latInput.addEventListener('change', function() {
    const la = parseFloat(latInput.value);
    const ln = parseFloat(lngInput.value);
    if (!isNaN(la) && !isNaN(ln)) {
        map.setView([la, ln], 15);
        setMarker(la, ln);
    }
});
lngInput.addEventListener('change', function() {
    const la = parseFloat(latInput.value);
    const ln = parseFloat(lngInput.value);
    if (!isNaN(la) && !isNaN(ln)) {
        map.setView([la, ln], 15);
        setMarker(la, ln);
    }
});

// ====================== Preview รูปใหม่ + ลบก่อนส่ง (เหมือน step3) ======================
function setupImageUploader(inputId, previewId) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    let dataTransfer = new DataTransfer();

    input.addEventListener("change", function() {
        // เพิ่มไฟล์ใหม่ลง DataTransfer
        for (let file of input.files) {
            dataTransfer.items.add(file);
        }

        // sync กลับไปยัง input.files
        input.files = dataTransfer.files;

        // render ใหม่
        renderPreview();
    });

    function renderPreview() {
        preview.innerHTML = "";

        Array.from(dataTransfer.files).forEach((file, index) => {
            let reader = new FileReader();

            reader.onload = function(e) {
                const box = document.createElement("div");
                box.className = "image-box";

                const img = document.createElement("img");
                img.src = e.target.result;

                const btn = document.createElement("button");
                btn.className = "remove-btn";
                btn.innerHTML = "×";

                btn.onclick = function(ev) {
                    ev.preventDefault();
                    dataTransfer.items.remove(index);
                    input.files = dataTransfer.files;
                    renderPreview();
                };

                box.appendChild(img);
                box.appendChild(btn);
                preview.appendChild(box);
            };

            reader.readAsDataURL(file);
        });
    }
}

// ติดตั้งให้ 2 ช่อง
setupImageUploader("outsideInput", "outsidePreview");
setupImageUploader("insideInput", "insidePreview");
</script>

</body>
</html>
