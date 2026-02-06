(function() {
$(document).ready(function () {
  // Populate dropdowns will be called in initializeArtworks
});

let isEditMode = false;
let allArtworks = [];
let filteredArtworks = [];
let currentPage = 1;
const itemsPerPage = 3;
let allCategories = [];
let currentArtworkId = null;

// Function to initialize artworks when content is loaded dynamically
function initializeArtworks() {
  // ensure we start on first page whenever the artworks management view is initialized
  currentPage = 1;
  // Populate dropdowns
  populateYearDropdowns();

  // Initial load
  loadArtworks();
  loadCategories();
  loadArtists();

  // SEARCH FUNCTIONALITY
  $(document).on("keyup", "#searchArtwork", function () {
    currentPage = 1;
    filterAndDisplayArtworks();
  });

  // FILTER BY CATEGORY
  $(document).on("change", "#filterCategory", function () {
    currentPage = 1;
    if ($(this).val() === "") {
      $("#searchArtwork").val("");
    }
    filterAndDisplayArtworks();
  });

  // FILTER BY STATUS
  $(document).on("change", "#filterStatus", function () {
    currentPage = 1;
    filterAndDisplayArtworks();
  });

  // SHOW ALL BUTTON
  $(document).on("click", "#btnShowAllArtworks", function () {
    currentPage = 1;
    $("#searchArtwork").val("");
    $("#filterCategory").val("");
    $("#filterStatus").val("");
    filterAndDisplayArtworks();
  });

  // PAGINATION HANDLERS - FIRST PAGE
  $(document).on("click", "#btnFirstPage", function () {
    currentPage = 1;
    displayCurrentPage();
  });

  // PAGINATION HANDLERS - PREVIOUS PAGE
  $(document).on("click", "#btnPrevPage", function () {
    if (currentPage > 1) {
      currentPage--;
      displayCurrentPage();
    }
  });

  // PAGINATION HANDLERS - NEXT PAGE
  $(document).on("click", "#btnNextPage", function () {
    const totalPages = Math.ceil(filteredArtworks.length / itemsPerPage);
    if (currentPage < totalPages) {
      currentPage++;
      displayCurrentPage();
    }
  });

  // PAGINATION HANDLERS - LAST PAGE
  $(document).on("click", "#btnLastPage", function () {
    const totalPages = Math.ceil(filteredArtworks.length / itemsPerPage);
    currentPage = totalPages;
    displayCurrentPage();
  });

  // ADD ARTWORK BUTTON HANDLER
  $(document).on("click", "#btnAddArtwork", function () {
    isEditMode = false;
    resetForm();
    $("#artworkModalLabel").html(
      '<i class="bi bi-person-plus"></i> Add Artwork'
    );
  });

  // SAVE ARTWORK BUTTON HANDLER
  $(document).on("click", "#btnSaveArtwork", function () {
    // Prevent multiple submissions
    if ($(this).prop('disabled')) return;
    
    if (validateArtworkForm()) {
      $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Saving...');
      
      if (isEditMode) {
        updateArtwork();
      } else {
        addArtwork();
      }
    }
  });

  // RESET BUTTON STATE WHEN MODAL IS HIDDEN
  $(document).on('hidden.bs.modal', '#artworkModal', function () {
    $("#btnSaveArtwork").prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Artwork');
  });

  // ARTWORK CARD CLICK HANDLER
  $(document).on("click", ".artwork-card", function () {
    const artworkId = $(this).data("id");
    showArtworkDetail(artworkId);
  });

  // EDIT BUTTON IN DETAIL MODAL
  $(document).on("click", "#btnEditFromDetail", function () {
    $("#detailModal").modal("hide");
    editArtwork(currentArtworkId);
  });

  // DELETE BUTTON IN DETAIL MODAL
  $(document).off("click.artworks", "#btnDeleteFromDetail");
  $(document).on("click.artworks", "#btnDeleteFromDetail", function () {
    $("#detailModal").modal("hide");
    showConfirm(
      "Delete Artwork",
      "Are you sure you want to delete this artwork? This action cannot be undone.",
      function () {
        deleteArtwork(currentArtworkId);
      }
    );
  });

  // CLEAR VALIDATION ERRORS ON INPUT/CHANGE
  $(document).on("input change", "#artworkTitle, #artworkDescription, #artworkImage, #artworkArtist, #artworkCategory, #artworkYear", function () {
    $(this).removeClass("is-invalid");
    const errorId =
      "#error" +
      $(this).attr("id").charAt(0).toUpperCase() +
      $(this).attr("id").slice(1);
    $(errorId).text("");
  });
}

//////////////////////////////////////////////////////////////////////// FUNCTIONS

// ESCAPE HTML FUNCTION
function escapeHtml(text) {
  if (text === null || text === undefined) return "";
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return String(text).replace(/[&<>"']/g, function (m) {
    return map[m];
  });
}

// DISPLAY CURRENT PAGE FUNCTION
function displayCurrentPage() {
  const totalPages = Math.ceil(filteredArtworks.length / itemsPerPage) || 1;
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const pageArtworks = filteredArtworks.slice(startIndex, endIndex);

  displayArtworks(pageArtworks);
  updatePaginationInfo();
  updatePaginationButtons();
}

// UPDATE PAGINATION INFO FUNCTION
function updatePaginationInfo() {
  const totalPages = Math.ceil(filteredArtworks.length / itemsPerPage) || 1;
  const startIndex = (currentPage - 1) * itemsPerPage + 1;
  const endIndex = Math.min(
    currentPage * itemsPerPage,
    filteredArtworks.length
  );

  $("#showingStart").text(filteredArtworks.length > 0 ? startIndex : 0);
  $("#showingEnd").text(endIndex);
  $("#totalArtworks").text(filteredArtworks.length);
  $("#currentPage").text(currentPage);
  $("#totalPages").text(totalPages);
}

// UPDATE PAGINATION BUTTONS FUNCTION
function updatePaginationButtons() {
  const totalPages = Math.ceil(filteredArtworks.length / itemsPerPage) || 1;

  $("#btnFirstPage").prop("disabled", currentPage === 1);
  $("#btnPrevPage").prop("disabled", currentPage === 1);
  $("#btnNextPage").prop("disabled", currentPage === totalPages);
  $("#btnLastPage").prop("disabled", currentPage === totalPages);
}

// FILTER AND DISPLAY ARTWORKS FUNCTION
function filterAndDisplayArtworks() {
  const search = $("#searchArtwork").val().toLowerCase();
  const categoryId = $("#filterCategory").val();
  const statusFilter = $("#filterStatus").val();

  filteredArtworks = allArtworks.filter(function (artwork) {
    const matchesSearch =
      search === "" ||
      (artwork.title && artwork.title.toLowerCase().includes(search)) ||
      (artwork.description && artwork.description.toLowerCase().includes(search)) ||
      (artwork.artist_name && artwork.artist_name.toLowerCase().includes(search));

    const matchesCategory =
      categoryId === "" || artwork.category_id == categoryId;

    const matchesStatus =
      statusFilter === "" || artwork.status === statusFilter;

    return matchesSearch && matchesCategory && matchesStatus;
  });

  displayCurrentPage();
}

// DISPLAY ARTWORKS FUNCTION
function displayArtworks(artworks) {
  const grid = $("#artworksGrid");
  grid.empty();

  if (artworks.length === 0) {
    grid.html(`
                <div class="no-artworks">
                    <i class="bi bi-image"></i>
                    <p>No artworks found.</p>
                </div>
            `);
  } else {
    artworks.forEach(function (artwork) {
      const hasImage = artwork.image_path && artwork.image_path.trim() !== "";
      const safeTitle = escapeHtml(artwork.title);
      const safeArtistName = escapeHtml(
        artwork.artist_name || "Unknown Artist"
      );
      const safeImagePath = "../" + escapeHtml(artwork.image_path);
      const statusClass = artwork.status === 'Approved' ? 'badge-success' : artwork.status === 'Rejected' ? 'badge-danger' : 'badge-warning';
      const statusText = artwork.status ? artwork.status.charAt(0).toUpperCase() + artwork.status.slice(1) : 'Pending';

      const card = $(`
                    <div class="artwork-card" data-id="${artwork.id}">
                        <div class="artwork-image">
                            ${
                              hasImage
                                ? `<img src="${safeImagePath}" alt="${safeTitle}" onerror="this.parentElement.innerHTML='<div class=\\'artwork-placeholder\\'><i class=\\'bi bi-image\\'></i></div>'">`
                                : `<div class="artwork-placeholder"><i class="bi bi-image"></i></div>`
                            }
                        </div>
                        <div class="artwork-info">
                            <div class="artwork-title">${safeTitle}</div>
                            <div class="artwork-artist">
                                <i class="bi bi-person-fill"></i>
                                <span>${safeArtistName}</span>
                            </div>
                            <div class="artwork-status">
                                <span class="badge ${statusClass}">${statusText}</span>
                            </div>
                        </div>
                    </div>
                `);
      grid.append(card);
    });
  }
}

// SHOW ARTWORK DETAIL FUNCTION
function showArtworkDetail(artworkId) {
  const artwork = allArtworks.find((a) => a.id == artworkId);
  if (!artwork) return;

  currentArtworkId = artworkId;

  $("#detailTitle").text(artwork.title);

  // Handle image display with fallback
  const hasImage = artwork.image_path && artwork.image_path.trim() !== "";
  const $detailImage = $("#detailImage");

  if (hasImage) {
    $detailImage.attr("src", "../" + escapeHtml(artwork.image_path));
    $detailImage.show();
    $detailImage.off("error").on("error", function () {
      $(this).attr(
        "src",
        'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="400"%3E%3Crect width="400" height="400" fill="%231a1a1a"/%3E%3Ctext x="50%25" y="50%25" font-size="80" fill="%23333" text-anchor="middle" dy=".3em"%3E📷%3C/text%3E%3C/svg%3E'
      );
    });
  } else {
    $detailImage.attr(
      "src",
      'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="400"%3E%3Crect width="400" height="400" fill="%231a1a1a"/%3E%3Ctext x="50%25" y="50%25" font-size="80" fill="%23333" text-anchor="middle" dy=".3em"%3E📷%3C/text%3E%3C/svg%3E'
    );
  }

  $("#detailArtist").text(artwork.artist_name || "Unknown Artist");
  $("#detailCategory").text(artwork.category_name || "Uncategorized");
  $("#detailYear").text(artwork.year_created || "Not Provided");
  $("#detailStatus").html(`<span class="badge ${artwork.status === 'Approved' ? 'badge-success' : artwork.status === 'Rejected' ? 'badge-danger' : 'badge-warning'}">${artwork.status || 'Pending'}</span>`);
  $("#detailDescription").text(
    artwork.description || "No description available"
  );

  $("#detailModal").modal("show");
}

// EDIT ARTWORK FUNCTION
function editArtwork(artworkId) {
  const artwork = allArtworks.find((a) => a.id == artworkId);
  if (!artwork) return;

  isEditMode = true;
  $("#artworkModalLabel").html(
    '<i class="bi bi-pencil-square"></i> Edit Artwork'
  );
  $("#artworkImage").prop("required", false);

  $("#artworkId").val(artwork.id);
  $("#artworkTitle").val(artwork.title);
  $("#artworkDescription").val(artwork.description);
  $("#artworkArtist").val(artwork.artist_id);
  $("#artworkCategory").val(artwork.category_id || "");
  $("#artworkYear").val(artwork.year_created || "");
  $("#artworkStatus").val(artwork.status || "Pending");
  $("#existingImagePath").val(artwork.image_path || "");

  $("#artworkModal").modal("show");
}

// RESET FORM FUNCTION
function resetForm() {
  $('#artworkFormContainer input[type="text"]').val("");
  $('#artworkFormContainer input[type="number"]').val("");
  $('#artworkFormContainer input[type="file"]').val("");
  $("#artworkFormContainer textarea").val("");
  $("#artworkFormContainer select").val("");
  $("#artworkStatus").val("Pending");
  $("#artworkId").val("");
  $("#existingImagePath").val("");
  $(".error-message").text("");
  $(".form-control").removeClass("is-invalid");
  isEditMode = false;
  $("#btnSaveArtwork").prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Artwork');
}

// VALIDATE ARTWORK FORM FUNCTION
function validateArtworkForm() {
  let isValid = true;
  $(".error-message").text("");
  $(".form-control").removeClass("is-invalid");

  // TITLE VALIDATION
  const title = $("#artworkTitle").val().trim();
  if (title === "") {
    $("#errorArtworkTitle").text("Title is required");
    $("#artworkTitle").addClass("is-invalid");
    isValid = false;
  } else if (title.length < 3) {
    $("#errorArtworkTitle").text("Title must be at least 3 characters");
    $("#artworkTitle").addClass("is-invalid");
    isValid = false;
  }

  // DESCRIPTION VALIDATION
  const description = $("#artworkDescription").val().trim();
  if (description === "") {
    $("#errorArtworkDescription").text("Description is required");
    $("#artworkDescription").addClass("is-invalid");
    isValid = false;
  } else if (description.length < 10) {
    $("#errorArtworkDescription").text(
      "Description must be at least 10 characters"
    );
    $("#artworkDescription").addClass("is-invalid");
    isValid = false;
  }

  // Image validation
  if (!isEditMode) {
    const imageFile = $("#artworkImage")[0].files[0];
    if (!imageFile) {
      $("#errorArtworkImage").text("Image is required");
      $("#artworkImage").addClass("is-invalid");
      isValid = false;
    } else {
      const validTypes = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/gif",
      ];
      if (!validTypes.includes(imageFile.type)) {
        $("#errorArtworkImage").text(
          "Please upload a valid image (JPG, PNG, or GIF)"
        );
        $("#artworkImage").addClass("is-invalid");
        isValid = false;
      } else if (imageFile.size > 5 * 1024 * 1024) {
        $("#errorArtworkImage").text("Image size must be less than 5MB");
        $("#artworkImage").addClass("is-invalid");
        isValid = false;
      }
    }
  } else {
    const imageFile = $("#artworkImage")[0].files[0];
    if (imageFile) {
      const validTypes = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/gif",
      ];
      if (!validTypes.includes(imageFile.type)) {
        $("#errorArtworkImage").text(
          "Please upload a valid image (JPG, PNG, or GIF)"
        );
        $("#artworkImage").addClass("is-invalid");
        isValid = false;
      } else if (imageFile.size > 5 * 1024 * 1024) {
        $("#errorArtworkImage").text("Image size must be less than 5MB");
        $("#artworkImage").addClass("is-invalid");
        isValid = false;
      }
    }
  }

  // ARTIST VALIDATION
  const artistId = $("#artworkArtist").val();
  if (artistId === "" || artistId === "Select Artist") {
    $("#errorArtworkArtist").text("Please select an artist");
    $("#artworkArtist").addClass("is-invalid");
    isValid = false;
  }

  // CATEGORY VALIDATION
  const categoryId = $("#artworkCategory").val();
  if (categoryId === "" || categoryId === "Select Category") {
    $("#errorArtworkCategory").text("Please select a category");
    $("#artworkCategory").addClass("is-invalid");
    isValid = false;
  }

  // YEAR CREATED VALIDATION
  const year = $("#artworkYear").val();
  if (year !== "") {
    const yearNum = parseInt(year);
    const currentYear = new Date().getFullYear();
    if (yearNum < 1000 || yearNum > currentYear) {
      $("#errorArtworkYear").text(`Year must be between 1000 and ${currentYear}`);
      $("#artworkYear").addClass("is-invalid");
      isValid = false;
    }
  }

  return isValid;
}

