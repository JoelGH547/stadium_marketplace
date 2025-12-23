<!-- สนามย่อย -->
<div class="card card-mint p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="fw-bold">สนามย่อยทั้งหมด</h5>
        <button onclick="openAddSubfieldModal()" class="btn btn-success btn-sm">➕ เพิ่มสนามย่อย</button>
    </div>

    <?php if(empty($subfields)): ?>
        <p class="text-muted mt-3">ยังไม่มีสนามย่อย</p>

    <?php else: ?>
        <ul class="list-group mt-3">

            <?php foreach($subfields as $sf): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">

                        <?php 
                            // images ใน DB เป็น JSON เช่น ["abc.jpg","def.jpg"]
                            $imgList = json_decode($sf['outside_images'] ?? '[]', true);

                            // ดึงรูปแรก ถ้ามี
                            $firstImg = (!empty($imgList) && isset($imgList[0]))
                                ? $imgList[0]
                                : 'no-image.png';

                            $imgPath = base_url('uploads/subfields/' . $firstImg);
                        ?>

                        <!-- รูปสนามย่อย -->
                        <img src="<?= $imgPath ?>" 
                            class="rounded me-3" 
                            style="width: 70px; height: 70px; object-fit: cover;">


                        <div>
                            <a href="javascript:void(0)" onclick="openSubfieldModal(<?= $sf['id'] ?>)" class="text-decoration-none text-dark">
                                <h5 class="fw-bold mb-1"><?= esc($sf['name']) ?></h5>
                            </a>
                            <small class="text-muted"><?= esc($sf['price']) ?> บาท/ชั่วโมง</small>
                        </div>
                </div>

                    <div>

                        <!-- ปุ่มเปิด/ปิดสถานะ -->
                        <?php if($sf['status'] === 'active'): ?>
                            <a href="<?= base_url('owner/fields/subfields/toggle/'.$stadium['id'].'/'.$sf['id']) ?>"
                                class="btn btn-success btn-sm me-2">
                                ✓ เปิดทำการ
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('owner/fields/subfields/toggle/'.$stadium['id'].'/'.$sf['id']) ?>"
                                class="btn btn-secondary btn-sm me-2">
                                ✗ ปิดปรับปรุง
                            </a>
                        <?php endif; ?>
                        
                        <!-- ปุ่มแก้ไข (ใน Popup) -->
                        <a href="javascript:void(0)" onclick="openSubfieldModal(<?= $sf['id'] ?>)" class="btn btn-warning btn-sm me-2">
                            ✏️ รายละเอียด
                        </a>

                        <a href="javascript:void(0)" onclick="openManageItemsModal(<?= $sf['id'] ?>)" class="btn btn-info btn-sm text-white me-2">
                            <i class="fas fa-boxes me-1"></i> จัดการสินค้า
                        </a>

                        <a href="javascript:void(0)" onclick="openSubfieldCalendar(<?= $sf['id'] ?>, '<?= esc($sf['name']) ?>')" class="btn btn-outline-primary btn-sm me-2">
                            <i class="fas fa-calendar-alt me-1"></i> ตารางจอง
                        </a>

                        <!-- ปุ่มลบ -->
                        <a href="<?= base_url('owner/fields/subfields/'.$stadium['id'].'/delete/'.$sf['id']) ?>"
                        class="btn btn-outline-danger btn-sm"
                        onclick="return confirm('ลบสนามย่อยนี้?')">
                            ลบ
                        </a>

                    </div>

                </li>
            <?php endforeach; ?>

        </ul>
    <?php endif; ?>
</div>


