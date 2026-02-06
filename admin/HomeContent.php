<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
requireAdmin(); // Only admins can access this page
?>

<div class="hero-content" id="heroContent">
    <div class="welcome-badge">Welcome back!</div>
    <h1>Art Noir</h1>
    <p>Discover, Manage & Celebrate Artistic Excellence</p>
    <p style="font-size: 1rem; color: #999;">
        Logged in as: <strong style="color: var(--secondary-color);"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>
    </p>
</div>
