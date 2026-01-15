<div class="min-h-screen bg-gray-900 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-gray-800 rounded-2xl p-8 border border-gray-700 shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Welcome, Engineer! 👷</h1>
            <p class="text-gray-400">Select your path to start customized quizzes.</p>
        </div>

        <form action="/quiz/setup/save" method="POST" class="space-y-6">
            <?= $this->csrfField() ?>

            <div>
                <label class="block text-gray-400 mb-2 text-sm font-semibold">Select Course</label>
                <select name="course_id" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="1">Civil Engineering</option>
                    <option value="2">Electrical Engineering</option>
                    <option value="3">Computer Engineering</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-400 mb-2 text-sm font-semibold">Education Level</label>
                <select name="edu_level_id" class="w-full bg-gray-900 border border-gray-700 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="1">Bachelor (License)</option>
                    <option value="2">Diploma (Sub-Engineer)</option>
                    <option value="3">TSLC (Asst. Sub-Engineer)</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-xl transition-all hover:scale-105 shadow-lg shadow-indigo-500/20">
                Start Learning 🚀
            </button>
        </form>
    </div>
</div>