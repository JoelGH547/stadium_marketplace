<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 1. ⬇️ แก้ไข Title ⬇️ -->
    <title><?= (isset($title) ? esc($title) : 'Customer Panel') ?></title>
    
    <!-- (CSS ทั้งหมดเหมือนเดิม... เรายืมมาจาก Layout อื่น) -->
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
            background-color: #f4f7f6;
        }
        .sidebar {
            width: 240px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            box-sizing: border-box;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar h2 {
            text-align: center;
            color: #ecf0f1;
            margin-bottom: 30px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li a {
            color: #ecf0f1;
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar-menu li a:hover {
            background-color: #34495e;
        }
        .sidebar-menu li a.active {
            background-color: #1abc9c;
            color: white;
        }
        .sidebar-menu .logout {
            position: absolute;
            bottom: 20px;
            width: 200px;
        }
        .sidebar-menu .logout a {
            background-color: #e74c3c;
            text-align: center;
        }
        .sidebar-menu .logout a:hover {
            background-color: #c0392b;
        }
        .main-content {
            margin-left: 240px;
            padding: 25px;
            width: calc(100% - 240px);
            overflow-y: auto;
            height: 100vh;
            box-sizing: border-box;
        }
        .content-wrapper {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        h1, h2, h3 {
            color: #34495e;
            margin-top: 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            vertical-align: middle;
        }
        .table thead th {
            background-color: #f7f9fa;
            font-weight: 600;
            color: #34495e;
        }
        .table tbody tr:nth-child(even) {
            background-color: #fdfdfd;
        }
        .table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            display: inline-block;
            padding: 8px 14px;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
            text-align: center;
        }
        .btn-primary { background-color: #3498db; }
        .btn-primary:hover { background-color: #2980b9; }
        .btn-warning { background-color: #f39c12; color: white; }
        .btn-warning:hover { background-color: #e67e22; }
        .btn-danger { background-color: #e74c3c; }
        .btn-danger:hover { background-color: #c0392b; }
        .btn-secondary { background-color: #bdc3c7; }
        .btn-secondary:hover { background-color: #95a5a6; }
        .btn-sm { padding: 5px 10px; font-size: 0.8rem; }
        .badge { display: inline-block; padding: 4px 10px; font-size: 0.8rem; font-weight: 600; border-radius: 12px; color: white; }
        .badge-admin { background-color: #e74c3c; }
        .badge-staff { background-color: #3498db; }
        .badge-vendor { background-color: #f39c12; }
        .badge-customer { background-color: #1abc9c; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 5px; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-danger, .alert-error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
    </style>
</head>
<body>

    <!-- ===== SIDEBAR (เมนูหลัก "ของ Customer") ===== -->
    <div class="sidebar">
        <!-- 2. ⬇️ แก้ไข H2 ⬇️ -->
        <h2>Stadium Booking</h2>
        <ul class="sidebar-menu">
            <!-- 3. ⬇️ แก้ไข Link -> customer/dashboard ⬇️ -->
            <li><a href="<?= base_url('customer/dashboard') ?>">หน้าหลัก (จองสนาม)</a></li>
            
            <!-- 4. ⬇️ (เพิ่ม "การจองของฉัน" (My Bookings)) ⬇️ -->
            <li><a href="<?= base_url('customer/bookings') ?>">การจองของฉัน</a></li>
            
            <!-- 5. ⬇️ (เพิ่ม "โปรไฟล์") ⬇️ -->
            <li><a href="<?= base_url('customer/profile') ?>">โปรไฟล์ของฉัน</a></li>
            
            <hr>
            
            <li class="logout">
                <!-- 6. ⬇️ แก้ไข Link -> logout (ตัวหลัก) ⬇️ -->
                <a href="<?= base_url('logout') ?>">ออกจากระบบ</a>
            </li>
        </ul>
    </div>

    <!-- ===== MAIN CONTENT (ส่วนเนื้อหา) ===== -->
    <div class="main-content">
        <div class="content-wrapper">
            <!-- นี่คือจุดที่ View อื่นๆ จะถูกแทรกเข้ามา -->
            <?= $this->renderSection('content') ?>
        </div>
    </div>

</body>
</html>