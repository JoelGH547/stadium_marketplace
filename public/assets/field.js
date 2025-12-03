document.addEventListener('DOMContentLoaded', () => {
    // --- Gallery Elements ---
    const overlay = document.getElementById('stadiumGalleryOverlay');
    const openBtn = document.querySelector('[data-gallery-open]');
    const closeBtn = document.querySelector('[data-gallery-close]');

    if (!overlay || !openBtn) return;

    const imageContainer = document.getElementById('galleryImageContainer');
    const mainImage = document.getElementById('galleryMainImage');
    const prevBtn = document.getElementById('galleryPrevBtn');
    const nextBtn = document.getElementById('galleryNextBtn');
    const thumbnailBtns = document.querySelectorAll('.gallery-thumb');

    // --- Gallery State ---
    let images = [];
    let currentIndex = 0;

    // --- Zoom & Pan State ---
    let scale = 1;
    let isPanning = false;
    let startPoint = { x: 0, y: 0 };
    let translate = { x: 0, y: 0 };

    // --- Functions ---

    const applyTransform = (force = false) => {
        const containerRect = imageContainer.getBoundingClientRect();
        const imgNaturalWidth = mainImage.naturalWidth;
        const imgNaturalHeight = mainImage.naturalHeight;

        if (!imgNaturalWidth || !imgNaturalHeight) {
            mainImage.style.transform = 'translate(0, 0) scale(1)';
            return;
        }

        const containerAspect = containerRect.width / containerRect.height;
        const imgAspect = imgNaturalWidth / imgNaturalHeight;

        let renderedWidth, renderedHeight;
        if (imgAspect > containerAspect) {
            renderedWidth = containerRect.width;
            renderedHeight = renderedWidth / imgAspect;
        } else {
            renderedHeight = containerRect.height;
            renderedWidth = renderedHeight * imgAspect;
        }

        const scaledWidth = renderedWidth * scale;
        const scaledHeight = renderedHeight * scale;

        const maxTx = Math.max(0, (scaledWidth - containerRect.width) / 2);
        const maxTy = Math.max(0, (scaledHeight - containerRect.height) / 2);

        if (force || !isPanning) {
            translate.x = Math.max(-maxTx, Math.min(maxTx, translate.x));
            translate.y = Math.max(-maxTy, Math.min(maxTy, translate.y));
        }

        mainImage.style.transform = `translate(${translate.x}px, ${translate.y}px) scale(${scale})`;
    };

    const resetZoomAndPan = () => {
        scale = 1;
        translate = { x: 0, y: 0 };
        isPanning = false;
        imageContainer.classList.remove('cursor-grabbing');
        imageContainer.classList.add('cursor-grab');
        mainImage.style.transform = 'translate(0, 0) scale(1)';
    };

    const updateGallery = (index) => {
        resetZoomAndPan();
        currentIndex = index;

        mainImage.style.opacity = '0';

        mainImage.onload = () => {
            mainImage.style.opacity = '1';
            applyTransform(true); // Force initial transform calculation
        };

        mainImage.src = images[index];
        mainImage.alt = `รูปสนาม ${index + 1}`;

        if (mainImage.complete) { // Handles cached images
            mainImage.onload();
        }

        thumbnailBtns.forEach((thumb, i) => {
            if (i === currentIndex) {
                thumb.classList.add('ring-blue-500');
                thumb.classList.remove('ring-transparent');
                thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } else {
                thumb.classList.remove('ring-blue-500');
                thumb.classList.add('ring-transparent');
            }
        });

        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex === images.length - 1;
    };

    const openGallery = () => {
        images = Array.from(thumbnailBtns).map(thumb => thumb.querySelector('img').src);
        if (images.length === 0) return;
        updateGallery(0);
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeGallery = () => {
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        setTimeout(resetZoomAndPan, 200);
    };

    const showNextImage = () => {
        if (currentIndex < images.length - 1) updateGallery(currentIndex + 1);
    };

    const showPrevImage = () => {
        if (currentIndex > 0) updateGallery(currentIndex - 1);
    };

    // --- Event Listeners ---

    openBtn.addEventListener('click', openGallery);
    closeBtn.addEventListener('click', closeGallery);
    nextBtn.addEventListener('click', showNextImage);
    prevBtn.addEventListener('click', showPrevImage);

    thumbnailBtns.forEach(thumb => {
        thumb.addEventListener('click', (e) => {
            updateGallery(parseInt(e.currentTarget.dataset.index, 10));
        });
    });

    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) closeGallery();
    });

    document.addEventListener('keydown', (event) => {
        if (overlay.classList.contains('hidden')) return;
        if (event.key === 'Escape') closeGallery();
        if (event.key === 'ArrowRight') showNextImage();
        if (event.key === 'ArrowLeft') showPrevImage();
    });

    // --- Zoom & Pan Logic ---

    imageContainer.addEventListener('wheel', (e) => {
        e.preventDefault();
        const rect = imageContainer.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;
        const zoomFactor = 1.1;
        const oldScale = scale;

        if (e.deltaY < 0) { // Zoom in
            scale = Math.min(scale * zoomFactor, 10);
        } else { // Zoom out
            scale = Math.max(scale / zoomFactor, 1);
        }

        if (oldScale === scale) return;

        if (scale <= 1) {
            resetZoomAndPan();
            return;
        }

        translate.x = mouseX - (mouseX - translate.x) * (scale / oldScale);
        translate.y = mouseY - (mouseY - translate.y) * (scale / oldScale);

        applyTransform(true);
    });

    imageContainer.addEventListener('mousedown', (e) => {
        if (scale <= 1) return;
        e.preventDefault();
        isPanning = true;
        startPoint = { x: e.clientX - translate.x, y: e.clientY - translate.y };
        imageContainer.classList.remove('cursor-grab');
        imageContainer.classList.add('cursor-grabbing');
    });

    imageContainer.addEventListener('mousemove', (e) => {
        if (!isPanning) return;
        e.preventDefault();
        translate.x = e.clientX - startPoint.x;
        translate.y = e.clientY - startPoint.y;
        applyTransform(false); // Don't clamp while panning for smoother feel
    });

    const stopPanning = () => {
        if (!isPanning) return;
        isPanning = false;
        imageContainer.classList.add('cursor-grab');
        imageContainer.classList.remove('cursor-grabbing');
        applyTransform(true); // Clamp to final position
    };

    imageContainer.addEventListener('mouseup', stopPanning);
    imageContainer.addEventListener('mouseleave', stopPanning);
});

