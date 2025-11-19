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

/* ==================== Sort Menu Controller ==================== */
document.addEventListener('DOMContentLoaded', function () {
  const wrap = document.getElementById('sortMenu');
  if (!wrap) return;

  wrap.classList.add('relative', 'z-10');

  const getButtons = () => wrap.querySelectorAll('button.sort-btn, button[data-sort]');

  function setActive(btn) {
    const buttons = getButtons();
    buttons.forEach(function (b) {
      b.classList.remove('bg-gray-900', 'text-white', 'font-semibold');
      b.classList.add('text-gray-700');
      b.setAttribute('aria-selected', 'false');
    });

    btn.classList.add('bg-[var(--primary)]', 'text-white', 'font-semibold');
    btn.classList.remove('text-gray-700');
    btn.setAttribute('aria-selected', 'true');
  }

  var current =
    wrap.querySelector('button[aria-selected="true"]') ||
    getButtons()[0];

  if (current) setActive(current);

  wrap.addEventListener('click', function (e) {
    const btn = e.target.closest('button.sort-btn, button[data-sort]');
    if (!btn || !wrap.contains(btn)) return;

    e.preventDefault();
    setActive(btn);

    const key = btn.dataset.sort || '';
    try {
      window.dispatchEvent(new CustomEvent('sort-change', { detail: { key: key } }));
    } catch (_) {
      const evt = document.createEvent('CustomEvent');
      evt.initCustomEvent('sort-change', true, true, { key: key });
      window.dispatchEvent(evt);
    }
  });

  wrap.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    const btn = e.target.closest('button.sort-btn, button[data-sort]');
    if (!btn) return;
    e.preventDefault();
    setActive(btn);

    const key = btn.dataset.sort || '';
    try {
      window.dispatchEvent(new CustomEvent('sort-change', { detail: { key: key } }));
    } catch (_) {
      const evt = document.createEvent('CustomEvent');
      evt.initCustomEvent('sort-change', true, true, { key: key });
      window.dispatchEvent(evt);
    }
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

  const INITIAL       = ROWS_INITIAL  * PER_ROW;
  const PREVIEW_LIMIT = ROWS_PREVIEW  * PER_ROW;
  const EXPAND_LIMIT  = ROWS_EXPANDED * PER_ROW;

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

  if (!ul.parentElement.classList.contains('vp-wrap')) {
    const wrapDiv = document.createElement('div');
    wrapDiv.className = 'vp-wrap';
    ul.parentElement.insertBefore(wrapDiv, ul);
    wrapDiv.appendChild(ul);
  }
  const wrap = ul.parentElement;

  function applyPaging() {
    const items = Array.from(ul.children).filter(el => el.tagName === 'LI');
    items.forEach(li => {
      li.classList.remove('vp-partial', 'hidden');
      li.style.removeProperty('maxHeight');
    });

    const total = items.length;

    if (total <= INITIAL) {
      const b = wrap.querySelector('#btnMoreOverlay');
      if (b) b.remove();
      return;
    }

    for (let i = EXPAND_LIMIT; i < total; i++) {
      items[i].classList.add('hidden');
    }

    const previewEnd = Math.min(PREVIEW_LIMIT, total);
    for (let i = INITIAL; i < previewEnd; i++) {
      items[i].classList.add('vp-partial');
    }

    const expandEnd = Math.min(EXPAND_LIMIT, total);
    for (let i = previewEnd; i < expandEnd; i++) {
      items[i].classList.add('hidden');
    }

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
    const max = Math.min(EXPAND_LIMIT, items.length);

    for (let i = INITIAL; i < max; i++) {
      items[i].classList.remove('hidden', 'vp-partial');
    }

    const btn = wrap.querySelector('#btnMoreOverlay');
    if (btn) btn.classList.add('hidden');
  }

  applyPaging();

  window.addEventListener('sort-change', () => {
    requestAnimationFrame(() => applyPaging());
  });
});

/* ==================== Venue DOM Sorter ==================== */
document.addEventListener('DOMContentLoaded', () => {
  const list = document.getElementById('venueItems');
  const sortMenu = document.getElementById('sortMenu');
  if (!list || !sortMenu) return;

  Array.from(list.children).forEach((li, i) => { if (!li.dataset._idx) li.dataset._idx = String(i); });

  const toNum = (v, d=0) => {
    const n = Number(v);
    return Number.isFinite(n) ? n : d;
  };

  function readMetrics(li){
    let price   = li.dataset.price ?? '';
    let distKm  = li.dataset.distanceKm ?? li.dataset['distance-km'] ?? li.dataset.dist ?? '';
    let rating  = li.dataset.rating ?? '';
    let popular = li.dataset.popular ?? '';

    if (price === '')  price  = (li.textContent.match(/฿\s*([\d,]+)/)?.[1] || '').replace(/,/g,'');
    if (distKm === '') distKm = (li.textContent.match(/([\d.]+)\s*km/i)?.[1] || '');
    if (rating === '') rating = (li.textContent.match(/⭐\s*([\d.]+)/)?.[1] || '');
    if (popular === '') popular = rating || '0';

    return {
      price:   toNum(price,   0),
      distKm:  toNum(distKm,  1e6),
      rating:  toNum(rating,  0),
      popular: toNum(popular, 0),
      idx:     toNum(li.dataset._idx, 0),
    };
  }

  function sortList(key){
    const items = Array.from(list.children);
    items.sort((a,b)=>{
      const A = readMetrics(a), B = readMetrics(b);
      if (key==='price'  && A.price  != B.price)  return A.price - B.price;
      if (key==='nearby' && A.distKm != B.distKm) return A.distKm - B.distKm;
      if (key==='rating' && A.rating != B.rating) return B.rating - A.rating;
      if (key!=='price' && key!=='nearby' && key!=='rating' && A.popular != B.popular) return B.popular - A.popular;
      return A.idx - B.idx;
    });
    const frag = document.createDocumentFragment();
    items.forEach(li=> frag.appendChild(li));
    list.appendChild(frag);
  }

  const current = sortMenu.querySelector('button[aria-selected="true"]');
  sortList(current?.dataset.sort || 'popular');

  window.addEventListener('sort-change', (e)=> sortList(e?.detail?.key || 'popular'));
});

