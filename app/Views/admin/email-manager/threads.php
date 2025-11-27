<?php 
$this->layout('admin/layout', [
    'pageTitle' => 'Email Threads - Email Manager',
    'additional_css' => [asset_url('css/email-manager.css')]
]); 
?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1>
                <i class="fas fa-envelope"></i>
                Email Threads
            </h1>
            <p>Manage and respond to customer inquiries and email communications</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-primary" onclick="window.location.href='/admin/email-manager'">
                <i class="fas fa-chart-line"></i>
                Dashboard
            </button>
            <button class="btn btn-success" onclick="openNewThreadModal()">
                <i class="fas fa-plus"></i>
                New Thread
            </button>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="content-card">
    <div class="card-header">
        <h3><i class="fas fa-filter"></i> Filters & Search</h3>
    </div>
    <div class="card-body">
        <div class="filters-grid">
            <div class="filter-group">
                <label for="status-filter" class="filter-label">Status:</label>
                <select id="status-filter" class="form-control">
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="pending">Pending</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="priority-filter" class="filter-label">Priority:</label>
                <select id="priority-filter" class="form-control">
                    <option value="">All Priority</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="assignee-filter" class="filter-label">Assignee:</label>
                <select id="assignee-filter" class="form-control">
                    <option value="">All Assignees</option>
                    <option value="unassigned">Unassigned</option>
                </select>
            </div>

            <div class="filter-group filter-search">
                <label for="search-input" class="filter-label">Search:</label>
                <div class="search-input-group">
                    <input type="text" id="search-input" class="form-control" placeholder="Search threads...">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>
        </div>

        <div class="filter-actions">
            <button class="btn btn-secondary" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Clear Filters
            </button>
            <button class="btn btn-primary" onclick="applyFilters()">
                <i class="fas fa-search"></i>
                Apply Filters
            </button>
        </div>
    </div>
</div>

<!-- Threads List -->
<div class="content-card">
    <div class="card-header">
        <div class="header-content">
            <h3><i class="fas fa-list"></i> Email Threads</h3>
            <div class="header-stats">
                <span class="stat-badge">
                    <i class="fas fa-envelope"></i>
                    <span id="total-count">0</span> Total
                </span>
                <span class="stat-badge warning">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="unread-count">0</span> Unread
                </span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Loading State -->
        <div id="loading-state" class="loading-container">
            <div class="spinner"></div>
            <p>Loading threads...</p>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="empty-state" style="display: none;">
            <div class="empty-state-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h3>No Threads Found</h3>
            <p>No email threads match your current filters.</p>
            <button class="btn btn-primary" onclick="clearFilters()">
                <i class="fas fa-refresh"></i>
                Clear Filters
            </button>
        </div>

        <!-- Threads Container -->
        <div id="threads-container" class="threads-container">
            <!-- Threads will be loaded here via AJAX -->
        </div>

        <!-- Pagination -->
        <div id="pagination" class="pagination-container" style="display: none;">
            <div class="pagination-info">
                Showing <span id="showing-start">0</span> to <span id="showing-end">0</span> of <span id="total-threads">0</span> threads
            </div>
            <div class="pagination-controls">
                <button id="prev-page" class="btn btn-secondary btn-sm" onclick="changePage(-1)" disabled>
                    <i class="fas fa-chevron-left"></i>
                    Previous
                </button>
                <div id="page-numbers" class="page-numbers">
                    <!-- Page numbers will be generated here -->
                </div>
                <button id="next-page" class="btn btn-secondary btn-sm" onclick="changePage(1)" disabled>
                    Next
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- New Thread Modal -->
<div id="new-thread-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Create New Thread</h3>
            <button class="modal-close" onclick="closeNewThreadModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="new-thread-form">
                <div class="form-group">
                    <label for="thread-subject" class="form-label">Subject:</label>
                    <input type="text" id="thread-subject" name="subject" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="thread-email" class="form-label">Customer Email:</label>
                    <input type="email" id="thread-email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="thread-priority" class="form-label">Priority:</label>
                    <select id="thread-priority" name="priority" class="form-control">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="thread-message" class="form-label">Message:</label>
                    <textarea id="thread-message" name="message" rows="6" class="form-control" required></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeNewThreadModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Create Thread
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadThreads();
    setupEventListeners();
});

let currentPage = 1;
let currentFilters = {};
const threadsPerPage = 20;

function setupEventListeners() {
    // Filter change listeners
    document.getElementById('status-filter').addEventListener('change', debounceFilters);
    document.getElementById('priority-filter').addEventListener('change', debounceFilters);
    document.getElementById('assignee-filter').addEventListener('change', debounceFilters);

    // Search input
    document.getElementById('search-input').addEventListener('input', debounceFilters);

    // New thread form
    document.getElementById('new-thread-form').addEventListener('submit', handleNewThread);
}

function debounceFilters() {
    clearTimeout(window.filterTimeout);
    window.filterTimeout = setTimeout(applyFilters, 300);
}

function applyFilters() {
    currentFilters = {
        status: document.getElementById('status-filter').value,
        priority: document.getElementById('priority-filter').value,
        assignee: document.getElementById('assignee-filter').value,
        search: document.getElementById('search-input').value
    };
    currentPage = 1;
    loadThreads();
}

function clearFilters() {
    document.getElementById('status-filter').value = '';
    document.getElementById('priority-filter').value = '';
    document.getElementById('assignee-filter').value = '';
    document.getElementById('search-input').value = '';
    currentFilters = {};
    currentPage = 1;
    loadThreads();
}

