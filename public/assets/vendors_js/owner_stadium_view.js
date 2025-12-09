let currentSubfieldData = null;

function openSubfieldModal(id) {
    const modal = new bootstrap.Modal(document.getElementById('subfieldModal'));
    modal.show();

    // Reset to View Mode
    toggleEditMode(false);

    // Clear previous data
    document.getElementById('subfieldName').innerText = 'Loading...';
    document.getElementById('facilityList').innerHTML = '<p class="text-center text-muted">กำลังโหลด...</p>';
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
            document.getElementById('subfieldPrice').innerText = sub.price;
            document.getElementById('subfieldDesc').innerText = sub.description || '-';

            const statusBadge = document.getElementById('subfieldStatus');
            if (sub.status === 'active') {
                statusBadge.className = 'badge bg-success';
                statusBadge.innerText = 'ใช้งานปกติ';
            } else {
                statusBadge.className = 'badge bg-secondary';
                statusBadge.innerText = 'ปิดปรับปรุง';
            }

            // Images View
            const images = JSON.parse(sub.outside_images || '[]');
            let imgHtml = '';
            if (images.length > 0) {
                imgHtml = `<img src="${SITE_URL}uploads/subfields/${images[0]}" class="img-fluid rounded" style="max-height: 200px;">`;
            } else {
                imgHtml = `<div class="text-muted p-3 bg-light rounded">ไม่มีรูปภาพ</div>`;
            }
            document.getElementById('subfieldImages').innerHTML = imgHtml;

            // --- Populate Edit Mode ---
            document.getElementById('editSubfieldId').value = sub.id;
            document.getElementById('editName').value = sub.name;
            document.getElementById('editPrice').value = sub.price;
            document.getElementById('editDesc').value = sub.description || '';
            document.getElementById('editStatus').value = sub.status;

            // Images Edit
            let editImgHtml = '';
            if (images.length > 0) {
                images.forEach(img => {
                    editImgHtml += `
                    <div class="col-4 text-center">
                        <img src="${SITE_URL}uploads/subfields/${img}" class="img-thumbnail mb-1" style="height: 80px; object-fit: cover;">
                        <div class="form-check small">
                            <input class="form-check-input" type="checkbox" name="delete_images[]" value="${img}" id="del_${img}">
                            <label class="form-check-label text-danger" for="del_${img}">ลบ</label>
                        </div>
                    </div>
                `;
                });
            } else {
                editImgHtml = '<p class="text-muted small">ไม่มีรูปภาพเดิม</p>';
            }
            document.getElementById('editCurrentImages').innerHTML = editImgHtml;


            // Facilities List
            let html = '';
            if (data.facilities.length === 0) {
                html = '<p class="text-muted text-center">ไม่มีรายการบริการเสริมในระบบ</p>';
            } else {
                data.facilities.forEach(item => {
                    const isChecked = data.checked.includes(item.id) ? 'checked' : '';

                    // Image handling
                    let imgHtml = '';
                    if (item.image) {
                        imgHtml = `<img src="${SITE_URL}uploads/items/${item.image}" class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">`;
                    } else {
                        imgHtml = `<div class="rounded me-2 bg-light d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px; font-size: 10px;">No Pic</div>`;
                    }

                    html += `
                    <label class="list-group-item d-flex align-items-start gap-2 p-2" style="cursor: pointer;">
                        <div class="mt-2">
                            <input class="form-check-input" type="checkbox" name="facilities[]" value="${item.id}" ${isChecked}>
                        </div>
                        
                        ${imgHtml}

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">${item.name}</span>
                                <span class="badge bg-success bg-opacity-10 text-success">${item.price} บ.</span>
                            </div>
                            <div class="small text-muted">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary me-1">${item.type_name}</span>
                                ${item.description || ''}
                            </div>
                        </div>
                    </label>
                `;
                });
            }
            document.getElementById('facilityList').innerHTML = html;

        })
        .catch(err => console.error(err));
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
