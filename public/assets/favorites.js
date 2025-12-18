document.addEventListener('DOMContentLoaded', function () {
  function showLoginPanel() {
    const loginPanel = document.getElementById('loginPanel');
    const loginBackdrop = document.getElementById('loginBackdrop');
    if (!loginPanel || !loginBackdrop) return;

    loginPanel.classList.remove('hidden');
    loginBackdrop.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }

  function getCsrf() {
    const loginForm = document.getElementById('popupLoginForm');
    if (!loginForm) return null;

    const csrfName = loginForm.dataset.csrfName;
    if (!csrfName) return null;

    const csrfField = loginForm.querySelector(`input[name="${csrfName}"]`);
    if (!csrfField) return null;

    return { name: csrfName, value: csrfField.value, field: csrfField };
  }

  function updateCsrf(csrfHash) {
    const csrf = getCsrf();
    if (csrf && csrfHash) csrf.field.value = csrfHash;
  }

  function setButtonState(btn, isFav) {
    btn.dataset.favorited = isFav ? '1' : '0';
    btn.title = isFav ? 'ลบออกจากรายการโปรด' : 'เพิ่มในรายการโปรด';

    // Button style variants (home/field/favorites share these tokens)
    btn.classList.remove('bg-rose-50', 'ring-2', 'ring-rose-200', 'bg-white/90', 'hover:bg-white');
    btn.classList.remove('bg-rose-100', 'ring-1');
    btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-rose-100');

    const isSmallFavBtn = btn.classList.contains('group/favorite'); // field.php style button
    if (isSmallFavBtn) {
      if (isFav) {
        btn.classList.add('bg-rose-100', 'ring-1', 'ring-rose-200');
      } else {
        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-rose-100');
      }
    } else {
      if (isFav) {
        btn.classList.add('bg-rose-50', 'ring-2', 'ring-rose-200');
      } else {
        btn.classList.add('bg-white/90', 'hover:bg-white');
      }
    }

    const svg = btn.querySelector('svg');
    if (svg) {
      svg.classList.remove('text-gray-600', 'text-rose-600');
      svg.classList.add(isFav ? 'text-rose-600' : 'text-gray-600');
    }
  }

  async function toggleFavorite(btn) {
    const stadiumId = parseInt(btn.dataset.stadiumId || '0', 10);
    if (!stadiumId) return;

    if (!window.CUSTOMER_LOGGED_IN) {
      showLoginPanel();
      return;
    }

    const csrf = getCsrf();
    if (!csrf) {
      // If CSRF is disabled, still try without it
      console.warn('CSRF token not found; attempting request without CSRF.');
    }

    const formData = new FormData();
    formData.append('stadium_id', String(stadiumId));
    if (csrf) formData.append(csrf.name, csrf.value);

    const url = window.FAVORITE_TOGGLE_URL || '/sport/favorites/toggle';

    btn.disabled = true;
    btn.classList.add('opacity-70');

    try {
      const resp = await fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });

      const data = await resp.json().catch(() => ({}));
      updateCsrf(data.csrf_hash);

      if (!resp.ok && data.need_login) {
        window.CUSTOMER_LOGGED_IN = false;
        showLoginPanel();
        return;
      }

      if (!data.success) {
        console.warn('Favorite toggle failed', data);
        return;
      }

      const isFav = !!data.favorited;
      setButtonState(btn, isFav);

      // If we're on favorites page and user un-favorites, remove card for nicer UX
      if (!isFav && (location.pathname || '').includes('/sport/favorites')) {
        const card = btn.closest('li');
        if (card) {
          card.style.transition = 'opacity 160ms ease';
          card.style.opacity = '0';
          setTimeout(() => card.remove(), 180);
        }
      }
    } catch (e) {
      console.error(e);
    } finally {
      btn.disabled = false;
      btn.classList.remove('opacity-70');
    }
  }

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-fav-toggle');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();
    toggleFavorite(btn);
  });
});