//////////////////////////////////////////////////////////////////////// AJAX

// LOAD ARTWORKS
function loadArtworks() {
  $.ajax({
    url: "../ws/WsArtworks.php",
    method: "GET",
    dataType: "json",
    success: function (data) {
      if (data == null || data.length == 0) {
        allArtworks = [];
        filteredArtworks = [];
      } else {
        allArtworks = data;
      }
      // If loadParams were set by another page (e.g., clicking a category),
      // apply them to the search/filter inputs before filtering.
      try {
        if (window.loadParams) {
          if (window.loadParams.search !== undefined) {
            $("#searchArtwork").val(window.loadParams.search);
          }
          if (window.loadParams.status !== undefined) {
            $("#filterStatus").val(window.loadParams.status);
          }
          // NOTE: do NOT delete loadParams here because category select
          // options may be populated later by loadCategories().
        }
      } catch (e) {
        // ignore if inputs not present
      }

      filterAndDisplayArtworks();

      // If navigation requested a specific artwork id (e.g., from notifications),
      // open its detail after artworks are loaded.
      try {
        if (window.loadParams && window.loadParams.id !== undefined) {
          const targetId = window.loadParams.id;
          // delete param first to avoid loops
          delete window.loadParams;
          // ensure artworks are present then show detail
          setTimeout(function() {
            showArtworkDetail(targetId);
          }, 50);
        }
      } catch (e) {
        // ignore
      }
    },
    error: function (xhr, status, error) {
      let message = error;
      if (xhr.responseText) {
        try {
          let json = JSON.parse(xhr.responseText);
          if (json.message) {
            message = json.message;
          }
        } catch(e) {
          // keep message
        }
      }
      showAlert(
        "error",
        "Error",
        "Error loading artworks: " + message + ". Please try again."
      );
    },
  });
}

