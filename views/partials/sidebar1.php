<!-- <div class="sidebar">
        <div class="px-4 mb-4">
            <h4 class="fw-bold"><i class="fa-solid fa-book-open-reader me-2"></i>SmartLib</h4>
            <span class="badge bg-info text-dark">Student Portal</span>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="/student-dashboard"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
            <a class="nav-link" href="/student-catalog"><i class="fa-lid fa-magnifying-glass me-2"></i> Search Catalog</a>
            <a class="nav-link" href="/student-loans"><i class="fa-solid fa-book me-2"></i> My Borrowed Books</a>
            <a class="nav-link" href="/student-holds"><i class="fa-solid fa-clock-rotate-left me-2"></i> Hold Requests</a>
            <a class="nav-link" href="/student-fines"><i class="fa-solid fa-receipt me-2"></i> My Fines</a>
            <hr class="text-light">
            <a class="nav-link text-danger" href="index.html"><i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out</a>
        </nav>
    </div> -->


    <div class="sidebar">
    <div class="px-4 mb-4">
        <h4 class="fw-bold"><i class="fa-solid fa-book-open-reader me-2"></i>SmartLib</h4>
        <span class="badge bg-info text-dark">Student Portal</span>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>" href="/student-dashboard">
            <i class="fa-solid fa-gauge me-2"></i> Dashboard
        </a>
        <a class="nav-link <?= ($currentPage ?? '') === 'catalog' ? 'active' : '' ?>" href="/student-catalog">
            <i class="fa-solid fa-magnifying-glass me-2"></i> Search Catalog
        </a>
        <a class="nav-link <?= ($currentPage ?? '') === 'loans' ? 'active' : '' ?>" href="/student-loans">
            <i class="fa-solid fa-book me-2"></i> My Borrowed Books
        </a>
        <a class="nav-link <?= ($currentPage ?? '') === 'holds' ? 'active' : '' ?>" href="/student-holds">
            <i class="fa-solid fa-clock-rotate-left me-2"></i> Hold Requests
        </a>
        <a class="nav-link <?= ($currentPage ?? '') === 'fines' ? 'active' : '' ?>" href="/student-fines">
            <i class="fa-solid fa-receipt me-2"></i> My Fines
        </a>
        <hr class="text-light">
        <a class="nav-link text-danger" href="/logout">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out
        </a>
    </nav>
</div>