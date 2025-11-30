<?php 
$this->layout('admin/layout', [
    'pageTitle' => 'Email Manager Dashboard',
    'additional_css' => [asset_url('css/email-manager.css')]
]); 
?>

<style>
    .email-dashboard {
        padding: 2rem;
    }

    .dashboard-header {
        margin-bottom: 2rem;
    }

    .dashboard-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--admin-text-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .dashboard-header p {
        color: var(--admin-text-muted);
        font-size: 0.95rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--admin-border);
        border-radius: 12px;
        padding: 1.5rem;
        transition: var(--transition-normal);
    }

    .stat-card:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: var(--admin-primary);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--admin-primary-light);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: var(--admin-text-secondary);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .stat-change {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
    }

    .stat-change.positive {
        color: var(--admin-success);
    }

    .stat-change.negative {
        color: var(--admin-warning);
    }

    .filter-bar {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--admin-border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .filter-label {
        color: var(--admin-text-muted);
        font-size: 0.85rem;
        font-weight: 600;
    }

    .filter-select,
    .search-box input {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--admin-border);
        color: var(--admin-text-primary);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: var(--transition-fast);
    }

    .filter-select:hover,
    .search-box input:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--admin-primary);
    }

    .filter-select:focus,
    .search-box input:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--admin-primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 200px;
    }

    .search-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--admin-text-muted);
        pointer-events: none;
    }

    .btn-primary-small {
        background: var(--admin-primary);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        transition: var(--transition-fast);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary-small:hover {
        background: var(--admin-primary-light);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }

    .threads-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .thread-item {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--admin-border);
        border-radius: 12px;
        padding: 1.5rem;
        cursor: pointer;
        transition: var(--transition-normal);
    }

    .thread-item:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: var(--admin-primary);
        transform: translateY(-2px);
    }

    .thread-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .thread-subject {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--admin-text-primary);
        margin-bottom: 0.5rem;
    }

    .thread-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        font-size: 0.85rem;
    }

    .thread-priority,
    .thread-status,
    .thread-assignee,
    .thread-date {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--admin-text-secondary);
    }

    .thread-priority.priority-high {
        background: rgba(239, 68, 68, 0.1);
        color: #fca5a5;
    }

    .thread-priority.priority-medium {
        background: rgba(245, 158, 11, 0.1);
        color: #fcd34d;
    }

    .thread-priority.priority-low {
        background: rgba(16, 185, 129, 0.1);
        color: #86efac;
    }

    .thread-status.status-open {
        background: rgba(6, 182, 212, 0.1);
        color: #67e8f9;
    }

    .thread-status.status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #fcd34d;
    }

    .thread-status.status-resolved {
        background: rgba(16, 185, 129, 0.1);
        color: #86efac;
    }

    .thread-preview {
        color: var(--admin-text-muted);
        font-size: 0.9rem;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .thread-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .thread-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .thread-tag {
        background: rgba(67, 97, 238, 0.2);
        color: var(--admin-primary-light);
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
    }

    .thread-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-small {
        background: var(--admin-primary);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.8rem;
        transition: var(--transition-fast);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-small:hover {
        background: var(--admin-primary-light);
    }

    .loading-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        gap: 1rem;
    }

    .spinner-lg {
        width: 40px;
        height: 40px;
        border: 4px solid var(--admin-border);
        border-top-color: var(--admin-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--admin-text-muted);
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .empty-state-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--admin-text-secondary);
        margin-bottom: 0.5rem;
    }

    .empty-state-description {
        font-size: 0.9rem;
    }
</style>

