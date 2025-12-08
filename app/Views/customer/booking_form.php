<?= $this->extend('layouts/customer') ?>
<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row">
        
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
                    
                    
                    <h5 class="fw-bold mt-4">สิ่งอำนวยความสะดวก</h5>
                    <div class="d-flex flex-wrap gap-2">
                        
                        <span class="badge bg-light text-dark border">Free Wi-Fi</span>
                        <span class="badge bg-light text-dark border">ที่จอดรถ</span>
                        
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="card shadow border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="mb-0">📅 จองสนามนี้</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('customer/booking/process') ?>" method="post">
                        <input type="hidden" name="stadium_id" value="<?= $stadium['id'] ?>">
                        
                        
                        <?php if(!empty($fields)): ?>
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
                            
                            <div class="mb-3 p-3 bg-light rounded text-center">
                                <span class="text-success fw-bold fs-5">฿<?= number_format($stadium['price']) ?></span> <small>/ ชั่วโมง</small>
                            </div>
                        <?php endif; ?>

                        
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
    
    
    const hasFields = <?= !empty($fields) ? 'true' : 'false' ?>;
    let fieldPricePerHour = hasFields ? 0 : <?= (int)($stadium['price'] ?? 0) ?>;
    
    
    function calculateTotal() {
        const hours = parseInt(hoursInput.value) || 1;
        const fieldTotal = fieldPricePerHour * hours;
        
        document.getElementById('summary-field').innerText = '฿' + fieldTotal.toLocaleString();
        document.getElementById('summary-addon').innerText = '+฿0'; // Always 0 since addons are removed
        document.getElementById('summary-total').innerText = '฿' + (fieldTotal).toLocaleString();
    }

    
    if(fieldSelect) {
        fieldSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            
            fieldPricePerHour = parseFloat(selectedOption.dataset.price || 0);
            
            calculateTotal();
        });
    }

    
    hoursInput.addEventListener('input', calculateTotal);
    
    
    calculateTotal();
});
</script>

<?= $this->endSection() ?>