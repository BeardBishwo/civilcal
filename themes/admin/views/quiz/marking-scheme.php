<?php
/**
 * MARKING SCHEME INTERFACE
 * Manual grading for theory questions
 */
$theoryAnswers = $theoryAnswers ?? [];
$attempt = $attempt ?? [];
$exam = $exam ?? [];
$student = $student ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-clipboard-check"></i>
                    <h1>Mark Theory Questions</h1>
                </div>
                <div class="header-subtitle">
                    <?php echo htmlspecialchars($student['username'] ?? 'Student'); ?> • 
                    <?php echo htmlspecialchars($exam['title'] ?? 'Exam'); ?> • 
                    <?php echo $unmarkedCount; ?> Unmarked
                </div>
            </div>
            <div class="header-actions" style="display:flex; gap:10px;">
                <div class="stat-pill">
                    <span class="label">TOTAL</span>
                    <span class="value"><?php echo $totalMarks; ?></span>
                </div>
                <div class="stat-pill success">
                    <span class="label">AWARDED</span>
                    <span class="value"><?php echo $awardedMarks; ?></span>
                </div>
                <button onclick="bulkSave()" class="btn-create-premium" style="border:none;">
                    <i class="fas fa-save"></i> SAVE ALL
                </button>
            </div>
        </div>

        <!-- Marking Interface -->
        <div class="pages-content" style="padding: 2rem;">
            
            <?php if (empty($theoryAnswers)): ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>No Theory Questions</h3>
                    <p>This exam has no theory questions to mark</p>
                </div>
            <?php else: ?>
                
                <?php foreach ($theoryAnswers as $index => $answer): ?>
                    <?php 
                    $content = json_decode($answer['content'], true);
                    $questionText = $content['text'] ?? '';
                    $modelAnswer = $answer['answer_explanation'] ?? '';
                    $studentAnswer = $answer['answer_text'] ?? '';
                    $currentMarks = $answer['marks_awarded'];
                    $maxMarks = $answer['allocated_marks'];
                    ?>
                    
                    <div class="marking-card" data-answer-id="<?php echo $answer['id']; ?>">
                        <!-- Question Header -->
                        <div class="marking-header">
                            <div class="question-number">
                                <span class="number-badge">Q<?php echo $index + 1; ?></span>
                                <span class="type-badge <?php echo $answer['theory_type'] == 'short' ? 'short' : 'long'; ?>">
                                    <?php echo $answer['theory_type'] == 'short' ? 'Short Answer' : 'Long Answer'; ?>
                                </span>
                                <span class="marks-badge"><?php echo $maxMarks; ?> Marks</span>
                            </div>
                            <div class="marking-status">
                                <?php if ($currentMarks !== null): ?>
                                    <span class="status-marked">
                                        <i class="fas fa-check-circle"></i> Marked
                                    </span>
                                <?php else: ?>
                                    <span class="status-pending">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Question Text -->
                        <div class="question-section">
                            <h4><i class="fas fa-question-circle"></i> Question</h4>
                            <div class="question-text"><?php echo nl2br(htmlspecialchars($questionText)); ?></div>
                        </div>

                        <!-- Student Answer -->
                        <div class="answer-section student-answer">
                            <h4><i class="fas fa-user-edit"></i> Student's Answer</h4>
                            <div class="answer-text">
                                <?php if (!empty($studentAnswer)): ?>
                                    <?php echo nl2br(htmlspecialchars($studentAnswer)); ?>
                                    <div class="word-count">
                                        <?php echo str_word_count($studentAnswer); ?> words
                                    </div>
                                <?php else: ?>
                                    <em style="color: #94a3b8;">No answer provided</em>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Model Answer -->
                        <div class="answer-section model-answer">
                            <h4><i class="fas fa-book-open"></i> Model Answer & Marking Scheme</h4>
                            <div class="answer-text"><?php echo nl2br(htmlspecialchars($modelAnswer)); ?></div>
                        </div>

                        <!-- Marking Controls -->
                        <div class="marking-controls">
                            <div class="marks-input-group">
                                <label>Marks Awarded</label>
                                <input 
                                    type="number" 
                                    class="marks-input" 
                                    min="0" 
                                    max="<?php echo $maxMarks; ?>" 
                                    step="0.5"
                                    value="<?php echo $currentMarks ?? ''; ?>"
                                    placeholder="0"
                                    data-answer-id="<?php echo $answer['id']; ?>"
                                >
                                <span class="max-marks">/ <?php echo $maxMarks; ?></span>
                            </div>
                            <div class="feedback-group">
                                <label>Feedback (Optional)</label>
                                <textarea 
                                    class="feedback-input" 
                                    rows="2" 
                                    placeholder="Provide feedback to student..."
                                    data-answer-id="<?php echo $answer['id']; ?>"
                                ><?php echo htmlspecialchars($answer['feedback'] ?? ''); ?></textarea>
                            </div>
                            <button onclick="saveSingleMark(<?php echo $answer['id']; ?>)" class="btn-save-single">
                                <i class="fas fa-check"></i> Save
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const baseUrl = '<?php echo app_base_url(); ?>';

