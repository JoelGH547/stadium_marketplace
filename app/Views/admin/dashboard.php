<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<!-- เพิ่ม CSS สำหรับการ์ดสรุปข้อมูล -->
<style>
    .stats-container {
        display: grid; /* 1. ⬇️ เปลี่ยนเป็น grid เพื่อให้ตัดแถวได้ง่าย */
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* 2. ⬇️ ทำให้ยืดหยุ่น */
        gap: 20px;
        margin-top: 20px;
    }
    .stat-card {
        /* flex: 1; (ลบออก) */
        background-color: #ecf0f1;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-card h3 {
        margin: 0;
        color: #34495e;
        font-size: 1.2rem;
    }
    .stat-card .number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #1abc9c;
        margin-top: 10px;
    }
    .stat-card a {
        display: inline-block;
        margin-top: 15px;
        text-decoration: none;
        color: #2980b9;
        font-weight: bold;
    }
</style>

<h1><?= esc($title) ?></h1>

<!-- 3. ⬇️ ลบส่วนแสดงชื่อ User ออก (เพราะเราไม่รู้ว่ามาจากตารางไหน) -->
<!-- <?php if (isset($user)): ?> ... <?php endif; ?> -->


<!-- ===== STATS CARDS (การ์ดสรุปข้อมูล) ===== -->
<div class="stats-container">

    <!-- Card: Total Stadiums -->
    <div class="stat-card" style="--stat-color: #3498db;">
        <h3>สนามกีฬาทั้งหมด</h3>
        <div class="number" style="color: #3498db;">
            <?= esc($total_stadiums) ?>
        </div>
        <a href="<?= base_url('admin/stadiums') ?>">ดูรายละเอียด &rarr;</a>
    </div>

    <!-- Card: Total Categories -->
    <div class="stat-card" style="--stat-color: #e67e22;">
        <h3>หมวดหมู่ทั้งหมด</h3>
        <div class="number" style="color: #e67e22;">
            <?= esc($total_categories) ?>
        </div>
        <a href="<?= base_url('admin/categories') ?>">ดูรายละเอียด &rarr;</a>
    </div>

    <!-- 4. ⬇️ ลบ "ผู้ใช้งานทั้งหมด" (เก่า) และเพิ่ม 3 การ์ดใหม่ ⬇️ -->

    <!-- Card: Total Admins -->
    <div class="stat-card" style="--stat-color: #e74c3c;">
        <h3>Admins</h3>
        <div class="number" style="color: #e74c3c;">
            <?= esc($total_admins) ?>
        </div>
        <a href="<?= base_url('admin/users') ?>#admins">ดูรายละเอียด &rarr;</a>
    </div>
    
    <!-- Card: Total Vendors -->
    <div class="stat-card" style="--stat-color: #f39c12;">
        <h3>Vendors</h3>
        <div class="number" style="color: #f39c12;">
            <?= esc($total_vendors) ?>
        </div>
        <a href="<?= base_url('admin/users') ?>#vendors">ดูรายละเอียด &rarr;</a>
    </div>
    
    <!-- Card: Total Customers -->
    <div class="stat-card" style="--stat-color: #1abc9c;">
        <h3>Customers</h3>
        <div class="number" style="color: #1abc9c;">
            <?= esc($total_customers) ?>
        </div>
        <a href="<?= base_url('admin/users') ?>#customers">ดูรายละเอียด &rarr;</a>
    </div>

</div>

<?= $this->endSection() ?>