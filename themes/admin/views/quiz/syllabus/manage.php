<?php
/**
 * PREMIUM SYLLABUS EDITOR - TREE VIEW
 */
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper p-4">

        <div class="card border-0 shadow-lg rounded-4 mb-4" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
            <div class="card-body p-4 text-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-1">
                            <a href="<?php echo app_base_url('admin/quiz/syllabus'); ?>" class="btn btn-white btn-sm rounded-circle text-primary bg-white">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <h2 class="fw-bold mb-0 text-white"><?php echo htmlspecialchars($level); ?></h2>
                        </div>
                        <div class="text-white-50 ms-5">Structure Builder</div>
                    </div>
                    <div class="d-flex gap-2">
                         <button onclick="openGenerateModal()" class="btn btn-outline-light rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fas fa-magic me-2"></i> Generate Exam
                        </button>
                        <button onclick="openAddModal()" class="btn btn-light rounded-pill px-4 fw-bold text-primary shadow-sm">
                            <i class="fas fa-plus me-2"></i> Add Top Section
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 tree-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 40%;">Structure Node</th>
                                    <th class="py-3">Type</th>
                                    <th class="py-3">Mapping</th>
                                    <th class="py-3 text-center">Weight</th>
                                    <th class="py-3 text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                function renderTree($nodes) {
                                    if(empty($nodes)) return;
                                    foreach ($nodes as $node): ?>
                                        <tr class="node-row" data-id="<?php echo $node['id']; ?>">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center" style="padding-left: <?php echo ($node['depth'] * 30); ?>px;">
                                                    <?php if($node['depth'] > 0): ?>
                                                        <div class="tree-connector"></div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="node-icon me-3 <?php echo getTypeColor($node['type']); ?>">
                                                        <i class="fas <?php echo getTypeIcon($node['type']); ?>"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($node['title']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border text-uppercase" style="font-size: 10px;">
                                                    <?php echo $node['type']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($node['category_name']): ?>
                                                    <div class="badge bg-soft-primary text-primary mb-1">
                                                        <i class="fas fa-folder me-1"></i> <?php echo htmlspecialchars($node['category_name']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($node['topic_name']): ?>
                                                    <div class="badge bg-soft-success text-success">
                                                        <i class="fas fa-tag me-1"></i> <?php echo htmlspecialchars($node['topic_name']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!$node['category_name'] && !$node['topic_name']): ?>
                                                    <span class="text-muted small italic">- No Link -</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if($node['questions_weight'] > 0): ?>
                                                    <span class="badge rounded-pill bg-indigo text-white px-3 py-2">
                                                        <?php echo $node['questions_weight']; ?> Qs
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button class="btn btn-sm btn-light text-primary border rounded-circle" 
                                                        onclick="openAddModal(<?php echo $node['id']; ?>, '<?php echo addslashes($node['title']); ?>')" 
                                                        title="Add Child">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light text-secondary border rounded-circle ms-1" 
                                                        onclick='editNode(<?php echo json_encode($node); ?>)' 
                                                        title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light text-danger border rounded-circle ms-1" 
                                                        onclick="deleteNode(<?php echo $node['id']; ?>)" 
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php 
                                        if (!empty($node['children'])) {
                                            renderTree($node['children']); 
                                        }
                                        ?>
                                    <?php endforeach;
                                }

                                function getTypeIcon($t) {
                                    return match($t) { 'paper'=>'fa-book', 'part'=>'fa-layer-group', 'section'=>'fa-columns', default=>'fa-file-alt' };
                                }
                                function getTypeColor($t) {
                                    return match($t) { 'paper'=>'bg-primary-soft', 'part'=>'bg-purple-soft', 'section'=>'bg-info-soft', default=>'bg-gray-soft' };
                                }
                                
                                renderTree($nodesTree); 
                                ?>
                            </tbody>
                        </table>

                        <?php if (empty($nodes)): ?>
                            <div class="text-center p-5">
                                <div class="mb-3"><i class="fas fa-sitemap fs-1 text-muted opacity-25"></i></div>
                                <h4 class="text-muted">Structure is Empty</h4>
                                <button onclick="openAddModal()" class="btn btn-primary mt-2">Create First Entry</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="nodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="nodeForm">
                <input type="hidden" name="id" id="nodeId">
                <input type="hidden" name="parent_id" id="parentId">
                <input type="hidden" name="level" value="<?php echo htmlspecialchars($level); ?>">

                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="parentInfo" class="alert alert-primary py-2 small mb-3" style="display:none;">
                        Adding inside: <b id="parentNameDisplay"></b>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Title / Name</label>
                        <input type="text" name="title" id="fieldTitle" class="form-control" required placeholder="e.g. Surveying">
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Type</label>
                            <select name="type" id="fieldType" class="form-select">
                                <option value="paper">Paper</option>
                                <option value="part">Part</option>
                                <option value="section">Section</option>
                                <option value="unit">Unit/Topic</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Question Count</label>
                            <input type="number" name="questions_weight" id="fieldWeight" class="form-control" value="0">
                        </div>
                    </div>

                    <hr class="my-4 border-light">
                    <h6 class="small fw-bold text-primary mb-3">Map to Content</h6>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Link to Main Category (Subject)</label>
                        <select name="linked_category_id" id="fieldCat" class="form-select">
                            <option value="">-- None --</option>
                            <?php foreach($categories as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Link to Sub-Topic</label>
                        <select name="linked_topic_id" id="fieldTopic" class="form-select">
                            <option value="">-- None --</option>
                            <?php foreach($topics as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="fieldActive" checked>
                        <label class="form-check-label small" for="fieldActive">Active in Exam Generation</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Tree Guidelines */
    .tree-connector {
        width: 15px;
        height: 1px;
        background: #dee2e6;
        margin-right: 10px;
        position: relative;
    }
    .tree-connector::before {
        content: '';
        position: absolute;
        left: -15px;
        top: -20px;
        width: 1px;
        height: 21px;
        background: #dee2e6;
    }
    /* Simple colored backgrounds for icons */
    .bg-primary-soft { background: #e0e7ff; color: #4f46e5; }
    .bg-purple-soft { background: #f3e8ff; color: #9333ea; }
    .bg-info-soft { background: #cffafe; color: #0891b2; }
    .bg-gray-soft { background: #f3f4f6; color: #4b5563; }
    
    .node-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
    }
    .bg-soft-primary { background: rgba(13, 110, 253, 0.1); }
    .bg-soft-success { background: rgba(25, 135, 84, 0.1); }
    .bg-indigo { background: #4f46e5; }
</style>

<script>
    const myModal = new bootstrap.Modal(document.getElementById('nodeModal'));
    
    function openAddModal(parentId = null, parentName = null) {
        document.getElementById('nodeForm').reset();
        document.getElementById('nodeId').value = '';
        document.getElementById('parentId').value = parentId || '';
        document.getElementById('modalTitle').innerText = parentId ? 'Add Sub-Item' : 'Add Top Item';
        
        // Show context
        if(parentId) {
            document.getElementById('parentInfo').style.display = 'block';
            document.getElementById('parentNameDisplay').innerText = parentName;
        } else {
            document.getElementById('parentInfo').style.display = 'none';
        }
        
        myModal.show();
    }

    function editNode(node) {
        document.getElementById('nodeForm').reset();
        document.getElementById('nodeId').value = node.id;
        document.getElementById('parentId').value = node.parent_id;
        
        document.getElementById('fieldTitle').value = node.title;
        document.getElementById('fieldType').value = node.type;
        document.getElementById('fieldWeight').value = node.questions_weight;
        
        // Handle Nulls properly for Selects
        document.getElementById('fieldCat').value = node.linked_category_id ? node.linked_category_id : "";
        document.getElementById('fieldTopic').value = node.linked_topic_id ? node.linked_topic_id : "";
        
        document.getElementById('fieldActive').checked = (node.is_active == 1);
        
        document.getElementById('modalTitle').innerText = 'Edit Structure';
        document.getElementById('parentInfo').style.display = 'none';
        
        myModal.show();
    }
    
    function deleteNode(id) {
        if(!confirm('Are you sure? This deletes all sub-items too.')) return;
        
        fetch('<?= app_base_url("admin/quiz/syllabus/delete/") ?>' + id, { method: 'POST' })
        .then(r => r.json())
        .then(d => {
            if(d.status === 'success') location.reload();
        });
    }

    document.getElementById('nodeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        const id = document.getElementById('nodeId').value;
        const url = id ? '<?= app_base_url("admin/quiz/syllabus/update/") ?>' + id : '<?= app_base_url("admin/quiz/syllabus/store") ?>';

        fetch(url, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
            if(d.status === 'success') {
                location.reload();
            } else {
                alert(d.message);
            }
        });
    });

    function openGenerateModal() {
        Swal.fire({
            title: 'Generate Exam Structure',
            text: 'This will create a draft exam blueprint based on these rules.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Proceed'
        }).then(res => {
            if(res.isConfirmed) {
                const fd = new FormData();
                fd.append('level', '<?= addslashes($level) ?>');
                fetch('<?= app_base_url("admin/quiz/syllabus/generate-exam") ?>', {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(d => {
                    if(d.status === 'success') {
                        Swal.fire('Success', d.message, 'success').then(() => {
                            if(d.redirect) window.location.href = d.redirect;
                        });
                    } else {
                        Swal.fire('Error', d.message, 'error');
                    }
                });
            }
        });
    }
</script>
