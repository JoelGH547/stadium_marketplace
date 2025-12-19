document.addEventListener('DOMContentLoaded', function () {
  // ===============================
  // ===== 1. ELEMENT SELECTORS ====
  // ===============================
  const filterToggle = document.getElementById('filterToggle');
  const filterPanel = document.getElementById('filterPanel');
  const searchInput = document.getElementById('venueSearch');
  const listEl = document.getElementById('allVenueList');
  const venueItems = Array.from(document.querySelectorAll('.venue-item'));

  // Server-side search filter elements
  const viewMode = document.getElementById('viewMode');
  const viewHourlyBox = document.getElementById('viewHourlyBox');
  const viewDailyBox = document.getElementById('viewDailyBox');
  const viewDate = document.getElementById('viewDate');
  const viewStartTime = document.getElementById('viewStartTime');
  const viewEndTime = document.getElementById('viewEndTime');
  const viewStartDate = document.getElementById('viewStartDate');
  const viewEndDate = document.getElementById('viewEndDate');

  // Client-side filter elements
  const sportChips = Array.from(document.querySelectorAll('#sport-filter-group .filter-chip'));
  const sortChips = Array.from(document.querySelectorAll('#sort-group .sort-chip'));
  const starRadios = Array.from(document.querySelectorAll('#star-filter-group .filter-rb'));
  const reviewRadios = Array.from(document.querySelectorAll('#review-filter-group .filter-rb'));
  const facilityCbs = Array.from(document.querySelectorAll('#facility-filter-group .filter-cb'));

  // ===============================
  // ===== 2. STATE MANAGEMENT =====
  // ===============================
  let activeSport = 'all';
  let activeSort = 'popular';
  let searchTerm = (searchInput?.value || '').trim().toLowerCase();
  let activeStar = 0;
  let activeReview = 0;
  let activeFacilities = new Set();
  let userLocation = null;

  // ===============================
  // ===== 3. UTILITY FUNCTIONS ====
  // ===============================
  const setActiveChip = (chips, activeEl) => {
    chips.forEach(chip => chip.classList.remove('active'));
    if (activeEl) activeEl.classList.add('active');
  };

  const updateDistances = () => {
    if (!userLocation) return;
    const R = 6371; // Earth radius in km
    const toRad = deg => deg * Math.PI / 180;
    venueItems.forEach(item => {
      const lat = parseFloat(item.dataset.lat || '');
      const lng = parseFloat(item.dataset.lng || '');
      let distVal = Infinity;
      if (!isNaN(lat) && !isNaN(lng)) {
        const dLat = toRad(lat - userLocation.lat);
        const dLon = toRad(lng - userLocation.lng);
        const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRad(userLocation.lat)) * Math.cos(toRad(lat)) * Math.sin(dLon / 2) ** 2;
        distVal = R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      }
      item.dataset.distance = distVal.toString();
      const badge = item.querySelector('.dist-badge span:last-child');
      if (badge) badge.textContent = distVal === Infinity ? '-- km.' : (distVal < 1 ? (distVal * 1000).toFixed(0) + ' m.' : distVal.toFixed(1) + ' km.');
    });
  };

  // ===========================================
  // ===== 4. CORE FILTER & SORT ALGORITHM =====
  // ===========================================
  const applyFiltersAndSort = () => {
    const visible = [];
    venueItems.forEach(item => {
      const { name, categoryId, rating, reviewCount, facilityIds } = item.dataset;
      const itemFacilities = (facilityIds || '').split(',').filter(id => id);

      const matchSearch = !searchTerm || name.toLowerCase().includes(searchTerm);
      const matchSport = activeSport === 'all' || categoryId === activeSport;
      const matchStar = activeStar === 0 || Math.floor(parseFloat(rating)) === activeStar;
      const matchReview = parseInt(reviewCount, 10) >= activeReview;
      const matchFacilities = activeFacilities.size === 0 || [...activeFacilities].every(facId => itemFacilities.includes(facId));

      const show = matchSearch && matchSport && matchStar && matchReview && matchFacilities;
      item.style.display = show ? '' : 'none';
      if (show) visible.push(item);
    });

    let sorted = visible.slice();
    switch (activeSort) {
      case 'price':
        sorted.sort((a, b) => parseFloat(a.dataset.price || '0') - parseFloat(b.dataset.price || '0'));
        break;
      case 'rating':
        sorted.sort((a, b) => parseInt(b.dataset.reviewCount || '0', 10) - parseInt(a.dataset.reviewCount || '0', 10));
        break;
      case 'nearby':
        sorted.sort((a, b) => parseFloat(a.dataset.distance || Infinity) - parseFloat(b.dataset.distance || Infinity));
        break;
      default: // 'popular'
        sorted.sort((a, b) => parseInt(b.dataset.reviewCount || '0', 10) - parseInt(a.dataset.reviewCount || '0', 10));
        break;
    }

    sorted.forEach(item => listEl.appendChild(item));
  };

  // ===============================
  // ===== 5. EVENT LISTENERS ======
  // ===============================
  if (filterToggle && filterPanel) {
    filterToggle.addEventListener('click', () => filterPanel.classList.toggle('hidden'));
  }

  if (searchInput) {
    let timer = null;
    searchInput.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        searchTerm = searchInput.value.trim().toLowerCase();
        applyFiltersAndSort();
      }, 150);
    });
  }

  document.querySelector('form[method="get"]')?.addEventListener('submit', (e) => {
    // Let the form submit normally for server-side filtering (date/time/mode)
    // Client-side search is handled by the 'input' event on the search box
  });

  sportChips.forEach(chip => {
    chip.addEventListener('click', () => {
      setActiveChip(sportChips, chip);
      activeSport = chip.dataset.value || 'all';
      applyFiltersAndSort();
    });
  });

  sortChips.forEach(chip => {
    chip.addEventListener('click', () => {
      setActiveChip(sortChips, chip);
      activeSort = chip.dataset.sort || 'popular';
      applyFiltersAndSort();
    });
  });

  const setupRadioGroup = (radios, getter, setter) => {
    radios.forEach(rb => {
      rb.addEventListener('click', (e) => {
        const value = parseInt(e.target.value, 10);
        // If clicking the same radio, uncheck it and reset the filter
        if (getter() === value) {
          e.target.checked = false;
          setter(0);
        } else {
          setter(value);
        }
        applyFiltersAndSort();
      });
    });
  };
  setupRadioGroup(starRadios, () => activeStar, (v) => { activeStar = v; });
  setupRadioGroup(reviewRadios, () => activeReview, (v) => { activeReview = v; });

  facilityCbs.forEach(cb => {
    cb.addEventListener('change', () => {
      activeFacilities = new Set(facilityCbs.filter(c => c.checked).map(c => c.value));
      applyFiltersAndSort();
    });
  });

  // ===============================
  // ===== 6. INITIALIZATION =======
  // ===============================
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        userLocation = { lat: pos.coords.latitude, lng: pos.coords.longitude };
        updateDistances();
        if (activeSort === 'nearby') applyFiltersAndSort();
      },
      () => console.warn('Could not get user location.'), { timeout: 8000 }
    );
  }

  venueItems.forEach((item, idx) => item.dataset.index = idx.toString());
  setActiveChip(sportChips, sportChips.find(c => c.dataset.value === 'all'));
  setActiveChip(sortChips, sortChips.find(c => c.dataset.sort === 'popular'));

  applyFiltersAndSort(); // Initial filter call

  // --- DATE/TIME PICKER LOGIC (unchanged) ---
  const pad2 = (n) => String(n).padStart(2, '0');
  const minutesToLabel = (m) => {
    if (m === 1440) return '24:00';
    return pad2(Math.floor(m / 60)) + ':' + pad2(m % 60);
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
    if (select) select.innerHTML = `<option value="">${placeholder}</option>`;
  };

  function applyModeUI() {
    if (!viewMode) return;
    const mode = viewMode.value || '';
    if (viewHourlyBox) viewHourlyBox.classList.toggle('hidden', mode !== 'hourly');
    if (viewDailyBox) viewDailyBox.classList.toggle('hidden', mode !== 'daily');
  }

  function buildViewStartOptions(dateStr) {
    if (!viewStartTime || !viewEndTime) return;
    clearSelect(viewStartTime, '— เลือกเวลาเริ่มต้น —');
    clearSelect(viewEndTime, '— เลือกเวลาสิ้นสุด —');
    viewEndTime.disabled = true;
    if (!dateStr) return;

    let startMin = (dateStr === new Date().toISOString().split('T')[0]) ? (Math.floor((new Date().getHours() * 60 + new Date().getMinutes()) / 60) + 1) * 60 : 0;
    for (let m = Math.min(Math.max(0, startMin), 1380); m <= 1380; m += 60) {
      viewStartTime.insertAdjacentHTML('beforeend', `<option value="${minutesToLabel(m)}">${minutesToLabel(m)}</option>`);
    }
    viewStartTime.disabled = viewStartTime.options.length <= 1;

    const want = (viewStartTime.dataset.selected || '').trim();
    if (want && Array.from(viewStartTime.options).some(o => o.value === want)) viewStartTime.value = want;
  }

  function buildViewEndOptions() {
    if (!viewStartTime || !viewEndTime) return;
    const startVal = viewStartTime.value;
    clearSelect(viewEndTime, '— เลือกเวลาสิ้นสุด —');
    if (!startVal) { viewEndTime.disabled = true; return; }

    const sMin = labelToMinutes(startVal);
    if (sMin === null || sMin >= 1440) { viewEndTime.disabled = true; return; }

    for (let m = sMin + 60; m <= 1440; m += 60) {
      viewEndTime.insertAdjacentHTML('beforeend', `<option value="${minutesToLabel(m)}">${minutesToLabel(m)}</option>`);
    }
    viewEndTime.disabled = viewEndTime.options.length <= 1;

    const want = (viewEndTime.dataset.selected || '').trim();
    if (want && Array.from(viewEndTime.options).some(o => o.value === want)) viewEndTime.value = want;
  }

  if (viewMode) viewMode.addEventListener('change', applyModeUI);
  if (viewDate) viewDate.addEventListener('change', () => {
    if (viewStartTime) viewStartTime.dataset.selected = '';
    if (viewEndTime) viewEndTime.dataset.selected = '';
    buildViewStartOptions(viewDate.value);
    if (viewStartTime) viewStartTime.value = '';
    buildViewEndOptions();
  });
  if (viewStartTime) viewStartTime.addEventListener('change', () => {
    if (viewEndTime) viewEndTime.dataset.selected = '';
    buildViewEndOptions();
  });
  if (viewStartDate && viewEndDate) {
    viewStartDate.addEventListener('change', () => {
      if (viewStartDate.value) viewEndDate.min = viewStartDate.value;
      if (viewEndDate.value && viewEndDate.value < viewStartDate.value) viewEndDate.value = viewStartDate.value;
    });
  }

  applyModeUI();
  if (viewDate && viewDate.value) {
    buildViewStartOptions(viewDate.value);
    buildViewEndOptions();
  } else {
    if (viewStartTime) clearSelect(viewStartTime, '— เลือกเวลาเริ่มต้น —');
    if (viewEndTime) { clearSelect(viewEndTime, '— เลือกเวลาสิ้นสุด —'); viewEndTime.disabled = true; }
  }
});
