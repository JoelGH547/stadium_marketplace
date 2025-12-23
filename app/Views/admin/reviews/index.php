<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold"><?= esc($title) ?></h1>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list me-2"></i>รายการรีวิวจากลูกค้า</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="reviewTable">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">ID</th>
                            <th>สนาม</th>
                            <th data-searchable="false">ลูกค้า</th>
                            <th width="120" data-searchable="false">คะแนน</th>
                            <th data-searchable="false">ข้อความรีวิว</th>
                            <th width="150" data-searchable="false">วันที่</th>
                            <th width="100" data-searchable="false">สถานะ</th>
                            <th width="150" data-searchable="false">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review) : ?>
                            <tr>
                                <td><?= $review['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($review['stadium_name']) ?></div>
                                </td>
                                <td><?= esc($review['customer_name']) ?></td>
                                <td>
                                    <div class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <i class="fas fa-star <?= $i <= $review['rating'] ? '' : 'text-muted opacity-25' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-muted">(<?= $review['rating'] ?>/5)</small>
                                </td>
                                <td>
                                    <div class="text-wrap" style="max-width: 300px;">
                                        <?= esc($review['comment']) ?>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt me-1"></i> <?= date('d/m/Y', strtotime($review['created_at'])) ?><br>
                                        <i class="far fa-clock me-1"></i> <?= date('H:i', strtotime($review['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($review['status'] == 'published') : ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2">
                                            <i class="fas fa-check-circle me-1"></i> แสดงผล
                                        </span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2">
                                            <i class="fas fa-eye-slash me-1"></i> ถูกซ่อน
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('admin/reviews/toggle/' . $review['id']) ?>" 
                                           class="btn btn-outline-<?= $review['status'] == 'published' ? 'warning' : 'success' ?> btn-sm"
                                           title="<?= $review['status'] == 'published' ? 'ซ่อนรีวิว' : 'แสดงรีวิว' ?>">
                                            <i class="fas <?= $review['status'] == 'published' ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                        </a>
                                        <a href="<?= base_url('admin/reviews/delete/' . $review['id']) ?>" 
                                           class="btn btn-outline-danger btn-sm btn-delete" 
                                           title="ลบรีวิว">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables for reviewTable manually (removing .table-datatable class above prevents auto-init)
    var table = $('#reviewTable').DataTable({
        "language": { 
            "search": "ค้นหา (ID หรือชื่อสนาม):", 
            "lengthMenu": "แสดง _MENU_", 
            "info": "แสดง _START_ ถึง _END_ จาก _TOTAL_", 
            "paginate": { "first": "หน้าแรก", "last": "หน้าสุดท้าย", "next": "ถัดไป", "previous": "ก่อนหน้า" }, 
            "zeroRecords": "ไม่พบข้อมูล", 
            "infoEmpty": "ไม่มีข้อมูล", 
            "infoFiltered": "(กรองจาก _MAX_)" 
        },
        "ordering": false
    });

    // Custom "Starts With" filter logic
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            // Apply only to this specific table
            if (settings.nTable.id !== 'reviewTable') return true;
            
            var searchTerm = table.search().toLowerCase().trim();
            if (!searchTerm) return true;

            // Column 0 is ID, Column 1 is Stadium Name
            var idValue = data[0].toLowerCase().trim();
            var stadiumName = data[1].toLowerCase().trim();

            // Match if either column STARTS WITH the search term
            return idValue.indexOf(searchTerm) === 0 || stadiumName.indexOf(searchTerm) === 0;
        }
    );

    // Filter redraw is already triggered by DataTables default search box
});
</script>
<?= $this->endSection() ?>
