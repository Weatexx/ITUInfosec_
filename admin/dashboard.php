<?php
session_start();
require_once 'config.php';
require_once 'security_utils.php';

// Simple session check
if (!isset($_SESSION['loggedin'])) {
  header('Location: login.php');
  exit;
}

// Get CSRF token for logout form
$csrfToken = getCsrfToken();
?>
<!doctype html>
<html lang="tr">

<head>
  <meta charset="utf-8" />
  <title>Admin Paneli | ITU CTF'26</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Fonts -->
  <!-- Fonts: Inherited from modern.css -->

  <!-- Custom Cyber Theme -->
  <link rel="stylesheet" href="../root/css/modern.css">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    /* Admin Specific Layout Overrides */
    body {
      overflow: hidden;
      /* Main scroll handled by content area */
    }

    .app-wrapper {
      display: flex;
      height: 100vh;
      width: 100vw;
      position: relative;
      z-index: 1;
    }

    .sidebar {
      width: 280px;
      background: rgba(5, 5, 8, 0.95);
      border-right: 1px solid var(--glass-border);
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease;
      z-index: 1000;
      backdrop-filter: blur(20px);
    }

    .sidebar-header {
      padding: 1.5rem;
      border-bottom: 1px solid var(--glass-border);
      text-align: center;
    }

    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      background: transparent;
      overflow: hidden;
      position: relative;
    }

    .top-header {
      height: 70px;
      background: rgba(5, 5, 8, 0.8);
      border-bottom: 1px solid var(--glass-border);
      display: flex;
      align-items: center;
      padding: 0 2rem;
      justify-content: space-between;
      backdrop-filter: blur(10px);
    }

    .content-area {
      flex: 1;
      overflow-y: auto;
      padding: 2rem;
      position: relative;
    }

    /* Modern Scrollbar */
    .content-area::-webkit-scrollbar {
      width: 8px;
    }

    .content-area::-webkit-scrollbar-track {
      background: rgba(0, 0, 0, 0.1);
    }

    .content-area::-webkit-scrollbar-thumb {
      background: var(--primary-dark);
      border-radius: 4px;
    }

    .nav-link {
      color: rgba(255, 255, 255, 0.6) !important;
      padding: 1rem 1.5rem;
      border-radius: 0;
      border-left: 3px solid transparent;
      transition: all 0.3s;
      font-family: 'Montserrat', sans-serif;
      font-size: 1.1rem;
      letter-spacing: 1px;
    }

    .nav-link:hover,
    .nav-link.active {
      background: linear-gradient(90deg, rgba(0, 242, 234, 0.1) 0%, transparent 100%);
      color: var(--primary-color) !important;
      border-left-color: var(--primary-color);
      box-shadow: none;
      /* Override modern.css global nav shadow */
    }

    .nav-link::after {
      display: none;
    }

    /* Remove bottom line from global css */

    .nav-link i {
      margin-right: 12px;
      width: 20px;
      text-align: center;
    }

    /* Mobile Sidebar */
    @media (max-width: 768px) {
      .sidebar {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 900;
        display: none;
      }

      .overlay.show {
        display: block;
      }
    }

    /* Content wrapper for glass cards in sub-pages */
    .admin-card {
      background: rgba(20, 20, 25, 0.7);
      backdrop-filter: blur(15px);
      border: 1px solid var(--glass-border);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      padding: 20px;
      margin-bottom: 20px;
      color: #fff;
    }

    label {
      color: var(--text-muted);
      margin-bottom: 5px;
    }

    .form-control,
    .form-select {
      background: rgba(0, 0, 0, 0.3);
      border: 1px solid var(--glass-border);
      color: #fff;
    }

    .form-control:focus,
    .form-select:focus {
      background: rgba(0, 0, 0, 0.5);
      border-color: var(--primary-color);
      color: #fff;
      box-shadow: 0 0 10px rgba(0, 242, 234, 0.2);
    }

    table {
      color: #ddd !important;
    }
  </style>
</head>

