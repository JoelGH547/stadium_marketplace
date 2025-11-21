/* ==================== Search panel ==================== */
(function(){
  const openBtn=document.getElementById('openSearch');
  const panel=document.getElementById('searchPanel');
  const backdrop=document.getElementById('searchBackdrop');
  const closeBtn=document.getElementById('closeSearch');
  const clearSports=document.getElementById('clearSports');
  if(!openBtn||!panel) return;

  const activeOn=['bg-[var(--primary)]/10','border-[var(--primary)]/40','text-[var(--primary)]'];

  function show(){ panel.classList.remove('hidden'); backdrop?.classList.remove('hidden'); panel.scrollTop=0; }
  function hide(){ panel.classList.add('hidden'); backdrop?.classList.add('hidden'); }

  openBtn.addEventListener('click', show);
  closeBtn?.addEventListener('click', hide);
  backdrop?.addEventListener('click', hide);

  panel.querySelectorAll('.sport-btn').forEach(btn=>{
    btn.addEventListener('click', ()=> activeOn.forEach(c=> btn.classList.toggle(c)));
  });
  clearSports?.addEventListener('click', ()=>{
    panel.querySelectorAll('.sport-btn').forEach(b=> activeOn.forEach(c=> b.classList.remove(c)));
  });
})();

/* ==================== Balls ==================== */
const Balls=(function(){
  const section=document.getElementById('hero'), canvas=document.getElementById('heroBalls');
  if(!section||!canvas) return { setThrottle:()=>{} };

  const ctx=canvas.getContext('2d');
  let W=0,H=0,balls=[],frame=0,throttled=false;

  const EMOJIS=[{char:'⚽',size:44},{char:'🏀',size:46},{char:'🏸',size:48},{char:'🎾',size:48}];
  const COUNT=8;

  function resize(){
    const r=section.getBoundingClientRect();
    W=canvas.width = Math.floor(r.width*devicePixelRatio);
    H=canvas.height= Math.floor(r.height*devicePixelRatio);
    canvas.style.width = r.width+'px';
    canvas.style.height= r.height+'px';
    ctx.setTransform(devicePixelRatio,0,0,devicePixelRatio,0,0);
  }
  function makeBall(){
    const e=EMOJIS[Math.floor(Math.random()*EMOJIS.length)];
    const sp=1+Math.random()*1.2, a=Math.random()*Math.PI*2;
    return {char:e.char,size:e.size,
      x:Math.random()*(W-e.size*2)+e.size, y:Math.random()*(H-e.size*2)+e.size,
      vx:Math.cos(a)*sp, vy:Math.sin(a)*sp, rot:Math.random()*Math.PI*2, vr:(Math.random()*0.02-0.01)};
  }
  function loop(){
    frame++;
    if(throttled && (frame%2===1)){ requestAnimationFrame(loop); return; }

    ctx.clearRect(0,0,W,H);
    const blur = throttled ? 2 : 6;
    for(let i=0;i<balls.length;i++){
      if(throttled && i%3===0) continue;
      const b=balls[i];
      b.x+=b.vx*2; b.y+=b.vy*2; b.rot+=b.vr;
      if(b.x<b.size||b.x>W-b.size) b.vx*=-1;
      if(b.y<b.size||b.y>H-b.size) b.vy*=-1;

      ctx.save(); ctx.translate(b.x,b.y); ctx.rotate(b.rot);
      ctx.font=`bold ${b.size}px system-ui, Apple Color Emoji, Segoe UI Emoji, Noto Color Emoji`;
      ctx.textAlign='center'; ctx.textBaseline='middle';
      ctx.shadowColor='rgba(0,0,0,0.18)'; ctx.shadowBlur=blur; ctx.globalAlpha=.95;
      ctx.fillText(b.char,0,0); ctx.restore();
    }
    requestAnimationFrame(loop);
  }

  function init(){ resize(); balls=Array.from({length:COUNT}, makeBall); requestAnimationFrame(loop); }
  init(); window.addEventListener('resize', resize);

  return { setThrottle:(v)=>{ throttled=!!v; } };
})();

