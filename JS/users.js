(function() {
$(document).ready(function () {
  // Empty - initialization happens when content is dynamically loaded
});

let isEditMode = false;
let allUsers = [];
let filteredUsers = [];
let currentPage = 1;
const itemsPerPage = 5;

// LOAD USERS FUNCTION
function loadUsers() {
  $.ajax({
    url: "../ws/WsUsers.php",
    method: "GET",
    dataType: "json",
    success: function (data) {
      if (data == null || data.length == 0) {
        allUsers = [];
        filteredUsers = [];
      } else {
        allUsers = data;
      }
      currentPage = 1;
      filterAndDisplayUsers();
    },
    error: function (xhr, status, error) {
      showAlert(
        "error",
        "Error",
        "An error " +
          error +
          " occurred while loading users. Please try again."
      );
    },
  });
}

// FILTER AND DISPLAY USERS FUNCTION
function filterAndDisplayUsers() {
  const search = $("#searchUser").val().toLowerCase() || "";
  const role = $("#filterRole").val();
  const state = $("#filterState").val();

  filteredUsers = allUsers.filter(function (user) {
    const matchesSearch =
      search === "" ||
      user.name.toLowerCase().includes(search) ||
      user.email.toLowerCase().includes(search);
    const matchesRole = role === "" || user.role === role;
    const matchesState = state === "" || user.state === state;
    return matchesSearch && matchesRole && matchesState;
  });

  displayCurrentPage();
}

// DISPLAY CURRENT PAGE FUNCTION
function displayCurrentPage() {
  const totalPages = Math.ceil(filteredUsers.length / itemsPerPage) || 1;
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const pageUsers = filteredUsers.slice(startIndex, endIndex);

  populateUsersTable(pageUsers);
  updatePaginationInfo();
  updatePaginationButtons();
}

// UPDATE PAGINATION INFO FUNCTION
function updatePaginationInfo() {
  const totalPages = Math.ceil(filteredUsers.length / itemsPerPage) || 1;
  const startIndex = (currentPage - 1) * itemsPerPage + 1;
  const endIndex = Math.min(currentPage * itemsPerPage, filteredUsers.length);

  $("#showingStart").text(filteredUsers.length > 0 ? startIndex : 0);
  $("#showingEnd").text(endIndex);
  $("#totalUsers").text(filteredUsers.length);
  $("#currentPage").text(currentPage);
  $("#totalPages").text(totalPages);
}

// UPDATE PAGINATION BUTTONS FUNCTION
function updatePaginationButtons() {
  const totalPages = Math.ceil(filteredUsers.length / itemsPerPage) || 1;

  $("#btnFirstPage").prop("disabled", currentPage === 1);
  $("#btnPrevPage").prop("disabled", currentPage === 1);
  $("#btnNextPage").prop("disabled", currentPage === totalPages);
  $("#btnLastPage").prop("disabled", currentPage === totalPages);
}

// POPULATE USERS TABLE FUNCTION
function populateUsersTable(users) {
  let usersTableBody = $("#usersTableBody");
  usersTableBody.empty();
  let row;

  if (users.length == 0) {
    row = `<tr>
      <td colspan="5" class="text-center">No users found.</td>
    </tr>`;
    usersTableBody.append(row);
  } else {
    users.forEach(function (user) {
      const roleBadge =
        user.role === "Admin" ? "badge-admin" : "badge-customer";
      const stateBadge =
        user.state === "Active" ? "badge-active" : "badge-inactive";

      row = `<tr>
        <td>${user.name}</td>
        <td>${user.email}</td>
        <td><span class="badge-role ${roleBadge}">${user.role}</span></td>
        <td><span class="badge-state ${stateBadge}">${user.state}</span></td>
        <td class="text-center">
          <button
            class="btn btn-outline-primary btn-sm edit-btn"
            data-toggle="modal"
            data-target="#userModal"
            data-id="${user.id}"
            data-name="${user.name}"
            data-email="${user.email}"
            data-role="${user.role}"
            data-state="${user.state}">
            <i class="bi bi-pencil"></i>
          </button>
          <button
            class="btn btn-outline-danger btn-sm delete-btn"
            data-id="${user.id}"
            data-name="${user.name}">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>`;
      usersTableBody.append(row);
    });
  }
}

// VALIDATE USER FORM FUNCTION
function validateUserForm() {
  let isValid = true;

  // Clear all previous errors
  $(".error-message").text("");
  $(".form-control").removeClass("is-invalid");

  // Validate Name
  const name = $("#userName").val().trim();
  if (name === "") {
    $("#errorUserName").text("Name is required");
    $("#userName").addClass("is-invalid");
    isValid = false;
  } else if (name.length < 3) {
    $("#errorUserName").text("Name must be at least 3 characters");
    $("#userName").addClass("is-invalid");
    isValid = false;
  }

  // Validate Email
  const email = $("#userEmail").val().trim();
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (email === "") {
    $("#errorUserEmail").text("Email is required");
    $("#userEmail").addClass("is-invalid");
    isValid = false;
  } else if (!emailRegex.test(email)) {
    $("#errorUserEmail").text("Please enter a valid email address");
    $("#userEmail").addClass("is-invalid");
    isValid = false;
  }

  // Validate Password (only for add mode)
  if (!isEditMode) {
    const password = $("#userPassword").val();
    if (password === "") {
      $("#errorUserPassword").text("Password is required");
      $("#userPassword").addClass("is-invalid");
      isValid = false;
    } else if (password.length < 6) {
      $("#errorUserPassword").text("Password must be at least 6 characters");
      $("#userPassword").addClass("is-invalid");
      isValid = false;
    }
  }

  // Validate Role
  const role = $("#userRole").val();
  if (role === "" || role === "Select Role") {
    $("#errorUserRole").text("Please select a role");
    $("#userRole").addClass("is-invalid");
    isValid = false;
  }

  // Validate State
  const state = $("#userState").val();
  if (state === "" || state === "Select State") {
    $("#errorUserState").text("Please select a state");
    $("#userState").addClass("is-invalid");
    isValid = false;
  }

  return isValid;
}

// ADD USER FUNCTION
function addUser() {
  const userData = {
    action: "add",
    name: $("#userName").val().trim(),
    email: $("#userEmail").val().trim(),
    password: $("#userPassword").val(),
    role: $("#userRole").val(),
    state: $("#userState").val(),
  };

  $.ajax({
    url: "../ws/WsUsers.php",
    method: "POST",
    data: userData,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        $("#userModal").modal("hide");
        loadUsers();
        resetForm();
        showAlert(
          "success",
          "Success",
          "User added successfully!"
        );
      } else {
        showAlert("error", "Error", response.message || "Could not add user");
      }
    },
    error: function (xhr, status, error) {
      showAlert(
        "error",
        "Error",
        'An error "' +
          error +
          '" occurred while adding the user. Please try again.'
      );
    },
  });
}

