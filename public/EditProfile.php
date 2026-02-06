<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
?>

<!-- Edit Profile Hero Section -->
<section class="hero-section">
    <h1 class="hero-title">Edit Profile</h1>
</section>

<!-- Edit Profile Content -->

<section class="profile-section">
    <div class="container">
        <div class="row">
            <!-- Edit Profile Card -->
            <div class="col-lg-8 mx-auto">
                <div class="profile-card">
                    <h2 class="section-title">
                        <i class="bi bi-pencil-square"></i> Edit Profile Information
                    </h2>

                    <form id="editProfileForm">
                        <div class="form-group">
                            <label for="editUserName">
                                <i class="bi bi-person"></i> Full Name
                            </label>
                            <input type="text" class="form-control" id="editUserName" placeholder="Enter your full name">
                            <small class="text-danger error-message" id="errorEditUserName"></small>
                        </div>

                        <div class="form-group">
                            <label for="editUserEmail">
                                <i class="bi bi-envelope"></i> Email Address
                            </label>
                            <input type="email" class="form-control" id="editUserEmail" placeholder="Enter your email" readonly>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label for="editUserRole">
                                <i class="bi bi-shield"></i> Role
                            </label>
                            <input type="text" class="form-control" id="editUserRole" readonly>
                        </div>

                        <div class="form-group">
                            <label for="editUserState">
                                <i class="bi bi-circle-fill"></i> Account Status
                            </label>
                            <input type="text" class="form-control" id="editUserState" readonly>
                        </div>

                        <hr class="my-4">

                        <h3 class="section-title mb-3">
                            <i class="bi bi-key"></i> Change Password (Optional)
                        </h3>

                        <div class="form-group">
                            <label for="currentPassword">
                                <i class="bi bi-lock"></i> Current Password
                            </label>
                            <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password to change password">
                            <small class="text-danger error-message" id="errorCurrentPassword"></small>
                        </div>

                        <div class="form-group">
                            <label for="newPassword">
                                <i class="bi bi-lock-fill"></i> New Password
                            </label>
                            <input type="password" class="form-control" id="newPassword" placeholder="Enter new password">
                            <small class="text-danger error-message" id="errorNewPassword"></small>
                        </div>

                        <div class="form-group">
                            <label for="confirmNewPassword">
                                <i class="bi bi-lock-fill"></i> Confirm New Password
                            </label>
                            <input type="password" class="form-control" id="confirmNewPassword" placeholder="Confirm new password">
                            <small class="text-danger error-message" id="errorConfirmNewPassword"></small>
                        </div>

                        <div class="form-group d-flex gap-2 action-buttons">
                            <button type="button" class="btn btn-primary" id="btnSaveProfile">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnCancelEdit">
                                <i class="bi bi-arrow-left"></i> Back to Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>