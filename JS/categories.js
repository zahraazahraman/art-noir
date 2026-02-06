(function() {
$(document).ready(function () {
  // Empty - initialization happens when content is dynamically loaded
});

let isEditMode = false;
let allCategories = [];
let filteredCategories = [];
let currentPage = 1;
const itemsPerPage = 3;

// LOAD CATEGORIES FUNCTION
function loadCategories() {
  $.ajax({
    url: "../ws/WsCategories.php",
    method: "GET",
    dataType: "json",
    success: function (data) {
      if (data == null || data.length == 0) {
        allCategories = [];
        filteredCategories = [];
      } else {
        allCategories = data;
        filteredCategories = data;
      }
      currentPage = 1;
      displayCurrentPage();
    },
    error: function (xhr, status, error) {
      showAlert(
        "error",
        "Error",
        "Failed to load categories. Please try again."
      );
    },
  });
}

// DISPLAY CURRENT PAGE FUNCTION
function displayCurrentPage() {
  const totalPages = Math.ceil(filteredCategories.length / itemsPerPage) || 1;
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const pageCategories = filteredCategories.slice(startIndex, endIndex);

  displayCategories(pageCategories);
  updatePaginationInfo();
  updatePaginationButtons();
}

// UPDATE PAGINATION INFO FUNCTION
function updatePaginationInfo() {
  const totalPages = Math.ceil(filteredCategories.length / itemsPerPage) || 1;
  const startIndex = (currentPage - 1) * itemsPerPage + 1;
  const endIndex = Math.min(currentPage * itemsPerPage, filteredCategories.length);

  $("#showingStart").text(filteredCategories.length > 0 ? startIndex : 0);
  $("#showingEnd").text(endIndex);
  $("#totalCategories").text(filteredCategories.length);
  $("#currentPage").text(currentPage);
  $("#totalPages").text(totalPages);
}

// UPDATE PAGINATION BUTTONS FUNCTION
function updatePaginationButtons() {
  const totalPages = Math.ceil(filteredCategories.length / itemsPerPage) || 1;

  $("#btnFirstPage").prop("disabled", currentPage === 1);
  $("#btnPrevPage").prop("disabled", currentPage === 1);
  $("#btnNextPage").prop("disabled", currentPage === totalPages);
  $("#btnLastPage").prop("disabled", currentPage === totalPages);
}

// FILTER AND DISPLAY CATEGORIES FUNCTION
function filterAndDisplayCategories() {
  const search = $("#searchCategory").val().toLowerCase();

  filteredCategories = allCategories.filter(function (category) {
    return search === "" || category.name.toLowerCase().includes(search);
  });

  displayCurrentPage();
}

// DISPLAY CATEGORIES FUNCTION
function displayCategories(categories) {
  const grid = $("#categoriesGrid");
  grid.empty();

  if (categories.length === 0) {
    grid.html(`
      <div class="no-categories">
        <i class="bi bi-tags"></i>
        <p>No categories found. Add your first category!</p>
      </div>
    `);
  } else {
    categories.forEach(function (category) {
      const card = $(`
        <div class="category-card">
          <div class="category-icon">
            <i class="bi bi-tag-fill"></i>
          </div>
          <div class="category-name">${category.name}</div>
          <div class="category-count">${category.item_count} items</div>
          <div class="category-actions">
            <button class="btn-edit-category" data-id="${category.id}" data-name="${category.name}">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn-delete-category" data-id="${category.id}" data-name="${category.name}">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      `);
      card.on('click', function(event) {
        if ($(event.target).is('button') || $(event.target).closest('button').length) return;
        // Only pass the category id as a filter parameter. Do not set a search term
        // because that would additionally filter by title/description/artist.
        window.loadParams = { category: category.id };
        window.loadContent("ManageArtworks.php");
        $(".nav-link").removeClass("active");
        $("#manageArtworksLink").addClass("active");
      });
      grid.append(card);
    });
  }
}

// VALIDATE CATEGORY FORM FUNCTION
function validateCategoryForm() {
  let isValid = true;
  $(".error-message").text("");
  $(".form-control").removeClass("is-invalid");

  const name = $("#categoryName").val().trim();
  if (name === "") {
    $("#errorCategoryName").text("Category name is required");
    $("#categoryName").addClass("is-invalid");
    isValid = false;
  } else if (name.length < 2) {
    $("#errorCategoryName").text(
      "Category name must be at least 2 characters"
    );
    $("#categoryName").addClass("is-invalid");
    isValid = false;
  }

  return isValid;
}

// ADD CATEGORY FUNCTION
function addCategory() {
  const categoryData = {
    action: "add",
    name: $("#categoryName").val().trim(),
  };

  $.ajax({
    url: "../ws/WsCategories.php",
    method: "POST",
    data: categoryData,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        $("#categoryModal").modal("hide");
        loadCategories();
        resetForm();
        showAlert(
          "success",
          "Success",
          "Category added successfully!"
        );
      } else {
        showAlert(
          "error",
          "Error",
          response.message || "Could not add category"
        );
      }
    },
    error: function (xhr, status, error) {
      showAlert(
        "error",
        "Error",
        "An error occurred while adding the category. Please try again."
      );
    },
  });
}

