<?php
/**
 * Resource HUD - Sticky Top Bar for Coins and Materials
 */
if (isset($_SESSION['user_id'])):
    $db = \App\Core\Database::getInstance();
    $res = $db->findOne('user_resources', ['user_id' => $_SESSION['user_id']]);
    if ($res):
?>
<div class="resource-hud-sticky">
    <div class="container container-flex">
        <div class="resource-item" title="Gamenta Coins">
            <i class="fas fa-coins text-warning"></i>
            <span class="res-value"><?= number_format($res['coins']) ?></span>
        </div>
        <div class="resource-item" title="Bricks">
            <i class="fas fa-cubes text-danger"></i>
            <span class="res-value"><?= number_format($res['bricks']) ?></span>
        </div>
        <div class="resource-item" title="Steel">
            <i class="fas fa-bars text-secondary"></i>
            <span class="res-value"><?= number_format($res['steel']) ?></span>
        </div>
        <div class="resource-item" title="Cement">
            <i class="fas fa-box text-light"></i>
            <span class="res-value"><?= number_format($res['cement']) ?></span>
        </div>
        <div class="resource-item" title="Sand">
            <i class="fas fa-mountain text-info" style="color: #fce38a !important;"></i>
            <span class="res-value"><?= number_format($res['sand']) ?></span>
        </div>
        <div class="resource-item" title="Wood Logs">
            <i class="fas fa-tree text-success"></i>
            <span class="res-value"><?= number_format($res['wood_logs']) ?></span>
        </div>
        <div class="resource-item" title="Wood Planks">
            <i class="fas fa-scroll text-brown" style="color: #a0522d !important;"></i>
            <span class="res-value"><?= number_format($res['wood_planks']) ?></span>
        </div>
    </div>
</div>

<style>
.resource-hud-sticky {
    position: sticky;
    top: 0;
    z-index: 1001;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 8px 0;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
}
.container-flex {
    display: flex;
    justify-content: center;
    gap: 25px;
    align-items: center;
}
.resource-item {
    display: flex;
    align-items: center;
    gap: 8px;
    transition: transform 0.2s;
}
.resource-item:hover {
    transform: translateY(-2px);
}
.res-value {
    letter-spacing: 0.5px;
}
@media (max-width: 576px) {
    .container-flex { gap: 12px; font-size: 12px; }
}
</style>
<?php 
    endif;
endif; ?>
