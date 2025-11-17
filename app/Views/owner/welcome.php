<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Stadium Marketplace | จองสนามออนไลน์</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* รีเซ็ตและตั้งค่าหน้า */
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
      font-family: 'Prompt', sans-serif;
    }

    /* วิดีโอพื้นหลังแบบเต็มจอ */
    #bg-video {
      position: fixed;
      top: 0;
      left: 0;
      min-width: 100%;
      min-height: 100%;
      object-fit: cover;
      z-index: -2;
    }

    /* ชั้นมืดครอบวิดีโอ */
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: -1;
    }

    /* เนื้อหา hero ตรงกลาง */
    .hero-content {
      position: relative;
      z-index: 1;
      text-align: center;
      color: #fff;
      top: 50%;
      transform: translateY(-50%);
    }

    h1 {
      font-weight: 700;
      font-size: 3rem;
    }

    .btn-start {
      margin-top: 20px;
      font-size: 1.25rem;
      padding: 10px 40px;
      border-radius: 50px;
      transition: all 0.3s;
    }

    .btn-start:hover {
      transform: scale(1.05);
    }
    h1, p {
  text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
    }

  </style>
</head>

<body>
  <!-- วิดีโอพื้นหลัง -->
  <video autoplay muted loop playsinline id="bg-video">
    <source src="<?= base_url('videos/stadium.mp4') ?>" type="video/mp4">
  </video>

  <!-- ชั้นมืด -->
  <div class="overlay"></div>

  <!-- ข้อความกลางหน้า -->
  <div class="hero-content">
    <h1>มีสนามว่างอยู่ใช่ไหม? </h1>
    <p class="lead">ให้เราช่วยคุณเชื่อมต่อกับนักกีฬาและผู้จองทั่วไทย</p>
    <a href="<?= base_url('owner/login') ?>" class="btn btn-primary btn-start">เริ่มต้นลงสนามของคุณ</a>
  </div>
</body>
</html>
