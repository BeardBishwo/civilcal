<?php
ob_start();
?>

<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Modules & Categories</h1>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Manage calculation modules and categories</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button style="background: #4361ee; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-plus-circle"></i>
                <span>Add Module</span>
            </button>
            <button style="background: transparent; color: #9ca3af; border: 1px solid rgba(102, 126, 234, 0.2); padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-upload"></i>
                <span>Import Module</span>
            </button>
        </div>
    </div>
</div>

<!-- Modules Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <?php foreach ($modules as $module): ?>
        <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #4cc9f0; margin: 0;"><?php echo htmlspecialchars($module['name']); ?></h3>
                <span style="background: #4361ee; color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;">v<?php echo $module['version']; ?></span>
            </div>
            <p style="color: #9ca3af; margin: 0 0 1.5rem 0;"><?php echo htmlspecialchars($module['description']); ?></p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="text-align: center; padding: 1rem; background: rgba(67, 97, 238, 0.1); border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #4cc9f0;"><?php echo $module['calculators_count']; ?></div>
                    <div style="color: #9ca3af; font-size: 0.875rem;">Calculators</div>
                </div>
                <div style="text-align: center; padding: 1rem; background: rgba(<?php echo ($module['status'] == 'active' ? '52, 211, 153' : '156, 163, 175'); ?>, 0.1); border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: <?php echo ($module['status'] == 'active' ? '#34d399' : '#9ca3af'); ?>;"><?php echo ucfirst($module['status']); ?></div>
                    <div style="color: #9ca3af; font-size: 0.875rem;">Status</div>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button style="flex: 1; background: transparent; color: #4cc9f0; border: 1px solid rgba(102, 126, 234, 0.2); padding: 0.5rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-gear"></i>
                    <span>Settings</span>
                </button>
                <button style="flex: 1; background: <?php echo ($module['status'] == 'active' ? 'transparent' : '#34d399'); ?>; color: <?php echo ($module['status'] == 'active' ? '#fbbf24' : 'white'); ?>; border: 1px solid rgba(<?php echo ($module['status'] == 'active' ? '251, 191, 36' : '52, 211, 153'); ?>, 0.2); padding: 0.5rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas <?php echo ($module['status'] == 'active' ? 'fa-pause' : 'fa-play'); ?>"></i>
                    <span><?php echo ($module['status'] == 'active' ? 'Disable' : 'Enable'); ?></span>
                </button>
                <button style="flex: 1; background: transparent; color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.5rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fas fa-trash"></i>
                    <span>Remove</span>
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Categories Management -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.25rem; font-weight: 600; color: #f9fafb; margin: 0;">Categories Management</h2>
    </div>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <th style="text-align: left; padding: 0.75rem 1rem; color: #9ca3af; font-weight: 600;">Category Name</th>
                    <th style="text-align: left; padding: 0.75rem 1rem; color: #9ca3af; font-weight: 600;">Slug</th>
                    <th style="text-align: left; padding: 0.75rem 1rem; color: #9ca3af; font-weight: 600;">Modules Count</th>
                    <th style="text-align: left; padding: 0.75rem 1rem; color: #9ca3af; font-weight: 600;">Description</th>
                    <th style="text-align: left; padding: 0.75rem 1rem; color: #9ca3af; font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $slug => $name): 
                    $count = array_reduce($modules, function($carry, $item) use ($slug) {
                        return $carry + ($item['category'] === $slug ? 1 : 0);
                    }, 0);
                ?>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                    <td style="padding: 0.75rem 1rem; color: #f9fafb;"><?php echo htmlspecialchars($name); ?></td>
                    <td style="padding: 0.75rem 1rem; color: #9ca3af;"><code><?php echo $slug; ?></code></td>
                    <td style="padding: 0.75rem 1rem;">
                        <span style="background: #4361ee; color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;"><?php echo $count; ?> modules</span>
                    </td>
                    <td style="padding: 0.75rem 1rem; color: #9ca3af;">Automatically created category</td>
                    <td style="padding: 0.75rem 1rem;">
                        <button style="background: transparent; color: #4cc9f0; border: 1px solid rgba(102, 126, 234, 0.2); padding: 0.25rem 0.5rem; border-radius: 4px; cursor: pointer; margin-right: 0.5rem;">
                            <i class="fas fa-pencil"></i>
                        </button>
                        <button style="background: transparent; color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.25rem 0.5rem; border-radius: 4px; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Add Category Form -->
    <div style="margin-top: 2rem; padding: 1.5rem; border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; background: rgba(255, 255, 255, 0.02);">
        <h3 style="font-size: 1rem; font-weight: 600; color: #f9fafb; margin: 0 0 1rem 0;">Add New Category</h3>
        <form style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <input type="text" placeholder="Category Name" style="background: rgba(10, 14, 39, 0.5); border: 1px solid rgba(102, 126, 234, 0.2); color: #f9fafb; padding: 0.5rem; border-radius: 6px;" required>
            <input type="text" placeholder="Slug (auto-generated)" style="background: rgba(10, 14, 39, 0.5); border: 1px solid rgba(102, 126, 234, 0.2); color: #f9fafb; padding: 0.5rem; border-radius: 6px;" required>
            <input type="text" placeholder="Description" style="background: rgba(10, 14, 39, 0.5); border: 1px solid rgba(102, 126, 234, 0.2); color: #f9fafb; padding: 0.5rem; border-radius: 6px;">
            <button type="submit" style="background: #4361ee; color: white; border: none; padding: 0.5rem; border-radius: 6px; cursor: pointer;">Add</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>