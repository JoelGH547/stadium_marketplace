document.addEventListener('DOMContentLoaded', function () {
    // ====== อ่านข้อมูลหลักจาก article ======
    const article = document.getElementById('stadiumDetail');
    let openTimeRaw = '';
    let closeTimeRaw = '';
    let pricePerHour = 0;

    if (article) {
        openTimeRaw = (article.dataset.openTime || '').trim();
        closeTimeRaw = (article.dataset.closeTime || '').trim();
        pricePerHour = parseFloat(article.dataset.priceHour || '0') || 0;
    }

    // ====== 2) วันที่ + เวลาเริ่ม/สิ้นสุด ======
    const dateInput = document.getElementById('bookingDate');
    const startSelect = document.getElementById('startTimeSelect');
    const endSelect = document.getElementById('endTimeSelect');
    const timeHelpText = document.getElementById('timeHelpText');
    const timeErrorText = document.getElementById('timeErrorText');

    function getLocalYMD(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    const todayStr = getLocalYMD(new Date());

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

    function buildTimeOptionsForDate(dateStr) {
        if (!startSelect || !endSelect) return;

        clearSelectOptions(startSelect, '— เลือกเวลาเริ่มต้น —');
        clearSelectOptions(endSelect, '— เลือกเวลาสิ้นสุด —');
        startSelect.disabled = false;
        endSelect.disabled = true;

        if (!dateStr) {
            if (timeErrorText) {
                timeErrorText.textContent = '';
                timeErrorText.classList.add('hidden');
            }
            return;
        }

        let openMinutes = parseTimeToMinutes(openTimeRaw);
        let closeMinutes = parseTimeToMinutes(closeTimeRaw);

        // fallback ถ้าตั้งค่าเวลาเปิดปิดไม่ถูกต้อง
        if (openMinutes === null || closeMinutes === null || closeMinutes <= openMinutes) {
            openMinutes = 9 * 60;
            closeMinutes = 22 * 60;
        }

        let startBase = openMinutes;
        if (dateStr === todayStr) {
            const now = new Date();
            const currentMinutes = now.getHours() * 60 + now.getMinutes();
            const nextHour = (Math.floor(currentMinutes / 60) + 1) * 60;
            if (nextHour > startBase) {
                startBase = nextHour;
            }
        }

        if (startBase > closeMinutes - 60) {
            startSelect.disabled = true;
            endSelect.disabled = true;
            if (timeErrorText) {
                timeErrorText.textContent =
                    dateStr === todayStr
                        ? 'วันนี้เลยเวลาเปิดให้จองแล้ว ไม่สามารถจองเพิ่มเติมได้'
                        : 'ไม่พบช่วงเวลาที่สามารถจองได้';
                timeErrorText.classList.remove('hidden');
            }
            return;
        }

        if (timeErrorText) {
            timeErrorText.textContent = '';
            timeErrorText.classList.add('hidden');
        }

        if (timeHelpText) {
            timeHelpText.textContent = 'เลือกเวลาเริ่มต้นและเวลาสิ้นสุดตามช่วงที่สนามเปิดให้บริการ';
        }

        for (let t = startBase; t <= closeMinutes - 60; t += 60) {
            const label = minutesToLabel(t);
            const opt = document.createElement('option');
            opt.value = label;
            opt.textContent = label;
            startSelect.appendChild(opt);
        }
    }

    function rebuildEndTimeOptions() {
        if (!startSelect || !endSelect) return;

        const startVal = startSelect.value;
        clearSelectOptions(endSelect, '— เลือกเวลาสิ้นสุด —');

        if (!startVal) {
            endSelect.disabled = true;
            return;
        }

        let openMinutes = parseTimeToMinutes(openTimeRaw);
        let closeMinutes = parseTimeToMinutes(closeTimeRaw);
        if (openMinutes === null || closeMinutes === null || closeMinutes <= openMinutes) {
            openMinutes = 9 * 60;
            closeMinutes = 22 * 60;
        }

        const startMinutes = parseTimeToMinutes(startVal);
        if (startMinutes === null) {
            endSelect.disabled = true;
            return;
        }

        const minEnd = startMinutes + 60;
        if (minEnd > closeMinutes) {
            endSelect.disabled = true;
            return;
        }

        for (let t = minEnd; t <= closeMinutes; t += 60) {
            const label = minutesToLabel(t);
            const opt = document.createElement('option');
            opt.value = label;
            opt.textContent = label;
            endSelect.appendChild(opt);
        }
        endSelect.disabled = false;
    }

    if (dateInput && startSelect && endSelect) {
        const minAttr = dateInput.getAttribute('min');
        const maxAttr = dateInput.getAttribute('max');
        if (!dateInput.value) {
            if ((!minAttr || todayStr >= minAttr) && (!maxAttr || todayStr <= maxAttr)) {
                dateInput.value = todayStr;
            }
        }

        buildTimeOptionsForDate(dateInput.value);

        dateInput.addEventListener('change', function () {
            buildTimeOptionsForDate(dateInput.value);
            syncBookingUI();
        });

        startSelect.addEventListener('change', function () {
            rebuildEndTimeOptions();
            syncBookingUI();
        });

        endSelect.addEventListener('change', function () {
            syncBookingUI();
        });
    }

    // ====== 3) ปุ่ม show schedule ======
    const btnShowSchedule = document.getElementById('btnShowSchedule');
    if (btnShowSchedule) {
        btnShowSchedule.addEventListener('click', function () {
            if (dateInput && (!dateInput.value || dateInput.value.trim() === '')) {
                alert('กรุณาเลือกวันที่ต้องการจอง');
                return;
            }
            if (startSelect && endSelect && (!startSelect.value || !endSelect.value)) {
                alert('กรุณาเลือกเวลาเริ่มต้นและเวลาสิ้นสุด');
                return;
            }

            const baseUrl = this.dataset.baseUrl || '';
            if (!baseUrl) return;

            // ตัดการส่ง field ออกแล้ว ใช้ baseUrl ตรง ๆ
            window.location.href = baseUrl;
        });
    }

    // ====== 4) สรุปราคา + ปุ่ม "จองเลย" + ตะกร้าไอเทม ======
    const bookingHoursLabel = document.getElementById('bookingHoursLabel');
    const bookingFieldPrice = document.getElementById('bookingFieldPrice');
    const bookingServiceFee = document.getElementById('bookingServiceFee');
    const bookBtn = document.getElementById('btnBookNow');
    const bookingItemsSummary = document.getElementById('bookingItemsSummary');
    const bookingItemsList = document.getElementById('bookingItemsList');
    const bookingForm = document.getElementById('bookingSubmitForm');
    const bookingDateField = document.getElementById('bookingDateField');
    const bookingTimeStartField = document.getElementById('bookingTimeStartField');
    const bookingTimeEndField = document.getElementById('bookingTimeEndField');
    const bookingHoursField = document.getElementById('bookingHoursField');
    const bookingItemsField = document.getElementById('bookingItemsField');
    const bookingPricePerHourField = document.getElementById('bookingPricePerHourField');
    const bookingBasePriceField = document.getElementById('bookingBasePriceField');

    const itemButtons = document.querySelectorAll('.add-item-btn');

    let cartItems = [];
    let cartTotal = 0;

    function updateItemsSummary() {
        if (!bookingItemsSummary) return;
        if (!cartItems.length) {
            bookingItemsSummary.textContent = 'ยังไม่ได้เลือกไอเทม';
            return;
        }
        const totalQty = cartItems.reduce(function (sum, item) {
            return sum + item.qty;
        }, 0);
        bookingItemsSummary.textContent =
            totalQty +
            ' รายการ — ' +
            cartTotal.toLocaleString('th-TH', { maximumFractionDigits: 2 }) +
            '฿';
    }

    function renderItemsList() {
        if (!bookingItemsList) return;

        while (bookingItemsList.firstChild) {
            bookingItemsList.removeChild(bookingItemsList.firstChild);
        }

        if (!cartItems.length) {
            const li = document.createElement('li');
            li.className = 'text-xs text-gray-400';
            li.textContent = 'ยังไม่มีไอเทมในตะกร้า';
            bookingItemsList.appendChild(li);
            return;
        }

        cartItems.forEach(function (item) {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between text-xs text-gray-700';

            const left = document.createElement('div');
            left.className = 'flex-1';
            left.textContent =
                item.name +
                ' x' +
                item.qty +
                ' — ' +
                (item.price * item.qty).toLocaleString('th-TH', { maximumFractionDigits: 2 }) +
                '฿';

            const right = document.createElement('div');
            right.className = 'ml-2 flex items-center gap-1';

            const btnDec = document.createElement('button');
            btnDec.type = 'button';
            btnDec.textContent = '−';
            btnDec.className =
                'h-6 w-6 rounded border border-gray-300 text-gray-700 hover:bg-gray-100';
            btnDec.dataset.action = 'dec';
            btnDec.dataset.itemId = item.id;

            const btnInc = document.createElement('button');
            btnInc.type = 'button';
            btnInc.textContent = '+';
            btnInc.className =
                'h-6 w-6 rounded border border-gray-300 text-gray-700 hover:bg-gray-100';
            btnInc.dataset.action = 'inc';
            btnInc.dataset.itemId = item.id;

            const btnRemove = document.createElement('button');
            btnRemove.type = 'button';
            btnRemove.textContent = 'ลบ';
            btnRemove.className = 'text-[10px] text-red-500 hover:underline';
            btnRemove.dataset.action = 'remove';
            btnRemove.dataset.itemId = item.id;

            right.appendChild(btnDec);
            right.appendChild(btnInc);
            right.appendChild(btnRemove);

            li.appendChild(left);
            li.appendChild(right);

            bookingItemsList.appendChild(li);
        });
    }

    function recalcCartTotal() {
        cartTotal = cartItems.reduce(function (sum, item) {
            return sum + item.price * item.qty;
        }, 0);
        updateItemsSummary();
        renderItemsList();
    }

    if (itemButtons && itemButtons.length > 0) {
        itemButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const id = btn.dataset.itemId;
                const name = btn.dataset.itemName || '';
                const price = parseFloat(btn.dataset.itemPrice || '0') || 0;

                if (!id || !price) {
                    return;
                }

                const found = cartItems.find(function (it) {
                    return it.id === id;
                });

                if (found) {
                    found.qty += 1;
                } else {
                    cartItems.push({
                        id: id,
                        name: name,
                        price: price,
                        qty: 1
                    });
                }

                recalcCartTotal();
                syncBookingUI();
            });
        });
    }

    if (bookingItemsList) {
        bookingItemsList.addEventListener('click', function (e) {
            const target = e.target;
            if (!target.dataset || !target.dataset.action) return;

            const action = target.dataset.action;
            const id = target.dataset.itemId;
            if (!id) return;

            const foundIndex = cartItems.findIndex(function (it) {
                return it.id === id;
            });
            if (foundIndex === -1) return;

            const item = cartItems[foundIndex];

            if (action === 'inc') {
                item.qty += 1;
            } else if (action === 'dec') {
                item.qty -= 1;
                if (item.qty <= 0) {
                    cartItems.splice(foundIndex, 1);
                }
            } else if (action === 'remove') {
                cartItems.splice(foundIndex, 1);
            }

            recalcCartTotal();
            syncBookingUI();
        });
    }

    function setBookingDefault() {
        // ล้างข้อมูลตะกร้า + รีเซ็ต UI เมื่อสถานะการจองยังไม่พร้อม
        cartItems = [];
        cartTotal = 0;

        if (bookingHoursLabel) {
            bookingHoursLabel.textContent = 'ต่อชั่วโมง';
        }
        if (bookingFieldPrice) {
            bookingFieldPrice.textContent = '--฿';
        }
        if (bookingServiceFee) {
            bookingServiceFee.textContent = '--฿';
        }
        if (bookingItemsSummary) {
            bookingItemsSummary.textContent = 'ยังไม่ได้เลือกไอเทม';
        }
        if (bookingItemsList) {
            while (bookingItemsList.firstChild) {
                bookingItemsList.removeChild(bookingItemsList.firstChild);
            }
            const li = document.createElement('li');
            li.className = 'text-xs text-gray-400';
            li.textContent = 'ยังไม่มีไอเทมในตะกร้า';
            bookingItemsList.appendChild(li);
        }
        if (bookBtn) {
            bookBtn.disabled = true;
            bookBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function enableBookBtn() {
        if (!bookBtn) return;
        bookBtn.disabled = false;
        bookBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    function getDurationHours() {
        if (!startSelect || !endSelect) return 0;
        const startVal = startSelect.value;
        const endVal = endSelect.value;
        const startM = parseTimeToMinutes(startVal);
        const endM = parseTimeToMinutes(endVal);
        if (startM === null || endM === null) return 0;
        const diff = endM - startM;
        if (diff <= 0) return 0;
        return diff / 60;
    }

    function updateItemButtonsState(ready) {
        if (!itemButtons || !itemButtons.length) return;
        itemButtons.forEach(function (btn) {
            btn.disabled = !ready;
            if (ready) {
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    }

    function syncBookingUI() {
        if (!bookingFieldPrice && !bookingServiceFee && !bookBtn && !bookingHoursLabel) {
            return;
        }

        let ready = true;

        // ไม่เช็ค fieldSelect แล้ว (ตัดระบบเลือกสนามย่อยออก)

        if (!dateInput || !dateInput.value) {
            ready = false;
        }

        if (!startSelect || !endSelect || !startSelect.value || !endSelect.value) {
            ready = false;
        }

        const hours = getDurationHours();
        if (hours <= 0) {
            ready = false;
        }

        if (!pricePerHour || pricePerHour <= 0) {
            ready = false;
        }

        if (!ready) {
            setBookingDefault();
            updateItemButtonsState(false);
            return;
        }

        const basePrice = hours * pricePerHour;
        const bookingPrice = basePrice + cartTotal;
        const serviceFee = bookingPrice * 0.05;

        if (bookingHoursLabel) {
            bookingHoursLabel.textContent = hours + ' ชั่วโมง';
        }

        if (bookingFieldPrice) {
            bookingFieldPrice.textContent =
                bookingPrice.toLocaleString('th-TH', { maximumFractionDigits: 2 }) + '฿';
        }

        if (bookingServiceFee) {
            bookingServiceFee.textContent =
                serviceFee.toLocaleString('th-TH', { maximumFractionDigits: 2 }) + '฿';
        }

        enableBookBtn();
        updateItemButtonsState(true);
    }

    setBookingDefault();
    updateItemsSummary();
    renderItemsList();
    syncBookingUI();
    updateItemButtonsState(false);

    if (bookBtn && bookingForm) {
        bookBtn.addEventListener('click', function (e) {
            if (bookBtn.disabled) {
                e.preventDefault();
                return;
            }

            // กันกรณี JS พลาด ไม่เจอวันที่/เวลา
            if (!dateInput || !startSelect || !endSelect) {
                e.preventDefault();
                return;
            }

            // กรอกค่าลง hidden
            if (bookingDateField) {
                bookingDateField.value = dateInput.value || '';
            }
            if (bookingTimeStartField) {
                bookingTimeStartField.value = startSelect.value || '';
            }
            if (bookingTimeEndField) {
                bookingTimeEndField.value = endSelect.value || '';
            }

            if (typeof getDurationHours === 'function') {
                const hrs = getDurationHours() || 0;

                if (bookingHoursField) {
                    bookingHoursField.value = String(hrs);
                }

                if (bookingBasePriceField) {
                    const basePrice = hrs * (pricePerHour || 0);
                    bookingBasePriceField.value = String(basePrice);
                }
            }

            if (bookingPricePerHourField) {
                bookingPricePerHourField.value = String(pricePerHour || 0);
            }

            if (bookingItemsField) {
                const payloadItems = Array.isArray(cartItems)
                    ? cartItems.map(function (it) {
                        return {
                            item_id: it.id || null,
                            item_name: it.name || '',
                            unit: it.unit || '',
                            qty: it.qty || 0,
                            price: it.price || 0
                        };
                    })
                    : [];
                bookingItemsField.value = JSON.stringify(payloadItems);
            }

            bookingForm.submit();
        });
    }
});
