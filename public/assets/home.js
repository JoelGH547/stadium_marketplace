/* ==== Arrow buttons for #nearScroller ==== */
(function () {
  const scroller = document.getElementById('nearScroller');
  const leftBtn = document.getElementById('nearLeft');
  const rightBtn = document.getElementById('nearRight');
  if (!scroller || !leftBtn || !rightBtn) return;

  function stepSize() {
    const card = scroller.querySelector('article');
    if (!card) return 320;
    const rect = card.getBoundingClientRect();
    return Math.round(rect.width + 16);
  }

  leftBtn.addEventListener('click', () => scroller.scrollBy({ left: -stepSize(), behavior: 'smooth' }));
  rightBtn.addEventListener('click', () => scroller.scrollBy({ left: stepSize(), behavior: 'smooth' }));

  scroller.querySelectorAll('img').forEach(img => {
    img.addEventListener('dragstart', e => e.preventDefault());
  });
})();

/* ==========================================================================
   MAIN HOME PAGE LOGIC (Sorting, Distance, Filters)
   ========================================================================== */
document.addEventListener('DOMContentLoaded', () => {
  // --- Element References ---
  const nearScroller = document.getElementById('nearScroller');
  const listEl = document.getElementById('venueItems');
  const sortMenu = document.getElementById('sortMenu');
  const hourlyDateInput = document.getElementById('hourlyDate');
  const homeStartTime = document.getElementById('homeStartTime');
  const homeEndTime = document.getElementById('homeEndTime');
  const dailyStartDate = document.getElementById('dailyStartDate');
  const dailyEndDate = document.getElementById('dailyEndDate');

  if (!listEl && !nearScroller) {
    console.log('Home script: No venue list or scroller found.');
    return;
  }
  
  let userLocation = null;

  // ===============================
  // SORTING LOGIC
  // ===============================
  const buttons = sortMenu ? Array.from(sortMenu.querySelectorAll('button.sort-btn')) : [];
  let currentSortKey = (buttons.find(b => b.getAttribute('aria-selected') === 'true') || buttons[0])?.dataset?.sort || 'popular';

  function setActive(btn) {
    if (!btn) return;
    buttons.forEach(b => {
      b.classList.remove('bg-[var(--primary)]', 'text-white', 'font-semibold');
      b.classList.add('text-gray-700', 'hover:text-[var(--primary)]', 'hover:bg-[var(--primary)]/10');
      b.setAttribute('aria-selected', 'false');
    });

    btn.classList.remove('text-gray-700', 'hover:text-[var(--primary)]', 'hover:bg-[var(--primary)]/10');
    btn.classList.add('bg-[var(--primary)]', 'text-white', 'font-semibold');
    btn.setAttribute('aria-selected', 'true');
  }

  function num(v) {
    const n = parseFloat(v || '0');
    return Number.isFinite(n) ? n : 0;
  }

  function sortListBy(key, opt = { emitEvent: true }) {
    if (!listEl) return;
    const itemsAll = Array.from(listEl.querySelectorAll('li'));
    const items = itemsAll.filter(li => !li.classList.contains('is-filtered-out'));
    const filteredOut = itemsAll.filter(li => li.classList.contains('is-filtered-out'));

    // decorate with stable index for tie-break
    items.forEach((li, i) => {
      if (!li.dataset.__idx) li.dataset.__idx = String(i);
    });

    const sorted = items.slice();

    if (key === 'popular') {
      sorted.sort((a, b) => {
        const da = num(a.dataset.popular);
        const db = num(b.dataset.popular);
        if (db !== da) return db - da;
        return num(a.dataset.__idx) - num(b.dataset.__idx);
      });
    } else if (key === 'price_low') {
      sorted.sort((a, b) => {
        const pa = num(a.dataset.price);
        const pb = num(b.dataset.price);
        const na = pa > 0 ? pa : Number.POSITIVE_INFINITY;
        const nb = pb > 0 ? pb : Number.POSITIVE_INFINITY;
        if (na !== nb) return na - nb;
        return num(a.dataset.__idx) - num(b.dataset.__idx);
      });
    } else if (key === 'price_high') {
      sorted.sort((a, b) => {
        const pa = num(a.dataset.price);
        const pb = num(b.dataset.price);
        const na = pa > 0 ? pa : Number.NEGATIVE_INFINITY;
        const nb = pb > 0 ? pb : Number.NEGATIVE_INFINITY;
        if (nb !== na) return nb - na;
        return num(a.dataset.__idx) - num(b.dataset.__idx);
      });
    } else if (key === 'reviews') {
      sorted.sort((a, b) => {
        const ra = num(a.dataset.reviewCount);
        const rb = num(b.dataset.reviewCount);
        if (rb !== ra) return rb - ra;
        return num(a.dataset.__idx) - num(b.dataset.__idx);
      });
    } else if (key === 'distance') {
      // this key is mainly for event dispatching, the sorting is done in applyRanking
      // but we can add a fallback sort here just in case.
      sorted.sort((a, b) => {
        const da = num(a.dataset.distanceKm);
        const db = num(b.dataset.distanceKm);
        return da - db;
      });
    } else {
      return; // Do nothing for unknown keys
    }

    // Apply new DOM order
    sorted.forEach(li => listEl.appendChild(li));
    // Keep filtered-out items at the end (still hidden)
    filteredOut.sort((a,b)=>num(a.dataset.__idx)-num(b.dataset.__idx)).forEach(li => listEl.appendChild(li));

    if (opt && opt.emitEvent === false) return;

    // Notify other parts of the app (like the pager) that the sort order has changed
    try {
      window.dispatchEvent(new CustomEvent('sort-change', { detail: { key } }));
    } catch (_) {
      const e = document.createEvent('CustomEvent');
      e.initCustomEvent('sort-change', true, true, { key });
      window.dispatchEvent(e);
    }
  }
  
  // Set up click handlers for sort buttons
  buttons.forEach(btn => {
    btn.addEventListener('click', (ev) => {
      ev.preventDefault();
      const sortKey = btn.dataset.sort || 'popular';
      currentSortKey = sortKey;
      window.__homeCurrentSortKey = currentSortKey;
      setActive(btn);
      sortListBy(sortKey);
    });
  });

  // ===============================
  // DISTANCE CALCULATION
  // ===============================
  function haversine(lat1, lon1, lat2, lon2) {
    const toRad = (deg) => (deg * Math.PI) / 180;
    const R = 6371; // km
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  }

  function formatDistance(d) {
    if (!isFinite(d)) return '-- km.';
    return d < 1 ? (d * 1000).toFixed(0) + ' m.' : d.toFixed(1) + ' km.';
  }

  function applyRanking() {
    // Process main venue list
    if (listEl) {
      const itemsAll = Array.from(listEl.querySelectorAll('li'));
    const items = itemsAll.filter(li => !li.classList.contains('is-filtered-out'));
    const filteredOut = itemsAll.filter(li => li.classList.contains('is-filtered-out'));
      items.forEach((li) => {
        const lat = parseFloat(li.dataset.lat || '');
        const lng = parseFloat(li.dataset.lng || '');
        let dist = Number.POSITIVE_INFINITY;

        if (userLocation && !isNaN(lat) && !isNaN(lng)) {
          dist = haversine(userLocation.lat, userLocation.lng, lat, lng);
        }
        li.dataset.distanceKm = dist.toString();
        
        if (userLocation) {
          const badge = li.querySelector('.dist-badge span:last-child');
          if (badge) badge.textContent = formatDistance(dist);
        }
      });
    }

    // Process "nearby" scroller
    if (nearScroller) {
      const cards = Array.from(nearScroller.querySelectorAll('article'));
      cards.forEach((card) => {
        const lat = parseFloat(card.dataset.lat || '');
        const lng = parseFloat(card.dataset.lng || '');
        let dist = Number.POSITIVE_INFINITY;

        if (userLocation && !isNaN(lat) && !isNaN(lng)) {
          dist = haversine(userLocation.lat, userLocation.lng, lat, lng);
        }
        card.dataset.distanceKm = dist.toString();

        if (userLocation) {
          const badge = card.querySelector('.dist-badge span:last-child');
          if (badge) badge.textContent = formatDistance(dist);
        }
      });

      // Sort "nearby" scroller by distance
      const sortedCards = cards.slice().sort((a, b) => {
        const da = parseFloat(a.dataset.distanceKm || '999999');
        const db = parseFloat(b.dataset.distanceKm || '999999');
        return da - db;
      });

      sortedCards.forEach((card) => nearScroller.appendChild(card));
    }
    
    // *** FIX: Re-apply the initial/default sort AFTER distance has been calculated ***
    if (sortMenu) {
        const initialBtn = buttons.find(b => b.getAttribute('aria-selected') === 'true') || buttons[0];
        if (initialBtn) {
            const initialSortKey = initialBtn.dataset.sort || 'popular';
            // No need to call setActive, it's already set in HTML
            sortListBy(initialSortKey);
        }
    } else {
        // Fallback if there's no sort menu, just notify that the list might have changed
         try {
          window.dispatchEvent(new CustomEvent('sort-change', { detail: { key: 'distance' } }));
        } catch (_) {
          const e = document.createEvent('CustomEvent');
          e.initCustomEvent('sort-change', true, true, { key: 'distance' });
          window.dispatchEvent(e);
        }
    }
  }
  
  // Set original index for stable default sorting
  if (listEl) {
    const itemsAll = Array.from(listEl.querySelectorAll('li'));
    const items = itemsAll.filter(li => !li.classList.contains('is-filtered-out'));
    const filteredOut = itemsAll.filter(li => li.classList.contains('is-filtered-out'));
    itemsAll.forEach((li, idx) => {
      if (!li.dataset.originalIndex) {
        li.dataset.originalIndex = String(idx);
      }
    });
  }

  // --- Geolocation Initialization ---
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        userLocation = { lat: pos.coords.latitude, lng: pos.coords.longitude };
        applyRanking();
      },
      (err) => {
        console.warn('Geolocation error on home:', err.message);
        applyRanking(); // Apply default sort even if location fails
      },
      { enableHighAccuracy: false, timeout: 8000, maximumAge: 600000 }
    );
  } else {
    applyRanking(); // Apply default sort if geolocation is not supported
  }

  // ===============================
  // HOME SEARCH FILTERS (Date/Time)
  // ===============================
  const pad2 = (n) => String(n).padStart(2, '0');
  const minutesToLabel = (m) => {
    if (m === 1440) return '24:00';
    const h = Math.floor(m / 60);
    const mm = m % 60;
    return pad2(h) + ':' + pad2(mm);
  };
  const labelToMinutes = (label) => {
    if (!label) return null;
    if (label === '24:00') return 1440;
    const parts = String(label).split(':');
    if (parts.length < 2) return null;
    const h = parseInt(parts[0], 10);
    const mm = parseInt(parts[1], 10);
    if (!Number.isFinite(h) || !Number.isFinite(mm)) return null;
    return h * 60 + mm;
  };
  const clearSelect = (select, placeholder) => {
    if (!select) return;
    select.innerHTML = '';
    const opt = document.createElement('option');
    opt.value = '';
    opt.textContent = placeholder;
    select.appendChild(opt);
  };

  function buildHomeStartOptions(dateStr) {
    if (!homeStartTime || !homeEndTime) return;
    clearSelect(homeStartTime, '— เลือกเวลาเริ่มต้น —');
    clearSelect(homeEndTime, '— เลือกเวลาสิ้นสุด —');
    homeEndTime.disabled = true;

    if (!dateStr) return;

    const todayStr = new Date().toISOString().split('T')[0];
    let startMin = 0;

    if (dateStr === todayStr) {
      const now = new Date();
      const cur = now.getHours() * 60 + now.getMinutes();
      const nextHour = (Math.floor(cur / 60) + 1) * 60;
      startMin = Math.min(Math.max(0, nextHour), 1380); // last start at 23:00
    }

    for (let m = startMin; m <= 1380; m += 60) {
      const opt = document.createElement('option');
      opt.value = minutesToLabel(m);
      opt.textContent = minutesToLabel(m);
      homeStartTime.appendChild(opt);
    }

    homeStartTime.disabled = homeStartTime.options.length <= 1;
  }

  function buildHomeEndOptions() {
    if (!homeStartTime || !homeEndTime) return;
    const startVal = homeStartTime.value;
    clearSelect(homeEndTime, '— เลือกเวลาสิ้นสุด —');
    if (!startVal) {
      homeEndTime.disabled = true;
      return;
    }
    const sMin = labelToMinutes(startVal);
    if (sMin === null || sMin >= 1440) {
      homeEndTime.disabled = true;
      return;
    }
    for (let m = sMin + 60; m <= 1440; m += 60) {
      const opt = document.createElement('option');
      opt.value = minutesToLabel(m);
      opt.textContent = minutesToLabel(m);
      homeEndTime.appendChild(opt);
    }
    homeEndTime.disabled = homeEndTime.options.length <= 1;
  }

  if (hourlyDateInput) {
    hourlyDateInput.addEventListener('change', () => {
      buildHomeStartOptions(hourlyDateInput.value);
      if (homeStartTime) homeStartTime.value = '';
      buildHomeEndOptions();
    });
  }
  if (homeStartTime) {
    homeStartTime.addEventListener('change', () => {
      buildHomeEndOptions();
    });
  }

  if (dailyStartDate && dailyEndDate) {
    dailyStartDate.addEventListener('change', () => {
      if (dailyStartDate.value) dailyEndDate.min = dailyStartDate.value;
      if (dailyEndDate.value && dailyStartDate.value && dailyEndDate.value < dailyStartDate.value) {
        dailyEndDate.value = dailyStartDate.value;
      }
    });
  }

  if (hourlyDateInput && hourlyDateInput.value) {
    buildHomeStartOptions(hourlyDateInput.value);
    if (homeStartTime && homeStartTime.value) buildHomeEndOptions();
  }
});

