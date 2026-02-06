<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
?>

<!-- Profile Hero Section -->
<section class="hero-section">
    <h1 class="hero-title">My Profile</h1>
</section>

<!-- Profile Content -->

<section class="profile-section">
    <div class="container">
        <div class="row">
            <!-- User Information Card -->
            <div class="col-lg-6 mb-4">
                <div class="profile-card">
                    <h2 class="section-title">
                        <i class="bi bi-person-circle"></i> User Information
                    </h2>
                    
                <form id="userInfoForm">
                    <div class="form-group">
                        <label for="userName">
                            <i class="bi bi-person"></i> Full Name
                        </label>
                        <input type="text" class="form-control" id="userName" readonly>
                    </div>

                    <div class="form-group">
                        <label for="userEmail">
                            <i class="bi bi-envelope"></i> Email Address
                        </label>
                        <input type="email" class="form-control" id="userEmail" readonly>
                    </div>

                    <div class="form-group">
                        <label for="userRole">
                            <i class="bi bi-shield"></i> Role
                        </label>
                        <input type="text" class="form-control" id="userRole" readonly>
                    </div>

                    <div class="form-group">
                        <label for="userState">
                            <i class="bi bi-circle-fill"></i> Account Status
                        </label>
                        <input type="text" class="form-control" id="userState" readonly>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary" id="btnEditProfile">
                            <i class="bi bi-pencil-square"></i> Edit Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Artist Profile Card -->
        <div class="col-lg-6 mb-4">
            <div class="profile-card" id="artistProfileCard">
                <!-- Content will be loaded dynamically -->
                <div class="text-center" style="padding: 2rem;">
                    <div class="spinner-border text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p style="color: #999; margin-top: 1rem;">Loading artist profile...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="action-buttons">
                <button id="createArtworkBtn" class="btn-action" style="display: none;">
                    <i class="bi bi-plus-circle"></i> Create New Artwork
                </button>
                <button id="viewMyArtworksBtn" class="btn-action" style="display: none;">
                    <i class="bi bi-images"></i> View My Artworks
                </button>
            </div>
        </div>
    </div>
</div>

</section>