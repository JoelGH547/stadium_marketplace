<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ข้อมูลสนาม: <?= esc($stadium['name']) ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url('assets/vendors_css/owner_stadium_view.css') ?>" rel="stylesheet">
</head>

<body class="bg-light">
<?= $this->include('owner/layout/header') ?>
<?= $this->include('owner/layout/sidebarfields') ?>

<div id="dashboard-wrapper" class="dashboard-wrapper">

    <div class="container mt-4">
        
    <!-- Hero Banner -->
    <?= $this->include('owner/fields/components/hero') ?>

    <!-- ข้อมูลหลักสนาม -->
    <div class="card card-mint p-4 mb-4">
        <h5 class="fw-bold mb-3">ข้อมูลพื้นฐาน</h5>

        <p><strong>ประเภท:</strong> <?= esc($stadium['category_name']) ?></p>
        <p><strong>จังหวัด:</strong> <?= esc($stadium['province']) ?></p>

        <p><strong>เวลาเปิด:</strong> <?= esc($stadium['open_time']) ?> น.</p>
        <p><strong>เวลาปิด:</strong> <?= esc($stadium['close_time']) ?> น.</p>
        <p><strong>ที่อยู่:</strong> <?= esc($stadium['address']) ?></p>

        <a href="<?= base_url('owner/fields/edit/'.$stadium['id']) ?>" 
           class="btn btn-mint mt-3">✏️ แก้ไขข้อมูลสนาม</a>
    </div>



    <!-- Subfields List -->
    <?= $this->include('owner/fields/components/subfield_list') ?>



</div>

