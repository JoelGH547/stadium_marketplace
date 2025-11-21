<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ขั้นตอนที่ 4: ที่อยู่และตำแหน่งสนาม</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <style>
    #map {
      width: 100%;
      height: 350px;
      margin-top: 10px;
      border-radius: 10px;
    }
  </style>
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 800px;">
  <a href="<?= base_url('owner/fields/step3') ?>" class="btn btn-secondary mb-3">
    ⬅ ย้อนกลับ
</a>


  <h3 class="fw-bold mb-3">ขั้นตอนที่ 4: ที่อยู่และตำแหน่งสนาม</h3>
  <p class="text-muted">กรอกที่อยู่และปักหมุดตำแหน่งสนามของคุณ</p>

  <?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <form method="post" action="<?= base_url('owner/fields/step4') ?>">

    <!-- จังหวัด -->
    <div class="mb-3">
      <label class="form-label fw-bold">จังหวัด *</label>
      <select name="province" class="form-select" required>
        <option value="">-- เลือกจังหวัด --</option>
        <?php foreach ($provinces = [
          "กรุงเทพมหานคร","เชียงใหม่","เชียงราย","ลำพูน","ลำปาง","แม่ฮ่องสอน",
          "พะเยา","น่าน","แพร่","พิษณุโลก","ตาก","กำแพงเพชร","พิจิตร","สุโขทัย",
          "นครสวรรค์","อุทัยธานี","อุดรธานี","ขอนแก่น","ร้อยเอ็ด","นครราชสีมา",
          "บุรีรัมย์","สุรินทร์","ศรีสะเกษ","อุบลราชธานี","เลย","สระแก้ว","ฉะเชิงเทรา",
          "ชลบุรี","ระยอง","จันทบุรี","ตราด","ประจวบคีรีขันธ์","เพชรบุรี","สมุทรสงคราม",
          "สมุทรสาคร","นครปฐม","สุพรรณบุรี","กาญจนบุรี","ราชบุรี","สงขลา",
          "นครศรีธรรมราช","ภูเก็ต","กระบี่","ตรัง","พัทลุง","ระนอง","ชุมพร",
          "สุราษฎร์ธานี","ยะลา","ปัตตานี","นราธิวาส"
        ] as $p): ?>
          <option value="<?= $p ?>"><?= $p ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- ที่อยู่ -->
    <div class="mb-3">
      <label class="form-label fw-bold">ที่อยู่สนาม *</label>
      <textarea name="address" class="form-control" required></textarea>
    </div>

    <hr>

    <!-- แผนที่ Leaflet -->
    <h5 class="fw-bold">ปักหมุดตำแหน่งสนาม</h5>
    <p class="text-muted">คลิกบนแผนที่หรือเลื่อนหมุดเพื่อกำหนดตำแหน่ง</p>

    <div id="map"></div>

    <div class="row mt-3">
      <div class="col-md-6">
        <label class="form-label fw-bold">Latitude *</label>
        <input type="text" id="lat" name="lat" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-bold">Longitude *</label>
        <input type="text" id="lng" name="lng" class="form-control" required>
      </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 mt-4">ถัดไป</button>

  </form>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
  // เริ่มต้นที่กรุงเทพ
  var map = L.map('map').setView([13.736717, 100.523186], 12);

  // ใช้แผนที่ OSM ฟรี
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
  }).addTo(map);

  // สร้างหมุดเริ่มต้น
  var marker = L.marker([13.736717, 100.523186], {draggable: true}).addTo(map);

  // อัปเดต lat/lng เมื่อเลื่อนหมุด
  marker.on('dragend', function (e) {
      var pos = marker.getLatLng();
      document.getElementById('lat').value = pos.lat;
      document.getElementById('lng').value = pos.lng;
  });

  // คลิกแผนที่เพื่อย้ายหมุด
  map.on('click', function(e) {
      marker.setLatLng(e.latlng);
      document.getElementById('lat').value = e.latlng.lat;
      document.getElementById('lng').value = e.latlng.lng;
  });
</script>

</body>
</html>
