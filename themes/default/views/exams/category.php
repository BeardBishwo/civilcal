<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="min-h-screen bg-gray-50 pt-20 pb-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="<?php echo app_base_url('exams'); ?>" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                        <span class="ml-4 text-gray-500 font-medium"><?php echo htmlspecialchars($category['title']); ?></span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                <?php echo htmlspecialchars($category['title']); ?>
            </h1>
            <p class="mt-4 text-lg text-gray-500">
                Select a mode to begin your preparation
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            
            <!-- Practice Mode -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-transparent hover:border-indigo-500 transition overflow-hidden group relative">
                <div class="p-8">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center text-green-600 mb-6">
                        <i class="fas fa-book-reader text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Practice Mode</h3>
                    <p class="text-gray-500 mb-6">
                        Learn at your own pace with immediate answers and detailed explanations.
                    </p>
                    <ul class="space-y-3 mb-8 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Instant Feedback</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> No Time Limit</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> 20 Questions / Set</li>
                    </ul>
                    <button onclick="startExam('practice')" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition transform group-hover:scale-[1.02]">
                        Start Practice
                    </button>
                    <!-- Loading Spinner (Hidden) -->
                     <div id="loading-practice" class="hidden absolute inset-0 bg-white/80 flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin text-3xl text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <!-- Mock Test Mode -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-transparent hover:border-indigo-500 transition overflow-hidden group relative">
                <div class="p-8">
                    <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center text-red-600 mb-6">
                        <i class="fas fa-stopwatch text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Mock Test</h3>
                    <p class="text-gray-500 mb-6">
                        Simulate the real exam environment with a timer and final scoring.
                    </p>
                    <ul class="space-y-3 mb-8 text-sm text-gray-600">
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Timed (45 Mins)</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> Result & Scorecard</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i> 50 Questions / Set</li>
                    </ul>
                    <button onclick="startExam('mock')" class="w-full bg-gray-900 text-white py-3 rounded-xl font-bold hover:bg-gray-800 transition transform group-hover:scale-[1.02]">
                        Start Mock Test
                    </button>
                     <!-- Loading Spinner (Hidden) -->
                     <div id="loading-mock" class="hidden absolute inset-0 bg-white/80 flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin text-3xl text-gray-900"></i>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</div>

<form id="startForm" action="<?php echo app_base_url('exams/start'); ?>" method="POST" style="display:none;">
    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
    <input type="hidden" name="mode" id="modeInput">
</form>

<script>
function startExam(mode) {
    document.getElementById('loading-' + mode).classList.remove('hidden');
    
    // Use fetch for better UX (no full page reload)
    const formData = new FormData();
    formData.append('category_id', '<?php echo $category['id']; ?>');
    formData.append('mode', mode);

    fetch('<?php echo app_base_url('exams/start'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            alert('Failed to start exam');
            document.getElementById('loading-' + mode).classList.add('hidden');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Network error');
        document.getElementById('loading-' + mode).classList.add('hidden');
    });
}
</script>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>
