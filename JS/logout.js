$(document).ready(function () {
  // Handle logout button click
  $("#logoutBtn, .logout-btn").on("click", function (e) {
    e.preventDefault(); // Prevent default link behavior

    showConfirm(
      "Logout Confirmation",
      "Are you sure you want to logout from Art Noir?",
      function () {
        // User clicked Confirm - proceed with logout
        window.location.href = "../ws/WsLogout.php";
      },
      function () {
        // User clicked Cancel - do nothing (optional callback)
        console.log("Logout cancelled");
      }
    );
  });
});