// LOAD CATEGORIES FUNCTION
function loadCategories() {
  $.ajax({
    url: "../ws/WsCategories.php",
    method: "GET",
    dataType: "json",
    success: function (data) {
      allCategories = data || [];
      if (data && data.length > 0) {
        const filterSelect = $("#filterCategory");
        const formSelect = $("#artworkCategory");

        // Clear previous options except the first (default) option
        filterSelect.find("option:not(:first)").remove();
        formSelect.find("option:not(:first)").remove();

        data.forEach(function (category) {
          const safeName = escapeHtml(category.name);
          filterSelect.append(
            `<option value="${category.id}">${safeName}</option>`
          );
          formSelect.append(
            `<option value="${category.id}">${safeName}</option>`
          );
        });
        // If loader set parameters (e.g., clicked a category), apply category filter now
        try {
          if (window.loadParams && window.loadParams.category !== undefined) {
            filterSelect.val(window.loadParams.category);
            // ensure any search/status set earlier are preserved
            filterAndDisplayArtworks();
            // clear loadParams after applying
            delete window.loadParams;
          }
        } catch (e) {
          // ignore
        }
      }
    },
    error: function (xhr, status, error) {
      showAlert("error", "Error", "Failed to load categories.");
      allCategories = [];
    },
  });
}

