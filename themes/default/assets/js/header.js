document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('siteHeader');
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mobileNav = document.getElementById('mobileNav');
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const globalSearch = document.getElementById('globalSearch');
    const searchSuggestions = document.getElementById('searchSuggestions');

    // Scroll effect for header
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Mobile menu toggle
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function() {
            mobileNav.classList.toggle('active');
            this.innerHTML = mobileNav.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
    }

    // Set permanent dark theme (navy blue)
    document.body.classList.add('dark-theme');
    document.body.setAttribute('data-theme', 'dark');

    // Disable theme toggle - dark mode only
    if (themeToggleBtn) {
        themeToggleBtn.style.opacity = '0.3';
        themeToggleBtn.style.cursor = 'not-allowed';
        themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
        themeToggleBtn.title = 'Dark Mode (Always On)';
    }

    // Enhanced search functionality
    if (globalSearch) {
        globalSearch.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (query.length < 2) {
                searchSuggestions.innerHTML = '';
                return;
            }

            // Simulate search results (replace with actual search API)
            const results = [
                { name: 'Concrete Volume Calculator', category: 'Civil', url: 'civil.php#concrete' },
                { name: 'Electrical Load Calculation', category: 'Electrical', url: 'electrical.php#load' },
                { name: 'Pipe Sizing Calculator', category: 'Plumbing', url: 'plumbing.php#pipe' },
                { name: 'HVAC Duct Sizing', category: 'HVAC', url: 'hvac.php#duct' },
                { name: 'Fire Sprinkler Calculation', category: 'Fire Protection', url: 'fire.php#sprinkler' }
            ].filter(item => 
                item.name.toLowerCase().includes(query.toLowerCase()) ||
                item.category.toLowerCase().includes(query.toLowerCase())
            );

            displaySearchResults(results);
        });

        globalSearch.addEventListener('focus', function() {
            this.setAttribute('placeholder', 'Press Ctrl+K to search quickly...');
        });

        globalSearch.addEventListener('blur', function() {
            this.setAttribute('placeholder', 'Search 50+ engineering tools...');
        });
    }

    function displaySearchResults(results) {
        if (searchSuggestions) {
            if (results.length === 0) {
                searchSuggestions.innerHTML = '<div class="p-3 text-gray-500">No tools found</div>';
                return;
            }

            searchSuggestions.innerHTML = results.map(result => `
                <a href="${result.url}" class="block p-3 hover:bg-blue-50 border-b border-gray-100 last:border-0">
                    <div class="font-medium text-gray-800">${result.name}</div>
                    <div class="text-sm text-gray-500">${result.category}</div>
                </a>
            `).join('');
        }
    }

    // Dropdown and mobile menu closing logic
    document.addEventListener('click', function(event) {
        // Close mobile nav
        if (mobileNav && hamburgerBtn && !header.contains(event.target)) {
            mobileNav.classList.remove('active');
            hamburgerBtn.innerHTML = '<i class="fas fa-bars"></i>';
        }

        // Close active dropdown
        const activeDropdown = document.querySelector('.dropdown-active');
        if (activeDropdown && !activeDropdown.contains(event.target)) {
            activeDropdown.classList.remove('dropdown-active');
        }
    });

    // Dropdown toggle
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', function(event) {
            event.preventDefault();
            this.parentElement.classList.toggle('dropdown-active');
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(event) {
        // Ctrl+K or / for search
        if (globalSearch && ((event.ctrlKey && event.key === 'k') || event.key === '/')) {
            event.preventDefault();
            globalSearch.focus();
        }
        
        // Escape to close mobile menu
        if (mobileNav && hamburgerBtn && event.key === 'Escape') {
            mobileNav.classList.remove('active');
            hamburgerBtn.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });
});