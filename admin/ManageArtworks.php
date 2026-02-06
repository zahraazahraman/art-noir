<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
requireAdmin(); // Only admins can access this page
?>

<!-- Artworks Management Section -->
<section class="artworks-management">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header">
            <h2><i class="bi bi-image"></i> Artworks Gallery</h2>
        </div>

        <!-- Controls Section -->
        <div class="controls-section">
            <div class="controls-row">
                <div class="search-box">
                    <input type="text" id="searchArtwork" class="form-control" placeholder="Search by title, description, or artist...">
                </div>
                <div class="filter-box">
                    <select id="filterCategory" class="form-control">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="filter-box">
                    <select id="filterStatus" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <button class="btn btn-secondary" id="btnShowAllArtworks">
                    <i class="bi bi-arrow-clockwise"></i> Show All
                </button>
                <button class="btn btn-add-artwork" data-toggle="modal" data-target="#artworkModal" id="btnAddArtwork">
                    <i class="bi bi-plus-circle"></i> Add Artwork
                </button>
            </div>
        </div>
        
        <!-- Artworks Grid Section -->
        <div class="artworks-grid" id="artworksGrid">
            <div class="no-artworks">
                <i class="bi bi-hourglass-split"></i>
                <p>Loading artworks...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-section">
            <div class="pagination-info">
                Showing <span id="showingStart">0</span> to <span id="showingEnd">0</span> of <span id="totalArtworks">0</span> artworks
            </div>
            <div class="pagination-controls">
                <button class="pagination-btn" id="btnFirstPage">
                    <i class="bi bi-chevron-bar-left"></i>
                </button>
                <button class="pagination-btn" id="btnPrevPage">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="page-number">
                    Page <span id="currentPage">1</span> of <span id="totalPages">1</span>
                </div>
                <button class="pagination-btn" id="btnNextPage">
                     <i class="bi bi-chevron-right"></i>
                </button>
                <button class="pagination-btn" id="btnLastPage">
                     <i class="bi bi-chevron-bar-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-image-fill"></i> <span id="detailTitle"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="detailImage" class="detail-image" src="" alt="Artwork">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="detail-info">
                            <div class="detail-label"><i class="bi bi-person-fill"></i> Artist</div>
                            <div class="detail-value" id="detailArtist"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-info">
                            <div class="detail-label"><i class="bi bi-tag-fill"></i> Category</div>
                            <div class="detail-value" id="detailCategory"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-info">
                            <div class="detail-label"><i class="bi bi-calendar-fill"></i> Year Created</div>
                            <div class="detail-value" id="detailYear"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-info">
                            <div class="detail-label"><i class="bi bi-check-circle-fill"></i> Status</div>
                            <div class="detail-value" id="detailStatus"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="detail-info">
                            <div class="detail-label"><i class="bi bi-file-text-fill"></i> Description</div>
                            <div class="detail-value" id="detailDescription"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle"></i> 
                </button>
                <button type="button" class="btn btn-primary" id="btnEditFromDetail">
                    <i class="bi bi-pencil"></i> 
                </button>
                <button type="button" class="btn btn-danger" id="btnDeleteFromDetail">
                    <i class="bi bi-trash"></i> 
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Artwork Modal -->
<div class="modal fade" id="artworkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="artworkModalLabel">
                    <i class="bi bi-plus-circle"></i> Add Artwork
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="artworkFormContainer">
                    <input type="hidden" id="artworkId">
                    <input type="hidden" id="existingImagePath">
                    
                    <div class="form-group">
                        <label for="artworkTitle" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="artworkTitle" placeholder="Enter artwork title">
                        <small class="text-danger error-message" id="errorArtworkTitle"></small>
                    </div>

                    <div class="form-group">
                        <label for="artworkDescription" class="form-label">Description *</label>
                        <textarea class="form-control" id="artworkDescription" placeholder="Enter artwork description"></textarea>
                        <small class="text-danger error-message" id="errorArtworkDescription"></small>
                    </div>

                    <div class="form-group">
                        <label for="artworkImage" class="form-label">Image *</label>
                        <input type="file" name="image" class="form-control" id="artworkImage" accept="image/*">
                        <small class="text-danger error-message" id="errorArtworkImage"></small>
                        <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF (Max 5MB)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="artworkArtist" class="form-label">Artist *</label>
                                <select class="form-control" id="artworkArtist">
                                    <option value="">Select Artist</option>
                                </select>
                                <small class="text-danger error-message" id="errorArtworkArtist"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="artworkCategory" class="form-label">Category *</label>
                                <select class="form-control" id="artworkCategory">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="artworkYear" class="form-label">Year Created</label>
                        <select class="form-control" id="artworkYear">
                            <option value="">Select Year (Optional)</option>
                        </select>
                        <small class="text-danger error-message" id="errorArtworkYear"></small>
                    </div>

                    <div class="form-group">
                        <label for="artworkStatus" class="form-label">Status</label>
                        <select class="form-control" id="artworkStatus">
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveArtwork">
                    <i class="bi bi-check-circle"></i> Save Artwork
                </button>
            </div>
        </div>
    </div>
</div>
