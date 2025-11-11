<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<!-- 💡 Note: เราใช้ layout.php เดิม ซึ่งมี CSS สำหรับ card และ sidebar อยู่แล้ว -->

<style>
    /* CSS เพิ่มเติมสำหรับ Customer Dashboard (ถ้าต้องการสีเฉพาะ) */
    .stats-container {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }
    .stat-card {
        flex: 1;
        background-color: #f7f9fa; /* สีพื้นหลังการ์ด */
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }
    .stat-card h3 {
        color: #34495e;
    }
    .stat-card .number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #1abc9c; /* สีเขียวมิ้นท์ Customer */
        margin-top: 10px;
    }
</style>

<h1>Customer Dashboard</h1>

<!-- แสดงข้อมูลต้อนรับ -->
<?php if (isset($customer)): ?>
    <p>สวัสดี, **<?= esc($customer['full_name'] ?? $customer['username']) ?>**!</p>
    <p>คุณล็อคอินในฐานะ **ลูกค้า (Customer)** คุณสามารถใช้เมนูหรือปุ่มด้านล่างเพื่อทำการจองสนามได้ทันที</p>
<?php endif; ?>


<!-- ===== STATS CARDS (การ์ดสรุปข้อมูลสำหรับ Customer) ===== -->
<div class="stats-container">

    <!-- Card 1: จำนวนการจองที่ยังไม่เสร็จ -->
    <div class="stat-card">
        <h3>การจองที่กำลังจะมา</h3>
        <div class="number">
            <!-- 💡 เปลี่ยนเป็น $upcoming_bookings ในอนาคต -->
            0
        </div>
        <a href="<?= base_url('customer/bookings/upcoming') ?>">ดูรายละเอียด &rarr;</a>
    </div>

    <!-- Card 2: สนามกีฬาที่จองบ่อยที่สุด -->
    <div class="stat-card">
        <h3>สนามที่จองล่าสุด</h3>
        <div class="number" style="font-size: 1.2rem; font-weight: normal;">
            สนามแบดมินตัน B4
        </div>
        <a href="<?= base_url('customer/book') ?>">จองสนามใหม่ &rarr;</a>
    </div>

    <!-- Card 3: แต้มสะสม -->
    <div class="stat-card">
        <h3>แต้มสะสม</h3>
        <div class="number">
            0
        </div>
        <p style="font-size: 0.85rem; color: #7f8c8d;">(ข้อมูลจำลอง)</p>
    </div>

</div>

<?= $this->endSection() ?>