<?php
// ========== ICON FUNCTION (ใช้ตาม ID) ==========
function sportIcon($id) {
    switch ($id) {

        case 1: // ฟุตบอล
            return '<svg width="50" height="50" fill="#4cb7a5" viewBox="0 0 24 24">
                <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm4.3 5.1l-1.2 2.6a1 1 0 01-.4.4l-2.3 1a1 1 0 01-.8 0l-2.3-1a1 1 0 01-.4-.4L7.7 7.1A8 8 0 0112 4a8 8 0 014.3 3.1z"/>
            </svg>';

        case 2: // บาสเกตบอล
            return '<svg width="50" height="50" fill="#4cb7a5" viewBox="0 0 24 24">
                <path d="M12 2a10 10 0 100 20A10 10 0 0012 2zm1 17.9V13h6.9a8 8 0 01-6.9 6.9z"/>
            </svg>';

        case 3: // เทนนิส
            return '<svg width="50" height="50" fill="#4cb7a5" viewBox="0 0 24 24">
                <path d="M19 3a8 8 0 01-11 11 8 8 0 1111-11z"/>
            </svg>';

        case 4: // แบดมินตัน
            return '<svg width="50" height="50" fill="#4cb7a5" viewBox="0 0 24 24">
                <path d="M2 20l5-2 7-12-3-3L2 20zm11-13l3 3 4-2-5-5-2 4z"/>
            </svg>';

        case 5: // วอลเลย์บอล
            return '<svg width="50" height="50" fill="#4cb7a5" viewBox="0 0 24 24">
                <path d="M12 2a10 10 0 00-3 19.5V12h8V3a10 10 0 00-5-1z"/>
            </svg>';

        case 6: // ฟุตซอล
            return '<svg width="50" height="50" fill="#4cb7a5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
            </svg>';

        default:
            return '<svg width="50" height="50" fill="#4cb7a5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
            </svg>';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ขั้นตอนที่ 1: เลือกประเภทสนาม</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    .sport-card {
      border: 2px solid #ccc;
      border-radius: 10px;
      padding: 25px 10px;
      text-align: center;
      cursor: pointer;
      transition: 0.2s;
      background: white;
    }
    .sport-card:hover {
      border-color: #4cb7a5;
      background: #e9fdf7;
    }
    .sport-card.active {
      border-color: #4cb7a5;
      background: #d8f9f0;
      box-shadow: 0 0 10px rgba(76,183,165,0.3);
    }
    input[type=radio] {
      display: none;
    }
</style>

</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 700px;">

  <!-- Back button -->
  <a href="<?= base_url('owner/dashboard') ?>" class="btn btn-secondary mb-3">
    ⬅ ย้อนกลับ
  </a>

  <h3 class="fw-bold mb-3">ขั้นตอนที่ 1: เลือกประเภทสนาม</h3>
  <p class="text-muted">เลือกประเภทกีฬาที่สนามของคุณรองรับ</p>

  <?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error'); ?></div>
  <?php endif; ?>


  <form method="post" action="<?= base_url('owner/fields/step1') ?>">

    <div class="row g-3">

      <?php foreach ($categories as $cat): ?>
        <div class="col-md-6">
          <label class="sport-card w-100" onclick="selectType(this)">
            
            <!-- ICON -->
            <div><?= sportIcon($cat['id']) ?></div>

            <!-- RADIO -->
            <input type="radio" name="category_id" value="<?= $cat['id'] ?>">

            <!-- NAME -->
            <h5 class="mt-2"><?= esc($cat['name']) ?></h5>
          </label>
        </div>
      <?php endforeach; ?>

    </div>

    <button type="submit" class="btn btn-primary w-100 mt-4">ถัดไป</button>
  </form>
</div>


<script>
function selectType(element) {
    document.querySelectorAll('.sport-card').forEach(card => {
        card.classList.remove('active');
    });

    element.classList.add('active');
    let radio = element.querySelector('input[type=radio]');
    radio.checked = true;
}
</script>

</body>
</html>
