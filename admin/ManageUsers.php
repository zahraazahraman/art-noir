<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
requireAdmin(); // Only admins can access this page
?>

<!-- Users Management Section -->
<section class="users-management">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header">
            <h2><i class="bi bi-people-fill"></i> Users Management</h2>
        </div>


        <!-- Controls Section -->
        <div class="controls-section">
            <div class="controls-row">
                <div class="search-box">
                    <input type="text" id="searchUser" class="form-control" placeholder="Search users by name or email...">
                </div>
                <div class="filter-box">
                    <select id="filterRole" class="form-control">
                        <option value="">All Roles</option>
                        <option value="Admin">Admin</option>
                        <option value="User">User</option>
                    </select>
                </div>
                <div class="filter-box">
                    <select id="filterState" class="form-control">
                        <option value="">All States</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <button class="btn btn-secondary" id="btnShowAllUsers">
                    <i class="bi bi-arrow-clockwise"></i> Show All
                </button>
                <button class="btn btn-add-user" data-toggle="modal" data-target="#userModal" id="btnAddUser">
                    <i class="bi bi-plus-circle"></i> Add User
                </button>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>State</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr>
                        <td colspan="5" class="text-center">Loading users...</td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-section">
                <div class="pagination-info">
                    Showing <span id="showingStart">0</span> to <span id="showingEnd">0</span> of <span id="totalUsers">0</span> users
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
    </div>
</section>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">
                    <i class="bi bi-person-plus"></i> Add User
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="userFormContainer">
                    <input type="hidden" id="userId">
                    
                    <div class="form-group">
                        <label for="userName" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="userName" placeholder="Enter user name">
                        <small class="text-danger error-message" id="errorUserName"></small>
                    </div>

                    <div class="form-group">
                        <label for="userEmail" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="userEmail" placeholder="Enter email address">
                        <small class="text-danger error-message" id="errorUserEmail"></small>
                    </div>

                    <div class="form-group" id="passwordGroup">
                        <label for="userPassword" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="userPassword" placeholder="Enter password">
                        <small class="text-danger error-message" id="errorUserPassword"></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userRole" class="form-label">Role *</label>
                                <select class="form-control" id="userRole">
                                    <option value="">Select Role</option>
                                    <option value="Admin">Admin</option>
                                    <option value="User">User</option>
                                </select>
                                <small class="text-danger error-message" id="errorUserRole"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="userState" class="form-label">State *</label>
                                <select class="form-control" id="userState">
                                    <option value="">Select State</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <small class="text-danger error-message" id="errorUserState"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveUser">
                    <i class="bi bi-check-circle"></i> Save User
                </button>
            </div>
        </div>
    </div>
</div>
