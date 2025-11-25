document.addEventListener('DOMContentLoaded', function () {
    // ====== Booking: อ่านข้อมูลจาก article (เวลาเปิด-ปิด + ราคา/ชม.) ======
    const article = document.getElementById('stadiumDetail');
    let openTimeRaw = '';
    let closeTimeRaw = '';
    let pricePerHour = 0;

    if (article) {
        openTimeRaw = (article.dataset.openTime || '').trim();
        closeTimeRaw = (article.dataset.closeTime || '').trim();
        pricePerHour = parseFloat(article.dataset.priceHour || '0') || 0;
    }

    // ====== 1. เลือกสนามย่อย + tooltip ======
    const fieldSelect = document.getElementById('stadiumFieldSelect');
    const btnShowSchedule = document.getElementById('btnShowSchedule');
    const infoBtn = document.getElementById('fieldInfoBtn');
    const tooltip = document.getElementById('fieldInfoTooltip');
    const tooltipTitle = document.getElementById('fieldInfoTitle');
    const tooltipBody = document.getElementById('fieldInfoBody');
    const statusLabel = document.getElementById('fieldStatusLabel');

    function updateFieldUI() {
        if (!fieldSelect) return;

        const opt = fieldSelect.options[fieldSelect.selectedIndex];
        if (!opt || !opt.value) {
            if (statusLabel) {
                statusLabel.textContent = '';
                statusLabel.className = 'text-xs font-medium text-gray-500';
            }
            if (infoBtn) {
                infoBtn.disabled = true;
            }
            if (tooltipTitle) tooltipTitle.textContent = '';
            if (tooltipBody) tooltipBody.textContent = '';
            if (tooltip) tooltip.classList.add('hidden');
            return;
        }

        const name = opt.dataset.name || opt.textContent || '';
        const desc = opt.dataset.desc || '';
        const status = (opt.dataset.status || 'active').toLowerCase();

        if (tooltipTitle) tooltipTitle.textContent = name;
        if (tooltipBody) tooltipBody.textContent = desc || 'ไม่มีรายละเอียดเพิ่มเติม';

        if (infoBtn) {
            infoBtn.disabled = !desc;
        }

        if (!statusLabel) return;

        if (status === 'active') {
            statusLabel.textContent = 'สถานะ: เปิดให้จอง';
            statusLabel.className = 'text-xs font-semibold text-emerald-600';
        } else {
            statusLabel.textContent = 'สถานะ: ปิดปรับปรุงชั่วคราว';
            statusLabel.className = 'text-xs font-semibold text-amber-600';
        }
    }

    if (fieldSelect) {
        fieldSelect.addEventListener('change', () => {
            updateFieldUI();
            syncBookingUI();
        });
        updateFieldUI();
    }

    if (infoBtn && tooltip) {
        infoBtn.addEventListener('mouseenter', () => {
            if (infoBtn.disabled) return;
            if (!tooltipBody || !tooltipBody.textContent.trim()) return;
            tooltip.classList.remove('hidden');
        });
        infoBtn.addEventListener('mouseleave', () => {
            tooltip.classList.add('hidden');
        });
    }

    // ====== 2. เลือกวันที่และเวลา (ตามกติกา) ======
    const dateInput = document.getElementById('bookingDate');
    const startSelect = document.getElementById('startTimeSelect');
    const endSelect = document.getElementById('endTimeSelect');
    const timeHelpText = document.getElementById('timeHelpText');
    const timeErrorText = document.getElementById('timeErrorText');

    function getLocalYMD(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
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
        return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
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

        dateInput.addEventListener('change', () => {
            buildTimeOptionsForDate(dateInput.value);
            syncBookingUI();
        });

        startSelect.addEventListener('change', () => {
            rebuildEndTimeOptions();
            syncBookingUI();
        });

        endSelect.addEventListener('change', () => {
            syncBookingUI();
        });
    }

    // ====== 3. ปุ่ม show schedule (ใช้ field + วันเวลา) ======
    if (btnShowSchedule && fieldSelect) {
        btnShowSchedule.addEventListener('click', function () {
            const opt = fieldSelect.options[fieldSelect.selectedIndex];
            if (!opt || !opt.value) {
                alert('กรุณาเลือกสนามย่อย (court) ก่อนดูตารางเวลา');
                return;
            }

            const status = (opt.dataset.status || 'active').toLowerCase();
            if (status !== 'active') {
                alert('สนามย่อยนี้กำลังปิดปรับปรุง กรุณาเลือกสนามอื่น');
                return;
            }

            if (dateInput && (!dateInput.value || dateInput.value.trim() === '')) {
                alert('กรุณาเลือกวันที่ต้องการจอง');
                return;
            }
            if (startSelect && endSelect && (!startSelect.value || !endSelect.value)) {
                alert('กรุณาเลือกเวลาเริ่มต้นและเวลาสิ้นสุด');
                return;
            }

            const baseUrl = this.dataset.baseUrl || '';
            const fieldId = opt.value;
            if (!baseUrl) return;

            const url = baseUrl + '?field=' + encodeURIComponent(fieldId);
            window.location.href = url;
        });
    }

    // ====== 4. Summary การจอง + ปุ่ม "จองเลย" ======
    // หมายเหตุ: ต้องมี element เหล่านี้ใน HTML ตาม id ที่ระบุ
    // - <span id="bookingPriceLine">ค่าจองสนาม (ต่อชั่วโมง) --฿</span>
    // - <span id="bookingFeeLine">ค่าบริการ --฿</span>
    // - <button id="btnBookNow">จองเลย</button>
    const bookingPriceLine = document.getElementById('bookingPriceLine');
    const bookingFeeLine = document.getElementById('bookingFeeLine');
    const bookBtn = document.getElementById('btnBookNow');

    function setBookingDefault() {
        if (bookingPriceLine) {
            bookingPriceLine.textContent = 'ค่าจองสนาม (ต่อชั่วโมง) --฿';
        }
        if (bookingFeeLine) {
            bookingFeeLine.textContent = 'ค่าบริการ --฿';
        }
        if (bookBtn) {
            bookBtn.disabled = true;
            bookBtn.classList.add('opacity-60', 'cursor-not-allowed');
        }
    }

    function enableBookBtn() {
        if (!bookBtn) return;
        bookBtn.disabled = false;
        bookBtn.classList.remove('opacity-60', 'cursor-not-allowed');
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

    function syncBookingUI() {
        // default state
        if (!bookingPriceLine && !bookingFeeLine && !bookBtn) {
            return; // ไม่มี element ให้จัดการ ก็ไม่ต้องทำอะไร
        }

        // ตรวจว่า field จำเป็นครบหรือยัง
        let ready = true;

        // 1) ถ้ามีสนามย่อยให้เลือก ต้องไม่ใช่ option ว่าง
        if (fieldSelect) {
            const opt = fieldSelect.options[fieldSelect.selectedIndex];
            if (!opt || !opt.value) {
                ready = false;
            } else {
                const status = (opt.dataset.status || 'active').toLowerCase();
                if (status !== 'active') {
                    ready = false;
                }
            }
        }

        // 2) วันที่
        if (!dateInput || !dateInput.value) {
            ready = false;
        }

        // 3) เวลาเริ่ม/สิ้นสุด
        if (!startSelect || !endSelect || !startSelect.value || !endSelect.value) {
            ready = false;
        }

        const hours = getDurationHours();
        if (hours <= 0) {
            ready = false;
        }

        if (!ready || !pricePerHour) {
            setBookingDefault();
            return;
        }

        // คำนวณราคา
        const bookingPrice = hours * pricePerHour;
        const serviceFee = bookingPrice * 0.05;

        if (bookingPriceLine) {
            bookingPriceLine.textContent =
                `ค่าจองสนาม (${hours} ชั่วโมง) ` +
                bookingPrice.toLocaleString('th-TH', { maximumFractionDigits: 2 }) +
                '฿';
        }

        if (bookingFeeLine) {
            bookingFeeLine.textContent =
                'ค่าบริการ ' +
                serviceFee.toLocaleString('th-TH', { maximumFractionDigits: 2 }) +
                '฿';
        }

        enableBookBtn();
    }

    // ตั้งค่าตั้งต้นตอนโหลดหน้า
    setBookingDefault();

    // ปุ่ม "จองเลย" — ตอนนี้ให้เป็น hover/handler ว่าง ๆ ไว้ก่อน
    if (bookBtn) {
        bookBtn.addEventListener('click', (e) => {
            if (bookBtn.disabled) {
                e.preventDefault();
                return;
            }
            // TODO: ตรงนี้เว้นไว้ให้คุณไปต่อเอง (เช่น เปิด popup ยืนยัน / ส่งฟอร์ม ฯลฯ)
            console.log('Booking click placeholder');
        });
    }
});
