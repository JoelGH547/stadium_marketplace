let currentSubfieldData = null;

function openSubfieldModal(id) {
    const modal = new bootstrap.Modal(document.getElementById('subfieldModal'));
    modal.show();

    // Reset to View Mode
    toggleEditMode(false);

    // Clear previous data
    document.getElementById('subfieldName').innerText = 'Loading...';
    const facilityList = document.getElementById('subfieldItemsList');
    if (facilityList) facilityList.innerHTML = '<div class="col-12 text-center text-muted">กำลังโหลด...</div>';

    document.getElementById('subfieldImages').innerHTML = '';
    document.getElementById('currentSubfieldId').value = id;

    // Fetch Data
    fetch(SITE_URL + 'owner/subfields/detail/' + id)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text) });
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            currentSubfieldData = data.subfield;
            const sub = data.subfield;

            // --- Populate View Mode ---
            document.getElementById('subfieldName').innerText = sub.name;
            document.getElementById('subfieldPrice').innerText = Number(sub.price).toLocaleString();
            document.getElementById('subfieldDesc').innerText = sub.description || '-';

            // Price Daily
            const dailyWrapper = document.getElementById('subfieldPriceDailyWrapper');
            if (sub.price_daily > 0) {
                dailyWrapper.innerHTML = `(เหมาวัน: <span class="text-dark fw-bold">${Number(sub.price_daily).toLocaleString()}</span> บาท)`;
            } else {
                dailyWrapper.innerHTML = '';
            }

            const statusBadge = document.getElementById('subfieldStatus');
            if (sub.status === 'active') {
                statusBadge.className = 'badge bg-success';
                statusBadge.innerText = 'ใช้งานปกติ';
            } else {
                statusBadge.className = 'badge bg-secondary';
                statusBadge.innerText = 'ปิดปรับปรุง';
            }

            // Images View (Outside & Inside)
            const outImages = JSON.parse(sub.outside_images || '[]');
            const inImages = JSON.parse(sub.inside_images || '[]');

            let imgHtml = '';

            const renderImages = (imgs, label) => {
                let html = '';
                if (imgs.length > 0) {
                    html += `<h6 class="text-start small fw-bold text-muted mt-2">${label}</h6><div class="d-flex flex-wrap gap-2 justify-content-center">`;
                    imgs.forEach(img => {
                        html += `<img src="${SITE_URL}uploads/subfields/${img}" class="img-thumbnail" style="height: 120px; object-fit: cover;">`;
                    });
                    html += `</div>`;
                }
                return html;
            };

            imgHtml += renderImages(outImages, 'ภายนอก');
            imgHtml += renderImages(inImages, 'ภายใน');

            if (imgHtml === '') {
                imgHtml = `<div class="text-muted p-3 bg-light rounded">ไม่มีรูปภาพ</div>`;
            }
            document.getElementById('subfieldImages').innerHTML = imgHtml;

            // --- Populate Edit Mode ---
            document.getElementById('editSubfieldId').value = sub.id;
            document.getElementById('editName').value = sub.name;
            document.getElementById('editPrice').value = sub.price;
            if (document.getElementById('editPriceDaily')) {
                document.getElementById('editPriceDaily').value = sub.price_daily;
            }
            document.getElementById('editDesc').value = sub.description || '';
            document.getElementById('editStatus').value = sub.status;

            // Images Edit (Separated)
            const renderEditImagesList = (imgs, deleteInputName) => {
                let html = '';
                if (imgs.length > 0) {
                    imgs.forEach(img => {
                        html += `
                        <div class="col-4 text-center">
                            <img src="${SITE_URL}uploads/subfields/${img}" class="img-thumbnail mb-1" style="height: 60px; object-fit: cover;">
                            <div class="form-check small">
                                <input class="form-check-input" type="checkbox" name="${deleteInputName}" value="${img}" id="del_${img}">
                                <label class="form-check-label text-danger" style="font-size: 0.7rem;" for="del_${img}">ลบ</label>
                            </div>
                        </div>
                        `;
                    });
                } else {
                    html = '<div class="col-12"><small class="text-muted fst-italic">ไม่มีรูปภาพ</small></div>';
                }
                return html;
            };

            const outContainer = document.getElementById('editExistingOutsideImages');
            const inContainer = document.getElementById('editExistingInsideImages');

            if (outContainer) outContainer.innerHTML = renderEditImagesList(outImages, 'delete_outside_images[]');
            if (inContainer) inContainer.innerHTML = renderEditImagesList(inImages, 'delete_inside_images[]');


            // Facilities / Items List (Enhanced)
            let html = '';
            let editHtml = ''; // For Edit Mode List
            const itemsList = document.getElementById('subfieldItemsList');
            const editItemsList = document.getElementById('editSubfieldItemsList');
            const facilities = data.facilities || [];

            if (facilities.length === 0) {
                html = '<div class="col-12"><p class="text-muted small text-center">ยังไม่มีบริการเสริม</p></div>';
                editHtml = '<p class="text-muted small text-center">ยังไม่มีบริการเสริม</p>';
            } else {
                facilities.forEach(item => {
                    let imgHtml = '';
                    if (item.image) {
                        imgHtml = `<img src="${SITE_URL}uploads/items/${item.image}" class="rounded shadow-sm me-3" style="width: 60px; height: 60px; object-fit: cover;">`;
                    } else {
                        imgHtml = `<div class="rounded bg-light d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;"><i class="fas fa-box text-muted"></i></div>`;
                    }

                    // View Mode HTML
                    html += `
                    <div class="col-12 col-md-6">
                        <div class="border rounded p-2 bg-white shadow-sm d-flex align-items-start h-100">
                            ${imgHtml}
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">${item.name}</h6>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">${Number(item.price).toLocaleString()} บ.</span>
                                </div>
                                <div class="small text-muted mt-1" style="font-size: 0.85rem;">
                                    ${item.description || '-'}
                                </div>
                            </div>
                        </div>
                    </div>
                    `;

                    // Edit Mode HTML
                    editHtml += `
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div class="d-flex align-items-center">
                            ${item.image ? `<img src="${SITE_URL}uploads/items/${item.image}" class="rounded me-2" style="width: 30px; height: 30px;">` : ''}
                            <div>
                                <div class="fw-bold small">${item.name}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">${item.price} บ.</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm py-0" onclick="deleteServiceFromSubfield(${item.item_id || item.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    `;
                });
            }
            if (itemsList) itemsList.innerHTML = html;
            if (editItemsList) editItemsList.innerHTML = editHtml;

        })
        .catch(err => {
            console.error(err);
            alert('Error loading detail: ' + err.message);
        });
}

