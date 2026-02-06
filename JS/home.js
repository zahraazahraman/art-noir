// Set active nav link based on current page
$(document).ready(function () {

  loadNotificationCount();
      
  // Refresh count every 30 seconds
  setInterval(loadNotificationCount, 30000);
  
  // Highlight active link on click
  $(".nav-link").on("click", function () {
    $(".nav-link").removeClass("active");
    $(this).addClass("active");
  });

  window.heroContent = $("#heroContent");

  // Navbar clicks
  $("#homeLink").on("click", function () {
    loadContent("HomeContent.php");
  });

  $("#dashboardLink").on("click", function () {
    loadContent("DashboardContent.php", function() {
      if (typeof initializeDashboard === 'function') {
        initializeDashboard();
      }
    });
  });

  $("#manageUsersLink").on("click", function () {
    loadContent("ManageUsers.php");
  });

  $("#manageArtistsLink").on("click", function () {
    loadContent("ManageArtists.php");
  });

  $("#manageArtworksLink").on("click", function () {
    loadContent("ManageArtworks.php");
  });

  $("#manageCategoriesLink").on("click", function () {
    loadContent("ManageCategories.php");
  });

  $("#manageMessagesLink").on("click", function () {
    loadContent("ManageMessages.php");
  });

  $("#notificationBell").on("click", function () {
    loadContent("Notifications.php");
  });

  function loadContent(url, callback) {
    // Close any open modals before loading new content
    $('.modal').modal('hide');
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');

    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'html',
      success: function(response) {
        if (response.trim().startsWith('{')) {
          try {
            var json = JSON.parse(response);
            if (json.redirect) {
              window.location.href = json.redirect;
              return;
            }
          } catch(e) {}
        }
        window.heroContent.html(response);
        if (callback) callback();

        // Initialize page-specific functionality
        if (url === 'ManageUsers.php') {
          if (typeof initializeUsers === 'function') {
            initializeUsers();
          }
        } else if (url === 'ManageArtists.php') {
          if (typeof initializeArtists === 'function') {
            initializeArtists();
          }
        } else if (url === 'ManageArtworks.php') {
          if (typeof initializeArtworks === 'function') {
            initializeArtworks();
          }
        } else if (url === 'ManageCategories.php') {
          if (typeof initializeCategories === 'function') {
            initializeCategories();
          }
        } else if (url === 'ManageMessages.php') {
          if (typeof initializeMessages === 'function') {
            initializeMessages();
          }
        } else if (url === 'Notifications.php') {
          if (typeof initializeNotifications === 'function') {
            initializeNotifications();
          }
        }
      },
      error: function() {
        showAlert('error', 'Error', 'Failed to load content');
      }
    });
  }

  window.loadContent = loadContent;

  // Load notification count
  function loadNotificationCount() {
      // Get both notification and message counts
      let totalCount = 0;
      let completedRequests = 0;

      function updateDisplay() {
          if (totalCount > 0) {
              $('#notificationCount').text(totalCount).show();
          } else {
              $('#notificationCount').hide();
          }
      }

        function handleResponse(count) {
          // Ensure numeric addition (responses may be strings)
          const n = parseInt(count, 10) || 0;
          totalCount += n;
          completedRequests++;
          if (completedRequests === 2) {
            updateDisplay();
          }
        }

      // Get notification count
      $.ajax({
          url: "../ws/WsNotifications.php?count=true",
          method: "GET",
          dataType: "json",
          success: function(data) {
              handleResponse(data.count || 0);
          },
          error: function() {
              console.error('Failed to load notification count');
              handleResponse(0);
          }
      });

      // Get message count
      $.ajax({
          url: "../ws/WsMessages.php?count=true",
          method: "GET",
          dataType: "json",
          success: function(data) {
              handleResponse(data.count || 0);
          },
          error: function() {
              console.error('Failed to load message count');
              handleResponse(0);
          }
      });
  }
});
