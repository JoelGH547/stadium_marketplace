<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="<?= base_url('customer/dashboard') ?>">Stadium Booking</a>
    </nav>

    <div class="container pb-5" style="margin-top: 30px; max-width: 600px;">

        <h2 class="mb-3"><?= esc($title) ?></h2>
        <p class="text-muted">กรุณาโอนเงินและแนบสลิปเพื่อยืนยันการจอง</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- รายละเอียดการจอง -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <strong>Booking ID:</strong> #<?= esc($booking['id']) ?>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <small class="text-muted">สนาม:</small><br>
                    <strong><?= esc($booking['stadium_name']) ?></strong>
                </li>
                
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">วันที่:</small><br>
                            <?= esc($booking['booking_date'] ?? 'ไม่ระบุ') ?>
                        </div>
                        <div class="col-6">
                             <small class="text-muted">เวลา:</small><br>
                            <span class="text-primary font-weight-bold">
                                <?= esc(substr($booking['start_time'], 0, 5)) ?> - <?= esc($booking['end_time']) ?>
                            </span>
                            <small class="text-muted">(<?= esc($booking['hours_count']) ?> ชม.)</small>
                        </div>
                    </div>
                </li>
                
                <li class="list-group-item bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>ยอดรวมที่ต้องชำระ:</span>
                        <h3 class="m-0 text-success">฿<?= esc(number_format($booking['total_price'], 2)) ?></h3>
                    </div>
                </li>
            </ul>
        </div>

        <!-- ส่วนการโอนเงิน -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-university mr-2"></i>ช่องทางการชำระเงิน</h5>
                <hr>
                <div class="d-flex align-items-center mb-3">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/c5/Kbank_logo.png" width="50" class="mr-3 rounded">
                    <div>
                        <strong>ธนาคาร</strong><br>
                        เลขบัญชี: <span class="text-primary" style="font-family: monospace; font-size: 1.1em;">123-4-56789-0</span><br>
                        ชื่อบัญชี: .... จำกัด
                    </div>
                </div>
                <div class="alert alert-info small m-0">
                    * กรุณาโอนเงินให้ตรงตามยอดที่ระบุ (฿<?= esc(number_format($booking['total_price'], 2)) ?>)
                </div>
            </div>
        </div>

        <!-- ฟอร์มอัปโหลดสลิป -->
        <!-- [สำคัญ] ต้องเพิ่ม enctype="multipart/form-data" เพื่อให้อัปโหลดไฟล์ได้ -->
        <form action="<?= base_url('customer/payment/process') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= esc($booking['id']) ?>">
            
            <div class="form-group">
                <label for="slip_image" class="font-weight-bold">อัปโหลดหลักฐานการโอนเงิน (สลิป):</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="slip_image" name="slip_image" accept="image/*" required>
                    <label class="custom-file-label" for="slip_image">เลือกไฟล์รูปภาพ...</label>
                </div>
                <small class="form-text text-muted">รองรับไฟล์ .jpg, .png, .jpeg</small>
            </div>

            <button type="submit" class="btn btn-success btn-lg btn-block mt-4 shadow">
                <i class="fas fa-check-circle mr-2"></i> ยืนยันการชำระเงิน
            </button>
        </form>

    </div>
    
    <!-- Script ให้ชื่อไฟล์แสดงตอนเลือกรูป -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script>
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    </script>

</body>
</html>