// LOAD ARTISTS FUNCTION
function loadArtists() {
  $.ajax({
    url: "../ws/WsArtists.php",
    method: "GET",
    dataType: "json",
    success: function (data) {
      if (data && data.length > 0) {
        const artistSelect = $("#artworkArtist");

        data.forEach(function (artist) {
          const safeName = escapeHtml(artist.name);
          artistSelect.append(
            `<option value="${artist.id}">${safeName}</option>`
          );
        });
      }
    },
    error: function (xhr, status, error) {
      showAlert("error", "Error", "Failed to load artists.");
    },
  });
}

// ADD ARTWORK FUNCTION
function addArtwork() {
  const formData = new FormData();

  const title = $("#artworkTitle").val().trim();
  const description = $("#artworkDescription").val().trim();
  const artistId = $("#artworkArtist").val();
  const categoryId = $("#artworkCategory").val();
  const yearCreated = $("#artworkYear").val().trim();
  const imageFile = $("#artworkImage")[0].files[0];

  formData.append("action", "add");
  formData.append("title", title);
  formData.append("description", description);
  formData.append("artist_id", artistId);
  formData.append("category_id", categoryId);
  formData.append("year_created", yearCreated);
  formData.append("status", $("#artworkStatus").val());
  formData.append("image", imageFile);

  $.ajax({
    url: "../ws/WsArtworks.php",
    method: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        $("#artworkModal").modal("hide");
        resetForm();
        loadArtworks();
        showAlert(
          "success",
          "Success",
          "Artwork added successfully!"
        );
      } else {
        $("#btnSaveArtwork").prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Artwork');
        showAlert(
          "error",
          "Error",
          response.message || "Could not add artwork"
        );
      }
    },
    error: function (xhr, status, error) {
      $("#btnSaveArtwork").prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Artwork');
      showAlert(
        "error",
        "Error",
        'An error "' +
          error +
          '" occurred while adding the artwork. Please try again.'
      );
    },
  });
}

