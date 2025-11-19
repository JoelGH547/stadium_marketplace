document.addEventListener('DOMContentLoaded', function () {
  const filterToggle = document.getElementById('filterToggle');
  const filterPanel  = document.getElementById('filterPanel');
  const searchInput  = document.getElementById('venueSearch');
  const listEl       = document.getElementById('allVenueList');

  const venueItems   = Array.from(document.querySelectorAll('.venue-item'));
  const sportChips   = Array.from(document.querySelectorAll('.filter-chip[data-filter-type="sport"]'));
  const sortChips    = Array.from(document.querySelectorAll('.sort-chip'));
  const areaCheckbox = Array.from(document.querySelectorAll('.area-filter'));

  // state
  let activeSport = 'all';
  let activeSort  = 'popular';
  let searchTerm  = '';
  let activeAreas = new Set();
  let userLocation = null;

  // เก็บ index เดิมไว้ใช้ตอนเรียง default
  venueItems.forEach((item, idx) => {
    item.dataset.originalIndex = idx.toString();
  });

  // toggle ฟิลเตอร์
  if (filterToggle && filterPanel) {
    filterToggle.addEventListener('click', () => {
      filterPanel.classList.toggle('hidden');
    });
  }

  // utility: set active chip style
  function setActiveChip(chips, activeEl) {
    chips.forEach(chip => {
      chip.classList.remove(
        'border-[var(--primary)]',
        'bg-[var(--primary)]/10',
        'text-[var(--primary)]',
        'ring-2',
        'ring-[var(--primary)]'
      );
    });
    if (activeEl) {
      activeEl.classList.add(
        'border-[var(--primary)]',
        'bg-[var(--primary)]/10',
        'text-[var(--primary)]',
        'ring-2',
        'ring-[var(--primary)]'
      );
    }
  }

  // sport filter chips
  sportChips.forEach(chip => {
    chip.addEventListener('click', () => {
      activeSport = chip.dataset.filterValue || 'all';
      setActiveChip(sportChips, chip);
      applyFilters();
    });
  });

  // sort chips
  sortChips.forEach(chip => {
    chip.addEventListener('click', () => {
      activeSort = chip.dataset.sort || 'popular';
      setActiveChip(sortChips, chip);
      applyFilters();
    });
  });

  // area checkboxes
  areaCheckbox.forEach(cb => {
    cb.addEventListener('change', () => {
      activeAreas = new Set(
        areaCheckbox
          .filter(c => c.checked)
          .map(c => c.value)
      );
      applyFilters();
    });
  });

  // search
  if (searchInput) {
    let timer = null;
    searchInput.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        searchTerm = searchInput.value.trim().toLowerCase();
        applyFilters();
      }, 120);
    });
  }

  // geolocation + ระยะห่าง
  function haversine(lat1, lon1, lat2, lon2) {
    const toRad = deg => deg * Math.PI / 180;
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

  function updateDistances() {
    if (!userLocation) return;

    venueItems.forEach(item => {
      const lat = parseFloat(item.dataset.lat || '');
      const lng = parseFloat(item.dataset.lng || '');
      let distText = '-- km.';
      let distVal  = Number.POSITIVE_INFINITY;

      if (!isNaN(lat) && !isNaN(lng)) {
        const d = haversine(userLocation.lat, userLocation.lng, lat, lng);
        distVal = d;
        distText = d < 1 ? (d * 1000).toFixed(0) + ' m.' : d.toFixed(1) + ' km.';
      }

      item.dataset.distance = distVal.toString();
      const badge = item.querySelector('.dist-badge span:last-child');
      if (badge) {
        badge.textContent = distText;
      }
    });
  }

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        userLocation = {
          lat: pos.coords.latitude,
          lng: pos.coords.longitude
        };
        updateDistances();
        applyFilters(); // ให้ sort ใกล้ตัวฉันใช้ค่า distance ได้
      },
      (err) => {
        console.warn('Geolocation error:', err.message);
      },
      {
        enableHighAccuracy: false,
        timeout: 8000,
        maximumAge: 600000
      }
    );
  }

  // core filter + sort
  function applyFilters() {
    const visible = [];

    venueItems.forEach(item => {
      const name    = (item.dataset.name || '').toLowerCase();
      const address = (item.dataset.address || '').toLowerCase();
      const catId   = item.dataset.categoryId || '';
      const areaTag = item.dataset.area || '';

      const matchSearch =
        !searchTerm ||
        name.includes(searchTerm) ||
        address.includes(searchTerm);

      const matchSport =
        activeSport === 'all' ||
        (catId && catId.toString() === activeSport.toString());

      const matchArea =
        activeAreas.size === 0 ||
        activeAreas.has(areaTag);

      const show = matchSearch && matchSport && matchArea;

      if (show) {
        item.classList.remove('hidden');
        visible.push(item);
      } else {
        item.classList.add('hidden');
      }
    });

    // sort visible
    let sorted = visible.slice();

    if (activeSort === 'price') {
      sorted.sort((a, b) => {
        const pa = parseFloat(a.dataset.price || '0');
        const pb = parseFloat(b.dataset.price || '0');
        return pa - pb;
      });
    } else if (activeSort === 'nearby') {
      sorted.sort((a, b) => {
        const da = parseFloat(a.dataset.distance || Number.POSITIVE_INFINITY);
        const db = parseFloat(b.dataset.distance || Number.POSITIVE_INFINITY);
        return da - db;
      });
    } else {
      // popular/default = index เดิม
      sorted.sort((a, b) => {
        const ia = parseInt(a.dataset.originalIndex || '0', 10);
        const ib = parseInt(b.dataset.originalIndex || '0', 10);
        return ia - ib;
      });
    }

    // re-append ตามลำดับใหม่
    if (listEl) {
      sorted.forEach(item => listEl.appendChild(item));
    }
  }

  // initial state
  if (sportChips.length) {
    setActiveChip(sportChips, sportChips[0]); // ทั้งหมด
  }
  if (sortChips.length) {
    setActiveChip(sortChips, sortChips[0]); // ยอดนิยม
  }

  applyFilters();
});