// UPDATE USER FUNCTION
function updateUser() {
  const userData = {
    action: "update",
    id: $("#userId").val(),
    name: $("#userName").val().trim(),
    email: $("#userEmail").val().trim(),
    role: $("#userRole").val(),
    state: $("#userState").val(),
  };

  $.ajax({
    url: "../ws/WsUsers.php",
    method: "POST",
    data: userData,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        $("#userModal").modal("hide");
        loadUsers();
        resetForm();
        showAlert(
          "success",
          "Success",
          "User updated successfully!"
        );
      } else {
        showAlert(
          "error",
          "Error",
          response.message || "Could not update user"
        );
      }
    },
    error: function (xhr, status, error) {
      showAlert(
        "error",
        "Error",
        "An error " +
          error +
          " occurred while updating the user. Please try again."
      );
    },
  });
}

// DELETE USER FUNCTION
function deleteUser(userId) {
  $.ajax({
    url: "../ws/WsUsers.php",
    method: "POST",
    data: {
      action: "delete",
      id: userId,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        showAlert(
          "success",
          "Success",
          "User deleted successfully!",
          function () {
            loadUsers();
          }
        );
      } else {
        showAlert(
          "error",
          "Error",
          response.message || "Could not delete user"
        );
      }
    },
    error: function (xhr, status, error) {
      showAlert(
        "error",
        "Error",
        "An error " +
          error +
          " occurred while deleting the user. Please try again."
      );
    },
  });
}

