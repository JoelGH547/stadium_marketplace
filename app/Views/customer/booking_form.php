<?= $this->extend('layouts/customer') ?>
<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row">
        <!-- ฝั่งซ้าย: รูปและรายละเอียดสนาม -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <?php 
                    $images = json_decode($stadium['outside_images'] ?? '[]', true);
                    $cover = !empty($images[0]) ? $images[0] : null;
                ?>
                <?php if($cover): ?>
                    <img src="<?= base_url('assets/uploads/stadiums/'.$cover) ?>" class="card-img-top" style="height: 300px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-light text-center py-5"><i class="fas fa-image fa-3x text-muted"></i></div>
                <?php endif; ?>
                
                <div class="card-body">
                    <h2 class="card-title fw-bold text-primary"><?= esc($stadium['name']) ?></h2>
                    <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> <?= esc($stadium['address']) ?></p>
                    <hr>
                    <h5 class="fw-bold">รายละเอียด</h5>
                    <p><?= nl2br(esc($stadium['description'])) ?></p>
                    
                    <!-- สิ่งอำนวยความสะดวก (Facilities) -->
                    <h5 class="fw-bold mt-4">สิ่งอำนวยความสะดวก</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <!-- (ส่วนนี้ดึงจาก StadiumFacilityModel ตามปกติ) -->
                        <span class="badge bg-light text-dark border">Free Wi-Fi</span>
                        <span class="badge bg-light text-dark border">ที่จอดรถ</span>
                        <!-- ... -->
                    </div>
                </div>
            </div>
        </div>

        <!-- ฝั่งขวา: ฟอร์มจอง -->
        <div class="col-lg-4">
            <div class="card shadow border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="mb-0">📅 จองสนามนี้</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('customer/booking/process') ?>" method="post">
                        <input type="hidden" name="stadium_id" value="<?= $stadium['id'] ?>">
                        
                        <!-- 1. เลือกสนามย่อย (เฉพาะ Complex) -->
                        <?php if(($stadium['booking_type'] ?? '') == 'complex'): ?>
                            <div class="mb-3">
                                <label class="fw-bold mb-1">เลือกสนามย่อย <span class="text-danger">*</span></label>
                                <select name="field_id" id="field_select" class="form-select" required>
                                    <option value="">-- กรุณาเลือกสนาม --</option>
                                    <?php foreach($fields as $field): ?>
                                        <option value="<?= $field['id'] ?>" data-price="<?= $field['price'] ?>">
                                            <?= esc($field['name']) ?> (฿<?= number_format($field['price']) ?>/ชม.)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <!-- กรณี Single: ซ่อน field_id ไว้ (หรือใช้ dummy id ถ้ามี) -->
                            <!-- ถ้า Single ไม่มี field_id ใน DB ให้เว้นว่าง หรือจัดการใน Controller -->
                            <div class="mb-3 p-3 bg-light rounded text-center">
                                <span class="text-success fw-bold fs-5">฿<?= number_format($stadium['price']) ?></span> <small>/ ชั่วโมง</small>
                            </div>
                        <?php endif; ?>

                        <!-- 2. วันที่และเวลา -->
                        <div class="mb-3">
                            <label class="fw-bold mb-1">วันที่จอง</label>
                            <input type="date" name="booking_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="fw-bold mb-1">เริ่มเวลา</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="fw-bold mb-1">จำนวน (ชม.)</label>
                                <input type="number" name="hours" id="hours_input" class="form-control" value="1" min="1" required>
                            </div>
                        </div>

                        <hr>

                        <!-- 3. ✅ รายการสินค้า/บริการเสริม (Add-ons) -->
                        <div class="mb-3">
                            <label class="fw-bold mb-2 text-primary"><i class="fas fa-cart-plus"></i> บริการเสริม (เลือกเพิ่มได้)</label>
                            
                            <!-- CASE A: สำหรับสนาม Complex (ซ่อนไว้ก่อน รอเลือกสนาม) -->
                            <?php if(($stadium['booking_type'] ?? '') == 'complex'): ?>
                                <div id="addons-container">
                                    <p class="text-muted small text-center py-2" id="no-field-msg">กรุณาเลือกสนามย่อยเพื่อดูสินค้า</p>
                                    
                                    <?php foreach($fields as $field): ?>
                                        <div class="field-addons d-none" id="addons-field-<?= $field['id'] ?>">
                                            <?php if(!empty($field['addons'])): ?>
                                                <?php foreach($field['addons'] as $item): ?>
                                                    <?= view_cell('\App\Cells\AddonCell::render', ['item' => $item]) ?> 
                                                    <!-- หรือถ้าไม่ได้ใช้ Cell ให้ใช้ HTML นี้ตรงๆ -->
                                                    <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                                                        <div class="d-flex align-items-center">
                                                            <input type="checkbox" name="addons[]" value="<?= $item['item_id'] ?>" class="form-check-input me-2 chk-addon" data-price="<?= $item['custom_price'] ?>">
                                                            <div>
                                                                <div class="small fw-bold"><?= esc($item['name']) ?></div>
                                                                <div class="text-muted" style="font-size: 0.8rem;"><?= esc($item['description']) ?></div>
                                                            </div>
                                                        </div>
                                                        <div class="text-success small fw-bold">+฿<?= number_format($item['custom_price']) ?></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="text-muted small text-center">ไม่มีสินค้าสำหรับสนามนี้</div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            <!-- CASE B: สำหรับสนาม Single (แสดงเลย) -->
                            <?php else: ?>
                                <div id="addons-single">
                                    <?php if(!empty($addons)): ?>
                                        <?php foreach($addons as $item): ?>
                                            <div class="d-flex align-items-center justify-content-between border-bottom py-2">
                                                <div class="d-flex align-items-center">
                                                    <input type="checkbox" name="addons[]" value="<?= $item['item_id'] ?>" class="form-check-input me-2 chk-addon" data-price="<?= $item['custom_price'] ?>">
                                                    <div>
                                                        <div class="small fw-bold"><?= esc($item['name']) ?></div>
                                                    </div>
                                                </div>
                                                <div class="text-success small fw-bold">+฿<?= number_format($item['custom_price']) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted small text-center">ไม่มีบริการเสริม</div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- สรุปยอดเงินคร่าวๆ (JS Calculate) -->
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="d-flex justify-content-between">
                                <span>ค่าสนาม</span>
                                <span id="summary-field">฿0</span>
                            </div>
                            <div class="d-flex justify-content-between text-success">
                                <span>บริการเสริม</span>
                                <span id="summary-addon">+฿0</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>รวมทั้งหมด</span>
                                <span id="summary-total" class="text-primary">฿0</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            ยืนยันการจอง
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldSelect = document.getElementById('field_select');
    const hoursInput = document.getElementById('hours_input');
    const noFieldMsg = document.getElementById('no-field-msg');
    
    // ราคา
    let fieldPricePerHour = <?= ($stadium['booking_type'] != 'complex') ? $stadium['price'] : 0 ?>;
    
    // ฟังก์ชันคำนวณเงิน
    function calculateTotal() {
        const hours = parseInt(hoursInput.value) || 1;
        const fieldTotal = fieldPricePerHour * hours;
        
        // รวมราคา Addons ที่ติ๊กถูก (เฉพาะที่มองเห็นอยู่)
        let addonTotal = 0;
        document.querySelectorAll('.chk-addon:checked').forEach(chk => {
            // เช็คว่าอยู่ใน container ที่แสดงอยู่ไหม (ป้องกันนับตัวที่ซ่อน)
            if(chk.closest('div').offsetParent !== null) {
                addonTotal += parseFloat(chk.dataset.price || 0);
            }
        });

        document.getElementById('summary-field').innerText = '฿' + fieldTotal.toLocaleString();
        document.getElementById('summary-addon').innerText = '+฿' + addonTotal.toLocaleString();
        document.getElementById('summary-total').innerText = '฿' + (fieldTotal + addonTotal).toLocaleString();
    }

    // Event: เปลี่ยนสนามย่อย (Complex)
    if(fieldSelect) {
        fieldSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const fieldId = this.value;
            
            // 1. อัปเดตราคาค่าสนาม
            fieldPricePerHour = parseFloat(selectedOption.dataset.price || 0);
            
            // 2. ซ่อน Addons ทั้งหมดก่อน
            document.querySelectorAll('.field-addons').forEach(el => el.classList.add('d-none'));
            if(noFieldMsg) noFieldMsg.classList.add('d-none');

            // 3. แสดง Addons ของสนามที่เลือก
            if(fieldId) {
                const targetAddons = document.getElementById('addons-field-' + fieldId);
                if(targetAddons) {
                    targetAddons.classList.remove('d-none');
                } else {
                    if(noFieldMsg) {
                        noFieldMsg.innerText = 'ไม่มีสินค้าสำหรับสนามนี้';
                        noFieldMsg.classList.remove('d-none');
                    }
                }
            } else {
                if(noFieldMsg) {
                    noFieldMsg.innerText = 'กรุณาเลือกสนามย่อยเพื่อดูสินค้า';
                    noFieldMsg.classList.remove('d-none');
                }
            }

            // 4. รีเซ็ต Checkbox ของสนามอื่น (เพื่อไม่ให้คิดเงินมั่ว)
            document.querySelectorAll('.chk-addon').forEach(chk => chk.checked = false);

            calculateTotal();
        });
    }

    // Event: เปลี่ยนจำนวนชั่วโมง หรือ ติ๊กของเพิ่ม
    hoursInput.addEventListener('input', calculateTotal);
    document.addEventListener('change', function(e) {
        if(e.target.classList.contains('chk-addon')) {
            calculateTotal();
        }
    });

    // Init ครั้งแรก
    calculateTotal();
});
</script>

<?= $this->endSection() ?>