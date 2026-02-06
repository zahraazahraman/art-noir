let currentFilter = 'all';
let currentType = 'notifications'; // 'notifications' or 'messages'

// Load data based on current type and filter
function loadData() {
    if (currentType === 'notifications') {
        loadNotifications();
    } else {
        loadMessages();
    }
}

// Load system notifications
function loadNotifications() {
    const url = currentFilter === 'unread' 
        ? '../ws/WsNotifications.php?unread=true' 
        : '../ws/WsNotifications.php';

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            displayNotifications(data);
            updateCounts();
        },
        error: function() {
            showAlert('error', 'Error', 'Failed to load notifications');
        }
    });
}

// Load user messages
function loadMessages() {
    const url = currentFilter === 'unread' 
        ? '../ws/WsMessages.php?unread=true' 
        : '../ws/WsMessages.php';

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('Messages data:', data);
            displayMessages(data);
            updateCounts();
        },
        error: function(xhr, status, error) {
            console.error('Failed to load messages:', error);
            showAlert('error', 'Error', 'Failed to load messages');
        }
    });
}

// Display system notifications
function displayNotifications(notifications) {
    const list = $('#notificationsList');
    list.empty();

    if (!notifications || notifications.length === 0) {
        list.html(`
            <div class="no-notifications">
                <i class="bi bi-bell-slash"></i>
                <p>No system notifications found</p>
            </div>
        `);
        return;
    }

    notifications.forEach(function(notif) {
        const readClass = notif.is_read == 1 ? 'read' : '';
        const timeAgo = formatTimeAgo(notif.created_at);
        
        const item = $(`
            <div class="notification-item notification-type ${readClass}" data-id="${notif.id}">
                <div class="notification-header">
                    <div class="notification-left">
                        <div class="notification-icon">
                            <i class="bi bi-${getNotificationIcon(notif.type)}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${escapeHtml(notif.title)}</div>
                            <div class="notification-message">${escapeHtml(notif.message)}</div>
                        </div>
                    </div>
                    <div class="notification-time">
                        <i class="bi bi-clock"></i> ${timeAgo}
                    </div>
                </div>
                <div class="notification-actions">
                    ${notif.related_table && notif.related_id ? 
                        `<button class="btn-view" data-table="${notif.related_table}" data-id="${notif.related_id}">
                            <i class="bi bi-eye"></i> View Details
                        </button>` : ''}
                    ${notif.is_read == 0 ? 
                        `<button class="btn-dismiss" data-action="mark-read" data-type="notification">
                            <i class="bi bi-check"></i> Mark as Read
                        </button>` : ''}
                </div>
            </div>
        `);

        list.append(item);
    });

    bindActions();
}

// Display user messages
function displayMessages(messages) {
    const list = $('#notificationsList');
    list.empty();

    if (!messages || messages.length === 0) {
        list.html(`
            <div class="no-notifications">
                <i class="bi bi-envelope-slash"></i>
                <p>No user messages found</p>
            </div>
        `);
        return;
    }

    messages.forEach(function(msg) {
        const readClass = msg.is_read == 1 ? 'read' : '';
        const timeAgo = formatTimeAgo(msg.created_at);
        
        const item = $(`
            <div class="notification-item message-type ${readClass}" data-id="${msg.id}">
                    <div class="notification-header">
                        <div class="notification-left">
                            <div class="notification-icon">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div class="notification-content">
                                ${msg.subject ? `<div class="message-subject">${escapeHtml(msg.subject)}</div>` : ''}
                                <div class="notification-message">${escapeHtml(msg.content)}</div>
                                <div class="message-sender">
                                    <i class="bi bi-person-circle"></i>
                                    <strong>${escapeHtml(msg.sender_name)}</strong>
                                    <span>•</span>
                                    <a href="mailto:${escapeHtml(msg.sender_email)}">${escapeHtml(msg.sender_email)}</a>
                                </div>
                            </div>
                        </div>
                        <div class="notification-time">
                            <i class="bi bi-clock"></i> ${timeAgo}
                        </div>
                    </div>
                    <div class="notification-actions">
                        <button class="btn-reply" data-email="${escapeHtml(msg.sender_email)}">
                            <i class="bi bi-reply-fill"></i> Reply
                        </button>
                        ${msg.is_read == 0 ? 
                            `<button class="btn-dismiss" data-action="mark-read" data-type="message">
                                <i class="bi bi-check"></i> Mark as Read
                            </button>` : ''}
                    </div>
                </div>
            `);

            list.append(item);
        });

        bindActions();
    }

