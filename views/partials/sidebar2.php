<div class="sidebar">
    <div class="px-4 mb-4">
        <h4 class="fw-bold"><i class="fa-solid fa-book-open-reader me-2"></i>SmartLib</h4>
        <span class="badge bg-warning text-dark">Librarian Desk</span>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>" href="/staff-dashboard">
            <i class="fa-solid fa-gauge me-2"></i> Overview
        </a>
        <a class="nav-link <?= ($currentPage ?? '') === 'circulation' ? 'active' : '' ?>" href="/staff-circulation">
            <i class="fa-solid fa-arrows-rotate me-2"></i> Issue / Return Desk
        </a>
        <a class="nav-link <?= ($currentPage ?? '') === 'inventory' ? 'active' : '' ?>" href="/staff-inventory">
            <i class="fa-solid fa-boxes-stacked me-2"></i> Book Inventory
        </a>
        <a class="nav-link <?= ($currentPage ?? '') === 'patrons' ? 'active' : '' ?>" href="/staff-patrons">
            <i class="fa-solid fa-users me-2"></i> Patron Management
        </a>
        <a class="nav-link <?= ($currentPage ?? '') === 'fines' ? 'active' : '' ?>" href="/staff-fines">
            <i class="fa-solid fa-calculator me-2"></i> Overdue & Fines
        </a>
        <a class="nav-link <?= ($currentPage ?? '') === 'reports' ? 'active' : '' ?>" href="/staff-reports">
            <i class="fa-solid fa-chart-line me-2"></i> Analytics Reports
        </a>
        <hr class="text-light">
        <a class="nav-link text-danger" href="/logout">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out
        </a>
    </nav>
</div>