document.addEventListener("DOMContentLoaded", function () {
  const header = document.getElementById("siteHeader");
  const hamburgerBtn = document.getElementById("hamburgerBtn");
  const mobileNav = document.getElementById("mobileNav");
  const themeToggleBtn = document.getElementById("themeToggleBtn");
  const globalSearch = document.getElementById("globalSearch");
  const searchSuggestions = document.getElementById("searchSuggestions");

  // Scroll effect for header
  window.addEventListener("scroll", function () {
    if (window.scrollY > 50) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  });

  // Mobile menu toggle
  if (hamburgerBtn) {
    hamburgerBtn.addEventListener("click", function () {
      mobileNav.classList.toggle("active");
      this.innerHTML = mobileNav.classList.contains("active")
        ? '<i class="fas fa-times"></i>'
        : '<i class="fas fa-bars"></i>';
    });
  }

  // Set permanent dark theme (navy blue)
  document.body.classList.add("dark-theme");
  document.body.setAttribute("data-theme", "dark");

  // Disable theme toggle - dark mode only
  if (themeToggleBtn) {
    themeToggleBtn.style.opacity = "0.3";
    themeToggleBtn.style.cursor = "not-allowed";
    themeToggleBtn.innerHTML = '<i class="fas fa-moon"></i>';
    themeToggleBtn.title = "Dark Mode (Always On)";
  }

  // Enhanced search functionality
  const searchToggleBtn = document.getElementById("searchToggleBtn");
  const searchOverlay = document.getElementById("searchOverlay");
  const searchOverlayClose = document.getElementById("searchOverlayClose");
  const overlaySearchInput = document.getElementById("overlaySearchInput");

  // Comprehensive Search Data with icons and descriptions
  const searchData = [
    { name: "Concrete Volume Calculator", category: "Civil", icon: "fas fa-cube", url: "civil", description: "Calculate concrete volume for construction projects", popular: true },
    { name: "Brick Calculator", category: "Civil", icon: "fas fa-th", url: "civil", description: "Estimate bricks needed for walls", popular: true },
    { name: "Steel Weight Calculator", category: "Civil", icon: "fas fa-weight-hanging", url: "civil", description: "Calculate steel reinforcement weight" },
    { name: "Electrical Load Calculation", category: "Electrical", icon: "fas fa-bolt", url: "electrical", description: "Calculate electrical load requirements", popular: true },
    { name: "Voltage Drop Calculator", category: "Electrical", icon: "fas fa-plug", url: "electrical", description: "Calculate voltage drop in circuits" },
    { name: "Wire Size Calculator", category: "Electrical", icon: "fas fa-ethernet", url: "electrical", description: "Determine proper wire gauge" },
    { name: "Pipe Sizing Calculator", category: "Plumbing", icon: "fas fa-faucet", url: "plumbing", description: "Calculate optimal pipe dimensions", popular: true },
    { name: "Water Flow Calculator", category: "Plumbing", icon: "fas fa-water", url: "plumbing", description: "Calculate water flow rates" },
    { name: "Drainage Calculator", category: "Plumbing", icon: "fas fa-tint", url: "plumbing", description: "Design drainage systems" },
    { name: "HVAC Duct Sizing", category: "HVAC", icon: "fas fa-wind", url: "hvac", description: "Calculate duct dimensions", popular: true },
    { name: "BTU Calculator", category: "HVAC", icon: "fas fa-thermometer-half", url: "hvac", description: "Calculate heating/cooling requirements" },
    { name: "Airflow Calculator", category: "HVAC", icon: "fas fa-fan", url: "hvac", description: "Calculate air flow rates" },
    { name: "Fire Sprinkler Calculation", category: "Fire Protection", icon: "fas fa-fire-extinguisher", url: "fire", description: "Design fire sprinkler systems", popular: true },
    { name: "Fire Hydrant Flow", category: "Fire Protection", icon: "fas fa-fire", url: "fire", description: "Calculate hydrant flow rates" },
    { name: "Site Grading Calculator", category: "Site Development", icon: "fas fa-map-marked-alt", url: "site", description: "Calculate earthwork volumes" },
    { name: "Excavation Calculator", category: "Site Development", icon: "fas fa-hard-hat", url: "site", description: "Estimate excavation requirements" },
  ];

  // Category icons
  const categoryIcons = {
    "Civil": "fas fa-hard-hat",
    "Electrical": "fas fa-bolt",
    "Plumbing": "fas fa-faucet",
    "HVAC": "fas fa-wind",
    "Fire Protection": "fas fa-fire-extinguisher",
    "Site Development": "fas fa-map-marked-alt"
  };

  // Create tool list item HTML
  function createToolItem(tool) {
    if (!tool) return '';
    const icon = tool.icon || 'fas fa-tools';
    const name = tool.name || 'Unknown Tool';
    const category = tool.category || 'General';
    const url = tool.url || '#';
    
    return `
      <a href="${url}" class="tool-item">
        <div class="tool-item-icon">
          <i class="${icon}"></i>
        </div>
        <div class="tool-item-content">
          <div class="tool-item-title">${name}</div>
          <div class="tool-item-category">${category}</div>
        </div>
      </a>
    `;
  }

  // Perform search and display results as list
  function performOverlaySearch(query) {
    try {
      const searchResultsSection = document.getElementById("searchResultsSection");
      const searchStats = document.getElementById("searchStats");
      
      if (!searchResultsSection) return;

      if (!query || query.length < 2) {
        searchResultsSection.innerHTML = "";
        if (searchStats) searchStats.textContent = "";
        return;
      }

      // Safe filtering
      const filtered = searchData.filter((item) => {
        if (!item) return false;
        const q = query.toLowerCase();
        const name = (item.name || '').toLowerCase();
        const cat = (item.category || '').toLowerCase();
        const desc = (item.description || '').toLowerCase();
        return name.includes(q) || cat.includes(q) || desc.includes(q);
      });

      // Update stats
      if (searchStats) {
        searchStats.textContent = `${filtered.length} result${filtered.length !== 1 ? 's' : ''}`;
      }

      if (filtered.length === 0) {
        searchResultsSection.innerHTML = `
          <div class="no-results">
            <i class="fas fa-search"></i>
            <p>No tools found for "${query}"</p>
          </div>
        `;
        return;
      }

      // Group by category
      const grouped = {};
      filtered.forEach(tool => {
        const cat = tool.category || 'General';
        if (!grouped[cat]) {
          grouped[cat] = [];
        }
        grouped[cat].push(tool);
      });

      // Build categorized HTML as list
      let html = "";
      Object.keys(grouped).forEach(category => {
        const icon = categoryIcons[category] || "fas fa-tools";
        html += `
          <h3 class="category-title">
            <i class="${icon}"></i>
            ${category}
          </h3>
          <div class="tools-list">
            ${grouped[category].map(tool => createToolItem(tool)).join("")}
          </div>
        `;
      });

      searchResultsSection.innerHTML = html;
      
    } catch (e) {
      console.error("Search error:", e);
      const stats = document.getElementById("searchStats");
      if (stats) stats.textContent = "Error searching";
    }
  }

  // Inline Search (Header Middle)
  if (globalSearch && searchSuggestions) {
    globalSearch.addEventListener("input", function () {
      const query = this.value.trim();
      
      if (query.length < 2) {
        searchSuggestions.innerHTML = "";
        return;
      }

      const filtered = searchData.filter(
        (item) =>
          item.name.toLowerCase().includes(query.toLowerCase()) ||
          item.category.toLowerCase().includes(query.toLowerCase())
      );

      if (filtered.length === 0) {
        searchSuggestions.innerHTML =
          '<div class="p-3 text-gray-500 text-center">No tools found</div>';
        return;
      }

      searchSuggestions.innerHTML = filtered
        .map(
          (result) => `
            <a href="${result.url}" class="block p-3 hover:bg-blue-50 border-b border-gray-100 last:border-0 text-left">
                <div class="font-medium text-gray-800">${result.name}</div>
                <div class="text-sm text-gray-500">${result.category}</div>
            </a>
        `
        )
        .join("");
    });

    globalSearch.addEventListener("focus", function () {
      this.setAttribute("placeholder", "Press / to search...");
    });

    globalSearch.addEventListener("blur", function () {
      this.setAttribute("placeholder", "Search 50+ engineering tools...");
    });
  }

  // Overlay Search
  if (searchToggleBtn && searchOverlay) {
    // Open Overlay
    searchToggleBtn.addEventListener("click", function (e) {
      e.preventDefault();
      searchOverlay.classList.add("active");
      if (overlaySearchInput) {
        overlaySearchInput.value = ""; // Clear input
        setTimeout(() => overlaySearchInput.focus(), 100);
      }
      document.body.style.overflow = "hidden"; // Prevent scrolling
    });

    // Close Overlay logic
    function closeOverlay() {
      searchOverlay.classList.remove("active");
      document.body.style.overflow = "";
      if (overlaySearchInput) overlaySearchInput.value = "";
      const searchResultsSection = document.getElementById("searchResultsSection");
      if (searchResultsSection) searchResultsSection.innerHTML = "";
      const searchStats = document.getElementById("searchStats");
      if (searchStats) searchStats.textContent = "";
    }

    if (searchOverlayClose) {
      searchOverlayClose.addEventListener("click", closeOverlay);
    }

    // Close on click outside content
    searchOverlay.addEventListener("click", function (e) {
      if (e.target === searchOverlay) {
        closeOverlay();
      }
    });

    // Close on Escape
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && searchOverlay.classList.contains("active")) {
        closeOverlay();
      }
    });

    // Search Input Logic
    if (overlaySearchInput) {
      overlaySearchInput.addEventListener("input", function () {
        performOverlaySearch(this.value.trim());
      });
    }
  }

  // Dropdown and mobile menu closing logic
  document.addEventListener("click", function (event) {
    // Close mobile nav
    if (mobileNav && hamburgerBtn && !header.contains(event.target)) {
      mobileNav.classList.remove("active");
      hamburgerBtn.innerHTML = '<i class="fas fa-bars"></i>';
    }

    // Close active dropdown
    const activeDropdown = document.querySelector(".dropdown-active");
    if (activeDropdown && !activeDropdown.contains(event.target)) {
      activeDropdown.classList.remove("dropdown-active");
    }
  });

  // Dropdown toggle
  const dropdownToggle = document.querySelector(".dropdown-toggle");
  if (dropdownToggle) {
    dropdownToggle.addEventListener("click", function (event) {
      event.preventDefault();
      this.parentElement.classList.toggle("dropdown-active");
    });
  }

  // Keyboard shortcuts
  document.addEventListener("keydown", function (event) {
    // Ctrl+K or / for search
    if (
      globalSearch &&
      ((event.ctrlKey && event.key === "k") || event.key === "/")
    ) {
      event.preventDefault();
      globalSearch.focus();
    }

    // Escape to close mobile menu
    if (mobileNav && hamburgerBtn && event.key === "Escape") {
      mobileNav.classList.remove("active");
      hamburgerBtn.innerHTML = '<i class="fas fa-bars"></i>';
    }
  });
});
