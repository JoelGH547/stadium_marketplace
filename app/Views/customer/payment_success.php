<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        
        .success-icon {
            font-size: 5rem;
            color: #28a745;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="<?= base_url('customer/dashboard') ?>">Stadium Booking</a>
        </nav>

    <div class="container text-center" style="margin-top: 50px; max-width: 600px;">

        <div class="success-icon">
            &#10004; </div>

        <h1 class="mt-3"><?= esc($title) ?></h1>
        <p class="lead">การจองของคุณ (Booking ID: <strong><?= esc($booking_id) ?></strong>) ได้รับการยืนยันเรียบร้อยแล้ว</p>
        
        <p>ขอบคุณที่ใช้บริการของเรา</p>
        
        <hr>

        <p>
            คุณสามารถตรวจสอบรายละเอียดการจองได้ที่หน้า "ประวัติการจอง" (ในอนาคต)
        </p>
        
        <a href="<?= base_url('customer/dashboard') ?>" class="btn btn-primary btn-lg">
            กลับไปหน้าหลัก
        </a>

    </div> <footer class="text-center" style="margin-top: 50px; padding: 20px; background-color: #f8f9fa;">
        <p>&copy; <?= date('Y') ?> Stadium Marketplace. All rights reserved.</p>
    </footer>

</body>
</html>