/* ==================== Venue Pager Overlay ==================== */
document.addEventListener('DOMContentLoaded', () => {
  const ul = document.getElementById('venueItems');
  if (!ul) return;

  const PER_ROW = 2;
  const ROWS_INITIAL = 4;
  const ROWS_PREVIEW = 5;
  const ROWS_EXPANDED = 10;

  const INITIAL = ROWS_INITIAL * PER_ROW;   // 4 แถวแรก = 8 การ์ด
  const PREVIEW_LIMIT = ROWS_PREVIEW * PER_ROW;   // แถวที่ 5   = 10 การ์ด
  const EXPAND_LIMIT = ROWS_EXPANDED * PER_ROW;   // แถว 1–10   = 20 การ์ด

  // จำว่าเคยกด "ดูเพิ่มเติม" แล้วหรือยัง
  let isExpanded = false;

  const css = `
  .vp-partial {
    filter: blur(0.6px);
    opacity: .65;
    pointer-events: none;
    position: relative;
    overflow: hidden;
  }
  .vp-partial::after{
    content:''; position:absolute; inset:0;
    background: linear-gradient(to bottom, rgba(248,250,252,0) 0%, rgba(248,250,252,1) 85%);
  }
  .vp-wrap{ position:relative; }
  .vp-more{
    position:absolute; left:50%; transform:translateX(-50%);
    bottom: 1.25rem;
  }
  @media (min-width: 640px){ .vp-more{ bottom: 1.5rem; } }
  `;
  const style = document.createElement('style');
  style.textContent = css;
  document.head.appendChild(style);

  // ห่อ ul ด้วย .vp-wrap หนึ่งชั้น (กันซ้อน)
  if (!ul.parentElement.classList.contains('vp-wrap')) {
    const wrapDiv = document.createElement('div');
    wrapDiv.className = 'vp-wrap';
    ul.parentElement.insertBefore(wrapDiv, ul);
    wrapDiv.appendChild(ul);
  }
  const wrap = ul.parentElement;

  function applyPaging() {
    const items = Array.from(ul.children).filter(el => el.tagName === 'LI');
    const activeItems = items.filter(li => !li.classList.contains('is-filtered-out'));
    const total = activeItems.length;

    // reset ทั้งหมดก่อน
    items.forEach(li => {
      li.classList.remove('vp-partial');
      // filtered-out items should stay hidden always
      if (li.classList.contains('is-filtered-out')) {
        li.classList.add('hidden');
      } else {
        li.classList.remove('hidden');
      }
      li.style.removeProperty('maxHeight');
    });

    // ถ้าการ์ดมีไม่ถึง 5 แถว (<= 10 ใบ) → แสดงหมด, ไม่ต้องมีปุ่ม / ไม่ต้องเบลอ
    if (total <= PREVIEW_LIMIT) {
      const b = wrap.querySelector('#btnMoreOverlay');
      if (b) b.remove();
      return;
    }

    // ถ้าเคยกดดูเพิ่มเติมแล้ว → เปิดแถว 1–10 เต็ม, ที่เหลือซ่อน, และไม่แสดงปุ่มอีกเลย
    if (isExpanded) {
      const max = Math.min(EXPAND_LIMIT, total);
      for (let i = 0; i < total; i++) {
        if (i < max) activeItems[i].classList.remove('hidden', 'vp-partial');
        else activeItems[i].classList.add('hidden');
      }
      const btn = wrap.querySelector('#btnMoreOverlay');
      if (btn) btn.classList.add('hidden');
      return;
    }

    // ===== สถานะปกติ (ยังไม่เคยกดดูเพิ่มเติม) =====

    // ซ่อนการ์ดเกินแถวที่ 10
    for (let i = EXPAND_LIMIT; i < total; i++) {
      activeItems[i].classList.add('hidden');
    }

    // ทำแถวที่ 5 ให้เบลอ (ใบ index 8–9)
    const previewEnd = Math.min(PREVIEW_LIMIT, total);
    for (let i = INITIAL; i < previewEnd; i++) {
      activeItems[i].classList.add('vp-partial');
    }

    // ซ่อนแถวที่ 6–10 (ใบ index 10–19)
    const expandEnd = Math.min(EXPAND_LIMIT, total);
    for (let i = PREVIEW_LIMIT; i < expandEnd; i++) {
      activeItems[i].classList.add('hidden');
    }

    // สร้าง / แสดงปุ่ม "ดูเพิ่มเติม"
    let btn = wrap.querySelector('#btnMoreOverlay');
    if (!btn) {
      btn = document.createElement('button');
      btn.id = 'btnMoreOverlay';
      btn.type = 'button';
      btn.className = 'vp-more px-6 py-3 text-sm font-semibold text-[var(--primary)] hover:underline';
      btn.textContent = 'ดูเพิ่มเติม';
      wrap.appendChild(btn);
      btn.addEventListener('click', () => expandToLimit());
    } else {
      btn.classList.remove('hidden');
    }
  }

  function expandToLimit() {
    const items = Array.from(ul.children).filter(el => el.tagName === 'LI');
    const activeItems = items.filter(li => !li.classList.contains('is-filtered-out'));
    const total = activeItems.length;
    const max = Math.min(EXPAND_LIMIT, total);

    for (let i = INITIAL; i < max; i++) {
      activeItems[i].classList.remove('hidden', 'vp-partial');
    }

    const btn = ul.parentElement.querySelector('#btnMoreOverlay');
    if (btn) btn.classList.add('hidden');

    // ตั้ง flag ว่าได้ขยายแล้ว → sort-change ครั้งต่อไปจะไม่กลับมาเบลอแถว 5
    isExpanded = true;
  }

  // expose for filters
  window.__venueApplyPaging = applyPaging;

  // เรียกตอนโหลดครั้งแรก
  applyPaging();

  // เวลา sort เปลี่ยน → re-apply layout ตามสถานะ isExpanded
  window.addEventListener('sort-change', () => {
    requestAnimationFrame(() => applyPaging());
  });
});


