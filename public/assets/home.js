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


/* ============ Nearby distance + limit 8/20 ============ */
document.addEventListener('DOMContentLoaded', () => {
  const nearScroller = document.getElementById('nearScroller');
  const listEl = document.getElementById('venueItems');

  if (!nearScroller && !listEl) return;

  let userLocation = null;

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
    // จัดการรายการหลักด้านล่าง
    if (listEl) {
      const items = Array.from(listEl.querySelectorAll('li'));
      items.forEach((li) => {
        const lat = parseFloat(li.dataset.lat || '');
        const lng = parseFloat(li.dataset.lng || '');
        let dist = Number.POSITIVE_INFINITY;

        if (userLocation && !isNaN(lat) && !isNaN(lng)) {
          dist = haversine(userLocation.lat, userLocation.lng, lat, lng);
        }

        li.dataset.distanceKm = dist.toString();
        li.dataset.distance = dist.toString();

        if (userLocation) {
          const badge = li.querySelector('.dist-badge span:last-child');
          if (badge) badge.textContent = formatDistance(dist);
        }
      });

      const sorted = items.slice().sort((a, b) => {
        const da = parseFloat(a.dataset.distanceKm || '999999');
        const db = parseFloat(b.dataset.distanceKm || '999999');
        return da - db;
      });

      sorted.forEach((li, idx) => {
        listEl.appendChild(li);
        if (idx < 20) li.classList.remove('hidden');
        else li.classList.add('hidden');
      });
    }

    // จัดการ section สนามใกล้คุณ
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
        card.dataset.distance = dist.toString();

        if (userLocation) {
          const badge = card.querySelector('.dist-badge span:last-child');
          if (badge) badge.textContent = formatDistance(dist);
        }
      });

      const sortedCards = cards.slice().sort((a, b) => {
        const da = parseFloat(a.dataset.distanceKm || '999999');
        const db = parseFloat(b.dataset.distanceKm || '999999');
        return da - db;
      });

      sortedCards.forEach((card, idx) => {
        nearScroller.appendChild(card);
        if (idx < 12) card.classList.remove('hidden');
        else card.classList.add('hidden');
      });
    }

    // ✅ แจ้ง overlay/pager ว่า “ลำดับเปลี่ยนแล้วนะ”
    try {
      window.dispatchEvent(new CustomEvent('sort-change', { detail: { key: 'distance' } }));
    } catch (_) {
      const e = document.createEvent('CustomEvent');
      e.initCustomEvent('sort-change', true, true, { key: 'distance' });
      window.dispatchEvent(e);
    }
  }


  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        userLocation = {
          lat: pos.coords.latitude,
          lng: pos.coords.longitude,
        };
        applyRanking();
      },
      (err) => {
        console.warn('Geolocation error on home:', err.message);
        // ไม่มีตำแหน่ง: อย่างน้อยให้จำกัดจำนวน 8/20 ตามลำดับเดิม
        applyRanking();
      },
      {
        enableHighAccuracy: false,
        timeout: 8000,
        maximumAge: 600000,
      }
    );
  } else {
    // browser ไม่รองรับ geolocation: จำกัดจำนวน 8/20 ตามลำดับเดิม
    applyRanking();
  }


  // ===============================
  // Home Search Filters: Date/Time Slots (00:00-24:00) + Basic Validation
  // - Prevent past dates via min attribute in HTML (server should still validate)
  // - If date is today, hide past time slots (like show page)
  // - End time must be > start time
  // - If user doesn't set anything (q/category/date/time), go to /sport/view plain
  // ===============================
  const hourlyDateInput = document.getElementById('hourlyDate');
  const homeStartTime   = document.getElementById('homeStartTime');
  const homeEndTime     = document.getElementById('homeEndTime');
  const dailyStartDate  = document.getElementById('dailyStartDate');
  const dailyEndDate    = document.getElementById('dailyEndDate');

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

    // If selected date is today, start from next full hour
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

  // Daily date basic constraint: end >= start
  if (dailyStartDate && dailyEndDate) {
    dailyStartDate.addEventListener('change', () => {
      if (dailyStartDate.value) dailyEndDate.min = dailyStartDate.value;
      if (dailyEndDate.value && dailyStartDate.value && dailyEndDate.value < dailyStartDate.value) {
        dailyEndDate.value = dailyStartDate.value;
      }
    });
  }

  function shouldGoPlainView(form) {
    try {
      const fd = new FormData(form);
      const q = String(fd.get('q') || '').trim();
      const cat = String(fd.get('category') || '').trim();

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

  // If user doesn't fill anything, go /sport/view (no query)
  [formHourly, formDaily].forEach((f) => {
    if (!f) return;
    f.addEventListener('submit', (e) => {
      if (shouldGoPlainView(f)) {
        e.preventDefault();
        window.location.href = (f && f.action) ? f.action.split('?')[0] : (window.location.origin + '/sport/view');
      }
    });
  });

  // Init if back with date
  if (hourlyDateInput && hourlyDateInput.value) {
    buildHomeStartOptions(hourlyDateInput.value);
    if (homeStartTime && homeStartTime.value) buildHomeEndOptions();
  }
});

