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
    const infoBoxPriceHourEl = document.getElementById('infoBoxPriceHour');
    const infoBoxPriceDayEl = document.getElementById('infoBoxPriceDay');

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
    function updateHourlyPrice() {
        const hours = getDurationHours();
        const pricePerHour = getPriceFromElement(infoBoxPriceHourEl);

        // For now, price calculation is simplified to focus on the label.
        // A full implementation would also consider cart items.
        if (hours > 0 && pricePerHour > 0) {
            const basePrice = hours * pricePerHour;
            const serviceFee = basePrice * 0.05;
            bookingFieldPriceEl.textContent = `${basePrice.toLocaleString()}฿`;
            bookingServiceFeeEl.textContent = `${serviceFee.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}฿`;
            bookingHoursLabel.textContent = `${hours} ชั่วโมง`;
            bookingDurationWrapper.classList.remove('hidden');
        } else {
            bookingFieldPriceEl.textContent = '--฿';
            bookingServiceFeeEl.textContent = '--฿';
            bookingDurationWrapper.classList.add('hidden');
            bookingHoursLabel.textContent = '';
        }
    }

    function updateDailyPrice() {
        const dailyPrice = getPriceFromElement(infoBoxPriceDayEl);
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        if (startDate && endDate && dailyPrice > 0) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            if (end < start) {
                bookingFieldPriceEl.textContent = '--฿';
                bookingServiceFeeEl.textContent = '--฿';
                bookingDurationWrapper.classList.add('hidden');
                bookingHoursLabel.textContent = '';
                return;
            }
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            const totalFieldPrice = diffDays * dailyPrice;
            const serviceFee = totalFieldPrice * 0.05;
            bookingFieldPriceEl.textContent = `${totalFieldPrice.toLocaleString()}฿`;
            bookingServiceFeeEl.textContent = `${serviceFee.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}฿`;
            bookingHoursLabel.textContent = `${diffDays} วัน`;
            bookingDurationWrapper.classList.remove('hidden');
        } else {
            bookingFieldPriceEl.textContent = '--฿';
            bookingServiceFeeEl.textContent = '--฿';
            bookingDurationWrapper.classList.add('hidden');
            bookingHoursLabel.textContent = '';
        }
    }

    function updateBookingUI() {
        const bookingType = bookingTypeSelect.value;
        if (bookingType === 'daily') {
            hourlyBookingFields.classList.add('hidden');
            dailyBookingFields.classList.remove('hidden');
            if (timeHelpText) timeHelpText.textContent = 'กรุณาเลือกช่วงวันที่ที่ต้องการจอง';
            updateDailyPrice();
        } else { // hourly
            hourlyBookingFields.classList.remove('hidden');
            dailyBookingFields.classList.add('hidden');
            if (timeHelpText) timeHelpText.textContent = 'สามารถจองได้เป็นช่วงชั่วโมงเต็ม และไม่สามารถเลือกเวลาที่ผ่านมาแล้วในวันนี้ได้';
            updateHourlyPrice();
        }
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

    // Listeners
    bookingTypeSelect.addEventListener('change', updateBookingUI);
    dateInput.addEventListener('change', () => { buildTimeOptionsForDate(dateInput.value); updateHourlyPrice(); });
    startTimeSelect.addEventListener('change', () => { rebuildEndTimeOptions(); updateHourlyPrice(); });
    endTimeSelect.addEventListener('change', updateHourlyPrice);
    startDateInput.addEventListener('change', () => {
        if (startDateInput.value) {
            endDateInput.min = startDateInput.value;
            if (endDateInput.value && endDateInput.value < startDateInput.value) endDateInput.value = '';
        }
        updateDailyPrice();
    });
    endDateInput.addEventListener('change', updateDailyPrice);

    // Initial setup for date inputs
    const todayStr = new Date().toISOString().split('T')[0];
    if (!dateInput.value) dateInput.value = todayStr;
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    startDateInput.min = tomorrow.toISOString().split('T')[0];

    buildTimeOptionsForDate(dateInput.value);
});

document.addEventListener('DOMContentLoaded', function () {
    const card = document.getElementById('bookingSummaryCard');
    if (!card) return;

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


