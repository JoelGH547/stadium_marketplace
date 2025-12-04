<!-- บริการ/สินค้าเพิ่มเติม -->
<div class="card card-mint p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="fw-bold">บริการและสินค้าเพิ่มเติม</h5>
        <a href="<?= base_url('owner/items/add/'.$stadium['id']) ?>" 
        class="btn btn-success btn-sm">➕ เพิ่มบริการ/สินค้า</a>
    </div>

    <?php if(empty($items)): ?>
        <p class="text-muted mt-3">ยังไม่มีบริการหรือสินค้าเพิ่มเติม</p>

    <?php else: ?>
        <ul class="list-group mt-3">

            <?php foreach($items as $item): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center">

                        <?php 
                            $img = !empty($item['image']) 
                                ? base_url('uploads/items/'.$item['image'])
                                : base_url('uploads/no-image.png');
                        ?>

                        <img src="<?= $img ?>" 
                            class="rounded me-3"
                            style="width: 60px; height: 60px; object-fit: cover;">

                        <div>
                            <strong><?= esc($item['name']) ?></strong>
                            <br>
                            <small class="text-muted">
                                <?= esc($item['price']) ?> บาท / <?= esc($item['unit']) ?>
                            </small>
                            <br>
                            <span class="badge bg-info">
                                <?= esc($item['type_name']) ?>
                            </span>
                        </div>

                    </div>

                    <div>
                        <a href="javascript:void(0)" onclick="openItemModal(<?= $item['id'] ?>)" 
                        class="btn btn-warning btn-sm me-2">✏️ แก้ไข</a>

                        <a href="<?= base_url('owner/items/delete/'.$item['id']) ?>" 
                        class="btn btn-outline-danger btn-sm"
                        onclick="return confirm('ลบรายการนี้?')">
                        ลบ
                        </a>
                    </div>

                </li>
            <?php endforeach; ?>

        </ul>
    <?php endif; ?>
</div>

<!-- Modal Item Details -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="itemModalLabel">รายละเอียดสินค้า/บริการ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <!-- VIEW MODE -->
        <div id="itemViewMode">
            <div class="row">
                <div class="col-md-5 text-center">
                    <div id="itemImageDisplay"></div>
                </div>
                <div class="col-md-7">
                    <h4 id="itemNameDisplay" class="fw-bold"></h4>
                    <span id="itemTypeDisplay" class="badge bg-secondary mb-2"></span>
                    <h5 class="text-success"><span id="itemPriceDisplay"></span> บาท / <span id="itemUnitDisplay"></span></h5>
                    <p class="text-muted mt-3" id="itemDescDisplay"></p>
                </div>
            </div>
        </div>

        <!-- EDIT MODE -->
        <div id="itemEditMode" style="display: none;">
            <form id="editItemForm" enctype="multipart/form-data">
                <input type="hidden" name="item_id" id="editItemId">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ชื่อสินค้า/บริการ</label>
                        <input type="text" name="name" id="editItemName" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">หมวดหมู่</label>
                        <select name="type_id" id="editItemType" class="form-select" required>
                            <!-- Options loaded via JS -->
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ราคา (บาท)</label>
                        <input type="number" name="price" id="editItemPrice" class="form-control" required min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">หน่วยนับ (เช่น ขวด, ลูก, ชั่วโมง)</label>
                        <input type="text" name="unit" id="editItemUnit" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">รายละเอียด</label>
                    <textarea name="description" id="editItemDesc" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">รูปภาพ (เปลี่ยนใหม่)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </form>
        </div>

      </div>
      <div class="modal-footer">
        <!-- View Footer -->
        <div id="itemViewFooter" class="w-100 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            <button type="button" class="btn btn-warning" onclick="toggleItemEditMode(true)">✏️ แก้ไขข้อมูล</button>
        </div>
        <!-- Edit Footer -->
        <div id="itemEditFooter" class="w-100 d-flex justify-content-between" style="display: none;">
            <button type="button" class="btn btn-secondary" onclick="toggleItemEditMode(false)">ยกเลิก</button>
            <button type="button" class="btn btn-success" onclick="saveItem()">บันทึกการเปลี่ยนแปลง</button>
        </div>
      </div>
    </div>
  </div>
</div>