<body>
  <!-- Global Backgrounds -->
  <div class="cyber-bg"></div>
  <div class="grid-overlay"></div>

  <div class="overlay" id="sidebarOverlay"></div>

  <div class="app-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="appSidebar">
      <div class="sidebar-header">
        <h3 class="m-0" style="color: #fff; font-family: 'Montserrat', sans-serif; letter-spacing: 2px;">
          ITU CTF'26 <span style="color: var(--primary-color); font-size: 0.8em;">ADMIN</span>
        </h3>
      </div>

      <nav class="flex-grow-1 py-3" style="overflow-y: auto;">
        <div class="nav flex-column">
          <a href="#" class="nav-link menu-link active" data-page="dashboard">
            <i class="bi bi-speedometer2"></i> Dashboard
          </a>

          <a href="#" class="nav-link menu-link" data-page="security">
            <i class="bi bi-shield-check"></i> Güvenlik
          </a>
          <div class="my-2 border-bottom border-secondary opacity-25"></div>

          <a href="#" class="nav-link menu-link" data-page="speakers">
            <i class="bi bi-person-video"></i> Konuşmacılar
          </a>
          <a href="#" class="nav-link menu-link" data-page="sponsors">
            <i class="bi bi-building"></i> Sponsorlar
          </a>

          <div class="my-2 border-bottom border-secondary opacity-25"></div>
          <div class="px-3 py-2 text-uppercase small text-muted">İçerik Yönetimi</div>

          <a href="#" class="nav-link menu-link" data-page="hero">
            <i class="bi bi-image"></i> Hero (Giriş)
          </a>
          <a href="#" class="nav-link menu-link" data-page="about">
            <i class="bi bi-info-circle"></i> Hakkımızda
          </a>

          <div class="my-2 border-bottom border-secondary opacity-25"></div>
          <div class="px-3 py-2 text-uppercase small text-muted">Ayarlar</div>

          <a href="#" class="nav-link menu-link" data-page="apply">
            <i class="bi bi-link"></i> Link & Butonlar
          </a>
          <a href="#" class="nav-link menu-link" data-page="speakers_section">
            <i class="bi bi-gear"></i> Konuşmacı Başlıkları
          </a>
          <a href="#" class="nav-link menu-link" data-page="sponsors_section">
            <i class="bi bi-gear"></i> Sponsor Başlıkları
          </a>
        </div>
      </nav>

      <div class="p-3 border-top border-secondary border-opacity-25">
        <form method="POST" action="logout.php" class="w-100">
          <?php echo getCsrfTokenField(); ?>
          <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">
            <i class="bi bi-box-arrow-right"></i> Çıkış Yap
          </button>
        </form>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Header -->
      <header class="top-header">
        <button class="btn btn-link text-white d-md-none" id="sidebarToggle">
          <i class="bi bi-list fs-4"></i>
        </button>

        <h4 class="m-0 d-none d-md-block" id="pageTitle"
          style="font-family: 'Montserrat', sans-serif; color: var(--primary-color);">
          DASHBOARD</h4>

        <div class="d-flex align-items-center">
          <div class="text-end me-3 d-none d-sm-block">
            <small class="d-block text-muted" style="font-size: 0.75rem;">GİRİŞ YAPAN</small>
            <span class="text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
          </div>
          <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
            style="width: 40px; height: 40px;">
            <i class="bi bi-person-fill text-white"></i>
          </div>
        </div>
      </header>

      <!-- Dynamic Content Area -->
      <div class="content-area" id="content-area">
        <!-- Welcome Message -->
        <div class="row align-items-center h-100 justify-content-center">
          <div class="col-md-6 text-center">
            <div class="p-5"
              style="border: 1px dashed var(--glass-border); border-radius: 20px; background: rgba(0,0,0,0.2);">
              <i class="bi bi-grid-1x2 text-primary"
                style="font-size: 5rem; text-shadow: 0 0 20px var(--primary-color);"></i>
              <h2 class="mt-4 text-white">Hoşgeldiniz</h2>
              <p class="text-muted">Lütfen sol menüden yönetmek istediğiniz alanı seçin.</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Sidebar Toggle for Mobile
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    function toggleSidebar() {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
    }

    toggleBtn?.addEventListener('click', toggleSidebar);
    overlay?.addEventListener('click', toggleSidebar);

    // Content Loading Logic
    function loadContent(page) {
      // Update Title
      const pageTitles = {
        'speakers': 'Konuşmacı Yönetimi',
        'security': 'Güvenlik ve Erişim Kontrolü',
        'sponsors': 'Sponsor Yönetimi',
        'sponsors': 'Sponsor Yönetimi',
        'about': 'Hakkımızda İçeriği',
        'hero': 'Hero (Giriş) Alanı',
        'apply': 'Buton Link Ayarları',
        'speakers_section': 'Konuşmacılar Bölüm Ayarları',
        'sponsors_section': 'Sponsorlar Bölüm Ayarları',
        'dashboard': 'Dashboard'
      };

      document.getElementById('pageTitle').innerText = pageTitles[page] || 'YÖNETİM PANELİ';

      // Handle active state
      document.querySelectorAll('.menu-link').forEach(el => el.classList.remove('active'));
      const activeBtn = document.querySelector(`.menu-link[data-page="${page}"]`);
      if (activeBtn) activeBtn.classList.add('active');

      if (page === 'dashboard') {
        $('#content-area').html(`
             <div class="row align-items-center h-100 justify-content-center">
                 <div class="col-md-6 text-center">
                     <div class="p-5 fade-in" style="border: 1px dashed var(--glass-border); border-radius: 20px; background: rgba(0,0,0,0.2);">
                         <i class="bi bi-grid-1x2 text-primary" style="font-size: 5rem; text-shadow: 0 0 20px var(--primary-color);"></i>
                         <h2 class="mt-4 text-white">Hoşgeldiniz</h2>
                         <p class="text-muted">Lütfen sol menüden yönetmek istediğiniz alanı seçin.</p>
                     </div>
                 </div>
             </div>
          `);
        if (window.innerWidth < 768) toggleSidebar();
        return;
      }

      let url = page + '.php';

      // Loading State
      $('#content-area').html('<div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div></div>');

      if (window.innerWidth < 768) toggleSidebar();

      setTimeout(function () {
        $.ajax({
          url: url,
          method: 'GET',
          success: function (data) {
            // Wrap content in styling container if needed or just inject
            // We assume the php files return just forms/tables. We will wrap them slightly nicely using JS if they don't have container
            $('#content-area').html('<div class="fade-in">' + data + '</div>');

            // Re-bind scripts if any included in the loaded content
            // Note: inline scripts in loaded content execute automatically by jQuery .html()
          },
          error: function () {
            $('#content-area').html('<div class="alert alert-danger">İçerik yüklenemedi.</div>');
          }
        });
      }, 300);
    }

    // Event Listeners for Menu
    document.querySelectorAll('.menu-link').forEach(link => {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        const page = this.getAttribute('data-page');
        loadContent(page);
      });
    });

    // Add simple fade animation
    const styleSheet = document.createElement("style");
    styleSheet.innerText = `
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.4s ease-out forwards; }
    `;
    document.head.appendChild(styleSheet);
  </script>
</body>

</html>