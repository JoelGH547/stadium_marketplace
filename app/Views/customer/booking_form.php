<?= $this->extend('layouts/customer') ?>

<?= $this->section('extra-css') ?>
<style>
    .gallery-container { height: 400px; border-radius: 12px; overflow: hidden; background: #000; }
    .gallery-container img { width: 100%; height: 100%; object-fit: contain; }
    .field-card { cursor: pointer; border: 2px solid transparent; transition: all 0.2s; }
    .field-card.selected { border-color: var(--primary); background-color: #f0fdfa; }
    .item-card { border-radius: 10px; border: 1px solid #eee; padding: 10px; display: flex; align-items: center; gap: 10px; margin-bottom: 10px; transition: 0.2s; }
    .item-card:hover { border-color: var(--primary); background: #fdfdfd; }
    .facility-badge { font-size: 0.75rem; padding: 4px 10px; background: #f1f5f9; color: #475569; border-radius: 999px; }
    .map-container { height: 300px; border-radius: 12px; background: #e2e8f0; position: relative; overflow: hidden; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="<?= base_url('customer/dashboard') ?>">หน้าแรก</a></li>
            <li class="breadcrumb-item active"><?= esc($stadium['name']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Photo Gallery -->
            <div id="stadiumCarousel" class="carousel slide gallery-container mb-4 shadow-sm" data-ride="carousel">
                <div class="carousel-inner">
                    <?php 
                        $out_imgs = json_decode($stadium['outside_images'] ?? '[]', true);
                        $in_imgs = json_decode($stadium['inside_images'] ?? '[]', true);
                        $all_imgs = array_merge($out_imgs, $in_imgs);
                        if (empty($all_imgs)) $all_imgs = ['default.jpg'];
                    ?>
                    <?php foreach($all_imgs as $index => $img): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> h-100">
                            <img src="<?= base_url('assets/uploads/stadiums/'.$img) ?>" class="d-block mx-auto h-100" alt="Stadium Photo">
                        </div>
                    <?php endforeach; ?>
                </div>
                <a class="carousel-control-prev" href="#stadiumCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </a>
                <a class="carousel-control-next" href="#stadiumCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </a>
            </div>

            <!-- Stadium Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="fw-bold text-dark mb-1"><?= esc($stadium['name']) ?></h2>
                            <p class="text-muted mb-0"><i class="fas fa-map-marker-alt text-danger mr-2"></i><?= esc($stadium['address']) ?></p>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-info px-3 py-2"><?= esc($stadium['category_name']) ?></span>
                        </div>
                    </div>
                    <hr class="my-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-info-circle mr-2 text-primary"></i>รายละเอียดสนาม</h5>
                    <p class="text-secondary"><?= nl2br(esc($stadium['description'])) ?></p>
                </div>
            </div>

            <!-- Sub Fields (Sub Fields) -->
            <h5 class="fw-bold mb-3"><i class="fas fa-th-large mr-2 text-primary"></i>เลือกสนามย่อย</h5>
            <div class="row mb-4">
                <?php foreach($fields as $field): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card field-card h-100 shadow-sm border-0" data-id="<?= $field['id'] ?>" data-price="<?= $field['price'] ?>" onclick="selectField(<?= $field['id'] ?>)">
                            <div class="card-body">
                                <h6 class="fw-bold mb-2"><?= esc($field['name']) ?></h6>
                                <p class="small text-muted mb-3"><?= esc($field['description']) ?></p>
                                
                                <div class="mb-3 d-flex flex-wrap gap-2">
                                    <?php foreach($field['facilities'] as $fac): ?>
                                        <span class="facility-badge" title="<?= esc($fac['name']) ?>">
                                            <i class="fas fa-check-circle mr-1"></i> <?= esc($fac['name']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                    <span class="text-primary fw-bold">฿<?= number_format($field['price']) ?> <small class="text-muted">/ชม.</small></span>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="radio_field_<?= $field['id'] ?>" name="field_id_radio" class="custom-control-input">
                                        <label class="custom-control-label" for="radio_field_<?= $field['id'] ?>"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Map Section -->
            <?php if(!empty($stadium['lat']) && !empty($stadium['lng'])): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-0">
                    <div class="p-3"><h5 class="fw-bold mb-0"><i class="fas fa-map mr-2 text-primary"></i>แผนที่การเดินทาง</h5></div>
                    <div class="map-container">
                        <iframe width="100%" height="100%" frameborder="0" style="border:0" 
                                src="https://www.google.com/maps?q=<?= $stadium['lat'] ?>,<?= $stadium['lng'] ?>&hl=th&z=15&output=embed" allowfullscreen>
                        </iframe>
                    </div>
                    <div class="p-3 text-center">
                        <a href="<?= $stadium['map_link'] ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                            <i class="fas fa-external-link-alt mr-2"></i>เปิดใน Google Maps
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Booking Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 text-center fw-bold">📅 ข้อมูลการจอง</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('customer/booking/process') ?>" method="post" id="bookingForm">
                        <input type="hidden" name="stadium_id" value="<?= $stadium['id'] ?>">
                        <input type="hidden" name="field_id" id="hidden_field_id" required>

                        <div class="mb-4">
                            <label class="small fw-bold text-uppercase text-muted">1. วันที่ต้องการจอง</label>
                            <input type="date" name="booking_date" class="form-control form-control-lg border-primary" required min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="row mb-4">
                            <div class="col-7">
                                <label class="small fw-bold text-uppercase text-muted">2. เริ่มเวลา</label>
                                <input type="time" name="start_time" class="form-control form-control-lg" required value="17:00">
                            </div>
                            <div class="col-5">
                                <label class="small fw-bold text-uppercase text-muted">3. ชม.</label>
                                <input type="number" name="hours" id="hours_input" class="form-control form-control-lg text-center" value="1" min="1" required>
                            </div>
                        </div>

                        <!-- Add-ons Section -->
                        <div id="addon-section" style="display: none;">
                            <label class="small fw-bold text-uppercase text-muted mb-2">4. บริการเสริม (Add-ons)</label>
                            <div id="addon-list">
                                <!-- Dynamic List -->
                            </div>
                        </div>

                        <div class="alert alert-info py-2 small mb-4" id="select-field-prompt">
                            <i class="fas fa-info-circle mr-1"></i> กรุณาเลือกสนามย่อยด้านซ้ายก่อน
                        </div>

                        <!-- Price Summary -->
                        <div class="bg-light p-3 rounded mb-4" id="summary-section" style="display: none;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">ค่าสนาม</span>
                                <span id="summary-field" class="fw-bold">฿0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">บริการเสริม</span>
                                <span id="summary-addon" class="text-success fw-bold">+฿0</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">ยอดรวมสุทธิ</span>
                                <h3 id="summary-total" class="mb-0 text-primary fw-bold">฿0</h3>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm py-3 rounded-pill" id="btn-submit" disabled>
                            จองสนามทันที
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data for JS -->
<script>
const fieldsData = <?= json_encode($fields) ?>;
let selectedFieldId = null;

function selectField(id) {
    selectedFieldId = id;
    document.getElementById('hidden_field_id').value = id;
    
    // UI Update
    document.querySelectorAll('.field-card').forEach(c => c.classList.remove('selected'));
    document.querySelector(`.field-card[data-id="${id}"]`).classList.add('selected');
    document.getElementById(`radio_field_${id}`).checked = true;
    
    // Show sections
    document.getElementById('addon-section').style.display = 'block';
    document.getElementById('summary-section').style.display = 'block';
    document.getElementById('select-field-prompt').style.display = 'none';
    document.getElementById('btn-submit').disabled = false;
    
    // Load Add-ons
    const field = fieldsData.find(f => f.id == id);
    const addonList = document.getElementById('addon-list');
    addonList.innerHTML = '';
    
    let allItems = [];
    field.facilities.forEach(fac => {
        fac.items.forEach(item => {
            allItems.push(item);
        });
    });

    if(allItems.length > 0) {
        allItems.forEach(item => {
            addonList.innerHTML += `
                <div class="item-card">
                    <input type="checkbox" name="items[]" value="${item.id}" data-price="${item.price}" class="item-checkbox">
                    <div class="flex-grow-1">
                        <div class="small fw-bold text-dark">${item.name}</div>
                        <div class="small text-muted">฿${parseFloat(item.price).toLocaleString()} / ${item.unit}</div>
                    </div>
                </div>
            `;
        });
        
        // Bind events to new checkboxes
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.addEventListener('change', calculateTotal);
        });
    } else {
        addonList.innerHTML = '<p class="small text-muted italic">ไม่มีบริการเสริมสำหรับสนามนี้</p>';
    }
    
    calculateTotal();
}

function calculateTotal() {
    if(!selectedFieldId) return;
    
    const field = fieldsData.find(f => f.id == selectedFieldId);
    const hours = parseInt(document.getElementById('hours_input').value) || 1;
    const fieldTotal = parseFloat(field.price) * hours;
    
    let addonTotal = 0;
    document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
        addonTotal += parseFloat(cb.dataset.price);
    });
    
    document.getElementById('summary-field').innerText = '฿' + fieldTotal.toLocaleString();
    document.getElementById('summary-addon').innerText = '+฿' + addonTotal.toLocaleString();
    document.getElementById('summary-total').innerText = '฿' + (fieldTotal + addonTotal).toLocaleString();
}

document.getElementById('hours_input').addEventListener('input', calculateTotal);

document.getElementById('bookingForm').addEventListener('submit', function(e) {
    if(!selectedFieldId) {
        e.preventDefault();
        Swal.fire('กรุณาเลือกสนาม', 'โปรดเลือกสนามย่อยที่ต้องการจองก่อนดำเนินการต่อ', 'warning');
    }
});
</script>
<?= $this->endSection() ?>
