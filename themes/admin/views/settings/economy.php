<?php
$content = '
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Premium Gradient Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-coins"></i>
                    <h1>Economy Mastery</h1>
                </div>
                <div class="header-subtitle">Command center for resources, bundles, cash packs, and progression ranks.</div>
            </div>
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">ENGINE</span>
                    <span class="value">v2.1</span>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="premium-tabs-container">
            <div class="tabs-header-premium">
                <button class="tab-btn-premium active" onclick="openTab(event, \'resources-tab\')">
                    <i class="fas fa-boxes"></i> Resources
                </button>
                <button class="tab-btn-premium" onclick="openTab(event, \'bundles-tab\')">
                    <i class="fas fa-box-open"></i> Bundles
                </button>
                <button class="tab-btn-premium" onclick="openTab(event, \'cash-tab\')">
                    <i class="fas fa-dollar-sign"></i> Cash Packs
                </button>
                <button class="tab-btn-premium" onclick="openTab(event, \'ranks-tab\')">
                    <i class="fas fa-award"></i> Ranks
                </button>
                <button class="tab-btn-premium" onclick="openTab(event, \'hud-tab\')">
                    <i class="fas fa-desktop"></i> HUD
                </button>
            </div>

            <!-- RESOURCES TAB -->
            <div id="resources-tab" class="tab-content-premium active">
                <div class="creation-toolbar">
                    <h5 class="toolbar-title"><i class="fas fa-boxes mr-2"></i> Master Resource Ledger</h5>
                </div>
                <div class="table-container">
                    <form onsubmit="saveEconomy(event, \'resources\')" class="economy-form">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Display Name</th>
                                    <th>Icon Path</th>
                                    <th>Buy</th>
                                    <th>Sell</th>
                                    <th class="text-center">Preview</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($resources as $key => $res) {
                                $content .= '
                                <tr class="category-item">
                                    <td class="td-key"><code>' . $key . '</code></td>
                                    <td><input type="text" name="resources['.$key.'][name]" value="'.htmlspecialchars($res['name']).'" class="form-input-premium"></td>
                                    <td><input type="text" name="resources['.$key.'][icon]" value="'.htmlspecialchars($res['icon']).'" class="form-input-premium small"></td>
                                    <td><input type="number" name="resources['.$key.'][buy]" value="'.$res['buy'].'" class="form-input-premium" style="width: 80px;"></td>
                                    <td><input type="number" name="resources['.$key.'][sell]" value="'.$res['sell'].'" class="form-input-premium" style="width: 80px;"></td>
                                    <td class="text-center">
                                        <img src="'.app_base_url($res['icon']).'" class="res-preview-admin">
                                    </td>
                                </tr>';
                            }
                            $content .= '
                            </tbody>
                        </table>
                        <div class="form-actions-premium">
                            <button type="submit" class="btn-create-premium">
                                <i class="fas fa-save"></i> SAVE RESOURCES
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- BUNDLES TAB -->
            <div id="bundles-tab" class="tab-content-premium">
                <div class="creation-toolbar">
                    <h5 class="toolbar-title"><i class="fas fa-box-open mr-2"></i> Bulk Purchase Bundles</h5>
                </div>
                <div class="table-container">
                    <form onsubmit="saveEconomy(event, \'bundles\')" class="economy-form">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th>Bundle Key</th>
                                    <th>Name</th>
                                    <th>Resource</th>
                                    <th>Qty</th>
                                    <th>Buy Price</th>
                                    <th>Sell Price</th>
                                    <th>Savings</th>
                                    <th class="text-center">Preview</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($bundles as $key => $bundle) {
                                $content .= '
                                <tr class="category-item">
                                    <td class="td-key"><code>' . $key . '</code></td>
                                    <td><input type="text" name="bundles['.$key.'][name]" value="'.htmlspecialchars($bundle['name']).'" class="form-input-premium"></td>
                                    <td><input type="text" name="bundles['.$key.'][resource]" value="'.htmlspecialchars($bundle['resource']).'" class="form-input-premium" style="width: 100px;"></td>
                                    <td><input type="number" name="bundles['.$key.'][qty]" value="'.$bundle['qty'].'" class="form-input-premium" style="width: 70px;"></td>
                                    <td><input type="number" name="bundles['.$key.'][buy]" value="'.$bundle['buy'].'" class="form-input-premium" style="width: 80px;"></td>
                                    <td><input type="number" name="bundles['.$key.'][sell]" value="'.$bundle['sell'].'" class="form-input-premium" style="width: 80px;"></td>
                                    <td><input type="number" name="bundles['.$key.'][savings]" value="'.$bundle['savings'].'" class="form-input-premium" style="width: 70px;"></td>
                                    <td class="text-center">
                                        <img src="'.app_base_url($bundle['icon']).'" class="res-preview-admin">
                                    </td>
                                    <input type="hidden" name="bundles['.$key.'][icon]" value="'.htmlspecialchars($bundle['icon']).'">
                                    <input type="hidden" name="bundles['.$key.'][description]" value="'.htmlspecialchars($bundle['description']).'">
                                </tr>';
                            }
                            $content .= '
                            </tbody>
                        </table>
                        <div class="form-actions-premium">
                            <button type="submit" class="btn-create-premium">
                                <i class="fas fa-box-open"></i> SAVE BUNDLES
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CASH PACKS TAB -->
            <div id="cash-tab" class="tab-content-premium">
                <div class="creation-toolbar">
                    <h5 class="toolbar-title"><i class="fas fa-dollar-sign mr-2"></i> Real Money Packs</h5>
                </div>
                <div class="table-container">
                    <form onsubmit="saveEconomy(event, \'cash_packs\')" class="economy-form">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th>Pack Key</th>
                                    <th>Display Name</th>
                                    <th>Coins Awarded</th>
                                    <th>Price (USD)</th>
                                    <th class="text-center">Popular</th>
                                    <th class="text-center">Preview</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($cashPacks as $key => $pack) {
                                $content .= '
                                <tr class="category-item">
                                    <td class="td-key"><code>' . $key . '</code></td>
                                    <td><input type="text" name="cash_packs['.$key.'][name]" value="'.htmlspecialchars($pack['name']).'" class="form-input-premium"></td>
                                    <td><input type="number" name="cash_packs['.$key.'][coins]" value="'.$pack['coins'].'" class="form-input-premium" style="width: 100px;"></td>
                                    <td><input type="number" step="0.01" name="cash_packs['.$key.'][price_usd]" value="'.$pack['price_usd'].'" class="form-input-premium" style="width: 80px;"></td>
                                    <td class="text-center">
                                        <label class="switch scale-sm">
                                            <input type="checkbox" name="cash_packs['.$key.'][popular]" value="1" '.($pack['popular'] ? 'checked' : '').'>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <img src="'.app_base_url($pack['icon']).'" class="res-preview-admin">
                                    </td>
                                    <input type="hidden" name="cash_packs['.$key.'][icon]" value="'.htmlspecialchars($pack['icon']).'">
                                    <input type="hidden" name="cash_packs['.$key.'][description]" value="'.htmlspecialchars($pack['description']).'">
                                </tr>';
                            }
                            $content .= '
                            </tbody>
                        </table>
                        <div class="form-actions-premium">
                            <button type="submit" class="btn-create-premium">
                                <i class="fas fa-dollar-sign"></i> SAVE CASH PACKS
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RANKS TAB -->
            <div id="ranks-tab" class="tab-content-premium">
                <div class="creation-toolbar" style="display:flex; justify-content:space-between; align-items:center;">
                    <h5 class="toolbar-title" style="margin:0;"><i class="fas fa-award mr-2"></i> Hierarchy & Progression</h5>
                    <button type="button" class="btn-create-premium" onclick="resetRanksStandard()" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);">
                        <i class="fas fa-crown"></i> APPLY CIVIL CITY STANDARD
                    </button>
                </div>
                <div class="table-container">
                    <form onsubmit="saveEconomy(event, \'ranks\')" class="economy-form">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th class="text-center">Tier</th>
                                    <th>Title</th>
                                    <th>Min Power</th>
                                    <th>Icon Path</th>
                                    <th class="text-center">Preview</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($ranks as $index => $rank) {
                                $content .= '
                                <tr class="category-item">
                                    <td class="text-center"><span class="metric-text">' . $rank['level'] . '</span></td>
                                    <td><input type="text" name="ranks['.$index.'][name]" value="'.htmlspecialchars($rank['name']).'" class="form-input-premium"></td>
                                    <td><input type="number" name="ranks['.$index.'][min]" value="'.$rank['min'].'" class="form-input-premium"></td>
                                    <td><input type="text" name="ranks['.$index.'][icon]" value="'.htmlspecialchars($rank['icon']).'" class="form-input-premium small"></td>
                                    <td class="text-center">
                                        ' . (!empty($rank['icon']) 
                                            ? '<img src="'.app_base_url($rank['icon']).'" class="res-preview-admin">' 
                                            : '<span style="color: #cbd5e1; font-size: 0.75rem; font-style: italic;">No Icon</span>') . '
                                    </td>
                                    <input type="hidden" name="ranks['.$index.'][level]" value="'.$rank['level'].'">
                                </tr>';
                            }
                            $content .= '
                            </tbody>
                        </table>
                        <div class="form-actions-premium">
                            <button type="submit" class="btn-create-premium">
                                <i class="fas fa-award"></i> SAVE RANKS
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- HUD TAB -->
            <div id="hud-tab" class="tab-content-premium">
                <div class="creation-toolbar">
                    <h5 class="toolbar-title"><i class="fas fa-desktop mr-2"></i> HUD Styling Configuration</h5>
                </div>
                <div style="padding: 2rem;">
                    <form onsubmit="saveEconomy(event, \'hud\')" class="economy-form">
                        <div class="premium-grid-2">
                            <div class="input-group-premium">
                                <label>Header Height</label>
                                <input type="text" name="hud[header_height]" value="'.($hudConfig['header_height'] ?? '40px').'" class="form-input-premium">
                            </div>
                            <div class="input-group-premium">
                                <label>Icon Size</label>
                                <input type="text" name="hud[icon_size]" value="'.($hudConfig['icon_size'] ?? '24px').'" class="form-input-premium">
                            </div>
                            <div class="input-group-premium">
                                <label>Font Size</label>
                                <input type="text" name="hud[font_size]" value="'.($hudConfig['font_size'] ?? '14px').'" class="form-input-premium">
                            </div>
                            <div class="input-group-premium">
                                <label>Item Gap</label>
                                <input type="text" name="hud[gap]" value="'.($hudConfig['gap'] ?? '20px').'" class="form-input-premium">
                            </div>
                        </div>
                        <div class="form-actions-premium">
                            <button type="submit" class="btn-create-premium">
                                <i class="fas fa-paint-brush"></i> SAVE HUD
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ========================================
   PREMIUM CORE STYLES (MATCHING CATEGORY UI)
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-gray-50: #f8f9fa;
    --admin-gray-200: #e5e7eb;
    --admin-gray-300: #d1d5db;
    --admin-gray-400: #9ca3af;
    --admin-gray-600: #4b5563;
    --admin-gray-800: #1f2937;
}

