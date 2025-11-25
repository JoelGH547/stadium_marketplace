document.addEventListener('DOMContentLoaded', function () {
  // ====== 1. ระยะห่างจากผู้ใช้ + เวลาเปิดปิดสนาม ======
  const article = document.getElementById('stadiumDetail');
  let openTimeRaw = '';
  let closeTimeRaw = '';

  if (article) {
    const lat = parseFloat(article.dataset.lat || '');
    const lng = parseFloat(article.dataset.lng || '');
    const distBadgeSpan = article.querySelector('.dist-badge span:last-child');

    // เวลาเปิด-ปิดจาก data-attribute (เช่น 09:00 หรือ 09:00:00)
    openTimeRaw = (article.dataset.openTime || '').trim();
    closeTimeRaw = (article.dataset.closeTime || '').trim();

    function haversine(lat1, lon1, lat2, lon2) {
      const toRad = (deg) => (deg * Math.PI) / 180;
      const R = 6371; // km
      const dLat = toRad(lat2 - lat1);
      const dLon = toRad(lon2 - lon1);
      const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRad(lat1)) *
        Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return R * c;
    }

    function updateDistance(userLat, userLng) {
      if (!distBadgeSpan || isNaN(lat) || isNaN(lng)) return;

      const d = haversine(userLat, userLng, lat, lng);
      let text = '-- km.';

      if (d < 1) {
        text = (d * 1000).toFixed(0) + ' m.';
      } else {
        text = d.toFixed(1) + ' km.';
      }

      distBadgeSpan.textContent = text;
    }

    if (navigator.geolocation && !isNaN(lat) && !isNaN(lng)) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          updateDistance(pos.coords.latitude, pos.coords.longitude);
        },
        () => {
          // ถ้า user ไม่ยอมแชร์ location ก็ปล่อยเป็น -- km. ไป
        },
        { enableHighAccuracy: false, maximumAge: 300000, timeout: 8000 }
      );
    }
  }

  // ====== 4. แกลเลอรี Hero รูปสนาม + อนิเมชัน ======
  const heroSection = document.getElementById('stadiumHero');
  if (!heroSection) return;

  const imgEl = document.getElementById('heroImage');
  if (!imgEl) return;

  let images = [];
  try {
    const raw = heroSection.dataset.images || '[]';
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) {
      images = parsed.filter(
        (src) => typeof src === 'string' && src.trim() !== ''
      );
    }
  } catch (e) {
    console.error('Invalid gallery images JSON in stadiumHero', e);
  }

  if (images.length === 0 && imgEl.src) {
    images = [imgEl.src];
  }

  let index = 0;

  function showImage(target) {
    if (!images.length) return;

    if (target < 0) {
      target = images.length - 1;
    } else if (target >= images.length) {
      target = 0;
    }

    // slide + fade ออกจากเฟรม
    imgEl.classList.add('opacity-0', 'translate-x-4');

    setTimeout(() => {
      index = target;
      imgEl.src = images[index];

      imgEl.onload = () => {
        imgEl.classList.remove('translate-x-4');
        requestAnimationFrame(() => {
          imgEl.classList.remove('opacity-0');
        });
      };
    }, 150);
  }

  // คลิกที่ภาพให้เลื่อนไปภาพถัดไป
  heroSection.addEventListener('click', () => {
    showImage(index + 1);
  });

  const prevBtn = heroSection.querySelector('[data-hero-prev]');
  const nextBtn = heroSection.querySelector('[data-hero-next]');

  if (prevBtn) {
    prevBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      showImage(index - 1);
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      showImage(index + 1);
    });
  }
});