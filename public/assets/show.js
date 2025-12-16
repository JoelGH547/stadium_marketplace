document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM Content Loaded - Consolidated Script Started');

    // --- Elements ---
    const article = document.getElementById('stadiumDetail');
    const bookingTypeSelect = document.getElementById('bookingTypeSelect');
    const hourlyBookingFields = document.getElementById('hourlyBookingFields');
    const dailyBookingFields = document.getElementById('dailyBookingFields');
    const dateInput = document.getElementById('bookingDate'); // Hourly date
    const startTimeSelect = document.getElementById('startTimeSelect');
    const endTimeSelect = document.getElementById('endTimeSelect');
    const startDateInput = document.getElementById('startDate'); // Daily start date
    const endDateInput = document.getElementById('endDate');
    const timeHelpText = document.getElementById('timeHelpText');
    const timeErrorText = document.getElementById('timeErrorText');
    const bookingDurationWrapper = document.getElementById('bookingDurationWrapper');
    const bookingHoursLabel = document.getElementById('bookingHoursLabel');
    const bookingFieldPriceEl = document.getElementById('bookingFieldPrice');
    const bookingServiceFeeEl = document.getElementById('bookingServiceFee');
    const bookingItemsSummaryEl = document.getElementById('bookingItemsSummary');
    const bookingItemsListEl = document.getElementById('bookingItemsList');
    const bookingItemsField = document.getElementById('bookingItemsField');
    const btnBookNow = document.getElementById('btnBookNow');

    const infoBoxPriceHourEl = document.getElementById('infoBoxPriceHour');
    const infoBoxPriceDayEl = document.getElementById('infoBoxPriceDay');

    // --- State ---
    const selectedItems = new Map(); // itemId -> { name, price, qty }

    // --- Data from DOM ---
    const openTimeRaw = (article.dataset.openTime || '').trim();
    const closeTimeRaw = (article.dataset.closeTime || '').trim();

    // --- Helper Functions ---
    const getPriceFromElement = (element) => {
        if (!element || !element.textContent) return 0;
        const priceText = element.textContent.replace(/[^0-9.]/g, '');
        return parseFloat(priceText) || 0;
    };

    function parseTimeToMinutes(str) {
        if (!str) return null;
        const parts = str.split(':');
        if (parts.length < 2) return null;
        const h = parseInt(parts[0], 10);
        const m = parseInt(parts[1], 10);
        if (Number.isNaN(h) || Number.isNaN(m)) return null;
        return h * 60 + m;
    }

    function minutesToLabel(mins) {
        const h = Math.floor(mins / 60);
        const m = mins % 60;
        return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
    }

    function clearSelectOptions(selectEl, placeholder) {
        if (!selectEl) return;
        while (selectEl.firstChild) {
            selectEl.removeChild(selectEl.firstChild);
        }
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = placeholder;
        selectEl.appendChild(opt);
    }

    function getDurationHours() {
        if (!startTimeSelect || !endTimeSelect) return 0;
        const startVal = startTimeSelect.value;
        const endVal = endTimeSelect.value;
        const startM = parseTimeToMinutes(startVal);
        const endM = parseTimeToMinutes(endVal);
        if (startM === null || endM === null) return 0;
        const diff = endM - startM;
        if (diff <= 0) return 0;
        return diff / 60;
    }

    function getDurationDays() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        if (!startDate || !endDate) return 0;
        const start = new Date(startDate);
        const end = new Date(endDate);
        if (end < start) return 0;
        const diffTime = Math.abs(end - start);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    }

    function getItemsTotal() {
        let total = 0;
        for (const item of selectedItems.values()) {
            total += item.price * item.qty;
        }
        return total;
    }

    // --- Item Logic ---

    // NEW: Check if booking time is selected
    function checkBookingValidity() {
        const bookingType = bookingTypeSelect.value;
        if (bookingType === 'daily') {
            return getDurationDays() > 0;
        } else {
            return getDurationHours() > 0;
        }
    }

    // NEW: Clear all items
    function clearAllItems() {
        if (selectedItems.size === 0) return;

        // Revert all buttons
        selectedItems.forEach((item, id) => {
            const controlDiv = document.querySelector(`div[data-item-control-id="${id}"]`);
            if (controlDiv && item.originalBtn) {
                controlDiv.replaceWith(item.originalBtn);
            }
        });

        selectedItems.clear();
        renderCartSummary();
        updateTotalCalculation();
    }

    // NEW: Disable/Enable Add Buttons
    function toggleAddItemButtons(enable) {
        const addBtns = document.querySelectorAll('.add-item-btn');
        addBtns.forEach(btn => {
            if (enable) {
                btn.disabled = false;
                btn.classList.remove('bg-gray-300', 'cursor-not-allowed', 'text-gray-500');
                btn.classList.add('bg-[var(--primary)]', 'text-white', 'hover:bg-teal-700', 'shadow-sm');
            } else {
                btn.disabled = true;
                btn.classList.add('bg-gray-300', 'cursor-not-allowed', 'text-gray-500');
                btn.classList.remove('bg-[var(--primary)]', 'text-white', 'hover:bg-teal-700', 'shadow-sm');
            }
        });
    }

    function updateItemButtonState(itemId) {
        // ... (unused currently)
    }

    // Since we are replacing the button with HTML, let's handle the UI updates via event delegation mostly,
    // but for the initial render, we need to correct the UI.

    function toggleItemControlUI(targetBtn, itemId, name, price) {
        // ... (unused currently)
    }

    function revertItemControlUI(itemId) {
        // ... (unused currently)
    }

    document.addEventListener('click', function (e) {
        // Add Item
        const addBtn = e.target.closest('.add-item-btn');
        if (addBtn) {
            // Check provided by button disabled state, but double check
            if (addBtn.disabled) return;

            const name = addBtn.dataset.itemName;
            const price = parseFloat(addBtn.dataset.itemPrice);
            const itemId = parseInt(addBtn.dataset.itemId);
            const unit = addBtn.dataset.itemUnit || 'ชิ้น'; // Capture unit
            const image = addBtn.dataset.itemImage || ''; // Capture image

            // Create item object
            const newItem = {
                id: itemId, // Store ID explicitly
                name: name,
                price: price,
                qty: 1,
                unit: unit, // Store unit
                image: image,
                originalBtn: addBtn.cloneNode(true)
            };
            selectedItems.set(itemId, newItem);

            // Replace button with controls (Decrease / Qty / Increase)
            const controlDiv = document.createElement('div');
            controlDiv.className = 'flex items-center gap-2';
            controlDiv.dataset.itemControlId = itemId;
            controlDiv.innerHTML = `
                <button type="button" class="btn-decrease-item w-7 h-7 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 flex items-center justify-center font-bold" data-id="${itemId}">-</button>
                <span class="text-sm font-semibold text-gray-900 w-6 text-center item-qty-display" data-id="${itemId}">1</span>
                <button type="button" class="btn-increase-item w-7 h-7 rounded-lg bg-[var(--primary)] text-white hover:bg-teal-700 flex items-center justify-center font-bold" data-id="${itemId}">+</button>
            `;

            addBtn.replaceWith(controlDiv);

            renderCartSummary();
            updateTotalCalculation();
            return;
        }

        // Increase
        const incBtn = e.target.closest('.btn-increase-item');
        if (incBtn) {
            const id = parseInt(incBtn.dataset.id);
            const item = selectedItems.get(id);
            if (item) {
                item.qty++;
                // Update UI
                const qtyDisplay = document.querySelector(`.item-qty-display[data-id="${id}"]`);
                if (qtyDisplay) qtyDisplay.textContent = item.qty;

                renderCartSummary();
                updateTotalCalculation();
            }
            return;
        }

        // Decrease
        const decBtn = e.target.closest('.btn-decrease-item');
        if (decBtn) {
            const id = parseInt(decBtn.dataset.id);
            const item = selectedItems.get(id);
            if (item) {
                item.qty--;
                if (item.qty <= 0) {
                    // Remove item
                    const controlDiv = document.querySelector(`div[data-item-control-id="${id}"]`);
                    // We need to find the original button if we are deleting
                    if (controlDiv && item.originalBtn) {
                        controlDiv.replaceWith(item.originalBtn);
                    }
                    selectedItems.delete(id);

                    // Re-run toggle to ensure revived button is disabled if needed (though unlikely when deleting)
                    // Actually, if we remove an item, the button comes back.
                    // If the booking is valid, it should be enabled. If invalid, disabled.
                    // We should check validity just in case.
                    const isValid = checkBookingValidity();
                    toggleAddItemButtons(isValid);
                } else {
                    // Update UI
                    const qtyDisplay = document.querySelector(`.item-qty-display[data-id="${id}"]`);
                    if (qtyDisplay) qtyDisplay.textContent = item.qty;
                }
                renderCartSummary();
                updateTotalCalculation();
            }
            return;
        }
    });


    function renderCartSummary() {
        if (!bookingItemsListEl || !bookingItemsSummaryEl) return;

        bookingItemsListEl.innerHTML = '';
        if (selectedItems.size === 0) {
            bookingItemsSummaryEl.classList.remove('hidden');
            // Reset hidden input
            if (bookingItemsField) bookingItemsField.value = '';
        } else {
            bookingItemsSummaryEl.classList.add('hidden');

            const itemsArray = [];
            selectedItems.forEach((item, id) => {
                const li = document.createElement('li');
                li.className = 'flex justify-between items-center text-xs py-1';
                li.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="text-gray-800">${item.name} <span class="text-gray-500">x${item.qty}</span></span>
                        <div class="flex items-center gap-1">
                            <button type="button" class="btn-decrease-item w-5 h-5 rounded bg-gray-100 text-gray-600 hover:bg-gray-200 flex items-center justify-center font-bold text-xs" data-id="${id}">-</button>
                            <button type="button" class="btn-increase-item w-5 h-5 rounded bg-[var(--primary)] text-white hover:bg-teal-700 flex items-center justify-center font-bold text-xs" data-id="${id}">+</button>
                        </div>
                    </div>
                    <span class="font-medium text-gray-900">${(item.price * item.qty).toLocaleString()}฿</span>
                `;
                bookingItemsListEl.appendChild(li);

                itemsArray.push({ id, ...item });
            });

            // Update hidden input
            if (bookingItemsField) bookingItemsField.value = JSON.stringify(itemsArray);
        }
    }

    // --- Time Slot Generation ---
    function buildTimeOptionsForDate(dateStr) {
        if (!startTimeSelect || !endTimeSelect) return;

        const todayStr = new Date().toISOString().split('T')[0];
        clearSelectOptions(startTimeSelect, '— เลือกเวลาเริ่มต้น —');
        clearSelectOptions(endTimeSelect, '— เลือกเวลาสิ้นสุด —');
        startTimeSelect.disabled = false;
        endTimeSelect.disabled = true;

        if (!dateStr) {
            if (timeErrorText) timeErrorText.classList.add('hidden');
            return;
        }

        let openMinutes = parseTimeToMinutes(openTimeRaw);
        let closeMinutes = parseTimeToMinutes(closeTimeRaw);
        if (openMinutes === null || closeMinutes === null || closeMinutes <= openMinutes) {
            openMinutes = 9 * 60;
            closeMinutes = 22 * 60;
        }

        let startBase = openMinutes;
        if (dateStr === todayStr) {
            const now = new Date();
            const currentMinutes = now.getHours() * 60 + now.getMinutes();
            const nextHour = (Math.floor(currentMinutes / 60) + 1) * 60;
            if (nextHour > startBase) startBase = nextHour;
        }

        if (startBase > closeMinutes - 60) {
            startTimeSelect.disabled = true;
            if (timeErrorText) {
                timeErrorText.textContent = dateStr === todayStr ? 'วันนี้เลยเวลาเปิดให้จองแล้ว' : 'ไม่พบช่วงเวลาที่จองได้';
                timeErrorText.classList.remove('hidden');
            }
            return;
        }

        if (timeErrorText) timeErrorText.classList.add('hidden');

        for (let t = startBase; t <= closeMinutes - 60; t += 60) {
            const label = minutesToLabel(t);
            const opt = document.createElement('option');
            opt.value = label;
            opt.textContent = label;
            startTimeSelect.appendChild(opt);
        }
    }

    function rebuildEndTimeOptions() {
        if (!startTimeSelect || !endTimeSelect) return;
        const startVal = startTimeSelect.value;
        clearSelectOptions(endTimeSelect, '— เลือกเวลาสิ้นสุด —');
        if (!startVal) {
            endTimeSelect.disabled = true;
            return;
        }
        let closeMinutes = parseTimeToMinutes(closeTimeRaw);
        if (closeMinutes === null) closeMinutes = 22 * 60;
        const startMinutes = parseTimeToMinutes(startVal);
        if (startMinutes === null) {
            endTimeSelect.disabled = true;
            return;
        }
        const minEnd = startMinutes + 60;
        if (minEnd > closeMinutes) {
            endTimeSelect.disabled = true;
            return;
        }
        for (let t = minEnd; t <= closeMinutes; t += 60) {
            const label = minutesToLabel(t);
            const opt = document.createElement('option');
            opt.value = label;
            opt.textContent = label;
            endTimeSelect.appendChild(opt);
        }
        endTimeSelect.disabled = false;
    }

    // --- Main UI Update Functions ---
    function updateTotalCalculation() {
        const bookingType = bookingTypeSelect.value;
        const itemsTotal = getItemsTotal();
        let fieldTotal = 0;
        let serviceFee = 0;
        let label = '';
        let showWrapper = false;
        let isValid = false;

        if (bookingType === 'daily') {
            const dailyPrice = getPriceFromElement(infoBoxPriceDayEl);
            const days = getDurationDays();

            if (days > 0 && dailyPrice > 0) {
                fieldTotal = days * dailyPrice;
                serviceFee = (fieldTotal + itemsTotal) * 0.05;
                label = `${days} วัน`;
                showWrapper = true;
                isValid = true;
            }
        } else {
            const hours = getDurationHours();
            const pricePerHour = getPriceFromElement(infoBoxPriceHourEl);

            if (hours > 0 && pricePerHour > 0) {
                fieldTotal = hours * pricePerHour;
                serviceFee = (fieldTotal + itemsTotal) * 0.05;
                label = `${hours} ชั่วโมง`;
                showWrapper = true;
                isValid = true;
            }
        }

        // Always show items price if any
        // But the structure is Field Price + Service Fee.
        // We might need to adjust the Logic: Service fee is based on Field + Items? Or just Field?
        // Usually, platform fees cover everything. Let's assume (Field + Items) * 5%.
        // But waiting, the UI has "ค่าจองสนาม" (Field Price) and "ค่าบริการ" (Service Fee).
        // It doesn't explicitly show "Items Total" line in the main summary logic, but we added a list.

        // Let's stick to the existing ID usage:
        // bookingFieldPriceEl -> Field Cost
        // bookingServiceFeeEl -> 5% of (Field + Items)? Or just Field?
        // Let's assume 5% is on everything.

        // However, if we don't have a specific "Total to Pay" element in this snippet, 
        // the user might be confused if the math doesn't sum up.
        // The previous code had `totalFieldPrice` and `serviceFee`.
        // Let's keep `bookingFieldPriceEl` strictly for Field cost.
        // And `bookingServiceFeeEl` for the fee.

        // If field selection is invalid, reset field price but KEEP items?
        // The original code hid everything if invalid.
        // But now we might have items selected without valid date/time?
        // Let's require valid booking time to show accurate pricing.

        // Toggle buttons based on validity
        toggleAddItemButtons(isValid);

        if (isValid) {
            bookingFieldPriceEl.textContent = `${fieldTotal.toLocaleString()}฿`;

            const totalBase = fieldTotal + itemsTotal;
            // Service fee on total?
            serviceFee = totalBase * 0.05;

            // Wait, usually the "Field Price" line says "Booking Fee".
            // If items are separate, where do they go?
            // "bookingItemsList" shows them.
            // We should probably show a Grand Total if possible, but the UI doesn't have it.
            // For now, let's just make sure Service Fee reflects the complexity or just field.
            // Let's stick to: Service Fee = 5% of Field Price (standard). Items are extras.
            // Actually, if I buy water, do I pay 5% service fee on water? Probably.
            // Let's use (Field + Items) * 0.05

            bookingServiceFeeEl.textContent = `${serviceFee.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}฿`;

            bookingHoursLabel.textContent = label;
            if (showWrapper) bookingDurationWrapper.classList.remove('hidden');

            // Enable/Disable Book Button
            btnBookNow.disabled = false;
            btnBookNow.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-300', 'text-gray-600');
            btnBookNow.classList.add('bg-[var(--primary)]', 'text-white', 'hover:bg-teal-700');

        } else {
            bookingFieldPriceEl.textContent = '--฿';
            bookingServiceFeeEl.textContent = '--฿';
            bookingDurationWrapper.classList.add('hidden');
            bookingHoursLabel.textContent = '';

            btnBookNow.disabled = true;
            btnBookNow.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-300', 'text-gray-600');
            btnBookNow.classList.remove('bg-[var(--primary)]', 'text-white', 'hover:bg-teal-700');

            // Auto-clear items if invalid
            if (selectedItems.size > 0) {
                clearAllItems();
            }
        }
    }

    function updateBookingUI() {
        // NEW: Reset logic when mode changes
        clearAllItems();

        // Reset Inputs
        if (startTimeSelect) {
            startTimeSelect.value = '';
            // Rebuild might be needed if date changed? No, just reset selection
            clearSelectOptions(startTimeSelect, '— เลือกเวลาเริ่มต้น —');
            buildTimeOptionsForDate(dateInput.value); // Re-init start values
        }
        if (endTimeSelect) {
            clearSelectOptions(endTimeSelect, '— เลือกเวลาสิ้นสุด —');
            endTimeSelect.disabled = true;
        }
        if (startDateInput) startDateInput.value = '';
        if (endDateInput) endDateInput.value = '';

        const bookingType = bookingTypeSelect.value;
        if (bookingType === 'daily') {
            hourlyBookingFields.classList.add('hidden');
            dailyBookingFields.classList.remove('hidden');
            if (timeHelpText) timeHelpText.textContent = 'กรุณาเลือกช่วงวันที่ที่ต้องการจอง';
        } else { // hourly
            hourlyBookingFields.classList.remove('hidden');
            dailyBookingFields.classList.add('hidden');
            if (timeHelpText) timeHelpText.textContent = 'สามารถจองได้เป็นช่วงชั่วโมงเต็ม และไม่สามารถเลือกเวลาที่ผ่านมาแล้วในวันนี้ได้';
        }

        updateTotalCalculation();
    }

    // --- Initialization & Event Listeners ---
    const hourlyPrice = getPriceFromElement(infoBoxPriceHourEl);
    const dailyPrice = getPriceFromElement(infoBoxPriceDayEl);
    const hourlyOption = bookingTypeSelect.querySelector('option[value="hourly"]');
    const dailyOption = bookingTypeSelect.querySelector('option[value="daily"]');
    let initialSelectedValue = 'hourly';

    if (hourlyOption && (!hourlyPrice || hourlyPrice <= 0)) {
        hourlyOption.remove();
        if (dailyOption && (dailyPrice > 0)) {
            initialSelectedValue = 'daily';
        }
    }
    if (dailyOption && (!dailyPrice || dailyPrice <= 0)) {
        dailyOption.remove();
    }

    if (bookingTypeSelect.options.length > 0) {
        bookingTypeSelect.value = initialSelectedValue;
        if (bookingTypeSelect.value !== initialSelectedValue) {
            bookingTypeSelect.value = bookingTypeSelect.options[0].value;
        }
    }

    if (bookingTypeSelect.options.length === 0) {
        const bookingSection = document.getElementById('bookingTypeSelect').closest('.space-y-3');
        if (bookingSection) {
            bookingSection.innerHTML = '<p class="text-sm text-gray-500">ไม่มีประเภทการจองที่ว่างสำหรับสนามนี้</p>';
        }
    } else {
        updateBookingUI();
    }

    // Restore State from Cart (if available)
    if (window.cartData) {
        try {
            const cart = window.cartData;

            // 1. Restore Booking Type & Times
            if (cart.booking_type && bookingTypeSelect.querySelector(`option[value="${cart.booking_type}"]`)) {
                bookingTypeSelect.value = cart.booking_type;
                updateBookingUI(); // Switch mode

                if (cart.booking_type === 'hourly') {
                    if (cart.booking_date) {
                        dateInput.value = cart.booking_date;
                        buildTimeOptionsForDate(cart.booking_date);
                    }
                    if (cart.time_start) {
                        startTimeSelect.value = cart.time_start;
                        rebuildEndTimeOptions();
                    }
                    if (cart.time_end) {
                        endTimeSelect.value = cart.time_end;
                    }
                } else {
                    // Daily
                    if (cart.start_date) startDateInput.value = cart.start_date;
                    if (cart.end_date) endDateInput.value = cart.end_date;
                }
            }

            // 2. Restore Items
            if (cart.items && Array.isArray(cart.items)) {
                // We need to wait for DOM to be ready? We are in DOMContentLoaded.
                // The buttons are in the DOM.
                cart.items.forEach(cItem => {
                    // Find the original button
                    // We need a selector. Id is in cItem.id.
                    // cItem might store id as 'id' or 'item_id'? CartController saves what is sent. 
                    // Show.js sends {id, ...}. So likely 'id'.
                    const itemId = cItem.id;
                    const addBtn = document.querySelector(`.add-item-btn[data-item-id="${itemId}"]`);

                    if (addBtn) {
                        // Manually simulate adding
                        const name = cItem.name || addBtn.dataset.itemName;
                        const price = parseFloat(cItem.price || addBtn.dataset.itemPrice);
                        const qty = parseInt(cItem.qty || 1);
                        const unit = cItem.unit || addBtn.dataset.itemUnit || 'ชิ้น';
                        const image = cItem.image || addBtn.dataset.itemImage;

                        selectedItems.set(parseInt(itemId), {
                            id: parseInt(itemId), // Ensure ID is present
                            name,
                            price,
                            qty: qty,
                            unit: unit,
                            image: image,
                            originalBtn: addBtn.cloneNode(true)
                        });

                        // Replace UI
                        const controlDiv = document.createElement('div');
                        controlDiv.className = 'flex items-center gap-2';
                        controlDiv.dataset.itemControlId = itemId;
                        controlDiv.innerHTML = `
                            <button type="button" class="btn-decrease-item w-7 h-7 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 flex items-center justify-center font-bold" data-id="${itemId}">-</button>
                            <span class="text-sm font-semibold text-gray-900 w-6 text-center item-qty-display" data-id="${itemId}">${qty}</span>
                            <button type="button" class="btn-increase-item w-7 h-7 rounded-lg bg-[var(--primary)] text-white hover:bg-teal-700 flex items-center justify-center font-bold" data-id="${itemId}">+</button>
                        `;
                        addBtn.replaceWith(controlDiv);
                    }
                });
                renderCartSummary();
            }

            updateTotalCalculation();

        } catch (e) {
            console.error("Failed to restore cart state", e);
        }
    }


    // --- Login Panel Logic ---
    const loginPanel = document.getElementById('loginPanel');
    const loginBackdrop = document.getElementById('loginBackdrop');
    const loginOverlayClose = document.querySelector('[data-login-overlay-close]');

    function showLoginPanel() {
        if (loginPanel && loginBackdrop) {
            loginPanel.classList.remove('hidden');
            loginBackdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function hideLoginPanel() {
        if (loginPanel && loginBackdrop) {
            loginPanel.classList.add('hidden');
            loginBackdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    if (loginOverlayClose) {
        loginOverlayClose.addEventListener('click', hideLoginPanel);
    }
    if (loginBackdrop) {
        loginBackdrop.addEventListener('click', hideLoginPanel);
    }
    // --- End Login Panel Logic ---


    // Add click handler for Book Now to validate and handle login
    btnBookNow.addEventListener('click', function (e) {
        // Always prevent default to handle logic here
        e.preventDefault();

        // 1. Check if user is logged in
        if (!window.IS_LOGGED_IN) {
            showLoginPanel();
            return; // Stop execution, just show login panel
        }

        // 2. If logged in, check if button is disabled (e.g., no valid time selected)
        if (btnBookNow.disabled) {
            return; // Do nothing if button is disabled
        }

        // 3. If logged in and button is enabled, populate fields and submit
        const bookingSubmitForm = document.getElementById('bookingSubmitForm');
        const bookingType = bookingTypeSelect.value;
        document.getElementById('bookingTypeField').value = bookingType;

        if (bookingType === 'hourly') {
            const hours = getDurationHours();
            const price = getPriceFromElement(infoBoxPriceHourEl);
            const totalField = hours * price;

            document.getElementById('bookingDateField').value = dateInput.value;
            document.getElementById('bookingTimeStartField').value = startTimeSelect.value;
            document.getElementById('bookingTimeEndField').value = endTimeSelect.value;
            document.getElementById('bookingHoursField').value = hours;
            document.getElementById('bookingPricePerHourField').value = price;
            document.getElementById('bookingBasePriceField').value = totalField;

            // Clear daily fields
            document.getElementById('bookingStartDateField').value = '';
            document.getElementById('bookingEndDateField').value = '';
            document.getElementById('bookingDaysField').value = '';
            document.getElementById('bookingPricePerDayField').value = '';

        } else { // Daily
            const days = getDurationDays();
            const price = getPriceFromElement(infoBoxPriceDayEl);
            const totalField = days * price;

            document.getElementById('bookingStartDateField').value = startDateInput.value;
            document.getElementById('bookingEndDateField').value = endDateInput.value;
            document.getElementById('bookingDaysField').value = days;
            document.getElementById('bookingPricePerDayField').value = price;
            document.getElementById('bookingBasePriceField').value = totalField;

            // Clear hourly fields
            document.getElementById('bookingDateField').value = '';
            document.getElementById('bookingTimeStartField').value = '';
            document.getElementById('bookingTimeEndField').value = '';
            document.getElementById('bookingHoursField').value = '';
            document.getElementById('bookingPricePerHourField').value = '';
        }

        const itemsArray = Array.from(selectedItems.values()).map(item => ({
            id: item.id,
            name: item.name,
            price: item.price,
            qty: item.qty,
            image: item.image
        }));
        document.getElementById('bookingItemsField').value = JSON.stringify(itemsArray);

        bookingSubmitForm.submit();
    });


    // Listeners
    bookingTypeSelect.addEventListener('change', updateBookingUI);
    dateInput.addEventListener('change', () => { buildTimeOptionsForDate(dateInput.value); updateTotalCalculation(); });
    startTimeSelect.addEventListener('change', () => { rebuildEndTimeOptions(); updateTotalCalculation(); });
    endTimeSelect.addEventListener('change', updateTotalCalculation);
    startDateInput.addEventListener('change', () => {
        if (startDateInput.value) {
            endDateInput.min = startDateInput.value;
            if (endDateInput.value && endDateInput.value < startDateInput.value) endDateInput.value = '';
        }
        updateTotalCalculation();
    });
    endDateInput.addEventListener('change', updateTotalCalculation);

    // Initial setup for date inputs
    const todayStr = new Date().toISOString().split('T')[0];
    if (!dateInput.value) dateInput.value = todayStr;
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    startDateInput.min = tomorrow.toISOString().split('T')[0];

    // Only set default if NOT restored
    if (!window.cartData) {
        buildTimeOptionsForDate(dateInput.value);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const card = document.getElementById('bookingSummaryCard');
    if (!card) return;

    // If the parent section has pointer-events-none (maintenance mode), disable sticky.
    if (card.closest('.pointer-events-none')) {
        return;
    }

    const originalParent = card.parentNode;
    const placeholder = document.createElement('div');

    let isFixed = false;
    let cardTop = 0;
    const offsetTop = 16; // ระยะห่างจากขอบบนตอนลอย

    function setup() {
        // วัดตำแหน่งการ์ดตอนอยู่ปกติ (placeholder ยังสูง 0)
        const rect = card.getBoundingClientRect();

        placeholder.style.width = rect.width + 'px';
        placeholder.style.height = '0px';
        placeholder.style.visibility = 'hidden';
        placeholder.style.pointerEvents = 'none';

        if (!placeholder.parentNode) {
            originalParent.insertBefore(placeholder, card);
        }

        cardTop = rect.top + window.scrollY;
    }

    function makeFixed() {
        if (isFixed) return;

        // ตอนจะลอย ค่อยให้ placeholder สูงเท่าการ์ด
        const rect = card.getBoundingClientRect();
        placeholder.style.height = rect.height + 'px';

        card.style.position = 'fixed';
        card.style.top = offsetTop + 'px';
        card.style.left = rect.left + 'px';
        card.style.width = rect.width + 'px';
        card.style.zIndex = 60;

        document.body.appendChild(card); // หลุดจากกรอบ overflow-hidden
        isFixed = true;
    }

    function resetPosition() {
        if (!isFixed) return;

        card.style.position = '';
        card.style.top = '';
        card.style.left = '';
        card.style.width = '';
        card.style.zIndex = '';

        // กลับสภาพปกติ: placeholder ไม่ต้องกินที่แล้ว
        placeholder.style.height = '0px';

        originalParent.insertBefore(card, placeholder);
        isFixed = false;
    }

    function onScroll() {
        const y = window.scrollY || document.documentElement.scrollTop;

        if (y >= cardTop - offsetTop) {
            makeFixed();      // ชนเพดาน → ลอยตาม
        } else {
            resetPosition();  // เลื่อนกลับขึ้น → กลับรังเดิม
        }
    }

    setup();
    window.addEventListener('scroll', onScroll);

    window.addEventListener('resize', function () {
        if (!isFixed) {
            setup(); // วัดใหม่เฉพาะตอนที่การ์ดยังไม่ลอย
        }
    });
});