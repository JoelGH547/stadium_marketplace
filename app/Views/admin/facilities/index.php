<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
    // 1. ดึงข้อมูลการจับคู่จาก Database
    $db = \Config\Database::connect();
    $mappings = $db->table('facility_categories')->get()->getResultArray();
    
    // 2. สร้าง Map เพื่อเช็คว่า (Facility + Category) มีอยู่จริงไหม
    // Key format: "facilityID_categoryID"
    $checkedMap = [];
    foreach($mappings as $m) {
        if ($m['category_id'] !== null) {
            $checkedMap[$m['facility_id'] . '_' . $m['category_id']] = true;
        }
    }
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">จัดการสิ่งอำนวยความสะดวก (Facilities)</h1>
    <a href="<?= base_url('admin/facilities/create') ?>" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus"></i> เพิ่มรายการใหม่
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">ตารางตรวจสอบสิ่งอำนวยความสะดวก</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th class="align-middle text-left bg-light" style="min-width: 180px; position: sticky; left: 0; z-index: 2;">
                            ประเภทกีฬา \ สิ่งของ
                        </th>
                        
                        <?php foreach ($facilities as $fac) : ?>
                            <th class="align-middle position-relative pt-4" style="min-width: 120px;">
                                
                                <div class="position-absolute bg-light rounded px-1 border" style="top: 5px; right: 5px; z-index: 10;">
                                    <a href="<?= base_url('admin/facilities/edit/' . $fac['id']) ?>" 
                                       class="text-warning mr-2 text-decoration-none" 
                                       title="แก้ไข">
                                        <i class="fas fa-pen fa-xs"></i>
                                    </a>
                                    
                                    <a href="javascript:void(0);" 
                                       class="text-danger text-decoration-none btn-delete-custom" 
                                       data-url="<?= base_url('admin/facilities/delete/' . $fac['id']) ?>"
                                       data-name="<?= esc($fac['name']) ?>"
                                       title="ลบ">
                                        <i class="fas fa-times fa-xs"></i>
                                    </a>
                                </div>

                                <div class="d-flex flex-column align-items-center mt-2">
                                    <?php if($fac['icon']): ?>
                                        <i class="<?= $fac['icon'] ?> mb-1 text-secondary"></i>
                                    <?php endif; ?>
                                    <span class="small font-weight-bold text-dark"><?= esc($fac['name']) ?></span>
                                </div>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php foreach ($categories as $cat) : ?>
                        <tr>
                            <td class="text-left font-weight-bold bg-white" style="position: sticky; left: 0; z-index: 1;">
                                <span class="mr-1"><?= $cat['emoji'] ?></span> <?= esc($cat['name']) ?>
                            </td>

                            <?php foreach ($facilities as $fac) : ?>
                                <?php 
                                    $isChecked = isset($checkedMap[$fac['id'] . '_' . $cat['id']]);
                                ?>
                                <td class="clickable-cell" 
                                    data-fac-id="<?= $fac['id'] ?>" 
                                    data-cat-id="<?= $cat['id'] ?>"
                                    style="cursor: pointer; transition: 0.2s;">
                                    
                                    <i class="<?= $isChecked ? 'fas fa-check-circle text-primary' : 'far fa-circle text-gray-300' ?> fa-lg icon-status"></i>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // ============================================================
        // 1. จัดการการติ๊กเลือก Matrix (Toggle Checkbox Real-time)
        // ============================================================
        const cells = document.querySelectorAll('.clickable-cell');

        cells.forEach(cell => {
            cell.addEventListener('click', function() {
                const facId = this.dataset.facId;
                const catId = this.dataset.catId;
                const icon = this.querySelector('.icon-status');

                // UI UPDATE (เปลี่ยนไอคอนทันที)
                if (icon.classList.contains('fa-check-circle')) {
                    // ถ้ามีแล้ว -> เอาออก (สีเทา)
                    icon.className = 'far fa-circle text-gray-300 fa-lg icon-status';
                } else {
                    // ถ้ายังไม่มี -> ใส่เข้าไป (สีฟ้า)
                    icon.className = 'fas fa-check-circle text-primary fa-lg icon-status';
                }

                // SERVER UPDATE (ส่ง AJAX)
                fetch('<?= base_url('admin/facilities/ajax_update') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        facility_id: facId,
                        category_id: catId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status !== 'success') {
                        // ถ้า Error ให้แจ้งเตือนและรีโหลด
                        Swal.fire('Error', data.message || 'เกิดข้อผิดพลาด', 'error');
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });

        // ============================================================
        // 2. จัดการ SweetAlert สำหรับปุ่มลบ (ใช้ class btn-delete-custom)
        // ============================================================
        const deleteButtons = document.querySelectorAll('.btn-delete-custom');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // ห้ามลิ้งค์ทำงานเอง
                
                const deleteUrl = this.getAttribute('data-url');
                const itemName = this.getAttribute('data-name');

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: `คุณต้องการลบ "${itemName}" ใช่หรือไม่?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // สั่ง Redirect ไปยัง URL ลบ
                        window.location.href = deleteUrl;
                    }
                });
            });
        });
        
        // เพิ่ม CSS เล็กน้อยตอนเอาเมาส์ชี้ช่องตาราง
        const style = document.createElement('style');
        style.innerHTML = `.clickable-cell:hover { background-color: #f1f3f9; }`;
        document.head.appendChild(style);
    });
</script>

<?= $this->endSection() ?>