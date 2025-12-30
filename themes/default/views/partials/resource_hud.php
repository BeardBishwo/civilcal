<?php
/**
 * Resource HUD - Sticky Top Bar with Dynamic Gamenta Economy Settings
 */
if (isset($_SESSION['user_id'])):
    $db = \App\Core\Database::getInstance();
    $wallet = $db->findOne('user_resources', ['user_id' => $_SESSION['user_id']]);
    
    // Fetch Dynamic Economy Settings
    $resources = \App\Services\SettingsService::get('economy_resources', []);
    $hudConfig = \App\Services\SettingsService::get('economy_hud_config', [
        'header_height' => '32px',
        'icon_size' => '20px',
        'font_size' => '12px',
        'gap' => '15px'
    ]);

    if ($wallet && !empty($resources)):
?>
<div class="resource-hud-sticky">
    <div class="container container-flex">
        <?php foreach ($resources as $key => $config): 
            $value = $wallet[$key] ?? 0;
            if ($value <= 0 && $key !== 'coins') continue; // Hide empty resources except coins
            
            $iconPath = $config['icon'];
            // Ensure no leading slash for app_base_url if it's already a relative theme path
            if (strpos($iconPath, '/') === 0) $iconPath = substr($iconPath, 1);
        ?>
        <div class="resource-item" title="<?= htmlspecialchars($config['name']) ?>">
            <img src="<?= app_base_url($iconPath) ?>" alt="<?= $key ?>" class="res-icon">
            <span class="res-value"><?= number_format($value) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
:root {
    --hud-height: <?= $hudConfig['header_height'] ?>;
    --hud-icon-size: <?= $hudConfig['icon_size'] ?>;
    --hud-font-size: <?= $hudConfig['font_size'] ?>;
    --hud-gap: <?= $hudConfig['gap'] ?>;
}

.resource-hud-sticky {
    position: sticky;
    top: 0;
    z-index: 1001;
    background: rgba(15, 23, 42, 0.9); /* Modern Dark Blue/Slate */
    backdrop-filter: blur(8px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    height: var(--hud-height);
    display: flex;
    align-items: center;
    color: #f8fafc;
    font-family: 'Inter', sans-serif;
}

.container-flex {
    display: flex;
    justify-content: center;
    gap: var(--hud-gap);
    align-items: center;
    width: 100%;
}

.resource-item {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 2px 8px;
    border-radius: 6px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.resource-item:hover {
    background: rgba(255, 255, 255, 0.05);
    transform: translateY(-1px);
}

.res-icon {
    width: var(--hud-icon-size);
    height: var(--hud-icon-size);
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

.res-value {
    font-size: var(--hud-font-size);
    font-weight: 700;
    letter-spacing: 0.3px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

@media (max-width: 768px) {
    .resource-hud-sticky { height: auto; padding: 5px 0; }
    .container-flex { gap: 10px; flex-wrap: wrap; }
    .res-value { font-size: 11px; }
}
</style>
<?php 
    endif;
endif; ?>
