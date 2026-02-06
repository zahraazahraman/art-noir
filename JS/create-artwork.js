// Load categories for dropdown
function loadCategories() {
    $.ajax({
        url: '../ws/WsCategories.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            const select = $('#artworkCategory');
            select.find('option:not(:first)').remove();
            
            if (data && data.length > 0) {
                data.forEach(function(category) {
                    select.append(`<option value="${category.id}">${category.name}</option>`);
                });
            }
        },
        error: function() {
            showError('Failed to load categories. Please refresh the page.');
        }
    });
}

// Load artist info and set artist ID
function loadArtistInfo() {
    $.ajax({
        url: '../ws/WsProfile.php',
        method: 'GET',
        data: { getArtist: true },
        dataType: 'json',
        success: function(data) {
            if (data.success && data.artist) {
                $('#artistId').val(data.artist.id);
            } else {
                showError('Artist profile not found. Please create an artist profile first.', function() {
                    window.loadContent('profile');
                });
            }
        },
        error: function() {
            showError('Failed to load artist information.', function() {
                window.loadContent('profile');
            });
        }
    });
}

// Populate year dropdown
function populateYearDropdown() {
    const currentYear = new Date().getFullYear();
    const yearSelect = $('#yearCreated');

    // Clear existing options except the first
    yearSelect.find('option:not(:first)').remove();

    // Populate years from current year to 1900
    for (let year = currentYear; year >= 1900; year--) {
        yearSelect.append(`<option value="${year}">${year}</option>`);
    }

    // Set default to current year
    yearSelect.val(currentYear);
}

// Setup image upload with drag & drop
function setupImageUpload() {
    const uploadArea = $('#uploadArea');
    const fileInput = $('#artworkImage');
    const imagePreview = $('#imagePreview');
    const previewImg = $('#previewImg');
    const uploadContent = uploadArea.find('.upload-content');
    
    // Click to upload
    uploadArea.on('click', function(e) {
        if (!$(e.target).hasClass('btn-remove-image') && !$(e.target).parent().hasClass('btn-remove-image')) {
            fileInput.click();
        }
    });
    
    // File input change
    fileInput.on('change', function(e) {
        handleFile(e.target.files[0]);
    });
    
    // Drag & drop
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('drag-over');
    });
    
    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
    });
    
    uploadArea.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            fileInput[0].files = files;
            handleFile(files[0]);
        }
    });
    
    // Remove image
    $('#removeImageBtn').on('click', function(e) {
        e.stopPropagation();
        fileInput.val('');
        imagePreview.hide();
        uploadContent.show();
    });
}

// Handle file selection
function handleFile(file) {
    if (!file) return;
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        showWarning('Please select a valid image file (JPG, PNG, or GIF).');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showWarning('Image size must not exceed 5MB. Please choose a smaller file.');
        return;
    }
    
    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        $('#previewImg').attr('src', e.target.result);
        $('.upload-content').hide();
        $('#imagePreview').show();
    };
    reader.readAsDataURL(file);
}

// Bind form submission
function bindFormSubmit() {
    $('#createArtworkForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('title', $('#artworkTitle').val().trim());
        formData.append('description', $('#artworkDescription').val().trim());
        formData.append('artist_id', $('#artistId').val());
        formData.append('category_id', $('#artworkCategory').val());
        formData.append('year_created', $('#yearCreated').val());
        formData.append('image', $('#artworkImage')[0].files[0]);
        formData.append('status', 'Pending');
        
        // Disable submit button
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Uploading...');
        
        $.ajax({
            url: '../ws/WsArtworks.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message || 'Artwork submitted successfully! It will be reviewed by our team.', function() {
                        window.loadContent('my-artworks');
                    });
                } else {
                    showError(response.message || 'Failed to submit artwork.');
                    submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit Artwork');
                }
            },
            error: function(xhr) {
                let message = 'Failed to upload artwork. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showError(message);
                submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit Artwork');
            }
        });
    });
}

// Form validation
function validateForm() {
    const title = $('#artworkTitle').val().trim();
    const description = $('#artworkDescription').val().trim();
    const category = $('#artworkCategory').val();
    const yearCreated = $('#yearCreated').val();
    const image = $('#artworkImage')[0].files[0];
    const artistId = $('#artistId').val();
    
    // Artist ID validation
    if (!artistId) {
        showError('Artist profile not found. Please create an artist profile first.');
        return false;
    }
    
    // Image validation
    if (!image) {
        showWarning('Please select an image for your artwork.');
        return false;
    }
    
    // Title validation
    if (title.length < 3) {
        showWarning('Artwork title must be at least 3 characters long.');
        $('#artworkTitle').focus();
        return false;
    }
    
    if (title.length > 150) {
        showWarning('Artwork title must not exceed 150 characters.');
        $('#artworkTitle').focus();
        return false;
    }
    
    // Description validation
    if (description.length < 50) {
        showWarning('Description must be at least 50 characters. Help viewers appreciate your work!');
        $('#artworkDescription').focus();
        return false;
    }
    
    if (description.length > 1000) {
        showWarning('Description must not exceed 1000 characters.');
        $('#artworkDescription').focus();
        return false;
    }
    
    // Category validation
    if (!category) {
        showWarning('Please select a category for your artwork.');
        $('#artworkCategory').focus();
        return false;
    }
    
    // Year validation
    if (!yearCreated) {
        showWarning('Please select the year your artwork was created.');
        $('#yearCreated').focus();
        return false;
    }
    
    return true;
}

// Initialize function for dynamic loading
function initializeCreateArtwork() {
    loadCategories();
    loadArtistInfo();
    setupImageUpload();
    populateYearDropdown();
    bindFormSubmit();
    
    // Bind character counter
    $('#artworkDescription').on('input', function() {
        const length = $(this).val().length;
        const formText = $(this).siblings('.form-text');
        
        if (length < 50) {
            formText.text(`Minimum 50 characters. ${50 - length} more needed.`).css('color', '#dc3545');
        } else if (length > 1000) {
            formText.text(`Maximum 1000 characters. ${length - 1000} over limit!`).css('color', '#dc3545');
        } else {
            formText.text(`${length}/1000 characters. Looking good!`).css('color', '#28a745');
        }
    });

     // Bind cancel button
    $(document).on('click', '#btnCancelAddArtwork', function () {
        loadContent('my-artworks');
    });
}
