<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Subscription Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage user subscriptions, plans, and billing</p>
        </div>
    </div>
</div>

<!-- Subscription Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-users" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Subscribers</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_subscribers'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Active</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +8% this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-dollar-sign" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Monthly Revenue</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo $stats['monthly_revenue'] ?? '$0.00'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">This Month</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-chart-line"></i> Growing</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-percentage" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Conversion Rate</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['conversion_rate'] ?? 0, 2); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Free to Paid</div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> Improving</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Churn Rate</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($stats['churn_rate'] ?? 0, 2); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Cancellation</div>
        <small style="color: <?php echo ($stats['churn_rate'] ?? 0) > 5 ? '#f87171' : '#10b981'; ?>; font-size: 0.75rem;">
            <i class="fas <?php echo ($stats['churn_rate'] ?? 0) > 5 ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
            <?php echo ($stats['churn_rate'] ?? 0) > 5 ? 'High' : 'Low'; ?> Rate
        </small>
    </div>
</div>

<!-- Subscription Plans -->
<div class="admin-card">
    <h2 class="admin-card-title">Subscription Plans</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-gem" style="color: #4cc9f0;"></i>
                Premium Plan
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="color: #f9fafb; font-weight: 600;"><?php echo $subscription_plans['premium']['price'] ?? '$19.99'; ?>/mo</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                    <?php echo number_format($subscription_plans['premium']['subscribers'] ?? 0); ?> subscribers
                </span>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0; color: #9ca3af; margin-bottom: 1rem;">
                <?php foreach (array_slice($subscription_plans['premium']['features'] ?? [], 0, 4) as $feature): ?>
                    <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-check-circle" style="color: #34d399;"></i>
                        <span><?php echo htmlspecialchars($feature); ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (count($subscription_plans['premium']['features'] ?? []) > 4): ?>
                    <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus" style="color: #fbbf24;"></i>
                        <span><?php echo count($subscription_plans['premium']['features'] ?? []) - 4; ?> more features</span>
                    </li>
                <?php endif; ?>
            </ul>
            <div style="display: flex; gap: 0.5rem;">
                <a href="<?php echo app_base_url('/admin/subscriptions/plans/premium/edit'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; justify-content: center;">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="<?php echo app_base_url('/admin/subscriptions/plans/premium/stats'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem; justify-content: center;">
                    <i class="fas fa-chart-bar"></i>
                    <span>Stats</span>
                </a>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user" style="color: #34d399;"></i>
                Standard Plan
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="color: #f9fafb; font-weight: 600;"><?php echo $subscription_plans['standard']['price'] ?? '$9.99'; ?>/mo</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                    <?php echo number_format($subscription_plans['standard']['subscribers'] ?? 0); ?> subscribers
                </span>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0; color: #9ca3af; margin-bottom: 1rem;">
                <?php foreach (array_slice($subscription_plans['standard']['features'] ?? [], 0, 4) as $feature): ?>
                    <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-check-circle" style="color: #34d399;"></i>
                        <span><?php echo htmlspecialchars($feature); ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (count($subscription_plans['standard']['features'] ?? []) > 4): ?>
                    <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus" style="color: #fbbf24;"></i>
                        <span><?php echo count($subscription_plans['standard']['features'] ?? []) - 4; ?> more features</span>
                    </li>
                <?php endif; ?>
            </ul>
            <div style="display: flex; gap: 0.5rem;">
                <a href="<?php echo app_base_url('/admin/subscriptions/plans/standard/edit'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; justify-content: center;">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="<?php echo app_base_url('/admin/subscriptions/plans/standard/stats'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem; justify-content: center;">
                    <i class="fas fa-chart-bar"></i>
                    <span>Stats</span>
                </a>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-gift" style="color: #fbbf24;"></i>
                Free Plan
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="color: #f9fafb; font-weight: 600;"><?php echo $subscription_plans['free']['price'] ?? '$0.00'; ?>/mo</span>
                <span style="color: #fbbf24; background: rgba(251, 191, 36, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                    <?php echo number_format($subscription_plans['free']['subscribers'] ?? 0); ?> subscribers
                </span>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0; color: #9ca3af; margin-bottom: 1rem;">
                <?php foreach (array_slice($subscription_plans['free']['features'] ?? [], 0, 4) as $feature): ?>
                    <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-check-circle" style="color: #34d399;"></i>
                        <span><?php echo htmlspecialchars($feature); ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (count($subscription_plans['free']['features'] ?? []) > 4): ?>
                    <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus" style="color: #fbbf24;"></i>
                        <span><?php echo count($subscription_plans['free']['features'] ?? []) - 4; ?> more features</span>
                    </li>
                <?php endif; ?>
            </ul>
            <div style="display: flex; gap: 0.5rem;">
                <a href="<?php echo app_base_url('/admin/subscriptions/plans/free/edit'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; justify-content: center;">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="<?php echo app_base_url('/admin/subscriptions/plans/free/stats'); ?>" 
                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem; justify-content: center;">
                    <i class="fas fa-chart-bar"></i>
                    <span>Stats</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Active Subscriptions -->