/* ==================== Home Venue Filters (aside) ==================== */
document.addEventListener('DOMContentLoaded', () => {
  const ul = document.getElementById('venueItems');
  if (!ul) return;

  const emptyState = document.getElementById('venueEmptyState');

  // ----- Price filter -----
  const btnHourly = document.getElementById('filterPriceHourly');
  const btnDaily  = document.getElementById('filterPriceDaily');
  const priceMinInput = document.getElementById('priceMin');
  const priceMaxInput = document.getElementById('priceMax');
  const priceSlider = document.getElementById('priceSlider');
  const rangeMin = document.getElementById('priceRangeMin');
  const rangeMax = document.getElementById('priceRangeMax');
  const fill = document.getElementById('priceTrackFill');
  const hMinEl = document.getElementById('priceHandleMin');
  const hMaxEl = document.getElementById('priceHandleMax');

  let priceMode = 'hourly';

  function num(v){ const n = Number(v); return Number.isFinite(n) ? n : 0; }

  function getBounds(mode){
    if (!priceSlider) return {min:0,max:0};
    const min = num(priceSlider.dataset[mode + 'Min']);
    const max = num(priceSlider.dataset[mode + 'Max']);
    return {min, max};
  }

  function setTabActive(mode){
    priceMode = mode;
    if (btnHourly && btnDaily){
      if (mode === 'hourly'){
        btnHourly.classList.add('bg-white','text-gray-900','shadow-sm');
        btnHourly.classList.remove('text-gray-500');
        btnDaily.classList.remove('bg-white','text-gray-900','shadow-sm');
        btnDaily.classList.add('text-gray-500');
      } else {
        btnDaily.classList.add('bg-white','text-gray-900','shadow-sm');
        btnDaily.classList.remove('text-gray-500');
        btnHourly.classList.remove('bg-white','text-gray-900','shadow-sm');
        btnHourly.classList.add('text-gray-500');
      }
    }
  }

  function clampRange(){
    if (!rangeMin || !rangeMax) return;
    const b = getBounds(priceMode);
    let a = num(rangeMin.value);
    let c = num(rangeMax.value);
    if (a < b.min) a = b.min;
    if (c > b.max) c = b.max;
    if (a > c) a = c;
    rangeMin.value = String(a);
    rangeMax.value = String(c);
    if (priceMinInput) priceMinInput.value = String(a);
    if (priceMaxInput) priceMaxInput.value = String(c);
    updateSliderUI();
  }

  function updateSliderUI(){
    if (!priceSlider || !fill || !hMinEl || !hMaxEl || !rangeMin || !rangeMax) return;
    const b = getBounds(priceMode);
    const minV = num(rangeMin.value);
    const maxV = num(rangeMax.value);
    const span = Math.max(1, b.max - b.min);
    const leftPct = ((minV - b.min) / span) * 100;
    const rightPct = ((maxV - b.min) / span) * 100;
    fill.style.left = leftPct + '%';
    fill.style.right = (100 - rightPct) + '%';
    hMinEl.style.left = leftPct + '%';
    hMaxEl.style.left = rightPct + '%';
  }

  function syncBoundsToUI(mode, resetValues=true){
    if (!rangeMin || !rangeMax) return;
    const b = getBounds(mode);
    rangeMin.min = String(b.min);
    rangeMin.max = String(b.max);
    rangeMax.min = String(b.min);
    rangeMax.max = String(b.max);
    if (resetValues){
      rangeMin.value = String(b.min);
      rangeMax.value = String(b.max);
      if (priceMinInput) priceMinInput.value = String(b.min);
      if (priceMaxInput) priceMaxInput.value = String(b.max);
    } else {
      // keep current but clamp
      clampRange();
    }
    updateSliderUI();
  }

  // ----- Checkbox filters -----
  function getCheckedValues(filterKey){
    return Array.from(document.querySelectorAll('.filter-cb[data-filter="' + filterKey + '"]'))
      .filter(el => el.checked)
      .map(el => num(el.value))
      .filter(v => v > 0);
  }

  function parseCsv(csv){
    if (!csv) return [];
    return String(csv).split(',').map(s => num(s.trim())).filter(v => v > 0);
  }

  function passesFilters(li){
    // Star (threshold): if select 4+ and 3+ => use min => >=3
    const stars = getCheckedValues('star');
    if (stars.length){
      const threshold = Math.min(...stars);
      if (num(li.dataset.rating) < threshold) return false;
    }

    // Reviews (minimum count)
    const rev = getCheckedValues('review');
    if (rev.length){
      const minCount = Math.min(...rev);
      if (num(li.dataset.reviewCount) < minCount) return false;
    }

    // Category (OR)
    const cats = getCheckedValues('category');
    if (cats.length){
      const cid = num(li.dataset.categoryId);
      if (!cats.includes(cid)) return false;
    }

    // Facilities (AND: selected must be subset)
    const facSel = getCheckedValues('facility');
    if (facSel.length){
      const have = new Set(parseCsv(li.dataset.facilityIds));
      for (const f of facSel){
        if (!have.has(f)) return false;
      }
    }

    // Price (only apply when narrowed from full bounds)
    if (priceSlider && rangeMin && rangeMax){
      const b = getBounds(priceMode);
      const minF = num(rangeMin.value);
      const maxF = num(rangeMax.value);
      const narrowed = (minF > b.min) || (maxF < b.max);

      if (narrowed){
        const key = priceMode === 'daily' ? 'dailyMin' : 'hourlyMin';
        const stadiumMin = num(li.dataset[key] || li.dataset.price);
        if (stadiumMin <= 0) return false;
        if (stadiumMin < minF || stadiumMin > maxF) return false;
      }
    }

    return true;
  }

  function updateEmptyState(){
    if (!emptyState) return;
    const active = Array.from(ul.querySelectorAll('li')).filter(li => !li.classList.contains('is-filtered-out'));
    emptyState.classList.toggle('hidden', active.length > 0);
  }

  function applyFilters(){
    const items = Array.from(ul.querySelectorAll('li'));
    items.forEach(li => {
      const ok = passesFilters(li);
      li.classList.toggle('is-filtered-out', !ok);
      if (!ok) li.classList.add('hidden');
    });

    // sort visible subset with current sort key (function is in outer scope)
    if (typeof window.__homeSortListBy === 'function') {
      window.__homeSortListBy(window.__homeCurrentSortKey || 'popular', { emitEvent: true });
    } else {
      // fallback: trigger paging refresh
      if (typeof window.__venueApplyPaging === 'function') window.__venueApplyPaging();
    }

    if (typeof window.__venueApplyPaging === 'function') window.__venueApplyPaging();
    updateEmptyState();
  }

  // Hook into existing sort function by exposing a wrapper (we inject below in sorting section)
  // If wrapper not present yet, we still apply filters; it will update paging and empty state.
  // Price events
  if (btnHourly) btnHourly.addEventListener('click', () => { setTabActive('hourly'); syncBoundsToUI('hourly', false); applyFilters(); });
  if (btnDaily)  btnDaily.addEventListener('click', () => { setTabActive('daily');  syncBoundsToUI('daily', false); applyFilters(); });

  if (rangeMin) rangeMin.addEventListener('input', () => { clampRange(); applyFilters(); });
  if (rangeMax) rangeMax.addEventListener('input', () => { clampRange(); applyFilters(); });

  if (priceMinInput) priceMinInput.addEventListener('input', () => {
    if (!rangeMin) return;
    rangeMin.value = String(num(priceMinInput.value));
    clampRange();
    applyFilters();
  });
  if (priceMaxInput) priceMaxInput.addEventListener('input', () => {
    if (!rangeMax) return;
    rangeMax.value = String(num(priceMaxInput.value));
    clampRange();
    applyFilters();
  });

  // Checkbox events
  document.querySelectorAll('.filter-cb').forEach(el => {
    el.addEventListener('change', () => applyFilters());
  });

  // initial UI
  setTabActive('hourly');
  syncBoundsToUI('hourly', true);
  updateSliderUI();
  applyFilters();
});