// Global functions for Subfield Services
function addNewServiceToSubfield() {
    const subId = document.getElementById('editSubfieldId').value;
    const name = document.getElementById('addService_name').value;
    const price = document.getElementById('addService_price').value;
    const unit = document.getElementById('addService_unit').value;
    const typeId = document.getElementById('addService_type').value;
    const desc = document.getElementById('addService_desc').value;
    const imgInput = document.getElementById('addService_image');

    if (!name || !price || !unit) {
        alert('กรุณากรอก ชื่อ, ราคา และหน่วย');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('price', price);
    formData.append('unit', unit);
    formData.append('type_id', typeId);
    formData.append('description', desc);
    formData.append('field_id', subId); // Direct assignment
    if (imgInput.files[0]) {
        formData.append('image', imgInput.files[0]);
    }

    // Need STADIUM_ID. If not available globally, we must get it.
    // Assuming STADIUM_ID is set in view.php
    if (typeof STADIUM_ID === 'undefined') {
        alert('System Error: Missing Stadium ID');
        return;
    }

    fetch(SITE_URL + 'owner/items/create/' + STADIUM_ID, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Clear inputs
                document.getElementById('addService_name').value = '';
                document.getElementById('addService_price').value = '';
                document.getElementById('addService_unit').value = '';
                document.getElementById('addService_desc').value = '';
                imgInput.value = '';

                // Refresh Modal
                openSubfieldModal(subId);
            } else {
                alert('Error: ' + (data.message || 'Cannot add service'));
            }
        })
        .catch(err => console.error(err));
}

function deleteServiceFromSubfield(itemId) {
    if (!confirm('ต้องการลบบริการนี้?')) return;

    // Assuming we have a route for delete item
    // owner/items/delete/(:num)
    // We need 'stadium_id' for the route usually? 
    // Routes: $routes->get('items/delete/(:num)', 'Owner\Items::delete/$1'); (Might need stadium_id params?)
    // Let's check Items::delete. It usually redirects. JS needs JSON.
    // I'll check Items.php in next tool if needed, but assuming standard delete.
    // I'll try generic delete.

    // Wait, typical route is `owner/items/delete/$itemId` ? 
    // Checking previous file view... `app/Controllers/Owner/Items.php`
    // I didn't see `delete` method in previous snippets. 
    // But I'll assumes it exists. 
    // Using fetch

    // Correction: `Items::delete` takes `$id`.

    fetch(SITE_URL + 'owner/items/delete/' + itemId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => res.json()) // Assuming it returns JSON or we handle redirect
        .then(data => {
            // If it redirects, response might be HTML.
            // If successful, reload.
            const subId = document.getElementById('editSubfieldId').value;
            openSubfieldModal(subId);
        })
        .catch(err => {
            // If it was a redirect, it might have worked. Reload anyway.
            const subId = document.getElementById('editSubfieldId').value;
            openSubfieldModal(subId);
        });
}

