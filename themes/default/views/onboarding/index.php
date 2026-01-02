<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mx-auto px-4 py-12 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        
        <!-- Step 1: Identity -->
        <div id="step-identity" class="p-8 text-center">
            <div class="mb-6">
                <i class="fas fa-user-astronaut text-6xl text-blue-600 mb-4"></i>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Who are you?</h1>
                <p class="text-gray-500">Tell us about your current status to help us personalize your experience.</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <button onclick="selectIdentity('student')" class="identity-btn p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all text-center group">
                    <i class="fas fa-graduation-cap text-3xl text-gray-400 group-hover:text-blue-500 mb-2 block"></i>
                    <span class="font-semibold text-gray-700 group-hover:text-blue-600">Student</span>
                </button>
                <button onclick="selectIdentity('aspirant')" class="identity-btn p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all text-center group">
                    <i class="fas fa-book-reader text-3xl text-gray-400 group-hover:text-blue-500 mb-2 block"></i>
                    <span class="font-semibold text-gray-700 group-hover:text-blue-600">PSC Aspirant</span>
                </button>
                <button onclick="selectIdentity('engineer')" class="identity-btn p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all text-center group">
                    <i class="fas fa-hard-hat text-3xl text-gray-400 group-hover:text-blue-500 mb-2 block"></i>
                    <span class="font-semibold text-gray-700 group-hover:text-blue-600">Site Engineer</span>
                </button>
                <button onclick="selectIdentity('professional')" class="identity-btn p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all text-center group">
                    <i class="fas fa-briefcase text-3xl text-gray-400 group-hover:text-blue-500 mb-2 block"></i>
                    <span class="font-semibold text-gray-700 group-hover:text-blue-600">Professional</span>
                </button>
            </div>
        </div>

        <!-- Step 2: Interests -->
        <div id="step-interests" class="hidden p-8">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">What interests you?</h1>
                <p class="text-gray-500">Select at least 3 topics you want to see in your feed.</p>
            </div>

            <div id="bubbles-container" class="flex flex-wrap justify-center gap-3 mb-8">
                <!-- Bubbles injected via JS -->
                <div class="animate-pulse flex space-x-2">
                    <div class="h-10 w-24 bg-gray-200 rounded-full"></div>
                    <div class="h-10 w-20 bg-gray-200 rounded-full"></div>
                    <div class="h-10 w-28 bg-gray-200 rounded-full"></div>
                </div>
            </div>

            <div class="text-center">
                <button onclick="saveInterests()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transform transition hover:scale-105">
                    Start Learning <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>

    </div>
</div>

<style>
    .bubble {
        cursor: pointer;
        padding: 10px 24px;
        border-radius: 9999px; /* Pill shape */
        border: 1px solid #e5e7eb;
        background-color: white;
        color: #374151;
        font-weight: 500;
        transition: all 0.2s ease;
        user-select: none;
    }
    .bubble:hover {
        background-color: #f3f4f6;
        border-color: #d1d5db;
    }
    .bubble.selected {
        background-color: #eff6ff;
        border-color: #2563eb;
        color: #2563eb;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1), 0 2px 4px -1px rgba(37, 99, 235, 0.06);
    }
    .identity-btn.selected {
        border-color: #2563eb;
        background-color: #eff6ff;
    }
    .identity-btn.selected i {
        color: #2563eb;
    }
    .identity-btn.selected span {
        color: #1d4ed8;
    }
</style>

<script>
    let selectedIdentity = null;
    let selectedCategories = new Set();
    const categoriesDiv = document.getElementById('bubbles-container');

    function selectIdentity(id) {
        selectedIdentity = id;
        
        // Visual Feedback
        document.querySelectorAll('.identity-btn').forEach(btn => btn.classList.remove('selected'));
        event.currentTarget.classList.add('selected');

        // Transition to next step
        setTimeout(() => {
            document.getElementById('step-identity').classList.add('hidden');
            document.getElementById('step-interests').classList.remove('hidden');
            loadCategories();
        }, 300);
    }

    function loadCategories() {
        fetch('/api/interests/categories')
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    categoriesDiv.innerHTML = '';
                    data.categories.forEach(cat => {
                        const btn = document.createElement('div');
                        btn.className = 'bubble';
                        btn.textContent = cat.name; // Could add icon here too
                        btn.onclick = () => toggleCategory(btn, cat.id);
                        categoriesDiv.appendChild(btn);
                    });
                }
            });
    }

    function toggleCategory(el, id) {
        if (selectedCategories.has(id)) {
            selectedCategories.delete(id);
            el.classList.remove('selected');
        } else {
            selectedCategories.add(id);
            el.classList.add('selected');
        }
    }

    function saveInterests() {
        if (selectedCategories.size === 0) {
            alert('Please select at least one interest.');
            return;
        }

        fetch('/api/interests/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                identity: selectedIdentity,
                categories: Array.from(selectedCategories)
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/dashboard';
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