.admin-wrapper-container {
    padding: 1rem;
    background: var(--admin-gray-50);
    min-height: calc(100vh - 70px);
}

.admin-content-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    overflow: hidden;
    /* padding-bottom: 2rem; REMOVED FOR CLEANER UI */
}

/* Header */
.compact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
    color: white;
}
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.25rem; opacity: 0.9; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    display: flex; flex-direction: column; align-items: center;
    min-width: 80px;
}
.stat-pill .label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

/* Tabs */
.premium-tabs-container { margin-top: 0; }
.tabs-header-premium { 
    display: flex; gap: 0; padding: 0 2rem; background: #f8fafc; 
    border-bottom: 2px solid var(--admin-gray-200); flex-wrap: wrap;
}
.tab-btn-premium { 
    background: none; border: none; padding: 1rem 1.5rem; font-weight: 700; 
    color: #64748b; cursor: pointer; transition: 0.3s; 
    border-bottom: 3px solid transparent; display: flex; align-items: center; 
    gap: 8px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;
    margin-bottom: -2px;
}
.tab-btn-premium:hover { color: var(--admin-primary); background: rgba(102, 126, 234, 0.05); }
.tab-btn-premium.active { color: var(--admin-primary); border-bottom-color: var(--admin-primary); background: white; }

