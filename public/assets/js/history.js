/**
 * History Page JavaScript Functionality
 * Handles history interactions, favorites, bulk actions, etc.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize history page functionality
    initializeHistoryPage();
});

function initializeHistoryPage() {
    // Load statistics
    loadStatistics();

    // Setup event listeners
    setupEventListeners();

    // Load recent calculations if available
    loadRecentCalculations();
}

function setupEventListeners() {
    // Favorite toggle buttons
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const calculationId = this.getAttribute('data-id');
            toggleFavorite(calculationId, this);
        });
    });

    // Item checkboxes for bulk actions
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();
        });
    });

    // Bulk action buttons
    setupBulkActions();

    // Search functionality
    setupSearch();

    // Load more results buttons
    setupLoadMore();
}

function toggleFavorite(calculationId, buttonElement) {
    const icon = buttonElement.querySelector('i');
    const isCurrentlyFavorite = icon.classList.contains('fas');

    // Show loading state
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(`/history/favorite/${calculationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update button appearance
                if (data.is_favorite) {
                    icon.className = 'fas fa-star text-warning';
                    buttonElement.title = 'Remove from favorites';
                    showNotification('Added to favorites!', 'success');
                } else {
                    icon.className = 'far fa-star text-muted';
                    buttonElement.title = 'Add to favorites';
                    showNotification('Removed from favorites!', 'info');
                }

                // Update history page statistics
                loadStatistics();
            } else {
                showNotification('Error updating favorite status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating favorite status', 'error');
        })
        .finally(() => {
            buttonElement.disabled = false;
        });
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    const bulkActionsDiv = document.querySelector('.bulk-actions');
    const selectedCountSpan = document.querySelector('.selected-count');

    if (checkboxes.length > 0) {
        bulkActionsDiv.classList.add('show');
        bulkActionsDiv.style.display = 'block';
        selectedCountSpan.textContent = `${checkboxes.length} item${checkboxes.length > 1 ? 's' : ''} selected`;
    } else {
        bulkActionsDiv.classList.remove('show');
        setTimeout(() => {
            bulkActionsDiv.style.display = 'none';
        }, 300);
    }
}

function setupBulkActions() {
    // Bulk delete
    const bulkDeleteBtn = document.getElementById('bulk-delete');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                showNotification('Please select items to delete', 'warning');
                return;
            }

            showConfirmModal('Delete Calculations', `Are you sure you want to delete ${selectedIds.length} calculation(s)?`, () => {
                performBulkDelete(selectedIds);
            });
        });
    }

    // Bulk add to favorites
    const bulkFavoriteBtn = document.getElementById('bulk-favorite');
    if (bulkFavoriteBtn) {
        bulkFavoriteBtn.addEventListener('click', function() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                showNotification('Please select items to add to favorites', 'warning');
                return;
            }

            performBulkFavorite(selectedIds, 'add');
        });
    }

    // Bulk remove from favorites
    const bulkRemoveFavoriteBtn = document.getElementById('bulk-remove-favorite');
    if (bulkRemoveFavoriteBtn) {
        bulkRemoveFavoriteBtn.addEventListener('click', function() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                showNotification('Please select items to remove from favorites', 'warning');
                return;
            }

            performBulkFavorite(selectedIds, 'remove');
        });
    }
}

function getSelectedIds() {
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.getAttribute('data-id'));
}

function performBulkDelete(ids) {
    fetch('/history/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Reload the page to reflect changes
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification('Error performing bulk delete', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error performing bulk delete', 'error');
        });
}

function performBulkFavorite(ids, action) {
    fetch('/history/bulk-favorite', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ids: ids, action: action })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Update the page to reflect changes
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification('Error performing bulk favorite operation', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error performing bulk favorite operation', 'error');
        });
}

function setupSearch() {
    const searchForm = document.querySelector('.search-box form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            // Let the form submit normally for now
            // Could be enhanced with AJAX search
        });
    }
}

function setupLoadMore() {
    const loadMoreButtons = document.querySelectorAll('.load-more-btn');
    loadMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const page = parseInt(this.getAttribute('data-page'));
            const calculatorType = this.getAttribute('data-calculator-type');
            loadMoreCalculations(page + 1, calculatorType, this);
        });
    });
}

function loadMoreCalculations(page, calculatorType, button) {
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';

    const url = calculatorType ?
        `/history/by-type/${calculatorType}?page=${page}` :
        `/history?page=${page}`;

    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Parse the new HTML and extract the history items
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newItems = doc.querySelectorAll('.history-item');

            if (newItems.length > 0) {
                const historyList = document.querySelector('.history-list');
                newItems.forEach(item => {
                    historyList.appendChild(item);
                });

                // Update load more button
                button.setAttribute('data-page', page);
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-plus"></i> Load More';
            } else {
                // No more items
                button.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading more calculations:', error);
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-plus"></i> Load More';
            showNotification('Error loading more calculations', 'error');
        });
}

function loadStatistics() {
    fetch('/history/stats')
        .then(response => response.json())
        .then(data => {
            if (data.total_calculations !== undefined) {
                updateStatisticsDisplay(data);
            }
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
}

function updateStatisticsDisplay(stats) {
    const elements = {
        'total-calculations': stats.total_calculations || 0,
        'favorite-calculations': stats.favorite_calculations || 0,
        'month-calculations': stats.month_calculations || 0,
        'top-calculator': stats.top_calculator || '-'
    };

    Object.entries(elements).forEach(([id, value]) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    });

    // Show statistics cards
    const statsCards = document.getElementById('stats-cards');
    if (statsCards && stats.total_calculations > 0) {
        statsCards.style.display = 'block';
    }
}

function loadRecentCalculations() {
    fetch('/history/recent?limit=5')
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                displayRecentCalculations(data);
            }
        })
        .catch(error => {
            console.error('Error loading recent calculations:', error);
        });
}

function displayRecentCalculations(calculations) {
    // This would display recent calculations in a sidebar or dashboard
    // Implementation depends on where this is called from
    console.log('Recent calculations:', calculations);
}

// Redundant showNotification removed - using global function
// If HistoryManager needs to export it, we can assign the global one
if (typeof window.showNotification === 'undefined') {
    // Fallback if global not loaded
    window.showNotification = function(message, type = 'info') {
        console.log(`[${type.toUpperCase()}] ${message}`);
    }
}

// Utility functions for export functionality
function exportHistory(format) {
    const url = `/history/export?format=${format}`;
    window.location.href = url;
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+F or Cmd+F to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.querySelector('.search-box input[type="text"]');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
});

// Initialize tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Call initialize tooltips when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeTooltips();
});

// Export for use in other scripts
window.HistoryManager = {
    toggleFavorite,
    exportHistory,
    showNotification: window.showNotification
};