// UPDATE ARTWORK FUNCTION
function updateArtwork() {
  const formData = new FormData();

  const id = $("#artworkId").val().trim();
  const title = $("#artworkTitle").val().trim();
  const description = $("#artworkDescription").val().trim();
  const artistId = $("#artworkArtist").val();
  const categoryId = $("#artworkCategory").val();
  const yearCreated = $("#artworkYear").val().trim();
  const imageFile = $("#artworkImage")[0].files[0];
  const existingImagePath = $("#existingImagePath").val().trim();

  formData.append("action", "update");
  formData.append("artwork_id", id);
  formData.append("title", title);
  formData.append("description", description);
  formData.append("artist_id", artistId);
  formData.append("category_id", categoryId);
  formData.append("year_created", yearCreated);
  formData.append("status", $("#artworkStatus").val());
  formData.append("old_image_path", existingImagePath);

  if (imageFile) {
    formData.append("image", imageFile);
  }

  $.ajax({
    url: "../ws/WsArtworks.php",
    method: "POST",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        $("#artworkModal").modal("hide");
        resetForm();
        loadArtworks();
        showAlert(
          "success",
          "Success",
          "Artwork updated successfully!"
        );
      } else {
        $("#btnSaveArtwork").prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Artwork');
        showAlert(
          "error",
          "Error",
          response.message || "Could not update artwork"
        );
      }
    },
    error: function (xhr, status, error) {
      $("#btnSaveArtwork").prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Artwork');
      showAlert(
        "error",
        "Error",
        'An error "' +
          error +
          '" occurred while updating the artwork. Please try again.'
      );
    },
  });
}

// DELETE ARTWORK FUNCTION
function deleteArtwork(id) {
  $.ajax({
    url: "../ws/WsArtworks.php",
    method: "POST",
    data: {
      action: "delete",
      id: id,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        showAlert(
          "success",
          "Success",
          "Artwork deleted successfully!",
          function () {
            loadArtworks();
          }
        );
      } else {
        showAlert(
          "error",
          "Error",
          response.message || "Could not delete artwork"
        );
      }
    },
    error: function (xhr, status, error) {
      showAlert(
        "error",
        "Error",
        'An error "' +
          error +
          '" occurred while deleting the artwork. Please try again.'
      );
    },
  });
}

// Function to populate year dropdowns
function populateYearDropdowns() {
  const currentYear = new Date().getFullYear();
  const yearSelect = $("#artworkYear");

  if (yearSelect.length === 0) return;

  // Clear existing options
  yearSelect.empty();

  // Add default option
  yearSelect.append('<option value="">Select Year (Optional)</option>');

  // Populate year (from current year to 1000)
  for (let year = currentYear; year >= 1000; year--) {
    yearSelect.append(`<option value="${year}">${year}</option>`);
  }
}

window.initializeArtworks = initializeArtworks;

})();
