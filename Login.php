<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Noir - Login</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="CSS/alert.css">
<link rel="stylesheet" href="CSS/login.css">

</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="bi bi-palette-fill"></i>
                </div>
                <h1 class="login-title">Art Noir</h1>
                <p class="login-subtitle">Welcome back! Please login to continue.</p>
            </div>

        <div id="loginFormContainer">
            <div class="role-selector">
                <div class="role-option active" data-role="Admin">
                    <i class="bi bi-shield-lock-fill"></i>
                    <div class="role-name">Admin</div>
                </div>
                <div class="role-option" data-role="User">
                    <i class="bi bi-person-fill"></i>
                    <div class="role-name">User</div>
                </div>
            </div>
            <input type="hidden" id="selectedRole" value="Admin">

            <div class="form-group">
                <label for="loginEmail" class="form-label">
                    <i class="bi bi-envelope-fill"></i> Email Address
                </label>
                <input type="email" class="form-control" id="loginEmail" placeholder="Enter your email">
                <small class="text-danger error-message" id="errorLoginEmail"></small>
            </div>

            <div class="form-group">
                <label for="loginPassword" class="form-label">
                    <i class="bi bi-lock-fill"></i> Password
                </label>
                <input type="password" class="form-control" id="loginPassword" placeholder="Enter your password">
                <small class="text-danger error-message" id="errorLoginPassword"></small>
            </div>

            <button type="button" class="btn btn-login" id="btnLogin">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </div>

        <div class="login-footer">
            <p style="margin-bottom: 0.5rem;">Don't have an account? <a href="Register.php" style="color: var(--secondary-color);">Register here</a></p>
            <p style="margin-bottom: 0.5rem;">Or <a href="public/index.php" style="color: var(--secondary-color);">Explore as Guest</a></p>
            &copy; 2025 Art Noir. All rights reserved.
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="JS/alert.js"></script>
<script src="JS/login.js"></script>

</body>
</html>