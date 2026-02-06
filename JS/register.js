$(document).ready(function () {
  // Role selection
  $(".role-option").on("click", function () {
    $(".role-option").removeClass("active");
    $(this).addClass("active");
    $("#selectedRole").val($(this).data("role"));
  });

  // Register button
  $("#btnRegister").on("click", function () {
    if (validateRegisterForm()) {
      register();
    }
  });

  // Enter key to submit
  $("#registerName, #registerEmail, #registerPassword, #registerConfirmPassword").on("keypress", function (e) {
    if (e.which === 13) {
      if (validateRegisterForm()) {
        register();
      }
    }
  });

  // Clear errors on input
  $("#registerName, #registerEmail, #registerPassword, #registerConfirmPassword").on("input", function () {
    $(this).removeClass("is-invalid");
    const errorId =
      "#error" +
      $(this).attr("id").charAt(0).toUpperCase() +
      $(this).attr("id").slice(1);
    $(errorId).text("");
  });

  // Form validation
  function validateRegisterForm() {
    let isValid = true;
    $(".error-message").text("");
    $(".form-control").removeClass("is-invalid");

    const name = $("#registerName").val().trim();
    if (name === "") {
      $("#errorRegisterName").text("Full name is required");
      $("#registerName").addClass("is-invalid");
      isValid = false;
    } else if (name.length < 2) {
      $("#errorRegisterName").text("Name must be at least 2 characters");
      $("#registerName").addClass("is-invalid");
      isValid = false;
    }

    const email = $("#registerEmail").val().trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === "") {
      $("#errorRegisterEmail").text("Email is required");
      $("#registerEmail").addClass("is-invalid");
      isValid = false;
    } else if (!emailRegex.test(email)) {
      $("#errorRegisterEmail").text("Please enter a valid email address");
      $("#registerEmail").addClass("is-invalid");
      isValid = false;
    }

    const password = $("#registerPassword").val();
    if (password === "") {
      $("#errorRegisterPassword").text("Password is required");
      $("#registerPassword").addClass("is-invalid");
      isValid = false;
    } else if (password.length < 6) {
      $("#errorRegisterPassword").text("Password must be at least 6 characters");
      $("#registerPassword").addClass("is-invalid");
      isValid = false;
    }

    const confirmPassword = $("#registerConfirmPassword").val();
    if (confirmPassword === "") {
      $("#errorRegisterConfirmPassword").text("Please confirm your password");
      $("#registerConfirmPassword").addClass("is-invalid");
      isValid = false;
    } else if (password !== confirmPassword) {
      $("#errorRegisterConfirmPassword").text("Passwords do not match");
      $("#registerConfirmPassword").addClass("is-invalid");
      isValid = false;
    }

    return isValid;
  }

  // Register function
  function register() {
    const registerData = {
      name: $("#registerName").val().trim(),
      email: $("#registerEmail").val().trim(),
      password: $("#registerPassword").val(),
      role: $("#selectedRole").val(),
    };

    // Disable button during request
    $("#btnRegister")
      .prop("disabled", true)
      .html('<i class="bi bi-hourglass-split"></i> Creating account...');

    $.ajax({
      url: "ws/WsRegister.php",
      method: "POST",
      data: registerData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAlert(
            "success",
            "Success",
            "Account created successfully! You can now login.",
            function () {
              window.location.href = "Login.php";
            }
          );
        } else {
          $("#btnRegister")
            .prop("disabled", false)
            .html('<i class="bi bi-person-plus-fill"></i> Create Account');
          showAlert(
            "error",
            "Registration Failed",
            response.message || "Registration failed. Please try again."
          );
        }
      },
      error: function (xhr, status, error) {
        $("#btnRegister")
          .prop("disabled", false)
          .html('<i class="bi bi-person-plus-fill"></i> Create Account');
        let message = "An error occurred during registration. Please try again.";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        }
        showAlert(
          "error",
          "Error",
          message
        );
      },
    });
  }
});