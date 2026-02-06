// Gallery functionality
let allArtworks = [];

// Initialize gallery after content is loaded
function initializeGallery() {
    // Reset artworks array
    allArtworks = [];

    // Load categories and gallery
    loadCategories();
    loadGallery();

    // Bind search functionality
    $(document).off('input', '#searchInput').on('input', '#searchInput', function () {
        filterArtworks();
    });

    // Bind category filter functionality
    $(document).off('change', '#categoryFilter').on('change', '#categoryFilter', function () {
        filterArtworks();
    });

    // Bind modal close on background click
    $(document).off('click', '#artworkModal').on('click', '#artworkModal', function (e) {
        if ($(e.target).is('#artworkModal')) {
            closeModal();
        }
    });
}

// Load categories for filter
function loadCategories() {
    $.ajax({
        url: "../ws/WsCategories.php",
        method: "GET",
        dataType: "json",
        success: function (data) {
            const select = $('#categoryFilter');
            select.empty();
            select.append('<option value="">All Categories</option>');

            if (data && data.length > 0) {
                data.forEach(function (category) {
                    select.append(
                        `<option value="${category.id}">${escapeHtml(category.name)}</option>`
                    );
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('Error loading categories:', error);
        }
    });
}

// Load gallery artworks
function loadGallery() {
    $.ajax({
        url: "../ws/WsPublicGallery.php",
        method: "GET",
        dataType: "json",
        success: function (data) {
            allArtworks = data || [];
            displayGallery(allArtworks);
        },
        error: function (xhr, status, error) {
            console.error('Error loading gallery:', error);
            $('#galleryGrid').html(`
                <div class="no-artworks">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p>Failed to load artworks. Please try again later.</p>
                </div>
            `);
        }
    });
}

// Filter artworks based on search term and category
function filterArtworks() {
    const searchTerm = $('#searchInput').val().toLowerCase();
    const categoryId = $('#categoryFilter').val();

    let filtered = allArtworks;

    // Filter by search term
    if (searchTerm) {
        filtered = filtered.filter(function (artwork) {
            return (
                (artwork.title && artwork.title.toLowerCase().includes(searchTerm)) ||
                (artwork.artist_name && artwork.artist_name.toLowerCase().includes(searchTerm)) ||
                (artwork.description && artwork.description.toLowerCase().includes(searchTerm))
            );
        });
    }

    // Filter by category
    if (categoryId) {
        filtered = filtered.filter(function (artwork) {
            return artwork.category_id == categoryId;
        });
    }

    displayGallery(filtered);
}

// Display artworks in the gallery grid
function displayGallery(artworks) {
    const grid = $('#galleryGrid');
    grid.empty();

    if (!artworks || artworks.length === 0) {
        grid.html(`
            <div class="no-artworks">
                <i class="bi bi-image"></i>
                <p>No artworks available at the moment.</p>
            </div>
        `);
        return;
    }

    artworks.forEach(function (artwork) {
        const hasImage = artwork.image_path && artwork.image_path.trim() !== '';
        const imagePath = hasImage
            ? `../${artwork.image_path}`
            : '../artworks/placeholder.jpg';

        const frame = $(`
            <div class="artwork-frame">
                <div class="artwork-image-container">
                    ${
                        hasImage
                            ? `<img src="${imagePath}" alt="${escapeHtml(artwork.title)}" class="artwork-image" onerror="this.src='../artworks/placeholder.jpg'">`
                            : `<div class="artwork-image" style="display:flex;align-items:center;justify-content:center;font-size:4rem;color:#333;">
                                   <i class="bi bi-image"></i>
                               </div>`
                    }
                </div>
                <div class="artwork-info">
                    <div class="artwork-title">${escapeHtml(artwork.title)}</div>
                    <div class="artwork-artist">
                        <i class="bi bi-person-fill"></i>
                        <span>${escapeHtml(artwork.artist_name || 'Unknown Artist')}</span>
                    </div>
                </div>
            </div>
        `);

        frame.data('artwork', artwork);
        frame.on('click', function () {
            showArtworkDetail($(this).data('artwork'));
        });

        grid.append(frame);
    });
}

// Show artwork detail in modal
function showArtworkDetail(artwork) {
    const imagePath = artwork.image_path
        ? `../${artwork.image_path}`
        : '../artworks/placeholder.jpg';

    $('#modalImage').attr('src', imagePath);
    $('#modalTitle').text(artwork.title || '');
    $('#modalArtist').text(artwork.artist_name || 'Unknown Artist');
    $('#modalCategory').text(artwork.category_name || 'Uncategorized');
    $('#modalYear').text(artwork.year_created || 'N/A');
    $('#modalDescription').text(artwork.description || 'No description available');

    $('#artworkModal').addClass('active');
}

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