function loadThreads() {
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');
    const container = document.getElementById('threads-container');

    loadingState.style.display = 'flex';
    emptyState.style.display = 'none';
    container.innerHTML = '';

    const params = new URLSearchParams({
        ...currentFilters,
        page: currentPage,
        limit: threadsPerPage
    });

    fetch(`/admin/email-manager/threads?${params}`)
        .then(response => response.json())
        .then(data => {
            renderThreads(data.threads || [], data.pagination || {});
            updateStats(data.stats || {});
        })
        .catch(error => {
            console.error('Error loading threads:', error);
            showError('Failed to load threads. Please try again.');
            renderThreads([], {});
        })
        .finally(() => {
            loadingState.style.display = 'none';
        });
}

function renderThreads(threads, pagination) {
    const container = document.getElementById('threads-container');
    const emptyState = document.getElementById('empty-state');
    const paginationDiv = document.getElementById('pagination');

    if (threads.length === 0) {
        emptyState.style.display = 'block';
        paginationDiv.style.display = 'none';
        return;
    }

    emptyState.style.display = 'none';

    container.innerHTML = threads.map(thread => `
        <div class="thread-item ${thread.is_unread ? 'unread' : ''} priority-${thread.priority}"
             onclick="viewThread(${thread.id})">
            <div class="thread-header">
                <div class="thread-main-info">
                    <div class="thread-subject">${escapeHtml(thread.subject)}</div>
                    <div class="thread-meta">
                        <span class="thread-priority priority-${thread.priority}">
                            <i class="fas fa-flag"></i>
                            ${thread.priority.toUpperCase()}
                        </span>
                        <span class="thread-status status-${thread.status}">
                            <i class="fas fa-circle"></i>
                            ${thread.status.toUpperCase()}
                        </span>
                        <span class="thread-assignee">
                            <i class="fas fa-user"></i>
                            ${thread.assignee ? escapeHtml(thread.assignee) : 'Unassigned'}
                        </span>
                        <span class="thread-date">
                            <i class="fas fa-clock"></i>
                            ${formatDate(thread.created_at)}
                        </span>
                    </div>
                </div>
                <div class="thread-actions">
                    ${thread.is_unread ? '<span class="unread-badge"><i class="fas fa-circle"></i></span>' : ''}
                </div>
            </div>
            <div class="thread-preview">
                ${escapeHtml(thread.preview || 'No preview available...')}
            </div>
            <div class="thread-footer">
                <div class="thread-tags">
                    ${thread.tags ? thread.tags.map(tag =>
                        `<span class="thread-tag">${escapeHtml(tag)}</span>`
                    ).join('') : ''}
                </div>
                <div class="thread-quick-actions">
                    <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); replyToThread(${thread.id})">
                        <i class="fas fa-reply"></i>
                        Reply
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); viewThread(${thread.id})">
                        <i class="fas fa-eye"></i>
                        View
                    </button>
                </div>
            </div>
        </div>
    `).join('');

    renderPagination(pagination);
}

function renderPagination(pagination) {
    const paginationDiv = document.getElementById('pagination');

    if (!pagination.total || pagination.total <= threadsPerPage) {
        paginationDiv.style.display = 'none';
        return;
    }

    paginationDiv.style.display = 'block';

    // Update pagination info
    document.getElementById('showing-start').textContent = pagination.from || 0;
    document.getElementById('showing-end').textContent = pagination.to || 0;
    document.getElementById('total-threads').textContent = pagination.total || 0;

    // Update page buttons
    document.getElementById('prev-page').disabled = pagination.current_page === 1;
    document.getElementById('next-page').disabled = pagination.current_page === pagination.last_page;

    // Generate page numbers
    const pageNumbers = document.getElementById('page-numbers');
    pageNumbers.innerHTML = '';

    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, startPage + 4);

    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.className = `btn btn-sm ${i === pagination.current_page ? 'btn-primary' : 'btn-secondary'}`;
        pageBtn.textContent = i;
        pageBtn.onclick = () => goToPage(i);
        pageNumbers.appendChild(pageBtn);
    }
}

function updateStats(stats) {
    document.getElementById('total-count').textContent = stats.total || 0;
    document.getElementById('unread-count').textContent = stats.unread || 0;
}

function changePage(direction) {
    const newPage = currentPage + direction;
    if (newPage >= 1) {
        goToPage(newPage);
    }
}

function goToPage(page) {
    currentPage = page;
    loadThreads();
}

function viewThread(threadId) {
    window.location.href = `/admin/email-manager/thread/${threadId}`;
}

function replyToThread(threadId) {
    window.location.href = `/admin/email-manager/thread/${threadId}#reply`;
}

function openNewThreadModal() {
    document.getElementById('new-thread-modal').style.display = 'block';
}

function closeNewThreadModal() {
    document.getElementById('new-thread-modal').style.display = 'none';
    document.getElementById('new-thread-form').reset();
}

function handleNewThread(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);

    fetch('/admin/email-manager/threads', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('Thread created successfully!', 'success');
            closeNewThreadModal();
            loadThreads();
        } else {
            showError(result.error || 'Failed to create thread');
        }
    })
    .catch(error => {
        console.error('Error creating thread:', error);
        showError('Failed to create thread. Please try again.');
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) {
        return 'Today';
    } else if (diffDays === 1) {
        return 'Yesterday';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else {
        return date.toLocaleDateString();
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    showNotification(message, 'error');
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
        <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
    `;

    document.querySelector('.admin-content').insertBefore(notification, document.querySelector('.admin-content').firstChild);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>