<!-- สนามย่อย -->
<div class="card card-mint p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="fw-bold">สนามย่อยทั้งหมด</h5>
        <a href="<?= base_url('owner/fields/subfields/'.$stadium['id']) ?>" 
        class="btn btn-success btn-sm">➕ เพิ่มสนามย่อย</a>
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

<!-- Modal Subfield Details -->
<div class="modal fade" id="subfieldModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="subfieldModalLabel">รายละเอียดสนามย่อย</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <!-- VIEW MODE -->
        <div id="viewMode">
            <div class="row">
                <!-- Left: Info & Images -->
                <div class="col-md-6">
                    <div id="subfieldImages" class="mb-3 text-center">
                        <!-- Images will be loaded here -->
                    </div>
                    <h5 id="subfieldName" class="fw-bold"></h5>
                    <p class="text-muted">ราคา: <span id="subfieldPrice"></span> บาท/ชม.</p>
                    <p>รายละเอียด: <span id="subfieldDesc"></span></p>
                    <p>สถานะ: <span id="subfieldStatus" class="badge"></span></p>
                </div>

                <!-- Right: Facilities -->
                <div class="col-md-6 border-start">
                    <h6 class="fw-bold text-success">สิ่งอำนวยความสะดวก / บริการเสริม</h6>
                    <p class="small text-muted">เลือกรายการที่มีในสนามย่อยนี้</p>
                    <form id="facilityForm">
                        <input type="hidden" id="currentSubfieldId" name="subfield_id">
                        <div id="facilityList" class="list-group">
                            <!-- Checkboxes will be loaded here -->
                            <p class="text-center text-muted">กำลังโหลด...</p>
                        </div>
                    </form>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-mint btn-sm" onclick="saveFacilities()">บันทึกบริการเสริม</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- EDIT MODE -->
        <div id="editMode" style="display: none;">
            <form id="editSubfieldForm" enctype="multipart/form-data">
                <input type="hidden" name="subfield_id" id="editSubfieldId">
                
                <div class="mb-3">
                    <label class="form-label">ชื่อสนามย่อย</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">ราคา (บาท/ชั่วโมง)</label>
                    <input type="number" name="price" id="editPrice" class="form-control" required min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">สถานะ</label>
                    <select name="status" id="editStatus" class="form-select">
                        <option value="active">ใช้งานปกติ</option>
                        <option value="maintenance">ปิดปรับปรุง</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">รายละเอียดเพิ่มเติม</label>
                    <textarea name="description" id="editDesc" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">รูปภาพปัจจุบัน</label>
                    <div id="editCurrentImages" class="row g-2">
                        <!-- Images with delete checkbox -->
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">เพิ่มรูปภาพใหม่</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                </div>
            </form>
        </div>

      </div>
      <div class="modal-footer justify-content-end">
        <button type="button" id="btnEditSubfield" class="btn btn-mint" onclick="toggleEditMode(true)">✏️ แก้ไขข้อมูล</button>
        <button type="button" id="btnSaveSubfield" class="btn btn-mint" onclick="saveSubfield()" style="display: none;">บันทึกการเปลี่ยนแปลง</button>
      </div>
    </div>
  </div>
</div>
