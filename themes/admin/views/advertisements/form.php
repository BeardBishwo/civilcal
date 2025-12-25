<?php

/**
 * OPTIMIZED ADVERTISEMENT CREATION/EDITING INTERFACE
 * Matches the 'Pages' module design as requested.
 */

// Page variables
$page_title = isset($ad) ? 'Edit Advertisement - Admin Panel' : 'New Advertisement - Admin Panel';
$currentPage = 'advertisements';
$breadcrumbs = [
    ['title' => 'Admin', 'url' => app_base_url('/admin')],
    ['title' => 'Advertisements', 'url' => app_base_url('/admin/advertisements')],
    ['title' => isset($ad) ? 'Edit Ad' : 'New Ad']
];

$is_edit = isset($ad);
?>

<!-- Optimized Admin Container -->
<div class="page-create-container">
    <div class="page-create-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-create-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-<?php echo $is_edit ? 'edit' : 'plus'; ?>"></i>
                    <h1><?php echo $is_edit ? 'Edit Campaign' : 'New Campaign'; ?></h1>
                </div>
                <div class="header-subtitle">
                    <?php echo $is_edit ? 'Update your advertisement content and placement' : 'Create a new advertisement with rich paylaod'; ?>
                </div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/advertisements'); ?>" class="btn btn-secondary btn-compact">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Ads</span>
                </a>
            </div>
        </div>

        <!-- Main Content Layout (Single Column) -->
        <div class="create-content-single-column">

            <form id="ad-form" method="POST" action="<?php echo $is_edit ? app_base_url('/admin/advertisements/update/' . $ad['id']) : app_base_url('/admin/advertisements/store'); ?>" class="main-form-container">

                <!-- Title & Slug Section -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title">Campaign Details</h3>
                    </div>
                    <div class="card-body-clean">
                        <div class="form-group-modern">
                            <label for="ad-name" class="form-label required">Internal Reference Name</label>
                            <input
                                type="text"
                                id="ad-name"
                                name="name"
                                class="form-control-modern form-control-lg"
                                value="<?php echo isset($ad) ? htmlspecialchars($ad['name']) : ''; ?>"
                                placeholder="e.g. 'Google AdSense Header' or 'Black Friday Banner'"
                                required
                                maxlength="255">
                            <small class="text-muted mt-2 d-block">This name is for your reference in the admin panel only.</small>
                        </div>
                    </div>
                </div>

                <!-- Content Editor Section -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title"><i class="fas fa-code"></i> Ad Payload (HTML / JS)</h3>
                    </div>
                    <div class="card-body-clean p-0">
                        <textarea id="code-editor" name="code" class="form-control-modern font-monospace" rows="15" 
                                  style="font-family: 'Fira Code', 'Consolas', monospace; font-size: 0.95rem; border:none; padding: 1.5rem; background: #fff;"
                                  placeholder="<!-- Paste your ad code, script or iframe here -->" required><?php echo isset($ad) ? htmlspecialchars($ad['code']) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Settings Grid -->
                <div class="settings-grid">

                    <!-- Placement Settings -->
                    <div class="content-card">
                        <div class="card-header-clean">
                            <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Placement</h3>
                        </div>
                        <div class="card-body-clean">
                            <div class="form-group-modern">
                                <label for="ad-location" class="form-label required">Display Zone</label>
                                <select id="ad-location" name="location" class="form-control-modern" required>
                                    <option value="" disabled <?php echo !isset($ad) ? 'selected' : ''; ?>>Select a zone...</option>
                                    <?php
                                    $locations = [
                                        'header_top' => 'Header (Above Nav)',
                                        'sidebar_top' => 'Sidebar (Top Widget)',
                                        'sidebar_bottom' => 'Sidebar (Sticky Bottom)',
                                        'calc_result' => 'Calculator Result (High CTR)',
                                        'footer_top' => 'Footer (Full Width)'
                                    ];
                                    $current = isset($ad) ? $ad['location'] : '';
                                    ?>
                                    <?php foreach ($locations as $key => $label): ?>
                                        <option value="<?php echo $key; ?>" <?php echo $current === $key ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Status Settings -->
                    <div class="content-card">
                        <div class="card-header-clean">
                            <h3 class="card-title"><i class="fas fa-toggle-on"></i> Status</h3>
                        </div>
                        <div class="card-body-clean">
                            <div class="form-group-modern">
                                <label for="ad-active" class="form-label">Active State</label>
                                <div class="form-check form-switch p-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="ad-active" style="width: 3em; height: 1.5em; margin-left: 0;"
                                           <?php echo (!isset($ad) || !empty($ad['is_active'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label ms-3 pt-1" for="ad-active">
                                        Enable this advertisement
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="<?php echo app_base_url('/admin/advertisements'); ?>" class="btn btn-secondary btn-lg">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-2"></i> Save Campaign
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
/* ========================================
   OPTIMIZED DESIGN SYSTEM (Copied from Pages)
   ======================================== */
:root {
    --primary-600: #4f46e5;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-700: #374151;
    --gray-900: #111827;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --radius-lg: 0.75rem;
}

.page-create-container {
    max-width: 100%;
    padding-bottom: 5rem;
    background-color: var(--gray-50);
    min-height: 100vh;
}

.compact-create-header {
    max-width: 960px; margin: 0 auto; padding: 2rem 0 1.5rem 0;
    display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem;
}
.header-title { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; }
.header-title i { font-size: 1.75rem; color: var(--primary-600); }
.header-title h1 { margin: 0; font-size: 1.875rem; font-weight: 700; color: var(--gray-900); }
.header-subtitle { font-size: 0.9375rem; color: var(--gray-500); margin: 0; }
.btn-compact { padding: 0.625rem 1.25rem; font-size: 0.875rem; border-radius: 8px; font-weight: 500; }

.create-content-single-column { max-width: 960px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem; }

/* CARDS */
.content-card {
    background: white; border-radius: var(--radius-lg); border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm); overflow: hidden;
}
.card-header-clean {
    padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100); background: white;
}
.card-title { font-size: 1.125rem; font-weight: 600; color: var(--gray-900); margin: 0; display: flex; align-items: center; gap: 0.625rem; }
.card-body-clean { padding: 1.5rem; }
.card-body-clean.p-0 { padding: 0; }

/* FORMS */
.form-group-modern { margin-bottom: 1.5rem; }
.form-label { display: block; font-size: 0.875rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem; }
.form-control-modern {
    width: 100%; padding: 0.625rem 0.875rem; font-size: 0.95rem; border: 1px solid var(--gray-300); border-radius: 0.5rem; transition: all 0.2s;
}
.form-control-modern:focus { border-color: var(--primary-600); outline: none; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
.settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
</style>