/* ==================== FIX Exclusive Active for Sort Menu ==================== */
document.addEventListener('DOMContentLoaded', () => {
  const wrap = document.getElementById('sortMenu');
  if (!wrap) return;

  const ACTIVE = ['bg-[var(--primary)]','text-white','font-semibold'];
  const INACTIVE_ADD = ['text-gray-700','hover:text-[var(--primary)]','hover:bg-[var(--primary)]/10'];
  const INACTIVE_REMOVE = ['bg-[var(--primary)]','text-white','font-semibold','bg-gray-900','hover:text-gray-900','hover:bg-gray-50'];

  function setExclusiveActive(btn){
    const buttons = wrap.querySelectorAll('button.sort-btn, button[data-sort]');
    buttons.forEach(b => {
      INACTIVE_REMOVE.forEach(c => b.classList.remove(c));
      INACTIVE_ADD.forEach(c => { if (!b.classList.contains(c)) b.classList.add(c); });
      b.setAttribute('aria-selected','false');
    });
    INACTIVE_ADD.forEach(c => btn.classList.remove(c));
    ACTIVE.forEach(c => { if (!btn.classList.contains(c)) btn.classList.add(c); });
    btn.setAttribute('aria-selected','true');
  }

  const current = wrap.querySelector('button[aria-selected="true"]') || wrap.querySelector('button.sort-btn, button[data-sort]');
  if (current) setExclusiveActive(current);

  wrap.addEventListener('click', (e) => {
    const btn = e.target.closest('button.sort-btn, button[data-sort]');
    if (!btn || !wrap.contains(btn)) return;
    setExclusiveActive(btn);
  }, true);

  wrap.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    const btn = e.target.closest('button.sort-btn, button[data-sort]');
    if (!btn || !wrap.contains(btn)) return;
    e.preventDefault();
    setExclusiveActive(btn);
  }, true);
});

/* ==================== Distance Calculator ==================== */
document.addEventListener('DOMContentLoaded', () => {
  const list = document.getElementById('venueItems');
  const nearScroller = document.getElementById('nearScroller');
  if ((!list && !nearScroller) || !navigator.geolocation) return;

  function toRad(deg){ return deg * Math.PI / 180; }
  function haversineKm(lat1, lon1, lat2, lon2){
    const R = 6371;
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a =
      Math.sin(dLat/2)**2 +
      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
      Math.sin(dLon/2)**2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
  }

  function updateDistanceForElement(el, baseLat, baseLon){
    const lat = Number(el.dataset.lat);
    const lon = Number(el.dataset.lng);
    if (!Number.isFinite(lat) || !Number.isFinite(lon)) return;

    const distKm = haversineKm(baseLat, baseLon, lat, lon);
    const kmForSort = distKm.toFixed(2);
    const label = distKm < 1
      ? Math.round(distKm * 1000) + 'm.'
      : distKm.toFixed(1) + 'km.';

    // ใช้กับ sort ในลิสต์ด้านล่าง
    el.dataset.distanceKm = kmForSort;

    const badgeSpan = el.querySelector('.dist-badge span:last-child');
    if (badgeSpan) badgeSpan.textContent = label;
  }

  navigator.geolocation.getCurrentPosition(
  (pos) => {
    const baseLat = pos.coords.latitude;
    const baseLon = pos.coords.longitude;

    // 1) update distance for venueList
    if (list) {
      list.querySelectorAll('li[data-lat][data-lng]').forEach(li => {
        updateDistanceForElement(li, baseLat, baseLon);
      });
    }

    // 2) update distance for nearScroller
    if (nearScroller) {
      nearScroller.querySelectorAll('article[data-lat][data-lng]').forEach(card => {
        updateDistanceForElement(card, baseLat, baseLon);
      });
    }

    // ⭐ 3) Sort nearScroller from near → far
    if (nearScroller) {
      const cards = Array.from(nearScroller.querySelectorAll('article[data-distance-km]'));

      cards.sort((a, b) => {
        const da = parseFloat(a.dataset.distanceKm ?? '99999');
        const db = parseFloat(b.dataset.distanceKm ?? '99999');
        return da - db; // ใกล้ → ไกล
      });

      cards.forEach(card => nearScroller.appendChild(card));
    }

    // trigger sort event for list
    try {
      window.dispatchEvent(new CustomEvent('sort-change', { detail: { key: 'nearby' } }));
    } catch (_) {
      const evt = document.createEvent('CustomEvent');
      evt.initCustomEvent('sort-change', true, true, { key: 'nearby' });
      window.dispatchEvent(evt);
    }
  },
    (err) => {
      console.warn('Cannot get geolocation for venue distance:', err);
    },
    { enableHighAccuracy: true, timeout: 8000 }
  );
});