// UPDATE CATEGORY FUNCTION
function updateCategory() {
  const categoryData = {
    action: "update",
    id: $("#categoryId").val(),
    name: $("#categoryName").val().trim(),
  };

  $.ajax({
    url: "../ws/WsCategories.php",
    method: "POST",
    data: categoryData,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        $("#categoryModal").modal("hide");
        loadCategories();
        resetForm();
        showAlert(
          "success",
          "Success",
          "Category updated successfully!"
        );
      } else {
        showAlert(
          "error",
          "Error",
          response.message || "Could not update category"
        );
      }
    },
    error: function (xhr, status, error) {
      showAlert(
        "error",
        "Error",
        "An error occurred while updating the category. Please try again."
      );
    },
  });
}

// DELETE CATEGORY FUNCTION
function deleteCategory(id) {
  $.ajax({
    url: "../ws/WsCategories.php",
    method: "POST",
    data: {
      action: "delete",
      id: id,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        loadCategories();
        showAlert(
          "success",
          "Success",
          "Category deleted successfully!"
        );
      } else {
        showAlert(
          "error",
          "Error",
          response.message || "Could not delete category"
        );
      }
    },
    error: function (xhr, status, error) {
      showAlert(
        "error",
        "Error",
        "An error occurred while deleting the category. Please try again."
      );
    },
  });
}

// RESET FORM FUNCTION
function resetForm() {
  $('#categoryFormContainer input[type="text"]').val("");
  $("#categoryId").val("");
  $(".error-message").text("");
  $(".form-control").removeClass("is-invalid");
  isEditMode = false;
}

// INITIALIZE CATEGORIES FUNCTION - Called when ManageCategories.php is dynamically loaded
function initializeCategories() {
  // Initial load
  loadCategories();

  // SEARCH FUNCTIONALITY
  $(document).on("keyup", "#searchCategory", function () {
    currentPage = 1;
    filterAndDisplayCategories();
  });

  // SHOW ALL BUTTON
  $(document).on("click", "#btnShowAllCategories", function () {
    currentPage = 1;
    $("#searchCategory").val("");
    filterAndDisplayCategories();
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
    const totalPages = Math.ceil(allCategories.length / itemsPerPage);
    if (currentPage < totalPages) {
      currentPage++;
      displayCurrentPage();
    }
  });

  $(document).on("click", "#btnLastPage", function () {
    const totalPages = Math.ceil(allCategories.length / itemsPerPage);
    currentPage = totalPages;
    displayCurrentPage();
  });

  // ADD CATEGORY BUTTON
  $(document).on("click", "#btnAddCategory", function () {
    isEditMode = false;
    resetForm();
    $("#categoryModalLabel").html(
      '<i class="bi bi-plus-circle"></i> Add Category'
    );
  });

  // SAVE CATEGORY BUTTON
  $(document).on("click", "#btnSaveCategory", function () {
    if (validateCategoryForm()) {
      if (isEditMode) {
        updateCategory();
      } else {
        addCategory();
      }
    }
  });

  // EDIT CATEGORY BUTTON
  $(document).on("click", ".btn-edit-category", function () {
    const categoryId = $(this).data("id");
    const categoryName = $(this).data("name");

    isEditMode = true;
    $("#categoryModalLabel").html(
      '<i class="bi bi-pencil-square"></i> Edit Category'
    );

    $("#categoryId").val(categoryId);
    $("#categoryName").val(categoryName);

    $("#categoryModal").modal("show");
  });

  // DELETE CATEGORY BUTTON
  $(document).off("click.categories", ".btn-delete-category");
  $(document).on("click.categories", ".btn-delete-category", function () {
    const categoryId = $(this).data("id");
    const categoryName = $(this).data("name");

    showConfirm(
      "Delete Category",
      `Are you sure you want to delete the category "${categoryName}"? This action cannot be undone.`,
      function () {
        deleteCategory(categoryId);
      }
    );
  });

  // CLEAR VALIDATION ERRORS ON INPUT
  $(document).on("input", "#categoryName", function () {
    $(this).removeClass("is-invalid");
    $("#errorCategoryName").text("");
  });
}

// Export the initialize function to window object
window.initializeCategories = initializeCategories;

})();
