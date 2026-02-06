(function() {
$(document).ready(function () {
  // Populate dropdowns will be called in initializeArtists
});

let isEditMode = false;
let allArtists = [];
let filteredArtists = [];
let currentPage = 1;
const itemsPerPage = 5;

// Function to initialize artists when content is loaded dynamically
function initializeArtists() {
  // Populate dropdowns
  populateYearDropdowns();
  populateCountries();

  // Initial load
  loadArtists();

  // Search functionality
  $(document).on("keyup", "#searchArtist", function () {
    currentPage = 1;
    filterAndDisplayArtists();
  });

  // Filter functionality
  $(document).on("change", "#filterArtistType", function () {
    currentPage = 1;
    filterAndDisplayArtists();
  });

  // SHOW ALL BUTTON
  $(document).on("click", "#btnShowAllArtists", function () {
    currentPage = 1;
    $("#searchArtist").val("");
    $("#filterArtistType").val("");
    filterAndDisplayArtists();
  });

  // Pagination buttons
  $(document).on("click", "#btnFirstPage", function () {
    currentPage = 1;
    displayCurrentPage();
  });

  // Previous button handlers
  $(document).on("click", "#btnPrevPage", function () {
    if (currentPage > 1) {
      currentPage--;
      displayCurrentPage();
    }
  });

  // Next button handler
  $(document).on("click", "#btnNextPage", function () {
    const totalPages = Math.ceil(filteredArtists.length / itemsPerPage);
    if (currentPage < totalPages) {
      currentPage++;
      displayCurrentPage();
    }
  });

  // Last page button handler
  $(document).on("click", "#btnLastPage", function () {
    const totalPages = Math.ceil(filteredArtists.length / itemsPerPage);
    currentPage = totalPages;
    displayCurrentPage();
  });

  // Add Artist Button - Reset form
  $(document).on("click", "#btnAddArtist", function () {
    isEditMode = false;
    resetForm();
    $("#artistModalLabel").html('<i class="bi bi-person-plus"></i> Add Artist');
  });

  // Save Artist Button
  $(document).on("click", "#btnSaveArtist", function () {
    if (validateArtistForm()) {
      if (isEditMode) {
        updateArtist();
      } else {
        addArtist();
      }
    }
  });

  // Clear error on input
  $(
    "#artistName, #artistBiography, #artistCountry, #artistType, #artistBirthYear, #artistDeathYear"
  ).on("input change", function () {
    $(this).removeClass("is-invalid");
    const errorId =
      "#error" +
      $(this).attr("id").charAt(0).toUpperCase() +
      $(this).attr("id").slice(1);
    $(errorId).text("");
  });

  // Edit Button Click (scoped to artists table body)
  $('#artistsTableBody').off('click.artistEdit').on('click.artistEdit', '.edit-btn', function() {
    isEditMode = true;
    $('#artistModalLabel').html('<i class="bi bi-pencil-square"></i> Edit Artist');
    
    $('#artistId').val($(this).data('id'));
    $('#artistName').val($(this).data('name'));
    $('#artistBiography').val($(this).data('biography'));
    $('#artistCountry').val($(this).data('country-id'));
    $('#artistBirthYear').val($(this).data('birth-year'));
    $('#artistDeathYear').val($(this).data('death-year'));
    $('#artistType').val($(this).data('artist-type'));
  });

  // Delete Button Click (scoped to artists table body)
  $('#artistsTableBody').off('click.artistDelete').on('click.artistDelete', '.delete-btn', function () {
    const artistId = $(this).data("id");
    const artistName = $(this).data("name");

    showConfirm(
      "Delete artist",
      `Are you sure you want to delete artist "${artistName}"? This action cannot be undone.`,
      function () {
        deleteArtist(artistId);
      }
    );
  });

  //////////////////////////////////////////////////////////////////////// FUNCTIONS

  // VALIDATION FUNCTION
  function validateArtistForm() {
    let isValid = true;

    // Clear all previous errors
    $(".error-message").text("");
    $(".form-control").removeClass("is-invalid");

    // Validate Name
    const name = $("#artistName").val().trim();
    if (name === "") {
      $("#errorArtistName").text("Name is required");
      $("#artistName").addClass("is-invalid");
      isValid = false;
    } else if (name.length < 2) {
      $("#errorArtistName").text("Name must be at least 2 characters");
      $("#artistName").addClass("is-invalid");
      isValid = false;
    }

    // Validate Biography
    const biography = $("#artistBiography").val().trim();
    if (biography === "") {
      $("#errorArtistBiography").text("Biography is required");
      $("#artistBiography").addClass("is-invalid");
      isValid = false;
    } else if (biography.length < 10) {
      $("#errorArtistBiography").text(
        "Biography must be at least 10 characters"
      );
      $("#artistBiography").addClass("is-invalid");
      isValid = false;
    }

    // Validate Country
    const country = $('#artistCountry').val();
    if (country === '' || country === 'Select Country') {
        $('#errorArtistCountry').text('Please select a country');
        $('#artistCountry').addClass('is-invalid');
        isValid = false;
    }

    // Validate Artist Type
    const artistType = $("#artistType").val();
    if (artistType === "" || artistType === "Select Type") {
      $("#errorArtistType").text("Please select an artist type");
      $("#artistType").addClass("is-invalid");
      isValid = false;
    }

    // Validate Birth Year (optional but must be valid if provided)
    const birthYear = $("#artistBirthYear").val();
    if (birthYear !== "") {
      if (!/^\d{4}$/.test(birthYear)) {
        $("#errorArtistBirthYear").text("Birth year must be a 4-digit number");
        $("#artistBirthYear").addClass("is-invalid");
        isValid = false;
      } else {
        const year = parseInt(birthYear);
        const currentYear = new Date().getFullYear();
        if (year < 1000 || year > currentYear) {
          $("#errorArtistBirthYear").text(
            `Birth year must be between 1000 and ${currentYear}`
          );
          $("#artistBirthYear").addClass("is-invalid");
          isValid = false;
        }
      }
    }

    // Validate Death Year (optional but must be valid if provided)
    const deathYear = $("#artistDeathYear").val();
    if (deathYear !== "") {
      if (!/^\d{4}$/.test(deathYear)) {
        $("#errorArtistDeathYear").text("Death year must be a 4-digit number");
        $("#artistDeathYear").addClass("is-invalid");
        isValid = false;
      } else {
        const year = parseInt(deathYear);
        if (year < 1000 || year > 2025) {
          $("#errorArtistDeathYear").text(
            "Death year must be between 1000 and 2025"
          );
          $("#artistDeathYear").addClass("is-invalid");
          isValid = false;
        }

        // Check if death year is after birth year
        if (birthYear !== "" && parseInt(deathYear) < parseInt(birthYear)) {
          $("#errorArtistDeathYear").text(
            "Death year must be after birth year"
          );
          $("#artistDeathYear").addClass("is-invalid");
          isValid = false;
        }
      }
    }

    return isValid;
  }

  // POPULATE YEAR DROPDOWNS FUNCTION
  function populateYearDropdowns() {
    const currentYear = new Date().getFullYear();
    const birthYearSelect = $("#artistBirthYear");
    const deathYearSelect = $("#artistDeathYear");

    // Populate birth year (from 1000 to current year)
    for (let year = currentYear; year >= 1000; year--) {
      birthYearSelect.append(`<option value="${year}">${year}</option>`);
    }

    // Populate death year (from 1000 to current year for future dates)
    for (let year = currentYear; year >= 1000; year--) {
      deathYearSelect.append(`<option value="${year}">${year}</option>`);
    }
  }

  // FILTER AND DISPLAY ARTISTS
  function filterAndDisplayArtists() {
    const search = $("#searchArtist").val().toLowerCase();
    const artistType = $("#filterArtistType").val();

    filteredArtists = allArtists.filter(function (artist) {
      const matchesSearch =
        search === "" || artist.name.toLowerCase().includes(search);
      const matchesType =
        artistType === "" || artist.artist_type === artistType;
      return matchesSearch && matchesType;
    });

    displayCurrentPage();
  }

  // DISPLAY CURRENT PAGE
  function displayCurrentPage() {
    const totalPages = Math.ceil(filteredArtists.length / itemsPerPage) || 1;
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(
      startIndex + itemsPerPage,
      filteredArtists.length
    );
    const pageArtists = filteredArtists.slice(startIndex, endIndex);

    populateArtistsTable(pageArtists);
    updatePaginationControls(totalPages, startIndex, endIndex);
  }

  // UPDATE PAGINATION CONTROLS
  function updatePaginationControls(totalPages, startIndex, endIndex) {
    $("#currentPage").text(currentPage);
    $("#totalPages").text(totalPages);
    $("#totalArtists").text(filteredArtists.length);
    $("#showingStart").text(filteredArtists.length > 0 ? startIndex + 1 : 0);
    $("#showingEnd").text(endIndex);

    // Enable/disable buttons
    $("#btnFirstPage, #btnPrevPage").prop("disabled", currentPage === 1);
    $("#btnNextPage, #btnLastPage").prop(
      "disabled",
      currentPage === totalPages || filteredArtists.length === 0
    );
  }

  // POPULATE ARTISTS TABLE FUNCTION
  function populateArtistsTable(artists) {
    let artistsTableBody = $("#artistsTableBody");
    artistsTableBody.empty();
    let row;

    if (artists.length == 0) {
      row = `<tr>
                <td colspan="7" class="text-center">No artists found.</td>
            </tr>`;
      artistsTableBody.append(row);
    } else {
      artists.forEach(function (artist) {
        // Handle death year - show "Alive" if null, empty, or "N/A"
        let deathYear = artist.death_year;
        if (
          !deathYear ||
          deathYear === "N/A" ||
          deathYear === "" ||
          deathYear === null
        ) {
          deathYear = "Alive";
        }

        const birthYear = artist.birth_year || "N/A";

        row = `<tr>
                    <td>${artist.name}</td>
                    <td>${artist.biography.substring(0, 50)}${
          artist.biography.length > 50 ? "..." : ""
        }</td>
                    <td>${artist.country_name}</td>
                    <td>${birthYear}</td>
                    <td>${deathYear}</td>
                    <td>${artist.artist_type}</td>
                    <td class="text-center">
                        <button
                            class="btn btn-outline-primary btn-sm edit-btn"
                            data-toggle="modal" 
                            data-target="#artistModal"
                            data-id="${artist.id}"
                            data-name="${artist.name}"
                            data-biography="${artist.biography}"
                            data-country-id="${artist.country_id}"
                            data-birth-year="${birthYear}"
                            data-death-year="${artist.death_year || ""}"
                            data-artist-type="${artist.artist_type}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button 
                            class="btn btn-outline-danger btn-sm delete-btn"
                            data-id="${artist.id}"
                            data-name="${artist.name}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
        artistsTableBody.append(row);
      });
    }
  }

  // RESET FORM FUNCTION
  function resetForm() {
    $('#artistFormContainer input[type="text"]').val("");
    $("#artistFormContainer textarea").val("");
    $("#artistFormContainer select").val("");
    $("#artistId").val("");
    $(".error-message").text("");
    $(".form-control").removeClass("is-invalid");
    isEditMode = false;
  }

  //////////////////////////////////////////////////////////////////////// AJAX

  // LOAD ARTISTS FUNCTION
  function loadArtists() {
    $.ajax({
      url: "../ws/WsArtists.php",
      method: "GET",
      dataType: "json",
      success: function (data) {
        if (data == null || data.length == 0) {
          allArtists = [];
          filteredArtists = [];
        } else {
          allArtists = data;
        }
        currentPage = 1;
        filterAndDisplayArtists();
      },
      error: function (xhr, status, error) {
        showAlert(
          "error",
          "Error",
          "An error " +
            error +
            " occured while loading artists. Please try again."
        );
      },
    });
  }

  // ADD ARTIST FUNCTION
  function addArtist() {
    const artistData = {
      action: "add",
      name: $("#artistName").val().trim(),
      biography: $("#artistBiography").val().trim(),
      country_id: $("#artistCountry").val(),
      birth_year: $("#artistBirthYear").val() || null,
      death_year: $("#artistDeathYear").val() || null,
      artist_type: $("#artistType").val(),
    };

    $.ajax({
      url: "../ws/WsArtists.php",
      method: "POST",
      data: artistData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#artistModal").modal("hide");
          loadArtists();
          resetForm();
          showAlert(
            "success",
            "Success",
            "Artist added successfully!"
          );
        } else {
          showAlert(
            "error",
            "Error",
            response.message || "Could not add artist"
          );
        }
      },
      error: function (xhr, status, error) {
        showAlert(
          "error",
          "Error",
          "An error " +
            error +
            " occurred while adding the artist. Please try again."
        );
      },
    });
  }

  // UPDATE ARTIST FUNCTION
  function updateArtist() {
    const artistData = {
      action: "update",
      id: $("#artistId").val(),
      name: $("#artistName").val().trim(),
      biography: $("#artistBiography").val().trim(),
      country_id: $("#artistCountry").val(),
      birth_year: $("#artistBirthYear").val() || null,
      death_year: $("#artistDeathYear").val() || null,
      artist_type: $("#artistType").val(),
    };

    $.ajax({
      url: "../ws/WsArtists.php",
      method: "POST",
      data: artistData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#artistModal").modal("hide");
          loadArtists();
          resetForm();
          showAlert(
            "success",
            "Success",
            "artist updated successfully!"
          );
        } else {
          showAlert(
            "error",
            "Error",
            response.message || "Could not update artist"
          );
        }
      },
      error: function (xhr, status, error) {
        showAlert(
          "error",
          "Error",
          "An error " +
            error +
            " occurred while updating the artist. Please try again."
        );
      },
    });
  }

  // DELETE ARTIST FUNCTION
  function deleteArtist(id) {
    $.ajax({
      url: "../ws/WsArtists.php",
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
            "Artist deleted successfully!",
            function () {
              // If current page becomes empty after deletion, go to previous page
              if (
                filteredArtists.length % itemsPerPage === 1 &&
                currentPage > 1
              ) {
                currentPage--;
              }
              loadArtists();
            }
          );
        } else {
          showAlert(
            "error",
            "Error",
            response.message || "Could not delete artist"
          );
        }
      },
      error: function (xhr, status, error) {
        showAlert(
          "error",
          "Error",
          "An error " +
            error +
            " occurred while deleting the artist. Please try again."
        );
      },
    });
  }

  // POPULATE COUNTRIES FUNCTION
  function populateCountries() {
    $.ajax({
      url: "../ws/WsCountries.php",
      method: "GET",
      dataType: "json",
      success: function(data) {
          const countrySelect = $('#artistCountry');
          countrySelect.find('option:not(:first)').remove();
          
          if (data && data.length > 0) {
              data.forEach(function(country) {
                  countrySelect.append(`<option value="${country.id}">${country.name}</option>`);
              });
          }
      },
      error: function() {
          console.error('Failed to load countries');
          showAlert('error', 'Error', 'Failed to load countries');
      }
    });
}
}

window.initializeArtists = initializeArtists;

})();
