<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Help Center</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Documentation, support, and help resources</p>
        </div>
    </div>
</div>

<!-- Help Overview -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-book" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Documentation</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($help_stats['docs_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Articles</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +5 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-question-circle" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">FAQs</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($help_stats['faq_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Frequently Asked</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Updated</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-comments" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Support Tickets</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($help_stats['open_tickets'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Open</div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-clock"></i> In Progress</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-bolt" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Quick Start</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($help_stats['tutorials_count'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Guides</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-play-circle"></i> New</small>
    </div>
</div>

<!-- Help Categories -->
<div class="admin-card">
    <h2 class="admin-card-title">Help Categories</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user" style="color: #4cc9f0;"></i>
                User Management
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Accounts, roles, permissions, and access control</p>
            <a href="<?php echo app_base_url('/help/users'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-arrow-right"></i>
                <span>Learn More</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calculator" style="color: #34d399;"></i>
                Calculator Features
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Available calculators, functions, and usage</p>
            <a href="<?php echo app_base_url('/help/calculators'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-arrow-right"></i>
                <span>Learn More</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-cog" style="color: #fbbf24;"></i>
                System Settings
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Configuration, environment, and system parameters</p>
            <a href="<?php echo app_base_url('/help/settings'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-arrow-right"></i>
                <span>Learn More</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-shield-alt" style="color: #22d3ee;"></i>
                Security
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Security best practices and guidelines</p>
            <a href="<?php echo app_base_url('/help/security'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <i class="fas fa-arrow-right"></i>
                <span>Learn More</span>
            </a>
        </div>
    </div>
</div>

<!-- Popular Articles -->
<div class="admin-card">
    <h2 class="admin-card-title">Popular Help Articles</h2>
    <div class="admin-card-content">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php if (!empty($popular_articles)): ?>
                <?php foreach ($popular_articles as $article): ?>
                    <li style="margin-bottom: 1rem; padding: 1rem; background: rgba(15, 23, 42, 0.5); border-radius: 6px; border-left: 3px solid #4cc9f0;">
                        <a href="<?php echo app_base_url('/help/article/'.($article['slug'] ?? '')); ?>" 
                           style="text-decoration: none; color: #f9fafb; display: block;">
                            <h4 style="margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-star" style="color: #fbbf24;"></i>
                                <?php echo htmlspecialchars($article['title'] ?? 'Untitled Article'); ?>
                            </h4>
                            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;"><?php echo htmlspecialchars(substr($article['summary'] ?? '', 0, 120)).(strlen($article['summary'] ?? '') > 120 ? '...' : ''); ?></p>
                            <div style="margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #9ca3af; font-size: 0.75rem;"><?php echo $article['category'] ?? 'General'; ?></span>
                                <span style="color: #9ca3af; font-size: 0.75rem;"><i class="fas fa-eye"></i> <?php echo number_format($article['views'] ?? 0); ?> views</span>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li style="text-align: center; padding: 2rem; color: #9ca3af;">
                    <i class="fas fa-book-open" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No popular articles available</p>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Support Options -->
<div class="admin-card">
    <h2 class="admin-card-title">Support Options</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-ticket-alt" style="color: #4cc9f0;"></i>
                Open Support Ticket
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Submit a support request for technical assistance</p>
            <a href="<?php echo app_base_url('/help/ticket'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-plus"></i>
                <span>Create Ticket</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-envelope" style="color: #34d399;"></i>
                Email Support
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Contact support directly via email</p>
            <a href="mailto:support@example.com"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-paper-plane"></i>
                <span>Send Email</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-comments" style="color: #fbbf24;"></i>
                Live Chat
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Get real-time support from our team</p>
            <a href="<?php echo app_base_url('/help/chat'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-comment-dots"></i>
                <span>Start Chat</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-video" style="color: #22d3ee;"></i>
                Video Tutorial
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Watch step-by-step video guides</p>
            <a href="<?php echo app_base_url('/help/videos'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <i class="fas fa-play-circle"></i>
                <span>Watch Videos</span>
            </a>
        </div>
    </div>
</div>

<!-- Help Management -->
<div class="admin-card">
    <h2 class="admin-card-title">Help Content Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/help/articles'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-book"></i>
            <span>Manage Articles</span>
        </a>

        <a href="<?php echo app_base_url('/admin/help/tickets'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-ticket-alt"></i>
            <span>Support Tickets</span>
        </a>

        <a href="<?php echo app_base_url('/admin/help/faq'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-question-circle"></i>
            <span>Manage FAQ</span>
        </a>

        <a href="<?php echo app_base_url('/admin/help/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-cog"></i>
            <span>Help Settings</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>