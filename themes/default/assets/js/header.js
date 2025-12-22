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
  const overlaySearchResults = document.getElementById("overlaySearchResults");

  // Search Data (Simulated - in production this would be an API call)
  const searchData = [
    { name: "Concrete Volume Calculator", category: "Civil", url: "civil" },
    { name: "Brick Calculator", category: "Civil", url: "civil" },
    {
      name: "Electrical Load Calculation",
      category: "Electrical",
      url: "electrical",
    },
    {
      name: "Voltage Drop Calculator",
      category: "Electrical",
      url: "electrical",
    },
    { name: "Pipe Sizing Calculator", category: "Plumbing", url: "plumbing" },
    { name: "Water Flow Calculator", category: "Plumbing", url: "plumbing" },
    { name: "HVAC Duct Sizing", category: "HVAC", url: "hvac" },
    { name: "BTU Calculator", category: "HVAC", url: "hvac" },
    {
      name: "Fire Sprinkler Calculation",
      category: "Fire Protection",
      url: "fire",
    },
    {
      name: "Site Grading Calculator",
      category: "Site Development",
      url: "site",
    },
  ];

  function performSearch(query, container) {
    if (!container) return;

    if (query.length < 2) {
      container.innerHTML = "";
      return;
    }

    const filtered = searchData.filter(
      (item) =>
        item.name.toLowerCase().includes(query.toLowerCase()) ||
        item.category.toLowerCase().includes(query.toLowerCase())
    );

    if (filtered.length === 0) {
      container.innerHTML =
        '<div class="p-3 text-gray-500 text-center">No tools found</div>';
      return;
    }

    container.innerHTML = filtered
      .map(
        (result) => `
            <a href="${result.url}" class="block p-3 hover:bg-blue-50 border-b border-gray-100 last:border-0 text-left">
                <div class="font-medium text-gray-800">${result.name}</div>
                <div class="text-sm text-gray-500">${result.category}</div>
            </a>
        `
      )
      .join("");
  }

  // Inline Search (Header Middle)
  if (globalSearch && searchSuggestions) {
    globalSearch.addEventListener("input", function () {
      performSearch(this.value.trim(), searchSuggestions);
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
        setTimeout(() => overlaySearchInput.focus(), 100);
      }
      document.body.style.overflow = "hidden"; // Prevent scrolling
    });

    // Close Overlay logic
    function closeOverlay() {
      searchOverlay.classList.remove("active");
      document.body.style.overflow = "";
      if (overlaySearchInput) overlaySearchInput.value = "";
      if (overlaySearchResults) overlaySearchResults.innerHTML = "";
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
    if (overlaySearchInput && overlaySearchResults) {
      overlaySearchInput.addEventListener("input", function () {
        performSearch(this.value.trim(), overlaySearchResults);
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
