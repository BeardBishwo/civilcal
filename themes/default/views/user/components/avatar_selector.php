<?php
// Civil Identity System - Avatar Selector Component
// Requires: $user (array), $db (Database instance)

$user_level = $user['level'] ?? 1;
$user_coins = $user['coins'] ?? 0;
$user_xp = $user['total_xp'] ?? 0;

// Get Owned Items
$wardrobe = $db->query("SELECT item_key FROM user_wardrobe WHERE user_id = ?", [$user['id']])->fetchAll(PDO::FETCH_COLUMN);

// DEFINING THE ASSETS
$avatars = [
    'starters' => [
        ['id' => 'avatar_starter_mascot', 'name' => 'Site Bot', 'img' => 'avatar_starter_mascot.webp', 'req_level' => 0],
        ['id' => 'avatar_starter_rookie_male', 'name' => 'Rookie (M)', 'img' => 'avatar_starter_rookie_male.webp', 'req_level' => 0],
        ['id' => 'avatar_starter_rookie_female', 'name' => 'Rookie (F)', 'img' => 'avatar_starter_rookie_female.webp', 'req_level' => 0],
    ],
    'pros' => [
        ['id' => 'avatar_rank_03_supervisor', 'name' => 'Supervisor', 'img' => 'avatar_rank_03_supervisor.webp', 'req_xp' => 2000, 'rank' => 'Supervisor'],
        ['id' => 'avatar_rank_05_senior', 'name' => 'Senior', 'img' => 'avatar_rank_05_senior.webp', 'req_xp' => 15000, 'rank' => 'Senior Engineer'],
        ['id' => 'avatar_rank_07_chief', 'name' => 'The Chief', 'img' => 'avatar_rank_07_chief.webp', 'req_xp' => 100000, 'rank' => 'Chief Engineer'],
    ]
];

$frames = [
    ['id' => 'frame_hazard', 'name' => 'Hazard', 'img' => 'frame_hazard.webp', 'price' => 50],
    ['id' => 'frame_blueprint', 'name' => 'Blueprint', 'img' => 'frame_blueprint.webp', 'price' => 200],
    ['id' => 'frame_gold', 'name' => 'Legendary', 'img' => 'frame_gold.webp', 'price' => 1000],
];
?>

