/**
 * Alert System for Art Noir
 * - Toast notifications for success/info (non-intrusive, auto-dismiss)
 * - Modal alerts for errors/warnings/confirmations (requires user action)
 * 
 * Usage:
 * - showToast(type, title, message, duration) - Auto-dismiss toast
 * - showAlert(type, title, message, callback) - Modal alert with OK button
 * - showConfirm(title, message, onConfirm, onCancel) - Confirmation dialog
 * - showSuccess(message) - Quick success toast
 * - showError(message, callback) - Error modal
 */

// Initialize toast container
$(document).ready(function() {
  if (!$('.toast-container').length) {
    $('body').append('<div class="toast-container"></div>');
  }
});

/**
 * Show a non-intrusive toast notification (auto-dismiss)
 * Perfect for success messages and info that don't need acknowledgment
 * @param {string} type - Toast type: 'success', 'error', 'warning', 'info'
 * @param {string} title - Toast title
 * @param {string} message - Toast message
 * @param {number} duration - Duration in milliseconds (default: 4000)
 */
function showToast(type, title, message, duration = 4000) {
  const icons = {
    success: '<i class="bi bi-check-circle-fill"></i>',
    error: '<i class="bi bi-x-circle-fill"></i>',
    warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
    info: '<i class="bi bi-info-circle-fill"></i>'
  };

  const toast = $(`
    <div class="toast-notification ${type}">
      <div class="toast-icon">${icons[type]}</div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
      <button class="toast-close">×</button>
      <div class="toast-progress" style="animation-duration: ${duration}ms;"></div>
    </div>
  `);

  $('.toast-container').append(toast);

  // Auto remove after duration
  const timeoutId = setTimeout(function() {
    removeToast(toast);
  }, duration);

  // Manual close
  toast.find('.toast-close').on('click', function(e) {
    e.stopPropagation();
    clearTimeout(timeoutId);
    removeToast(toast);
  });

  // Click anywhere on toast to dismiss
  toast.on('click', function() {
    clearTimeout(timeoutId);
    removeToast(toast);
  });
}

// Remove toast with animation
function removeToast(toast) {
  toast.addClass('removing');
  setTimeout(function() {
    toast.remove();
  }, 300);
}

/**
 * Smart alert system - automatically chooses toast or modal based on type
 * @param {string} type - Alert type: 'success', 'error', 'warning', 'info', 'confirm'
 * @param {string} title - Alert title
 * @param {string} message - Alert message
 * @param {function} callback - Optional callback function after alert closes
 */
function showAlert(type, title, message, callback = null) {
  // Success and info use non-intrusive toasts (auto-dismiss)
  if (type === 'success' || type === 'info') {
    const duration = type === 'success' ? 2000 : 2500;
    showToast(type, title, message, duration);
    if (callback) {
      setTimeout(callback, duration);
    }
    return;
  }

  // Error, warning, and confirm use modals (require acknowledgment)
  // Close any open Bootstrap modals first
  $('.modal').modal('hide');

  // Wait for modal animation to complete
  setTimeout(function() {
    // Remove modal backdrop if it exists
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');

    const icons = {
      error: '<i class="bi bi-x-circle-fill"></i>',
      warning: '<i class="bi bi-exclamation-triangle-fill"></i>',
      confirm: '<i class="bi bi-question-circle-fill"></i>'
    };

    const overlay = $(`
      <div class="custom-alert-overlay">
        <div class="custom-alert">
          <div class="custom-alert-icon ${type}">
            ${icons[type]}
          </div>
          <div class="custom-alert-title">${title}</div>
          <div class="custom-alert-message">${message}</div>
          <div class="custom-alert-buttons">
            <button class="custom-alert-btn custom-alert-btn-primary" id="alertOkBtn">OK</button>
          </div>
        </div>
      </div>
    `);

    $('body').append(overlay);

    $('#alertOkBtn').on('click', function() {
      overlay.remove();
      if (callback) callback();
    });

    // Close on overlay click
    overlay.on('click', function(e) {
      if ($(e.target).hasClass('custom-alert-overlay')) {
        overlay.remove();
        if (callback) callback();
      }
    });

    // Close on ESC key
    $(document).one('keydown', function(e) {
      if (e.key === 'Escape') {
        overlay.remove();
        if (callback) callback();
      }
    });
  }, 300);
}

/**
 * Show a confirmation dialog
 * @param {string} title - Confirmation title
 * @param {string} message - Confirmation message
 * @param {function} onConfirm - Callback function when confirmed
 * @param {function} onCancel - Optional callback function when cancelled
 */
function showConfirm(title, message, onConfirm, onCancel = null) {
  // Close any open Bootstrap modals first
  $('.modal').modal('hide');

  // Wait for modal animation to complete
  setTimeout(function() {
    // Remove modal backdrop if it exists
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');

    const overlay = $(`
      <div class="custom-alert-overlay">
        <div class="custom-alert">
          <div class="custom-alert-icon confirm">
            <i class="bi bi-question-circle-fill"></i>
          </div>
          <div class="custom-alert-title">${title}</div>
          <div class="custom-alert-message">${message}</div>
          <div class="custom-alert-buttons">
            <button class="custom-alert-btn custom-alert-btn-secondary" id="alertCancelBtn">Cancel</button>
            <button class="custom-alert-btn custom-alert-btn-danger" id="alertConfirmBtn">Confirm</button>
          </div>
        </div>
      </div>
    `);

    $('body').append(overlay);

    $('#alertConfirmBtn').on('click', function() {
      overlay.remove();
      if (onConfirm) onConfirm();
    });

    $('#alertCancelBtn').on('click', function() {
      overlay.remove();
      if (onCancel) onCancel();
    });

    // Close on overlay click (counts as cancel)
    overlay.on('click', function(e) {
      if ($(e.target).hasClass('custom-alert-overlay')) {
        overlay.remove();
        if (onCancel) onCancel();
      }
    });

    // Close on ESC key (counts as cancel)
    $(document).one('keydown', function(e) {
      if (e.key === 'Escape') {
        overlay.remove();
        if (onCancel) onCancel();
      }
    });
  }, 300);
}

/**
 * Quick success toast (auto-dismiss, non-intrusive)
 * Perfect for: "Saved!", "Updated!", "Added successfully!"
 * This function now just calls showAlert with 'success' type
 * @param {string} message - Success message
 * @param {function} callback - Optional callback after toast disappears
 */
function showSuccess(message, callback = null) {
  showAlert('success', 'Success', message, callback);
}

/**
 * Error alert (modal, requires OK)
 * Use for errors that need user acknowledgment
 * @param {string} message - Error message
 * @param {function} callback - Optional callback after close
 */
function showError(message, callback = null) {
  showAlert('error', 'Error', message, callback);
}

/**
 * Warning alert (modal, requires OK)
 * @param {string} message - Warning message
 * @param {function} callback - Optional callback after close
 */
function showWarning(message, callback = null) {
  showAlert('warning', 'Warning', message, callback);
}

/**
 * Info toast (auto-dismiss, non-intrusive)
 * @param {string} message - Info message
 */
function showInfo(message) {
  showToast('info', 'Info', message, 3500);
}

/**
 * Quick success toast with custom title
 * @param {string} title - Custom title
 * @param {string} message - Success message
 */
function showSuccessWithTitle(title, message) {
  showToast('success', title, message, 3500);
}
