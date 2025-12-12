<!-- Header Navbar -->
<nav class="navbar navbar-expand-lg" style="background:#00c389; padding:12px 25px; color:white;">
    <div class="container-fluid">

        <!-- Logo กดแล้วกลับหน้า Dashboard -->
        <a class="navbar-brand fw-bold text-white" 
           href="<?= base_url('owner/dashboard') ?>">
            eBooking • Owner
        </a>

        <div>
            <span class="me-3">👋 สวัสดี <?= session()->get('owner_name') ?></span>
            <a href="<?= base_url('owner/logout') ?>" class="btn btn-light btn-sm">
                ออกจากระบบ
            </a>
        </div>
    </div>
</nav>
