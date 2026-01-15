<?php

/**
 * SPONSORSHIPS MANAGEMENT INTERFACE
 * Styled to match the premium User Management interface.
 */

// Extract data for use in template
$page_title = $page_title ?? 'Sponsorship Management - Admin Panel';
$sponsors = $sponsors ?? [];

// Calculate stats for sponsorships
$totalSponsors = count($sponsors);
$activeSponsors = count(array_filter($sponsors, fn($s) => ($s['status'] ?? 'active') === 'active'));
// For campaigns, we'd ideally have a count, but we'll mock or calculate if possible
$totalCampaigns = 0; // Placeholder until we have campaign data passed
foreach ($sponsors as $s) {
    if (isset($s['campaigns'])) $totalCampaigns += count($s['campaigns']);
}
?>

<!-- Premium Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-handshake"></i>
                    <h1>Sponsorships</h1>
                </div>
                <div class="header-subtitle"><?php echo $totalSponsors; ?> partners • <?php echo $activeSponsors; ?> active</div>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary btn-compact" data-bs-toggle="modal" data-bs-target="#addSponsorModal">
                    <i class="fas fa-plus"></i>
                    <span>New Partner</span>
                </button>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalSponsors; ?></div>
                    <div class="stat-label">Total Partners</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $activeSponsors; ?></div>
                    <div class="stat-label">Active Partners</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalCampaigns; ?></div>
                    <div class="stat-label">Live Campaigns</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">Analytics</div>
                    <div class="stat-label">View Impact</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search partners..." id="page-search">
                    <button class="search-clear" id="search-clear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <select id="status-filter" class="filter-compact">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="toolbar-right">
                <div class="view-controls">
                    <button class="view-btn active" data-view="table" title="Table View">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="view-btn" data-view="grid" title="Grid View">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="pages-content">

            <!-- Table View -->
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($sponsors)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-handshake"></i>
                            <h3>No partners onboarded</h3>
                            <p>Add your first B2B partner to start managing campaigns</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSponsorModal">
                                <i class="fas fa-plus"></i>
                                Add Partner
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-checkbox">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th class="col-title">Partner</th>
                                        <th class="col-title">Strategic Contact</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sponsors as $sponsor): ?>
                                        <tr data-page-id="<?php echo $sponsor['id']; ?>" class="page-row">
                                            <td>
                                                <input type="checkbox" class="page-checkbox" value="<?php echo $sponsor['id']; ?>">
                                            </td>
                                            <td>
                                                <div class="user-info-compact" style="display:flex; align-items:center; gap:0.75rem;">
                                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--admin-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight:600; overflow: hidden;">
                                                        <?php if (!empty($sponsor['logo_path'])): ?>
                                                            <img src="/storage/uploads/admin/logos/<?= $sponsor['logo_path'] ?>" style="width:100%; height:100%; object-fit:cover;">
                                                        <?php else: ?>
                                                            <?php echo strtoupper(substr($sponsor['name'], 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="page-info">
                                                        <div class="page-title-compact"><?php echo htmlspecialchars($sponsor['name']); ?></div>
                                                        <div class="page-slug-compact"><?php echo htmlspecialchars($sponsor['website_url'] ?? ''); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="author-compact">
                                                    <span class="fw-bold"><?php echo htmlspecialchars($sponsor['contact_person'] ?? 'N/A'); ?></span>
                                                    <span class="text-muted small"><?php echo htmlspecialchars($sponsor['contact_email'] ?? ''); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo ($sponsor['status'] ?? 'active') === 'active' ? 'active' : 'inactive'; ?>">
                                                    <i class="fas fa-<?php echo ($sponsor['status'] ?? 'active') === 'active' ? 'check-circle' : 'ban'; ?>"></i>
                                                    <?php echo ucfirst($sponsor['status'] ?? 'active'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                    <button class="action-btn-icon edit-btn"
                                                        data-bs-toggle="modal" data-bs-target="#createCampaignModal"
                                                        onclick="setSponsorId(<?= $sponsor['id'] ?>, '<?= htmlspecialchars($sponsor['name'], ENT_QUOTES) ?>')"
                                                        title="New Campaign">
                                                        <i class="fas fa-rocket"></i>
                                                    </button>
                                                    <button class="action-btn-icon" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="action-btn-icon delete-btn" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Grid View (Optional implementation similar to users) -->
            <div id="grid-view" class="view-section">
                <!-- Grid items would go here -->
            </div>
        </div>
    </div>
</div>

<!-- Copy the Modals with consistent styling -->

<!-- Add Sponsor Modal -->
<div class="modal fade" id="addSponsorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content premium-modal-container" method="POST" action="/admin/sponsors/store" enctype="multipart/form-data">
            <div class="premium-modal-header">
                <div class="header-icon-box">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="header-text">
                    <h5 class="modal-title">Onboard New Partner</h5>
                    <p class="modal-subtitle">Establish a strategic sponsorship connection</p>
                </div>
                <button type="button" class="btn-close-premium" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="premium-modal-body">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="premium-input-group">
                            <label class="premium-label">Company Name</label>
                            <div class="input-with-icon">
                                <i class="fas fa-building"></i>
                                <input type="text" name="name" class="premium-control" required placeholder="e.g. Acme Corporation">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="premium-input-group">
                            <label class="premium-label">Strategic Website</label>
                            <div class="input-with-icon">
                                <i class="fas fa-globe"></i>
                                <input type="url" name="website_url" class="premium-control" placeholder="https://corporate.acme.com">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="premium-input-group">
                            <label class="premium-label">Primary Contact</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user-tie"></i>
                                <input type="text" name="contact_person" class="premium-control" placeholder="Full name">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="premium-input-group">
                            <label class="premium-label">Corporate Email</label>
                            <div class="input-with-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="contact_email" class="premium-control" placeholder="contact@acme.com">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="premium-upload-zone">
                            <label class="premium-label">Brand Identity (Logo)</label>
                            <div class="upload-wrapper">
                                <i class="fas fa-image"></i>
                                <input type="file" name="logo" class="premium-file-input" accept="image/*" id="logoUpload">
                                <label for="logoUpload" class="upload-trigger">Click to select brand logo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="premium-modal-footer">
                <button type="button" class="btn-premium-secondary" data-bs-dismiss="modal">Discard</button>
                <button type="submit" class="btn-premium-primary">
                    <span>Finalize Onboarding</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Create Campaign Modal -->
<div class="modal fade" id="createCampaignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content premium-modal-container bg-campaign" method="POST" action="/admin/sponsors/campaigns/create">
            <input type="hidden" name="sponsor_id" id="campaignSponsorId">

            <div class="premium-modal-header variant-info">
                <div class="header-icon-box shadow-info">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="header-text">
                    <h5 class="modal-title">Launch Campaign</h5>
                    <p class="modal-subtitle">Strategic placement for <span id="campaignSponsorName" class="fw-bold text-white"></span></p>
                </div>
                <button type="button" class="btn-close-premium" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="premium-modal-body">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="premium-input-group">
                            <label class="premium-label">Campaign Title</label>
                            <div class="input-with-icon">
                                <i class="fas fa-tag"></i>
                                <input type="text" name="title" class="premium-control" required placeholder="e.g. Q4 Growth Acceleration">
                            </div>
                            <small class="text-muted mt-1 d-block opacity-75">Internal reference name only</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="premium-input-group">
                            <label class="premium-label">Target Calculator</label>
                            <div class="input-with-icon">
                                <i class="fas fa-calculator"></i>
                                <input type="text" name="calculator_slug" class="premium-control" required placeholder="e.g. concrete">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="premium-input-group">
                            <label class="premium-label">Priority Tier</label>
                            <div class="input-with-icon">
                                <i class="fas fa-layer-group"></i>
                                <input type="number" name="priority" class="premium-control" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="premium-input-group">
                            <label class="premium-label">Ad Headline (Strategic Message)</label>
                            <div class="input-with-icon">
                                <i class="fas fa-bullhorn"></i>
                                <input type="text" name="ad_text" class="premium-control" placeholder="Visual copy for the placement">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="premium-input-group">
                            <label class="premium-label">Deployment Date</label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar-plus"></i>
                                <input type="date" name="start_date" class="premium-control" required value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="premium-input-group">
                            <label class="premium-label">Expiration Date</label>
                            <div class="input-with-icon">
                                <i class="fas fa-calendar-minus"></i>
                                <input type="date" name="end_date" class="premium-control" required value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="premium-input-group">
                            <label class="premium-label">Impression Ceiling</label>
                            <div class="input-with-icon">
                                <i class="fas fa-eye"></i>
                                <input type="number" name="max_impressions" class="premium-control" value="0" placeholder="0 = Infinite">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="premium-modal-footer">
                <button type="button" class="btn-premium-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-premium-info">
                    <span>Execute Campaign</span>
                    <i class="fas fa-bolt"></i>
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    function setSponsorId(id, name) {
        document.getElementById('campaignSponsorId').value = id;
        document.getElementById('campaignSponsorName').innerText = name;
    }

    // Logic for switching views (Table/Grid)
    document.addEventListener('DOMContentLoaded', function() {
        const viewBtns = document.querySelectorAll('.view-btn');
        const sections = document.querySelectorAll('.view-section');

        viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.dataset.view;
                viewBtns.forEach(b => b.classList.toggle('active', b === btn));
                sections.forEach(s => s.classList.toggle('active', s.id === view + '-view'));
            });
        });
    });