/* ==================== Login Overlay สำหรับ guest ==================== */
(function () {
  const loggedIn = !!window.CUSTOMER_LOGGED_IN;
  const backdrop = document.getElementById('loginBackdrop');
  const panel = document.getElementById('loginPanel');

  if (!backdrop || !panel) return;

  function openOverlay() {
    if (loggedIn) return false; // ถ้าล็อกอินแล้ว ไม่ทำอะไร
    backdrop.classList.remove('hidden');
    panel.classList.remove('hidden');
    // ไม่ล็อก body เพื่อให้ยัง scroll หน้าได้
    return true;
  }

  function closeOverlay() {
    backdrop.classList.add('hidden');
    panel.classList.add('hidden');
  }

  // ปุ่มปิดทั้งหมด (รวมกากบาท + ปุ่ม/ลิงก์ที่ใส่ data-login-overlay-close)
  panel.querySelectorAll('[data-login-overlay-close]').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      closeOverlay();
    });
  });

  // คลิกเงาดำด้านหลัง = ปิด
  backdrop.addEventListener('click', closeOverlay);

  // 1) ปุ่มค้นหาสนามใน hero (#openSearch)
  const heroSearchBtn = document.getElementById('openSearch');
  if (heroSearchBtn) {
    heroSearchBtn.addEventListener('click', (e) => {
      if (!loggedIn) {
        e.preventDefault();
        e.stopImmediatePropagation(); // กัน handler search เดิม
        openOverlay();
      }
    }, { capture: true });
  }

  // 2) เมนู "ค้นหาสนาม" ที่ header (ลิงก์ที่มี #search)
  document.querySelectorAll('a[href*="#search"]').forEach(a => {
    a.addEventListener('click', (e) => {
      if (!loggedIn) {
        e.preventDefault();
        openOverlay();
      }
    });
  });

  // 3) การ์ด "สนามใกล้คุณ" → ใช้ article ใน #nearScroller
  document.querySelectorAll('#nearScroller article').forEach(card => {
    card.addEventListener('click', (e) => {
      if (!loggedIn) {
        e.preventDefault();
        e.stopImmediatePropagation(); // กัน JS เดิมที่พาไป sport/show
        openOverlay();
      }
    }, { capture: true });
  });

  // 4) การ์ดในรายการหลัก (#venueItems) ถ้ามี <a href="sport/show/...">
  document.querySelectorAll('#venueItems a[href*="sport/show"]').forEach(a => {
    a.addEventListener('click', (e) => {
      if (!loggedIn) {
        e.preventDefault();
        openOverlay();
      }
    });
  });

  // 5) ปุ่ม "ดูทั้งหมด" สนามใกล้คุณ
  function findNearViewAll() {
    const scroller = document.getElementById('nearScroller');
    if (!scroller) return null;
    // หา section หรือ wrapper ที่ครอบ nearScroller
    const section = scroller.closest('section') || scroller.parentElement;
    if (!section) return null;

    const candidates = section.querySelectorAll('a,button');
    for (const el of candidates) {
      if (!el.textContent) continue;
      const text = el.textContent.trim();
      if (text === 'ดูทั้งหมด' || text.includes('ดูทั้งหมด')) {
        return el;
      }
    }
    return null;
  }

  const nearViewAll = findNearViewAll();
  if (nearViewAll) {
    nearViewAll.addEventListener('click', (e) => {
      // ถ้าล็อกอินแล้ว → ปล่อยให้ลิงก์ทำงานตามปกติ
      if (loggedIn) return;

      // ถ้ายังไม่ล็อกอิน → กัน navigation แล้วเปิด hover login
      e.preventDefault();
      openOverlay();
    });
  }

  // 6) ปุ่ม "ดูทั้งหมด" อันล่าง (section id="venueSeeAll")
  const bottomViewAll = document.querySelector('#venueSeeAll a');

  if (bottomViewAll) {
    bottomViewAll.addEventListener('click', (e) => {
      // ถ้าล็อกอินแล้ว → ปล่อยให้ทำงานตามปกติ
      if (loggedIn) return;

      // ถ้ายังไม่ล็อกอิน → เปิด hover login
      e.preventDefault();
      openOverlay();
    });
  }
})();

