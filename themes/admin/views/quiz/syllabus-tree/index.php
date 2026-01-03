<?php
/**
 * Syllabus Tree Manager View
 * 
 * Interactive tree view for managing recursive syllabus structure
 */
$pageTitle = $page_title ?? 'Syllabus Tree Manager';
$tree = $tree ?? [];
$currentLevel = $current_level ?? null;
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-muted">Manage hierarchical syllabus structure (Papers → Parts → Sections → Units)</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="showCreateNodeModal(null)">
                    <i class="bi bi-plus-circle"></i> Add Root Node
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="expandAll()">
                    <i class="bi bi-arrows-expand"></i> Expand All
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="collapseAll()">
                    <i class="bi bi-arrows-collapse"></i> Collapse All
                </button>
            </div>
        </div>
    </div>

    <!-- Level Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label">Filter by Level:</label>
                    <select class="form-select" id="levelFilter" onchange="filterByLevel(this.value)">
                        <option value="">All Levels</option>
                        <option value="Level 4" <?= $currentLevel === 'Level 4' ? 'selected' : '' ?>>Level 4 (Sub-Engineer)</option>
                        <option value="Level 5" <?= $currentLevel === 'Level 5' ? 'selected' : '' ?>>Level 5 (Engineer)</option>
                        <option value="Level 7" <?= $currentLevel === 'Level 7' ? 'selected' : '' ?>>Level 7 (Officer)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Search:</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search nodes..." onkeyup="searchNodes(this.value)">
                </div>
            </div>
        </div>
    </div>

    <!-- Tree View -->
    <div class="card">
        <div class="card-body">
            <div id="syllabusTree" class="syllabus-tree">
                <?php if (empty($tree)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-diagram-3 display-1"></i>
                        <p class="mt-3">No syllabus structure found. Create a root node to get started.</p>
                    </div>
                <?php else: ?>
                    <?php echo renderTree($tree); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Node Modal -->
<div class="modal fade" id="nodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nodeModalTitle">Create Node</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="nodeForm" onsubmit="saveNode(event)">
                <div class="modal-body">
                    <input type="hidden" id="nodeId" name="node_id">
                    <input type="hidden" id="parentId" name="parent_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" id="nodeTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" id="nodeSlug" name="slug">
                        <small class="text-muted">Leave blank to auto-generate</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <select class="form-select" id="nodeType" name="type" required>
                            <option value="paper">Paper</option>
                            <option value="part">Part</option>
                            <option value="section">Section</option>
                            <option value="unit" selected>Unit</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select class="form-select" id="nodeLevel" name="level">
                            <option value="">None</option>
                            <option value="Level 4">Level 4</option>
                            <option value="Level 5">Level 5</option>
                            <option value="Level 7">Level 7</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="nodeDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <input type="number" class="form-control" id="nodeOrder" name="order" min="0">
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="nodeActive" name="is_active" checked>
                        <label class="form-check-label" for="nodeActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Node</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.syllabus-tree {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.tree-node {
    margin-left: 20px;
    border-left: 2px solid #e0e0e0;
    padding-left: 15px;
    margin-bottom: 8px;
}

.tree-node-header {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 8px;
    transition: all 0.2s;
}

.tree-node-header:hover {
    background: #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tree-node-toggle {
    cursor: pointer;
    margin-right: 8px;
    color: #6c757d;
    font-size: 14px;
}

.tree-node-icon {
    margin-right: 8px;
    font-size: 16px;
}

.tree-node-title {
    flex: 1;
    font-weight: 500;
}

.tree-node-badge {
    font-size: 11px;
    padding: 2px 8px;
    margin-left: 8px;
}

.tree-node-actions {
    display: flex;
    gap: 4px;
}

.tree-node-actions button {
    padding: 2px 8px;
    font-size: 12px;
}

.tree-children {
    display: none;
}

.tree-children.expanded {
    display: block;
}

