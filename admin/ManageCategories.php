<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
requireAdmin(); // Only admins can access this page
?>

<!-- Categories Management Section -->
<section class="categories-management">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header">
            <h2><i class="bi bi-tags-fill"></i> Categories Management</h2>
        </div>

        <!-- Controls Section -->
        <div class="controls-section">
            <div class="controls-row">
                <div class="search-box">
                    <input type="text" id="searchCategory" class="form-control" placeholder="Search categories by name...">
                </div>
                <button class="btn btn-secondary" id="btnShowAllCategories">
                    <i class="bi bi-arrow-clockwise"></i> Show All
                </button>
                <button class="btn btn-add-category" data-toggle="modal" data-target="#categoryModal" id="btnAddCategory">
                    <i class="bi bi-plus-circle"></i> Add Category
                </button>
            </div>
        </div>

        <div class="categories-grid" id="categoriesGrid">
            <div class="no-categories">
                <i class="bi bi-hourglass-split"></i>
                <p>Loading categories...</p>
            </div>
        </div>

        <!-- Pagination Section -->
        <div class="pagination-section">
            <div class="pagination-info">
                Showing <span id="showingStart">0</span> to <span id="showingEnd">0</span> of <span id="totalCategories">0</span> categories
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

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">
                    <i class="bi bi-plus-circle"></i> Add Category
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="categoryFormContainer">
                    <input type="hidden" id="categoryId">
                    
                    <div class="form-group">
                        <label for="categoryName" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="categoryName" placeholder="Enter category name (e.g., Painting, Sculpture, Photography)">
                        <small class="text-danger error-message" id="errorCategoryName"></small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveCategory">
                    <i class="bi bi-check-circle"></i> Save Category
                </button>
            </div>
        </div>
    </div>
</div>