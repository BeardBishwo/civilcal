/**
 * Favorites Management System
 * Handles toggling and displaying user favorites
 */

// Get dynamic base path for deployment flexibility
function getBasePath() {
  const path = window.location.pathname;
  const parts = path.split('/').filter(p => p);
  // If we're in a subdirectory (e.g., /Bishwo_Calculator/), detect it
  // Otherwise return root
  if (parts.length > 0 && !parts[0].includes('.')) {
    // Check if first part is likely a base directory
    const firstPart = parts[0];
    if (!['calculator', 'api', 'login', 'dashboard'].includes(firstPart)) {
      return '/' + firstPart;
    }
  }
  return '';
}

const BASE_PATH = getBasePath();

document.addEventListener("DOMContentLoaded", () => {
  initFavorites();
  loadFavoritesList();
});

// Initialize favorite buttons on the page
function initFavorites() {
  const currentPath = window.location.pathname;

  // Only show star on calculator pages (heuristic check)
  if (
    !currentPath.includes("/calculator/") &&
    !currentPath.includes("/feature/")
  )
    return;

  // Inject star icon into header if not exists
  const headerTitle = document.querySelector("h1, h2.mb-0"); // Targets main headers
  if (headerTitle && !document.getElementById("favorite-toggle")) {
    const starBtn = document.createElement("button");
    starBtn.id = "favorite-toggle";
    starBtn.className =
      "btn btn-link text-warning p-0 ms-3 border-0 bg-transparent";
    starBtn.innerHTML = '<i class="bi bi-star fs-4"></i>';
    starBtn.title = "Add to Favorites";
    starBtn.onclick = toggleFavorite;

    headerTitle.parentNode.insertBefore(starBtn, headerTitle.nextSibling);

    checkFavoriteStatus();
  }
}

async function checkFavoriteStatus() {
  // Check if this current page is in favorites list
  const slug = getCalculatorSlug();
  if (!slug) return;

  try {
    const response = await fetch(`${BASE_PATH}/api/favorites`);
    const data = await response.json();

    if (data.success && data.favorites) {
      const exists = data.favorites.find((f) => f.calculator_slug === slug);
      updateStarIcon(!!exists);
    }
  } catch (e) {
    // Likely not logged in
    console.log("Favorites check failed (Guest?)");
  }
}

async function toggleFavorite() {
  const slug = getCalculatorSlug();
  const name = document.querySelector("h1, h2.mb-0").textContent.trim();
  if (!slug) return;

  // Animation feedback
  const btn = document.getElementById("favorite-toggle");
  btn.innerHTML = '<i class="bi bi-hourglass-split fs-4 text-warning"></i>';

  try {
    const response = await fetch(`${BASE_PATH}/api/favorites/toggle`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        calculator_slug: slug,
        calculator_name: name,
        category: "Calculator",
      }),
    });

    const data = await response.json();
    if (data.success) {
      updateStarIcon(data.action === "added");
      // Reload list if sidebar widget exists
      loadFavoritesList();
    } else {
      if (data.message === "Unauthorized") {
        alert("Please login to save favorites!");
      }
      updateStarIcon(false); // Reset
    }
  } catch (e) {
    console.error("Toggle failed", e);
    updateStarIcon(false);
  }
}

function updateStarIcon(isFavorite) {
  const btn = document.getElementById("favorite-toggle");
  if (!btn) return;

  if (isFavorite) {
    btn.innerHTML = '<i class="bi bi-star-fill fs-4"></i>';
    btn.title = "Remove from Favorites";
    btn.classList.add("active-favorite");
  } else {
    btn.innerHTML = '<i class="bi bi-star fs-4"></i>';
    btn.title = "Add to Favorites";
    btn.classList.remove("active-favorite");
  }
}

function getCalculatorSlug() {
  // Extract slug from URL (e.g. /calculator/finance/loan -> finance/loan)
  const path = window.location.pathname;
  const match = path.match(/\/calculator\/(.+)/);
  return match ? match[1] : null;
}

// Populate sidebar favorites widget if it exists
async function loadFavoritesList() {
  const container = document.getElementById("favorites-widget-content");
  if (!container) return;

  try {
    const response = await fetch(`${BASE_PATH}/api/favorites`);
    const data = await response.json();

    if (data.success && data.favorites.length > 0) {
      container.innerHTML = data.favorites
        .map(
          (f) => `
                <a href="${BASE_PATH}/calculator/${f.calculator_slug}" class="d-flex align-items-center mb-2 text-decoration-none p-2 rounded hover-bg-white-10">
                    <i class="bi bi-star-fill text-warning me-2 small"></i>
                    <span class="text-white small text-truncate">${f.calculator_name}</span>
                </a>
            `
        )
        .join("");
      document.getElementById("favorites-widget").style.display = "block";
    } else {
      container.innerHTML =
        '<div class="text-muted small text-center p-2">No favorites yet</div>';
    }
  } catch (e) {
    // Guest or error
    container.innerHTML =
      `<div class="text-muted small text-center p-2"><a href="${BASE_PATH}/login" class="text-primary">Login</a> to see favorites</div>`;
  }
}
