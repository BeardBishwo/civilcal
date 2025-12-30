<?php
$content = '
<div class="admin-content premium-admin">
    <div class="page-header-premium">
        <div class="header-main">
            <h1 class="page-title">
                <i class="fas fa-coins text-warning"></i>
                Gamenta World Economy
            </h1>
            <p class="page-description">The Command Center for your game\'s financial and social ecosystem.</p>
        </div>
        <div class="header-actions">
            <span class="badge badge-pill badge-primary-subtle">Game Engine v2.1</span>
        </div>
    </div>

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
            <div class="premium-card-admin">
                <div class="card-header-premium">
                    <h3 class="card-title-premium">Master Resource Ledger</h3>
                </div>
                <div class="card-content-premium">
                    <form onsubmit="saveEconomy(event, \'resources\')" class="economy-form">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Display Name</th>
                                    <th>Icon Path</th>
                                    <th>Buy</th>
                                    <th>Sell</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($resources as $key => $res) {
                                $content .= '
                                <tr>
                                    <td class="td-key"><code>' . $key . '</code></td>
                                    <td><input type="text" name="resources['.$key.'][name]" value="'.htmlspecialchars($res['name']).'" class="form-control-premium"></td>
                                    <td><input type="text" name="resources['.$key.'][icon]" value="'.htmlspecialchars($res['icon']).'" class="form-control-premium small"></td>
                                    <td><input type="number" name="resources['.$key.'][buy]" value="'.$res['buy'].'" class="form-control-premium" style="width: 70px;"></td>
                                    <td><input type="number" name="resources['.$key.'][sell]" value="'.$res['sell'].'" class="form-control-premium" style="width: 70px;"></td>
                                    <td class="text-center">
                                        <img src="'.app_base_url($res['icon']).'" class="res-preview-admin">
                                    </td>
                                </tr>';
                            }
                            $content .= '
                            </tbody>
                        </table>
                        <div class="form-actions-premium">
                            <button type="submit" class="btn-premium-save">
                                <i class="fas fa-save"></i> Save Resources
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- BUNDLES TAB -->
        <div id="bundles-tab" class="tab-content-premium">
            <div class="premium-card-admin">
                <div class="card-header-premium">
                    <h3 class="card-title-premium">Bulk Purchase Bundles</h3>
                    <p class="text-muted small mb-0">Configure special bundle offers with bulk discounts</p>
                </div>
                <div class="card-content-premium">
                    <form onsubmit="saveEconomy(event, \'bundles\')" class="economy-form">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Bundle Key</th>
                                    <th>Name</th>
                                    <th>Resource</th>
                                    <th>Qty</th>
                                    <th>Buy Price</th>
                                    <th>Sell Price</th>
                                    <th>Savings</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($bundles as $key => $bundle) {
                                $content .= '
                                <tr>
                                    <td class="td-key"><code>' . $key . '</code></td>
                                    <td><input type="text" name="bundles['.$key.'][name]" value="'.htmlspecialchars($bundle['name']).'" class="form-control-premium"></td>
                                    <td><input type="text" name="bundles['.$key.'][resource]" value="'.htmlspecialchars($bundle['resource']).'" class="form-control-premium" style="width: 100px;"></td>
                                    <td><input type="number" name="bundles['.$key.'][qty]" value="'.$bundle['qty'].'" class="form-control-premium" style="width: 60px;"></td>
                                    <td><input type="number" name="bundles['.$key.'][buy]" value="'.$bundle['buy'].'" class="form-control-premium" style="width: 80px;"></td>
                                    <td><input type="number" name="bundles['.$key.'][sell]" value="'.$bundle['sell'].'" class="form-control-premium" style="width: 80px;"></td>
                                    <td><input type="number" name="bundles['.$key.'][savings]" value="'.$bundle['savings'].'" class="form-control-premium" style="width: 70px;"></td>
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
                            <button type="submit" class="btn-premium-save">
                                <i class="fas fa-box-open"></i> Save Bundles
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- CASH PACKS TAB -->
        <div id="cash-tab" class="tab-content-premium">
            <div class="premium-card-admin">
                <div class="card-header-premium">
                    <h3 class="card-title-premium">Real Money Packs</h3>
                    <p class="text-muted small mb-0">Configure in-app purchase offerings (requires payment gateway)</p>
                </div>
                <div class="card-content-premium">
                    <form onsubmit="saveEconomy(event, \'cash_packs\')" class="economy-form">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Pack Key</th>
                                    <th>Display Name</th>
                                    <th>Coins Awarded</th>
                                    <th>Price (USD)</th>
                                    <th>Popular</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($cashPacks as $key => $pack) {
                                $content .= '
                                <tr>
                                    <td class="td-key"><code>' . $key . '</code></td>
                                    <td><input type="text" name="cash_packs['.$key.'][name]" value="'.htmlspecialchars($pack['name']).'" class="form-control-premium"></td>
                                    <td><input type="number" name="cash_packs['.$key.'][coins]" value="'.$pack['coins'].'" class="form-control-premium" style="width: 100px;"></td>
                                    <td><input type="number" step="0.01" name="cash_packs['.$key.'][price_usd]" value="'.$pack['price_usd'].'" class="form-control-premium" style="width: 80px;"></td>
                                    <td class="text-center">
                                        <label class="switch-small">
                                            <input type="checkbox" name="cash_packs['.$key.'][popular]" value="1" '.($pack['popular'] ? 'checked' : '').'>
                                            <span class="slider-small"></span>
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
                            <button type="submit" class="btn-premium-save">
                                <i class="fas fa-dollar-sign"></i> Save Cash Packs
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RANKS TAB -->
        <div id="ranks-tab" class="tab-content-premium">
            <div class="premium-card-admin">
                <div class="card-header-premium">
                    <h3 class="card-title-premium">Hierarchy & Progression</h3>
                </div>
                <div class="card-content-premium">
                    <form onsubmit="saveEconomy(event, \'ranks\')" class="economy-form">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Tier</th>
                                    <th>Title</th>
                                    <th>Min Power</th>
                                    <th>Icon Path</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($ranks as $index => $rank) {
                                $content .= '
                                <tr>
                                    <td class="text-center font-weight-bold">' . $rank['level'] . '</td>
                                    <td><input type="text" name="ranks['.$index.'][name]" value="'.htmlspecialchars($rank['name']).'" class="form-control-premium"></td>
                                    <td><input type="number" name="ranks['.$index.'][min]" value="'.$rank['min'].'" class="form-control-premium"></td>
                                    <td><input type="text" name="ranks['.$index.'][icon]" value="'.htmlspecialchars($rank['icon']).'" class="form-control-premium small"></td>
                                    <td class="text-center">
                                        <img src="'.app_base_url($rank['icon']).'" class="rank-preview-admin">
                                    </td>
                                    <input type="hidden" name="ranks['.$index.'][level]" value="'.$rank['level'].'">
                                </tr>';
                            }
                            $content .= '
                            </tbody>
                        </table>
                        <div class="form-actions-premium">
                            <button type="submit" class="btn-premium-save">
                                <i class="fas fa-award"></i> Save Ranks
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- HUD TAB -->
        <div id="hud-tab" class="tab-content-premium">
            <div class="premium-card-admin">
                <div class="card-header-premium">
                    <h3 class="card-title-premium">HUD Styling</h3>
                </div>
                <div class="card-content-premium">
                    <form onsubmit="saveEconomy(event, \'hud\')" class="economy-form">
                        <div class="premium-grid-2">
                            <div class="input-group-premium">
                                <label>Header Height</label>
                                <input type="text" name="hud[header_height]" value="'.($hudConfig['header_height'] ?? '40px').'" class="form-control-premium">
                            </div>
                            <div class="input-group-premium">
                                <label>Icon Size</label>
                                <input type="text" name="hud[icon_size]" value="'.($hudConfig['icon_size'] ?? '24px').'" class="form-control-premium">
                            </div>
                            <div class="input-group-premium">
                                <label>Font Size</label>
                                <input type="text" name="hud[font_size]" value="'.($hudConfig['font_size'] ?? '14px').'" class="form-control-premium">
                            </div>
                            <div class="input-group-premium">
                                <label>Item Gap</label>
                                <input type="text" name="hud[gap]" value="'.($hudConfig['gap'] ?? '20px').'" class="form-control-premium">
                            </div>
                        </div>
                        <div class="form-actions-premium">
                            <button type="submit" class="btn-premium-save">
                                <i class="fas fa-paint-brush"></i> Save HUD
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --premium-primary: #daa520;
    --premium-bg: #f8fafc;
    --premium-dark: #0f172a;
    --premium-border: #e2e8f0;
}

.premium-admin { padding: 30px; background: var(--premium-bg); min-height: 100vh; }
.page-header-premium { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
.page-title { font-size: 2.2rem; font-weight: 800; color: var(--premium-dark); margin-bottom: 5px; }
.page-description { color: #64748b; font-size: 1.1rem; }
.badge-primary-subtle { background: #eff6ff; color: #2563eb; padding: 8px 16px; font-weight: 700; font-size: 0.8rem; border: 1px solid #dbeafe; }

.tabs-header-premium { display: flex; gap: 10px; margin-bottom: 30px; border-bottom: 1px solid var(--premium-border); padding-bottom: 1px; flex-wrap: wrap; }
.tab-btn-premium { background: none; border: none; padding: 12px 20px; font-weight: 700; color: #64748b; cursor: pointer; transition: 0.3s; border-bottom: 3px solid transparent; display: flex; align-items: center; gap: 8px; font-size: 0.95rem; }
.tab-btn-premium:hover { color: var(--premium-dark); }
.tab-btn-premium.active { color: var(--premium-primary); border-bottom-color: var(--premium-primary); }

.tab-content-premium { display: none; animation: fadeIn 0.4s ease-out; }
.tab-content-premium.active { display: block; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.premium-card-admin { background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--premium-border); overflow: hidden; }
.card-header-premium { padding: 25px 30px; background: #fcfcfc; border-bottom: 1px solid var(--premium-border); }
.card-title-premium { font-size: 1.2rem; font-weight: 700; color: var(--premium-dark); margin: 0; }
.card-content-premium { padding: 30px; }

.premium-table { width: 100%; border-collapse: collapse; }
.premium-table th { text-align: left; padding: 12px; background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--premium-border); }
.premium-table td { padding: 12px; border-bottom: 1px solid var(--premium-border); vertical-align: middle; }
.td-key code { background: #fee2e2; color: #b91c1c; padding: 4px 8px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; }

.form-control-premium { width: 100%; padding: 8px 12px; border: 1.5px solid var(--premium-border); border-radius: 8px; font-weight: 600; color: var(--premium-dark); transition: 0.2s; font-size: 0.9rem; }
.form-control-premium:focus { outline: none; border-color: var(--premium-primary); box-shadow: 0 0 0 3px rgba(218, 165, 32, 0.1); }
.form-control-premium.small { font-size: 0.75rem; color: #64748b; }

.res-preview-admin, .rank-preview-admin { width: 32px; height: 32px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); }

.premium-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
.input-group-premium label { display: block; font-weight: 700; margin-bottom: 8px; color: #1e293b; font-size: 0.9rem; }

.form-actions-premium { margin-top: 30px; text-align: right; }
.btn-premium-save { background: var(--premium-dark); color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 0.95rem; box-shadow: 0 8px 12px -3px rgba(15, 23, 42, 0.3); }
.btn-premium-save:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 12px 20px -5px rgba(15, 23, 42, 0.4); }

.switch-small { position: relative; display: inline-block; width: 40px; height: 22px; }
.switch-small input { opacity: 0; width: 0; height: 0; }
.slider-small { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .3s; border-radius: 22px; }
.slider-small:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
input:checked + .slider-small { background-color: var(--premium-primary); }
input:checked + .slider-small:before { transform: translateX(18px); }
</style>

<script>
async function saveEconomy(event, type) {
    event.preventDefault();
    const form = event.target;
    const submitBtn = form.querySelector(\'button[type="submit"]\');
    const originalContent = submitBtn.innerHTML;
    
    submitBtn.innerHTML = \'<i class="fas fa-spinner fa-spin"></i> Saving...\';
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
            toastPremium(result.message, "success");
        } else {
            toastPremium("Error: " + result.message, "error");
        }
    } catch (error) {
        toastPremium("Failed: " + error.message, "error");
    } finally {
        submitBtn.innerHTML = originalContent;
        submitBtn.disabled = false;
    }
}

function toastPremium(message, type) {
    const toast = document.createElement("div");
    toast.style.cssText = `
        position: fixed; top: 20px; right: 20px; padding: 16px 32px; 
        border-radius: 12px; background: #0f172a; color: white; 
        font-weight: 700; z-index: 10000; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3);
        border-left: 6px solid ${type === "success" ? "#10b981" : "#ef4444"};
        animation: slideIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = "0"; toast.style.transform = "translateX(100px)"; toast.style.transition = "0.5s"; setTimeout(() => toast.remove(), 500); }, 4000);
}

const style = document.createElement("style");
style.textContent = "@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }";
document.head.appendChild(style);

function openTab(evt, tabName) {
    const tabContents = document.getElementsByClassName("tab-content-premium");
    for (let i = 0; i < tabContents.length; i++) tabContents[i].classList.remove("active");
    const tabBtns = document.getElementsByClassName("tab-btn-premium");
    for (let i = 0; i < tabBtns.length; i++) tabBtns[i].classList.remove("active");
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
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
