<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">System Tests</h4>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" id="runAllTests">
                        <i class="fas fa-play"></i> Run All Tests
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Test Category</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($available_tests as $key => $label): ?>
                                <?php if ($key === 'all') continue; ?>
                                <tr id="test-row-<?= $key ?>">
                                    <td><?= htmlspecialchars($label) ?></td>
                                    <td>
                                        <span class="text-muted test-description">Click "Run" to test this component</span>
                                        <div class="test-messages mt-1 small"></div>
                                    </td>
                                    <td><span class="badge badge-secondary">Not Run</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info run-test-btn" data-test="<?= $key ?>">
                                            <i class="fas fa-play"></i> Run
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
            if (!key) {
                return;
            }

            if (!aggregated[key]) {
                aggregated[key] = {
                    status: result.status,
                    messages: []
                };
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
        
        const badge = row.querySelector('.badge');
        const msgContainer = row.querySelector('.test-messages');
        const description = row.querySelector('.test-description');
        
        // Hide the initial description
        if (description) {
            description.style.display = 'none';
        }
        
        // Update badge
        badge.className = 'badge';
        if (status === 'pass') {
            badge.classList.add('badge-success');
            badge.textContent = 'Passed';
        } else if (status === 'fail') {
            badge.classList.add('badge-danger');
            badge.textContent = 'Failed';
        } else if (status === 'warning') {
            badge.classList.add('badge-warning');
            badge.textContent = 'Warning';
        } else {
            badge.classList.add('badge-secondary');
            badge.textContent = 'Pending';
        }
        
        // Update messages
        msgContainer.innerHTML = '';
        if (messages && messages.length > 0) {
            messages.forEach(msg => {
                const div = document.createElement('div');
                div.textContent = msg;
                if (status === 'fail') div.className = 'text-danger';
                else if (status === 'warning') div.className = 'text-warning';
                else div.className = 'text-success';
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
            
            // Set loading state
            this.disabled = true;
            btnIcon.className = 'fas fa-spinner fa-spin';
            
            fetch('/Bishwo_Calculator/admin/debug/run-tests', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `test_type=${testType}&csrf_token=${csrfToken}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    refreshTableFromResults(data.results);
                } else {
                    alert('Error running test: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error occurred');
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
        
        btn.disabled = true;
        icon.className = 'fas fa-spinner fa-spin';
        
        fetch('/Bishwo_Calculator/admin/debug/run-tests', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `test_type=all&csrf_token=${csrfToken}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                refreshTableFromResults(data.results);
            } else {
                alert('Error running tests: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error occurred');
        })
        .finally(() => {
            btn.disabled = false;
            icon.className = 'fas fa-play';
        });
    });
});
</script>
