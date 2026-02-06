<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
requireAdmin(); // Only admins can access this page
?>

<!-- Messages Management Section -->
<section class="admin-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2 class="admin-title">
                            <i class="bi bi-envelope"></i> Contact Messages
                        </h2>
                        <div class="admin-actions">
                            <button class="btn-admin-refresh" onclick="loadMessages()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <div class="admin-card-body">
                        <div class="table-responsive">
                            <table class="admin-table" id="messagesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sender</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="messagesTableBody">
                                    <!-- Messages will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <div id="noMessagesMessage" class="text-center py-5" style="display: none;">
                            <i class="bi bi-envelope-x" style="font-size: 3rem; color: #6c757d;"></i>
                            <h4 class="mt-3">No Messages Yet</h4>
                            <p class="text-muted">Contact form messages will appear here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Message Detail Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalTitle">Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <!-- Message details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnMarkAsRead">Mark as Read</button>
                <button type="button" class="btn btn-success" id="btnMarkAsReplied">Mark as Replied</button>
            </div>
        </div>
    </div>
</div>