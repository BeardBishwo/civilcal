<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>

<!-- CDN Utilities -->
<script src="https://cdn.tailwindcss.com"></script>

<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    dark: '#000000',
                    surface: '#0a0a0a',
                    glass: 'rgba(255, 255, 255, 0.03)',
                    'glass-border': 'rgba(255, 255, 255, 0.1)',
                    accent: '#ffffff',
                    'accent-muted': '#cbd5e1',
                }
            }
        }
    }
</script>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    
    .table-premium thead {
        background: rgba(255, 255, 255, 0.03);
    }
    
    .table-premium tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        transition: all 0.2s ease;
    }
    
    .table-premium tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .breadcrumb-item-premium {
        @apply text-accent-muted/60 transition-colors hover:text-white;
    }
</style>

<div class="min-h-screen pt-12 pb-20 px-4 sm:px-6 lg:px-8 bg-dark text-white">
    <div class="max-w-7xl mx-auto">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 items-center space-x-2 text-sm font-medium">
            <a href="<?php echo app_base_url('/projects'); ?>" class="text-white/40 hover:text-white transition-colors">Projects</a>
            <i class="fas fa-chevron-right text-[10px] text-white/20"></i>
            <span class="text-white/80"><?php echo htmlspecialchars($project['name']); ?></span>
        </nav>

        <!-- Project Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 space-y-6 md:space-y-0">
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center">
                        <i class="fas fa-folder-open text-3xl text-white/60"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black tracking-tight text-white"><?php echo htmlspecialchars($project['name']); ?></h1>
                        <p class="text-accent-muted text-lg opacity-60 font-medium"><?php echo htmlspecialchars($project['description'] ?: 'Organized estimation workspace.'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="<?php echo app_base_url('/'); ?>" class="bg-white/5 border border-white/10 hover:bg-white/10 text-white px-6 py-3 rounded-2xl font-bold transition-all flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>New Calculation</span>
                </a>
            </div>
        </div>

        <!-- Calculations Card -->
        <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between bg-white/[0.01]">
                <h3 class="font-black tracking-wider uppercase text-xs text-white/40">Saved Nodes (<?php echo count($calculations); ?>)</h3>
            </div>

            <div class="overflow-x-auto">
                <?php if (empty($calculations)): ?>
                    <div class="py-32 flex flex-col items-center text-center">
                        <div class="w-20 h-20 mb-6 bg-white/5 rounded-full flex items-center justify-center border border-white/10">
                            <i class="fas fa-file-invoice text-2xl text-white/20"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-white/80">Workspace Empty</h3>
                        <p class="text-accent-muted/60 text-sm max-w-xs mx-auto mb-8">Ready to start? Save your results from any calculator to this project.</p>
                        <a href="<?php echo app_base_url('/'); ?>" class="text-xs font-black uppercase tracking-widest text-white/40 hover:text-white transition-colors border-b border-white/10 pb-1">Browse Tools</a>
                    </div>
                <?php else: ?>
                    <table class="w-full text-left table-premium">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-white/30">
                                <th class="px-8 py-5">Node Category</th>
                                <th class="px-8 py-5">Calculation ID</th>
                                <th class="px-8 py-5 text-right">Added On</th>
                                <th class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php foreach ($calculations as $calc): ?>
                                <tr class="group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center group-hover:border-white/20 transition-all">
                                                <i class="fas fa-calculator text-white/40 group-hover:text-white/80 transition-colors"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-white"><?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $calc['calculator_type']))); ?></div>
                                                <div class="text-[10px] font-bold text-white/20 uppercase tracking-tighter">System Output Node</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <code class="bg-white/5 px-3 py-1.5 rounded-lg text-xs font-mono text-white/40 border border-white/5">#<?php echo $calc['id']; ?></code>
                                    </td>
                                    <td class="px-8 py-6 text-right font-medium text-white/40">
                                        <?php echo date('M d, Y', strtotime($calc['created_at'])); ?>
                                        <div class="text-[10px] text-white/20"><?php echo date('h:i A', strtotime($calc['created_at'])); ?></div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <button 
                                            onclick="alert('Loading estimation dataset...')"
                                            class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-white/5 border border-white/5 hover:bg-white text-white hover:text-black transition-all font-bold text-xs"
                                        >
                                            <i class="fas fa-external-link-alt text-[10px]"></i>
                                            <span>Recall</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5">
                 <p class="text-[10px] font-bold text-white/20 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    CALCULATION NODES IN THIS PROJECT ARE SECURELY SYNCED TO YOUR ACCOUNT
                 </p>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
