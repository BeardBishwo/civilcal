<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        
        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-sitemap"></i>
                    <h1>Syllabus Manager</h1>
                </div>
                <div class="header-subtitle"><?php echo !empty($syllabus) ? count($syllabus) . ' streams configured' : 'Define your quiz hierarchy'; ?></div>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary btn-compact" data-toggle="modal" data-target="#modalCategory">
                    <i class="fas fa-plus"></i>
                    <span>Add Stream</span>
                </button>
            </div>
        </div>

        <!-- Syllabus Content -->
        <div class="pages-content">
            <div id="hierarchy-view" class="view-section active">
                <div class="table-container p-0 border-0 shadow-none">
                    <?php if (empty($syllabus)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-sitemap"></i>
                            <h3>No syllabus defined</h3>
                            <p>Start by adding a master stream (e.g., "Engineering", "General Knowledge").</p>
                            <button class="btn btn-primary btn-compact mt-3" data-toggle="modal" data-target="#modalCategory">
                                Create First Stream
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="accordion" id="syllabusAccordion">
                            <?php foreach ($syllabus as $catId => $cat): ?>
                                <div class="compact-card mb-3 border">
                                    <div class="compact-card-header bg-white" id="heading<?php echo $catId; ?>">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <button class="btn btn-link text-left font-weight-bold text-dark p-0 text-decoration-none d-flex align-items-center gap-3 flex-grow-1" type="button" data-toggle="collapse" data-target="#collapse<?php echo $catId; ?>">
                                                <div class="icon-box-sm bg-primary-subtle text-primary rounded-3">
                                                    <i class="fas fa-folder"></i>
                                                </div>
                                                <span class="fs-5"><?php echo htmlspecialchars($cat['name']); ?></span>
                                            </button>
                                            
                                            <div class="actions d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-success font-weight-bold" onclick="openSubjectModal(<?php echo $catId; ?>, '<?php echo addslashes($cat['name']); ?>')" title="Add Subject">
                                                    <i class="fas fa-plus me-1"></i> Subject
                                                </button>
                                                <div class="dropdown no-arrow">
                                                    <button class="btn btn-sm btn-light rounded-circle" type="button" data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                                        <a class="dropdown-item" href="javascript:void(0)" onclick="editCategory(<?php echo $catId; ?>, '<?php echo addslashes($cat['name']); ?>', '<?php echo $cat['slug']; ?>')">
                                                            <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i> Edit
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteItem('categories', <?php echo $catId; ?>)">
                                                            <i class="fas fa-trash fa-sm fa-fw mr-2 text-danger"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="collapse<?php echo $catId; ?>" class="collapse show" data-parent="#syllabusAccordion">
                                        <div class="card-body bg-light-subtle p-0">
                                            <!-- Subjects List -->
                                            <?php if (empty($cat['subjects'])): ?>
                                                <div class="p-4 text-center text-muted">
                                                    <small>No subjects in this stream.</small>
                                                </div>
                                            <?php else: ?>
                                                <div class="list-group list-group-flush">
                                                    <?php foreach ($cat['subjects'] as $subId => $sub): ?>
                                                        <div class="list-group-item bg-transparent border-bottom">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div class="w-100">
                                                                     <div class="d-flex align-items-center mb-2">
                                                                        <i class="fas fa-book text-success me-2"></i> 
                                                                        <strong class="text-dark me-2"><?php echo htmlspecialchars($sub['name']); ?></strong>
                                                                         <div class="dropdown no-arrow d-inline-block">
                                                                            <i class="fas fa-cog text-muted cursor-pointer hover-primary" data-toggle="dropdown" style="font-size: 0.8rem;"></i>
                                                                            <div class="dropdown-menu shadow animated--fade-in">
                                                                                <a class="dropdown-item" href="javascript:void(0)" onclick="editSubject(<?php echo $subId; ?>, '<?php echo addslashes($sub['name']); ?>', <?php echo $catId; ?>)">Edit Subject</a>
                                                                                <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteItem('subjects', <?php echo $subId; ?>)">Delete Subject</a>
                                                                            </div>
                                                                        </div>
                                                                     </div>

                                                                    <div class="ms-4 ps-1 d-flex flex-wrap align-items-center gap-2">
                                                                        <!-- Topics List -->
                                                                        <?php if (!empty($sub['topics'])): ?>
                                                                            <?php foreach ($sub['topics'] as $topic): ?>
                                                                                <span class="badge bg-white border text-secondary fw-normal d-flex align-items-center gap-2 px-2 py-1 rounded-pill">
                                                                                    <?php echo htmlspecialchars($topic['name']); ?>
                                                                                    <div class="d-flex gap-1 border-start ps-2 ms-1">
                                                                                        <i class="fas fa-pencil-alt text-muted cursor-pointer hover-primary" onclick="editTopic(<?php echo $topic['id']; ?>, '<?php echo addslashes($topic['name']); ?>', <?php echo $subId; ?>)" title="Edit" style="font-size: 10px;"></i>
                                                                                        <i class="fas fa-times text-muted cursor-pointer hover-danger" onclick="deleteItem('topics', <?php echo $topic['id']; ?>)" title="Remove" style="font-size: 10px;"></i>
                                                                                    </div>
                                                                                </span>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                        <button class="btn btn-sm btn-outline-secondary rounded-circle py-0 px-1" onclick="openTopicModal(<?php echo $subId; ?>, '<?php echo addslashes($sub['name']); ?>')" title="Add Topic" style="width: 24px; height: 24px;">
                                                                            <i class="fas fa-plus" style="font-size: 10px;"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals (Placed at bottom to confirm visibility control) -->
<div class="modal fade" id="modalCategory" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form id="formCategory" method="POST" action="<?php echo app_base_url('admin/quiz/categories/store'); ?>">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalCategoryTitle">Stream/Category Manager</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="cat_id">
                    <div class="form-group-compact">
                        <label>Stream Name</label>
                        <input type="text" class="form-control" name="name" id="cat_name" required placeholder="e.g. Civil Engineering">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSubject" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form id="formSubject" method="POST" action="<?php echo app_base_url('admin/quiz/subjects/store'); ?>">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalSubjectTitle">Subject Manager</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="sub_id">
                    <input type="hidden" name="category_id" id="sub_cat_id">
                    <div class="alert alert-success py-2 mb-3"><small>Adding to stream: <strong id="sub_cat_name_display"></strong></small></div>
                    <div class="form-group-compact">
                        <label>Subject Name</label>
                        <input type="text" class="form-control" name="name" id="sub_name" required placeholder="e.g. Structural Analysis">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTopic" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form id="formTopic" method="POST" action="<?php echo app_base_url('admin/quiz/topics/store'); ?>">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalTopicTitle">Topic Manager</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="topic_id">
                    <input type="hidden" name="subject_id" id="topic_sub_id">
                    <div class="alert alert-info py-2 mb-3"><small>Adding to subject: <strong id="topic_sub_name_display"></strong></small></div>
                    <div class="form-group-compact">
                        <label>Topic Name</label>
                        <input type="text" class="form-control" name="name" id="topic_name" required placeholder="e.g. Beam Deflection">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info btn-sm text-white">Save Topic</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Robust Premium Layout CSS */
.admin-wrapper-container {
    padding: 1.5rem;
    max-width: 1600px;
    margin: 0 auto;
}

.compact-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.header-title {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-title i {
    font-size: 1.5rem;
    color: var(--admin-primary);
}

.header-title h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--bs-gray-900, #212529);
    margin: 0;
}

.header-subtitle {
    font-size: 0.9rem;
    color: var(--bs-gray-600, #6c757d);
    margin-top: 0.25rem;
    margin-left: 2.5rem;
}

.compact-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    border: 1px solid rgba(0,0,0,0.05);
    overflow: hidden;
}

.compact-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    background: #fff;
}

