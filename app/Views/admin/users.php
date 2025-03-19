<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php require_once __DIR__ . '/../partials/admin_sidebar.php'; ?>

        <!-- Main content -->
        <main class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3 border-bottom">
                <h1 class="h2">Manage Users</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Search Bar -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="/php-sneakers-store/public/admin/users" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search users by name, email, or role..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                            <?php if (isset($_GET['search'])): ?>
                                <a href="/php-sneakers-store/public/admin/users" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                <div class="col-md-3">
                    <form action="/php-sneakers-store/public/admin/users" method="GET" class="d-flex">
                        <?php if(isset($_GET['search']) && !empty($_GET['search'])): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                        <?php endif; ?>
                        <select name="role" class="form-select" onchange="this.form.submit()">
                            <option value="">All Roles</option>
                            <option value="admin" <?php echo isset($_GET['role']) && $_GET['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="user" <?php echo isset($_GET['role']) && $_GET['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['is_admin'] ? 'danger' : 'success'; ?>">
                                                <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info view-user" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewUserModal"
                                                    data-id="<?php echo $user['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                    data-is-admin="<?php echo $user['is_admin']; ?>"
                                                    data-created-at="<?php echo $user['created_at']; ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary update-status" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#updateStatusModal"
                                                    data-id="<?php echo $user['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                    data-is-admin="<?php echo $user['is_admin']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <p id="view_user_name"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <p id="view_user_email"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <p id="view_user_role"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Member Since</label>
                    <p id="view_user_created_at"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update User Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/php-sneakers-store/public/admin/toggle-admin" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="update_user_id">
                    <div class="mb-3">
                        <label class="form-label">User Name</label>
                        <p id="update_user_name"></p>
                    </div>
                    <div class="mb-3">
                        <label for="is_admin" class="form-label">User Role</label>
                        <select class="form-select" id="is_admin" name="is_admin" required>
                            <option value="0">User</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle view user button clicks
    document.querySelectorAll('.view-user').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            
            // Populate the view modal with user data
            document.getElementById('view_user_name').textContent = data.name;
            document.getElementById('view_user_email').textContent = data.email;
            document.getElementById('view_user_role').textContent = data.isAdmin === '1' ? 'Admin' : 'User';
            document.getElementById('view_user_created_at').textContent = new Date(data.createdAt).toLocaleString();
        });
    });

    // Handle update status button clicks
    document.querySelectorAll('.update-status').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            
            // Populate the update modal with user data
            document.getElementById('update_user_id').value = data.id;
            document.getElementById('update_user_name').textContent = data.name;
            document.getElementById('is_admin').value = data.isAdmin;
        });
    });
});
</script>

<style>
    .card {
        border: none;
        transition: transform 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    .table th {
        font-weight: 500;
        color: #495057;
    }
    
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }

    @media (min-width: 768px) {
        main {
            margin-left: 0 !important;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto !important;
            padding-left: 20px;
            padding-right: 20px;
        }
        
        .col-md-9.col-lg-10 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .container-fluid .row > .col-md-9.col-lg-10 {
            margin: 0 auto;
        }
    }

    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }

    .row {
        margin-left: 0;
        margin-right: 0;
        justify-content: center;
    }
    
    /* Modal styling */
    .modal-backdrop {
        opacity: 0.5 !important;
    }

    .modal {
        background: rgba(0, 0, 0, 0.5);
    }

    .modal-dialog {
        margin: 1.75rem auto;
        max-width: 500px;
    }
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 