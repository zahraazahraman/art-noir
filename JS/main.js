// Main JavaScript for page navigation and content loading

let currentPage = 'gallery';

// Initialize on document ready
$(document).ready(function () {
    // Load default content
    loadContent('gallery');

    // Bind navigation click events using event delegation
    $(document).on('click', '.nav-link[data-page], .dropdown-item[data-page]', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadContent(page);
    });

    // Brand logo click
    $('.navbar-brand').on('click', function (e) {
        e.preventDefault();
        loadContent('gallery');
    });

    // CTA button in about page (event delegation for dynamic content)
    $(document).on('click', '.btn-cta[data-page]', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadContent(page);
    });

    // Close modal on ESC key
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $('#artworkModal').hasClass('active')) {
            closeModal();
        }
    });
});

// Load dynamic content
function loadContent(page) {
    currentPage = page;

    // Update active nav link
    $('.nav-link').removeClass('active');
    $(`.nav-link[data-page="${page}"]`).addClass('active');

    // Close mobile menu if open
    $('.navbar-collapse').collapse('hide');

    // Show loading indicator
    $('#contentContainer').html(`
        <div class="text-center" style="padding: 5rem 2rem;">
            <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Loading...</span>
            </div>
            <p style="color: #999; margin-top: 1rem;">Loading content...</p>
        </div>
    `);

    // Scroll to top
    $('html, body').animate({ scrollTop: 0 }, 300);

    // Select content file
    let contentFile = '';
    switch (page) {
        case 'gallery':
            contentFile = 'Gallery.php';
            break;
        case 'about':
            contentFile = 'About.php';
            break;
        case 'contact':
            contentFile = 'Contact.php';
            break;
        case 'profile':
            contentFile = 'Profile.php';
            break;
        case 'edit-profile':
            contentFile = 'EditProfile.php';
            break;
        case 'my-artworks':
            contentFile = 'MyArtworks.php';
            break;
        case 'create-artist':
            contentFile = 'CreateArtist.php';
            break;
        case 'create-artwork':
            contentFile = 'CreateArtwork.php';
            break;
        default:
            contentFile = 'Gallery.php';
    }

    // Load content via AJAX
    $.ajax({
        url: contentFile,
        method: 'GET',
        success: function (data) {
            $('#contentContainer').html(data);

            // Initialize page-specific functionality
            if (page === 'gallery') {
                initializeGallery();
            } else if (page === 'contact') {
                initializeContactForm();
            } else if (page === 'profile') {
                initializeProfile();
            } else if (page === 'edit-profile') {
                initializeEditProfile();
            } else if (page === 'my-artworks') {
                initializeMyArtworks();
            } else if (page === 'create-artist') {
                initializeCreateArtist();
            } else if (page === 'create-artwork') {
                initializeCreateArtwork();
            }
        },
        error: function (xhr, status, error) {
            console.error('Error loading content:', error);
            $('#contentContainer').html(`
                <div class="text-center" style="padding: 5rem 2rem;">
                    <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: #d4af37;"></i>
                    <h2 style="color: #d4af37; margin-top: 1rem;">Error Loading Content</h2>
                    <p style="color: #999;">Please try again later.</p>
                    <button onclick="loadContent('${page}')" class="btn btn-submit" style="margin-top: 1rem;">
                        <i class="bi bi-arrow-clockwise"></i> Retry
                    </button>
                </div>
            `);
        }
    });
}

// Close artwork modal
function closeModal() {
    $('#artworkModal').removeClass('active');
}
