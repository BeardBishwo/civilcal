document.addEventListener('DOMContentLoaded', () => {
    console.log('Search Toggle Script Loaded');
    const searchBtn = document.getElementById('searchToggleBtn');
    
    if (!searchBtn) {
        console.error('Search Toggle Button NOT FOUND (ID: searchToggleBtn)');
        return;
    } else {
        console.log('Search Toggle Button Found:', searchBtn);
    }

    // Create Overlay if it doesn't exist
    let overlay = document.querySelector('.search-overlay');
    if (!overlay) {
        console.log('Creating Search Overlay...');
        overlay = document.createElement('div');
        overlay.className = 'search-overlay';
        overlay.innerHTML = `
            <button class="search-overlay-close">&times;</button>
            <div class="search-overlay-content">
                <input type="text" placeholder="Search 50+ engineering tools..." id="overlaySearchInput">
            </div>
        `;
        document.body.appendChild(overlay);
    }

    const closeBtn = overlay.querySelector('.search-overlay-close');
    const input = overlay.querySelector('input');

    // Open Search
    searchBtn.addEventListener('click', (e) => {
        console.log('Search Button Clicked');
        e.preventDefault();
        overlay.classList.add('active');
        console.log('Overlay class added:', overlay.classList);
        setTimeout(() => input.focus(), 100);
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    });

    // Close Search
    const closeSearch = () => {
        console.log('Closing Search');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    };

    closeBtn.addEventListener('click', closeSearch);

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            closeSearch();
        }
    });

    // Close on click outside content
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closeSearch();
        }
    });
});
