// Contact form functionality
function initializeContactForm() {

    $('#contactForm').off('submit').on('submit', function (e) {
        e.preventDefault();

        // Get form data
        const formData = {
            name: $('#name').val().trim(),
            email: $('#email').val().trim(),
            subject: $('#subject').val().trim(),
            message: $('#message').val().trim()
        };

        // Basic validation
        if (!formData.name || !formData.email || !formData.subject || !formData.message) {
            showMessage('Please fill in all fields', 'error');
            return;
        }

        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            showMessage('Please enter a valid email address', 'error');
            return;
        }

        // Disable submit button
        const submitBtn = $('.btn-submit');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true)
                 .html('<i class="bi bi-hourglass-split"></i> Sending...');

        // AJAX request
        $.ajax({
            url: '../ws/WsContact.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',

            success: function (response) {
                if (response.success) {
                    showMessage(
                        'Thank you! Your message has been sent successfully. We\'ll get back to you soon.',
                        'success'
                    );
                    $('#contactForm')[0].reset();
                } else {
                    showMessage(response.message || 'Failed to send message. Please try again.', 'error');
                }
            },

            error: function () {
                showMessage(
                    'An error occurred. Please try again later or contact us directly via email.',
                    'error'
                );
            },

            complete: function () {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
}

// Message handler
function showMessage(message, type) {
    const messageDiv = $('#formMessage');

    messageDiv
        .removeClass('success error')
        .addClass(type)
        .html(
            `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}-fill"></i> ${message}`
        )
        .fadeIn();

    // Auto-hide success messages
    if (type === 'success') {
        setTimeout(function () {
            messageDiv.fadeOut();
        }, 5000);
    }

    // Scroll to message
    $('html, body').animate({
        scrollTop: messageDiv.offset().top - 100
    }, 500);
}
