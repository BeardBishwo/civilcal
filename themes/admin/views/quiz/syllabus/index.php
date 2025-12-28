<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Syllabus Manager</h1>
        <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalCategory">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Stream/Category
        </button>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hierarchy (Stream > Subject > Topic)</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($syllabus)): ?>
                        <div class="text-center py-5">
                            <img src="<?php echo app_base_url('assets/images/empty.svg'); ?>" alt="Empty" style="width: 150px; opacity: 0.5;">
                            <p class="mt-3 text-gray-500">No syllabus defined yet. Start by adding a stream (e.g., Civil Engineering).</p>
                        </div>
                    <?php else: ?>
                        <div class="accordion" id="syllabusAccordion">
                            <?php foreach ($syllabus as $catId => $cat): ?>
                                <div class="card mb-2 border-left-primary">
                                    <div class="card-header d-flex justify-content-between align-items-center bg-white" id="heading<?php echo $catId; ?>">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left font-weight-bold text-primary collapsed" type="button" data-toggle="collapse" data-target="#collapse<?php echo $catId; ?>">
                                                <i class="fas fa-folder mr-2"></i> <?php echo htmlspecialchars($cat['name']); ?>
                                            </button>
                                        </h2>
                                        <div class="actions">
                                            <button class="btn btn-sm btn-outline-success mr-1" onclick="openSubjectModal(<?php echo $catId; ?>, '<?php echo addslashes($cat['name']); ?>')" title="Add Subject">
                                                <i class="fas fa-plus"></i> Subject
                                            </button>
                                            <button class="btn btn-sm btn-light text-primary" onclick="editCategory(<?php echo $catId; ?>, '<?php echo addslashes($cat['name']); ?>', '<?php echo $cat['slug']; ?>')" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light text-danger" onclick="deleteItem('categories', <?php echo $catId; ?>)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div id="collapse<?php echo $catId; ?>" class="collapse" data-parent="#syllabusAccordion">
                                        <div class="card-body bg-gray-100">
                                            <!-- Subjects List -->
                                            <?php if (empty($cat['subjects'])): ?>
                                                <p class="text-muted ml-4"><small>No subjects added yet.</small></p>
                                            <?php else: ?>
                                                <ul class="list-group list-group-flush">
                                                    <?php foreach ($cat['subjects'] as $subId => $sub): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 pl-4">
                                                            <div>
                                                                <i class="fas fa-book text-success mr-2"></i> 
                                                                <strong><?php echo htmlspecialchars($sub['name']); ?></strong>
                                                                <div class="ml-4 mt-2">
                                                                    <!-- Topics List -->
                                                                    <?php if (!empty($sub['topics'])): ?>
                                                                        <?php foreach ($sub['topics'] as $topic): ?>
                                                                            <span class="badge badge-light border mr-1 mb-1 p-2">
                                                                                <i class="fas fa-tag text-info mr-1"></i> <?php echo htmlspecialchars($topic['name']); ?>
                                                                                <i class="fas fa-times text-danger ml-2 cursor-pointer" onclick="deleteItem('topics', <?php echo $topic['id']; ?>)" title="Remove Topic"></i>
                                                                                <i class="fas fa-pencil-alt text-gray-500 ml-1 cursor-pointer" onclick="editTopic(<?php echo $topic['id']; ?>, '<?php echo addslashes($topic['name']); ?>', <?php echo $subId; ?>)" title="Edit"></i>
                                                                            </span>
                                                                        <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                    <button class="btn btn-xs btn-outline-info rounded-pill mt-1" onclick="openTopicModal(<?php echo $subId; ?>, '<?php echo addslashes($sub['name']); ?>')">
                                                                        <i class="fas fa-plus"></i> Add Topic
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="actions">
                                                                <button class="btn btn-sm btn-light text-primary" onclick="editSubject(<?php echo $subId; ?>, '<?php echo addslashes($sub['name']); ?>', <?php echo $catId; ?>)">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-light text-danger" onclick="deleteItem('subjects', <?php echo $subId; ?>)">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </li>
                                                        <hr class="my-1 border-top-dashed">
                                                    <?php endforeach; ?>
                                                </ul>
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

<!-- Modals -->

<!-- Category Modal -->
<div class="modal fade" id="modalCategory" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formCategory" method="POST" action="<?php echo app_base_url('admin/quiz/categories/store'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCategoryTitle">Add New Stream/Category</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="cat_id">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="cat_name" required>
                    </div>
                    <div class="form-group">
                        <label>Slug (Optional)</label>
                        <input type="text" class="form-control" name="slug" id="cat_slug">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Subject Modal -->
<div class="modal fade" id="modalSubject" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formSubject" method="POST" action="<?php echo app_base_url('admin/quiz/subjects/store'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSubjectTitle">Add Subject</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="sub_id">
                    <input type="hidden" name="category_id" id="sub_cat_id">
                    <div class="alert alert-info py-1"><small>Adding to Category: <strong id="sub_cat_name_display"></strong></small></div>
                    <div class="form-group">
                        <label>Subject Name</label>
                        <input type="text" class="form-control" name="name" id="sub_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Topic Modal -->
<div class="modal fade" id="modalTopic" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formTopic" method="POST" action="<?php echo app_base_url('admin/quiz/topics/store'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTopicTitle">Add Topic</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="topic_id">
                    <input type="hidden" name="subject_id" id="topic_sub_id">
                    <div class="alert alert-info py-1"><small>Adding to Subject: <strong id="topic_sub_name_display"></strong></small></div>
                    <div class="form-group">
                        <label>Topic Name</label>
                        <input type="text" class="form-control" name="name" id="topic_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Topic</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
        $('#cat_slug').val(slug);
        $('#formCategory').attr('action', '<?php echo app_base_url('admin/quiz/categories/update/'); ?>' + id);
        $('#modalCategoryTitle').text('Edit Category');
        $('#modalCategory').modal('show');
    }

    function editSubject(id, name, catId) {
        $('#sub_id').val(id);
        $('#sub_cat_id').val(catId);
        $('#sub_cat_name_display').text('Current Category'); 
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
