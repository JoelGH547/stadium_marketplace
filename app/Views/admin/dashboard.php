<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<style>
    .stat-card {
        border: none;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        border-bottom: 4px solid var(--mint-primary);
    }
    .stat-icon-box {
        width: 56px; height: 56px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
    }
    .bg-mint-light { background-color: #ccfbf1; color: #0f766e; }
    .bg-blue-light { background-color: #dbeafe; color: #1e40af; }
    .bg-orange-light { background-color: #ffedd5; color: #c2410c; }
    .bg-purple-light { background-color: #f3e8ff; color: #7e22ce; }
    
    .card-title-text { color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; }
    .card-value-text { color: #1e293b; font-size: 2.2rem; font-weight: 700; line-height: 1.2; }
    
    /* Table Styles */
    .table-custom thead th {
        background-color: #f8fafc; color: #64748b; font-weight: 600; font-size: 0.85rem; border-top: none; padding: 1rem;
    }
    .table-custom tbody td { padding: 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
    .table-hover tbody tr:hover { background-color: #f0fdfa; }
</style>

<div class="container-fluid p-0">
    
    <div class="mb-4 d-flex justify-content-between align-items-end">
        <div>
            <h3 class="fw-bold text-dark mb-1">ภาพรวมระบบ</h3>
            <p class="text-muted mb-0 small">ข้อมูลอัปเดตล่าสุด: <?= date('d M Y, H:i') ?></p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-4 d-flex align-items-center justify-content-between">
                <div>
                    <div class="card-title-text mb-2">Vendors รออนุมัติ</div>
                    <div class="card-value-text text-warning"><?= number_format($pendingCount) ?></div> 
                    <a href="<?= base_url('admin/vendors/pending') ?>" class="btn btn-sm btn-outline-warning mt-3 rounded-pill px-3">
                        ตรวจสอบทันที
                    </a>
                </div>
                <div class="stat-icon-box bg-orange-light"><i class="fas fa-user-clock"></i></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-4 d-flex align-items-center justify-content-between">
                <div>
                    <div class="card-title-text mb-2">ลูกค้าใหม่ (24 ชม.)</div>
                    <div class="card-value-text text-primary"><?= number_format($newCustomerCount) ?></div>
                    <a href="<?= base_url('admin/users/new_customers') ?>" class="text-decoration-none small text-muted mt-2 d-block">
                        ดูรายชื่อ <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="stat-icon-box bg-purple-light"><i class="fas fa-user-plus"></i></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-4 d-flex align-items-center justify-content-between">
                <div>
                    <div class="card-title-text mb-2">ยอดจอง (วันนี้)</div>
                    <div class="card-value-text">0</div>
                    <span class="badge bg-light text-muted mt-2">Coming Soon</span>
                </div>
                <div class="stat-icon-box bg-blue-light"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card p-4 d-flex align-items-center justify-content-between">
                <div>
                    <div class="card-title-text mb-2">สนามทั้งหมด</div>
                    <div class="card-value-text text-success"><?= number_format($stadiumCount) ?></div>
                    <a href="<?= base_url('admin/stadiums') ?>" class="text-decoration-none small text-muted mt-2 d-block">
                        จัดการสนาม <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="stat-icon-box bg-mint-light"><i class="fas fa-map-location-dot"></i></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-store me-2 text-warning"></i>Vendors ล่าสุด</h6>
                    <a href="<?= base_url('admin/users/vendors') ?>" class="btn btn-sm btn-light rounded-pill px-3">ดูทั้งหมด</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0">
                        <thead><tr><th>ชื่อร้าน/สนาม</th><th>สถานะ</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentVendors as $v): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= esc($v['vendor_name']) ?></td>
                                <td>
                                    <?php if(($v['status'] ?? '') == 'pending'): ?>
                                        <span class="badge bg-warning text-dark rounded-pill">รออนุมัติ</span>
                                    <?php else: ?>
                                        <span class="badge bg-success rounded-pill">อนุมัติแล้ว</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0 text-dark"><i class="fas fa-users me-2 text-primary"></i>ลูกค้าล่าสุด</h6>
                    <a href="<?= base_url('admin/users/customers') ?>" class="btn btn-sm btn-light rounded-pill px-3">ดูทั้งหมด</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0">
                        <thead><tr><th>ชื่อลูกค้า</th><th>เบอร์โทร</th></tr></thead>
                        <tbody>
                            <?php foreach ($recentCustomers as $c): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center me-2" style="width:32px; height:32px;">
                                            <i class="fas fa-user text-secondary small"></i>
                                        </div>
                                        <span class="fw-bold text-dark"><?= esc($c['full_name']) ?></span>
                                    </div>
                                </td>
                                <td class="text-muted"><?= esc($c['phone_number'] ?? '-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>