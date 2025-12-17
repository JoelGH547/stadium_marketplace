function switchTab(tabName) {
    // Hide all contents
    ['pending', 'confirmed', 'cancelled'].forEach(t => {
        document.getElementById('content-' + t).classList.add('hidden');
        document.getElementById('content-' + t).classList.remove('block');
        
        // Reset tab styles
        const btn = document.getElementById('tab-' + t);
        btn.classList.remove('border-[var(--primary)]', 'text-[var(--primary)]');
        btn.classList.add('border-transparent', 'text-gray-500', 'hover:border-gray-300', 'hover:text-gray-700');
    });

    // Show active content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    document.getElementById('content-' + tabName).classList.add('block');

    // Highlight active tab
    const activeBtn = document.getElementById('tab-' + tabName);
    activeBtn.classList.remove('border-transparent', 'text-gray-500', 'hover:border-gray-300', 'hover:text-gray-700');
    activeBtn.classList.add('border-[var(--primary)]', 'text-[var(--primary)]');
}
