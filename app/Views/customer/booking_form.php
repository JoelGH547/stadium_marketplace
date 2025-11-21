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
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><span class="navbar-text">สวัสดี, <?= esc(session()->get('username')) ?></span></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('logout') ?>">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 30px;">
        <h2><?= esc($stadium['name']) ?></h2>
        <span class="badge badge-info"><?= esc($stadium['category_name'] ?? 'N/A') ?></span>
        <p class="mt-3"><?= esc($stadium['description']) ?></p>
        <h3 class="text-danger">ราคา: <?= esc(number_format($stadium['price'], 2)) ?> บาท/ชั่วโมง</h3>
        <hr>

        <h4>กรุณาเลือกข้อมูลการจอง</h4>
        
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('customer/booking/process') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="stadium_id" value="<?= esc($stadium['id']) ?>">

            <div class="form-group">
                <label for="field_id" class="font-weight-bold text-primary">เลือกสนามย่อย / โซน:</label>
                <select name="field_id" id="field_id" class="form-control" required>
                    <option value="" disabled selected>-- กรุณาเลือกสนามที่ต้องการ --</option>
                    
                    <?php if (!empty($fields)): ?>
                        <?php foreach ($fields as $field): ?>
                            <option value="<?= $field['id'] ?>">
                                <?= esc($field['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>ไม่มีข้อมูลสนามย่อย</option>
                    <?php endif; ?>
                </select>
                <small class="form-text text-muted">เช่น สนาม 1, สนาม 2, หรือ โต๊ะ VIP</small>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="booking_date">วันที่จอง:</label>
                    <input type="date" class="form-control" name="booking_date" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="start_time">เวลาที่เริ่ม:</label>
                    <input type="time" class="form-control" name="start_time" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="hours">จำนวนชั่วโมง:</label>
                    <select name="hours" class="form-control">
                        <option value="1">1 ชั่วโมง</option>
                        <option value="2">2 ชั่วโมง</option>
                        <option value="3">3 ชั่วโมง</option>
                        <option value="4">4 ชั่วโมง</option>
                        <option value="5">5 ชั่วโมง</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-lg mt-3 btn-block">ยืนยันการจอง (รอดำเนินการ)</button>
        </form>
    </div>

</body>
</html>