<!-- Modal: Add Subfield -->
<!-- Modal: Add Subfield -->
<div class="modal fade" id="addSubfieldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-mint text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> เพิ่มสนามย่อยใหม่</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <form id="addSubfieldForm" enctype="multipart/form-data">
                    
                    <!-- Basic Info -->
                    <div class="card p-3 border-0 shadow-sm mb-3">
                        <h6 class="fw-bold text-mint"><i class="fas fa-info-circle me-1"></i> ข้อมูลทั่วไป</h6>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">ชื่อสนาม <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="เช่น สนาม 1 (หญ้าเทียม)">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">สถานะ</label>
                                <select name="status" class="form-select">
                                    <option value="active">เปิดใช้งาน</option>
                                    <option value="maintenance">ปิดปรับปรุง</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รายละเอียด</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="รายละเอียดเพิ่มเติม..."></textarea>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="card p-3 border-0 shadow-sm mb-3">
                        <h6 class="fw-bold text-mint"><i class="fas fa-tags me-1"></i> ราคาค่าบริการ</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ราคาต่อชั่วโมง (บาท) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" required min="0" placeholder="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ราคาเหมาวัน (บาท)</label>
                                <input type="number" name="price_daily" class="form-control" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="card p-3 border-0 shadow-sm mb-3">
                        <h6 class="fw-bold text-mint"><i class="fas fa-images me-1"></i> รูปภาพ</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">รูปปก/ภายนอก</label>
                                <input type="file" name="outside_images[]" class="form-control" multiple accept="image/*">
                                <small class="text-muted">เลือกได้หลายรูป</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">รูปภายใน/สิ่งอำนวยความสะดวก</label>
                                <input type="file" name="inside_images[]" class="form-control" multiple accept="image/*">
                            </div>
                        </div>
                    </div>

                    <!-- Services & Items -->
                    <div class="card p-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                             <h6 class="fw-bold text-mint mb-0"><i class="fas fa-concierge-bell me-1"></i> บริการเสริมที่รองรับ</h6>
                             <button type="button" class="btn btn-sm btn-outline-success" onclick="toggleInlineItemForm()">
                                 <i class="fas fa-plus"></i> เพิ่มรายการใหม่
                             </button>
                        </div>

                        <!-- Inline Item Creator (Hidden by default) -->
                        <!-- Inline Item Creator (Hidden by default) -->
                        <div id="inlineItemCreator" class="bg-white p-3 border rounded mb-3 shadow-sm" style="display: none;">
                            <h6 class="small fw-bold text-success mb-2"><i class="fas fa-plus-circle"></i> สร้างสินค้า/บริการใหม่ด่วน</h6>
                            
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="small text-muted">ชื่อสินค้า *</label>
                                    <input type="text" id="inlineName" class="form-control form-control-sm" placeholder="เช่น น้ำดื่ม, ลูกบอล">
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">ประเภท *</label>
                                    <select id="inlineTypeId" class="form-select form-select-sm">
                                        <?php if(isset($facility_types)): ?>
                                            <?php foreach($facility_types as $ft): ?>
                                                <option value="<?= $ft['id'] ?>"><?= esc($ft['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="small text-muted">ราคา (บาท) *</label>
                                    <input type="number" id="inlinePrice" class="form-control form-control-sm" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">หน่วยนับ *</label>
                                    <input type="text" id="inlineUnit" class="form-control form-control-sm" placeholder="เช่น ชิ้น, ครั้ง, ชม.">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="small text-muted">รายละเอียด</label>
                                <textarea id="inlineDesc" class="form-control form-control-sm" rows="2" placeholder="รายละเอียดเพิ่มเติม..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted">รูปภาพสินค้า</label>
                                <input type="file" id="inlineImage" class="form-control form-control-sm" accept="image/*">
                            </div>

                            <button type="button" class="btn btn-success btn-sm w-100 fw-bold" onclick="saveInlineItem()">
                                <i class="fas fa-save me-1"></i> บันทึกสินค้า
                            </button>
                        </div>
                        <!-- End Inline Creator -->

                        <div class="bg-white border rounded p-2" style="max-height: 150px; overflow-y: auto;" id="subfieldItemsContainer">
                            <?php if(empty($items)): ?>
                                <p class="text-muted small text-center mb-0 mt-2" id="noItemsMsg">ยังไม่มีสินค้าในระบบ</p>
                            <?php else: ?>
                                <div class="row g-2" id="itemsCheckboxList">
                                    <?php foreach($items as $item): ?>
                                        <div class="col-md-6 item-checkbox-wrapper">
                                            <label class="d-flex align-items-center gap-2 p-2 border rounded bg-light hover-shadow pointer w-100">
                                                <input type="checkbox" name="items[]" value="<?= $item['id'] ?>" class="form-check-input">
                                                <div class="text-truncate">
                                                    <span class="fw-bold small"><?= esc($item['name']) ?></span>
                                                    <span class="text-muted small">(<?= number_format($item['price']) ?>)</span>
                                                </div>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-mint fw-bold" onclick="submitAddSubfield()">
                    <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Subfield Detail & Edit -->
<div class="modal fade" id="subfieldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-mint text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i> รายละเอียดสนามย่อย</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <input type="hidden" id="currentSubfieldId">

                <!-- VIEW MODE -->
                <div id="viewMode">
                    <!-- Hero/Header Info -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="fw-bold mb-1" id="subfieldName">Loading...</h4>
                                    <span id="subfieldStatus" class="badge bg-secondary">Loading...</span>
                                </div>
                                <div class="text-end">
                                    <h5 class="text-mint fw-bold mb-0"><span id="subfieldPrice">0</span> บาท/ชม.</h5>
                                    <small class="text-muted" id="subfieldPriceDailyWrapper"></small>
                                </div>
                            </div>
                            <hr>
                            <p class="text-muted" id="subfieldDesc">-</p>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white fw-bold text-mint"><i class="fas fa-images me-1"></i> รูปภาพ</div>
                        <div class="card-body">
                            <div id="subfieldImages" class="text-center"></div>
                        </div>
                    </div>

                    <!-- Facilities / Items -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white fw-bold text-mint"><i class="fas fa-concierge-bell me-1"></i> บริการเสริม</div>
                        <div class="card-body">
                            <div id="facilityList">
                                <div id="subfieldItemsList" class="row g-2"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EDIT MODE -->
                <div id="editMode" style="display: none;">
                    <form id="editSubfieldForm" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="editSubfieldId">
                        
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">ชื่อสนาม</label>
                                    <input type="text" name="name" id="editName" class="form-control" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">ราคาต่อชั่วโมง</label>
                                        <input type="number" name="price" id="editPrice" class="form-control" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">ราคาเหมาวัน</label>
                                        <input type="number" name="price_daily" id="editPriceDaily" class="form-control" placeholder="0.00">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">สถานะ</label>
                                        <select name="status" id="editStatus" class="form-select">
                                            <option value="active">ใช้งานปกติ</option>
                                            <option value="maintenance">ปิดปรับปรุง</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">รายละเอียด</label>
                                    <textarea name="description" id="editDesc" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white fw-bold"><i class="fas fa-images me-1"></i> จัดการรูปภาพ</div>
                            <div class="card-body">
                                <div class="row">
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label text-mint small fw-bold">ภายนอก</label>
                                         <div class="row g-2 mb-2" id="editExistingOutsideImages"></div>
                                         <input type="file" name="outside_images[]" class="form-control form-control-sm" multiple accept="image/*">
                                         <small class="text-muted" style="font-size: 0.75rem;">เพิ่มรูปภายนอกใหม่</small>
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label text-mint small fw-bold">ภายใน</label>
                                         <div class="row g-2 mb-2" id="editExistingInsideImages"></div>
                                         <input type="file" name="inside_images[]" class="form-control form-control-sm" multiple accept="image/*">
                                         <small class="text-muted" style="font-size: 0.75rem;">เพิ่มรูปภายในใหม่</small>
                                     </div>
                                </div>
                            </div>
                        </div>


                    </form>
                </div>

            </div>
            <div class="modal-footer" id="itemViewFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-warning text-white" id="btnEditSubfield" onclick="toggleEditMode(true)">
                    <i class="fas fa-edit me-1"></i> แก้ไข
                </button>
                <button type="button" class="btn btn-mint text-white" id="btnSaveSubfield" style="display: none;" onclick="saveSubfield()">
                    <i class="fas fa-save me-1"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Item (Catalog) -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">เพิ่มสินค้า/บริการใหม่ (เข้า Catalog)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addItemForm" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label">ชื่อสินค้า *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ประเภท</label>
                            <!-- We need types here. If not passed, we might need to fetch via JS or pass from controller view() -->
                            <!-- Assuming $facility_types is passed to view -->
                            <select name="type_id" class="form-select" required>
                                <?php if(isset($facility_types)): ?>
                                    <?php foreach($facility_types as $ft): ?>
                                        <option value="<?= $ft['id'] ?>"><?= esc($ft['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ราคา *</label>
                            <input type="number" name="price" class="form-control" required min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">หน่วยนับ</label>
                        <input type="text" name="unit" class="form-control" placeholder="เช่น ชิ้น, ขวด, ครั้ง">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">รายละเอียด</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">รูปภาพ</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="submitAddItem()">บันทึกสินค้า</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Manage Items (Private per Subfield) -->
<div class="modal fade" id="manageItemsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-boxes me-2"></i> จัดการสินค้า: <span id="manageItemsSubfieldName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="manageItemsSubfieldId">
                
                <h6 class="text-muted fw-bold mb-3"><i class="fas fa-list me-1"></i> รายการสินค้าที่มีอยู่</h6>
                <div class="table-responsive mb-4 rounded shadow-sm border">
                    <table class="table table-hover align-middle mb-0" id="manageItemsTable">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th style="width: 70px;" class="ps-3">รูป</th>
                                <th>รายละเอียดสินค้า</th>
                                <th style="width: 150px;">ราคา</th>
                                <th style="width: 100px;" class="text-center">สถานะ</th>
                                <th style="width: 80px;" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="manageItemsTableBody" class="bg-white"></tbody>
                    </table>
                </div>

                <div class="bg-light p-4 rounded-3 border">
                    <h6 class="fw-bold text-success mb-3"><i class="fas fa-plus-circle me-1"></i> เพิ่มสินค้าใหม่</h6>
                    <form id="formAddManageItem">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">ชื่อสินค้า <span class="text-danger">*</span></label>
                                <input type="text" id="manageItemName" class="form-control" placeholder="เช่น น้ำดื่ม, ผ้าเช็ดตัว" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">หมวดหมู่ <span class="text-danger">*</span></label>
                                <select id="manageItemType" class="form-select">
                                    <?php if(isset($facility_types)): ?>
                                        <?php foreach($facility_types as $type): ?>
                                            <option value="<?= $type['id'] ?>"><?= esc($type['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-bold">ราคา (บาท) <span class="text-danger">*</span></label>
                                <input type="number" id="manageItemPrice" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-bold">หน่วยนับ <span class="text-danger">*</span></label>
                                <input type="text" id="manageItemUnit" class="form-control" placeholder="ชิ้น, ขวด" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-bold">รูปภาพ</label>
                                <input type="file" id="manageItemImage" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12">
                                <label class="form-label small text-muted fw-bold">รายละเอียดเพิ่มเติม</label>
                                <input type="text" id="manageItemDesc" class="form-control" placeholder="รายละเอียดอื่นๆ...">
                            </div>
                            <div class="col-12 text-end mt-3">
                                <button type="button" class="btn btn-success px-4" onclick="submitManageItem()">
                                    <i class="fas fa-save me-1"></i> บันทึกสินค้า
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Define SITE_URL for external JS
    const STADIUM_ID = <?= $stadium['id'] ?>;
    const SITE_URL = '<?= base_url() ?>/';
</script>
<script src="<?= base_url('assets/vendors_js/owner_stadium_view.js') ?>?v=<?= time() ?>"></script>

</body>
</html>
