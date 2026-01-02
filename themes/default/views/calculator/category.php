<?php
$page_title = $title . ' - ' . \App\Services\SettingsService::get('site_name', 'Civil Cal');
?>

<div class="container" style="padding: 40px 20px;">
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: #1e293b;"><?php echo htmlspecialchars($title); ?></h1>
        <p style="color: #64748b; font-size: 1.1rem;">Explore our specialized professional engineering tools for the <?php echo htmlspecialchars($category); ?> category.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
        <?php foreach ($calculators as $calc): ?>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 30px; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; position: relative;" 
             onclick="window.location.href='<?php echo app_base_url('/calculators/' . $category . '/' . $calc['calculator_id'] . '/protected'); ?>'"
             onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.05)'"
             onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
            
            <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #6366f1; margin-bottom: 20px;">
                <i class="fas <?php echo $calc['icon'] ?? 'fa-calculator'; ?>"></i>
            </div>
            
            <h3 style="font-weight: 800; color: #1e293b; margin-bottom: 10px;"><?php echo htmlspecialchars($calc['name']); ?></h3>
            <p style="color: #64748b; font-size: 0.9rem; line-height: 1.6;"><?php echo htmlspecialchars($calc['description']); ?></p>
            
            <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #6366f1;">
                    v<?php echo $calc['version'] ?? '1.0'; ?>
                </span>
                <i class="fas fa-arrow-right" style="color: #cbd5e1;"></i>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