<div class="email-dashboard">
    <div class="email-manager-container">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-envelope"></i>
                Email Manager
            </h1>
            <p class="dashboard-subtitle">Manage customer inquiries and email communications</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="total-threads">0</div>
                <div class="stat-label">Total Threads</div>
                <div class="stat-change positive" id="threads-change">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12% from last month</span>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-number" id="unread-threads">0</div>
                <div class="stat-label">Unread Threads</div>
                <div class="stat-change negative" id="unread-change">
                    <i class="fas fa-arrow-up"></i>
                    <span>+3 from yesterday</span>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-number" id="resolved-threads">0</div>
                <div class="stat-label">Resolved This Month</div>
                <div class="stat-change positive" id="resolved-change">
                    <i class="fas fa-arrow-up"></i>
                    <span>+18% improvement</span>
                </div>
            </div>

            <div class="stat-card danger">
                <div class="stat-number" id="high-priority">0</div>
                <div class="stat-label">High Priority</div>
                <div class="stat-change negative" id="priority-change">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Needs attention</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label">Status:</label>
                <select class="filter-select" id="status-filter">
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="pending">Pending</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Priority:</label>
                <select class="filter-select" id="priority-filter">
                    <option value="">All Priority</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Assignee:</label>
                <select class="filter-select" id="assignee-filter">
                    <option value="">All Assignees</option>
                    <option value="unassigned">Unassigned</option>
                </select>
            </div>

            <div class="search-box">
                <input type="text" placeholder="Search threads..." id="search-input">
                <i class="fas fa-search search-icon"></i>
            </div>

            <div class="filter-group">
                <button class="btn-primary-small" id="new-thread-btn">
                    <i class="fas fa-plus"></i>
                    New Thread
                </button>
            </div>
        </div>

        <!-- Thread List -->
        <div class="threads-container" id="threads-container">
            <div class="loading-container" id="loading">
                <div class="spinner-lg"></div>
                <span>Loading threads...</span>
            </div>
        </div>
    </div>

    <!-- Template Management Modal -->
    <div class="template-modal" id="template-modal">
        <div class="template-modal-content">
            <div class="template-modal-header">
                <h3>Email Templates</h3>
                <button class="template-close" id="template-close">&times;</button>
            </div>
            <div class="template-modal-body">
                <div class="template-actions">
                    <button class="btn-primary-small" id="new-template-btn">
                        <i class="fas fa-plus"></i>
                        New Template
                    </button>
                </div>
                <div class="template-grid" id="template-grid">
                    <!-- Templates will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Email Manager JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial data
            loadDashboardData();

            // Event listeners
            setupEventListeners();
        });

        function loadDashboardData() {
            // Load stats
            fetch('/admin/email-manager/stats')
                .then(response => response.json())
                .then(data => {
                    updateStats(data);
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                    // Show demo data
                    showDemoData();
                });

            // Load threads
            loadThreads();
        }

        function loadThreads(filters = {}) {
            const loading = document.getElementById('loading');
            const container = document.getElementById('threads-container');

            loading.style.display = 'flex';

            const params = new URLSearchParams(filters);
            fetch(`/admin/email-manager/threads?${params}`)
                .then(response => response.json())
                .then(data => {
                    renderThreads(data.threads || []);
                })
                .catch(error => {
                    console.error('Error loading threads:', error);
                    renderThreads(getDemoThreads());
                })
                .finally(() => {
                    loading.style.display = 'none';
                });
        }

        function renderThreads(threads) {
            const container = document.getElementById('threads-container');

            if (threads.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">📧</div>
                        <div class="empty-state-title">No threads found</div>
                        <div class="empty-state-description">
                            No email threads match your current filters.
                        </div>
                    </div>
                `;
                return;
            }

            container.innerHTML = threads.map(thread => `
                <div class="thread-item ${thread.is_unread ? 'unread' : ''} priority-${thread.priority}" 
                     onclick="viewThread(${thread.id})">
                    <div class="thread-header">
                        <div class="thread-main-info">
                            <div class="thread-subject">${thread.subject}</div>
                            <div class="thread-meta">
                                <span class="thread-priority priority-${thread.priority}">
                                    ${thread.priority.toUpperCase()}
                                </span>
                                <span class="thread-status status-${thread.status}">
                                    ${thread.status.toUpperCase()}
                                </span>
                                <span class="thread-assignee">
                                    ${thread.assignee ? thread.assignee : 'Unassigned'}
                                </span>
                                <span class="thread-date">${formatDate(thread.created_at)}</span>
                            </div>
                        </div>
                    </div>
                    <div class="thread-preview">
                        ${thread.preview || 'No preview available...'}
                    </div>
                    <div class="thread-footer">
                        <div class="thread-tags">
                            ${thread.tags ? thread.tags.map(tag => 
                                `<span class="thread-tag">${tag}</span>`
                            ).join('') : ''}
                        </div>
                        <div class="thread-actions">
                            <button class="btn-small btn-primary-small" onclick="event.stopPropagation(); replyToThread(${thread.id})">
                                <i class="fas fa-reply"></i>
                                Reply
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function updateStats(data) {
            // Update stat numbers with animation
            animateNumber('total-threads', data.total_threads || 0);
            animateNumber('unread-threads', data.unread_threads || 0);
            animateNumber('resolved-threads', data.resolved_threads || 0);
            animateNumber('high-priority', data.high_priority || 0);
        }

        function animateNumber(elementId, targetValue) {
            const element = document.getElementById(elementId);
            const startValue = parseInt(element.textContent) || 0;
            const increment = targetValue > startValue ? 1 : -1;
            const duration = 1000; // 1 second
            const stepTime = 50;
            const steps = duration / stepTime;
            const valueIncrement = (targetValue - startValue) / steps;

            let current = startValue;
            const timer = setInterval(() => {
                current += valueIncrement;
                element.textContent = Math.round(current);

                if ((increment > 0 && current >= targetValue) ||
                    (increment < 0 && current <= targetValue)) {
                    element.textContent = targetValue;
                    clearInterval(timer);
                }
            }, stepTime);
        }

        function setupEventListeners() {
            // Filter change listeners
            document.getElementById('status-filter').addEventListener('change', applyFilters);
            document.getElementById('priority-filter').addEventListener('change', applyFilters);
            document.getElementById('assignee-filter').addEventListener('change', applyFilters);

            // Search input
            let searchTimeout;
            document.getElementById('search-input').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 300);
            });

            // Template modal
            document.getElementById('template-close').addEventListener('click', closeTemplateModal);
        }

        function applyFilters() {
            const filters = {
                status: document.getElementById('status-filter').value,
                priority: document.getElementById('priority-filter').value,
                assignee: document.getElementById('assignee-filter').value,
                search: document.getElementById('search-input').value
            };

            loadThreads(filters);
        }

        function viewThread(threadId) {
            window.location.href = `/admin/email-manager/thread/${threadId}`;
        }

        function replyToThread(threadId) {
            // This would typically open a modal or navigate to a reply form
            window.location.href = `/admin/email-manager/thread/${threadId}#reply`;
        }

        function openTemplateModal() {
            document.getElementById('template-modal').style.display = 'block';
            loadTemplates();
        }

        function closeTemplateModal() {
            document.getElementById('template-modal').style.display = 'none';
        }

        function loadTemplates() {
            fetch('/admin/email-manager/templates')
                .then(response => response.json())
                .then(data => {
                    renderTemplates(data.templates || []);
                })
                .catch(error => {
                    console.error('Error loading templates:', error);
                    renderTemplates(getDemoTemplates());
                });
        }

        function renderTemplates(templates) {
            const container = document.getElementById('template-grid');

            if (templates.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">📝</div>
                        <div class="empty-state-title">No templates found</div>
                        <div class="empty-state-description">
                            Create your first email template to speed up responses.
                        </div>
                    </div>
                `;
                return;
            }

            container.innerHTML = templates.map(template => `
                <div class="template-card" onclick="useTemplate(${template.id})">
                    <div class="template-header">
                        <div>
                            <div class="template-title">${template.name}</div>
                            <div class="template-category">${template.category}</div>
                        </div>
                    </div>
                    <div class="template-preview">
                        ${template.preview}
                    </div>
                    <div class="template-variables">
                        Variables: ${template.variables ? template.variables.join(', ') : 'None'}
                    </div>
                    <div class="template-actions">
                        <button class="btn-small btn-primary-small" onclick="event.stopPropagation(); useTemplate(${template.id})">
                            <i class="fas fa-check"></i>
                            Use Template
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function useTemplate(templateId) {
            // This would typically populate a reply form with the template
            fetch(`/admin/email-manager/templates/${templateId}/use`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Apply template to current reply form
                    closeTemplateModal();
                    showNotification('Template applied successfully', 'success');
                })
                .catch(error => {
                    console.error('Error using template:', error);
                    showNotification('Error applying template', 'error');
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

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type}`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Demo data functions (for development/testing)
        function showDemoData() {
            updateStats({
                total_threads: 45,
                unread_threads: 8,
                resolved_threads: 32,
                high_priority: 3
            });
        }

        function getDemoThreads() {
            return [{
                    id: 1,
                    subject: 'Question about concrete mix calculation',
                    status: 'open',
                    priority: 'high',
                    assignee: 'Admin User',
                    created_at: new Date().toISOString(),
                    preview: 'Hi, I need help understanding the concrete strength calculations...',
                    is_unread: true,
                    tags: ['concrete', 'calculation']
                },
                {
                    id: 2,
                    subject: 'Feature request: Add steel beam calculations',
                    status: 'pending',
                    priority: 'medium',
                    assignee: null,
                    created_at: new Date(Date.now() - 86400000).toISOString(),
                    preview: 'It would be great to have beam load capacity calculations...',
                    is_unread: false,
                    tags: ['feature-request', 'structural']
                }
            ];
        }

        function getDemoTemplates() {
            return [{
                    id: 1,
                    name: 'Welcome Message',
                    category: 'General',
                    preview: 'Welcome to Bishwo Calculator! We\'re here to help...',
                    variables: ['{{user_name}}', '{{calculation_type}}']
                },
                {
                    id: 2,
                    name: 'Calculation Help',
                    category: 'Support',
                    preview: 'Thank you for your question about calculations...',
                    variables: ['{{user_name}}', '{{specific_calculator}}', '{{calculation_result}}']
                }
            ];
        }
    </script>
</div>