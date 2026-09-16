<?php
$pageTitle   = "Analytics Reports | SmartLib LMS";
$currentPage = "reports";

require_once 'views/partials/header2.php';
require_once 'views/partials/sidebar2.php';
?>

<div class="main-content">
    <?php require_once 'views/partials/topbar2.php'; ?>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color: var(--primary-blue);">Analytics & Library Insights</h4>
            <p class="text-muted small mb-0">Overview of circulation performance, popular titles, and financial statistics.</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-primary rounded-pill d-print-none">
            <i class="fa-solid fa-print me-2"></i>Print Report
        </button>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-soft-primary text-primary rounded-circle me-3">
                        <i class="fa-solid fa-book fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block">Total Catalog Titles</small>
                        <h4 class="fw-bold mb-0"><?= number_format((int)($kpis['total_titles'] ?? 0)) ?></h4>
                        <small class="text-muted"><?= number_format((int)($kpis['total_inventory_copies'] ?? 0)) ?> copies total</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-soft-info text-info rounded-circle me-3">
                        <i class="fa-solid fa-book-reader fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block">Active Borrowers</small>
                        <h4 class="fw-bold mb-0"><?= number_format((int)($kpis['active_loans'] ?? 0)) ?></h4>
                        <small class="text-info"><?= number_format((int)($kpis['overdue_loans'] ?? 0)) ?> overdue</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-soft-success text-success rounded-circle me-3">
                        <i class="fa-solid fa-users fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block">Patron Population</small>
                        <h4 class="fw-bold mb-0"><?= number_format((int)($kpis['registered_students'] ?? 0)) ?></h4>
                        <small class="text-muted">Registered patrons</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-soft-warning text-warning rounded-circle me-3">
                        <i class="fa-solid fa-coins fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block">Fines Revenue</small>
                        <h4 class="fw-bold text-success mb-0">₦<?= number_format((float)($kpis['total_fines_collected'] ?? 0), 2) ?></h4>
                        <small class="text-muted">Total collected</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Circulation Trend Chart -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-line text-primary me-2"></i>6-Month Circulation Velocity</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyLoansChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Distribution Chart -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-pie text-primary me-2"></i>Catalog by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Borrowed Titles Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-trophy text-warning me-2"></i>Most Requested Book Titles</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Rank</th>
                            <th>Book Title</th>
                            <th>ISBN</th>
                            <th>Category</th>
                            <th class="text-end">Total Times Issued</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($topBooks)): ?>
                            <?php $rank = 1; foreach ($topBooks as $book): ?>
                                <tr>
                                    <td><span class="badge rounded-pill bg-light text-dark fw-bold">#<?= $rank++ ?></span></td>
                                    <td><span class="fw-bold text-dark"><?= htmlspecialchars($book['title']) ?></span></td>
                                    <td><code><?= htmlspecialchars($book['isbn']) ?></code></td>
                                    <td><span class="badge bg-soft-primary text-primary"><?= htmlspecialchars($book['category_name']) ?></span></td>
                                    <td class="text-end fw-bold text-primary"><?= number_format((int)$book['borrow_count']) ?> Loans</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No loan metrics available yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Line Chart: Monthly Loans
    const ctxLoans = document.getElementById('monthlyLoansChart').getContext('2d');
    new Chart(ctxLoans, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartMonths) ?>,
            datasets: [{
                label: 'Books Issued',
                data: <?= json_encode($chartLoanCounts) ?>,
                borderColor: '#1e3a8a',
                backgroundColor: 'rgba(30, 58, 138, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // 2. Doughnut Chart: Categories
    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chartCategories) ?>,
            datasets: [{
                data: <?= json_encode($chartCatCounts) ?>,
                backgroundColor: ['#1e3a8a', '#0d6efd', '#0dcaf0', '#198754', '#ffc107', '#dc3545', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>

<?php 
// require_once 'views/partials/modals_staff.php';
require_once 'views/partials/footer2.php'; 
?>