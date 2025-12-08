<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="<?= base_url('customer/dashboard') ?>">Stadium Booking</a>
    </nav>

    <div class="container pb-5" style="margin-top: 30px; max-width: 800px;">

        <h2 class="mb-4 text-center">ยืนยันการชำระเงิน</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <strong>Booking ID:</strong> #<?= esc($booking['id']) ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>สนาม:</strong> <?= esc($booking['stadium_name']) ?></p>
                        <p><strong>วันที่:</strong> <?= esc($booking['booking_date']) ?></p>
                        <p><strong>เวลา:</strong> <?= esc($booking['start_time']) ?> - <?= esc($booking['end_time']) ?> (<?= esc($booking['hours_count']) ?> ชม.)</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <h4 class="text-muted">ยอดชำระทั้งหมด</h4>
                        <h2 class="text-success font-weight-bold">฿<?= number_format($booking['total_price'], 2) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <ul class="nav nav-pills card-header-pills" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-transfer-tab" data-toggle="pill" href="#pills-transfer" role="tab">
                            <i class="fas fa-qrcode mr-1"></i> สแกนจ่าย (PromptPay)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-card-tab" data-toggle="pill" href="#pills-card" role="tab">
                            <i class="fas fa-credit-card mr-1"></i> บัตรเครดิต / เดบิต
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="pills-tabContent">
                    
                    <div class="tab-pane fade show active" id="pills-transfer" role="tabpanel">
                        <div class="row">
                            <div class="col-md-5 text-center border-right">
                                <p class="mb-2">สแกนเพื่อชำระเงิน</p>
                                
                                <img src="https://promptpay.io/0802174652/<?= $booking['total_price'] ?>" 
                                     class="img-fluid border rounded p-2 mb-2" 
                                     width="200" 
                                     alt="QR Code">
                                
                                <p class="small text-muted">พร้อมเพย์: 080-217-4652</p>
                            </div>
                            <div class="col-md-7 pl-4">
                                <form action="<?= base_url('customer/payment/process') ?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="booking_id" value="<?= esc($booking['id']) ?>">
                                    <input type="hidden" name="payment_method" value="transfer">
                                    
                                    <div class="form-group">
                                        <label>แนบหลักฐานการโอนเงิน (สลิป)</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="slip_image" name="slip_image" accept="image/*" required>
                                            <label class="custom-file-label" for="slip_image">เลือกไฟล์...</label>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">
                                        <i class="fas fa-paper-plane mr-2"></i> แจ้งชำระเงิน
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pills-card" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> ระบบจำลอง (Simulation Mode): สามารถกรอกเลขอะไรก็ได้
                        </div>
                        
                        <form id="creditCardForm" action="<?= base_url('customer/payment/process') ?>" method="post">
                            <input type="hidden" name="booking_id" value="<?= esc($booking['id']) ?>">
                            <input type="hidden" name="payment_method" value="credit_card">

                            <div class="form-group">
                                <label>หมายเลขบัตร</label>
                                <input type="text" class="form-control" placeholder="0000 0000 0000 0000" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>ชื่อบนบัตร</label>
                                    <input type="text" class="form-control" placeholder="NAME SURNAME" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>หมดอายุ</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>CVV</label>
                                    <input type="password" class="form-control" placeholder="123" required>
                                </div>
                            </div>

                            <button type="button" class="btn btn-success btn-block btn-lg mt-3 btn-pay-card">
                                <i class="fas fa-lock mr-2"></i> ชำระเงินทันที (฿<?= number_format($booking['total_price']) ?>)
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        
        document.querySelector('.btn-pay-card').addEventListener('click', function() {
            const form = document.getElementById('creditCardForm');
            
            
            if(!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            
            let timerInterval;
            Swal.fire({
                title: 'กำลังเชื่อมต่อธนาคาร...',
                html: 'กรุณาอย่าปิดหน้าต่างนี้',
                timer: 2000, 
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then((result) => {
                
                form.submit();
            });
        });
    </script>

</body>
</html>