// Initialize global variables if not already set
window.currentFilter = window.currentFilter || 'all';
window.allArtworks = window.allArtworks || [];

function loadMyArtworks() {
    $.ajax({
        url: '../ws/WsMyArtworks.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                window.allArtworks = data.artworks || [];
                updateStats();
                displayArtworks(window.allArtworks);
            } else {
                showError(data.message || 'Failed to load artworks');
            }
        },
        error: function() {
            $('#myArtworksGrid').html(`
                <div class="no-artworks">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p>Failed to load artworks</p>
                </div>
            `);
        }
    });
}

// Update statistics counters
function updateStats() {
    const total = window.allArtworks.length;
    const approved = window.allArtworks.filter(a => a.status === 'Approved').length;
    const pending = window.allArtworks.filter(a => a.status === 'Pending').length;
    const rejected = window.allArtworks.filter(a => a.status === 'Rejected').length;
    
    $('#totalCount').text(total);
    $('#approvedCount').text(approved);
    $('#pendingCount').text(pending);
    $('#rejectedCount').text(rejected);
}

// Filter artworks based on current filter and search term
function filterArtworks() {
    const searchTerm = $('#searchArtworks').val().toLowerCase();
    
    let filtered = window.allArtworks;
    
    // Filter by status
    if (window.currentFilter !== 'all') {
        filtered = filtered.filter(artwork => artwork.status === window.currentFilter);
    }
    
    // Filter by search term
    if (searchTerm) {
        filtered = filtered.filter(artwork => 
            artwork.title.toLowerCase().includes(searchTerm) ||
            (artwork.description && artwork.description.toLowerCase().includes(searchTerm)) ||
            (artwork.category_name && artwork.category_name.toLowerCase().includes(searchTerm))
        );
    }
    
    displayArtworks(filtered);
}

// Display artworks in the grid
function displayArtworks(artworks) {
    const grid = $('#myArtworksGrid');
    grid.empty();
    
    if (!artworks || artworks.length === 0) {
        grid.html(`
                <button class="btn-action" onclick="window.loadContent('create-artwork')">
                    <i class="bi bi-plus-circle"></i> Create Your First Artwork
                </button>
        `);
        return;
    }
    
    artworks.forEach(function(artwork) {
        const statusClass = `status-${artwork.status.toLowerCase()}`;
        const imagePath = artwork.image_path ? `../${artwork.image_path}` : '../artworks/placeholder.jpg';
        
        const card = $(`
            <div class="artwork-card" data-id="${artwork.id}">
                <img src="${imagePath}" alt="${escapeHtml(artwork.title)}" class="artwork-image" onerror="this.src='../artworks/placeholder.jpg'">
                <div class="artwork-info">
                    <h3 class="artwork-title">${escapeHtml(artwork.title)}</h3>
                    <div class="artwork-meta">
                        <span class="artwork-category">
                            <i class="bi bi-tag"></i> ${escapeHtml(artwork.category_name || 'Uncategorized')}
                        </span>
                        <span class="artwork-year">
                            <i class="bi bi-calendar3"></i> ${artwork.year_created || 'N/A'}
                        </span>
                    </div>
                    <div class="artwork-status ${statusClass}">${artwork.status}</div>
                    <div class="artwork-actions">
                        <button class="btn-edit" data-id="${artwork.id}">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn-delete" data-id="${artwork.id}">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `);
        
        grid.append(card);
    });
    
    // Bind edit and delete buttons
    $('.btn-edit').on('click', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        editArtwork(id);
    });
    
    $('.btn-delete').on('click', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        deleteArtwork(id);
    });
}

// Edit artwork function (redirect to edit page)
function editArtwork(id) {
    // For now, redirect to create page with edit mode
    showInfo('Edit functionality coming soon! For now, please delete and recreate the artwork.');
}

function deleteArtwork(id) {
    const artwork = window.allArtworks.find(a => a.id == id);
    if (!artwork) return;
    
    showConfirm(
        'Delete Artwork',
        `Are you sure you want to delete "${artwork.title}"? This action cannot be undone.`,
        function() {
            $.ajax({
                url: '../ws/WsArtworks.php',
                method: 'POST',
                data: {
                    action: 'delete',
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSuccess('Artwork deleted successfully!');
                        loadMyArtworks(); // Reload the list
                    } else {
                        showError(response.message || 'Failed to delete artwork');
                    }
                },
                error: function() {
                    showError('Failed to delete artwork. Please try again.');
                }
            });
        }
    );
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Document ready
$(document).ready(function() {
    loadMyArtworks();
    
    // Create new artwork button
    $('#createNewArtworkBtn').on('click', function() {
        window.loadContent('create-artwork');
    });
    
    // Filter buttons
    $('.btn-filter').on('click', function() {
        $('.btn-filter').removeClass('active');
        $(this).addClass('active');
        window.currentFilter = $(this).data('status');
        filterArtworks();
    });
    
    // Search functionality
    $('#searchArtworks').on('input', function() {
        filterArtworks();
    });
});

// Initialize function for dynamic loading
function initializeMyArtworks() {
    loadMyArtworks();
    
    // Re-bind events if needed
    $('#createNewArtworkBtn').off('click').on('click', function() {
        window.loadContent('create-artwork');
    });
    
    $('.btn-filter').off('click').on('click', function() {
        $('.btn-filter').removeClass('active');
        $(this).addClass('active');
        window.currentFilter = $(this).data('status');
        filterArtworks();
    });
    
    $('#searchArtworks').off('input').on('input', function() {
        filterArtworks();
    });
}
