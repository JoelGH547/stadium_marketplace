<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="<?= base_url('customer/dashboard') ?>">Stadium Booking</a>
    </nav>

    <div class="container" style="margin-top: 30px; max-width: 600px;">

        <h1><?= esc($title) ?></h1>
        <p>กรุณาตรวจสอบรายละเอียดการจอง และยืนยันการชำระเงิน</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <strong>Booking ID:</strong> <?= esc($booking['id']) ?>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>สนาม:</strong> <?= esc($booking['stadium_name']) ?>
                </li>
                
                <li class="list-group-item">
                    <strong>วันที่:</strong> <?= esc($booking['booking_date'] ?? 'ไม่ระบุ') ?>
                </li>

                <li class="list-group-item">
                    <strong>เวลา:</strong> 
                    <span class="text-primary">
                        <?= esc(substr($booking['start_time'], 0, 5)) ?> - <?= esc($booking['end_time']) ?>
                    </span>
                    <small class="text-muted">(<?= esc($booking['hours_count']) ?> ชั่วโมง)</small>
                </li>

                <li class="list-group-item">
                    <strong>สถานะ:</strong> <span class="badge badge-warning"><?= esc(ucfirst($booking['status'])) ?></span>
                </li>
                
                <li class="list-group-item list-group-item-success">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>ยอดรวมที่ต้องชำระ:</span>
                        <h3 class="m-0"><?= esc(number_format($booking['total_price'], 2)) ?> บาท</h3>
                    </div>
                </li>
            </ul>
        </div>

        <div style="margin-top: 20px;">
            
            
            <form action="<?= base_url('customer/payment/process') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" value="<?= esc($booking['id']) ?>">
                <button type="submit" class="btn btn-success btn-lg btn-block">
                    ยืนยันการชำระเงิน
                </button>
            </form>
        </div>

    </div>
    
    <footer class="text-center" style="margin-top: 50px; padding: 20px; background-color: #f8f9fa;">
        <p>&copy; <?= date('Y') ?> Stadium Marketplace</p>
    </footer>

</body>
</html>