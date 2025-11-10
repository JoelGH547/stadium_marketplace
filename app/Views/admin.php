<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 
      - $title จะถูกส่งมาจาก Controller (เช่น 'Product List')
      - 'CI4 Stock' คือชื่อระบบหลัก
    -->
    <title><?= (isset($title) ? esc($title) . ' | ' : '') ?>CI4 Stock System</title>
    
    <!-- (CSS สำหรับ Layout นี้เท่านั้น) -->
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            background-color: #f4f6f9;
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 240px;
            background-color: #343a40;
            color: white;
            padding: 15px;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            color: #f8f9fa;
            margin-top: 0;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        .sidebar-menu a {
            color: #c2c7d0;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: #495057;
            color: #fff;
        }
        .main-content {
            flex-grow: 1;
            padding: 30px;
            overflow-y: auto;
        }
        .content-wrapper {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <!-- ===== 1. Sidebar (เมนูหลัก) ===== -->
    <div class="sidebar">
        <h2>CI4 Stock</h2>
        <ul class="sidebar-menu">
            <li><a href="<?= base_url('admin/categories') ?>">จัดการหมวดหมู่</a></li>
            <li><a href="<?= base_url('admin/products') ?>">จัดการสินค้า</a></li>
            <hr style="border-color: #495057;">
            <li><a href="<?= base_url('admin/stock/in') ?>">รับสินค้าเข้า</a></li>
            <li><a href="<?= base_url('admin/stock/out') ?>">เบิกสินค้าออก</a></li>
            <hr style="border-color: #495057;">
            <li><a href="<?= base_url('logout') ?>" style="color: #dc3545;">ออกจากระบบ</a></li>
        </ul>
    </div>

    <!-- ===== 2. Main Content (ส่วนเนื้อหา) ===== -->
    <div class="main-content">
        <div class="content-wrapper">
            <!-- 
              - นี่คือจุดที่ CI4 จะ "ฉีด" เนื้อหาจาก View อื่นๆ (เช่น index.php, create.php)
              - เข้ามาแทนที่
            -->
            <?= $this->renderSection('content') ?>
        </div>
    </div>

</body>
</html>