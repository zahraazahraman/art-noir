<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
requireAdmin(); // Only admins can access this page
?>

<!-- Notifications & Messages Management Page -->
<section class="notifications-page">
    <div class="container">
        <div class="section-header">
            <h2><i class="bi bi-bell-fill"></i> Notifications & Messages</h2>
        </div>

        <div class="notifications-controls">
            <div class="type-tabs">
                <button class="btn btn-tab active" data-type="notifications">
                    <i class="bi bi-bell"></i> System Notifications
                    <span class="tab-count" id="notifCount">0</span>
                </button>
                <button class="btn btn-tab" data-type="messages">
                    <i class="bi bi-envelope"></i> User Messages
                    <span class="tab-count" id="msgCount">0</span>
                </button>
            </div>
            
            <div class="filter-buttons">
                <button class="btn btn-filter active" data-filter="all">
                    <i class="bi bi-list-ul"></i> All
                </button>
                <button class="btn btn-filter" data-filter="unread">
                    <i class="bi bi-envelope-fill"></i> Unread Only
                </button>
            </div>
            
            <button class="btn btn-mark-all" id="btnMarkAllRead">
                <i class="bi bi-check-all"></i> Mark All as Read
            </button>
        </div>

        <div class="notifications-list" id="notificationsList">
            <div class="no-notifications">
                <i class="bi bi-hourglass-split"></i>
                <p>Loading...</p>
            </div>
        </div>
    </div>
</section>