.node-type-paper { color: #0d6efd; }
.node-type-part { color: #6610f2; }
.node-type-section { color: #6f42c1; }
.node-type-unit { color: #20c997; }
</style>

<script>
const baseUrl = '<?= app_base_url() ?>';

function renderTree(nodes) {
    let html = '';
    nodes.forEach(node => {
        html += renderNode(node);
    });
    return html;
}

function renderNode(node) {
    const hasChildren = node.children && node.children.length > 0;
    const icon = getNodeIcon(node.type);
    const typeClass = `node-type-${node.type}`;
    
    let html = `
        <div class="tree-node" data-node-id="${node.id}">
            <div class="tree-node-header">
                ${hasChildren ? `<span class="tree-node-toggle" onclick="toggleNode(${node.id})"><i class="bi bi-chevron-right"></i></span>` : '<span style="width:20px;display:inline-block;"></span>'}
                <span class="tree-node-icon ${typeClass}">${icon}</span>
                <span class="tree-node-title">${node.title}</span>
                <span class="badge tree-node-badge bg-secondary">${node.type}</span>
                ${node.level ? `<span class="badge tree-node-badge bg-info">${node.level}</span>` : ''}
                <div class="tree-node-actions">
                    <button class="btn btn-sm btn-outline-primary" onclick="showCreateNodeModal(${node.id})" title="Add Child">
                        <i class="bi bi-plus"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="editNode(${node.id})" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteNode(${node.id})" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
    `;
    
    if (hasChildren) {
        html += `<div class="tree-children" id="children-${node.id}">`;
        node.children.forEach(child => {
            html += renderNode(child);
        });
        html += '</div>';
    }
    
    html += '</div>';
    return html;
}

function getNodeIcon(type) {
    const icons = {
        'paper': '<i class="bi bi-file-earmark-text"></i>',
        'part': '<i class="bi bi-folder"></i>',
        'section': '<i class="bi bi-collection"></i>',
        'unit': '<i class="bi bi-book"></i>'
    };
    return icons[type] || '<i class="bi bi-circle"></i>';
}

function toggleNode(nodeId) {
    const children = document.getElementById(`children-${nodeId}`);
    const toggle = event.target.closest('.tree-node-toggle');
    
    if (children.classList.contains('expanded')) {
        children.classList.remove('expanded');
        toggle.innerHTML = '<i class="bi bi-chevron-right"></i>';
    } else {
        children.classList.add('expanded');
        toggle.innerHTML = '<i class="bi bi-chevron-down"></i>';
    }
}

function expandAll() {
    document.querySelectorAll('.tree-children').forEach(el => {
        el.classList.add('expanded');
    });
    document.querySelectorAll('.tree-node-toggle').forEach(el => {
        el.innerHTML = '<i class="bi bi-chevron-down"></i>';
    });
}

function collapseAll() {
    document.querySelectorAll('.tree-children').forEach(el => {
        el.classList.remove('expanded');
    });
    document.querySelectorAll('.tree-node-toggle').forEach(el => {
        el.innerHTML = '<i class="bi bi-chevron-right"></i>';
    });
}

function showCreateNodeModal(parentId) {
    document.getElementById('nodeModalTitle').textContent = 'Create New Node';
    document.getElementById('nodeForm').reset();
    document.getElementById('nodeId').value = '';
    document.getElementById('parentId').value = parentId || '';
    new bootstrap.Modal(document.getElementById('nodeModal')).show();
}

function editNode(nodeId) {
    fetch(`${baseUrl}/admin/quiz/syllabus-tree/details/${nodeId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const node = data.data.node;
                document.getElementById('nodeModalTitle').textContent = 'Edit Node';
                document.getElementById('nodeId').value = node.id;
                document.getElementById('parentId').value = node.parent_id || '';
                document.getElementById('nodeTitle').value = node.title;
                document.getElementById('nodeSlug').value = node.slug;
                document.getElementById('nodeType').value = node.type;
                document.getElementById('nodeLevel').value = node.level || '';
                document.getElementById('nodeDescription').value = node.description || '';
                document.getElementById('nodeOrder').value = node.order;
                document.getElementById('nodeActive').checked = node.is_active == 1;
                new bootstrap.Modal(document.getElementById('nodeModal')).show();
            }
        });
}

function saveNode(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const nodeId = document.getElementById('nodeId').value;
    const url = nodeId 
        ? `${baseUrl}/admin/quiz/syllabus-tree/update/${nodeId}`
        : `${baseUrl}/admin/quiz/syllabus-tree/create`;

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    });
}

function deleteNode(nodeId) {
    if (!confirm('Delete this node and all its children?')) return;
    
    fetch(`${baseUrl}/admin/quiz/syllabus-tree/delete/${nodeId}`, {
        method: 'POST'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    });
}

function filterByLevel(level) {
    const url = new URL(window.location);
    if (level) {
        url.searchParams.set('level', level);
    } else {
        url.searchParams.delete('level');
    }
    window.location = url;
}

function searchNodes(query) {
    if (query.length < 2) {
        // Show all nodes
        document.querySelectorAll('.tree-node').forEach(el => el.style.display = '');
        return;
    }

    const level = document.getElementById('levelFilter').value;
    fetch(`${baseUrl}/admin/quiz/syllabus-tree/search?q=${encodeURIComponent(query)}&level=${level}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const resultIds = data.results.map(r => r.id);
                document.querySelectorAll('.tree-node').forEach(el => {
                    const nodeId = el.dataset.nodeId;
                    el.style.display = resultIds.includes(parseInt(nodeId)) ? '' : 'none';
                });
            }
        });
}
</script>

<?php
function renderTree($nodes) {
    $html = '';
    foreach ($nodes as $node) {
        $html .= renderNode($node);
    }
    return $html;
}

function renderNode($node) {
    $hasChildren = !empty($node['children']);
    $icon = getNodeIcon($node['type']);
    $typeClass = "node-type-{$node['type']}";
    
    $html = '<div class="tree-node" data-node-id="' . $node['id'] . '">';
    $html .= '<div class="tree-node-header">';
    
    if ($hasChildren) {
        $html .= '<span class="tree-node-toggle" onclick="toggleNode(' . $node['id'] . ')"><i class="bi bi-chevron-right"></i></span>';
    } else {
        $html .= '<span style="width:20px;display:inline-block;"></span>';
    }
    
    $html .= '<span class="tree-node-icon ' . $typeClass . '">' . $icon . '</span>';
    $html .= '<span class="tree-node-title">' . htmlspecialchars($node['title']) . '</span>';
    $html .= '<span class="badge tree-node-badge bg-secondary">' . $node['type'] . '</span>';
    
    if (!empty($node['level'])) {
        $html .= '<span class="badge tree-node-badge bg-info">' . htmlspecialchars($node['level']) . '</span>';
    }
    
    $html .= '<div class="tree-node-actions">';
    $html .= '<button class="btn btn-sm btn-outline-primary" onclick="showCreateNodeModal(' . $node['id'] . ')" title="Add Child"><i class="bi bi-plus"></i></button>';
    $html .= '<button class="btn btn-sm btn-outline-secondary" onclick="editNode(' . $node['id'] . ')" title="Edit"><i class="bi bi-pencil"></i></button>';
    $html .= '<button class="btn btn-sm btn-outline-danger" onclick="deleteNode(' . $node['id'] . ')" title="Delete"><i class="bi bi-trash"></i></button>';
    $html .= '</div></div>';
    
    if ($hasChildren) {
        $html .= '<div class="tree-children" id="children-' . $node['id'] . '">';
        foreach ($node['children'] as $child) {
            $html .= renderNode($child);
        }
        $html .= '</div>';
    }
    
    $html .= '</div>';
    return $html;
}

function getNodeIcon($type) {
    $icons = [
        'paper' => '<i class="bi bi-file-earmark-text"></i>',
        'part' => '<i class="bi bi-folder"></i>',
        'section' => '<i class="bi bi-collection"></i>',
        'unit' => '<i class="bi bi-book"></i>'
    ];
    return $icons[$type] ?? '<i class="bi bi-circle"></i>';
}
?>