// Bind action buttons within notifications/messages
function bindActions() {
    // View related item (notifications only)
    $('.btn-view').on('click', function() {
        const table = $(this).data('table');
        const id = $(this).data('id');
        const notifId = $(this).closest('.notification-item').data('id');
        viewRelatedItem(table, id, notifId);
    });

    // Reply to message
    $('.btn-reply').on('click', function() {
        const email = $(this).data('email');
        window.location.href = `mailto:${email}`;
    });

    // Mark as read
    $('.btn-dismiss[data-action="mark-read"]').on('click', function(e) {
        e.stopPropagation();
        const itemId = $(this).closest('.notification-item').data('id');
        const type = $(this).data('type');
        markAsRead(itemId, type);
    });

}

// Get icon name based on notification type
function getNotificationIcon(type) {
    switch(type) {
        case 'artwork':
        case 'artwork_pending': return 'image';
        case 'new_user': 
        case 'user': return 'person-plus';
        case 'artwork_approved': return 'check-circle';
        case 'artwork_rejected': return 'x-circle';
        case 'artist': return 'palette';
        case 'category': return 'tags';
        default: return 'bell';
    }
}

// Format time ago
function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
    if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
    return date.toLocaleDateString();
}

// View related item
function viewRelatedItem(table, id, notifId) {
    // Mark as read first
    markAsRead(notifId, 'notification', false);

    // Load content instead of redirect
    if (table === 'artworks') {
        window.loadParams = { id: id };
        window.loadContent(`ManageArtworks.php`);
        $(".nav-link").removeClass("active");
        $("#manageArtworksLink").addClass("active");
    } else if (table === 'users') {
        window.loadContent(`ManageUsers.php?highlight=${id}`);
        $(".nav-link").removeClass("active");
        $("#manageUsersLink").addClass("active");
    }
}

// Mark item as read
function markAsRead(id, type, reload = true) {
    const url = type === 'notification' ? '../ws/WsNotifications.php' : '../ws/WsMessages.php';
    
    $.ajax({
        url: url,
        method: 'POST',
        data: {
            action: 'mark_read',
            id: id
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && reload) {
                loadData();
            }
        }
    });
}

// Mark all as read
function markAllAsRead() {
    const url = currentType === 'notifications' ? '../ws/WsNotifications.php' : '../ws/WsMessages.php';
    const itemName = currentType === 'notifications' ? 'notifications' : 'messages';
    
    $.ajax({
        url: url,
        method: 'POST',
        data: { action: 'mark_all_read' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                loadData();
                showAlert('success', 'Success', `All ${itemName} marked as read`);
            }
        }
    });
}

// Update notification and message counts
function updateCounts() {
    // Update notification count
    $.ajax({
        url: '../ws/WsNotifications.php?count=true',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            const count = data.count || 0;
            $('#notificationsBadge').text(count + ' Unread');
            $('#notifCount').text(count);
        }
    });

    // Update message count
    $.ajax({
        url: '../ws/WsMessages.php?count=true',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            const count = data.count || 0;
            $('#messagesBadge').text(count + ' Unread');
            $('#msgCount').text(count);
        }
    });
}


// Helper escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Auto-refresh counts every 30 seconds
setInterval(updateCounts, 30000);

// Document ready
$(document).ready(function() {
    loadData();
    updateCounts();

    // Type tabs (System Notifications vs User Messages)
    $(document).on('click', '.btn-tab', function() {
        $('.btn-tab').removeClass('active');
        $(this).addClass('active');
        currentType = $(this).data('type');
        loadData();
    });

    // Filter buttons (All vs Unread)
    $(document).on('click', '.btn-filter', function() {
        $('.btn-filter').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        loadData();
    });

    // Mark all as read
    $(document).on('click', '#btnMarkAllRead', function() {
        markAllAsRead();
    });

    // Auto-refresh counts every 30 seconds
    setInterval(updateCounts, 30000);
});

// Function to initialize notifications when content is loaded dynamically
function initializeNotifications() {
    loadData();
    updateCounts();
}
