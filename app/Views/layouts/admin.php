<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            /* Theme Colors */
            --sidebar-bg: #111827;
            --sidebar-bg-accent: #134e4a;
            --mint-primary: #14b8a6;
            --mint-light: #ccfbf1;
            --mint-hover: #2dd4bf;
            --bg-body: #f0fdfa;
        }

        body {
            font-family: 'Prompt', sans-serif;
            min-height: 100vh;
            background-color: var(--bg-body);
            font-size: 0.9rem; 
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: #9ca3af;
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        .sidebar-header {
            background-color: var(--sidebar-bg-accent);
            color: #fff;
        }
        .nav-link {
            color: #9ca3af;
            padding: 10px 15px;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
            font-weight: 400;
            border-left: 4px solid transparent;
            font-size: 0.9rem;
        }
        .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
        }
        .nav-link.active-menu {
            color: var(--mint-hover);
            background-color: rgba(20, 184, 166, 0.1);
            border-left: 4px solid var(--mint-primary);
        }
        .nav-link i {
            width: 25px;
            font-size: 1.1rem;
        }
        
        /* Submenu */
        .submenu { background-color: #00000030; }
        .submenu .nav-link { padding-left: 55px; font-size: 0.85rem; border-left: none; }
        .submenu .nav-link.active-sub { color: var(--mint-hover) !important; font-weight: 500; }

        /* Content */
        .content { width: 100%; padding: 25px; overflow-y: auto; height: 100vh; }
        
        /* Custom Buttons */
        .btn-mint { background-color: var(--mint-primary); color: white; border: none; }
        .btn-mint:hover { background-color: #0d9488; color: white; }
        
        /* Table & Buttons (Compact Mode) */
        .table td, .table th { padding: 0.5rem 0.5rem !important; vertical-align: middle; font-size: 0.9rem; }
        .btn-sm { padding: 0.2rem 0.5rem; font-size: 0.85rem; }
        .card-body { padding: 1rem; }

        /* Misc */
        .collapse.show ~ .nav-link .fa-chevron-down { transform: rotate(180deg); }
        .fa-chevron-down { transition: transform 0.3s; font-size: 0.75em; }
        
        /* DataTables Style */
        .dataTables_wrapper { padding: 1rem 0; }
        .dataTables_filter input, .dataTables_length select {
            border-radius: 0.375rem !important; border: 1px solid #ddd !important; padding: 0.4rem 0.75rem;
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
                <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= (strpos($currentUrl, 'dashboard') !== false) ? 'active-menu' : '' ?>">
                    <div><i class="fas fa-chart-pie"></i> Dashboard</div>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= base_url('admin/categories') ?>" class="nav-link <?= (strpos($currentUrl, 'categories') !== false) ? 'active-menu' : '' ?>">
                    <div><i class="fas fa-layer-group"></i> จัดการประเภทกีฬา</div>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('admin/facility-types') ?>" class="nav-link <?= (strpos($currentUrl, 'facility-types') !== false) ? 'active-menu' : '' ?>">
                    <div><i class="fas fa-concierge-bell"></i> หมวดหมู่สิ่งอำนวยความสะดวก</div>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('admin/stadiums') ?>" class="nav-link <?= (strpos($currentUrl, 'stadiums') !== false) ? 'active-menu' : '' ?>">
                    <div><i class="fas fa-map-location-dot"></i> จัดการสนามกีฬา</div>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= base_url('admin/bookings') ?>" class="nav-link <?= (strpos($currentUrl, 'bookings') !== false) ? 'active-menu' : '' ?>">
                    <div><i class="fas fa-calendar-check"></i> จัดการการจอง</div>
                </a>
            </li>

            <?php $isUserMenu = (strpos($currentUrl, 'admin/users') !== false || strpos($currentUrl, 'admin/vendors/pending') !== false); ?>
            <li class="nav-item">
                <a class="nav-link <?= $isUserMenu ? 'active-menu' : 'collapsed' ?>" href="#userSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="<?= $isUserMenu ? 'true' : 'false' ?>" aria-controls="userSubmenu">
                    <div><i class="fas fa-users-cog"></i> จัดการผู้ใช้งาน</div>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="collapse <?= $isUserMenu ? 'show' : '' ?>" id="userSubmenu">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item"><a href="<?= base_url('admin/users/admins') ?>" class="nav-link <?= (strpos($currentUrl, 'users/admins') !== false) ? 'active-sub' : '' ?>">• Admins (ผู้ดูแล)</a></li>
                        <li class="nav-item"><a href="<?= base_url('admin/users/vendors') ?>" class="nav-link <?= (strpos($currentUrl, 'users/vendors') !== false && strpos($currentUrl, 'pending') === false) ? 'active-sub' : '' ?>">• Vendors (เจ้าของ)</a></li>
                        <li class="nav-item"><a href="<?= base_url('admin/users/customers') ?>" class="nav-link <?= (strpos($currentUrl, 'users/customers') !== false) ? 'active-sub' : '' ?>">• Customers (ลูกค้า)</a></li>
                        <li class="nav-item"><a href="<?= base_url('admin/vendors/pending') ?>" class="nav-link <?= (strpos($currentUrl, 'vendors/pending') !== false) ? 'active-sub' : '' ?>">• อนุมัติ Vendor</a></li>
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
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // DataTables
            $('.table-datatable').DataTable({
                "language": { "search": "ค้นหา:", "lengthMenu": "แสดง _MENU_", "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_", "paginate": { "first": "หน้าแรก", "last": "หน้าสุดท้าย", "next": "ถัดไป", "previous": "ก่อนหน้า" }, "zeroRecords": "ไม่พบข้อมูล", "infoEmpty": "ไม่มีข้อมูล", "infoFiltered": "(กรองจาก _MAX_)" },
                "ordering": false
            });

            // Alerts
            <?php if(session()->getFlashdata('success')): ?>
                Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: '<?= session()->getFlashdata('success') ?>', confirmButtonColor: '#14b8a6', timer: 3000, timerProgressBar: true });
            <?php endif; ?>
            <?php if(session()->getFlashdata('error')): ?>
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: '<?= session()->getFlashdata('error') ?>', confirmButtonColor: '#ef4444' });
            <?php endif; ?>

            // Confirm Delete
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault(); 
                const href = $(this).attr('href'); 
                Swal.fire({ title: 'คุณแน่ใจหรือไม่?', text: "ข้อมูลที่ลบจะไม่สามารถกู้คืนได้!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'ใช่, ลบเลย!', cancelButtonText: 'ยกเลิก' }).then((result) => { if (result.isConfirmed) { window.location.href = href; } });
            });
        });
    </script>

    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"> 
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-header border-0 p-0">
                    <button type="button" class="btn-close btn-close-white ms-auto mb-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center d-flex justify-content-center align-items-center" style="min-height: 200px;">
                    <img src="" id="previewImage" class="img-fluid rounded shadow-lg" style="max-height: 90vh; max-width: 100%; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script สำหรับจับคลิกรูปภาพที่มี class "img-zoomable"
        document.addEventListener('DOMContentLoaded', function() {
            const zoomImages = document.querySelectorAll('.img-zoomable');
            const modalImage = document.getElementById('previewImage');
            const modalElement = document.getElementById('imagePreviewModal');

            if (modalElement && zoomImages.length > 0) {
                const imageModal = new bootstrap.Modal(modalElement);

                zoomImages.forEach(img => {
                    img.style.cursor = 'zoom-in';
                    img.classList.add('shadow-sm'); 

                    img.addEventListener('click', function() {
                        modalImage.src = this.src; 
                        imageModal.show(); 
                    });
                });
            }
        });
    </script>

</body>
</html>