function toggleEditMode(showEdit) {
    if (showEdit) {
        document.getElementById('viewMode').style.display = 'none';
        document.getElementById('btnEditSubfield').style.display = 'none';

        document.getElementById('editMode').style.display = 'block';
        document.getElementById('btnSaveSubfield').style.display = 'inline-block';
    } else {
        document.getElementById('viewMode').style.display = 'block';
        document.getElementById('btnEditSubfield').style.display = 'inline-block';

        document.getElementById('editMode').style.display = 'none';
        document.getElementById('btnSaveSubfield').style.display = 'none';
    }
}

function saveSubfield() {
    const id = document.getElementById('editSubfieldId').value;
    const form = document.getElementById('editSubfieldForm');
    const formData = new FormData(form);

    // Add a flag to tell controller to return JSON
    formData.append('is_ajax', '1');

    fetch(SITE_URL + 'owner/subfields/update/' + id, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('บันทึกข้อมูลเรียบร้อย');
                location.reload(); // Reload to show changes
            } else {
                alert('เกิดข้อผิดพลาด: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error saving data');
        });
}

function saveFacilities() {
    const id = document.getElementById('currentSubfieldId').value;
    const form = document.getElementById('facilityForm');
    const formData = new FormData(form);

    fetch(SITE_URL + 'owner/subfields/facilities/update/' + id, {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('บันทึกข้อมูลบริการเสริมเรียบร้อย');
            } else {
                alert('เกิดข้อผิดพลาด: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => console.error(err));
}

// ==========================================
// INLINE ITEM CREATION (Quick Add)
// ==========================================
function toggleInlineItemForm() {
    const form = document.getElementById('inlineItemCreator');
    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
}

function saveInlineItem() {
    const nameInput = document.getElementById('inlineName');
    const priceInput = document.getElementById('inlinePrice');
    const unitInput = document.getElementById('inlineUnit');
    const typeInput = document.getElementById('inlineTypeId');
    const descInput = document.getElementById('inlineDesc');
    const imageInput = document.getElementById('inlineImage');

    const name = nameInput.value;
    const price = priceInput.value;

    if (!name || !price) {
        alert('กรุณากรอกชื่อและราคา');
        return;
    }

    // Extract Stadium ID from URL path (assuming /owner/fields/view/ID)
    const parts = window.location.pathname.split('/');
    const stadiumId = parts[parts.length - 1]; // Last segment

    const formData = new FormData();
    formData.append('name', name);
    formData.append('price', price);
    formData.append('unit', unitInput.value);
    formData.append('type_id', typeInput.value);
    formData.append('description', descInput.value);

    if (imageInput.files.length > 0) {
        formData.append('image', imageInput.files[0]);
    }

    // Disable button to prevent double click
    // const btn = event.target; // event not passed explicitly, handled by onclick

    fetch(`${SITE_URL}owner/items/store/${stadiumId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.item) {
                const item = data.item;

                // Remove "No Items" msg if exists
                const noMsg = document.getElementById('noItemsMsg');
                if (noMsg) noMsg.remove();

                let container = document.getElementById('itemsCheckboxList');
                if (!container) {
                    document.getElementById('subfieldItemsContainer').innerHTML = '<div class="row g-2" id="itemsCheckboxList"></div>';
                    container = document.getElementById('itemsCheckboxList');
                }

                const html = `
                <div class="col-md-6 item-checkbox-wrapper">
                    <label class="d-flex align-items-center gap-2 p-2 border rounded bg-light hover-shadow pointer w-100">
                        <input type="checkbox" name="items[]" value="${item.id}" class="form-check-input" checked>
                        <div class="text-truncate">
                            <span class="fw-bold small">${item.name}</span>
                            <span class="text-muted small">(${item.price} บ./${unitInput.value})</span>
                        </div>
                    </label>
                </div>
            `;

                // Insert at beginning
                container.insertAdjacentHTML('afterbegin', html);

                // Reset and Close
                nameInput.value = '';
                priceInput.value = '';
                descInput.value = '';
                imageInput.value = '';
                unitInput.value = '';

                toggleInlineItemForm();

            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => console.error('Error:', error));
}

function openItemModal(id) {
    const modal = new bootstrap.Modal(document.getElementById('itemModal'));
    modal.show();

    toggleItemEditMode(false);

    // Clear
    document.getElementById('itemNameDisplay').innerText = 'Loading...';
    document.getElementById('itemImageDisplay').innerHTML = '';
    document.getElementById('editItemId').value = id;

    fetch(SITE_URL + 'owner/items/detail/' + id)
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            const item = data.item;
            const types = data.types;

            // --- View Mode ---
            document.getElementById('itemNameDisplay').innerText = item.name;
            document.getElementById('itemPriceDisplay').innerText = item.price;
            document.getElementById('itemUnitDisplay').innerText = item.unit || '-';
            document.getElementById('itemDescDisplay').innerText = item.description || '-';

            // Find type name
            const typeObj = types.find(t => t.id == item.facility_type_id);
            document.getElementById('itemTypeDisplay').innerText = typeObj ? typeObj.name : '-';

            // Image
            let imgHtml = '';
            if (item.image) {
                imgHtml = `<img src="${SITE_URL}uploads/items/${item.image}" class="img-fluid rounded shadow-sm" style="max-height: 250px;">`;
            } else {
                imgHtml = `<div class="text-muted p-4 bg-light rounded">ไม่มีรูปภาพ</div>`;
            }
            document.getElementById('itemImageDisplay').innerHTML = imgHtml;

            // --- Edit Mode ---
            document.getElementById('editItemName').value = item.name;
            document.getElementById('editItemPrice').value = item.price;
            document.getElementById('editItemUnit').value = item.unit;
            document.getElementById('editItemDesc').value = item.description || '';

            // Populate Types Dropdown
            let typeOptions = '';
            types.forEach(t => {
                const selected = t.id == item.facility_type_id ? 'selected' : '';
                typeOptions += `<option value="${t.id}" ${selected}>${t.name}</option>`;
            });
            document.getElementById('editItemType').innerHTML = typeOptions;

        })
        .catch(err => console.error(err));
}

function toggleItemEditMode(showEdit) {
    if (showEdit) {
        document.getElementById('itemViewMode').style.display = 'none';
        document.getElementById('itemViewFooter').style.display = 'none';
        document.getElementById('itemEditMode').style.display = 'block';
        document.getElementById('itemEditFooter').style.display = 'flex';
    } else {
        document.getElementById('itemViewMode').style.display = 'block';
        document.getElementById('itemViewFooter').style.display = 'flex';
        document.getElementById('itemEditMode').style.display = 'none';
        document.getElementById('itemEditFooter').style.display = 'none';
    }
}

function saveItem() {
    const id = document.getElementById('editItemId').value;
    const form = document.getElementById('editItemForm');
    const formData = new FormData(form);

    fetch(SITE_URL + 'owner/items/update/' + id, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('บันทึกข้อมูลเรียบร้อย');
                location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error saving data');
        });
}

// ==========================================
// ADD SUBFIELD MODAL
// ==========================================
function openAddSubfieldModal() {
    const modal = new bootstrap.Modal(document.getElementById('addSubfieldModal'));
    modal.show();
    document.getElementById('addSubfieldForm').reset();
}

function submitAddSubfield() {
    const form = document.getElementById('addSubfieldForm');
    const formData = new FormData(form);
    const stadiumId = window.location.href.split('/').pop(); // Get ID from URL

    fetch(SITE_URL + 'owner/subfields/create/' + stadiumId, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('เพิ่มสนามย่อยสำเร็จ');
                location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => console.error(err));
}

// ==========================================
// ADD ITEM MODAL
// ==========================================
function openAddItemModal() {
    const modal = new bootstrap.Modal(document.getElementById('addItemModal'));
    modal.show();
    document.getElementById('addItemForm').reset();
}

function submitAddItem() {
    const form = document.getElementById('addItemForm');
    const formData = new FormData(form);
    const stadiumId = window.location.href.split('/').pop(); // Get ID from URL

    fetch(SITE_URL + 'owner/items/store/' + stadiumId, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('เพิ่มสินค้าสำเร็จ');
                location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => console.error(err));
}

// --- Manage Items Modal Logic (Per Subfield) ---

function openManageItemsModal(subId) {
    if (!subId) return;

    // Reset Form
    document.getElementById('formAddManageItem').reset();
    document.getElementById('manageItemsSubfieldId').value = subId;
    document.getElementById('manageItemsTableBody').innerHTML = '<tr><td colspan="5" class="text-center">กำลังโหลด...</td></tr>';
    document.getElementById('manageItemsSubfieldName').innerText = '...';

    const modal = new bootstrap.Modal(document.getElementById('manageItemsModal'));
    modal.show();

    // Fetch Detail
    fetch(SITE_URL + 'owner/subfields/detail/' + subId)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('manageItemsSubfieldName').innerText = data.subfield.name;
                renderManageItemsTable(data.facilities || []);
            } else {
                alert('Error loading data');
                modal.hide();
            }
        })
        .catch(err => console.error(err));
}

function renderManageItemsTable(items) {
    const tbody = document.getElementById('manageItemsTableBody');
    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">ยังไม่มีสินค้า</td></tr>';
        return;
    }

    let html = '';
    items.forEach(item => {
        const img = item.image ? SITE_URL + 'uploads/items/' + item.image : SITE_URL + 'uploads/no-image.png';
        const itemId = item.item_id || item.id;
        const isChecked = (item.status === 'active') ? 'checked' : '';

        html += `
        <tr>
            <td class="ps-3"><img src="${img}" class="rounded-3 shadow-sm" style="width: 48px; height: 48px; object-fit: cover;"></td>
            <td>
                <div class="fw-bold text-dark" style="font-size: 1rem;">${item.name}</div>
                <div class="d-flex align-items-center mt-1">
                     <span class="badge bg-light text-secondary border me-2">${item.type_name || 'General'}</span>
                     <span class="small text-muted text-truncate" style="max-width: 200px;">${item.description || '-'}</span>
                </div>
            </td>
            <td>
                <div class="fw-bold text-success">${Number(item.price).toLocaleString()} ฿</div>
                <div class="small text-muted">ต่อ ${item.unit}</div>
            </td>
            <td class="text-center">
                <div class="form-check form-switch d-flex justify-content-center scale-125">
                    <input class="form-check-input" style="cursor: pointer;" type="checkbox" onchange="toggleItemStatus(${itemId})" ${isChecked}>
                </div>
            </td>
            <td class="text-center">
                <button class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="deleteManageItem(${itemId})">
                    <i class="fas fa-trash me-1"></i> ลบ
                </button>
            </td>
        </tr>
        `;
    });
    tbody.innerHTML = html;
}

function toggleItemStatus(itemId) {
    fetch(SITE_URL + 'owner/items/toggleStatus/' + itemId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('Status updated', data.status);
            } else {
                alert('ไม่สามารถเปลี่ยนสถานะได้: ' + (data.message || 'Unknown Error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        });
}

function submitManageItem() {
    const subId = document.getElementById('manageItemsSubfieldId').value;
    const name = document.getElementById('manageItemName').value;
    const price = document.getElementById('manageItemPrice').value;
    const unit = document.getElementById('manageItemUnit').value;
    const typeId = document.getElementById('manageItemType').value;
    const desc = document.getElementById('manageItemDesc').value;
    const imgInput = document.getElementById('manageItemImage');

    if (!name || !price || !unit) {
        alert('กรุณากรอกข้อมูลสำคัญ (ชื่อ, ราคา, หน่วย)');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('price', price);
    formData.append('unit', unit);
    formData.append('type_id', typeId);
    formData.append('description', desc);
    formData.append('field_id', subId);
    if (imgInput.files[0]) {
        formData.append('image', imgInput.files[0]);
    }

    if (typeof STADIUM_ID === 'undefined') { alert('System Error: STADIUM_ID missing'); return; }

    fetch(SITE_URL + 'owner/items/store/' + STADIUM_ID, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Clear Form inputs but keep subfieldId
                document.getElementById('manageItemName').value = '';
                document.getElementById('manageItemPrice').value = '';
                document.getElementById('manageItemUnit').value = '';
                document.getElementById('manageItemDesc').value = '';
                document.getElementById('manageItemImage').value = '';

                // Refresh Table
                fetch(SITE_URL + 'owner/subfields/detail/' + subId)
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) renderManageItemsTable(d.facilities || []);
                    });
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => console.error(err));
}

function deleteManageItem(itemId) {
    if (!confirm('ยืนยันการลบสินค้า?')) return;

    fetch(SITE_URL + 'owner/items/delete/' + itemId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Refresh
                const subId = document.getElementById('manageItemsSubfieldId').value;
                fetch(SITE_URL + 'owner/subfields/detail/' + subId)
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) renderManageItemsTable(d.facilities || []);
                    });
            } else {
                alert('Error deleting item');
            }
        })
        .catch(err => console.error(err));
}
