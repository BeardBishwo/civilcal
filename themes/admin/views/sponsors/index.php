<?php

/**
 * SPONSORSHIPS MANAGEMENT INTERFACE (PREMIUM LIGHT)
 * Clean, High-Contrast design.
 */

// Data extraction
$page_title = 'Sponsorship Management';
$sponsors = $sponsors ?? [];
$totalSponsors = count($sponsors);
$activeSponsors = count(array_filter($sponsors, fn($s) => ($s['status'] ?? 'active') === 'active'));
?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                Sponsorships
            </h1>
            <p class="text-gray-500 text-sm mt-1">Manage strategic B2B partners and campaigns.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?php echo app_base_url('admin/sponsors/create'); ?>" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-200 active:scale-95 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> New Partner
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i class="fas fa-handshake text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900"><?php echo $totalSponsors; ?></div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Partners</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900"><?php echo $activeSponsors; ?></div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Active Partners</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <i class="fas fa-bullhorn text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">--</div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Live Campaigns</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 group cursor-pointer hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div>
                <div class="text-sm font-bold text-gray-900">Analytics</div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide">View Report <i class="fas fa-arrow-right ml-1"></i></div>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-list text-indigo-500"></i> Partner List
            </h3>
            <!-- Simple Filter -->
            <div class="flex gap-2">
                <input type="text" placeholder="Search partners..." class="bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-sm outline-none focus:border-indigo-500 transition-all w-64">
                <button class="bg-white border border-gray-200 p-1.5 rounded-lg text-gray-500 hover:text-indigo-600 hover:border-indigo-200 transition-all">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </div>

        <?php if (empty($sponsors)): ?>
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-handshake-slash text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">No partners found</h3>
                <p class="text-gray-500 mt-1 mb-6">Get started by onboarding your first strategic partner.</p>
                <a href="<?php echo app_base_url('admin/sponsors/create'); ?>" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                    <i class="fas fa-plus mr-2"></i> Onboard Partner
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Company</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($sponsors as $sponsor): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg overflow-hidden border border-indigo-100">
                                            <?php if (!empty($sponsor['logo_path'])): ?>
                                                <img src="<?= app_base_url('storage/uploads/admin/logos/' . $sponsor['logo_path']) ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <?= strtoupper(substr($sponsor['name'], 0, 1)) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($sponsor['name']) ?></div>
                                            <div class="text-xs text-gray-500"><?= htmlspecialchars($sponsor['website_url'] ?? '') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($sponsor['contact_person'] ?? 'N/A') ?></div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($sponsor['contact_email'] ?? '') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <?= ucfirst($sponsor['status'] ?? 'active') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?php echo app_base_url('admin/sponsors/campaign/' . $sponsor['id']); ?>" class="text-amber-500 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 p-2 rounded-lg transition-all" title="Launch Campaign">
                                            <i class="fas fa-rocket"></i>
                                        </a>
                                        <button class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-all" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 p-2 rounded-lg transition-all" title="Delete">
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