// RESET FORM FUNCTION
function resetForm() {
  $('#userFormContainer input[type="text"]').val("");
  $('#userFormContainer input[type="email"]').val("");
  $('#userFormContainer input[type="password"]').val("");
  $("#userFormContainer select").val("");
  $(".error-message").text("");
  $(".form-control").removeClass("is-invalid");
  isEditMode = false;
}

// INITIALIZE USERS FUNCTION - Called when ManageUsers.php is dynamically loaded
function initializeUsers() {
  // Initial load
  loadUsers();

  // SEARCH FUNCTIONALITY
  $(document).on("keyup", "#searchUser", function () {
    currentPage = 1;
    filterAndDisplayUsers();
  });

  // FILTER FUNCTIONALITY
  $(document).on("change", "#filterRole, #filterState", function () {
    currentPage = 1;
    filterAndDisplayUsers();
  });

  // SHOW ALL BUTTON
  $(document).on("click", "#btnShowAllUsers", function () {
    currentPage = 1;
    $("#searchUser").val("");
    $("#filterRole").val("");
    $("#filterState").val("");
    filterAndDisplayUsers();
  });

  // Pagination buttons
  $(document).on("click", "#btnFirstPage", function () {
    currentPage = 1;
    displayCurrentPage();
  });

  $(document).on("click", "#btnPrevPage", function () {
    if (currentPage > 1) {
      currentPage--;
      displayCurrentPage();
    }
  });

  $(document).on("click", "#btnNextPage", function () {
    const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
    if (currentPage < totalPages) {
      currentPage++;
      displayCurrentPage();
    }
  });

  $(document).on("click", "#btnLastPage", function () {
    const totalPages = Math.ceil(filteredUsers.length / itemsPerPage);
    currentPage = totalPages;
    displayCurrentPage();
  });

  // ADD USER BUTTON
  $(document).on("click", "#btnAddUser", function () {
    isEditMode = false;
    resetForm();
    $("#passwordGroup").show();
    $("#userModalLabel").html('<i class="bi bi-person-plus"></i> Add User');
  });

  // SAVE USER BUTTON
  $(document).on("click", "#btnSaveUser", function () {
    if (validateUserForm()) {
      if (isEditMode) {
        updateUser();
      } else {
        addUser();
      }
    }
  });

  // EDIT BUTTON
  $(document).on("click", ".edit-btn", function () {
    isEditMode = true;
    $("#passwordGroup").hide();
    $("#userModalLabel").html('<i class="bi bi-pencil-square"></i> Edit User');

    $("#userId").val($(this).data("id"));
    $("#userName").val($(this).data("name"));
    $("#userEmail").val($(this).data("email"));
    $("#userRole").val($(this).data("role"));
    $("#userState").val($(this).data("state"));
  });

  // DELETE BUTTON
  $(document).off("click.users", ".delete-btn");
  $(document).on("click.users", ".delete-btn", function () {
    const userId = $(this).data("id");
    const userName = $(this).data("name");

    showConfirm(
      "Delete User",
      `Are you sure you want to delete user "${userName}"? This action cannot be undone.`,
      function () {
        deleteUser(userId);
      }
    );
  });

  // CLEAR VALIDATION ERRORS ON INPUT
  $(document).on("input change", "#userName, #userEmail, #userPassword, #userRole, #userState", function () {
    $(this).removeClass("is-invalid");
    const errorId =
      "#error" +
      $(this).attr("id").charAt(0).toUpperCase() +
      $(this).attr("id").slice(1);
    $(errorId).text("");
  });
}

// Export the initialize function to window object
window.initializeUsers = initializeUsers;

})();
