<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartLib LMS | Automated Library Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #1B365D;
            --accent-blue: #2B6CB0;
            --light-bg: #F8FAFC;
            --dark-text: #2D3748;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            background-color: var(--light-bg);
        }

        /* Navbar Styling */
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-blue) !important;
            font-size: 1.4rem;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--accent-blue) 100%);
            color: white;
            padding: 100px 0 80px 0;
            clip-path: polygon(0 0, 100% 0, 100% 92%, 0 100%);
        }
        .hero-title {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.2;
        }

        /* Feature Cards */
        .feature-card {
            border: none;
            border-radius: 12px;
            background: #ffffff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(27, 54, 93, 0.15);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: rgba(43, 108, 176, 0.1);
            color: var(--accent-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        /* Quick Search Bar */
        .search-box {
            background: white;
            border-radius: 50px;
            padding: 8px 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .search-box input {
            border: none;
            outline: none;
            box-shadow: none !important;
        }

        /* Stats Counter */
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-blue);
        }

        /* Footer */
        footer {
            background-color: var(--primary-blue);
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fa-solid fa-book-open-reader me-2"></i>SmartLib LMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active me-3" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link me-3" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link me-3" href="#search">Catalog Search</a></li>
                    <li class="nav-item me-2">
                        <a href="#login" class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#loginModal">Sign In</a>
                    </li>
                    <li class="nav-item">
                        <a href="#login" class="btn btn-primary rounded-pill px-4" style="background-color: var(--primary-blue);" data-bs-toggle="modal" data-bs-target="#loginModal">Access Portal</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section id="home" class="hero-section d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-6">
                    <h1 class="hero-title mb-3">Modernizing Library Operations & Academic Discovery</h1>
                    <p class="lead mb-4 opacity-90">An automated, web-based platform streamlining circulation management, real-time catalog searching, self-service holds, and dynamic fine calculations.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#search" class="btn btn-light btn-lg rounded-pill px-4 fw-bold" style="color: var(--primary-blue);">
                            <i class="fa-solid fa-magnifying-glass me-2"></i>Search Books
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg rounded-pill px-4">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="reading-a-book.jpg" alt="Library Automation" class="img-fluid" style="max-height: 380px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Live Search Section -->
    <section id="search" class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold mb-3" style="color: var(--primary-blue);">Online Catalog Search</h2>
                    <p class="text-muted mb-4">Query book titles, authors, categories, or ISBNs in real time.</p>
                    
                    <div class="search-box d-flex align-items-center mb-4">
                        <i class="fa-solid fa-magnifying-glass text-muted ms-3 fs-5"></i>
                        <input type="text" id="bookSearchInput" class="form-control form-control-lg px-3" placeholder="Search by Title, Author, Category, or ISBN...">
                        <button id="searchBtn" class="btn btn-primary rounded-pill px-4" style="background-color: var(--accent-blue);">Search</button>
                    </div>
                </div>
            </div>

            <!-- Dynamic Search Results Table -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="searchResultsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ISBN</th>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Category</th>
                                            <th>Shelf Location</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bookTableBody">
                                        <!-- Pre-populated Sample Data -->
                                        <tr>
                                            <td><code>978-0131103627</code></td>
                                            <td class="fw-bold">The C Programming Language</td>
                                            <td>Brian W. Kernighan</td>
                                            <td>Computer Science</td>
                                            <td>Rack B-04</td>
                                            <td><span class="badge bg-success">Available (5)</span></td>
                                            <td><button class="btn btn-sm btn-outline-primary rounded-pill hold-btn">Hold Book</button></td>
                                        </tr>
                                        <tr>
                                            <td><code>978-0201633610</code></td>
                                            <td class="fw-bold">Design Patterns: Reusable Software</td>
                                            <td>Erich Gamma et al.</td>
                                            <td>Software Engineering</td>
                                            <td>Rack A-12</td>
                                            <td><span class="badge bg-success">Available (2)</span></td>
                                            <td><button class="btn btn-sm btn-outline-primary rounded-pill hold-btn">Hold Book</button></td>
                                        </tr>
                                        <tr>
                                            <td><code>978-0132350884</code></td>
                                            <td class="fw-bold">Clean Code: Agile Software Craftsmanship</td>
                                            <td>Robert C. Martin</td>
                                            <td>Software Engineering</td>
                                            <td>Rack A-08</td>
                                            <td><span class="badge bg-danger">Checked Out</span></td>
                                            <td><button class="btn btn-sm btn-secondary rounded-pill" disabled>Unavailable</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key System Features Section -->
    <section id="features" class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h6 class="text-uppercase fw-bold text-primary">System Capabilities</h6>
                <h2 class="fw-bold" style="color: var(--primary-blue);">Designed for Library Efficiency</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4">
                        <div class="feature-icon">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Role-Based Security</h5>
                        <p class="text-muted">Multi-level authorization for Administrators, Librarians, and Students with protected access controls.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4">
                        <div class="feature-icon">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Automated Circulation</h5>
                        <p class="text-muted">Instant recording of check-outs, returns, due dates, and self-service book reservations.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4">
                        <div class="feature-icon">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Dynamic Fine Engine</h5>
                        <p class="text-muted">Automatic daily penalty calculations for overdue items with transparent audit logs.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Stats Section -->
    <section class="py-5 text-white" style="background-color: var(--primary-blue);">
        <div class="container">
            <div class="row text-center gy-4">
                <div class="col-md-3">
                    <div class="stat-number text-white">24/7</div>
                    <p class="mb-0 opacity-75">Web Catalog Access</p>
                </div>
                <div class="col-md-3">
                    <div class="stat-number text-white">100%</div>
                    <p class="mb-0 opacity-75">Automated Fine Ledger</p>
                </div>
                <div class="col-md-3">
                    <div class="stat-number text-white">0%</div>
                    <p class="mb-0 opacity-75">Paper Record Dependency</p>
                </div>
                <div class="col-md-3">
                    <div class="stat-number text-white">&lt; 2s</div>
                    <p class="mb-0 opacity-75">Catalog Query Speed</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <!-- Login Modal -->
		<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content border-0 shadow">
								<div class="modal-header text-white" style="background-color: var(--primary-blue);">
										<h5 class="modal-title fw-bold"><i class="fa-solid fa-lock me-2"></i>Sign In to SmartLib Portal</h5>
										<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body p-4">
										<!-- Dynamic Alert Container -->
										<div id="loginAlert" class="alert d-none" role="alert"></div>

										<form id="ajaxLoginForm">
												<div class="mb-3">
														<label class="form-label font-weight-bold">Select Role</label>
														<select class="form-select" name="userRole" id="userRole" required>
																<option value="student">Student / Academic Patron</option>
																<option value="librarian">Librarian / Staff</option>
																<option value="administrator">System Administrator</option>
														</select>
												</div>
												<div class="mb-3">
														<label class="form-label">Email or Library ID</label>
														<input type="text" name="identity" class="form-control" placeholder="e.g., STU/2026/0142 or admin@smartlib.edu" required>
												</div>
												<div class="mb-3">
														<label class="form-label">Password</label>
														<input type="password" name="password" class="form-control" placeholder="••••••••" required>
												</div>
												<button type="submit" id="loginBtn" class="btn btn-primary w-100 py-2 rounded-pill" style="background-color: var(--primary-blue);">
														<span id="btnText">Login to Portal</span>
														<span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
												</button>
										</form>
								</div>
						</div>
				</div>
		</div>

    <!-- Footer -->
    <footer class="py-4 text-center">
        <div class="container">
            <p class="mb-0 small">&copy; 2026 SmartLib LMS. Web-Based Library Management System Project.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 & jQuery Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery Interactive Frontend Script -->
    <script>
        $(document).ready(function() {
            
            // Real-Time Table Filter Functionality
            $("#bookSearchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#bookTableBody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Smooth Scroll for Nav Links
            $("a.nav-link").on('click', function(event) {
                if (this.hash !== "") {
                    event.preventDefault();
                    var hash = this.hash;
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top - 70
                    }, 600);
                }
            });

            // Book Hold Button Interaction
            $(".hold-btn").on("click", function() {
                var bookTitle = $(this).closest("tr").find("td:nth-child(2)").text();
                alert("Reservation Request Submitted:\n'" + bookTitle + "' has been placed on hold. Please log in to complete your request.");
                $('#loginModal').modal('show');
            });

            // Login Form Submission Simulator
            $("#loginForm").on("submit", function(e) {
                e.preventDefault();
                var role = $("#userRole").val();
                alert("Login Successful! Redirecting to the " + role.toUpperCase() + " Dashboard...");
                $('#loginModal').modal('hide');
            });
        });
    </script>

		<script>
			$(document).ready(function () {
    $("#ajaxLoginForm").on("submit", function (e) {
        e.preventDefault();

        var $alert = $("#loginAlert");
        var $btn = $("#loginBtn");
        var $btnText = $("#btnText");
        var $btnSpinner = $("#btnSpinner");

        // UI Reset & Loading State
        $alert.addClass("d-none").removeClass("alert-danger alert-success").text("");
        $btn.prop("disabled", true);
        $btnText.text("Authenticating...");
        $btnSpinner.removeClass("d-none");

        $.ajax({
            url: "model/user.login.php",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $alert.removeClass("d-none").addClass("alert-success").text(response.message);
                    setTimeout(function () {
                        window.location.href = response.redirect;
                    }, 1200);
                } else {
                    $alert.removeClass("d-none").addClass("alert-danger").text(response.message);
                    $btn.prop("disabled", false);
                    $btnText.text("Login to Portal");
                    $btnSpinner.addClass("d-none");
                }
            },
            error: function () {
                $alert.removeClass("d-none").addClass("alert-danger").text("An unexpected server error occurred. Please try again.");
                $btn.prop("disabled", false);
                $btnText.text("Login to Portal");
                $btnSpinner.addClass("d-none");
            }
        });
    });
});

			/* $(document).ready(function() {
				$("#ajaxLoginForm").on("submit", function(e) {
					e.preventDefault();
					var formData = $(this).serialize();
					$("#loginBtn").attr("disabled", true);
					$("#btnText").text("Processing...");
					$("#btnSpinner").removeClass("d-none");
					$("#loginAlert").removeClass("alert-success alert-danger").addClass("d-none");

					$.ajax({
						url: "model/user.login.php",
						type: "POST",
						data: formData,
						dataType: "json",
						success: function(response) {
							if (response.success) {
								$("#loginAlert").removeClass("d-none alert-danger").addClass("alert-success").text(response.message);
								setTimeout(function() {
									window.location.href = response.redirect;
								}, 1500);
							} else {
								$("#loginAlert").removeClass("d-none alert-success").addClass("alert-danger").text(response.message);
							}
						},
						error: function() {
							$("#loginAlert").removeClass("d-none alert-success").addClass("alert-danger").text("An error occurred. Please try again.");
						},
						complete: function() {
							$("#loginBtn").attr("disabled", false);
							$("#btnText").text("Login to Portal");
							$("#btnSpinner").addClass("d-none");
						}
					});
				});
			}); */
			
		</script>
</body>
</html>