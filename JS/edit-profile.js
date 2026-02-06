// Edit Profile page functionality

function initializeEditProfile() {
    loadUserInfoForEdit();

    // Bind save profile button
    $(document).on('click', '#btnSaveProfile', function () {
        if (validateEditProfileForm()) {
            saveProfile();
        }
    });

    // Bind cancel button
    $(document).on('click', '#btnCancelEdit', function () {
        loadContent('profile');
    });

    // Clear errors on input
    $(document).on('input', '#editUserName, #currentPassword, #newPassword, #confirmNewPassword', function () {
        $(this).removeClass('is-invalid');
        const errorId = '#error' + $(this).attr('id').charAt(0).toUpperCase() + $(this).attr('id').slice(1);
        $(errorId).text('');
    });
}

function loadUserInfoForEdit() {
    $.ajax({
        url: "../ws/WsProfile.php",
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success && response.user) {
                const user = response.user;
                $('#editUserName').val(user.name);
                $('#editUserEmail').val(user.email);
                $('#editUserRole').val(user.role);
                $('#editUserState').val(user.state);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error loading user info for edit:', error);
            showAlert('error', 'Error', 'Failed to load user information');
        }
    });
}

function validateEditProfileForm() {
    let isValid = true;
    $('.error-message').text('');
    $('.form-control').removeClass('is-invalid');

    const userName = $('#editUserName').val().trim();
    const currentPassword = $('#currentPassword').val().trim();
    const newPassword = $('#newPassword').val().trim();
    const confirmNewPassword = $('#confirmNewPassword').val().trim();

    // Validate name
    if (userName === '') {
        $('#errorEditUserName').text('Name is required');
        $('#editUserName').addClass('is-invalid');
        isValid = false;
    } else if (userName.length < 2) {
        $('#errorEditUserName').text('Name must be at least 2 characters');
        $('#editUserName').addClass('is-invalid');
        isValid = false;
    }

    // Validate password fields if password change is attempted
    if (newPassword !== '' || confirmNewPassword !== '' || currentPassword !== '') {
        if (currentPassword === '') {
            $('#errorCurrentPassword').text('Current password is required to change password');
            $('#currentPassword').addClass('is-invalid');
            isValid = false;
        }

        if (newPassword === '') {
            $('#errorNewPassword').text('New password is required');
            $('#newPassword').addClass('is-invalid');
            isValid = false;
        } else if (newPassword.length < 6) {
            $('#errorNewPassword').text('New password must be at least 6 characters');
            $('#newPassword').addClass('is-invalid');
            isValid = false;
        }

        if (confirmNewPassword === '') {
            $('#errorConfirmNewPassword').text('Please confirm your new password');
            $('#confirmNewPassword').addClass('is-invalid');
            isValid = false;
        } else if (newPassword !== confirmNewPassword) {
            $('#errorConfirmNewPassword').text('Passwords do not match');
            $('#confirmNewPassword').addClass('is-invalid');
            isValid = false;
        }
    }

    return isValid;
}

function saveProfile() {
    const profileData = {
        userName: $('#editUserName').val().trim()
    };

    // Add password fields if provided
    const currentPassword = $('#currentPassword').val().trim();
    const newPassword = $('#newPassword').val().trim();

    if (currentPassword !== '' && newPassword !== '') {
        profileData.currentPassword = currentPassword;
        profileData.newPassword = newPassword;
    }

    // Disable button during request
    $('#btnSaveProfile')
        .prop('disabled', true)
        .html('<i class="bi bi-hourglass-split"></i> Saving...');

    $.ajax({
        url: '../ws/WsProfile.php',
        method: 'POST',
        data: profileData,
        dataType: 'json',
        success: function (response) {
            $('#btnSaveProfile')
                .prop('disabled', false)
                .html('<i class="bi bi-check-circle"></i> Save Changes');

            if (response.success) {
                loadContent('profile');
                showAlert('success', 'Success', response.message);
            } else {
                showAlert('error', 'Error', response.message || 'Failed to update profile');
            }
        },
        error: function (xhr, status, error) {
            $('#btnSaveProfile')
                .prop('disabled', false)
                .html('<i class="bi bi-check-circle"></i> Save Changes');
            showAlert('error', 'Error', 'An error occurred while updating profile');
        }
    });
}