.tab-content-premium { display: none; }
.tab-content-premium.active { display: block; }

/* Creation Toolbar (Section Headers) */
.creation-toolbar {
    padding: 1rem 2rem;
    background: #f8fafc;
    border-bottom: 1px solid var(--admin-gray-200);
}
.toolbar-title {
    font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;
}

/* Table */
.table-container { padding: 0; }
.table-compact { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.table-compact th {
    background: white; padding: 0.75rem 1.5rem; text-align: left; font-weight: 600;
    color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}
.table-compact td {
    padding: 0.6rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle;
}
.category-item:hover { background: #f8fafc; }

.td-key code { 
    background: #fee2e2; color: #b91c1c; padding: 4px 8px; 
    border-radius: 6px; font-weight: 600; font-size: 0.75rem; 
}

.metric-text { font-weight: 700; color: #64748b; font-size: 0.9rem; }

/* Form Inputs */
.input-group-premium { position: relative; }
.input-group-premium label { 
    display: block; font-weight: 700; margin-bottom: 8px; 
    color: #475569; font-size: 0.85rem; 
}
.form-input-premium {
    width: 100%; height: 36px; padding: 0 0.75rem; font-size: 0.85rem; font-weight: 600;
    border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: all 0.2s;
    background: white; color: #334155;
}
.form-input-premium:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
.form-input-premium.small { font-size: 0.7rem; color: #64748b; font-family: monospace; }

/* Buttons */
.btn-create-premium {
    height: 40px; padding: 0 1.5rem; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white; font-weight: 700; font-size: 0.85rem; border: none; border-radius: 8px; cursor: pointer;
    display: inline-flex; align-items: center; gap: 0.5rem; transition: 0.2s;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2); white-space: nowrap; letter-spacing: 0.5px; text-transform: uppercase;
}
.btn-create-premium:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3); }

.form-actions-premium { margin-top: 1.5rem; padding: 0 2rem 1rem; text-align: right; }

/* Previews */
.res-preview-admin { width: 32px; height: 32px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); }

