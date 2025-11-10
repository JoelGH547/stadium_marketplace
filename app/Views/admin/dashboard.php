<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<!-- เพิ่ม CSS สำหรับการ์ดสรุปข้อมูล -->
<style>
    .stats-container {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }
    .stat-card {
        flex: 1;
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

<!-- แสดงชื่อผู้ใช้ที่ Login -->
<?php if (isset($user)): ?>
    <p>สวัสดี, <?= esc($user['username']) ?>! (Role: <?= esc($user['role']) ?>)</p>
<?php endif; ?>


<!-- ===== STATS CARDS (การ์ดสรุปข้อมูล) ===== -->
<div class="stats-container">

    <!-- Card: Total Stadiums (แก้ไขจาก Products) -->
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

    <!-- Card: Total Users -->
    <div class="stat-card" style="--stat-color: #9b59b6;">
        <h3>ผู้ใช้งานทั้งหมด</h3>
        <div class="number" style="color: #9b59b6;">
            <?= esc($total_users) ?>
        </div>
        <a href="<?= base_url('admin/users') ?>">ดูรายละเอียด &rarr;</a>
    </div>

</div>

<!-- (สามารถเพิ่มส่วน "สินค้าใกล้หมด" หรือ กราฟ ที่นี่ในอนาคต) -->

<?= $this->endSection() ?>