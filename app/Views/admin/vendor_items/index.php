<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<style>
    .filter-group label { font-size: 0.85rem; font-weight: bold; color: #6c757d; margin-bottom: 5px; }
</style>

<div class="container-fluid p-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h3 mb-0 text-gray-800">จัดการสินค้า/บริการเสริม (Vendor Items)</h3>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-3 border-0">
        <div class="card-body bg-light py-3">
            <div class="row g-3">
                <div class="col-md-12 mb-2">
                    <span class="text-primary fw-bold"><i class="fas fa-filter"></i> ตัวกรองข้อมูล (Filters)</span>
                </div>
                
                <div class="col-md-4 filter-group">
                    <label>กรองตามกีฬา:</label>
                    <select id="filter-sport" class="form-select form-select-sm shadow-sm">
                        <option value="">ทั้งหมด</option>
                    </select>
                </div>

                <div class="col-md-4 filter-group">
                    <label>กรองตามหมวดหมู่:</label>
                    <select id="filter-category" class="form-select form-select-sm shadow-sm">
                        <option value="">ทั้งหมด</option>
                    </select>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow mb-4 border-0">
        <div class="card-body">
            <div class="table-responsive">
                
                <table class="table table-hover align-middle" id="vendorItemsTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="8%">รูปภาพ</th>
                            <th width="15%">ชื่อสินค้า</th>
                            <th width="15%">รายละเอียด</th>
                            <th width="8%">ราคา</th>
                            <th width="15%">ชื่อสนาม</th>
                            <th width="10%">กีฬา</th>     <th width="12%">หมวดหมู่</th>  <th width="7%" class="text-center">สถานะ</th>
                            <th width="5%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)) : ?>
                            <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?= $item['id'] ?></td>
                                
                                <td>
                                    <?php if (!empty($item['image'])) : ?>
                                        <img src="<?= base_url('assets/uploads/items/' . $item['image']) ?>" 
                                             class="rounded shadow-sm" 
                                             style="width: 45px; height: 45px; object-fit: cover;">
                                    <?php else : ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted small" style="width: 45px; height: 45px;">No Pic</div>
                                    <?php endif; ?>
                                </td>

                                <td class="fw-bold"><?= esc($item['name']) ?></td>
                                
                                <td>
                                    <div class="text-muted small text-truncate" style="max-width: 150px;">
                                        <?= esc($item['description']) ?>
                                    </div>
                                </td>

                                <td class="text-success fw-bold">฿<?= number_format($item['price'], 0) ?></td>

                                <td>
                                    <span class="text-dark fw-bold small"><?= esc($item['stadium_name']) ?></span>
                                </td>

                                <td>
                                    <?php if(!empty($item['sport_name'])): ?>
                                        <span class="badge bg-info text-dark bg-opacity-10 border border-info">
                                            <?= esc($item['sport_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if(!empty($item['facility_type_name'])): ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark border">
                                            <?= esc($item['facility_type_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <?php if ($item['is_active'] ?? true): ?>
                                        <span class="badge bg-success rounded-pill">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill">Inactive</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a href="<?= base_url('admin/vendor_items/delete/' . $item['id']) ?>" 
                                       class="btn btn-outline-danger btn-sm btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#vendorItemsTable').DataTable({
            "searching": true, // เปิดระบบค้นหาไว้ (แต่ซ่อนกล่องด้วย dom)
            "dom": 'lrtip',    // l=length, r=processing, t=table, i=info, p=pagination (ซ่อน f=filter กล่องค้นหาหลัก)
            "lengthMenu": [ [10, 50, 100, -1], [10, 50, 100, "ทั้งหมด"] ],
            "language": {
                "lengthMenu": "แสดง _MENU_ รายการ",
                "zeroRecords": "ไม่พบข้อมูล",
                "info": "หน้า _PAGE_ จาก _PAGES_",
                "infoEmpty": "ไม่มีข้อมูล",
                "infoFiltered": "(กรองจาก _MAX_ รายการ)",
                "paginate": { "first": "หน้าแรก", "last": "สุดท้าย", "next": "ถัดไป", "previous": "ก่อนหน้า" }
            },
            "columnDefs": [
                { "orderable": false, "targets": [1, 9] }, // ห้ามเรียงรูปภาพและปุ่มจัดการ
                {
                    // [สำคัญ] กำหนดให้คอลัมน์ กีฬา(6) และ หมวดหมู่(7) เวลาค้นหาหรือเรียงลำดับ ให้มองแค่ "ข้อความ" ไม่เอา HTML Tags
                    "targets": [6, 7],
                    "render": function ( data, type, row ) {
                        if ( type === 'filter' || type === 'sort' ) {
                            // ลบ tags html ออก (เช่น <span...>) แล้วตัดช่องว่างหน้าหลัง
                            return data.replace(/<[^>]+>/g, "").trim();
                        }
                        return data; // ถ้าเป็น type 'display' ให้แสดง HTML Badge สวยๆ เหมือนเดิม
                    }
                }
            ],
            
            // ฟังก์ชันสร้างตัวเลือกใน Dropdown
            "initComplete": function () {
                // ระบุคอลัมน์ที่จะทำ Filter: index 6 (กีฬา) และ 7 (หมวดหมู่)
                this.api().columns([6, 7]).every(function () {
                    var column = this;
                    // เลือก ID ของ Select Box ให้ตรงกับคอลัมน์
                    var selectId = (column.index() == 6) ? '#filter-sport' : '#filter-category';
                    var select = $(selectId);
                    
                    // เคลียร์ค่าเก่าออกก่อน (เผื่อมีการ reload)
                    select.find('option:not(:first)').remove();

                    // ดึงข้อมูลทั้งหมดในคอลัมน์มาวนลูป
                    var uniqueData = [];
                    column.data().each(function (d, j) {
                        // แกะ HTML ออกให้เหลือแต่ชื่อเพียวๆ
                        var text = d ? d.replace(/<[^>]+>/g, "").trim() : '';
                        
                        // เช็คว่ามีข้อมูล, ไม่ใช่ขีด, และยังไม่เคยเก็บลง array
                        if (text && text !== '-' && !uniqueData.includes(text)) {
                            uniqueData.push(text);
                        }
                    });

                    // เรียงลำดับ ก-ฮ
                    uniqueData.sort();

                    // สร้าง <option> ยัดใส่ Dropdown
                    uniqueData.forEach(function(val) {
                        select.append('<option value="' + val + '">' + val + '</option>');
                    });

                    // เมื่อมีการเลือก Dropdown
                    select.on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        // สั่งค้นหาแบบ Exact Match (^...$)
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });
                });
            }
        });
    });

    // ส่วน Delete (เหมือนเดิม)
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); 
                const href = this.getAttribute('href'); 
                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: "คุณต้องการลบรายการนี้ใช่หรือไม่?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) window.location.href = href;
                });
            });
        });
    });
</script>

<?= $this->endSection() ?>