/* Grid */
.premium-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

/* Switch Toggle */
.switch { position: relative; display: inline-block; width: 34px; height: 18px; margin: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #4f46e5; }
input:checked + .slider:before { transform: translateX(16px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }
.scale-sm { transform: scale(0.8); }

@media (max-width: 768px) {
    .premium-grid-2 { grid-template-columns: 1fr; }
}
</style>

<script>
async function saveEconomy(event, type) {
    event.preventDefault();
    const form = event.target;
    const submitBtn = form.querySelector(\'button[type="submit"]\');
    const originalContent = submitBtn.innerHTML;
    
    submitBtn.innerHTML = \'<i class="fas fa-spinner fa-spin"></i> SAVING...\';
    submitBtn.disabled = true;
    
    try {
        const formData = new FormData(form);
        formData.append(\'type\', type);
        formData.append(\'csrf_token\', \'' . ($_SESSION['csrf_token'] ?? '') . '\');
        
        const response = await fetch(\'' . app_base_url('admin/settings/economy/save') . '\', {
            method: \'POST\',
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            const Toast = Swal.mixin({ toast: true, position: \'top-end\', showConfirmButton: false, timer: 1500, timerProgressBar: true });
            Toast.fire({ icon: \'success\', title: result.message });
        } else {
            Swal.fire(\'Error\', result.message, \'error\');
        }
    } catch (error) {
        Swal.fire(\'Error\', \'Failed: \' + error.message, \'error\');
    } finally {
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    }
}

function openTab(evt, tabName) {
    const tabContents = document.getElementsByClassName("tab-content-premium");
    for (let i = 0; i < tabContents.length; i++) tabContents[i].classList.remove("active");
    const tabBtns = document.getElementsByClassName("tab-btn-premium");
    for (let i = 0; i < tabBtns.length; i++) tabBtns[i].classList.remove("active");
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}

async function resetRanksStandard() {
    const result = await Swal.fire({
        title: \'Apply Civil City Standard?\',
        text: "This will OVERWRITE all current ranks with the official 27-Tier System.",
        icon: \'warning\',
        showCancelButton: true,
        confirmButtonColor: \'#ef4444\',
        cancelButtonColor: \'#cbd5e1\',
        confirmButtonText: \'Yes, Apply It\'
    });
    
    if (!result.isConfirmed) return;
    
    const btn = document.querySelector(\'button[onclick="resetRanksStandard()"]\');
    const original = btn.innerHTML;
    btn.innerHTML = \'<i class="fas fa-spinner fa-spin"></i> APPLYING...\';
    btn.disabled = true;

    try {
        const response = await fetch(\'' . app_base_url('admin/settings/economy/reset-ranks') . '\', {
            method: \'POST\',
            headers: { \'Content-Type\': \'application/x-www-form-urlencoded\' },
            body: \'csrf_token=\' + encodeURIComponent(\'' . ($_SESSION['csrf_token'] ?? '') . '\')
        });
        
        const result = await response.json();
        
        if (result.success) {
            const Toast = Swal.mixin({ toast: true, position: \'top-end\', showConfirmButton: false, timer: 1500, timerProgressBar: true });
            Toast.fire({ icon: \'success\', title: result.message }).then(() => location.reload());
        } else {
            Swal.fire(\'Error\', result.message, \'error\');
            btn.innerHTML = original;
            btn.disabled = false;
        }
    } catch (e) {
        Swal.fire(\'Error\', e.message, \'error\');
        btn.innerHTML = original;
        btn.disabled = false;
    }
}
</script>
';

$breadcrumbs = [
    ['title' => 'Settings', 'url' => app_base_url('admin/settings')],
    ['title' => 'Economy Mastery']
];

$page_title = 'Economy Mastery - Admin Panel';
$currentPage = 'settings';

include __DIR__ . '/../../layouts/main.php';
?>
