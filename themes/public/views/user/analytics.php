<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Performance Analytics</h1>
            <a href="<?php echo app_base_url('/profile'); ?>" class="text-indigo-600 font-medium hover:underline flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Overall Progress -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Exam Performance History</h2>
                <canvas id="performanceChart" height="250"></canvas>
            </div>

            <!-- Subject Mastery -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                 <h2 class="text-lg font-bold text-gray-900 mb-6">Subject Mastery</h2>
                 <canvas id="masteryChart" height="250"></canvas>
            </div>

            <!-- Key Metrics -->
            <div class="lg:col-span-2 bg-indigo-900 rounded-2xl shadow-lg p-8 text-white">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-indigo-800">
                    <div class="p-4">
                        <div class="text-4xl font-bold mb-1">Top 10%</div>
                        <div class="text-indigo-300 text-sm uppercase tracking-wide">Global Ranking</div>
                    </div>
                     <div class="p-4">
                        <div class="text-4xl font-bold mb-1">85%</div>
                        <div class="text-indigo-300 text-sm uppercase tracking-wide">Average Score</div>
                    </div>
                     <div class="p-4">
                        <div class="text-4xl font-bold mb-1">12</div>
                        <div class="text-indigo-300 text-sm uppercase tracking-wide">Exams Completed</div>
                    </div>
                     <div class="p-4">
                        <div class="text-4xl font-bold mb-1">3.5h</div>
                        <div class="text-indigo-300 text-sm uppercase tracking-wide">Time Spent</div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dummy Data for Demonstration
    
    // Performance Chart (Line JS)
    const ctx1 = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Exam 1', 'Exam 2', 'Exam 3', 'Exam 4', 'Exam 5'],
            datasets: [{
                label: 'Score (%)',
                data: [65, 72, 68, 85, 90],
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });

    // Mastery Chart (Radar or Bar)
    const ctx2 = document.getElementById('masteryChart').getContext('2d');
    new Chart(ctx2, {
        type: 'radar',
        data: {
            labels: ['Math', 'Science', 'English', 'History', 'Logic'],
            datasets: [{
                label: 'Mastery Level',
                data: [80, 65, 90, 75, 85],
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.2)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: { r: { min: 0, max: 100 } }
        }
    });
});
</script>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
