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
                                        <span class="text-muted">Waiting to run...</span>
                                        <div class="test-messages mt-1 small"></div>
                                    </td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
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
    
    // Helper to update test row UI
    function updateTestRow(testType, status, messages) {
        const row = document.getElementById('test-row-' + testType);
        if (!row) return;
        
        const badge = row.querySelector('.badge');
        const msgContainer = row.querySelector('.test-messages');
        
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
    
    // Run single test
    document.querySelectorAll('.run-test-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const testType = this.dataset.test;
            const btnIcon = this.querySelector('i');
            
            // Set loading state
            this.disabled = true;
            btnIcon.className = 'fas fa-spinner fa-spin';
            
            fetch('/admin/debug/run-tests', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `test_type=${testType}&csrf_token=${csrfToken}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // The backend returns results keyed by test name (e.g. "PHP Version"), 
                    // but we need to map back to our keys (e.g. "system").
                    // Since the controller runs all tests anyway when 'all' is passed, 
                    // or specific ones, let's handle the response structure.
                    // Actually, the controller's runSystemTests returns an array keyed by descriptive names.
                    // We might need to adjust the controller or the JS to match keys.
                    // For now, let's assume the controller returns the full results array.
                    
                    // Let's map descriptive names back to keys for UI update
                    const nameToKey = {
                        'PHP Version': 'system',
                        'Database Connection': 'database',
                        'File Permissions': 'files',
                        'Module System': 'modules',
                        'User Authentication': 'auth',
                        'GeoLocation Service': 'services', // Approximate mapping
                        'Installer Service': 'services',   // Approximate mapping
                        'Admin Panel': 'system'            // Approximate mapping
                    };
                    
                    // Since the controller returns ALL results even for single test request (based on current implementation),
                    // we can update all rows.
                    Object.entries(data.results).forEach(([name, result]) => {
                        // Find key by matching label in our table
                        // This is a bit hacky, better to have consistent keys.
                        // Let's try to match by text content of first cell
                        document.querySelectorAll('tbody tr').forEach(tr => {
                            if (tr.cells[0].textContent.trim() === name) {
                                const key = tr.id.replace('test-row-', '');
                                updateTestRow(key, result.status, result.messages);
                            }
                        });
                    });
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
        
        fetch('/admin/debug/run-tests', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `test_type=all&csrf_token=${csrfToken}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Object.entries(data.results).forEach(([name, result]) => {
                     document.querySelectorAll('tbody tr').forEach(tr => {
                        if (tr.cells[0].textContent.trim() === name) {
                            const key = tr.id.replace('test-row-', '');
                            updateTestRow(key, result.status, result.messages);
                        }
                    });
                });
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