<div class="wardrobe-container bg-white rounded-lg shadow-sm p-6">
    
    <h3 class="text-xl font-bold mb-6 text-gray-800 border-b pb-2">Civil Identity</h3>
    
    <!-- Avatars Section -->
    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Select Avatar</h4>
    <div class="grid grid-cols-3 sm:grid-cols-4 gap-6 mb-8">
        <?php foreach (array_merge($avatars['starters'], $avatars['pros']) as $avi): ?>
            <?php 
                // Check Lock Status
                $is_locked = false;
                if (isset($avi['req_xp']) && $user_xp < $avi['req_xp']) {
                    $is_locked = true;
                }
                
                // Check Ownership (Should be redundant with logic, but safe)
                if (!$is_locked && isset($avi['req_xp']) && !in_array($avi['id'], $wardrobe)) {
                    // Logic issue fallback: if they have XP but not item in DB yet
                    $is_locked = true; 
                }

                $isSelected = ($user['avatar_id'] === $avi['id']);
                $opacity = $is_locked ? 'opacity-50 grayscale cursor-not-allowed' : 'cursor-pointer hover:scale-105 transition duration-200';
                $border = $isSelected ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200';
            ?>
            <div class="relative group text-center <?php echo $opacity; ?>" 
                 onclick="<?php echo $is_locked ? '' : "updateIdentity('avatar', '{$avi['id']}')" ?>">
                
                <div class="relative inline-block">
                    <img src="<?php echo app_url('themes/default/assets/resources/avatars/' . $avi['img']); ?>" 
                         class="w-20 h-20 mx-auto rounded-full border-4 <?php echo $border; ?> object-cover bg-gray-100">
                    
                    <?php if ($isSelected): ?>
                        <div class="absolute -top-1 -right-1 bg-blue-500 text-white rounded-full p-1 w-6 h-6 flex items-center justify-center">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($is_locked): ?>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="bg-black/80 text-white text-[10px] px-2 py-1 rounded backdrop-blur-sm">
                            <i class="fas fa-lock mr-1"></i> <?php echo isset($avi['rank']) ? $avi['rank'] : 'Locked'; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <p class="text-xs mt-2 font-medium text-gray-600"><?php echo $avi['name']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Frames Section -->
    <div class="flex justify-between items-center mb-4 mt-8 border-t pt-6">
        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Cosmetic Frames</h4>
        <span class="text-yellow-600 font-bold bg-yellow-50 px-3 py-1 rounded-full text-sm border border-yellow-200">
            <i class="fas fa-coins mr-1"></i> <?php echo number_format($user_coins); ?> Coins
        </span>
    </div>

    <div class="grid grid-cols-3 sm:grid-cols-4 gap-6">
        <!-- Default (No Frame) -->
        <div class="text-center cursor-pointer" onclick="updateIdentity('frame', 'default')">
             <div class="w-20 h-20 mx-auto rounded-full border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition">
                <span class="text-xs text-gray-400">None</span>
             </div>
             <p class="text-xs mt-2 font-medium text-gray-600">No Frame</p>
        </div>

        <?php foreach ($frames as $frame): ?>
            <?php 
                $owned = in_array($frame['id'], $wardrobe);
                $can_afford = $user_coins >= $frame['price'];
                $isSelected = ($user['frame_id'] === $frame['id']);
            ?>
            <div class="text-center relative">
                <div class="relative w-20 h-20 mx-auto mb-3">
                    <!-- Preview Base -->
                    <img src="<?php echo app_url('themes/default/assets/resources/avatars/' . ($user['avatar_id'] ? $user['avatar_id'] . '.webp' : 'avatar_starter_mascot.webp')); ?>" 
                         class="absolute w-16 h-16 top-2 left-2 rounded-full z-0 opacity-40 grayscale">
                    <!-- Frame Overlay -->
                    <img src="<?php echo app_url('themes/default/assets/resources/avatars/' . $frame['img']); ?>" 
                         class="absolute w-20 h-20 top-0 left-0 z-10 drop-shadow-md">
                    
                    <?php if ($isSelected): ?>
                        <div class="absolute -top-1 -right-1 bg-blue-500 text-white rounded-full p-1 w-5 h-5 flex items-center justify-center z-20">
                            <i class="fas fa-check text-[10px]"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($owned): ?>
                    <button onclick="updateIdentity('frame', '<?php echo $frame['id']; ?>')" 
                            class="w-full text-[10px] px-2 py-1.5 rounded-md font-medium transition <?php echo $isSelected ? 'bg-gray-100 text-gray-500 cursor-default' : 'bg-green-100 text-green-700 hover:bg-green-200'; ?>">
                        <?php echo $isSelected ? 'Equipped' : 'Equip'; ?>
                    </button>
                <?php else: ?>
                    <button onclick="buyFrame('<?php echo $frame['id']; ?>', <?php echo $frame['price']; ?>)"
                            class="w-full text-[10px] px-2 py-1.5 rounded-md font-bold transition shadow-sm <?php echo $can_afford ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed'; ?>"
                            <?php echo $can_afford ? '' : 'disabled'; ?>>
                        Buy <?php echo $frame['price']; ?>©
                    </button>
                <?php endif; ?>
                
                <p class="text-xs mt-1.5 font-medium text-gray-600"><?php echo $frame['name']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<script>
async function updateIdentity(type, key) {
    const btn = event.currentTarget;
    const originalOpacity = btn.style.opacity;
    btn.style.opacity = '0.5';

    try {
        const formData = new FormData();
        formData.append('type', type);
        formData.append('key', key);
        formData.append('csrf_token', '<?php echo $_SESSION['csrf_token'] ?? ''; ?>');

        const response = await fetch('<?php echo app_url('api/user/identity/update'); ?>', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            // Toast or just reload to see changes
             // Assuming you have a toast library, else simple reload
            window.location.reload(); 
        } else {
            alert(result.message);
            btn.style.opacity = originalOpacity;
        }
    } catch (e) {
        console.error(e);
        alert('Failed to update identity');
        btn.style.opacity = originalOpacity;
    }
}

async function buyFrame(key, price) {
    if (!confirm(`Buy this frame for ${price} Coins?`)) return;

    try {
        const formData = new FormData();
        formData.append('type', 'frame');
        formData.append('key', key);
        formData.append('csrf_token', '<?php echo $_SESSION['csrf_token'] ?? ''; ?>');

        const response = await fetch('<?php echo app_url('api/user/identity/buy'); ?>', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            window.location.reload();
        } else {
            alert(result.message);
        }
    } catch (e) {
        alert('Transaction failed');
    }
}
</script>
