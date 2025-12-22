<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title) ?></h1>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายการรีวิวจากลูกค้า</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>สนาม</th>
                            <th>ลูกค้า</th>
                            <th>คะแนน</th>
                            <th>ข้อความรีวิว</th>
                            <th>วันที่</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review) : ?>
                            <tr>
                                <td><?= $review['id'] ?></td>
                                <td><?= esc($review['stadium_name']) ?></td>
                                <td><?= esc($review['customer_name']) ?></td>
                                <td>
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-gray-300' ?>"></i>
                                    <?php endfor; ?>
                                </td>
                                <td><?= esc($review['comment']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></td>
                                <td>
                                    <span class="badge badge-<?= $review['status'] == 'approved' ? 'success' : 'secondary' ?>">
                                        <?= $review['status'] == 'approved' ? 'แสดงผลปกติ' : 'ถูกซ่อนไว้' ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/reviews/toggle/' . $review['id']) ?>" 
                                       class="btn btn-<?= $review['status'] == 'approved' ? 'warning' : 'success' ?> btn-sm">
                                        <i class="fas <?= $review['status'] == 'approved' ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                        <?= $review['status'] == 'approved' ? 'ซ่อน' : 'แสดง' ?>
                                    </a>
                                    <a href="<?= base_url('admin/reviews/delete/' . $review['id']) ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('ยืนยันการลบรีวิวนี้ถาวร?')">
                                        <i class="fas fa-trash"></i> ลบ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