<div class="admin-card">
    <h2 class="admin-card-title">Active Subscriptions</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">User</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Plan</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Status</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Started</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Renewal</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Amount</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($active_subscriptions)): ?>
                        <?php foreach (array_slice($active_subscriptions, 0, 10) as $subscription): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 32px; height: 32px; background: rgba(76, 201, 240, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <span style="color: #4cc9f0; font-size: 0.875rem;"><?php echo strtoupper(substr($subscription['user'] ?? 'U', 0, 1)); ?></span>
                                        </div>
                                        <div>
                                            <span style="color: #f9fafb;"><?php echo htmlspecialchars($subscription['username'] ?? 'Unknown'); ?></span>
                                            <br>
                                            <small style="color: #9ca3af;"><?php echo htmlspecialchars($subscription['email'] ?? ''); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($subscription['plan_name'] ?? 'Free'); ?></td>
                                <td style="padding: 0.75rem;">
                                    <span style="color: <?php echo $subscription['status'] === 'active' ? '#34d399' : ($subscription['status'] === 'cancelled' ? '#f87171' : '#fbbf24'); ?>;
                                              background: <?php echo $subscription['status'] === 'active' ? 'rgba(52, 211, 153, 0.1)' : ($subscription['status'] === 'cancelled' ? 'rgba(248, 113, 113, 0.1)' : 'rgba(251, 191, 36, 0.1)'); ?>;
                                              padding: 0.25rem 0.5rem;
                                              border-radius: 4px;
                                              font-size: 0.75rem;">
                                        <?php echo ucfirst($subscription['status'] ?? 'active'); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo $subscription['start_date'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;"><?php echo $subscription['next_payment'] ?? 'Never'; ?></td>
                                <td style="padding: 0.75rem;"><?php echo $subscription['amount'] ?? '$0.00'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="<?php echo app_base_url('/admin/subscriptions/'.($subscription['id'] ?? 0).'/view'); ?>" 
                                           style="padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo app_base_url('/admin/subscriptions/'.($subscription['id'] ?? 0).'/cancel'); ?>" 
                                           style="padding: 0.25rem 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 4px; text-decoration: none; color: #f87171; font-size: 0.875rem;">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <a href="<?php echo app_base_url('/admin/subscriptions/'.($subscription['id'] ?? 0).'/invoice'); ?>" 
                                           style="padding: 0.25rem 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 1rem; color: #9ca3af;">No active subscriptions found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Subscription Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Subscription Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/subscriptions/plans/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-plus-circle"></i>
            <span>Create Plan</span>
        </a>

        <a href="<?php echo app_base_url('/admin/subscriptions/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-file-import"></i>
            <span>Import Subscriptions</span>
        </a>

        <a href="<?php echo app_base_url('/admin/subscriptions/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-export"></i>
            <span>Export Data</span>
        </a>

        <a href="<?php echo app_base_url('/admin/subscriptions/analytics'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-chart-line"></i>
            <span>Analytics</span>
        </a>

        <a href="<?php echo app_base_url('/admin/subscriptions/invoice-generator'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-file-invoice"></i>
            <span>Invoice Generator</span>
        </a>

        <a href="<?php echo app_base_url('/admin/subscriptions/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(236, 72, 153, 0.1); border: 1px solid rgba(236, 72, 153, 0.2); border-radius: 6px; text-decoration: none; color: #ec4899;">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
</div>

<!-- Subscription Trends -->
<div class="admin-card">
    <h2 class="admin-card-title">Subscription Trends</h2>
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem;">Revenue Over Time</h3>
            <div style="height: 300px; display: flex; align-items: end; justify-content: space-around; gap: 0.5rem; padding: 1rem;">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <?php $revenue = rand(5000, 15000); ?>
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                        <div style="background: #4cc9f0; height: <?php echo min(250, $revenue / 100); ?>px; width: 20px; margin-bottom: 0.5rem; border-radius: 2px 2px 0 0;"></div>
                        <div style="color: #9ca3af; font-size: 0.75rem;"><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem;">Plan Distribution</h3>
            <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
                <div style="display: grid; gap: 1rem; text-align: center; width: 100%;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #4cc9f0;">Premium</span>
                        <span style="color: #f9fafb;"><?php echo ($plan_distribution['premium'] ?? 0); ?>%</span>
                    </div>
                    <div style="height: 100px; background: rgba(76, 201, 240, 0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #4cc9f0; font-weight: 700; font-size: 1.5rem;">
                        <?php echo ($plan_distribution['premium'] ?? 0); ?>%
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #34d399;">Standard</span>
                        <span style="color: #f9fafb;"><?php echo ($plan_distribution['standard'] ?? 0); ?>%</span>
                    </div>
                    <div style="height: 100px; background: rgba(52, 211, 153, 0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #34d399; font-weight: 700; font-size: 1.5rem;">
                        <?php echo ($plan_distribution['standard'] ?? 0); ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Subscription Activity</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Event</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">User</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Plan</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Amount</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_activity)): ?>
                        <?php foreach (array_slice($recent_activity, 0, 10) as $activity): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-<?php echo $activity['icon'] ?? 'dollar-sign'; ?>" 
                                           style="color: <?php echo $activity['status'] === 'success' ? '#34d399' : '#f87171'; ?>;"></i>
                                        <span style="color: <?php echo $activity['status'] === 'success' ? '#f9fafb' : '#f87171'; ?>;"><?php echo htmlspecialchars($activity['action'] ?? 'Unknown Action'); ?></span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($activity['username'] ?? 'Unknown'); ?></td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($activity['plan'] ?? 'Free'); ?></td>
                                <td style="padding: 0.75rem;"><?php echo $activity['amount'] ?? '$0.00'; ?></td>
                                <td style="padding: 0.75rem;"><?php echo $activity['timestamp'] ?? 'Unknown'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1rem; color: #9ca3af;">No recent activity</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>