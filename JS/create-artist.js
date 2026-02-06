// Load countries for dropdown
function loadCountries() {
    $.ajax({
        url: '../ws/WsCountries.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            const select = $('#artistCountry');
            select.find('option:not(:first)').remove();
            
            if (data && data.length > 0) {
                data.forEach(function(country) {
                    select.append(`<option value="${country.id}">${country.name}</option>`);
                });
            }
        },
        error: function() {
            showError('Failed to load countries. Please refresh the page.');
        }
    });
}

// Populate birth year dropdown
function populateBirthYearDropdown() {
    const currentYear = new Date().getFullYear();
    const yearSelect = $('#artistBirthYear');

    // Clear existing options except the first
    yearSelect.find('option:not(:first)').remove();

    // Populate years from current year down to 1900
    for (let year = currentYear; year >= 1900; year--) {
        yearSelect.append(`<option value="${year}">${year}</option>`);
    }
}

// Bind form submission
function bindFormSubmit() {
    $('#createArtistForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        const formData = {
            action: 'create',
            name: $('#artistName').val().trim(),
            biography: $('#artistBiography').val().trim(),
            country_id: $('#artistCountry').val(),
            birth_year: $('#artistBirthYear').val(),
            artist_type: $('#artistType').val(),
            user_id: null // Will be set by backend from session
        };
        
        // Disable submit button
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Creating Profile...');
        
        $.ajax({
            url: '../ws/WsCreateArtist.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message, function() {
                        // Redirect to profile page
                        window.loadContent('profile');
                    });
                } else {
                    showError(response.message);
                    submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Create Artist Profile');
                }
            },
            error: function(xhr) {
                let message = 'Failed to create artist profile. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showError(message);
                submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Create Artist Profile');
            }
        });
    });
}

// Form validation
function validateForm() {
    const name = $('#artistName').val().trim();
    const biography = $('#artistBiography').val().trim();
    const country = $('#artistCountry').val();
    const birthYear = $('#artistBirthYear').val();
    
    // Name validation
    if (name.length < 2) {
        showWarning('Artist name must be at least 2 characters long.');
        $('#artistName').focus();
        return false;
    }
    
    if (name.length > 100) {
        showWarning('Artist name must not exceed 100 characters.');
        $('#artistName').focus();
        return false;
    }
    
    // Biography validation
    if (biography.length < 50) {
        showWarning('Biography must be at least 50 characters long. Help us understand your artistic journey!');
        $('#artistBiography').focus();
        return false;
    }
    
    if (biography.length > 1000) {
        showWarning('Biography must not exceed 1000 characters.');
        $('#artistBiography').focus();
        return false;
    }
    
    // Country validation
    if (!country) {
        showWarning('Please select your country.');
        $('#artistCountry').focus();
        return false;
    }
    
    // Birth year validation
    if (!birthYear) {
        showWarning('Please select your birth year.');
        $('#artistBirthYear').focus();
        return false;
    }
    
    const birthYearNum = parseInt(birthYear);
    const currentYear = new Date().getFullYear();
    
    if (birthYearNum < 1900 || birthYearNum > currentYear) {
        showWarning(`Birth year must be between 1900 and ${currentYear}.`);
        $('#artistBirthYear').focus();
        return false;
    }
    
    return true;
}

// Initialize function for dynamic loading
function initializeCreateArtist() {
    loadCountries();
    populateBirthYearDropdown();
    bindFormSubmit();
    
    // Bind character counter
    $('#artistBiography').on('input', function() {
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
    $(document).on('click', '#btnCancelAddArtist', function () {
        loadContent('profile');
    });


}
