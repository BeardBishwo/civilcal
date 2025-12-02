<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Debug Tests</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Run system diagnostic tests to verify functionality</p>
        </div>
    </div>
</div>

<!-- Test Categories -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-vial" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Tests</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($test_stats['total_tests'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Available</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Comprehensive Suite</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Passed Tests</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($test_stats['passed_tests'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Successful</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +5% improvement</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #f87171; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Failed Tests</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #f87171; margin-bottom: 0.5rem;"><?php echo number_format($test_stats['failed_tests'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Issues Found</div>
        <small style="color: #f87171; font-size: 0.75rem;"><i class="fas fa-bug"></i> Requires Attention</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-percentage" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Success Rate</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($test_stats['success_rate'] ?? 0, 2); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Overall</div>
        <small style="color: <?php echo ($test_stats['success_rate'] ?? 0) >= 90 ? '#10b981' : '#f87171'; ?>; font-size: 0.75rem;">
            <i class="fas <?php echo ($test_stats['success_rate'] ?? 0) >= 90 ? 'fa-smile' : 'fa-frown'; ?>"></i>
            <?php echo ($test_stats['success_rate'] ?? 0) >= 90 ? 'Excellent' : 'Needs Improvement'; ?>
        </small>
    </div>
</div>

<!-- Test Categories -->
<div class="admin-card">
    <h2 class="admin-card-title">Test Categories</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-server" style="color: #4cc9f0;"></i>
                System Tests
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">System components and configuration</p>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: #9ca3af;">Tests:</span>
                    <span style="color: #f9fafb;"><?php echo $categories['system']['total'] ?? 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: #9ca3af;">Passed:</span>
                    <span style="color: #34d399;"><?php echo $categories['system']['passed'] ?? 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #9ca3af;">Failed:</span>
                    <span style="color: #f87171;"><?php echo $categories['system']['failed'] ?? 0; ?></span>
                </div>
            </div>
            <a href="<?php echo app_base_url('/admin/debug/tests/system'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-play"></i>
                <span>Run Tests</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-database" style="color: #34d399;"></i>
                Database Tests
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Database connectivity and operations</p>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: #9ca3af;">Tests:</span>
                    <span style="color: #f9fafb;"><?php echo $categories['database']['total'] ?? 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: #9ca3af;">Passed:</span>
                    <span style="color: #34d399;"><?php echo $categories['database']['passed'] ?? 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #9ca3af;">Failed:</span>
                    <span style="color: #f87171;"><?php echo $categories['database']['failed'] ?? 0; ?></span>
                </div>
            </div>
            <a href="<?php echo app_base_url('/admin/debug/tests/database'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-play"></i>
                <span>Run Tests</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calculator" style="color: #fbbf24;"></i>
                Calculator Tests
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Calculator functionality and accuracy</p>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: #9ca3af;">Tests:</span>
                    <span style="color: #f9fafb;"><?php echo $categories['calculator']['total'] ?? 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: #9ca3af;">Passed:</span>
                    <span style="color: #34d399;"><?php echo $categories['calculator']['passed'] ?? 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #9ca3af;">Failed:</span>
                    <span style="color: #f87171;"><?php echo $categories['calculator']['failed'] ?? 0; ?></span>
                </div>
            </div>
            <a href="<?php echo app_base_url('/admin/debug/tests/calculator'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-play"></i>
                <span>Run Tests</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user" style="color: #22d3ee;"></i>
                User Tests
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Authentication and user management</p>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: #9ca3af;">Tests:</span>
                    <span style="color: #f9fafb;"><?php echo $categories['user']['total'] ?? 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: #9ca3af;">Passed:</span>
                    <span style="color: #34d399;"><?php echo $categories['user']['passed'] ?? 0; ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #9ca3af;">Failed:</span>
                    <span style="color: #f87171;"><?php echo $categories['user']['failed'] ?? 0; ?></span>
                </div>
            </div>
            <a href="<?php echo app_base_url('/admin/debug/tests/user'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <i class="fas fa-play"></i>
                <span>Run Tests</span>
            </a>
        </div>
    </div>
</div>

<!-- Recent Test Results -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Test Results</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Test Name</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Category</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Status</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Duration</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Date</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_tests)): ?>
                        <?php foreach (array_slice($recent_tests, 0, 12) as $test): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($test['name'] ?? 'Unknown Test'); ?></td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($test['category'] ?? 'General'); ?></td>
                                <td style="padding: 0.75rem;">
                                    <span class="status-<?php echo $test['status'] === 'passed' ? 'success' : ($test['status'] === 'failed' ? 'error' : 'warning'); ?>" 
                                          style="background: rgba(<?php echo $test['status'] === 'passed' ? '52, 211, 153, 0.1' : ($test['status'] === 'failed' ? '248, 113, 113, 0.1' : '251, 191, 36, 0.1'); ?>); 
                                                 border: 1px solid rgba(<?php echo $test['status'] === 'passed' ? '52, 211, 153, 0.3' : ($test['status'] === 'failed' ? '248, 113, 113, 0.3' : '251, 191, 36, 0.3'); ?>); 
                                                 padding: 0.25rem 0.5rem; 
                                                 border-radius: 4px; 
                                                 font-size: 0.75rem;">
                                        <?php echo ucfirst($test['status'] ?? 'unknown'); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo $test['duration'] ?? '0ms'; ?></td>
                                <td style="padding: 0.75rem;"><?php echo $test['date'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/debug/tests/'.($test['id'] ?? 0).'/view'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-eye"></i>
                                        <span>Details</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/debug/tests/'.($test['id'] ?? 0).'/rerun'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                                        <i class="fas fa-redo"></i>
                                        <span>Rerun</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 1rem; color: #9ca3af;">No test results available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Run Tests -->
<div class="admin-card">
    <h2 class="admin-card-title">Run Tests</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-vial" style="color: #4cc9f0;"></i>
                Quick Diagnostics
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Run a quick diagnostic test to check system health</p>
            <a href="<?php echo app_base_url('/admin/debug/tests/quick'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                <i class="fas fa-bolt"></i>
                <span>Run Quick Test</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-flask" style="color: #34d399;"></i>
                Full Diagnostic
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Comprehensive test suite covering all system components</p>
            <a href="<?php echo app_base_url('/admin/debug/tests/full-diagnostic'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
                <i class="fas fa-certificate"></i>
                <span>Run Full Diagnostic</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-cogs" style="color: #fbbf24;"></i>
                Custom Test
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Run a custom test with selected components</p>
            <a href="<?php echo app_base_url('/admin/debug/tests/custom'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
                <i class="fas fa-sliders-h"></i>
                <span>Custom Test</span>
            </a>
        </div>
    </div>
</div>

<!-- Test Configuration -->
<div class="admin-card">
    <h2 class="admin-card-title">Test Configuration</h2>
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
        <div>
            <form method="POST" action="<?php echo app_base_url('/admin/debug/tests/configure'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-cog" style="color: #4cc9f0;"></i>
                        Test Settings
                    </h3>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Test Timeout (seconds)</label>
                        <input type="number" name="test_timeout" value="<?php echo htmlspecialchars($test_config['timeout'] ?? 30); ?>" min="1" max="300"
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Maximum Concurrent Tests</label>
                        <input type="number" name="max_concurrent" value="<?php echo htmlspecialchars($test_config['max_concurrent'] ?? 5); ?>" min="1" max="20"
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #f9fafb;">Run Tests Automatically</span>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="auto_run" <?php echo ($test_config['auto_run'] ?? false) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($test_config['auto_run'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #f9fafb;">Include Performance Tests</span>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="include_performance" <?php echo ($test_config['include_performance'] ?? true) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($test_config['include_performance'] ?? true) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                    
                    <button type="submit" 
                            style="margin-top: 1.5rem; padding: 0.75rem 2rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                        <i class="fas fa-save"></i>
                        <span>Save Configuration</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div>
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-history" style="color: #22d3ee;"></i>
                    Test History
                </h3>
                
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Last Full Test:</span>
                        <span style="color: #f9fafb;"><?php echo $history['last_full_test'] ?? 'Never'; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Total Test Runs:</span>
                        <span style="color: #f9fafb;"><?php echo number_format($history['total_runs'] ?? 0); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Success Rate (Last 10):</span>
                        <span style="color: <?php echo ($history['recent_success_rate'] ?? 0) >= 90 ? '#34d399' : ($history['recent_success_rate'] ?? 0) >= 70 ? '#fbbf24' : '#f87171'; ?>;"><?php echo number_format($history['recent_success_rate'] ?? 0); ?>%</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #9ca3af;">Avg. Duration:</span>
                        <span style="color: #f9fafb;"><?php echo $history['average_duration'] ?? '0s'; ?></span>
                    </div>
                </div>
                
                <a href="<?php echo app_base_url('/admin/debug/tests/history'); ?>" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none;">
                    <i class="fas fa-chart-bar"></i>
                    <span>View Full History</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Test Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Test Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/debug/tests/schedule'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-calendar-alt"></i>
            <span>Schedule Tests</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/tests/templates'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-clipboard-list"></i>
            <span>Test Templates</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/tests/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-import"></i>
            <span>Import Tests</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/tests/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-export"></i>
            <span>Export Tests</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/tests/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-cog"></i>
            <span>Test Settings</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>