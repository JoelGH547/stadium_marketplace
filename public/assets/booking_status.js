
// Tab Switching Logic
function switchTab(tabName) {
    // Hide all contents
    ['pending', 'confirmed', 'cancelled'].forEach(t => {
        const content = document.getElementById('content-' + t);
        if(content) {
            content.classList.add('hidden');
            content.classList.remove('block');
        }
        
        // Reset tab styles
        const btn = document.getElementById('tab-' + t);
        if(btn) {
            btn.classList.remove('border-[var(--primary)]', 'text-[var(--primary)]');
            btn.classList.add('border-transparent', 'text-gray-500', 'hover:border-gray-300', 'hover:text-gray-700');
        }
    });

    // Show active content
    const activeContent = document.getElementById('content-' + tabName);
    if(activeContent) {
        activeContent.classList.remove('hidden');
        activeContent.classList.add('block');
    }

    // Highlight active tab
    const activeBtn = document.getElementById('tab-' + tabName);
    if(activeBtn) {
        activeBtn.classList.remove('border-transparent', 'text-gray-500', 'hover:border-gray-300', 'hover:text-gray-700');
        activeBtn.classList.add('border-[var(--primary)]', 'text-[var(--primary)]');
    }
}

// Review Modal Logic
function openReviewModal(data) {
    document.getElementById('modalBookingId').value = data.id;
    document.getElementById('modalStadiumName').innerText = data.stadium_name;
    document.getElementById('modalFieldName').innerText = data.field_name;
    document.getElementById('modalBookingDate').innerText = 'จองเมื่อ: ' + data.booking_date; // Basic format, can be improved if needed
    
    // Reset form
    document.getElementById('modalComment').value = '';
    setRating(5); // Default to 5 stars

    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}

// Star Rating Logic
function setRating(rating) {
    document.getElementById('modalRating').value = rating;
    const stars = document.querySelectorAll('.star-btn svg');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('text-yellow-400');
            star.classList.remove('text-gray-300');
        } else {
            star.classList.add('text-gray-300');
            star.classList.remove('text-yellow-400');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Attach click events to stars
    const starBtns = document.querySelectorAll('.star-btn');
    starBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const rating = this.getAttribute('data-value');
            setRating(parseInt(rating));
        });
    });
});
