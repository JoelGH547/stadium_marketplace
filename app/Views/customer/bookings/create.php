<?= $this->extend('customer/layout') ?>
<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>
<p><a href="<?= base_url('customer/dashboard') ?>">
    <button class="btn btn-secondary">&laquo; Back to Stadium List</button>
</a></p>

<?php $validation = session()->getFlashdata('validation'); ?>
<?php if ($validation): ?>
    <div class="alert alert-danger">
        <?= $validation->listErrors() ?? 'Please check your input.' ?>
    </div>
<?php endif; ?>

<div class="content-wrapper" style="margin-bottom: 20px; background-color: #f9f9f9;">
    <h3>You are booking: <?= esc($stadium['name']) ?></h3>
    <p><?= esc($stadium['description']) ?></p>
    <p><strong>Category:</strong> <?= esc($stadium['category_name'] ?? 'N/A') ?></p>
    <p><strong>Price:</strong> <span id="price-per-hour"><?= esc(number_format($stadium['price'], 2)) ?></span> THB per hour</p>
</div>

<form action="<?= base_url('customer/book/process') ?>" method="post">
    <?= csrf_field() ?>

    <input type="hidden" name="stadium_id" value="<?= esc($stadium['id']) ?>">
    <input type="hidden" name="vendor_id" value="<?= esc($stadium['vendor_id']) ?>">
    <input type="hidden" id="hidden_price_per_hour" value="<?= esc($stadium['price']) ?>">

    <div class="form-group">
        <label for="booking_date">Date (วันที่จอง):</label>
        <input type="date" id="booking_date" name="booking_date" class="form-control" value="<?= old('booking_date', date('Y-m-d')) ?>" required>
    </div>

    <div class="form-group">
        <label for="start_time">Start Time (เวลาเริ่ม):</label>
        <input type="time" id="start_time" name="start_time" class="form-control" value="<?= old('start_time') ?>" required onchange="calculatePrice()">
    </div>

    <div class="form-group">
        <label for="end_time">End Time (เวลาสิ้นสุด):</label>
        <input type="time" id="end_time" name="end_time" class="form-control" value="<?= old('end_time') ?>" required onchange="calculatePrice()">
    </div>
    
    <hr>
    
    <div class="form-group">
        <h3>Total Price (ราคารวม): <span id="total-price-display">0.00</span> THB</h3>
        <input type="hidden" id="total_price" name="total_price" value="0">
    </div>

    <div class="form-group" style="margin-top: 20px;">
        <button type="submit" class="btn btn-primary">Confirm Booking (ยืนยันการจอง)</button>
    </div>
</form>

<script>
    function calculatePrice() {
        // 1. ดึง "ราคาต่อชั่วโมง" (จาก hidden input)
        var pricePerHour = parseFloat(document.getElementById('hidden_price_per_hour').value);
        
        // 2. ดึง "เวลา" (จากฟอร์ม)
        var startTime = document.getElementById('start_time').value;
        var endTime = document.getElementById('end_time').value;

        // (ถ้ายังกรอกไม่ครบ... ให้เป็น 0)
        if (!startTime || !endTime || !pricePerHour) {
            document.getElementById('total-price-display').innerText = '0.00';
            document.getElementById('total_price').value = 0;
            return;
        }

        // 3. แปลง "เวลา" (String) ...เป็น "เวลา" (Date Object)
        // (เราใช้ '1970-01-01' เป็น "วันจำลอง" ...เพื่อคำนวณ "ส่วนต่าง" (Diff) ชั่วโมง)
        var startDate = new Date('1970-01-01T' + startTime + 'Z');
        var endDate = new Date('1970-01-01T' + endTime + 'Z');

        // 4. คำนวณ "ส่วนต่าง" (เป็น "มิลลิวินาที")
        var diffMs = endDate.getTime() - startDate.getTime();

        // (ถ้าเวลาสิ้นสุด "น้อยกว่า" เวลาเริ่มต้น... ให้เป็น 0)
        if (diffMs <= 0) {
            document.getElementById('total-price-display').innerText = '0.00';
            document.getElementById('total_price').value = 0;
            return;
        }

        // 5. แปลง "มิลลิวินาที" -> "ชั่วโมง" (1000ms * 60s * 60m)
        var diffHours = diffMs / (1000 * 60 * 60);

        // 6. คำนวณ "ราคารวม"
        var totalPrice = diffHours * pricePerHour;

        // 7. "แสดงผล" (Display) ราคารวม
        document.getElementById('total-price-display').innerText = totalPrice.toFixed(2);
        // 8. "เก็บ" (Set) ราคารวม... ลงใน hidden input (เพื่อส่ง POST)
        document.getElementById('total_price').value = totalPrice.toFixed(2);
    }
</script>

<?= $this->endSection() ?>