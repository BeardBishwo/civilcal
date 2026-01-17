<div class="max-w-4xl mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-amber-600 to-orange-600">
                Launch Campaign
            </h1>
            <p class="text-gray-500 text-sm mt-1">Strategic placement for <span class="font-bold text-gray-800"><?php echo htmlspecialchars($sponsor['name']); ?></span></p>
        </div>
        <a href="<?php echo app_base_url('admin/sponsors'); ?>" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:bg-gray-50 text-gray-600 shadow-sm flex items-center gap-2 transition-all">
            <i class="fas fa-arrow-left"></i> Cancel
        </a>
    </div>

    <form method="POST" action="<?php echo app_base_url('admin/sponsors/campaigns/create'); ?>" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <input type="hidden" name="sponsor_id" value="<?php echo $sponsor['id']; ?>">

        <!-- Left Column: Form -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-xl p-8 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500 to-orange-500"></div>

                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <div class="p-2 bg-amber-50 rounded-lg text-amber-600">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    Campaign Details
                </h3>

                <div class="space-y-6">
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Campaign Title</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-tag"></i>
                            </div>
                            <input type="text" name="title" required placeholder="e.g. Q4 Growth Acceleration"
                                class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all placeholder-gray-400">
                        </div>
                        <span class="text-xs text-gray-400 mt-1 ml-1">Internal reference name only</span>
                    </div>

                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Ad Headline (Visual Copy)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-heading"></i>
                            </div>
                            <input type="text" name="ad_text" id="adTextInput" oninput="updatePreview()" placeholder="Visual copy for the placement..."
                                class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all placeholder-gray-400 font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Target Calculator (Slug)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <input type="text" name="calculator_slug" required placeholder="e.g. concrete"
                                    class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all placeholder-gray-400">
                            </div>
                        </div>
                        <div class="group">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Priority Tier</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <input type="number" name="priority" value="0"
                                    class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all placeholder-gray-400">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white border border-gray-100 rounded-2xl shadow-xl p-8 relative overflow-hidden">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    Schedule & Limits
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" required value="<?= date('Y-m-d') ?>" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" required value="<?= date('Y-m-d', strtotime('+30 days')) ?>" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="group md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Impression Cap (Optional)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-eye"></i>
                            </div>
                            <input type="number" name="max_impressions" value="0" placeholder="0 = Unlimited"
                                class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder-gray-400">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-8 py-4 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white rounded-xl font-bold shadow-xl shadow-amber-200 active:scale-95 transition-all flex items-center gap-2 transform hover:-translate-y-0.5 text-lg">
                    <i class="fas fa-rocket"></i> Execute Campaign
                </button>
            </div>
        </div>

        <!-- Right Column: Preview -->
        <div class="lg:col-span-1 space-y-6">
            <div class="sticky top-8">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">Live Preview</h3>

                <!-- Mock Ad Unit -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm relative group cursor-pointer hover:border-amber-300 transition-all">
                    <div class="absolute top-0 right-0 bg-gray-100 text-gray-400 text-[10px] px-1.5 py-0.5 rounded-bl font-medium">AD</div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded bg-indigo-50 flex items-center justify-center text-indigo-700 font-bold text-sm border border-indigo-100 flex-shrink-0">
                            <?= !empty($sponsor['logo_path']) ? '<img src="' . app_base_url('storage/uploads/admin/logos/' . $sponsor['logo_path']) . '" class="w-full h-full object-cover rounded">' : strtoupper(substr($sponsor['name'], 0, 1)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-gray-400 mb-0.5"><?= htmlspecialchars($sponsor['name']) ?></div>
                            <div class="text-sm font-bold text-gray-900 leading-tight" id="adTextPreview">Your visual copy needs to be catchy...</div>
                            <div class="mt-2 text-xs font-medium text-indigo-600 flex items-center gap-1 group-hover:underline">
                                Learn More <i class="fas fa-arrow-right text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl p-4">
                    <h4 class="text-sm font-bold text-blue-700 mb-2"><i class="fas fa-info-circle"></i> Placement Info</h4>
                    <p class="text-xs text-blue-600 leading-relaxed">
                        Ad will be injected into the calculation results page for the <strong>Target Calculator</strong>. Priority 0 is default; higher numbers appear first.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function updatePreview() {
        const input = document.getElementById('adTextInput');
        const preview = document.getElementById('adTextPreview');
        preview.textContent = input.value || 'Your visual copy needs to be catchy...';
    }
</script>