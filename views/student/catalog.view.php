<?php
    require_once 'model/Database.php';

   /*  // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.html');
        exit;
    }
 */
    $user_id = $_SESSION['user_id'];
    $search = trim($_GET['q'] ?? '');
    $category_filter = (int)($_GET['category'] ?? 0);

// Handle Hold Request Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_hold_book_id'])) {
    $book_id = (int)$_POST['request_hold_book_id'];

    // Check if book has available copies
    $stmtCheck = $db->checkExist("SELECT available_copies FROM books WHERE book_id = :book_id", [':book_id' => $book_id]);
    $book = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($book && $book['available_copies'] > 0) {
        // Prevent duplicate pending holds for the same book
        $stmtExisting = $db->checkExist(
            "SELECT hold_id FROM holds WHERE user_id = :user_id AND book_id = :book_id AND status = 'pending'",
            [':user_id' => $user_id, ':book_id' => $book_id]
        );

        if ($stmtExisting->rowCount() === 0) {
            $db->checkExist(
                "INSERT INTO holds (book_id, user_id, status) VALUES (:book_id, :user_id, 'pending')",
                [':book_id' => $book_id, ':user_id' => $user_id]
            );
            $_SESSION['flash_msg'] = "Hold request submitted successfully! Check your Hold Requests tab.";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_msg'] = "You already have an active hold request for this book.";
            $_SESSION['flash_type'] = "warning";
        }
    } else {
        $_SESSION['flash_msg'] = "Sorry, this book is currently out of stock.";
        $_SESSION['flash_type'] = "danger";
    }
   

    header('Location: ' . $_SERVER['PHP_SELF'] . ($search ? "?q=" . urlencode($search) : ''));
    exit;
}

// Fetch categories for filter dropdown
$stmtCategories = $db->checkExist("SELECT category_id, category_name FROM categories ORDER BY category_name ASC", []);
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Build SQL search query
$params = [];
$sqlBooks = "
    SELECT 
        b.book_id,
        b.isbn,
        b.title,
        b.author,
        b.publisher,
        b.shelf_location,
        b.available_copies,
        b.status,
        c.category_name
    FROM books b
    LEFT JOIN categories c ON b.category_id = c.category_id
    WHERE 1=1
";

if (!empty($search)) {
    $sqlBooks .= " AND (b.title LIKE :search OR b.author LIKE :search OR b.isbn LIKE :search)";
    $params[':search'] = "%{$search}%";
}

if ($category_filter > 0) {
    $sqlBooks .= " AND b.category_id = :category_id";
    $params[':category_id'] = $category_filter;
}

$sqlBooks .= " ORDER BY b.title ASC";
$stmtBooks = $db->checkExist($sqlBooks, $params);
$books = $stmtBooks->fetchAll(PDO::FETCH_ASSOC);

// Page Metadata
$pageTitle = "Search Book Catalog | SmartLib LMS";
$currentPage = "catalog";

require_once 'views/partials/header1.php';
require_once 'views/partials/sidebar1.php';
?>

<!-- Main Content Area -->
<div class="main-content">
    <?php require_once 'views/partials/topbar1.php'; ?>

    <!-- Flash Notifications -->
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

    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
        <h4 class="fw-bold mb-3" style="color: var(--primary-blue);">
            <i class="fa-solid fa-magnifying-glass me-2"></i>Library Book Catalog
        </h4>

        <!-- Search & Filter Form -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control" placeholder="Search by Title, Author, or ISBN..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= $category_filter === (int)$cat['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary fw-bold">Search</button>
            </div>
        </form>

        <!-- Books Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ISBN</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Shelf Location</th>
                        <th>Availability</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($books)): ?>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($book['isbn']) ?></code></td>
                                <td class="fw-bold"><?= htmlspecialchars($book['title']) ?></td>
                                <td><?= htmlspecialchars($book['author']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($book['category_name'] ?? 'General') ?></span></td>
                                <td><code><?= htmlspecialchars($book['shelf_location']) ?></code></td>
                                <td>
                                    <?php if ((int)$book['available_copies'] > 0): ?>
                                        <span class="badge bg-success"><?= $book['available_copies'] ?> Available</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ((int)$book['available_copies'] > 0): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="request_hold_book_id" value="<?= $book['book_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="fa-solid fa-bookmark me-1"></i> Request Hold
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary rounded-pill" disabled>Unavailable</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No books found matching your search.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'views/partials/footer1.php'; ?>
use PDO;
