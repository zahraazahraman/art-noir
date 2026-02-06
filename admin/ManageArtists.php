<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
requireAdmin(); // Only admins can access this page
?>

<!-- Artists Management Section -->
<section class="artists-management">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header">
            <h2><i class="bi bi-person-workspace"></i> Artists Management</h2>
        </div>

        <!-- Controls Section -->
        <div class="controls-section">
            <div class="controls-row">
                <div class="search-box">
                    <input type="text" id="searchArtist" class="form-control" placeholder="Search artists by name...">
                </div>
                <div class="filter-box">
                    <select id="filterArtistType" class="form-control">
                        <option value="">All Types</option>
                        <option value="historical">Historical</option>
                        <option value="community">Community</option>
                    </select>
                </div>
                <button class="btn btn-secondary" id="btnShowAllArtists">
                    <i class="bi bi-arrow-clockwise"></i> Show All
                </button>
                <button class="btn btn-add-artist" data-toggle="modal" data-target="#artistModal" id="btnAddArtist">
                    <i class="bi bi-plus-circle"></i> Add Artist
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Biography</th>
                        <th>Country</th>
                        <th>Birth Year</th>
                        <th>Death Year</th>
                        <th>Type</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="artistsTableBody">
                    <tr>
                        <td colspan="7" class="text-center">Loading artists...</td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-section">
                <div class="pagination-info">
                    Showing <span id="showingStart">0</span> to <span id="showingEnd">0</span> of <span id="totalArtists">0</span> artists
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" id="btnFirstPage">
                        <i class="bi bi-chevron-double-left"></i>
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
                        <i class="bi bi-chevron-double-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Artist Modal -->
<div class="modal fade" id="artistModal" tabindex="-1" role="dialog" aria-labelledby="artistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="artistModalLabel">
                    <i class="bi bi-person-plus"></i> Add Artist
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="artistFormContainer">
                    <input type="hidden" id="artistId">
                    
                    <div class="form-group">
                        <label for="artistName" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="artistName" placeholder="Enter artist name">
                        <small class="text-danger error-message" id="errorArtistName"></small>
                    </div>

                    <div class="form-group">
                        <label for="artistBiography" class="form-label">Biography *</label>
                        <textarea class="form-control" id="artistBiography" placeholder="Enter artist biography"></textarea>
                        <small class="text-danger error-message" id="errorArtistBiography"></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="artistCountry" class="form-label">Country *</label>
                                <select class="form-control" id="artistCountry">
                                    <option value="">Select Country</option>
                                </select>
                                <small class="text-danger error-message" id="errorArtistCountry"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="artistType" class="form-label">Artist Type *</label>
                                <select class="form-control" id="artistType">
                                    <option value="">Select Type</option>
                                    <option value="historical">Historical</option>
                                    <option value="community">Community</option>
                                </select>
                                <small class="text-danger error-message" id="errorArtistType"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="artistBirthYear" class="form-label">Birth Year</label>
                                <select class="form-control" id="artistBirthYear">
                                    <option value="">Select Birth Year (Optional)</option>
                                </select>
                                <small class="text-danger error-message" id="errorArtistBirthYear"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="artistDeathYear" class="form-label">Death Year</label>
                                <select class="form-control" id="artistDeathYear">
                                    <option value="">Select Death Year (Optional / Alive)</option>
                                </select>
                                <small class="text-danger error-message" id="errorArtistDeathYear"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveArtist">
                    <i class="bi bi-check-circle"></i> Save Artist
                </button>
            </div>
        </div>
    </div>
</div>