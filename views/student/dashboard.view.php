<?php

  require_once 'model/Database.php'; 

  // Ensure user is logged in
  if (!isset($_SESSION['user_id'])) {
      header('Location: index.html');
      exit;
  }

  $user_id = $_SESSION['user_id'];

  // 1. Fetch total active borrowed books
  $stmtBorrowed = $db->checkExist(
      "SELECT COUNT(*) AS total FROM loans WHERE user_id = :user_id AND status = 'borrowed'", 
      [':user_id' => $user_id]
  );
  $borrowedCount = $stmtBorrowed->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

  // 2. Fetch total active holds
  $stmtHolds = $db->checkExist(
      "SELECT COUNT(*) AS total FROM holds WHERE user_id = :user_id AND status = 'pending'", 
      [':user_id' => $user_id]
  );
  $holdsCount = $stmtHolds->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

  // 3. Fetch total overdue items
  $stmtOverdue = $db->checkExist(
      "SELECT COUNT(*) AS total FROM loans WHERE user_id = :user_id AND status = 'overdue'", 
      [':user_id' => $user_id]
  );
  $overdueCount = $stmtOverdue->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

  // 4. Fetch total unpaid fines
  $stmtFines = $db->checkExist(
      "SELECT SUM(amount) AS total FROM fines WHERE user_id = :user_id AND status = 'unpaid'", 
      [':user_id' => $user_id]
  );
  $totalFine = $stmtFines->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;
?>
<?php 
  require_once 'views/partials/header1.php'; 
  require_once 'views/partials/sidebar1.php';
?>
    

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Header Bar -->
        <?php require_once 'views/partials/topbar1.php'; ?>

        <!-- Metric Stat Cards -->
        <div class="row g-3 mb-4">
          <!-- Books Borrowed Card -->
          <div class="col-md-3">
              <div class="card stat-card p-3 bg-white">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <span class="text-muted small fw-bold">BOOKS BORROWED</span>
                          <h2 class="fw-bold my-1 text-primary"><?= (int)$borrowedCount ?></h2>
                          <span class="badge bg-success">Max Limit: 4</span>
                      </div>
                      <i class="fa-solid fa-book text-primary bg-icon"></i>
                  </div>
              </div>
          </div>

          <!-- Active Holds Card -->
          <div class="col-md-3">
              <div class="card stat-card p-3 bg-white">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <span class="text-muted small fw-bold">ACTIVE HOLDS</span>
                          <h2 class="fw-bold my-1 text-info"><?= (int)$holdsCount ?></h2>
                          <span class="badge bg-info text-dark">
                              <?= $holdsCount > 0 ? 'Pending Pickup' : 'No Holds' ?>
                          </span>
                      </div>
                      <i class="fa-solid fa-bookmark text-info bg-icon"></i>
                  </div>
              </div>
          </div>

          <!-- Overdue Items Card -->
          <div class="col-md-3">
              <div class="card stat-card p-3 bg-white">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <span class="text-muted small fw-bold">OVERDUE ITEMS</span>
                          <h2 class="fw-bold my-1 text-danger"><?= (int)$overdueCount ?></h2>
                          <?php if ($overdueCount > 0): ?>
                              <span class="badge bg-danger">Action Required</span>
                          <?php else: ?>
                              <span class="badge bg-success">Good Standing</span>
                          <?php endif; ?>
                      </div>
                      <i class="fa-solid fa-triangle-exclamation text-danger bg-icon"></i>
                  </div>
              </div>
          </div>

          <!-- Outstanding Fines Card (Naira Currency) -->
          <div class="col-md-3">
              <div class="card stat-card p-3 bg-white">
                  <div class="d-flex justify-content-between align-items-center">
                      <div>
                          <span class="text-muted small fw-bold">OUTSTANDING FINE</span>
                          <h2 class="fw-bold my-1 text-warning">₦<?= number_format((float)$totalFine, 2) ?></h2>
                          <span class="text-muted small">
                              <?= $totalFine > 0 ? 'Unpaid Penalties' : 'No Unpaid Fines' ?>
                          </span>
                      </div>
                      <i class="fa-solid fa-wallet text-warning bg-icon"></i>
                  </div>
              </div>
          </div>
        </div>

        <!-- Borrowed Books Table -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
          <div class="card-header bg-white py-3">
              <h5 class="fw-bold mb-0" style="color: var(--primary-blue);">
                  <i class="fa-solid fa-book-reader me-2"></i>Currently Borrowed Books
              </h5>
          </div>
          <div class="card-body p-0">
              <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                      <thead class="table-light">
                          <tr>
                              <th>ISBN</th>
                              <th>Book Title</th>
                              <th>Issue Date</th>
                              <th>Due Date</th>
                              <th>Status</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php if (!empty($borrowedBooks)): ?>
                              <?php foreach ($borrowedBooks as $book): ?>
                                  <?php 
                                      $days = (int)$book['days_left'];
                                      $issueDate = date('d M Y', strtotime($book['issue_date']));
                                      $dueDate   = date('d M Y', strtotime($book['due_date']));

                                      // Dynamic Status Calculation
                                      if ($days < 0) {
                                          $statusBadge = '<span class="badge bg-danger">Overdue (' . abs($days) . ' Days)</span>';
                                      } elseif ($days <= 3) {
                                          $statusBadge = '<span class="badge bg-warning text-dark">Due Soon (' . $days . ' Days)</span>';
                                      } else {
                                          $statusBadge = '<span class="badge bg-success">On Schedule</span>';
                                      }
                                  ?>
                                  <tr>
                                      <td><code><?= htmlspecialchars($book['isbn']) ?></code></td>
                                      <td class="fw-bold"><?= htmlspecialchars($book['title']) ?></td>
                                      <td><?= $issueDate ?></td>
                                      <td><?= $dueDate ?></td>
                                      <td><?= $statusBadge ?></td>
                                      <td>
                                          <button class="btn btn-sm btn-outline-primary rounded-pill btn-renew" data-loan-id="<?= $book['loan_id'] ?>">
                                              Request Renewal
                                          </button>
                                      </td>
                                  </tr>
                              <?php endforeach; ?>
                          <?php else: ?>
                              <tr>
                                  <td colspan="6" class="text-center text-muted py-4">You have no currently borrowed books.</td>
                              </tr>
                          <?php endif; ?>
                      </tbody>
                  </table>
              </div>
          </div>
        </div>
    </div>
<?php require_once 'views/partials/footer1.php' ?>