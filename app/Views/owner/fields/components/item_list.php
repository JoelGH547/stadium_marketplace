<!-- บริการ/สินค้าเพิ่มเติม -->
<div class="card card-mint p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="fw-bold">บริการและสินค้าเพิ่มเติม</h5>
        <button onclick="openAddItemModal()" class="btn btn-success btn-sm">➕ เพิ่มบริการ/สินค้า</button>
    </div>

    <?php if(empty($items)): ?>
        <p class="text-muted mt-3">ยังไม่มีบริการหรือสินค้าเพิ่มเติม</p>

    <?php else: ?>
        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">รูป</th>
                        <th>ชื่อบริการ</th>
                        <th>สนาม</th>
                        <th>ราคา</th>
                        <th>สถานะ</th>
                        <th class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                        <?php 
                            $img = !empty($item['image']) 
                                ? base_url('uploads/items/'.$item['image'])
                                : base_url('uploads/no-image.png');
                                
                            $fieldName = ($item['field_name'] === '_SYSTEM_CATALOG_') ? 'ส่วนกลาง' : esc($item['field_name']);
                            
                            $statusClass = ($item['status'] === 'active') ? 'bg-success' : 'bg-secondary';
                            $statusLabel = ($item['status'] === 'active') ? 'ใช้งาน' : 'ระงับ';
                        ?>
                        <tr>
                            <td>
                                <img src="<?= $img ?>" class="rounded bg-light border" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($item['name']) ?></div>
                                <div class="small text-muted"><?= esc($item['type_name']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-normal">
                                    <i class="fas fa-map-marker-alt me-1 text-mint"></i> <?= $fieldName ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-mint"><?= number_format($item['price']) ?></span> 
                                <span class="small text-muted">/ <?= esc($item['unit']) ?></span>
                            </td>
                            <td>
                                <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                            </td>
                            <td class="text-end">
                                <a href="javascript:void(0)" onclick="openItemModal(<?= $item['id'] ?>)" class="btn btn-sm btn-warning mb-1">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a href="<?= base_url('owner/items/delete/'.$item['id']) ?>" 
                                   class="btn btn-sm btn-outline-danger mb-1" 
                                   onclick="return confirm('ยืนยันลบรายการนี้?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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
