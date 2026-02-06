$(document).ready(function () {
  // Role selection
  $(".role-option").on("click", function () {
    $(".role-option").removeClass("active");
    $(this).addClass("active");
    $("#selectedRole").val($(this).data("role"));
  });

  // Login button
  $("#btnLogin").on("click", function () {
    if (validateLoginForm()) {
      login();
    }
  });

  // Enter key to submit
  $("#loginEmail, #loginPassword").on("keypress", function (e) {
    if (e.which === 13) {
      if (validateLoginForm()) {
        login();
      }
    }
  });

  // Clear errors on input
  $("#loginEmail, #loginPassword").on("input", function () {
    $(this).removeClass("is-invalid");
    const errorId =
      "#error" +
      $(this).attr("id").charAt(0).toUpperCase() +
      $(this).attr("id").slice(1);
    $(errorId).text("");
  });

  function validateLoginForm() {
    let isValid = true;
    $(".error-message").text("");
    $(".form-control").removeClass("is-invalid");

    const email = $("#loginEmail").val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email === "") {
      $("#errorLoginEmail").text("Email is required");
      $("#loginEmail").addClass("is-invalid");
      isValid = false;
    } else if (!emailRegex.test(email)) {
      $("#errorLoginEmail").text("Please enter a valid email address");
      $("#loginEmail").addClass("is-invalid");
      isValid = false;
    }

    const password = $("#loginPassword").val();
    if (password === "") {
      $("#errorLoginPassword").text("Password is required");
      $("#loginPassword").addClass("is-invalid");
      isValid = false;
    } else if (password.length < 6) {
      $("#errorLoginPassword").text("Password must be at least 6 characters");
      $("#loginPassword").addClass("is-invalid");
      isValid = false;
    }

    return isValid;
  }

  function login() {
    const loginData = {
      email: $("#loginEmail").val().trim(),
      password: $("#loginPassword").val(),
      role: $("#selectedRole").val(),
    };

    // Disable button during request
    $("#btnLogin")
      .prop("disabled", true)
      .html('<i class="bi bi-hourglass-split"></i> Logging in...');

    $.ajax({
      url: "ws/WsLogin.php",
      method: "POST",
      data: loginData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Redirect immediately without alert
          window.location.href = response.redirect || "index.php";
        } else {
          $("#btnLogin")
            .prop("disabled", false)
            .html('<i class="bi bi-box-arrow-in-right"></i> Login');
          showAlert(
            "error",
            "Login Failed",
            response.message || "Invalid credentials. Please try again."
          );
        }
      },
      error: function (xhr, status, error) {
        $("#btnLogin")
          .prop("disabled", false)
          .html('<i class="bi bi-box-arrow-in-right"></i> Login');
        showAlert(
          "error",
          "Error",
          "An error" + error + "occurred during login. Please try again."
        );
      },
    });
  }
});
