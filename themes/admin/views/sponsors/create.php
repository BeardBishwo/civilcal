<div class="max-w-3xl mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                Onboard New Partner
            </h1>
            <p class="text-gray-500 text-sm mt-1">Establish a strategic sponsorship connection.</p>
        </div>
        <a href="<?php echo app_base_url('admin/sponsors'); ?>" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:bg-gray-50 text-gray-600 shadow-sm flex items-center gap-2 transition-all">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white border border-gray-100 rounded-2xl shadow-xl p-8 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>

        <form method="POST" action="<?php echo app_base_url('admin/sponsors/store'); ?>" enctype="multipart/form-data" class="space-y-6 relative z-10">

            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Company Name -->
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Company Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-building"></i>
                            </div>
                            <input type="text" name="name" required placeholder="e.g. Acme Corporation"
                                class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-gray-400 focus:shadow-md">
                        </div>
                    </div>

                    <!-- Website -->
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Strategic Website</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-globe"></i>
                            </div>
                            <input type="url" name="website_url" placeholder="https://corporate.acme.com"
                                class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-gray-400 focus:shadow-md">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Contact Person -->
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Primary Contact</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <input type="text" name="contact_person" placeholder="Full name"
                                class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-gray-400 focus:shadow-md">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="group">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Corporate Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" name="contact_email" placeholder="contact@acme.com"
                                class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all placeholder-gray-400 focus:shadow-md">
                        </div>
                    </div>
                </div>

                <!-- Logo Upload -->
                <div class="group">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Brand Identity (Logo)</label>
                    <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:bg-gray-50 transition-colors cursor-pointer" onclick="document.getElementById('logoUpload').click()">
                        <i class="fas fa-cloud-upload-alt text-4xl text-indigo-400 mb-3"></i>
                        <p class="text-sm text-gray-600 font-medium">Click to select brand logo</p>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                        <input type="file" name="logo" id="logoUpload" class="hidden" accept="image/*" onchange="previewLogo(this)">
                    </div>
                    <div id="logoPreview" class="mt-4 hidden text-center">
                        <img src="" class="h-20 mx-auto rounded-lg shadow-sm border border-gray-100">
                        <button type="button" class="text-xs text-rose-500 font-bold mt-2 hover:underline" onclick="clearLogo()">Remove Logo</button>
                    </div>
                </div>
            </div>

            <!-- Action -->
            <div class="flex justify-end pt-8 border-t border-gray-100">
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-bold shadow-xl shadow-indigo-300 active:scale-95 transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    Finalize Onboarding <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewLogo(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreview');
                preview.classList.remove('hidden');
                preview.querySelector('img').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearLogo() {
        const input = document.getElementById('logoUpload');
        input.value = '';
        document.getElementById('logoPreview').classList.add('hidden');
    }
</script>