</script>

<style>
    /* CSS CLONE FROM USERS MANAGEMENT - Optimized for consistency */

    .admin-wrapper-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
        background: #f8f9fa;
        min-height: calc(100vh - 70px);
    }

    .admin-content-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .header-title h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .header-subtitle {
        font-size: 0.875rem;
        opacity: 0.8;
    }

    .btn-compact {
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
        border-radius: 8px;
        font-weight: 500;
    }

    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .stat-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .stat-icon.primary {
        background: #667eea;
    }

    .stat-icon.success {
        background: #48bb78;
    }

    .stat-icon.warning {
        background: #ed8936;
    }

    .stat-icon.info {
        background: #4299e1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 500;
    }

    .compact-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .search-compact {
        position: relative;
        min-width: 250px;
    }

    .search-compact i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .search-compact input {
        width: 100%;
        padding: 0.625rem 0.75rem 0.625rem 2.5rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .filter-compact {
        padding: 0.625rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
        background: white;
    }

    .view-controls {
        display: flex;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        overflow: hidden;
    }

    .view-btn {
        padding: 0.625rem;
        border: none;
        background: white;
        color: #6b7280;
        cursor: pointer;
    }

    .view-btn.active {
        background: #667eea;
        color: white;
    }

    .table-compact {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table-compact th {
        background: #f8f9fa;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
    }

    .table-compact td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .table-compact tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-active {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .status-inactive {
        background: rgba(237, 137, 54, 0.1);
        color: #ed8936;
    }

    .actions-compact {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .action-btn-icon {
        width: 2.25rem;
        height: 2.25rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: white;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .action-btn-icon:hover {
        background: #f3f4f6;
        transform: translateY(-2px);
    }

    .edit-btn:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .delete-btn:hover {
        background: #f56565;
        color: white;
        border-color: #f56565;
    }

    .empty-state-compact {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state-compact i {
        font-size: 3rem;
        color: #9ca3af;
        margin-bottom: 1rem;
    }

    /* ========================================
   ULTRA-PREMIUM MODAL SYSTEM
   ======================================== */

    .premium-modal-container {
        border-radius: 24px;
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        background: #ffffff;
        overflow: hidden;
    }

    .premium-modal-header {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        padding: 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        position: relative;
        border: none;
    }

    .variant-info {
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    }

    .header-icon-box {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.5rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .shadow-info {
        box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
    }

    .header-text .modal-title {
        color: #ffffff !important;
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0;
        border: none;
    }

    .header-text .modal-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        margin: 0.25rem 0 0 0;
    }

    .btn-close-premium {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-close-premium:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .premium-modal-body {
        padding: 2rem;
    }

    .premium-input-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .premium-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #4b5563;
        letter-spacing: 0.02em;
        padding-left: 0.25rem;
    }

    .input-with-icon {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-with-icon i {
        position: absolute;
        left: 1rem;
        color: #9ca3af;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    .premium-control {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 3rem;
        border-radius: 12px;
        border: 2px solid #f3f4f6;
        background: #f9fafb;
        color: #1f2937;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .premium-control:focus {
        outline: none;
        background: #ffffff;
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .premium-upload-zone .upload-wrapper {
        position: relative;
        border: 2px dashed #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .premium-upload-zone .upload-wrapper:hover {
        border-color: #6366f1;
        background: rgba(99, 102, 241, 0.05);
    }

    .premium-file-input {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .upload-trigger {
        font-size: 0.9rem;
        color: #6b7280;
        font-weight: 500;
        margin: 0;
    }

    .premium-modal-footer {
        padding: 1.5rem 2rem 2rem;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        border: none;
    }

    .btn-premium-primary,
    .btn-premium-info {
        padding: 0.875rem 1.75rem;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
        color: #ffffff;
    }

    .btn-premium-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    .btn-premium-info {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
    }

    .btn-premium-primary:hover,
    .btn-premium-info:hover {
        transform: translateY(-2px);
        filter: brightness(1.1);
    }

    .btn-premium-secondary {
        padding: 0.875rem 1.75rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #4b5563;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
</style>