.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1);
}

.icon-box-sm {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.hover-primary:hover { color: var(--admin-primary) !important; }
.hover-danger:hover { color: var(--admin-danger) !important; }

/* Modal Fixes */
.modal { display: none; } /* Default hidden */
.modal-backdrop { z-index: 1040; }
.modal { z-index: 1050; }
.modal.show { display: block; padding-right: 17px; background: rgba(0,0,0,0.5); }

.form-group-compact label {
    font-weight: 600;
    font-size: 0.85rem;
    color: #555;
    margin-bottom: 0.4rem;
}

.ms-4 { margin-left: 1.5rem !important; }
.me-2 { margin-right: 0.5rem !important; }
.gap-2 { gap: 0.5rem !important; }
.gap-3 { gap: 1rem !important; }
</style>

<script>
    // Handlers
    function openSubjectModal(catId, catName) {
        $('#sub_id').val('');
        $('#sub_cat_id').val(catId);
        $('#sub_cat_name_display').text(catName);
        $('#sub_name').val('');
        $('#formSubject').attr('action', '<?php echo app_base_url('admin/quiz/subjects/store'); ?>');
        $('#modalSubjectTitle').text('Add Subject');
        $('#modalSubject').modal('show');
    }

    function openTopicModal(subId, subName) {
        $('#topic_id').val('');
        $('#topic_sub_id').val(subId);
        $('#topic_sub_name_display').text(subName);
        $('#topic_name').val('');
        $('#formTopic').attr('action', '<?php echo app_base_url('admin/quiz/topics/store'); ?>');
        $('#modalTopicTitle').text('Add Topic');
        $('#modalTopic').modal('show');
    }

    function editCategory(id, name, slug) {
        $('#cat_id').val(id);
        $('#cat_name').val(name);
        $('#formCategory').attr('action', '<?php echo app_base_url('admin/quiz/categories/update/'); ?>' + id);
        $('#modalCategoryTitle').text('Edit Stream');
        $('#modalCategory').modal('show');
    }

    function editSubject(id, name, catId) {
        $('#sub_id').val(id);
        $('#sub_cat_id').val(catId);
        $('#sub_cat_name_display').text('Current Stream'); 
        $('#sub_name').val(name);
        $('#formSubject').attr('action', '<?php echo app_base_url('admin/quiz/subjects/update/'); ?>' + id);
        $('#modalSubjectTitle').text('Edit Subject');
        $('#modalSubject').modal('show');
    }

    function editTopic(id, name, subId) {
        $('#topic_id').val(id);
        $('#topic_sub_id').val(subId);
        $('#topic_sub_name_display').text('Current Subject');
        $('#topic_name').val(name);
        $('#formTopic').attr('action', '<?php echo app_base_url('admin/quiz/topics/update/'); ?>' + id);
        $('#modalTopicTitle').text('Edit Topic');
        $('#modalTopic').modal('show');
    }

    function deleteItem(type, id) {
        if (!confirm('Are you sure? This will delete all child items (Subjects/Topics/Questions).')) return;
        
        $.post('<?php echo app_base_url('admin/quiz/'); ?>' + type + '/delete/' + id, function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert('Error: ' + res.error);
            }
        });
    }

    // Generic Form Submission
    $('form').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        $.post(form.attr('action'), form.serialize(), function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert('Error: ' + (res.error || res.message));
            }
        }).fail(function() {
            alert('Server error occurred.');
        });
    });
</script>