/* ============ เก็บลำดับเดิมของการ์ด (ใช้กับยอดนิยม/ดีฟอลต์) ============ */
document.addEventListener('DOMContentLoaded', () => {
  const listEl = document.getElementById('venueItems');
  if (!listEl) return;

  const items = Array.from(listEl.querySelectorAll('li'));
  items.forEach((li, idx) => {
    if (!li.dataset.originalIndex) {
      li.dataset.originalIndex = String(idx); // ใช้เป็นลำดับ default
    }
  });
});
/* ============ เมนูเรียงลำดับด้านล่าง (home) ============ */
document.addEventListener('DOMContentLoaded', () => {
  const sortMenu = document.getElementById('sortMenu');
  const listEl = document.getElementById('venueItems');
  if (!sortMenu || !listEl) return;

  const buttons = Array.from(sortMenu.querySelectorAll('button.sort-btn'));

  function setActive(btn) {
    buttons.forEach(b => {
      b.classList.remove('bg-[var(--primary)]', 'text-white', 'font-semibold');
      b.classList.add('text-gray-700', 'hover:text-[var(--primary)]', 'hover:bg-[var(--primary)]/10');
      b.setAttribute('aria-selected', 'false');
    });

    btn.classList.remove('text-gray-700', 'hover:text-[var(--primary)]', 'hover:bg-[var(--primary)]/10');
    btn.classList.add('bg-[var(--primary)]', 'text-white', 'font-semibold');
    btn.setAttribute('aria-selected', 'true');
  }

  function sortListBy(sortKey) {
    const items = Array.from(listEl.querySelectorAll('li'));
    const sorted = items.slice();

    if (sortKey === 'price') {
      // ราคาถูกสุด
      sorted.sort((a, b) => {
        const pa = parseFloat(a.dataset.price || '0');
        const pb = parseFloat(b.dataset.price || '0');
        return pa - pb;
      });
    } else if (sortKey === 'nearby') {
      // ใช้ปุ่มนี้เป็น "ราคาสุดหรู" (แพงสุดก่อน) — เปลี่ยนแค่ข้อความใน home.php อย่าเปลี่ยน data-sort
      sorted.sort((a, b) => {
        const pa = parseFloat(a.dataset.price || '0');
        const pb = parseFloat(b.dataset.price || '0');
        return pb - pa;
      });
    } else if (sortKey === 'rating') {
      // ได้คะแนนรีวิวสูง — ตอนนี้ rating ยัง 0 หมด เลยจะเรียงเหมือนเดิม
      sorted.sort((a, b) => {
        const ra = parseFloat(a.dataset.rating || '0');
        const rb = parseFloat(b.dataset.rating || '0');
        if (rb !== ra) return rb - ra; // สูง → ต่ำ
        const ia = parseInt(a.dataset.originalIndex || '0', 10);
        const ib = parseInt(b.dataset.originalIndex || '0', 10);
        return ia - ib;
      });
    } else {
      // ยอดนิยม (หรือค่าอื่น ๆ) = ลำดับเดิมตาม originalIndex
      sorted.sort((a, b) => {
        const ia = parseInt(a.dataset.originalIndex || '0', 10);
        const ib = parseInt(b.dataset.originalIndex || '0', 10);
        return ia - ib;
      });
    }

    // จัด DOM ใหม่ตามลำดับที่เรียงแล้ว
    sorted.forEach(li => listEl.appendChild(li));

    // แจ้งให้ระบบ overlay (ดูเพิ่มเติม) รู้ว่ามีการเรียงใหม่ → มันจะ re-apply 4 แถว + แถว 5 เบลอให้อัตโนมัติ
    try {
      window.dispatchEvent(new CustomEvent('sort-change', { detail: { key: sortKey } }));
    } catch (_) {
      const e = document.createEvent('CustomEvent');
      e.initCustomEvent('sort-change', true, true, { key: sortKey });
      window.dispatchEvent(e);
    }
  }

  // ตั้งค่าตอนเริ่มต้นตาม aria-selected (ปุ่มยอดนิยมเป็น true อยู่แล้ว)
  const initial = buttons.find(b => b.getAttribute('aria-selected') === 'true') || buttons[0];
  if (initial) {
    setActive(initial);
    sortListBy(initial.dataset.sort || 'popular');
  }

  // คลิกปุ่มเมนู
  sortMenu.addEventListener('click', (ev) => {
    const btn = ev.target.closest('button.sort-btn');
    if (!btn || !sortMenu.contains(btn)) return;
    setActive(btn);
    sortListBy(btn.dataset.sort || 'popular');
  });

  // รองรับกด Enter / Spacebar บนปุ่ม
  sortMenu.addEventListener('keydown', (ev) => {
    if (ev.key !== 'Enter' && ev.key !== ' ') return;
    const btn = ev.target.closest('button.sort-btn');
    if (!btn || !sortMenu.contains(btn)) return;
    ev.preventDefault();
    setActive(btn);
    sortListBy(btn.dataset.sort || 'popular');
  });
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
    const total = items.length;

    // reset ทั้งหมดก่อน
    items.forEach(li => {
      li.classList.remove('vp-partial', 'hidden');
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
        if (i < max) items[i].classList.remove('hidden', 'vp-partial');
        else items[i].classList.add('hidden');
      }
      const btn = wrap.querySelector('#btnMoreOverlay');
      if (btn) btn.classList.add('hidden');
      return;
    }

    // ===== สถานะปกติ (ยังไม่เคยกดดูเพิ่มเติม) =====

    // ซ่อนการ์ดเกินแถวที่ 10
    for (let i = EXPAND_LIMIT; i < total; i++) {
      items[i].classList.add('hidden');
    }

    // ทำแถวที่ 5 ให้เบลอ (ใบ index 8–9)
    const previewEnd = Math.min(PREVIEW_LIMIT, total);
    for (let i = INITIAL; i < previewEnd; i++) {
      items[i].classList.add('vp-partial');
    }

    // ซ่อนแถวที่ 6–10 (ใบ index 10–19)
    const expandEnd = Math.min(EXPAND_LIMIT, total);
    for (let i = PREVIEW_LIMIT; i < expandEnd; i++) {
      items[i].classList.add('hidden');
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
    const total = items.length;
    const max = Math.min(EXPAND_LIMIT, total);

    for (let i = INITIAL; i < max; i++) {
      items[i].classList.remove('hidden', 'vp-partial');
    }

    const btn = ul.parentElement.querySelector('#btnMoreOverlay');
    if (btn) btn.classList.add('hidden');

    // ตั้ง flag ว่าได้ขยายแล้ว → sort-change ครั้งต่อไปจะไม่กลับมาเบลอแถว 5
    isExpanded = true;
  }

  // เรียกตอนโหลดครั้งแรก
  applyPaging();

  // เวลา sort เปลี่ยน → re-apply layout ตามสถานะ isExpanded
  window.addEventListener('sort-change', () => {
    requestAnimationFrame(() => applyPaging());
  });
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

  if (mainSearchBtn) {
    mainSearchBtn.addEventListener('click', () => {
      // Check which form is visible and submit it
      if (!formHourly.classList.contains('hidden')) {
        formHourly.submit();
      } else if (!formDaily.classList.contains('hidden')) {
        formDaily.submit();
      }
    });
  }

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