// ========== Leaflet Map ==========
document.addEventListener("DOMContentLoaded", () => {
    const mapEl = document.getElementById("stadium-map");
    if (!mapEl) return;

    const lat = parseFloat(mapEl.dataset.lat);
    const lng = parseFloat(mapEl.dataset.lng);

    if (isNaN(lat) || isNaN(lng)) return;

    // สร้างแผนที่
    const map = L.map(mapEl).setView([lat, lng], 16);

    // Tile จาก OSM (ฟรี)
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // ปักหมุด
    L.marker([lat, lng]).addTo(map);
});

// ===== ระยะทางจากตำแหน่งผู้ใช้ไปยังสนาม (หน้า field) =====
document.addEventListener('DOMContentLoaded', () => {
    const badge = document.querySelector('.dist-badge');
    if (!badge) return;

    const lat = parseFloat(badge.dataset.lat || '');
    const lng = parseFloat(badge.dataset.lng || '');
    if (!isFinite(lat) || !isFinite(lng)) return;

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

    function formatDistance(d) {
        if (!isFinite(d)) return '-- km.';
        if (d < 1) {
            // น้อยกว่า 1 กม. แสดงเป็นเมตร
            return Math.round(d * 1000) + ' m.';
        }
        return d.toFixed(1) + ' km.';
    }

    if (!navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const userLat = pos.coords.latitude;
            const userLng = pos.coords.longitude;

            const distKm = haversine(userLat, userLng, lat, lng);
            const valueEl = badge.querySelector('span:last-child');
            if (valueEl) {
                valueEl.textContent = formatDistance(distKm);
            }
        },
        (err) => {
            console.warn('Geolocation error on field page:', err.message);
            // ถ้าไม่ได้ตำแหน่งก็ปล่อยให้เป็น "-- km." เหมือนเดิม
        },
        {
            enableHighAccuracy: false,
            timeout: 8000,
            maximumAge: 600000,
        }
    );
});
