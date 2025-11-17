<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<style>
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .stat-card {
        background-color: #ecf0f1;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .stat-card h3 { margin: 0; color: #34495e; font-size: 1.2rem; }
    .stat-card .number { font-size: 2.5rem; font-weight: bold; margin-top: 10px; }
    .stat-card a { display: inline-block; margin-top: 15px; text-decoration: none; color: #2980b9; font-weight: bold; }
    
    /* (Style 3 สีสำหรับ "Receive") */
    .stat-card.pending-vendor { /* (สีส้ม) */
        background-color: #fdf3e6; border: 1px solid #f39c12;
    }
    .stat-card.pending-vendor .number { color: #f39c12; }

    .stat-card.new-booking { /* (สีเขียว) */
        background-color: #eafaf1; border: 1px solid #2ecc71;
    }
    .stat-card.new-booking .number { color: #2ecc71; }
    
    .stat-card.pending-booking { /* (สีฟ้า) */
        background-color: #e5f5fb; border: 1px solid #3498db;
    }
    .stat-card.pending-booking .number { color: #3498db; }

    /* 1. ⬇️ (เพิ่ม) Style สำหรับลูกค้าใหม่ ⬇️ */
    .stat-card.new-customer { /* (สีเขียวมิ้นท์) */
        background-color: #e8f8f5; border: 1px solid #1abc9c;
    }
    .stat-card.new-customer .number { color: #1abc9c; }
</style>

<h1><?= esc($title) ?></h1>

<div class="stats-container">

    <?php if ($total_pending_vendors > 0): ?>
        <div class="stat-card pending-vendor"> 
            <h3>Vendors รอยืนยัน</h3>
            <div class="number"><?= esc($total_pending_vendors) ?></div>
            <a href="<?= base_url('admin/vendors/pending') ?>">อนุมัติเลย &rarr;</a>
        </div>
    <?php endif; ?>

    <?php if ($total_new_bookings > 0): ?>
        <div class="stat-card new-booking"> 
            <h3>การจองใหม่ (จ่ายแล้ว)</h3>
            <div class="number"><?= esc($total_new_bookings) ?></div>
            <a href="<?= base_url('admin/bookings/new') ?>">ดูเลย &rarr;</a>
        </div>
    <?php endif; ?>
    
    <?php if ($total_pending_bookings > 0): ?>
        <div class="stat-card pending-booking"> 
            <h3>การจอง (รอจ่ายเงิน)</h3>
            <div class="number"><?= esc($total_pending_bookings) ?></div>
            <a href="<?= base_url('admin/bookings/pending') ?>">ดูรายการ &rarr;</a>
        </div>
    <?php endif; ?>

    <?php if ($total_new_customers > 0): ?>
        <div class="stat-card new-customer"> 
            <h3>ลูกค้าใหม่ (ใน 24 ชม.)</h3>
            <div class="number"><?= esc($total_new_customers) ?></div>
            <a href="<?= base_url('admin/users') ?>#customers">ดูทั้งหมด &rarr;</a>
        </div>
    <?php endif; ?>


    <div class="stat-card">
        <h3>สนามกีฬาทั้งหมด</h3>
        <div class="number" style="color: #9b59b6;">
            <?= esc($total_stadiums) ?>
        </div>
    </div>
    <div class="stat-card">
        <h3>หมวดหมู่ทั้งหมด</h3>
        <div class="number" style="color: #e67e22;">
            <?= esc($total_categories) ?>
        </div>
    </div>
    <div class="stat-card">
        <h3>Admins (ทั้งหมด)</h3>
        <div class="number" style="color: #e74c3c;">
            <?= esc($total_admins) ?>
        </div>
    </div>
    <div class="stat-card">
        <h3>Vendors (ทั้งหมด)</h3>
        <div class="number" style="color: #f39c12;">
            <?= esc($total_vendors) ?>
        </div>
    </div>
    <div class="stat-card">
        <h3>Customers (ทั้งหมด)</h3>
        <div class="number" style="color: #1abc9c;">
            <?= esc($total_customers) ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>