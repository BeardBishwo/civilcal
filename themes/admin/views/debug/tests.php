<?php
/**
 * System Tests View
 */
$page_title = 'System Tests';
$breadcrumbs = [
    ['title' => 'Debug', 'url' => app_base_url('/admin/debug')],
    ['title' => 'System Tests']
];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-check-circle"></i>
                    <h1>System Tests</h1>
                </div>
                <div class="header-subtitle">Run diagnostic checks on system components</div>
            </div>
            <div class="header-actions">
                <button type="button" class="btn btn-primary btn-compact" id="runAllTests">
                    <i class="fas fa-play"></i> <span>Run All Tests</span>
                </button>
            </div>
        </div>

        <!-- Tests Table -->
        <div class="settings-card">
            <div class="settings-card-body p-0">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Test Category</th>
                            <th style="width: 45%;">Description & Results</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($available_tests as $key => $label): ?>
                            <?php if ($key === 'all') continue; ?>
                            <tr id="test-row-<?= $key ?>" class="test-row">
                                <td style="font-weight: 600; color: var(--admin-gray-800);">
                                    <?= htmlspecialchars($label) ?>
                                </td>
                                <td>
                                    <span class="text-muted test-description small">Click "Run" to test this component</span>
                                    <div class="test-messages" style="margin-top: 5px; font-size: 0.9em;"></div>
                                </td>
                                <td>
                                    <span class="status-badge status-secondary">Not Run</span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="actions-compact" style="justify-content: flex-end;">
                                        <button class="btn btn-sm btn-outline-info run-test-btn" data-test="<?= $key ?>" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">
                                            <i class="fas fa-play"></i> Run
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?= $_SESSION['csrf_token'] ?? '' ?>';

    const testCategoryMap = {
        'PHP Version': 'system',
        'Admin Panel': 'system',
        'Database Connection': 'database',
        'Module System': 'modules',
        'User Authentication': 'auth',
        'GeoLocation Service': 'services',
        'Installer Service': 'services',
        'File Permissions': 'files'
    };

    const statusPriority = { pass: 1, warning: 2, fail: 3 };

    function aggregateResults(results) {
        const aggregated = {};

        Object.entries(results).forEach(([name, result]) => {
            const key = testCategoryMap[name];
            if (!key) return;

            if (!aggregated[key]) {
                aggregated[key] = { status: result.status, messages: [] };
            } else if (statusPriority[result.status] > statusPriority[aggregated[key].status]) {
                aggregated[key].status = result.status;
            }

            if (Array.isArray(result.messages) && result.messages.length) {
                result.messages.forEach(message => {
                    aggregated[key].messages.push(`${name}: ${message}`);
                });
            }
        });

        return aggregated;
    }
    
    // Helper to update test row UI
    function updateTestRow(testType, status, messages) {
        const row = document.getElementById('test-row-' + testType);
        if (!row) return;
        
        const badge = row.querySelector('.status-badge');
        const msgContainer = row.querySelector('.test-messages');
        const description = row.querySelector('.test-description');
        
        // Hide the initial description
        if (description) description.style.display = 'none';
        
        // Update badge
        badge.className = 'status-badge';
        badge.innerHTML = '';
        
        if (status === 'pass') {
            badge.classList.add('status-success');
            badge.innerHTML = '<i class="fas fa-check-circle"></i> Passed';
        } else if (status === 'fail') {
            badge.classList.add('status-danger');
            badge.innerHTML = '<i class="fas fa-times-circle"></i> Failed';
        } else if (status === 'warning') {
            badge.classList.add('status-warning');
            badge.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Warning';
        } else {
            badge.classList.add('status-secondary');
            badge.innerHTML = 'Pending';
        }
        
        // Update messages
        msgContainer.innerHTML = '';
        if (messages && messages.length > 0) {
            messages.forEach(msg => {
                const div = document.createElement('div');
                div.textContent = msg;
                div.style.marginBottom = '2px';
                if (status === 'fail') div.style.color = 'var(--admin-danger)';
                else if (status === 'warning') div.style.color = 'var(--admin-warning)';
                else div.style.color = 'var(--admin-success)';
                msgContainer.appendChild(div);
            });
        }
    }
    
    function refreshTableFromResults(rawResults) {
        const aggregated = aggregateResults(rawResults);
        Object.entries(aggregated).forEach(([key, data]) => {
            updateTestRow(key, data.status, data.messages);
        });
    }

    // Run single test
    document.querySelectorAll('.run-test-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const testType = this.dataset.test;
            const btnIcon = this.querySelector('i');
            
            this.disabled = true;
            btnIcon.className = 'fas fa-spinner fa-spin';
            
            fetch('<?= app_base_url('/admin/debug/run-tests') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `test_type=${testType}&csrf_token=${csrfToken}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    refreshTableFromResults(data.results);
                } else {
                    if(window.AdminApp) AdminApp.showNotification('Error running test', 'error');
                }
            })
            .catch(err => {
                if(window.AdminApp) AdminApp.showNotification('Network error occurred', 'error');
            })
            .finally(() => {
                this.disabled = false;
                btnIcon.className = 'fas fa-play';
            });
        });
    });
    
    // Run all tests
    document.getElementById('runAllTests').addEventListener('click', function() {
        const btn = this;
        const icon = btn.querySelector('i');
        const span = btn.querySelector('span');
        const originalText = span.textContent;
        
        btn.disabled = true;
        icon.className = 'fas fa-spinner fa-spin';
        span.textContent = 'Running...';
        
        fetch('<?= app_base_url('/admin/debug/run-tests') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `test_type=all&csrf_token=${csrfToken}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshTableFromResults(data.results);
                if(window.AdminApp) AdminApp.showNotification('All tests completed', 'success');
            } else {
                if(window.AdminApp) AdminApp.showNotification('Error running tests', 'error');
            }
        })
        .catch(err => {
            if(window.AdminApp) AdminApp.showNotification('Network error occurred', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            icon.className = 'fas fa-play';
            span.textContent = originalText;
        });
    });
});
</script>
