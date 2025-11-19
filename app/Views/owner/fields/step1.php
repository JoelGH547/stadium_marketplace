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
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: 0.2s;
    }
    .sport-card:hover {
      border-color: #007bff;
      background: #f0f8ff;
    }
    .sport-card.active {
      border-color: #007bff;
      background: #e7f1ff;
      box-shadow: 0 0 10px rgba(0,123,255,0.3);
    }
    input[type=radio] {
      display: none;
    }
  </style>
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 700px;">
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
            <input type="radio" name="category_id" value="<?= $cat['id'] ?>">
            <h4><?= esc($cat['name']) ?></h4>
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
