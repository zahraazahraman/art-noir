<?php
// Initialize session (without forcing login since this is public)
ini_set('session.gc_maxlifetime', 600);
session_set_cookie_params(600);
session_start();

// Update last activity time if authenticated
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $_SESSION['LAST_ACTIVITY'] = time();
}

// Get user info from session
$isAuthenticated = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $isAuthenticated ? $_SESSION['username'] : '';
$userEmail = $isAuthenticated ? $_SESSION['email'] : '';
$userId = $isAuthenticated ? $_SESSION['user_id'] : null;

// Check if user is an artist
$isArtist = false;
if ($isAuthenticated && $userId) {
    require_once __DIR__ . "/../Models/ArtistModel.php";
    $artistModel = new Artist();
    $artist = $artistModel->getArtistByUserId($userId);
    $isArtist = $artist !== null;
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Noir - Gallery</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="../CSS/public-gallery.css">
<link rel="stylesheet" href="../CSS/contact.css">
<link rel="stylesheet" href="../CSS/about.css">
<link rel="stylesheet" href="../CSS/profile.css">
<link rel="stylesheet" href="../CSS/alert.css">
<link rel="stylesheet" href="../CSS/create-artist.css">
<link rel="stylesheet" href="../CSS/create-artwork.css">


</head>
<body>
    <!-- Public Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark public-navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-palette-fill"></i> Art Noir
            </a>


        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#publicNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#" data-page="gallery">
                        <i class="bi bi-house-fill"></i> Gallery
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-page="about">
                        <i class="bi bi-info-circle-fill"></i> About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-page="contact">
                        <i class="bi bi-envelope-fill"></i> Contact
                    </a>
                </li>
                <?php if ($isAuthenticated): ?>
                    <!-- Authenticated User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($userName); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-header">
                                <strong><?php echo htmlspecialchars($userName); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($userEmail); ?></small>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-page="profile">
                                <i class="bi bi-person-fill"></i> My Profile
                            </a>
                            <?php if ($isArtist): ?>
                            <a class="dropdown-item" href="#" data-page="my-artworks">
                                <i class="bi bi-images"></i> My Artworks
                            </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" id="logoutBtn">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <!-- Non-authenticated Menu -->
                    <li class="nav-item">
                        <a href="../Login.php" class="btn btn-login">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Dynamic Content Container -->
<div id="contentContainer"></div>

<!-- Artwork Detail Modal (only for gallery) -->
<div class="artwork-modal" id="artworkModal">
    <div class="modal-content-3d">
        
        <img id="modalImage" class="modal-artwork-image" src="" alt="">
        
        <div class="modal-artwork-details">
            <h2 class="modal-artwork-title" id="modalTitle"></h2>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="bi bi-person-fill"></i> Artist
                </div>
                <div class="detail-value" id="modalArtist"></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="bi bi-tag-fill"></i> Category
                </div>
                <div class="detail-value" id="modalCategory"></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">
                    <i class="bi bi-calendar-fill"></i> Year
                </div>
                <div class="detail-value" id="modalYear"></div>
            </div>
            
            <div class="detail-row" style="border-bottom: none;">
                <div class="detail-label">
                    <i class="bi bi-file-text-fill"></i> Description
                </div>
                <div class="detail-value" id="modalDescription"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass PHP variables to JavaScript
    const isAuthenticated = <?php echo json_encode($isAuthenticated); ?>;
    const userId = <?php echo json_encode($userId); ?>;
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="../JS/alert.js"></script>
<script src="../JS/main.js"></script>
<script src="../JS/public-gallery.js"></script>
<script src="../JS/contact.js"></script>
<script src="../JS/profile.js"></script>
<script src="../JS/edit-profile.js"></script>
<script src="../JS/logout.js"></script>

</body>
</html>