<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0" style="color: var(--primary-blue);">
            Welcome Back, <?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?>
        </h3>
        <p class="text-muted small">
            Student ID: <?= htmlspecialchars($_SESSION['library_card_no'] ?? 'N/A') ?> | 
            Department: <?= htmlspecialchars($_SESSION['department'] ?? 'N/A') ?>
        </p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-primary rounded-pill position-relative">
            <i class="fa-regular fa-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
        </button>
        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
            <strong>
                <?= strtoupper(implode('', array_map(fn($w) => $w[0] ?? '', explode(' ', trim($_SESSION['full_name'] ?? ''))))) ?>
            </strong>
        </div>
    </div>
</div>