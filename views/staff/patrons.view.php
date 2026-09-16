<?php
$pageTitle   = "Patron Management | SmartLib LMS";
$currentPage = "patrons";

require_once 'views/partials/header2.php';
require_once 'views/partials/sidebar2.php';
?>

<div class="main-content">
    <?php require_once 'views/partials/topbar2.php'; ?>

    <!-- Flash Notification -->
    <?php if (isset($_SESSION['flash_msg'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'info' ?> alert-dismissible fade show mb-4" role="alert">
            <?= htmlspecialchars($_SESSION['flash_msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php 
            unset($_SESSION['flash_msg']);
            unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>

    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary-blue);">Patron Management</h4>
            <p class="text-muted small mb-0">Manage registered students, faculty, and system staff accounts.</p>
        </div>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addPatronModal">
            <i class="fa-solid fa-user-plus me-2"></i>Register New Patron
        </button>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="/staff-patrons" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="role_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="0">All Roles</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['role_id'] ?>" <?= $roleFilter === (int)$role['role_id'] ? 'selected' : '' ?>>
                                <?= ucfirst(htmlspecialchars($role['role_name'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="suspended" <?= $statusFilter === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, card no, email, department..." value="<?= htmlspecialchars($searchTerm) ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-1"></i>Search</button>
                    <a href="/staff-patrons" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Patrons Data Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Patron</th>
                            <th>Card No</th>
                            <th>Role & Department</th>
                            <th>Active Loans</th>
                            <th>Unpaid Fines</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($patrons)): ?>
                            <?php foreach ($patrons as $patron): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block text-dark"><?= htmlspecialchars($patron['full_name']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($patron['email']) ?></small>
                                    </td>
                                    <td><code><?= htmlspecialchars($patron['library_card_no']) ?></code></td>
                                    <td>
                                        <span class="badge bg-soft-info text-info me-1"><?= ucfirst(htmlspecialchars($patron['role_name'])) ?></span>
                                        <small class="text-muted d-block"><?= htmlspecialchars($patron['department'] ?? 'N/A') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark fw-bold"><?= $patron['active_loans'] ?> Loans</span>
                                    </td>
                                    <td>
                                        <?php if ((float)$patron['unpaid_fines'] > 0): ?>
                                            <span class="text-danger fw-bold">₦<?= number_format((float)$patron['unpaid_fines'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">₦0.00</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($patron['status'] === 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php elseif ($patron['status'] === 'suspended'): ?>
                                            <span class="badge bg-danger">Suspended</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editPatronModal<?= $patron['user_id'] ?>">
                                            <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Patron Modal -->
                                <div class="modal fade" id="editPatronModal<?= $patron['user_id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content text-start">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-pen me-2"></i>Edit Patron Account</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="/staff-update-patron">
                                                <input type="hidden" name="user_id" value="<?= $patron['user_id'] ?>">
                                                <div class="modal-body p-4">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Full Name</label>
                                                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($patron['full_name']) ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Library Card No</label>
                                                            <input type="text" name="library_card_no" class="form-control" value="<?= htmlspecialchars($patron['library_card_no']) ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Email Address</label>
                                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($patron['email']) ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold">Department</label>
                                                            <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($patron['department'] ?? '') ?>">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label fw-bold">System Role</label>
                                                            <select name="role_id" class="form-select" required>
                                                                <?php foreach ($roles as $role): ?>
                                                                    <option value="<?= $role['role_id'] ?>" <?= $role['role_id'] == $patron['role_id'] ? 'selected' : '' ?>>
                                                                        <?= ucfirst(htmlspecialchars($role['role_name'])) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label fw-bold">Account Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="active" <?= $patron['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                                <option value="suspended" <?= $patron['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                                                <option value="inactive" <?= $patron['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label fw-bold">Reset Password</label>
                                                            <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill"><i class="fa-solid fa-floppy-disk me-1"></i>Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No patrons registered in the system matching criteria.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add New Patron Modal -->
<div class="modal fade" id="addPatronModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-plus me-2"></i>Register New Patron Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/staff-add-patron">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" placeholder="e.g., Nasir Yakubu" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Library Card No <span class="text-danger">*</span></label>
                            <input type="text" name="library_card_no" class="form-control" placeholder="e.g., STU/2026/0004" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="e.g., student@smartlib.edu" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Department</label>
                            <input type="text" name="department" class="form-control" placeholder="e.g., Biochemistry">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">System Role <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-select" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['role_id'] ?>" <?= $role['role_id'] == 3 ? 'selected' : '' ?>>
                                        <?= ucfirst(htmlspecialchars($role['role_name'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Initial Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" value="12345678" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill"><i class="fa-solid fa-check me-1"></i>Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// require_once 'views/partials/modals_staff.php';
require_once 'views/partials/footer2.php'; 
?>