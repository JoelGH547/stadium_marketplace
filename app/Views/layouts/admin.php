<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* กำหนดตัวแปรสี Mint Theme */
            --sidebar-bg: #111827;      /* หรือ #0f172a ถ้าชอบโทนน้ำเงินลึก */
            --sidebar-bg-accent: #134e4a; /* สีเขียวเข้มสำหรับส่วนหัว */
            --mint-primary: #14b8a6;    /* สีมินต์หลัก */
            --mint-light: #ccfbf1;      /* สีมินต์อ่อน */
            --mint-hover: #2dd4bf;      /* สีมินต์สว่างตอนเอาเมาส์ชี้ */
            --bg-body: #f0fdfa;         /* พื้นหลังหน้าเว็บอมเขียวจางๆ */
        }

        body {
            font-family: 'Prompt', sans-serif;
            min-height: 100vh;
            background-color: var(--bg-body);
        }

        /* --- Sidebar Styling --- */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: var(--sidebar-bg); /* สีพื้นหลัง Sidebar */
            color: #9ca3af; /* สีตัวหนังสือเทาอ่อน */
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }

        .sidebar-header {
            background-color: var(--sidebar-bg-accent); /* สีหัว Sidebar */
            color: #fff;
        }

        .nav-link {
            color: #9ca3af;
            padding: 14px 20px;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
            font-weight: 400;
            border-left: 4px solid transparent; /* เตรียมเส้นซ้ายไว้ */
        }

        .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* เมนูที่ถูกเลือก (Active) - ธีมมินต์ */
        .nav-link.active-menu {
            color: var(--mint-hover); /* ตัวหนังสือสีมินต์สว่าง */
            background-color: rgba(20, 184, 166, 0.1); /* พื้นหลังสีมินต์จางๆ */
            border-left: 4px solid var(--mint-primary); /* เส้นด้านซ้ายสีมินต์ */
        }

        .nav-link i {
            width: 25px;
            font-size: 1.1rem;
        }
        
        /* --- Submenu Styling --- */
        .submenu {
            background-color: #00000030; /* สีพื้นหลังเมนูย่อยให้เข้มลงนิดนึง */
        }
        .submenu .nav-link {
            padding-left: 55px;
            font-size: 0.9em;
            border-left: none;
        }
        /* เมนูย่อยที่ถูกเลือก */
        .submenu .nav-link.active-sub {
            color: var(--mint-hover) !important;
            font-weight: 500;
        }

        /* --- Content Area --- */
        .content {
            width: 100%;
            padding: 25px;
            overflow-y: auto;
            height: 100vh;
        }
        
        /* --- Custom Button Theme Mint (แถมให้) --- */
        .btn-mint {
            background-color: var(--mint-primary);
            color: white;
            border: none;
        }
        .btn-mint:hover {
            background-color: #0d9488;
            color: white;
        }

        /* ลูกศรหมุน */
        .collapse.show ~ .nav-link .fa-chevron-down {
            transform: rotate(180deg);
        }
        .fa-chevron-down {
            transition: transform 0.3s;
            font-size: 0.75em;
        }
    </style>
</head>
<body class="d-flex">

    <?php 
        $uri = service('uri');
        $currentUrl = $uri->getPath(); 
    ?>

    <div class="sidebar d-flex flex-column p-0 flex-shrink-0">
        <div class="p-4 text-center sidebar-header border-bottom border-secondary border-opacity-25">
            <h4 class="m-0 fw-bold tracking-wide">
                <i class="fas fa-leaf me-2 text-white"></i>Stadium<span style="color: var(--mint-hover);">Admin</span>
            </h4>
        </div>

        <ul class="nav flex-column mt-3">
            
            <li class="nav-item">
                <a href="<?= base_url('admin/dashboard') ?>" 
                   class="nav-link <?= (strpos($currentUrl, 'dashboard') !== false) ? 'active-menu' : '' ?>">
                    <div><i class="fas fa-chart-pie"></i> Dashboard</div>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('admin/categories') ?>" 
                   class="nav-link <?= (strpos($currentUrl, 'categories') !== false) ? 'active-menu' : '' ?>">
                    <div><i class="fas fa-layer-group"></i> จัดการหมวดหมู่</div>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('admin/stadiums') ?>" 
                   class="nav-link <?= (strpos($currentUrl, 'stadiums') !== false) ? 'active-menu' : '' ?>">
                    <div><i class="fas fa-map-location-dot"></i> จัดการสนามกีฬา</div>
                </a>
            </li>

            <?php 
                $isUserMenu = (strpos($currentUrl, 'admin/users') !== false);
            ?>
            <li class="nav-item">
                <a class="nav-link <?= $isUserMenu ? 'active-menu' : 'collapsed' ?>" 
                   href="#userSubmenu" 
                   data-bs-toggle="collapse" 
                   role="button" 
                   aria-expanded="<?= $isUserMenu ? 'true' : 'false' ?>" 
                   aria-controls="userSubmenu">
                    <div><i class="fas fa-users-cog"></i> จัดการผู้ใช้งาน</div>
                    <i class="fas fa-chevron-down"></i>
                </a>
                
                <div class="collapse <?= $isUserMenu ? 'show' : '' ?>" id="userSubmenu">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a href="<?= base_url('admin/users/admins') ?>" 
                               class="nav-link <?= (strpos($currentUrl, 'users/admins') !== false) ? 'active-sub' : '' ?>">
                                • Admins (ผู้ดูแล)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/users/vendors') ?>" 
                               class="nav-link <?= (strpos($currentUrl, 'users/vendors') !== false) ? 'active-sub' : '' ?>">
                                • Vendors (เจ้าของ)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/users/customers') ?>" 
                               class="nav-link <?= (strpos($currentUrl, 'users/customers') !== false) ? 'active-sub' : '' ?>">
                                • Customers (ลูกค้า)
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            </ul>
        
        <div class="mt-auto p-3 border-top border-secondary border-opacity-25">
            <a href="<?= base_url('admin/logout') ?>" class="btn btn-outline-danger w-100">
                <i class="fas fa-power-off me-2"></i> ออกจากระบบ
            </a>
        </div>
    </div>

    <div class="content">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>