/* ==================== Deck Carousel ==================== */
(function(){
  const stage=document.getElementById('heroCarousel'); if(!stage) return;
  const slides=[...stage.querySelectorAll('[data-slide]')]; if(!slides.length) return;
  const prevBtn=document.getElementById('prevSlide'), nextBtn=document.getElementById('nextSlide');

  let index=0, autoTimer=null, isAnimating=false;

  function metric(){ const w=stage.clientWidth; return { off:Math.min(96,Math.max(48,w*0.08)), drop:12 }; }
  function pose(role){
    const {off,drop}=metric();
    if(role==='current') return `translate3d(0,0,0) scale(1)`;
    if(role==='prev')    return `translate3d(${-off}px, ${drop}px, 0) scale(.965)`;
    if(role==='next')    return `translate3d(${ off}px, ${drop}px, 0) scale(.965)`;
    return `translate3d(0,24px,0) scale(.9)`;
  }
  function setRole(el, role){
    el.classList.remove('is-current','is-prev','is-next','is-hidden');
    el.classList.add(role==='others' ? 'is-hidden' : `is-${role}`);
    el.style.transform = pose(role);
    el.style.opacity   = (role==='current') ? 1 : (role==='prev'||role==='next') ? 0.98 : 0;
  }
  function layout(){
    const n=slides.length, prev=(index-1+n)%n, next=(index+1)%n;
    slides.forEach((el,i)=>{
      if(i===index)      setRole(el,'current');
      else if(i===prev)  setRole(el,'prev');
      else if(i===next)  setRole(el,'next');
      else               setRole(el,'others');
    });
  }

  function animateTo(newIndex){
    if(isAnimating) return;
    isAnimating=true;
    Balls.setThrottle(true);

    const n=slides.length;
    const old=index;
    index=(newIndex+n)%n;

    const prevOld=(old-1+n)%n, nextOld=(old+1)%n;
    const prevNew=(index-1+n)%n, nextNew=(index+1)%n;

    slides.forEach((el,i)=>{
      let from='others', to='others';
      if(i===old)     from='current'; else if(i===prevOld) from='prev'; else if(i===nextOld) from='next';
      if(i===index)   to='current';   else if(i===prevNew) to='prev';   else if(i===nextNew) to='next';

      const fT=pose(from), tT=pose(to);
      const fO=(from==='current')?1:((from==='prev'||from==='next')?0.98:0);
      const tO=(to==='current')?1:((to==='prev'||to==='next')?0.98:0);

      setRole(el, to);
      el.animate(
        [{transform:fT,opacity:fO},{transform:tT,opacity:tO}],
        {duration:420, easing:'cubic-bezier(.25,.8,.25,1)', fill:'both'}
      );
    });

    setTimeout(()=>{ isAnimating=false; Balls.setThrottle(false); }, 450);
  }
  const go=(d)=>animateTo(index+d);

  prevBtn?.addEventListener('click', ()=>go(-1));
  nextBtn?.addEventListener('click', ()=>go(+1));

  (function attachSwipe(el){
    let startX=null, pid=null;
    el.addEventListener('pointerdown', e=>{ startX=e.clientX; pid=e.pointerId; el.setPointerCapture(pid); });
    el.addEventListener('pointerup', e=>{
      if(startX==null) return;
      const dx=e.clientX-startX; startX=null;
      if(Math.abs(dx)>28) go(dx<0?+1:-1);
    });
    el.addEventListener('pointercancel', ()=>{ startX=null; });
  })(stage);

  stage.tabIndex=0;
  stage.addEventListener('keydown', e=>{
    if(e.key==='ArrowLeft')  go(-1);
    if(e.key==='ArrowRight') go(+1);
  });

  function startAuto(){ if(!autoTimer) autoTimer=setInterval(()=>go(+1), 5600); }
  function stopAuto(){ if(autoTimer){ clearInterval(autoTimer); autoTimer=null; } }
  stage.addEventListener('pointerenter', stopAuto);
  stage.addEventListener('pointerleave', startAuto);
  document.addEventListener('visibilitychange', ()=> document.hidden ? stopAuto() : startAuto());

  let rT=null;
  window.addEventListener('resize', ()=>{
    stopAuto(); clearTimeout(rT);
    rT=setTimeout(()=>{ layout(); startAuto(); }, 120);
  }, {passive:true});

  layout(); startAuto();
})();

/* ==== Arrow buttons for #nearScroller ==== */
(function(){
  const scroller = document.getElementById('nearScroller');
  const leftBtn  = document.getElementById('nearLeft');
  const rightBtn = document.getElementById('nearRight');
  if(!scroller || !leftBtn || !rightBtn) return;

  function stepSize(){
    const card = scroller.querySelector('article');
    if(!card) return 320;
    const rect = card.getBoundingClientRect();
    return Math.round(rect.width + 16);
  }

  leftBtn.addEventListener('click', ()=> scroller.scrollBy({left: -stepSize(), behavior: 'smooth'}));
  rightBtn.addEventListener('click',()=> scroller.scrollBy({left:  stepSize(), behavior: 'smooth'}));

  scroller.querySelectorAll('img').forEach(img=>{
    img.addEventListener('dragstart', e=> e.preventDefault());
  });
})();


/* ============ Nearby distance + limit 8/20 ============ */
document.addEventListener('DOMContentLoaded', () => {
  const nearScroller = document.getElementById('nearScroller');
  const listEl       = document.getElementById('venueItems');

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
        let dist  = Number.POSITIVE_INFINITY;

        if (userLocation && !isNaN(lat) && !isNaN(lng)) {
          dist = haversine(userLocation.lat, userLocation.lng, lat, lng);
        }

        li.dataset.distanceKm = dist.toString();
        li.dataset.distance   = dist.toString();

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
        else          li.classList.add('hidden');
      });
    }

    // จัดการ section สนามใกล้คุณ
    if (nearScroller) {
      const cards = Array.from(nearScroller.querySelectorAll('article'));
      cards.forEach((card) => {
        const lat = parseFloat(card.dataset.lat || '');
        const lng = parseFloat(card.dataset.lng || '');
        let dist  = Number.POSITIVE_INFINITY;

        if (userLocation && !isNaN(lat) && !isNaN(lng)) {
          dist = haversine(userLocation.lat, userLocation.lng, lat, lng);
        }

        card.dataset.distanceKm = dist.toString();
        card.dataset.distance   = dist.toString();

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
        else         card.classList.add('hidden');
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
  const listEl   = document.getElementById('venueItems');
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
    const items  = Array.from(listEl.querySelectorAll('li'));
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

  const PER_ROW       = 2;
  const ROWS_INITIAL  = 4;
  const ROWS_PREVIEW  = 5;
  const ROWS_EXPANDED = 10;

  const INITIAL       = ROWS_INITIAL  * PER_ROW;   // 4 แถวแรก = 8 การ์ด
  const PREVIEW_LIMIT = ROWS_PREVIEW  * PER_ROW;   // แถวที่ 5   = 10 การ์ด
  const EXPAND_LIMIT  = ROWS_EXPANDED * PER_ROW;   // แถว 1–10   = 20 การ์ด

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
        else         items[i].classList.add('hidden');
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
    const max   = Math.min(EXPAND_LIMIT, total);

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
  const panel    = document.getElementById('loginPanel');

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
