// Profile page functionality

function initializeProfile() {
    loadUserInfo();
    loadArtistProfile();

    // Bind action buttons
    $(document).on('click', '#createArtistBtn', function () {
        loadContent('create-artist');
    });

    $(document).on('click', '#createArtworkBtn', function () {
        loadContent('create-artwork');
    });

    $(document).on('click', '#viewMyArtworksBtn', function () {
        loadContent('my-artworks');
    });

    // Edit Profile functionality
    $(document).on('click', '#btnEditProfile', function () {
        loadContent('edit-profile');
    });
}

// Load user information
function loadUserInfo() {
    $.ajax({
        url: "../ws/WsProfile.php",
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success && response.user) {
                const user = response.user;
                $('#userName').val(user.name);
                $('#userEmail').val(user.email);
                $('#userRole').val(user.role);
                $('#userState').val(user.state);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error loading user info:', error);
        }
    });
}

// Load artist profile information
function loadArtistProfile() {
    $.ajax({
        url: "../ws/WsProfile.php",
        method: "GET",
        data: { getArtist: true },
        dataType: "json",
        success: function (response) {
            if (response.success && response.artist) {
                displayArtistProfile(response.artist);
                $('#createArtworkBtn').show();
                $('#viewMyArtworksBtn').show();
            } else {
                displayNoArtistProfile();
            }
        },
        error: function (xhr, status, error) {
            console.error('Error loading artist profile:', error);
            displayNoArtistProfile();
        }
    });
}

// Display artist profile
function displayArtistProfile(artist) {
    const birthYear = artist.birth_year || 'N/A';
    const deathYear = artist.death_year || 'Present';
    const country = artist.country_name || 'Unknown';

    const html = `
        <h2 class="section-title">
            <i class="bi bi-palette"></i> Artist Profile
        </h2>
        <div class="artist-profile-content">
            <div class="artist-avatar">
                <i class="bi bi-brush-fill"></i>
            </div>
            <h3 class="artist-name">${escapeHtml(artist.name)}</h3>
            <span class="artist-type-badge">${escapeHtml(artist.artist_type)}</span>

            <div class="artist-info-item">
                <i class="bi bi-globe"></i>
                <span>${escapeHtml(country)}</span>
            </div>

            <div class="artist-info-item">
                <i class="bi bi-calendar"></i>
                <span>${birthYear} - ${deathYear}</span>
            </div>

            ${
                artist.biography
                    ? `
                <div class="artist-bio">
                    <h4><i class="bi bi-file-text"></i> Biography</h4>
                    <p>${escapeHtml(artist.biography)}</p>
                </div>`
                    : ''
            }
        </div>
    `;

    $('#artistProfileCard').html(html);
}

// Display no artist profile message
function displayNoArtistProfile() {
    const html = `
        <h2 class="section-title">
            <i class="bi bi-palette"></i> Artist Profile
        </h2>
        <div class="no-artist-profile">
            <i class="bi bi-easel"></i>
            <h3>No Artist Profile Yet</h3>
            <p>Create your artist profile to start uploading artworks and showcase your talent!</p>
            <button class="btn-create-artist" id="createArtistBtn">
                <i class="bi bi-plus-circle"></i> Create Artist Profile
            </button>
        </div>
    `;

    $('#artistProfileCard').html(html);
}

// Initialize My Artworks page
function initializeMyArtworks() {
    loadMyArtworks();

    $(document).on('click', '#createNewArtworkBtn', function () {
        loadContent('create-artwork');
    });
}

// Load user's artworks
function loadMyArtworks() {
    $.ajax({
        url: "../ws/WsMyArtworks.php",
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success && response.artworks) {
                displayMyArtworks(response.artworks);
            } else {
                displayNoArtworks();
            }
        },
        error: function (xhr, status, error) {
            console.error('Error loading artworks:', error);
            $('#myArtworksGrid').html(`
                <div class="no-artworks">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p>Failed to load artworks. Please try again later.</p>
                </div>
            `);
        }
    });
}

// Display user's artworks
function displayMyArtworks(artworks) {
    const grid = $('#myArtworksGrid');
    grid.empty();

    if (!artworks || artworks.length === 0) {
        displayNoArtworks();
        return;
    }

    artworks.forEach(function (artwork) {
        const hasImage = artwork.image_path && artwork.image_path.trim() !== '';
        const imagePath = hasImage
            ? `../${artwork.image_path}`
            : '../artworks/placeholder.jpg';

        let statusClass = '';
        if (artwork.status === 'Pending') statusClass = 'status-pending';
        else if (artwork.status === 'Approved') statusClass = 'status-approved';
        else if (artwork.status === 'Rejected') statusClass = 'status-rejected';

        const frame = $(`
            <div class="artwork-frame">
                <span class="artwork-status status-badge ${statusClass}">
                    ${artwork.status}
                </span>
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
                        <i class="bi bi-calendar"></i>
                        <span>${artwork.year_created || 'N/A'}</span>
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

// Display no artworks message
function displayNoArtworks() {
    $('#myArtworksGrid').html(`
        <div class="no-artworks">
            <i class="bi bi-image"></i>
            <p>You haven't created any artworks yet.</p>
            <button class="btn-action" onclick="loadContent('create-artwork')" style="margin-top:1rem;">
                <i class="bi bi-plus-circle"></i> Create Your First Artwork
            </button>
        </div>
    `);
}

// Helper function
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
