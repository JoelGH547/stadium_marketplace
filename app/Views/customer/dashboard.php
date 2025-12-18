<?= $this->extend('layouts/customer') ?>

<?= $this->section('extra-css') ?>
<style>
    .stadium-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }
    .stadium-card {
        border-radius: 12px;
        overflow: hidden;
    }
    .stadium-card-image {
        width: 100%;
        height: 200px;
        background-color: #f8f9fa;
    }
    .category {
        display: inline-block;
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 font-weight-bold text-dark"><?= esc($title) ?></h1>
            <p class="text-muted">ยินดีต้อนรับ! เลือกสนามที่คุณต้องการจอง:</p>
        </div>
    </div>

    <div class="stadium-grid">
        <?php if (! empty($stadiums) && is_array($stadiums)): ?>
            <?php foreach ($stadiums as $stadium): ?>
                <div class="stadium-card shadow-sm border-0 h-100 bg-white">
                    <?php 
                        $images = json_decode($stadium['outside_images'] ?? '[]', true);
                        $cover = !empty($images[0]) ? $images[0] : null;
                    ?>
                    <div class="stadium-card-image position-relative">
                        <?php if($cover): ?>
                            <img src="<?= base_url('assets/uploads/stadiums/'.$cover) ?>" 
                                 class="w-100 h-100 object-fit-cover">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                <i class="fas fa-image fa-3x text-muted opacity-25"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-3">
                        <span class="category mb-2"><?= esc($stadium['category_name'] ?? 'General') ?></span>
                        
                        <h3 class="h5 fw-bold mb-2 text-dark"><?= esc($stadium['name']) ?></h3>
                        <p class="small text-muted mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= esc($stadium['description']) ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="price">
                                <span class="h5 mb-0 text-primary font-weight-bold">฿<?= number_format($stadium['price'] ?? 0, 0) ?></span> 
                                <small class="text-muted">/ชม.</small>
                            </div>
                        </div>
                        
                        <a href="<?= base_url('customer/booking/stadium/' . $stadium['id']) ?>" 
                           class="btn btn-primary btn-block py-2 fw-bold shadow-sm rounded-pill">
                            ดูรายละเอียด และ จอง
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <p class="lead text-muted">ขออภัย, ยังไม่มีสนามกีฬาที่เปิดให้จองในขณะนี้</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
