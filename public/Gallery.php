<!-- Hero Section with Search and Filters -->

<section class="hero-section">
    <div class="search-filter-container">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Search artworks, artists..." class="search-input">
        </div>
        <div class="filter-box">
            <i class="bi bi-funnel-fill"></i>
            <select id="categoryFilter" class="category-select">
                <option value="">All Categories</option>
            </select>
        </div>
    </div>
</section>

<!-- Gallery -->

<section class="gallery-container">
    <div class="gallery-grid" id="galleryGrid">
        <div class="no-artworks">
            <i class="bi bi-hourglass-split"></i>
            <p>Loading artworks...</p>
        </div>
    </div>
</section>