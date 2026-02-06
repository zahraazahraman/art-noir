// Refactored Dashboard JavaScript - modular, conflict-free
(function (window, $) {
  'use strict';

  if (!window.ArtNoirDashboard) window.ArtNoirDashboard = {};
  const Dashboard = window.ArtNoirDashboard;

  // Internal state
  Dashboard.state = Dashboard.state || {
    initialized: false,
    charts: {
      artworkStatus: null,
      category: null,
      artistType: null,
      userRole: null,
      growth: null
    },
    growthMetric: 'artworks',
    filters: {
      timeRange: 'all',
      startDate: null,
      endDate: null,
      category: 'all',
      artistType: 'all',
      status: 'all',
      comparison: false
    }
  };

  // Track auto-refresh interval to prevent duplicates
  Dashboard.autoRefreshInterval = null;

  /* -------------------------
     Utilities
  ------------------------- */
  Dashboard.escapeHtml = function (text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  };

  Dashboard.loadScript = function (src) {
    return new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = src;
      s.onload = resolve;
      s.onerror = reject;
      document.head.appendChild(s);
    });
  };

  Dashboard.getDateRange = function () {
    const f = Dashboard.state.filters;
    const now = new Date();
    let start, end;

    switch (f.timeRange) {
      case 'today':
        start = new Date(now.setHours(0, 0, 0, 0));
        end = new Date(now.setHours(23, 59, 59, 999));
        break;
      case 'week':
        const weekStart = new Date(now);
        weekStart.setDate(now.getDate() - now.getDay());
        start = new Date(weekStart.setHours(0, 0, 0, 0));
        end = new Date();
        break;
      case 'month':
        start = new Date(now.getFullYear(), now.getMonth(), 1);
        end = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59);
        break;
      case 'year':
        start = new Date(now.getFullYear(), 0, 1);
        end = new Date(now.getFullYear(), 11, 31, 23, 59, 59);
        break;
      case 'custom':
        if (f.startDate && f.endDate) {
          start = new Date(f.startDate);
          end = new Date(f.endDate);
        }
        break;
      default:
        return null;
    }

    return start && end
      ? { start: start.toISOString().split('T')[0], end: end.toISOString().split('T')[0] }
      : null;
  };

  Dashboard.setDate = function () {
    const date = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    $('#currentDate').text(date.toLocaleDateString('en-US', options));
  };

  /* -------------------------
     Chart lifecycle helpers
  ------------------------- */
  Dashboard.destroyAllCharts = function () {
    Object.keys(Dashboard.state.charts).forEach(key => {
      const c = Dashboard.state.charts[key];
      if (c && typeof c.destroy === 'function') {
        try { c.destroy(); } catch (e) { console.warn('Error destroying chart', key, e); }
      }
      Dashboard.state.charts[key] = null;
    });
  };

  /* -------------------------
     Data loaders
  ------------------------- */
  Dashboard.loadCategories = function () {
    return $.getJSON('../ws/WsCategories.php').then(data => {
      const select = $('#filterCategory');
      if (select.length) {
        select.find('option:not(:first)').remove();
        (data || []).forEach(c => select.append(`<option value="${c.id}">${Dashboard.escapeHtml(c.name)}</option>`));
      }
      return data;
    }).catch(() => { console.error('Failed to load categories'); return []; });
  };

  Dashboard.loadStats = function () {
    const range = Dashboard.getDateRange();
    const p = new URLSearchParams();
    if (range) { p.append('start_date', range.start); p.append('end_date', range.end); }
    if (Dashboard.state.filters.comparison) p.append('comparison', 'true');
    if (Dashboard.state.filters.category !== 'all') p.append('category_id', Dashboard.state.filters.category);
    if (Dashboard.state.filters.artistType !== 'all') p.append('artist_type', Dashboard.state.filters.artistType);
    if (Dashboard.state.filters.status !== 'all') p.append('status', Dashboard.state.filters.status);

    return $.getJSON(`../ws/WsDashboardStats.php?${p.toString()}`).then(res => {
      if (!res || !res.success) return;
      $('#totalUsers').text(res.stats.totalUsers);
      $('#totalArtists').text(res.stats.totalArtists);
      $('#totalArtworks').text(res.stats.totalArtworks);
      $('#pendingArtworks').text(res.stats.pendingArtworks);
      if (Dashboard.state.filters.comparison && res.comparison) {
        Dashboard.updateComparisonStats(res.comparison);
      } else {
        Dashboard.resetComparisonStats();
      }
    }).catch(e => console.error('Error loading statistics:', e));
  };

  Dashboard.updateComparisonStats = function (comparison) {
    const update = (selector, change) => {
      const el = $(selector); const span = el.find('span'); const icon = el.find('i');
      if (change > 0) { el.removeClass('negative neutral').addClass('positive'); icon.removeClass('bi-arrow-down bi-arrow-right').addClass('bi-arrow-up'); span.text(`+${change}%`); }
      else if (change < 0) { el.removeClass('positive neutral').addClass('negative'); icon.removeClass('bi-arrow-up bi-arrow-right').addClass('bi-arrow-down'); span.text(`${change}%`); }
      else { el.removeClass('positive negative').addClass('neutral'); icon.removeClass('bi-arrow-up bi-arrow-down').addClass('bi-arrow-right'); span.text('0%'); }
    };
    update('#usersChange', comparison.usersChange);
    update('#artistsChange', comparison.artistsChange);
    update('#artworksChange', comparison.artworksChange);
  };

  Dashboard.resetComparisonStats = function () {
    $('.stat-change').removeClass('positive negative').addClass('neutral');
    $('.stat-change i').removeClass('bi-arrow-up bi-arrow-down').addClass('bi-arrow-right');
    $('.stat-change span').text('0%');
  };

  /* -------------------------
     Charts
  ------------------------- */
  Dashboard.loadGrowthChart = function () {
    const metric = Dashboard.state.growthMetric;
    return $.getJSON(`../ws/WsDashboardGrowth.php?metric=${metric}`).then(data => {
      if (!data || !data.success) return;
      const el = document.getElementById('growthChart');
      if (!el) return;
      const ctx = el.getContext('2d');
      const gradient = ctx.createLinearGradient(0, 0, 0, 400);
      gradient.addColorStop(0, 'rgba(212, 175, 55, 0.4)');
      gradient.addColorStop(1, 'rgba(212, 175, 55, 0.05)');
      Dashboard.destroyAllCharts(); // ensure clean state before creating main charts
      Dashboard.state.charts.growth = new Chart(ctx, {
        type: 'line',
        data: { labels: data.labels, datasets: [{ label: metric.charAt(0).toUpperCase() + metric.slice(1), data: data.data, borderColor: '#d4af37', backgroundColor: gradient, fill: true, tension: 0.4 }] },
        options: { responsive: true, maintainAspectRatio: false }
      });
    }).catch(e => console.error('Error loading growth chart:', e));
  };

  Dashboard.loadArtworkStatusChart = function () {
    const range = Dashboard.getDateRange();
    const p = new URLSearchParams({ chart: 'artwork_status' });
    if (range) { p.append('start_date', range.start); p.append('end_date', range.end); }
    if (Dashboard.state.filters.category !== 'all') p.append('category_id', Dashboard.state.filters.category);
    if (Dashboard.state.filters.artistType !== 'all') p.append('artist_type', Dashboard.state.filters.artistType);

    return $.getJSON(`../ws/WsDashboardCharts.php?${p.toString()}`).then(res => {
      if (!res || !res.success) return;
      const el = document.getElementById('artworkStatusChart'); if (!el) return;
      if (Dashboard.state.charts.artworkStatus && typeof Dashboard.state.charts.artworkStatus.destroy === 'function') {
        try { Dashboard.state.charts.artworkStatus.destroy(); } catch (e) { console.warn('Error destroying artworkStatus chart', e); }
      }
      Dashboard.state.charts.artworkStatus = null;
      const ctx = el.getContext('2d');
      Dashboard.state.charts.artworkStatus = new Chart(ctx, { type: 'doughnut', data: { labels: ['Approved','Pending','Rejected'], datasets: [{ data: [res.data.approved||0,res.data.pending||0,res.data.rejected||0], backgroundColor:['rgba(76,175,80,0.8)','rgba(255,193,7,0.8)','rgba(244,67,54,0.8)'] }] }, options: { responsive:true, maintainAspectRatio:false } });
    }).catch(e => console.error('Error loading artwork status chart:', e));
  };

  Dashboard.loadCategoryChart = function () {
    const range = Dashboard.getDateRange();
    const p = new URLSearchParams({ chart: 'categories' });
    if (range) { p.append('start_date', range.start); p.append('end_date', range.end); }
    if (Dashboard.state.filters.status !== 'all') p.append('status', Dashboard.state.filters.status);
    if (Dashboard.state.filters.artistType !== 'all') p.append('artist_type', Dashboard.state.filters.artistType);

    return $.getJSON(`../ws/WsDashboardCharts.php?${p.toString()}`).then(res => {
      if (!res || !res.success) return;
      const el = document.getElementById('categoryChart'); if (!el) return;
      if (Dashboard.state.charts.category && typeof Dashboard.state.charts.category.destroy === 'function') {
        try { Dashboard.state.charts.category.destroy(); } catch (e) { console.warn('Error destroying category chart', e); }
      }
      Dashboard.state.charts.category = null;
      const ctx = el.getContext('2d');
      Dashboard.state.charts.category = new Chart(ctx, { type: 'bar', data: { labels: res.labels||[], datasets:[{ label:'Number of Artworks', data: res.values||[], backgroundColor:'rgba(212,175,55,0.8)', borderColor:'#d4af37' }] }, options:{ responsive:true, maintainAspectRatio:false } });
    }).catch(e => console.error('Error loading category chart:', e));
  };

  Dashboard.loadArtistTypeChart = function () {
    const range = Dashboard.getDateRange();
    const p = new URLSearchParams({ chart: 'artist_types' });
    if (range) { p.append('start_date', range.start); p.append('end_date', range.end); }

    return $.getJSON(`../ws/WsDashboardCharts.php?${p.toString()}`).then(res => {
      if (!res || !res.success) return;
      const el = document.getElementById('artistTypeChart'); if (!el) return;
      if (Dashboard.state.charts.artistType && typeof Dashboard.state.charts.artistType.destroy === 'function') {
        try { Dashboard.state.charts.artistType.destroy(); } catch (e) { console.warn('Error destroying artistType chart', e); }
      }
      Dashboard.state.charts.artistType = null;
      const ctx = el.getContext('2d');
      Dashboard.state.charts.artistType = new Chart(ctx, { type: 'pie', data: { labels:['Historical','Community'], datasets:[{ data: [res.data.historical||0, res.data.community||0], backgroundColor:['rgba(212,175,55,0.8)','rgba(139,69,19,0.8)'] }] }, options:{ responsive:true, maintainAspectRatio:false } });
    }).catch(e => console.error('Error loading artist type chart:', e));
  };

  Dashboard.loadUserRoleChart = function () {
    const range = Dashboard.getDateRange();
    const p = new URLSearchParams({ chart: 'user_roles' });
    if (range) { p.append('start_date', range.start); p.append('end_date', range.end); }

    return $.getJSON(`../ws/WsDashboardCharts.php?${p.toString()}`).then(res => {
      if (!res || !res.success) return;
      const el = document.getElementById('userRoleChart'); if (!el) return;
      if (Dashboard.state.charts.userRole && typeof Dashboard.state.charts.userRole.destroy === 'function') {
        try { Dashboard.state.charts.userRole.destroy(); } catch (e) { console.warn('Error destroying userRole chart', e); }
      }
      Dashboard.state.charts.userRole = null;
      const ctx = el.getContext('2d');
      Dashboard.state.charts.userRole = new Chart(ctx, { type: 'doughnut', data: { labels:['Admin','User'], datasets:[{ data:[res.data.admin||0, res.data.user||0], backgroundColor:['rgba(220,53,69,0.8)','rgba(0,123,255,0.8)'] }] }, options:{ responsive:true, maintainAspectRatio:false } });
    }).catch(e => console.error('Error loading user role chart:', e));
  };

  /* -------------------------
     Lists / Activity
  ------------------------- */
  Dashboard.loadTopArtists = function () {
    const range = Dashboard.getDateRange();
    const p = new URLSearchParams(); if (range) { p.append('start_date', range.start); p.append('end_date', range.end); }
    if (Dashboard.state.filters.artistType !== 'all') p.append('artist_type', Dashboard.state.filters.artistType);

    return $.getJSON(`../ws/WsDashboardTopArtists.php?${p.toString()}`).then(res => {
      const container = $('#topArtistsContainer').empty();
      if (!res || !res.success) return;
      (res.artists||[]).forEach((a, i) => container.append(`<div class="performer-item"><div class="performer-rank">${i+1}</div><div class="performer-info"><div class="performer-name">${Dashboard.escapeHtml(a.name)}</div><div class="performer-details">${Dashboard.escapeHtml(a.artist_type)}</div></div><div class="performer-count">${a.artwork_count}</div></div>`));
    }).catch(e => { console.error('Error loading top artists:', e); $('#topArtistsContainer').html('<div class="loading-spinner">Error loading data</div>'); });
  };

  Dashboard.loadRecentArtworks = function () {
    const range = Dashboard.getDateRange(); const p = new URLSearchParams(); if (range) { p.append('start_date', range.start); p.append('end_date', range.end); }
    if (Dashboard.state.filters.status !== 'all') p.append('status', Dashboard.state.filters.status);
    if (Dashboard.state.filters.category !== 'all') p.append('category_id', Dashboard.state.filters.category);

    return $.getJSON(`../ws/WsDashboardRecentArtworks.php?${p.toString()}`).then(res => {
      const container = $('#recentArtworksContainer').empty(); if (!res || !res.success) return;
      (res.artworks||[]).forEach(a => container.append(`<div class="recent-artwork-item"><img src="${a.image_path?('../'+a.image_path):'../artworks/placeholder.jpg'}" alt="${Dashboard.escapeHtml(a.title)}" class="recent-artwork-thumb" onerror="this.src='../artworks/placeholder.jpg'"><div class="recent-artwork-info"><div class="recent-artwork-title">${Dashboard.escapeHtml(a.title)}</div><div class="recent-artwork-artist">by ${Dashboard.escapeHtml(a.artist_name)}</div></div><span class="recent-artwork-status ${ (a.status||'').toLowerCase() }">${a.status}</span></div>`));
    }).catch(e => { console.error('Error loading recent artworks:', e); $('#recentArtworksContainer').html('<div class="loading-spinner">Error loading data</div>'); });
  };

  Dashboard.loadActivity = function (filter = 'all') {
    const range = Dashboard.getDateRange(); const p = new URLSearchParams({ filter }); if (range) { p.append('start_date', range.start); p.append('end_date', range.end); }
    return $.getJSON(`../ws/WsDashboardActivity.php?${p.toString()}`).then(res => {
      const container = $('#activityTimeline').empty(); if (!res || !res.success) return;
      (res.activities||[]).forEach(a => container.append(`<div class="activity-item"><div class="activity-icon"><i class="bi ${Dashboard.getActivityIcon(a.type)}"></i></div><div class="activity-content"><div class="activity-title">${Dashboard.escapeHtml(a.title)}</div><div class="activity-description">${Dashboard.escapeHtml(a.description)}</div><div class="activity-time"><i class="bi bi-clock"></i> ${a.time_ago}</div></div></div>`));
    }).catch(e => { console.error('Error loading activity:', e); $('#activityTimeline').html('<div class="loading-spinner">Error loading data</div>'); });
  };

  Dashboard.getActivityIcon = function (type) { const icons = { artwork:'bi-image-fill', user:'bi-person-fill', artist:'bi-palette-fill', category:'bi-tags-fill' }; return icons[type] || 'bi-circle-fill'; };

  /* -------------------------
     Core loaders and init
  ------------------------- */
  Dashboard.loadCharts = function () {
    // create charts in parallel where possible
    return Promise.allSettled([
      Dashboard.loadGrowthChart(),
      Dashboard.loadArtworkStatusChart(),
      Dashboard.loadCategoryChart(),
      Dashboard.loadArtistTypeChart(),
      Dashboard.loadUserRoleChart()
    ]);
  };

  Dashboard.loadAll = function () {
    Dashboard.loadStats();
    Dashboard.loadCharts();
    Dashboard.loadTopArtists();
    Dashboard.loadRecentArtworks();
    Dashboard.loadActivity();
  };

  Dashboard.resetFilters = function () {
    Dashboard.state.filters = { timeRange:'all', startDate:null, endDate:null, category:'all', artistType:'all', status:'all', comparison:false };
    $('#filterTimeRange').val('all'); $('#filterCategory').val('all'); $('#filterArtistType').val('all'); $('#filterStatus').val('all'); $('#filterComparison').prop('checked', false); $('#customRangeGroup').hide();
    Dashboard.loadAll();
    if (typeof showToast === 'function') showToast('info','Filters Reset','All filters have been reset to default',2000);
  };

  Dashboard.bindEvents = function () {
    // Use delegated namespaced events to avoid duplicates
    $(document).off('.artnoir_dashboard')
      .on('change.artnoir_dashboard', '#filterTimeRange', function () { Dashboard.state.filters.timeRange = $(this).val(); $('#customRangeGroup').toggle($(this).val() === 'custom'); if ($(this).val() !== 'custom') Dashboard.loadAll(); })
      .on('change.artnoir_dashboard', '#filterStartDate, #filterEndDate', function () { Dashboard.state.filters.startDate = $('#filterStartDate').val(); Dashboard.state.filters.endDate = $('#filterEndDate').val(); if (Dashboard.state.filters.startDate && Dashboard.state.filters.endDate) Dashboard.loadAll(); })
      .on('change.artnoir_dashboard', '#filterCategory', function () { Dashboard.state.filters.category = $(this).val(); Dashboard.loadAll(); })
      .on('change.artnoir_dashboard', '#filterArtistType', function () { Dashboard.state.filters.artistType = $(this).val(); Dashboard.loadAll(); })
      .on('change.artnoir_dashboard', '#filterStatus', function () { Dashboard.state.filters.status = $(this).val(); Dashboard.loadAll(); })
      .on('change.artnoir_dashboard', '#filterComparison', function () { Dashboard.state.filters.comparison = $(this).is(':checked'); Dashboard.loadAll(); })
      .on('click.artnoir_dashboard', '#btnResetFilters', Dashboard.resetFilters)
      .on('click.artnoir_dashboard', '#btnRefreshDashboard', function () { const i = $(this).find('i').addClass('fa-spin'); Dashboard.loadAll(); setTimeout(() => i.removeClass('fa-spin'), 1000); })
      .on('change.artnoir_dashboard', '#growthMetricSelect', function () { Dashboard.state.growthMetric = $(this).val(); Dashboard.loadGrowthChart(); })
      .on('click.artnoir_dashboard', '.btn-activity-filter', function () { $('.btn-activity-filter').removeClass('active'); $(this).addClass('active'); const filter = $(this).data('filter'); Dashboard.loadActivity(filter); })
      .on('click.artnoir_dashboard', '#btnExportPDF', function () { Dashboard.exportToPDF(); });
  };

  Dashboard.init = function () {
    // Check if dashboard container exists (detect if we're on dashboard page)
    const dashboardContainer = document.getElementById('dashboardContainer') || document.querySelector('[data-dashboard]');
    if (!dashboardContainer) return;

    // Always reinitialize when init is called (even if previously initialized)
    // This handles navigation away and back
    Dashboard.state.initialized = true;
    Dashboard.setDate();
    Dashboard.bindEvents();
    Dashboard.loadCategories();
    Dashboard.loadAll();
    
    // auto refresh - clear any previous intervals to avoid duplicates
    if (Dashboard.autoRefreshInterval) clearInterval(Dashboard.autoRefreshInterval);
    Dashboard.autoRefreshInterval = setInterval(Dashboard.loadAll, 300000);
  };

  /* -------------------------
     Legacy-compatible wrappers
     (exposed both on window.ArtNoirDashboard and as globals)
  ------------------------- */
  Dashboard.viewPendingArtworks = function () { window.loadParams = { status: 'Pending' }; $('#manageArtworksLink').click(); };

  Dashboard.refreshGrowthChart = function () { return Dashboard.loadGrowthChart(); };
  Dashboard.refreshArtworkStatusChart = function () { return Dashboard.loadArtworkStatusChart(); };
  Dashboard.refreshCategoryChart = function () { return Dashboard.loadCategoryChart(); };
  Dashboard.refreshArtistTypeChart = function () { return Dashboard.loadArtistTypeChart(); };
  Dashboard.refreshUserRoleChart = function () { return Dashboard.loadUserRoleChart(); };

  // Attach to ArtNoirDashboard
  window.ArtNoirDashboard = Dashboard;

  // Expose simple global wrappers for inline handlers that still reference old names
  window.initializeDashboard = Dashboard.init;
  window.viewPendingArtworks = Dashboard.viewPendingArtworks;
  window.refreshGrowthChart = Dashboard.refreshGrowthChart;
  window.refreshArtworkStatusChart = Dashboard.refreshArtworkStatusChart;
  window.refreshCategoryChart = Dashboard.refreshCategoryChart;
  window.refreshArtistTypeChart = Dashboard.refreshArtistTypeChart;
  window.refreshUserRoleChart = Dashboard.refreshUserRoleChart;
  window.filterActivity = function (f) { Dashboard.loadActivity(f); };

  /* -------------------------
     Export to PDF - Full featured
  ------------------------- */
  Dashboard.exportToPDF = async function () {
    try {
      if (typeof showToast === 'function') {
        showToast('info', 'Generating PDF', 'Please wait while we prepare your dashboard export...', 2000);
      }

      // Import jsPDF and html2canvas from CDN
      if (typeof window.jspdf === 'undefined') {
        await Dashboard.loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
      }
      if (typeof html2canvas === 'undefined') {
        await Dashboard.loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
      }

      const { jsPDF } = window.jspdf;
      const pdf = new jsPDF('p', 'mm', 'a4');
      const pageWidth = pdf.internal.pageSize.getWidth();
      const pageHeight = pdf.internal.pageSize.getHeight();
      let yOffset = 20;

      // Add header
      pdf.setFillColor(26, 26, 26);
      pdf.rect(0, 0, pageWidth, 40, 'F');

      pdf.setTextColor(212, 175, 55);
      pdf.setFontSize(24);
      pdf.setFont(undefined, 'bold');
      pdf.text('Art Noir Dashboard Report', pageWidth / 2, 20, { align: 'center' });

      pdf.setFontSize(12);
      pdf.setFont(undefined, 'normal');
      pdf.setTextColor(180, 180, 180);
      const date = new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
      pdf.text(`Generated on ${date}`, pageWidth / 2, 30, { align: 'center' });

      yOffset = 50;

      // Add Statistics
      pdf.setTextColor(212, 175, 55);
      pdf.setFontSize(16);
      pdf.setFont(undefined, 'bold');
      pdf.text('Key Statistics', 15, yOffset);
      yOffset += 10;

      pdf.setFontSize(11);
      pdf.setFont(undefined, 'normal');
      pdf.setTextColor(60, 60, 60);

      const stats = [
        { label: 'Total Users', value: $('#totalUsers').text() },
        { label: 'Total Artists', value: $('#totalArtists').text() },
        { label: 'Total Artworks', value: $('#totalArtworks').text() },
        { label: 'Pending Artworks', value: $('#pendingArtworks').text() }
      ];

      stats.forEach((stat, index) => {
        const xPos = 15 + (index % 2) * 95;
        const yPos = yOffset + Math.floor(index / 2) * 15;

        pdf.setDrawColor(212, 175, 55);
        pdf.setFillColor(245, 245, 245);
        pdf.roundedRect(xPos, yPos, 85, 12, 2, 2, 'FD');

        pdf.setTextColor(60, 60, 60);
        pdf.setFont(undefined, 'bold');
        pdf.text(stat.label + ':', xPos + 5, yPos + 8);

        pdf.setTextColor(212, 175, 55);
        pdf.text(stat.value, xPos + 75, yPos + 8, { align: 'right' });
      });

      yOffset += 40;

      // Add filters info if active
      const activeFilters = [];
      if (Dashboard.state.filters.timeRange !== 'all') {
        activeFilters.push(`Time Range: ${$('#filterTimeRange option:selected').text()}`);
      }
      if (Dashboard.state.filters.category !== 'all') {
        activeFilters.push(`Category: ${$('#filterCategory option:selected').text()}`);
      }
      if (Dashboard.state.filters.artistType !== 'all') {
        activeFilters.push(`Artist Type: ${$('#filterArtistType option:selected').text()}`);
      }
      if (Dashboard.state.filters.status !== 'all') {
        activeFilters.push(`Status: ${$('#filterStatus option:selected').text()}`);
      }

      if (activeFilters.length > 0) {
        pdf.setTextColor(212, 175, 55);
        pdf.setFontSize(14);
        pdf.setFont(undefined, 'bold');
        pdf.text('Active Filters', 15, yOffset);
        yOffset += 8;

        pdf.setFontSize(10);
        pdf.setFont(undefined, 'normal');
        pdf.setTextColor(100, 100, 100);
        activeFilters.forEach(filter => {
          pdf.text('• ' + filter, 20, yOffset);
          yOffset += 6;
        });
        yOffset += 5;
      }

      // Add Growth Chart
      if (yOffset > pageHeight - 100) {
        pdf.addPage();
        yOffset = 20;
      }

      pdf.setTextColor(212, 175, 55);
      pdf.setFontSize(14);
      pdf.setFont(undefined, 'bold');
      pdf.text('Monthly Growth Trends', 15, yOffset);
      yOffset += 8;

      const growthCanvas = document.getElementById('growthChart');
      if (growthCanvas) {
        const growthImage = growthCanvas.toDataURL('image/png');
        pdf.addImage(growthImage, 'PNG', 15, yOffset, 180, 80);
        yOffset += 90;
      }

      // Add Top Artists
      if (yOffset > pageHeight - 80) {
        pdf.addPage();
        yOffset = 20;
      }

      pdf.setTextColor(212, 175, 55);
      pdf.setFontSize(14);
      pdf.setFont(undefined, 'bold');
      pdf.text('Top Artists', 15, yOffset);
      yOffset += 8;

      pdf.setFontSize(10);
      pdf.setFont(undefined, 'normal');
      pdf.setTextColor(60, 60, 60);

      const artists = [];
      $('#topArtistsContainer .performer-item').each(function(index) {
        if (index < 5) { // Top 5 for PDF
          const rank = $(this).find('.performer-rank').text();
          const name = $(this).find('.performer-name').text();
          const count = $(this).find('.performer-count').text();
          artists.push({ rank, name, count });
        }
      });

      artists.forEach((artist, index) => {
        pdf.setDrawColor(200, 200, 200);
        pdf.setFillColor(250, 250, 250);
        pdf.roundedRect(15, yOffset, 180, 10, 1, 1, 'FD');

        pdf.setFont(undefined, 'bold');
        pdf.setTextColor(212, 175, 55);
        pdf.text(artist.rank + '.', 20, yOffset + 7);

        pdf.setFont(undefined, 'normal');
        pdf.setTextColor(60, 60, 60);
        pdf.text(artist.name, 30, yOffset + 7);

        pdf.setFont(undefined, 'bold');
        pdf.setTextColor(212, 175, 55);
        pdf.text(artist.count + ' artworks', 185, yOffset + 7, { align: 'right' });

        yOffset += 12;
      });

      // Add footer
      const totalPages = pdf.internal.pages.length - 1;
      for (let i = 1; i <= totalPages; i++) {
        pdf.setPage(i);
        pdf.setFillColor(26, 26, 26);
        pdf.rect(0, pageHeight - 15, pageWidth, 15, 'F');

        pdf.setTextColor(180, 180, 180);
        pdf.setFontSize(9);
        pdf.text('Art Noir Dashboard Report', 15, pageHeight - 7);
        pdf.text(`Page ${i} of ${totalPages}`, pageWidth - 15, pageHeight - 7, { align: 'right' });
      }

      // Save PDF
      const filename = `ArtNoir_Dashboard_${new Date().toISOString().split('T')[0]}.pdf`;
      pdf.save(filename);

      if (typeof showToast === 'function') {
        showToast('success', 'PDF Exported', 'Dashboard has been exported successfully!', 3000);
      }

    } catch (error) {
      console.error('Error exporting PDF:', error);
      if (typeof showError === 'function') {
        showError('Failed to export PDF. Please try again.');
      }
    }
  };

  // Initialize on DOM ready only if admin/dashboard content present
  $(document).ready(function () {
    // don't auto-init globally; call initializeDashboard from admin loader when content is injected
  });

})(window, jQuery);