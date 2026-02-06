<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
?>

<!-- Create Artist Profile Hero Section -->
<section class="hero-section">
    <h1 class="hero-title">Create Artist Profile</h1>
    <p class="hero-description">Build your artist identity and showcase your work</p>
</section>

<!-- Create Artist Form -->
<section class="about-section">
    <div class="container">
        <div class="content-card">
            <h2 class="section-title">
                <i class="bi bi-palette-fill"></i> Artist Information
            </h2>
            
            <form id="createArtistForm" enctype="multipart/form-data">
                <div class="row">
                    <!-- Artist Name -->
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="artistName">
                                <i class="bi bi-person-fill"></i> Artist Name *
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="artistName" 
                                name="artistName" 
                                placeholder="Enter your artist name"
                                required>
                            <small class="form-text">This is how you'll be known in the gallery</small>
                        </div>
                    </div>

                    <!-- Country -->
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="artistCountry">
                                <i class="bi bi-globe"></i> Country *
                            </label>
                            <select class="form-control" id="artistCountry" name="artistCountry" required>
                                <option value="">Select your country</option>
                                <!-- Countries loaded dynamically -->
                            </select>
                        </div>
                    </div>

                    <!-- Biography -->
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <label for="artistBiography">
                                <i class="bi bi-journal-text"></i> Biography *
                            </label>
                            <textarea 
                                class="form-control" 
                                id="artistBiography" 
                                name="artistBiography" 
                                rows="5" 
                                placeholder="Tell us about yourself, your artistic journey, style, and inspirations..."
                                required></textarea>
                            <small class="form-text">Minimum 50 characters. Help art lovers understand your work.</small>
                        </div>
                    </div>

                    <!-- Birth Year -->
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="artistBirthYear">
                                <i class="bi bi-calendar-check"></i> Birth Year *
                            </label>
                            <select 
                                class="form-control" 
                                id="artistBirthYear" 
                                name="artistBirthYear" 
                                required>
                                <option value="">Select Birth Year</option>
                                <!-- Years populated dynamically -->
                            </select>
                        </div>
                    </div>

                    <!-- Artist Type (Hidden) -->
                    <input type="hidden" id="artistType" name="artistType" value="community">
                </div>

                <!-- Info Box -->
                <div class="info-box mb-4">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        <strong>Important:</strong> Your artist profile will be reviewed by our team. 
                        Once approved, you'll be able to submit artworks to the gallery. 
                        Please ensure all information is accurate and professional.
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="btnCancelAddArtist">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </button>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="bi bi-check-circle"></i> Create Artist Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="../JS/create-artist.js"></script>
