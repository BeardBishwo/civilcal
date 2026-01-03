<div class="content-wrapper p-4">

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-4 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h2 class="fw-bold mb-1">Create Question</h2>
            <p class="mb-0 text-white-50">Add new content to your bank. Supports LaTeX math & Images.</p>
        </div>
    </div>

    <form id="questionForm" action="/admin/quiz/questions/store" method="POST">
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-uppercase small text-muted">Question Stem</label>
                            <div class="position-relative">
                                <textarea name="question" class="form-control form-control-lg border-0 bg-light" 
                                          rows="4" placeholder="Type your question here... (e.g. What is the density of steel?)" required></textarea>
                                
                                <div class="position-absolute bottom-0 end-0 p-2">
                                    <button type="button" class="btn btn-sm btn-white shadow-sm rounded-circle" title="Insert Image" onclick="MediaManager.open('q_img')"><i class="fas fa-image text-primary"></i></button>
                                    <button type="button" class="btn btn-sm btn-white shadow-sm rounded-circle" title="Insert Math" onclick="insertMath()"><i class="fas fa-square-root-alt text-danger"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-center bg-light p-2 rounded-pill mb-4" style="width: fit-content; margin: 0 auto;">
                            <input type="radio" class="btn-check" name="type" id="type_mcq" value="MCQ" checked onchange="toggleType('MCQ')">
                            <label class="btn btn-outline-primary border-0 rounded-pill px-4 fw-bold" for="type_mcq">
                                <i class="fas fa-list-ul me-2"></i> Multiple Choice
                            </label>

                            <input type="radio" class="btn-check" name="type" id="type_tf" value="TF" onchange="toggleType('TF')">
                            <label class="btn btn-outline-primary border-0 rounded-pill px-4 fw-bold" for="type_tf">
                                <i class="fas fa-check-circle me-2"></i> True / False
                            </label>
                        </div>

                        <div id="options_area">
                            </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-white fw-bold text-uppercase small text-muted border-0 pt-4">
                        Categorization
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Main Stream</label>
                            <select class="form-select bg-light border-0 fw-bold" name="syllabus_main_id" id="main_cat" onchange="filterSubCats()">
                                <?php foreach($mainCategories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($_SESSION['last_q_main_id']) && $_SESSION['last_q_main_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Topic</label>
                            <select class="form-select bg-light border-0" name="syllabus_node_id" id="sub_cat">
                                <option>-- Select Stream First --</option>
                                <?php foreach($subCategories as $sub): ?>
                                    <option value="<?= $sub['id'] ?>" data-parent="<?= $sub['parent_id'] ?>" 
                                        <?= (isset($_SESSION['last_q_sub_id']) && $_SESSION['last_q_sub_id'] == $sub['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sub['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <hr class="text-muted opacity-25">

                        <div class="mb-3">
                            <label class="form-label small fw-bold d-flex justify-content-between">
                                Difficulty Level <span class="badge bg-warning text-dark" id="diff_val">Medium</span>
                            </label>
                            <input type="range" class="form-range" min="1" max="3" step="1" id="difficulty" name="level" oninput="updateDiffLabel(this.value)">
                            <div class="d-flex justify-content-between small text-muted px-1">
                                <span>Easy</span>
                                <span>Medium</span>
                                <span>Hard</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-3 shadow-sm mt-2" 
                                style="background: #764ba2; border:none; letter-spacing: 0.5px;">
                            SAVE QUESTION
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // 1. Templates for the UI
    const templates = {
        // Standard 4 Options
        MCQ: `
            <div class="row g-3 animate__animated animate__fadeIn">
                ${[1, 2, 3, 4].map(n => `
                <div class="col-md-6">
                    <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                        <span class="input-group-text bg-white border-0 text-muted fw-bold ps-3">Option ${String.fromCharCode(64 + n)}</span>
                        <input type="text" name="option_${n}" class="form-control border-0 bg-white" placeholder="Answer..." required>
                        <div class="input-group-text bg-white border-0">
                            <input class="form-check-input" type="radio" name="correct_answer" value="${n}" required>
                        </div>
                    </div>
                </div>
                `).join('')}
                <div class="col-12 text-center mt-3">
                    <small class="text-muted"><i class="fas fa-info-circle"></i> Click the radio button next to the correct answer.</small>
                </div>
            </div>
        `,

        // Enterprise True/False Toggle
        TF: `
            <div class="p-5 bg-light rounded-3 text-center animate__animated animate__flipInX border border-dashed">
                <h5 class="text-muted fw-bold mb-4">Select the Correct Answer</h5>
                
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="correct_answer" id="btn_true" value="1" autocomplete="off">
                    <label class="btn btn-outline-success btn-lg px-5 py-3 fw-bold" for="btn_true">
                        <i class="fas fa-check me-2"></i> TRUE
                    </label>

                    <input type="radio" class="btn-check" name="correct_answer" id="btn_false" value="2" autocomplete="off">
                    <label class="btn btn-outline-danger btn-lg px-5 py-3 fw-bold" for="btn_false">
                        <i class="fas fa-times me-2"></i> FALSE
                    </label>
                </div>

                <input type="hidden" name="option_1" value="True">
                <input type="hidden" name="option_2" value="False">
                <input type="hidden" name="option_3" value="">
                <input type="hidden" name="option_4" value="">
            </div>
        `
    };

    // 2. Logic Controller
    function toggleType(type) {
        const container = document.getElementById('options_area');
        
        // Instant visual switch
        container.innerHTML = templates[type];
    }

    // 3. Helper: Difficulty Label Update
    function updateDiffLabel(val) {
        const labels = {1: 'Easy', 2: 'Medium', 3: 'Hard'};
        const colors = {1: 'success', 2: 'warning', 3: 'danger'};
        const badge = document.getElementById('diff_val');
        
        badge.innerText = labels[val];
        badge.className = `badge bg-${colors[val]} text-${val === '2' ? 'dark' : 'white'}`;
    }

    // 4. Smart Category Filter (Inherited from Phase 9)
    function filterSubCats() {
        const mainId = document.getElementById('main_cat').value;
        const subOptions = document.querySelectorAll('#sub_cat option');
        let firstVisible = null;
        
        subOptions.forEach(opt => {
            if (opt.value === "") return; // Skip placeholder
            
            if (opt.dataset.parent == mainId) {
                opt.style.display = 'block';
                if (!firstVisible) firstVisible = opt;
            } else {
                opt.style.display = 'none';
            }
        });

        // Auto-select first valid option
        if (firstVisible) {
            document.getElementById('sub_cat').value = firstVisible.value;
        } else {
            document.getElementById('sub_cat').value = "";
        }
    }

    // Initialize on Load
    document.addEventListener('DOMContentLoaded', () => {
        toggleType('MCQ');
        filterSubCats(); // Initialize filter
    });
</script>

<style>
    /* Premium UI Tweaks */
    .btn-check:checked + .btn-outline-primary {
        background-color: #6f42c1;
        color: white;
        border-color: #6f42c1;
        box-shadow: 0 4px 6px rgba(111, 66, 193, 0.3);
    }
    .form-control:focus {
        box-shadow: none;
        border: 2px solid #bba3e8 !important; /* Soft Purple Focus */
    }
</style>
