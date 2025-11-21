<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Performance Settings</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Configure caching, optimization, and performance tuning</p>
        </div>
    </div>
</div>

<!-- Settings Management Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/settings'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-cog"></i>
            <span>General</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/email'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-envelope"></i>
            <span>Email</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/security'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-shield-alt"></i>
            <span>Security</span>
        </a>
        <a href="<?php echo app_base_url('/admin/settings/performance'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-tachometer-alt"></i>
            <span>Performance</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">Performance Configuration</h2>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <form method="POST" action="<?php echo app_base_url('/admin/settings/performance/update'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-bolt" style="color: #4cc9f0;"></i>
                        Caching Configuration
                    </h3>
                    
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="color: #f9fafb;">Enable Full Page Cache</span>
                            <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                                <input type="checkbox" name="page_cache_enabled" <?php echo ($performance_config['page_cache_enabled'] ?? false) ? 'checked' : ''; ?> 
                                       style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($performance_config['page_cache_enabled'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                                <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                            </label>
                        </div>
                        <p style="color: #9ca3af; margin: 0; font-size: 0.75rem;">Cache entire web pages for faster loading</p>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="color: #f9fafb;">Enable Object Cache</span>
                            <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                                <input type="checkbox" name="object_cache_enabled" <?php echo ($performance_config['object_cache_enabled'] ?? false) ? 'checked' : ''; ?> 
                                       style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($performance_config['object_cache_enabled'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                                <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                            </label>
                        </div>
                        <p style="color: #9ca3af; margin: 0; font-size: 0.75rem;">Cache database queries and objects</p>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="color: #f9fafb;">Enable Asset Minification</span>
                            <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                                <input type="checkbox" name="asset_minification_enabled" <?php echo ($performance_config['asset_minification_enabled'] ?? false) ? 'checked' : ''; ?> 
                                       style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($performance_config['asset_minification_enabled'] ?? false) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                                <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                            </label>
                        </div>
                        <p style="color: #9ca3af; margin: 0; font-size: 0.75rem;">Minify CSS and JavaScript files</p>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Page Cache TTL (minutes)</label>
                        <input type="number" name="page_cache_ttl" value="<?php echo htmlspecialchars($performance_config['page_cache_ttl'] ?? 60); ?>" min="1" max="1440"
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                    
                    <div>
                        <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Object Cache TTL (minutes)</label>
                        <input type="number" name="object_cache_ttl" value="<?php echo htmlspecialchars($performance_config['object_cache_ttl'] ?? 30); ?>" min="1" max="1440"
                               style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
                    <button type="submit" 
                            style="padding: 0.75rem 2rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                        <i class="fas fa-save"></i>
                        <span>Save Performance Settings</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div>
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-chart-line" style="color: #34d399;"></i>
                    Performance Status
                </h3>
                
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Current Load:</span>
                        <span style="color: <?php echo ($performance_status['current_load'] ?? 0) > 80 ? '#f87171' : '#34d399'; ?>;"><?php echo $performance_status['current_load'] ?? 0; ?>%</span>
                    </div>
                    <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; width: <?php echo $performance_status['current_load'] ?? 0; ?>%; background: <?php echo ($performance_status['current_load'] ?? 0) > 80 ? '#f87171' : '#34d399'; ?>;"></div>
                    </div>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Database Queries:</span>
                        <span style="color: #f9fafb;"><?php echo $performance_status['db_queries'] ?? 0; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #9ca3af;">Page Load Time:</span>
                        <span style="color: #f9fafb;"><?php echo $performance_status['page_load_time'] ?? '0ms'; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #9ca3af;">Memory Usage:</span>
                        <span style="color: #f9fafb;"><?php echo $performance_status['memory_usage'] ?? '0MB'; ?></span>
                    </div>
                </div>
                
                <div style="display: flex; gap: 0.5rem;">
                    <a href="<?php echo app_base_url('/admin/settings/performance/clear-cache'); ?>" 
                       style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; text-decoration: none;">
                        <i class="fas fa-trash"></i>
                        <span>Clear Cache</span>
                    </a>
                    <a href="<?php echo app_base_url('/admin/settings/performance/run-diagnostics'); ?>" 
                       style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; text-decoration: none;">
                        <i class="fas fa-wrench"></i>
                        <span>Diagnostics</span>
                    </a>
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-compress" style="color: #fbbf24;"></i>
                    Compression Settings
                </h3>
                
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #f9fafb;">Gzip Compression</span>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="gzip_enabled" <?php echo ($performance_config['gzip_enabled'] ?? true) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($performance_config['gzip_enabled'] ?? true) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="color: #f9fafb;">Image Optimization</span>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="image_optimization" <?php echo ($performance_config['image_optimization'] ?? true) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($performance_config['image_optimization'] ?? true) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #f9fafb;">Browser Caching</span>
                        <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 24px;">
                            <input type="checkbox" name="browser_caching" <?php echo ($performance_config['browser_caching'] ?? true) ? 'checked' : ''; ?> 
                                   style="opacity: 0; width: 0; height: 0;">
                            <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo ($performance_config['browser_caching'] ?? true) ? '#34d399' : '#f87171'; ?>; transition: .4s; border-radius: 24px;"></span>
                            <span style="position: absolute; content: ''; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="admin-card">
    <h2 class="admin-card-title">Performance Metrics</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #4cc9f0;"></i>
                Page Load Times
            </h3>
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Average Load Time</span>
                <span style="color: #f9fafb;"><?php echo $metrics['avg_load_time'] ?? '0ms'; ?></span>
            </div>
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Best Case</span>
                <span style="color: #34d399;"><?php echo $metrics['best_load_time'] ?? '0ms'; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Worst Case</span>
                <span style="color: #f87171;"><?php echo $metrics['worst_load_time'] ?? '0ms'; ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-database" style="color: #34d399;"></i>
                Database Performance
            </h3>
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Queries per Second</span>
                <span style="color: #f9fafb;"><?php echo $metrics['queries_per_second'] ?? '0'; ?></span>
            </div>
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Avg Query Time</span>
                <span style="color: #f9fafb;"><?php echo $metrics['avg_query_time'] ?? '0ms'; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Slow Queries</span>
                <span style="color: <?php echo ($metrics['slow_queries'] ?? 0) > 0 ? '#f87171' : '#34d399'; ?>;"><?php echo $metrics['slow_queries'] ?? 0; ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-memory" style="color: #fbbf24;"></i>
                Memory Usage
            </h3>
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Current Usage</span>
                <span style="color: #f9fafb;"><?php echo $metrics['current_memory'] ?? '0MB'; ?></span>
            </div>
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Peak Usage</span>
                <span style="color: #f9fafb;"><?php echo $metrics['peak_memory'] ?? '0MB'; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Memory Limit</span>
                <span style="color: #f9fafb;"><?php echo $metrics['memory_limit'] ?? '0MB'; ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-hdd" style="color: #22d3ee;"></i>
                Cache Performance
            </h3>
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Cache Hits</span>
                <span style="color: #34d399;"><?php echo number_format($metrics['cache_hits'] ?? 0); ?></span>
            </div>
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Cache Misses</span>
                <span style="color: #f87171;"><?php echo number_format($metrics['cache_misses'] ?? 0); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Hit Ratio</span>
                <span style="color: <?php echo ($metrics['cache_hit_ratio'] ?? 0) > 80 ? '#34d399' : '#fbbf24'; ?>;"><?php echo number_format($metrics['cache_hit_ratio'] ?? 0, 2); ?>%</span>
            </div>
        </div>
    </div>
</div>

<!-- Performance Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Performance Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/settings/performance/clear-cache'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-trash-alt"></i>
            <span>Clear All Cache</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/performance/optimization'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-compress"></i>
            <span>Optimization</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/performance/diagnostics'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-wrench"></i>
            <span>Diagnostics</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/performance/profiling'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-chart-bar"></i>
            <span>Profiling</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/performance/monitoring'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-chart-line"></i>
            <span>Monitoring</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings/performance/reports'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-file-alt"></i>
            <span>Performance Reports</span>
        </a>
    </div>
</div>

<!-- Performance Best Practices -->
<div class="admin-card">
    <h2 class="admin-card-title">Performance Best Practices</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #4cc9f0;"></i>
                Caching Strategy
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Implement multiple layers of caching to improve response times and reduce server load.</p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-compress" style="color: #34d399;"></i>
                Asset Optimization
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Minify and bundle CSS and JS files, optimize images, and leverage compression.</p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-database" style="color: #fbbf24;"></i>
                Database Optimization
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Use proper indexing, query optimization, and connection pooling for better database performance.</p>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-server" style="color: #22d3ee;"></i>
                Server Configuration
            </h3>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Configure server-side caching, adjust memory limits, and optimize web server settings.</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>