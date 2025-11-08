<?php
$content = '
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Users Management</h2>
            <p class="text-muted mb-0">Manage all users, roles, and permissions</p>
        </div>
        <div class="quick-actions">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus me-2"></i>Add User
            </button>
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-download me-2"></i>Export Users
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
            <div class="d-flex">
                <input type="text" class="form-control form-control-sm me-2" placeholder="Search users..." id="userSearch">
                <select class="form-select form-select-sm" style="width: auto;">
                    <option>All Roles</option>
                    <option>Admin</option>
                    <option>Engineer</option>
                    <option>User</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/assets/images/avatar1.jpg" class="rounded-circle me-2" width="32" height="32" alt="User">
                                    <div>
                                        <div class="fw-bold">John Doe</div>
                                        <small class="text-muted">@johndoe</small>
                                    </div>
                                </div>
                            </td>
                            <td>john@example.com</td>
                            <td><span class="badge bg-primary">Admin</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>2 hours ago</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/assets/images/avatar2.jpg" class="rounded-circle me-2" width="32" height="32" alt="User">
                                    <div>
                                        <div class="fw-bold">Jane Smith</div>
                                        <small class="text-muted">@janesmith</small>
                                    </div>
                                </div>
                            </td>
                            <td>jane@example.com</td>
                            <td><span class="badge bg-info">Engineer</span></td>
                            <td><span class="badge bg-success">Active</span></td>
                            <td>1 day ago</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/assets/images/avatar3.jpg" class="rounded-circle me-2" width="32" height="32" alt="User">
                                    <div>
                                        <div class="fw-bold">Mike Johnson</div>
                                        <small class="text-muted">@mikejohnson</small>
                                    </div>
                                </div>
                            </td>
                            <td>mike@example.com</td>
                            <td><span class="badge bg-secondary">User</span></td>
                            <td><span class="badge bg-warning">Inactive</span></td>
                            <td>3 days ago</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin">Administrator</option>
                            <option value="engineer">Engineer</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addUserForm" class="btn btn-primary">Add User</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" id="editFullName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="editUsername" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="editEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" id="editRole" required>
                            <option value="">Select Role</option>
                            <option value="admin">Administrator</option>
                            <option value="engineer">Engineer</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="editStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editUserForm" class="btn btn-primary">Update User</button>
            </div>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="/assets/images/avatar1.jpg" class="rounded-circle mb-3" width="100" height="100" alt="User">
                        <h5 id="detailsName">John Doe</h5>
                        <p class="text-muted" id="detailsUsername">@johndoe</p>
                        <span class="badge bg-primary" id="detailsRole">Admin</span>
                    </div>
                    <div class="col-md-8">
                        <h6>User Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td id="detailsEmail">john@example.com</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td id="detailsStatus"><span class="badge bg-success">Active</span></td>
                            </tr>
                            <tr>
                                <td><strong>Last Login:</strong></td>
                                <td id="detailsLastLogin">2 hours ago</td>
                            </tr>
                            <tr>
                                <td><strong>Joined:</strong></td>
                                <td id="detailsJoined">Jan 15, 2023</td>
                            </tr>
                            <tr>
                                <td><strong>Calculations:</strong></td>
                                <td id="detailsCalculations">156</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// User management JavaScript
document.addEventListener("DOMContentLoaded", function() {
    // Search functionality
    const userSearch = document.getElementById("userSearch");
    if (userSearch) {
        userSearch.addEventListener("input", function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.getElementById("usersTable");
            const rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName("td");
                let found = false;
                
                for (let j = 0; j < cells.length - 1; j++) { // Exclude actions column
                    if (cells[j].textContent.toLowerCase().includes(searchTerm)) {
                        found = true;
                        break;
                    }
                }
                
                row.style.display = found ? "" : "none";
            }
        });
    }
    
    // Add User Form
    const addUserForm = document.getElementById("addUserForm");
    if (addUserForm) {
        addUserForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const userData = Object.fromEntries(formData);
            
            // Here you would typically send an AJAX request to add the user
            console.log("Adding user:", userData);
            
            // Show success message and close modal
            alert("User added successfully!");
            bootstrap.Modal.getInstance(document.getElementById("addUserModal")).hide();
            this.reset();
        });
    }
    
    // Edit User Form
    const editUserForm = document.getElementById("editUserForm");
    if (editUserForm) {
        editUserForm.addEventListener("submit", function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const userData = Object.fromEntries(formData);
            
            // Here you would typically send an AJAX request to update the user
            console.log("Updating user:", userData);
            
            // Show success message and close modal
            alert("User updated successfully!");
            bootstrap.Modal.getInstance(document.getElementById("editUserModal")).hide();
        });
    }
    
    // Edit button handlers
    const editButtons = document.querySelectorAll("[data-bs-target=\"#editUserModal\"]");
    editButtons.forEach(button => {
        button.addEventListener("click", function() {
            const row = this.closest("tr");
            const cells = row.getElementsByTagName("td");
            
            // Populate edit form with row data
            document.getElementById("editUserId").value = "1"; // Would get actual user ID
            document.getElementById("editFullName").value = cells[0].textContent.trim();
            document.getElementById("editUsername").value = cells[0].querySelector("small").textContent;
            document.getElementById("editEmail").value = cells[1].textContent;
            document.getElementById("editRole").value = cells[2].textContent.toLowerCase().trim();
            document.getElementById("editStatus").value = cells[3].textContent.toLowerCase().trim();
        });
    });
    
    // View Profile button handlers
    const viewButtons = document.querySelectorAll("[data-bs-target=\"#userDetailsModal\"]");
    viewButtons.forEach(button => {
        button.addEventListener("click", function() {
            const row = this.closest("tr");
            const cells = row.getElementsByTagName("td");
            
            // Populate user details modal with row data
            document.getElementById("detailsName").textContent = cells[0].textContent.trim();
            document.getElementById("detailsUsername").textContent = cells[0].querySelector("small").textContent;
            document.getElementById("detailsEmail").textContent = cells[1].textContent;
            document.getElementById("detailsRole").textContent = cells[2].textContent;
            document.getElementById("detailsStatus").innerHTML = cells[3].innerHTML;
            document.getElementById("detailsLastLogin").textContent = cells[4].textContent;
        });
    });
    
    // Delete confirmation
    const deleteButtons = document.querySelectorAll("[title=\"Delete\"]");
    deleteButtons.forEach(button => {
        button.addEventListener("click", function() {
            if (confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
                // Here you would typically send an AJAX request to delete the user
                console.log("Deleting user...");
                this.closest("tr").remove();
                alert("User deleted successfully!");
            }
        });
    });
});
</script>
';

include __DIR__ . '/../../layouts/admin.php';
?>
