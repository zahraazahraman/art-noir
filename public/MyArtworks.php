<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
?>

<!-- My Artworks Hero Section -->
<section class="hero-section">
    <h1 class="hero-title">My Artworks</h1>
    <p class="hero-description">View and manage your submitted artworks</p>
</section>

<!-- My Artworks Content -->
<section class="gallery-container">
    <div class="container">
        <!-- Action Bar -->
        <div class="artwork-controls">
            <div class="left-controls">
                <button class="btn-filter active" data-status="all">
                    <i class="bi bi-grid-3x3-gap"></i> All
                </button>
                <button class="btn-filter" data-status="Approved">
                    <i class="bi bi-check-circle"></i> Approved
                </button>
                <button class="btn-filter" data-status="Pending">
                    <i class="bi bi-clock-history"></i> Pending
                </button>
                <button class="btn-filter" data-status="Rejected">
                    <i class="bi bi-x-circle"></i> Rejected
                </button>
            </div>
            <div class="right-controls">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchArtworks" placeholder="Search your artworks...">
                </div>
                <button class="btn-action" id="createNewArtworkBtn">
                    <i class="bi bi-plus-circle"></i> Create New
                </button>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="artworks-stats">
            <div class="stat-item">
                <div class="stat-value" id="totalCount">0</div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-item stat-approved">
                <div class="stat-value" id="approvedCount">0</div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-item stat-pending">
                <div class="stat-value" id="pendingCount">0</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-item stat-rejected">
                <div class="stat-value" id="rejectedCount">0</div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        <!-- Artworks Grid -->
        <div class="gallery-grid" id="myArtworksGrid">
            <div class="no-artworks">
                <i class="bi bi-hourglass-split"></i>
                <p>Loading your artworks...</p>
            </div>
        </div>
    </div>
</section>

<script src="../JS/my-artworks.js"></script>
