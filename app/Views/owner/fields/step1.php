<?php
// ========== ICON FUNCTION (ใช้ตาม ID) ==========  
function sportIcon($id) {
    switch ($id) {

        case 1: // ฟุตบอล
            return '<i class="fa-solid fa-futbol fa-3x" style="color:#4cb7a5"></i>';

        case 2: // บาสเกตบอล
            return '<i class="fa-solid fa-basketball fa-3x" style="color:#4cb7a5"></i>';

        case 3: // เทนนิส
            return '<i class="fa-solid fa-table-tennis-paddle-ball fa-3x" style="color:#4cb7a5"></i>';

        case 4: // แบดมินตัน
            return '<i class="fa-solid fa-feather-pointed fa-3x" style="color:#4cb7a5"></i>';

        case 5: // วอลเลย์บอล
            return '<i class="fa-solid fa-volleyball fa-3x" style="color:#4cb7a5"></i>';

        case 6: // ฟุตซอล
            return '<i class="fa-solid fa-futbol fa-3x" style="color:#4cb7a5"></i>';

        default:
            return '<i class="fa-solid fa-circle fa-3x" style="color:#4cb7a5"></i>';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ขั้นตอนที่ 1: เลือกประเภทสนาม</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    body {
        background: #f1faf8;
    }

    .sport-card {
        border: 2px solid #cfe7e2;
        border-radius: 12px;
        padding: 25px 10px;
        text-align: center;
        cursor: pointer;
        transition: 0.25s;
        background: white;
    }

    .sport-card:hover {
        border-color: #4cb7a5;
        background: #e6faf5;
        transform: translateY(-3px);
    }

    .sport-card.active {
        border-color: #4cb7a5;
        background: #d5f5ed;
        box-shadow: 0 0 12px rgba(76,183,165,0.3);
    }

    input[type=radio] {
        display: none;
    }

    .top-title {
        color: #2a8f7a;
        font-weight: bold;
    }

</style>

</head>

<body>
<?= $this->include('owner/layout/header') ?>
<div class="container mt-5" style="max-width: 700px;">

  <!-- Back button -->
  <a href="<?= base_url('owner/dashboard') ?>" class="btn btn-outline-secondary mb-3">
    ⬅ ย้อนกลับ
  </a>

  <h3 class="fw-bold mb-3 top-title">ขั้นตอนที่ 1: เลือกประเภทสนาม</h3>
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
            <div class="display-4"><?= $cat['emoji'] ?></div>

            <!-- RADIO -->
            <input type="radio" name="category_id" value="<?= $cat['id'] ?>">

            <!-- NAME -->
            <h5 class="mt-3"><?= esc($cat['name']) ?></h5>
          </label>
        </div>
      <?php endforeach; ?>

    </div>

    <button type="submit" class="btn w-100 mt-4" 
            style="background:#4cb7a5; color:white; font-size:18px;">
      ถัดไป
    </button>
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
