<?php
use App\Models\OwnerStadiumModel;

$stadiumModel = new OwnerStadiumModel();

$stadiums = $stadiumModel
    ->where('vendor_id', session()->get('owner_id'))
    ->orderBy('id', 'DESC')
    ->findAll();
?>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">

    <!-- Header -->
    <div class="sidebar-header">
        <h5 class="text-center mb-0 mt-2">สนามของคุณ</h5>
        <button class="close-btn" onclick="toggleSidebar()">×</button>
    </div>

    <!-- รายชื่อสนาม -->
    <div class="sidebar-content">
        <ul class="nav flex-column stadium-list">

            <?php if (!empty($stadiums) && is_array($stadiums)): ?>
                <?php foreach ($stadiums as $st): ?>
                    <li class="nav-item">
                        <a href="<?= base_url('owner/fields/view/'.$st['id']) ?>" 
                        class="nav-link stadium-item">
                        🏟 <?= esc($st['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="nav-item text-center text-muted py-3">
                    ยังไม่มีสนาม
                </li>
            <?php endif; ?>


        </ul>

        <!-- Footer อยู่ใต้สุดของรายการ -->
        <div class="sidebar-footer mt-3">
            <a href="<?= base_url('owner/fields/step1') ?>" class="btn btn-mint w-100 mb-2">➕ เพิ่มสนามใหม่</a>
            <a href="<?= base_url('owner/logout') ?>" class="btn btn-outline-danger w-100">🚪 ออกจากระบบ</a>
        </div>
    </div>

</div>

<!-- ปุ่มเปิด Sidebar (ย้ายออกมาอยู่นอก sidebar) -->
<button class="toggle-btn-inside" onclick="toggleSidebar()">☰</button>




<style>
/* ============ SIDEBAR ============ */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 260px;
    height: 100%;
    background: white;
    transform: translateX(-260px);
    transition: 0.3s ease;
    z-index: 1000;
    border-right: 1px solid #ddd;
    display: flex;
    flex-direction: column;
}

.sidebar.open {
    transform: translateX(0);
}

/* Header สีเขียว */
.sidebar-header {
    background: #00c389;
    color: white;
    padding: 20px 10px;
    position: relative;
    border-bottom: 1px solid #009d6a;
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 12px;
    background: none;
    border: none;
    color: white;
    font-size: 26px;
    cursor: pointer;
}

/* เนื้อหา */
.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 10px 0;
}

.sidebar-footer {
    padding: 15px;
    background: #f8f9fa;
    border-top: 1px solid #ddd;
    z-index: 5;   /* เดิมไม่มี */
    position: relative;
}


/* รายชื่อสนาม */
.stadium-item {
    padding: 10px 20px;
    border-radius: 6px;
    color: #333;
    font-weight: 500;
    display: block;
}

.stadium-item:hover {
    background: #e9fdf7;
    color: #00a278;
}

/* ปุ่มเปิด sidebar - ย้ายเข้าไปด้านใน */
.toggle-btn-inside {
    position: fixed;
    bottom: 20px;
    left: 20px;
    background: #00c389;
    border: none;
    color: white;
    font-size: 24px;
    padding: 10px 14px;
    border-radius: 10px;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(0,0,0,0.25);
    z-index: 2000; /* สูงกว่า sidebar */
}


/* เอาปุ่มเปิดเดิมออก (ถ้ามีอยู่ด้านนอก) */
.toggle-btn {
    display: none !important;
}

/* Dashboard เลื่อนตาม */
.dashboard-wrapper {
    transition: margin-left 0.3s ease;
    margin-left: 0;
}

.dashboard-wrapper.shifted {
    margin-left: 260px;
    padding-left: 10px; /* เพิ่มเล็กน้อยให้เนียน */
}

</style>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const wrap = document.getElementById("dashboard-wrapper");

    sidebar.classList.toggle("open");
    wrap.classList.toggle("shifted");
}
</script>
