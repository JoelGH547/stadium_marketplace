<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ขั้นตอนที่ 4: ที่อยู่และตำแหน่งสนาม</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <style>
    body { background: #f1faf8; }
    h3 { color: #2a8f7a; font-weight: 700; }

    .box-wrapper {
      background: white;
      padding: 30px;
      border-radius: 15px;
      border-top: 5px solid #4cb7a5;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    #map {
      width: 100%;
      height: 350px;
      margin-top: 10px;
      border-radius: 12px;
      border: 3px solid #cdeee7;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .btn-primary { background: #4cb7a5 !important; border: none; }
    .btn-primary:hover { background: #3aa18e !important; }
  </style>
</head>

<body>

<?= $this->include('owner/layout/header') ?>

<div class="container mt-5" style="max-width: 800px;">

  <a href="<?= base_url('owner/fields/step3') ?>" class="btn btn-back mb-3">⬅ ย้อนกลับ</a>

  <div class="box-wrapper">

    <h3 class="fw-bold mb-3">ขั้นตอนที่ 4: ที่อยู่และตำแหน่งสนาม</h3>
    <p class="text-muted">กรอกที่อยู่และปักหมุดตำแหน่งบนแผนที่</p>

    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('owner/fields/step4') ?>">

      <!-- จังหวัด -->
      <div class="mb-3">
        <label class="form-label">จังหวัด *</label>
        <select id="provinceSelect" name="province" class="form-select" required>
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
        <label class="form-label">ที่อยู่สนาม *</label>
        <textarea name="address" class="form-control" required></textarea>
      </div>

      <hr>

      <h5 class="fw-bold text-success">ปักหมุดตำแหน่งสนาม</h5>
      <p class="text-muted">คลิกบนแผนที่ หรือเลื่อนหมุดเพื่อเลือกตำแหน่ง</p>

      <div id="map"></div>

      <div class="row mt-3">
        <div class="col-md-6">
          <label class="form-label">Latitude *</label>
          <input type="text" id="lat" name="lat" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Longitude *</label>
          <input type="text" id="lng" name="lng" class="form-control" required>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 mt-4">ถัดไป</button>

    </form>

  </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
/* พิกัดกลางของจังหวัดไทย */
const provinceCoords = {
  "กรุงเทพมหานคร": [13.736717, 100.523186],
  "เชียงใหม่": [18.7883, 98.9853],
  "เชียงราย": [19.9072, 99.8301],
  "ลำพูน": [18.5800, 99.0087],
  "ลำปาง": [18.2888, 99.4908],
  "แม่ฮ่องสอน": [19.3013, 97.9675],
  "พะเยา": [19.1659, 99.9000],
  "น่าน": [18.7750, 100.7730],
  "แพร่": [18.1446, 100.1410],
  "พิษณุโลก": [16.8211, 100.2659],
  "ตาก": [16.8839, 99.1253],
  "กำแพงเพชร": [16.4827, 99.5220],
  "พิจิตร": [16.4383, 100.3500],
  "สุโขทัย": [17.0056, 99.8260],
  "นครสวรรค์": [15.7033, 100.1364],
  "อุทัยธานี": [15.3810, 100.0240],

  "ขอนแก่น": [16.4419, 102.8350],
  "อุดรธานี": [17.4157, 102.7859],
  "ร้อยเอ็ด": [16.0538, 103.6520],
  "อุบลราชธานี": [15.2287, 104.8564],
  "นครราชสีมา": [14.9799, 102.0977],
  "บุรีรัมย์": [14.9930, 103.1029],
  "ศรีสะเกษ": [15.1143, 104.3294],

  "ชลบุรี": [13.3611, 100.9847],
  "ระยอง": [12.6814, 101.2810],
  "จันทบุรี": [12.6112, 102.1030],
  "ตราด": [12.2420, 102.5170],
  "สระแก้ว": [13.8240, 102.0640],
  "ฉะเชิงเทรา": [13.6900, 101.0762],

  "ภูเก็ต": [7.8804, 98.3923],
  "กระบี่": [8.0667, 98.9167],
  "สุราษฎร์ธานี": [9.1382, 99.3218],
  "สงขลา": [7.1756, 100.6140],
  "นครศรีธรรมราช": [8.4304, 99.9631],

  "ปัตตานี": [6.8682, 101.2500],
  "ยะลา": [6.5410, 101.2800],
  "นราธิวาส": [6.4264, 101.8231]
};

// เริ่มต้นที่กรุงเทพ
var map = L.map('map').setView([13.736717, 100.523186], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
}).addTo(map);

// หมุดเริ่มต้น
var marker = L.marker([13.736717, 100.523186], { draggable: true }).addTo(map);

// ลากหมุด → อัปเดตช่อง Lat/Lng
marker.on('dragend', function () {
  var pos = marker.getLatLng();
  document.getElementById('lat').value = pos.lat;
  document.getElementById('lng').value = pos.lng;
});

// คลิกแผนที่ → ย้ายหมุด
map.on('click', function (e) {
  marker.setLatLng(e.latlng);
  document.getElementById('lat').value = e.latlng.lat;
  document.getElementById('lng').value = e.latlng.lng;
});

// เมื่อเลือกจังหวัด → ปรับตำแหน่งแผนที่และหมุด
document.getElementById("provinceSelect").addEventListener("change", function () {
  let province = this.value;
  if (provinceCoords[province]) {
    let pos = provinceCoords[province];
    map.setView(pos, 12);
    marker.setLatLng(pos);
    document.getElementById('lat').value = pos[0];
    document.getElementById('lng').value = pos[1];
  }
});
</script>

</body>
</html>
