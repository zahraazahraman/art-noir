<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
requireAdmin(); // Only admins can access this page
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Noir - Management</title>


<!-- Bootstrap 4 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<link rel="stylesheet" href="../CSS/home.css">
<link rel="stylesheet" href="../CSS/sections.css">
<link rel="stylesheet" href="../CSS/alert.css">
<link rel="stylesheet" href="../CSS/dashboard.css">


<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">


</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-palette-fill"></i> Art Noir
            </a>

        <!-- Toggler for mobile -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#" id="homeLink">
                        <i class="bi bi-house-fill"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="dashboardLink">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="manageUsersLink">
                        <i class="bi bi-people-fill"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="manageArtistsLink">
                        <i class="bi bi-person-workspace"></i> Artists
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="manageArtworksLink">
                        <i class="bi bi-image"></i> Artworks
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="manageCategoriesLink">
                        <i class="bi bi-tags-fill"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="notificationBell">
                        <i class="bi bi-bell-fill"></i>
                        <span class="badge badge-danger notification-badge" id="notificationCount" style="display: none;">0</span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <div class="dropdown-header">
                            <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($_SESSION['email']); ?></small><br>
                            <small class="text-muted">Role: <?php echo htmlspecialchars($_SESSION['role']); ?></small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="../public/index.php">
                            <i class="bi bi-eye"></i> Visit Gallery
                        </a>
                        <a class="dropdown-item" href="#" id="logoutBtn">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content" id="heroContent">
        <div class="welcome-badge">Welcome back!</div>
        <h1>Art Noir</h1>
        <p>Discover, Manage & Celebrate Artistic Excellence</p>
        <p style="font-size: 1rem; color: #999;">
            Logged in as: <strong style="color: var(--secondary-color);"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        </p>
    </div>
</section>

<!-- Bootstrap 4 JS Bundle -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS -->
<script src="../JS/home.js" defer></script>
<script src="../JS/logout.js" defer></script>
<script src="../JS/alert.js" defer></script>
<script src="../JS/artists.js" defer></script>
<script src="../JS/artworks.js" defer></script>
<script src="../JS/categories.js" defer></script>
<script src="../JS/users.js" defer></script>
<script src="../JS/notification.js" defer></script>
<script src="../JS/dashboard.js" defer></script>


</body>
</html>