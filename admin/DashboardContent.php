<?php
// Protect this page - must be logged in
require_once __DIR__ . "/../CheckSession.php";
requireAdmin(); // Only admins can access this page
?>

<div id="dashboardContainer">
<!-- Dashboard Hero Section -->
<section class="dashboard-hero">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="welcome-badge">Dashboard Overview</div>
                <h1>Welcome back, <span id="adminName"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</h1>
                <p>Here's what's happening with Art Noir</p>
            </div>
            <div class="col-lg-4 text-right">
                <div class="date-display">
                    <i class="bi bi-calendar-event"></i>
                    <span id="currentDate"></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dashboard Filters -->
<section class="dashboard-filters">
    <div class="container-fluid">
        <div class="filters-card">
            <div class="filters-header">
                <h3><i class="bi bi-funnel-fill"></i> Dashboard Filters</h3>
                <div class="filter-actions">
                    <button class="btn-filter-export" id="btnExportPDF">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </button>
                    <button class="btn-filter-reset" id="btnResetFilters">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
                    </button>
                    <button class="btn-filter-refresh" id="btnRefreshDashboard">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="filters-body">
                <div class="filter-group">
                    <label><i class="bi bi-calendar-range"></i> Time Range</label>
                    <select class="filter-select" id="filterTimeRange">
                        <option value="all" selected >All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month" >This Month</option>
                        <option value="year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                <div class="filter-group filter-group-custom" id="customRangeGroup" style="display: none;">
                    <label><i class="bi bi-calendar3"></i> Custom Date Range</label>
                    <div class="date-range-inputs">
                        <input type="date" class="filter-input" id="filterStartDate">
                        <span class="date-separator">to</span>
                        <input type="date" class="filter-input" id="filterEndDate">
                    </div>
                </div>

                <div class="filter-group">
                    <label><i class="bi bi-tag-fill"></i> Category</label>
                    <select class="filter-select" id="filterCategory">
                        <option value="all">All Categories</option>
                        <!-- Categories loaded dynamically -->
                    </select>
                </div>

                <div class="filter-group">
                    <label><i class="bi bi-palette-fill"></i> Artist Type</label>
                    <select class="filter-select" id="filterArtistType">
                        <option value="all">All Types</option>
                        <option value="historical">Historical</option>
                        <option value="community">Community</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label><i class="bi bi-check-circle-fill"></i> Artwork Status</label>
                    <select class="filter-select" id="filterStatus">
                        <option value="all">All Status</option>
                        <option value="Approved">Approved</option>
                        <option value="Pending">Pending</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>

                <div class="filter-group comparison-toggle">
                    <label class="toggle-label">
                        <input type="checkbox" id="filterComparison">
                        <span class="toggle-switch"></span>
                        <span class="toggle-text">
                            <i class="bi bi-graph-up-arrow"></i> Compare to Previous Period
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dashboard Statistics Cards -->
<section class="dashboard-stats">
    <div class="container-fluid">
        <div class="row">
            <!-- Total Users Card -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-users">
                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="totalUsers">0</h3>
                        <p>Total Users</p>
                        <div class="stat-change" id="usersChange">
                            <i class="bi bi-arrow-up"></i>
                            <span>0%</span> vs previous period
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Artists Card -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-artists">
                    <div class="stat-icon">
                        <i class="bi bi-palette-fill"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="totalArtists">0</h3>
                        <p>Total Artists</p>
                        <div class="stat-change" id="artistsChange">
                            <i class="bi bi-arrow-up"></i>
                            <span>0%</span> vs previous period
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Artworks Card -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-artworks">
                    <div class="stat-icon">
                        <i class="bi bi-image-fill"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="totalArtworks">0</h3>
                        <p>Total Artworks</p>
                        <div class="stat-change" id="artworksChange">
                            <i class="bi bi-arrow-up"></i>
                            <span>0%</span> vs previous period
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals Card -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card stat-pending">
                    <div class="stat-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="pendingArtworks">0</h3>
                        <p>Pending Approvals</p>
                        <div class="stat-action">
                            <button class="btn-quick-action" onclick="viewPendingArtworks()">
                                <i class="bi bi-eye"></i> Review Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Charts Section -->
<section class="dashboard-charts">
    <div class="container-fluid">
        <!-- Monthly Growth Chart - Full Width -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-card chart-card-featured">
                    <div class="chart-header">
                        <h3><i class="bi bi-graph-up-arrow"></i> Monthly Growth Trends</h3>
                        <div class="chart-actions">
                            <select class="chart-metric-select" id="growthMetricSelect">
                                <option value="artworks">Artworks</option>
                                <option value="users">Users</option>
                                <option value="artists">Artists</option>
                            </select>
                            <button class="btn-chart-refresh" onclick="refreshGrowthChart()">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-body chart-body-large">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Artworks Status Chart -->
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="bi bi-pie-chart-fill"></i> Artworks by Status</h3>
                        <div class="chart-actions">
                            <button class="btn-chart-refresh" onclick="refreshArtworkStatusChart()">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="artworkStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Categories Distribution Chart -->
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="bi bi-bar-chart-fill"></i> Artworks by Category</h3>
                        <div class="chart-actions">
                            <button class="btn-chart-refresh" onclick="refreshCategoryChart()">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Artist Types Chart -->
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="bi bi-circle-fill"></i> Artists by Type</h3>
                        <div class="chart-actions">
                            <button class="btn-chart-refresh" onclick="refreshArtistTypeChart()">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="artistTypeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- User Roles Chart -->
            <div class="col-lg-6 mb-4">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="bi bi-person-badge-fill"></i> Users by Role</h3>
                        <div class="chart-actions">
                            <button class="btn-chart-refresh" onclick="refreshUserRoleChart()">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                    <div class="chart-body">
                        <canvas id="userRoleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Top Performers Section -->
<section class="dashboard-top-performers">
    <div class="container-fluid">
        <div class="row">
            <!-- Top Artists -->
            <div class="col-lg-6 mb-4">
                <div class="performers-card">
                    <div class="performers-header">
                        <h3><i class="bi bi-trophy-fill"></i> Top Artists</h3>
                        <span class="performers-subtitle">By number of artworks</span>
                    </div>
                    <div class="performers-body" id="topArtistsContainer">
                        <div class="loading-spinner">
                            <i class="bi bi-hourglass-split"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Artworks -->
            <div class="col-lg-6 mb-4">
                <div class="performers-card">
                    <div class="performers-header">
                        <h3><i class="bi bi-clock-history"></i> Recent Artworks</h3>
                        <span class="performers-subtitle">Latest submissions</span>
                    </div>
                    <div class="performers-body" id="recentArtworksContainer">
                        <div class="loading-spinner">
                            <i class="bi bi-hourglass-split"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Activity Timeline -->
<section class="dashboard-activity">
    <div class="container-fluid">
        <div class="activity-card">
            <div class="activity-header">
                <h3><i class="bi bi-activity"></i> Recent Activity</h3>
                <div class="activity-filters">
                    <button class="btn-activity-filter active" data-filter="all">All</button>
                    <button class="btn-activity-filter" data-filter="artworks">Artworks</button>
                    <button class="btn-activity-filter" data-filter="users">Users</button>
                    <button class="btn-activity-filter" data-filter="artists">Artists</button>
                </div>
            </div>
            <div class="activity-body" id="activityTimeline">
                <div class="loading-spinner">
                    <i class="bi bi-hourglass-split"></i> Loading activity...
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<script src="../JS/dashboard.js"></script>
