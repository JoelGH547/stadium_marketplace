<?= $this->extend('layouts/customer') ?>

<?= $this->section('extra-css') ?>
<style>
    :root { --primary-soft: #f0fdf4; --mint-dark: #059669; }
    .gallery-container { height: 400px; border-radius: 12px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; }
    .gallery-container img { width: 100%; height: 100%; object-fit: contain; }
    
    .field-card { cursor: pointer; border: 2px solid #f1f5f9; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 16px; overflow: hidden; background: #fff; }
    .field-card:hover { transform: translateY(-5px); border-color: #cbd5e1; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .field-card.selected { border-color: var(--primary); background-color: var(--primary-soft); box-shadow: 0 0 0 1px var(--primary); }
    .field-card .card-img-top { height: 160px; object-fit: cover; }
    
    .item-card { border-radius: 12px; border: 1px solid #e2e8f0; padding: 12px; display: flex; align-items: center; gap: 12px; margin-bottom: 12px; transition: 0.2s; background: #fff; }
    .item-card:hover { border-color: var(--primary); background: var(--primary-soft); }
    .item-img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; background: #f1f5f9; border: 1px solid #e2e8f0; }
    
    .facility-badge { font-size: 0.7rem; padding: 4px 10px; background: #f8fafc; color: #64748b; border-radius: 8px; border: 1px solid #e2e8f0; font-weight: 500; }
    .map-container { height: 350px; border-radius: 16px; background: #f1f5f9; position: relative; overflow: hidden; border: 1px solid #e2e8f0; }
    
    .sidebar-card { border: none; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    .sidebar-header { border-radius: 20px 20px 0 0 !important; background: linear-gradient(135deg, var(--primary) 0%, #0d9488 100%); }
    
    .custom-control-input:checked ~ .custom-control-label::before { background-color: var(--primary); border-color: var(--primary); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 small">
            <li class="breadcrumb-item"><a href="<?= base_url('customer/dashboard') ?>" class="text-muted">หน้าแรก</a></li>
            <li class="breadcrumb-item active text-primary fw-bold"><?= esc($stadium['name']) ?></li>
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
                        $all_imgs = array_merge($out_imgs ?? [], $in_imgs ?? []);
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
            <div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="fw-bold text-dark mb-1 h3"><?= esc($stadium['name']) ?></h1>
                            <p class="text-muted mb-0 small"><i class="fas fa-map-marker-alt text-danger mr-2"></i><?= esc($stadium['address']) ?></p>
                        </div>
                        <span class="badge badge-info px-3 py-2 rounded-pill font-weight-normal"><?= esc($stadium['category_name']) ?></span>
                    </div>
                    <hr class="my-4 opacity-50">
                    <h5 class="fw-bold mb-3 d-flex align-items-center"><i class="fas fa-info-circle mr-2 text-primary"></i>รายละเอียดสนาม</h5>
                    <p class="text-secondary leading-relaxed"><?= nl2br(esc($stadium['description'])) ?></p>
                </div>
            </div>

            <!-- Sub Fields -->
            <div class="d-flex align-items-center mb-3">
                <h5 class="fw-bold mb-0 flex-grow-1"><i class="fas fa-th-large mr-2 text-primary"></i>เลือกสนามย่อย</h5>
                <span class="text-muted small"><?= count($fields) ?> พื้นที่ให้บริการ</span>
            </div>
            
            <div class="row mb-5">
                <?php foreach($fields as $field): ?>
                    <?php 
                        $f_imgs = json_decode($field['outside_images'] ?? '[]', true);
                        $f_thumb = !empty($f_imgs[0]) ? base_url('assets/uploads/fields/'.$f_imgs[0]) : base_url('assets/uploads/stadiums/default.jpg');
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card field-card h-100 border-0 shadow-sm" data-id="<?= $field['id'] ?>" data-price="<?= $field['price'] ?>" onclick="selectField(<?= $field['id'] ?>)">
                            <img src="<?= $f_thumb ?>" class="card-img-top" alt="<?= esc($field['name']) ?>">
                            <div class="card-body d-flex flex-column">
                                <h6 class="fw-bold text-dark mb-1"><?= esc($field['name']) ?></h6>
                                <p class="small text-muted mb-3 line-clamp-2"><?= esc($field['description']) ?></p>
                                
                                <div class="mb-3 d-flex flex-wrap gap-1">
                                    <?php foreach($field['facilities'] as $fac): ?>
                                        <span class="facility-badge">
                                            <i class="fas fa-check-circle mr-1 text-success opacity-75"></i> <?= esc($fac['name']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                    <div class="price-tag">
                                        <span class="h5 mb-0 text-primary fw-bold">฿<?= number_format($field['price']) ?></span>
                                        <small class="text-muted">/ชม.</small>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline mr-0">
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
            <div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="p-4"><h5 class="fw-bold mb-0 text-dark"><i class="fas fa-map-marked-alt mr-2 text-primary"></i>แผนที่และการเดินทาง</h5></div>
                    <div class="map-container mx-3 mb-3">
                        <iframe width="100%" height="100%" frameborder="0" style="border:0" 
                                src="https://www.google.com/maps?q=<?= $stadium['lat'] ?>,<?= $stadium['lng'] ?>&hl=th&z=15&output=embed" allowfullscreen>
                        </iframe>
                    </div>
                    <div class="p-4 text-center border-top bg-light">
                        <a href="<?= $stadium['map_link'] ?>" target="_blank" class="btn btn-white btn-sm px-4 shadow-sm border rounded-pill text-primary font-weight-bold">
                            <i class="fas fa-directions mr-2"></i>เปิดใน Google Maps
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Booking Sidebar -->
        <div class="col-lg-4">
            <div class="card sidebar-card sticky-top border-0 overflow-hidden" style="top: 20px;">
                <div class="card-header sidebar-header text-white py-4 border-0">
                    <h5 class="mb-0 text-center fw-bold"><i class="fas fa-calendar-check mr-2"></i>จองสนามทันที</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('customer/booking/process') ?>" method="post" id="bookingForm">
                        <input type="hidden" name="stadium_id" value="<?= $stadium['id'] ?>">
                        <input type="hidden" name="field_id" id="hidden_field_id" required>

                        <div class="mb-4">
                            <label class="small fw-bold text-uppercase text-muted letter-spacing-1 mb-2">1. วันที่ต้องการจอง</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="far fa-calendar-alt text-primary"></i></span>
                                </div>
                                <input type="date" name="booking_date" class="form-control form-control-lg border-left-0 pl-0 font-weight-bold" required min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-7">
                                <label class="small fw-bold text-uppercase text-muted letter-spacing-1 mb-2">2. เริ่มเวลา</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="far fa-clock text-primary"></i></span>
                                    </div>
                                    <input type="time" name="start_time" class="form-control form-control-lg border-left-0 pl-0 font-weight-bold" required value="17:00">
                                </div>
                            </div>
                            <div class="col-5">
                                <label class="small fw-bold text-uppercase text-muted letter-spacing-1 mb-2">3. ชม.</label>
                                <input type="number" name="hours" id="hours_input" class="form-control form-control-lg text-center font-weight-bold border-primary" value="1" min="1" required>
                            </div>
                        </div>

                        <!-- Add-ons Section -->
                        <div id="addon-section" class="mb-4" style="display: none;">
                            <label class="small fw-bold text-uppercase text-muted letter-spacing-1 mb-2">4. บริการเสริม (Add-ons)</label>
                            <div id="addon-list" class="max-height-300 overflow-auto pr-1">
                                <!-- Dynamic List -->
                            </div>
                        </div>

                        <div class="alert alert-light border text-center py-3 px-2 mb-4 animate__animated animate__fadeIn" id="select-field-prompt">
                            <div class="mb-2 text-warning"><i class="fas fa-mouse-pointer fa-2x"></i></div>
                            <div class="small fw-bold text-muted">กรุณาเลือกสนามย่อยที่ต้องการก่อน</div>
                        </div>

                        <!-- Price Summary -->
                        <div class="bg-light p-4 rounded-4 mb-4 border" id="summary-section" style="display: none;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">ค่าสนาม</span>
                                <span id="summary-field" class="fw-bold font-weight-bold">฿0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted small">บริการเสริม</span>
                                <span id="summary-addon" class="text-success fw-bold font-weight-bold">+฿0</span>
                            </div>
                            <div class="pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark">ราคาสุทธิ</span>
                                    <h3 id="summary-total" class="mb-0 text-primary fw-bold h4">฿0</h3>
                                </div>
                                <small class="text-muted d-block mt-1 text-right italic" style="font-size: 0.65rem;">*ราคานี้รวมภาษีมูลค่าเพิ่มแล้ว</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-lg py-3 rounded-pill transition-all" id="btn-submit" disabled>
                            ยืนยันการจองสนาม
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
    const activeCard = document.querySelector(`.field-card[data-id="${id}"]`);
    activeCard.classList.add('selected');
    document.getElementById(`radio_field_${id}`).checked = true;
    
    // Show sections with smooth transition
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
            const itemImg = item.image ? '<?= base_url('assets/uploads/items/') ?>' + item.image : '<?= base_url('assets/uploads/stadiums/default.jpg') ?>';
            addonList.innerHTML += `
                <div class="item-card">
                    <div class="custom-control custom-checkbox mr-2">
                        <input type="checkbox" name="items[]" value="${item.id}" data-price="${item.price}" class="custom-control-input item-checkbox" id="item_${item.id}">
                        <label class="custom-control-label" for="item_${item.id}"></label>
                    </div>
                    <img src="${itemImg}" class="item-img" alt="${item.name}">
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="small fw-bold text-dark text-truncate">${item.name}</div>
                        <div class="small text-success fw-bold">฿${parseFloat(item.price).toLocaleString()} / ${item.unit}</div>
                    </div>
                </div>
            `;
        });
        
        // Bind events
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.addEventListener('change', calculateTotal);
        });
    } else {
        addonList.innerHTML = '<div class="p-3 text-center border rounded-3 bg-white small text-muted italic">ไม่มีบริการเสริมเพิ่มเติม</div>';
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
        Swal.fire({
            title: 'กรุณาเลือกสนาม',
            text: 'โปรดเลือกสนามย่อยที่ต้องการจองเพื่อทำรายการต่อ',
            icon: 'warning',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'ตกลง'
        });
    }
});
</script>
<?= $this->endSection() ?>
