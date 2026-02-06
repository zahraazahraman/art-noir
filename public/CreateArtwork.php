<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
?>

<!-- Create Artwork Hero Section -->
<section class="hero-section">
    <h1 class="hero-title">Create New Artwork</h1>
    <p class="hero-description">Share your masterpiece with the world</p>
</section>

<!-- Create Artwork Form -->
<section class="about-section">
    <div class="container">
        <div class="content-card">
            <h2 class="section-title">
                <i class="bi bi-image-fill"></i> Artwork Details
            </h2>
            
            <form id="createArtworkForm" enctype="multipart/form-data">
                <div class="row">
                    <!-- Artwork Image Upload -->
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <label>
                                <i class="bi bi-cloud-upload"></i> Artwork Image *
                            </label>
                            <div class="upload-area" id="uploadArea">
                                <div class="upload-content">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <p class="upload-text">Drag & drop your artwork here</p>
                                    <p class="upload-subtext">or click to browse</p>
                                    <input 
                                        type="file" 
                                        class="file-input" 
                                        id="artworkImage" 
                                        name="artworkImage" 
                                        accept="image/*"
                                        required>
                                </div>
                                <div class="image-preview" id="imagePreview" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview">
                                    <button type="button" class="btn-remove-image" id="removeImageBtn">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB. Recommended: 1920x1080px</small>
                        </div>
                    </div>

                    <!-- Artwork Title -->
                    <div class="col-md-8 mb-4">
                        <div class="form-group">
                            <label for="artworkTitle">
                                <i class="bi bi-text-left"></i> Artwork Title *
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="artworkTitle" 
                                name="artworkTitle" 
                                placeholder="Enter a captivating title"
                                required>
                        </div>
                    </div>

                    <!-- Year Created -->
                    <div class="col-md-4 mb-4">
                        <div class="form-group">
                            <label for="yearCreated">
                                <i class="bi bi-calendar3"></i> Year Created *
                            </label>
                            <select 
                                class="form-control" 
                                id="yearCreated" 
                                name="yearCreated" 
                                required>
                                <option value="">Select Year</option>
                                <!-- Years populated dynamically -->
                            </select>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="col-md-6 mb-4">
                        <div class="form-group">
                            <label for="artworkCategory">
                                <i class="bi bi-tag-fill"></i> Category *
                            </label>
                            <select class="form-control" id="artworkCategory" name="artworkCategory" required>
                                <option value="">Select category</option>
                                <!-- Categories loaded dynamically -->
                            </select>
                        </div>
                    </div>

                    <!-- Artist (Hidden - auto-filled) -->
                    <input type="hidden" id="artistId" name="artistId">

                    <!-- Description -->
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <label for="artworkDescription">
                                <i class="bi bi-card-text"></i> Description *
                            </label>
                            <textarea 
                                class="form-control" 
                                id="artworkDescription" 
                                name="artworkDescription" 
                                rows="5" 
                                placeholder="Describe your artwork, the inspiration behind it, techniques used, and what makes it special..."
                                required></textarea>
                            <small class="form-text">Minimum 50 characters. Help viewers connect with your work.</small>
                        </div>
                    </div>
                </div>

                <!-- Submission Info Box -->
                <div class="info-box mb-4">
                    <i class="bi bi-info-circle-fill"></i>
                    <div>
                        <strong>Submission Process:</strong> Your artwork will be submitted for review. 
                        Our team will carefully evaluate it based on quality, originality, and alignment with Art Noir standards. 
                        You'll be notified once the review is complete. Approved artworks will appear in the public gallery.
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="btnCancelAddArtwork">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </button>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="bi bi-check-circle"></i> Submit Artwork
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="../JS/create-artwork.js"></script>