function saveSingleMark(answerId) {
    const card = document.querySelector(`[data-answer-id="${answerId}"]`);
    const marksInput = card.querySelector('.marks-input');
    const feedbackInput = card.querySelector('.feedback-input');
    
    const marks = parseFloat(marksInput.value);
    const feedback = feedbackInput.value;
    
    if (isNaN(marks)) {
        Swal.fire('Error', 'Please enter valid marks', 'error');
        return;
    }
    
    fetch(`${baseUrl}/admin/quiz/marking/save`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            answer_id: answerId,
            marks: marks,
            feedback: feedback
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            Swal.fire({
                icon: 'success',
                title: 'Saved!',
                timer: 1500,
                showConfirmButton: false
            });
            
            // Update status badge
            const statusDiv = card.querySelector('.marking-status');
            statusDiv.innerHTML = '<span class="status-marked"><i class="fas fa-check-circle"></i> Marked</span>';
        } else {
            Swal.fire('Error', d.message, 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error', 'Failed to save marks', 'error');
    });
}

function bulkSave() {
    const marks = {};
    let hasErrors = false;
    
    document.querySelectorAll('.marking-card').forEach(card => {
        const answerId = card.dataset.answerId;
        const marksInput = card.querySelector('.marks-input');
        const feedbackInput = card.querySelector('.feedback-input');
        
        const marksValue = parseFloat(marksInput.value);
        
        if (!isNaN(marksValue)) {
            marks[answerId] = {
                marks: marksValue,
                feedback: feedbackInput.value
            };
        }
    });
    
    if (Object.keys(marks).length === 0) {
        Swal.fire('Error', 'Please enter marks for at least one question', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Save All Marks?',
        text: `This will save marks for ${Object.keys(marks).length} questions.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Save All'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`${baseUrl}/admin/quiz/marking/bulk-save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    attempt_id: <?php echo $attempt['id']; ?>,
                    marks: marks
                })
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'All Saved!',
                        text: d.message,
                        timer: 2000
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', d.message, 'error');
                }
            });
        }
    });
}
</script>

<style>
.marking-card {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    transition: all 0.2s ease;
}

.marking-card:hover {
    border-color: #667eea;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
}

.marking-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
}

.question-number {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.number-badge {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1rem;
}

.type-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.type-badge.short {
    background: #dbeafe;
    color: #1e40af;
}

.type-badge.long {
    background: #fef3c7;
    color: #92400e;
}

.marks-badge {
    background: #f1f5f9;
    color: #475569;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-marked {
    color: #10b981;
    font-weight: 600;
}

.status-pending {
    color: #f59e0b;
    font-weight: 600;
}

.question-section, .answer-section {
    margin-bottom: 1.5rem;
}

.question-section h4, .answer-section h4 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.question-text, .answer-text {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #cbd5e1;
    line-height: 1.6;
}

.student-answer .answer-text {
    border-left-color: #667eea;
}

.model-answer .answer-text {
    border-left-color: #10b981;
}

.word-count {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: #94a3b8;
    text-align: right;
}

.marking-controls {
    display: grid;
    grid-template-columns: 200px 1fr auto;
    gap: 1rem;
    align-items: start;
    background: #f8fafc;
    padding: 1.25rem;
    border-radius: 8px;
}

.marks-input-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.marks-input-group label, .feedback-group label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
}

.marks-input {
    width: 100px;
    padding: 0.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 1.25rem;
    font-weight: 700;
    text-align: center;
}

.max-marks {
    font-size: 0.9rem;
    color: #94a3b8;
    margin-left: 0.5rem;
}

.feedback-input {
    width: 100%;
    padding: 0.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    resize: vertical;
}

.btn-save-single {
    background: #10b981;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-save-single:hover {
    background: #059669;
    transform: translateY(-1px);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #94a3b8;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .marking-controls {
        grid-template-columns: 1fr;
    }
}
</style>