/* ==================== Search Tabs (Hourly / Daily) ==================== */
document.addEventListener('DOMContentLoaded', () => {
  const tabHourly = document.getElementById('tabHourly');
  const tabDaily = document.getElementById('tabDaily');
  const formHourly = document.getElementById('formHourly');
  const formDaily = document.getElementById('formDaily');
  const mainSearchBtn = document.getElementById('mainSearchBtn');

  if (!tabHourly || !tabDaily || !formHourly || !formDaily) return;
  function shouldGoPlainView(form) {
    try {
      const fd = new FormData(form);
      const q = String(fd.get('q') || '').trim();
      const cat = String(fd.get('category_id') || fd.get('category') || '').trim();

      const date = String(fd.get('date') || '').trim();
      const st = String(fd.get('start_time') || '').trim();
      const et = String(fd.get('end_time') || '').trim();

      const sd = String(fd.get('start_date') || '').trim();
      const ed = String(fd.get('end_date') || '').trim();

      return !q && !cat && !date && !st && !et && !sd && !ed;
    } catch (e) {
      return false;
    }
  }

  const goPlainView = (form) => {
    const url = (form && form.action) ? form.action.split('?')[0] : (window.location.origin + '/sport/view');
    window.location.href = url;
  };

  // Handle clicking the main search button
  if (mainSearchBtn) {
    mainSearchBtn.addEventListener('click', () => {
      const activeForm = !formHourly.classList.contains('hidden') ? formHourly : formDaily;
      if (!activeForm) return;
      if (shouldGoPlainView(activeForm)) {
        goPlainView(activeForm);
        return;
      }
      activeForm.submit();
    });
  }

  // Handle pressing Enter inside the form (submit event)
  [formHourly, formDaily].forEach((f) => {
    if (!f) return;
    f.addEventListener('submit', (e) => {
      if (shouldGoPlainView(f)) {
        e.preventDefault();
        goPlainView(f);
      }
    });
  });

  function switchTab(mode) {
    const currentForm = mode === 'hourly' ? formDaily : formHourly;
    const nextForm = mode === 'hourly' ? formHourly : formDaily;

    // Fade out current form
    currentForm.style.opacity = '1';
    currentForm.style.transform = 'translateX(0)';
    currentForm.style.transition = 'opacity 0.2s ease-out, transform 0.2s ease-out';

    requestAnimationFrame(() => {
      currentForm.style.opacity = '0';
      currentForm.style.transform = 'translateX(-20px)';
    });

    // Hide current form after animation
    setTimeout(() => {
      currentForm.classList.add('hidden');
      currentForm.style.opacity = '';
      currentForm.style.transform = '';

      // Prepare next form for animation
      nextForm.style.opacity = '0';
      nextForm.style.transform = 'translateX(20px)';
      nextForm.classList.remove('hidden');

      // Fade in next form
      requestAnimationFrame(() => {
        nextForm.style.transition = 'opacity 0.3s ease-in, transform 0.3s ease-in';
        nextForm.style.opacity = '1';
        nextForm.style.transform = 'translateX(0)';
      });

      // Clean up inline styles after animation
      setTimeout(() => {
        nextForm.style.opacity = '';
        nextForm.style.transform = '';
        nextForm.style.transition = '';
      }, 300);
    }, 200);

    if (mode === 'hourly') {
      // Active Hourly
      tabHourly.classList.remove('text-gray-500', 'bg-gray-50', 'border-transparent');
      tabHourly.classList.add('text-[var(--primary)]', 'bg-white', 'border-[var(--primary)]');

      // Inactive Daily
      tabDaily.classList.remove('text-[var(--primary)]', 'bg-white', 'border-[var(--primary)]');
      tabDaily.classList.add('text-gray-500', 'bg-gray-50', 'border-transparent');
    } else {
      // Active Daily
      tabDaily.classList.remove('text-gray-500', 'bg-gray-50', 'border-transparent');
      tabDaily.classList.add('text-[var(--primary)]', 'bg-white', 'border-[var(--primary)]');

      // Inactive Hourly
      tabHourly.classList.remove('text-[var(--primary)]', 'bg-white', 'border-[var(--primary)]');
      tabHourly.classList.add('text-gray-500', 'bg-gray-50', 'border-transparent');
    }
  }

  tabHourly.addEventListener('click', () => switchTab('hourly'));
  tabDaily.addEventListener('click', () => switchTab('daily'));

  // Popular Stadiums Scroller
  const popularScroller = document.getElementById('popularScroller');
  const popularLeft = document.getElementById('popularLeft');
  const popularRight = document.getElementById('popularRight');

  if (popularScroller && popularLeft && popularRight) {
    popularLeft.addEventListener('click', () => {
      popularScroller.scrollBy({ left: -300, behavior: 'smooth' });
    });

    popularRight.addEventListener('click', () => {
      popularScroller.scrollBy({ left: 300, behavior: 'smooth' });
    });
  }
});
