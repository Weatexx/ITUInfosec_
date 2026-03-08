<?php
// Admin redirection
if (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) {
    header('Location: admin/login.php');
    exit;
}

require_once 'admin/data_manager.php';
$dataManager = new DataManager();

// Fetch data from JSON
$speakers = $dataManager->getSpeakers();
$about = $dataManager->getAboutUs();
$sponsors = $dataManager->getSponsors();
$sponsorsSection = $dataManager->getSponsorsSection();
$speakersSection = $dataManager->getSpeakersSection();
$hero = $dataManager->getHero();
$apply = $dataManager->getApply();

// Default values
if (!$about) {
    $about = [
        'title' => 'Hakkımızda',
        'subtitle' => 'İstanbul Teknik Üniversitesi - ITU Infosec',
        'content' => 'Siber güvenlik alanında öncü...'
    ];
}

$applyUrl = $apply['button_url'] ?? '#';
$contactUrl = $apply['contact_url'] ?? '#';

if (!$hero) {
    $hero = [
        'top_title' => 'Geleneksel ITU Infosec CTF yarışması',
        'main_title' => 'ITU CTF' . '26',
        'description' => 'Siber güvenlik alanında bilgi paylaşımı, teknik oturumlar ve uygulamalı atölyeler sunan, İTÜ Ayazağa SDKM (Süleyman Demirel Kültür Merkezi)\'nde gerçekleşecek kapsamlı bir etkinlik.',
        'top_title_size' => 14,
        'main_title_size' => 36,
        'description_size' => 16
    ];
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITU CTF'26 | ITU Infosec</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Fonts & Icons -->
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap 5 (CDN for latest features) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Cyber Theme -->
    <link rel="stylesheet" href="root/css/modern.css">
</head>

<body>
    <!-- Ambient Backgrounds -->
    <div class="cyber-bg">
        <!-- Animated Circuit SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(6, 182, 212, 0.05)" stroke-width="1" />
                </pattern>
                <radialGradient id="grad" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" style="stop-color:rgba(2, 6, 23, 0);stop-opacity:0" />
                    <stop offset="100%" style="stop-color:rgba(2, 6, 23, 1);stop-opacity:1" />
                </radialGradient>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
            <circle cx="20%" cy="20%" r="2" fill="#06b6d4">
                <animate attributeName="opacity" values="0;1;0" dur="4s" repeatCount="indefinite" />
            </circle>
            <circle cx="50%" cy="50%" r="2" fill="#3b82f6">
                <animate attributeName="opacity" values="0;1;0" dur="5s" repeatCount="indefinite" />
            </circle>
            <circle cx="80%" cy="80%" r="2" fill="#06b6d4">
                <animate attributeName="opacity" values="0;1;0" dur="3s" repeatCount="indefinite" />
            </circle>
            <rect width="100%" height="100%" fill="url(#grad)" />
        </svg>
    </div>
    <div class="grid-overlay"></div>

    <!-- Normalize Wrapper -->
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg fixed-top navbar-glass">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="root/img/InfosecLogo.png" alt="ITU Logo" height="40" class="me-3">
                    <span style="font-weight: 800; color: #fff; font-size: 1.5rem; letter-spacing: 1px;">ITU CTF'26</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
                    <span class="fa fa-bars" style="color: #fff;"></span>
                </button>

                <div class="collapse navbar-collapse" id="navmenu">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item mx-2"><a href="#hero-section" class="nav-link">ANASAYFA</a></li>
                        <li class="nav-item mx-2"><a href="#bizkimiz" class="nav-link">HAKKIMIZDA</a></li>
                        <li class="nav-item mx-2"><a href="#konusmacilar" class="nav-link">KONUŞMACILAR</a></li>
                        <li class="nav-item mx-2"><a href="#sponsorlar" class="nav-link">SPONSORLAR</a></li>
                        <li class="nav-item mx-2">
                            <a href="<?php echo htmlspecialchars($contactUrl); ?>" target="_blank"
                                class="nav-link contact-btn">İLETİŞİM</a>
                        </li>
                        <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                            <button onclick="window.open('<?php echo htmlspecialchars($applyUrl); ?>', '_blank')"
                                class="btn-glow">BAŞVURU YAP</button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="hero-section" class="d-flex align-items-center"
            style="min-height: 100vh; position: relative; overflow: hidden; padding: 120px 0;">
            <div class="container hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <p class="hero-subtitle mb-3" style="font-size: <?php echo $hero['top_title_size']; ?>px;">
                            <?php echo htmlspecialchars($hero['top_title']); ?>
                        </p>
                        <h1 class="glitch-title mb-4"
                            style="font-size: <?php echo $hero['main_title_size']; ?>px; line-height: 1.1;">
                            <?php echo $hero['main_title']; ?>
                        </h1>
                        <p class="hero-desc mb-5" style="font-size: <?php echo $hero['description_size']; ?>px;">
                            <?php echo $hero['description']; ?>
                        </p>
                        <button onclick="window.open('<?php echo htmlspecialchars($applyUrl); ?>', '_blank')"
                            class="btn-cyber">HEMEN BAŞVUR</button>
                    </div>
                    <!-- Hero Image / Visual -->
                    <div class="col-lg-6 text-center d-none d-lg-block hero-bg-img">
                        <img src="root/img/heroimage.png" class="img-fluid"
                            style="filter: drop-shadow(0 0 30px rgba(0, 242, 234, 0.2)); max-height: 500px;"
                            alt="Cyber Hero">
                    </div>
                </div>
            </div>
        </section>

        <!-- Sponsor Ticker (Scrolling) -->
        <?php if (!empty($sponsors)) { ?>
            <div class="sponsor-ticker-wrapper">
                <div class="sponsor-ticker-track">
                    <?php
                    // Duplicate sponsors list multiple times to ensure smooth infinite scroll
                    $tickerSponsors = array_merge($sponsors, $sponsors, $sponsors, $sponsors);
                    foreach ($tickerSponsors as $ts) {
                        $tsPhoto = $ts['photo'] ?? '';
                        if (!empty($tsPhoto) && strpos($tsPhoto, 'uploads/') === 0) {
                            $tsPhoto = 'admin/' . $tsPhoto;
                        }
                        ?>
                        <div class="ticker-item" onclick="openSponsorModalById('<?php echo $ts['id']; ?>')">
                            <?php if (!empty($tsPhoto) && (file_exists($tsPhoto) || strpos($tsPhoto, 'admin/') === 0)) { ?>
                                <img src="<?php echo htmlspecialchars($tsPhoto); ?>"
                                    alt="<?php echo htmlspecialchars($ts['title']); ?>">
                            <?php } else { ?>
                                <i class="fas fa-building fa-2x" style="color: var(--primary-color); margin-right: 10px;"></i>
                            <?php } ?>
                            <span><?php echo htmlspecialchars($ts['title']); ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <!-- About Section -->
        <section id="bizkimiz" style="padding: 100px 0; position: relative;">
            <div class="container">
                <div class="text-center mb-5 pb-3">
                    <h2 class="section-title"><?php echo htmlspecialchars($about['title'] ?? 'HAKKIMIZDA'); ?></h2>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="glass-panel p-5 rounded-4 text-center">
                            <?php if (isset($about['subtitle']) && $about['subtitle'] !== '') { ?>
                                <h3 class="mb-4" style="color: var(--primary-color); font-weight: 600;">
                                    <?php echo htmlspecialchars($about['subtitle']); ?>
                                </h3>
                            <?php } ?>

                            <p class="lead" style="color: rgba(255,255,255,0.8); line-height: 1.8;">
                                <?php echo nl2br(htmlspecialchars($about['content'] ?? '')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Speakers Section -->
        <section id="konusmacilar" style="padding: 100px 0;">
            <div class="container">
                <div class="text-center mb-5 pb-3">
                    <h2 class="section-title">
                        <?php echo htmlspecialchars($speakersSection['title'] ?? 'KONUŞMACILAR'); ?>
                    </h2>
                    <?php if (!empty($speakersSection['description'])) { ?>
                        <p class="text-muted mt-3"><?php echo nl2br(htmlspecialchars($speakersSection['description'])); ?>
                        </p>
                    <?php } ?>
                </div>

                <div class="row g-5 justify-content-center">
                    <?php if (!empty($speakers)) {
                        foreach ($speakers as $row) {
                            $photoPath = $row['photo'] ?? 'root/img/default-user.png';
                            if (!empty($photoPath) && strpos($photoPath, 'uploads/') === 0) {
                                $photoPath = 'admin/' . $photoPath;
                            }
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="cyber-card d-flex flex-column align-items-center text-center h-100">
                                    <div class="card-img-wrapper mb-4">
                                        <?php if (file_exists($photoPath) || strpos($photoPath, 'admin/') === 0) { ?>
                                            <img src="<?php echo htmlspecialchars($photoPath); ?>"
                                                alt="<?php echo htmlspecialchars($row['name']); ?>">
                                        <?php } else { ?>
                                            <div
                                                style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #222; border-radius: 50%;">
                                                <i class="fas fa-user mb-0" style="font-size: 2rem; color: #555;"></i>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <h4 class="card-name mb-2"><?php echo htmlspecialchars($row['name']); ?></h4>
                                    <p class="card-role mb-4"><?php echo htmlspecialchars($row['expertise'] ?? ''); ?></p>

                                    <div class="mt-auto w-100 pt-3">
                                        <button
                                            onclick="window.open('<?php echo htmlspecialchars($row['profile_url'] ?? '#'); ?>', '_blank')"
                                            class="btn btn-sm btn-outline-light w-100 rounded-pill"
                                            style="border-color: rgba(255,255,255,0.2);">Profili Görüntüle</button>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="col-12 text-center text-muted">Henüz konuşmacı eklenmemiş.</div>
                    <?php } ?>
                </div>
            </div>
        </section>

        <!-- Sponsors Section -->
        <section id="sponsorlar" style="padding: 100px 0;">
            <div class="container">
                <div class="text-center mb-5 pb-3">
                    <h2 class="section-title"><?php echo htmlspecialchars($sponsorsSection['title'] ?? 'SPONSORLAR'); ?>
                    </h2>
                    <p class="text-muted mt-3">
                        <?php echo nl2br(htmlspecialchars($sponsorsSection['description'] ?? '')); ?>
                    </p>
                </div>

                <div class="row g-4 justify-content-center">
                    <?php if (!empty($sponsors)) {
                        foreach ($sponsors as $row) {
                            $photoPath = $row['photo'] ?? '';
                            if (!empty($photoPath) && strpos($photoPath, 'uploads/') === 0) {
                                $photoPath = 'admin/' . $photoPath;
                            }
                            ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="cyber-card sponsor-card d-flex flex-column h-100" style="cursor: pointer;"
                                    onclick="openSponsorModalById('<?php echo htmlspecialchars($row['id']); ?>')">

                                    <div class="d-flex align-items-center mb-4">
                                        <div class="card-img-wrapper measure-img me-3 mb-0"
                                            style="width: 60px; height: 60px; flex-shrink: 0;">
                                            <?php if (!empty($photoPath)) { ?>
                                                <img src="<?php echo htmlspecialchars($photoPath); ?>"
                                                    alt="<?php echo htmlspecialchars($row['title']); ?>">
                                            <?php } else { ?>
                                                <div
                                                    style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #222; border-radius: 50%;">
                                                    <i class="fas fa-building" style="font-size: 1.5rem; color: #555;"></i>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <h4 class="card-name mb-0" style="font-size: 1.2rem;">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </h4>
                                    </div>

                                    <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                                        <?php
                                        $desc = htmlspecialchars($row['description'] ?? '');
                                        echo (strlen($desc) > 80) ? substr($desc, 0, 80) . '...' : $desc;
                                        ?>
                                    </p>

                                    <div class="mt-auto pt-2">
                                        <span class="badge"
                                            style="background: rgba(0, 242, 234, 0.1); color: var(--primary-color); border: 1px solid var(--primary-color); padding: 8px 12px;">
                                            <?php echo htmlspecialchars($row['expertise'] ?? 'Sponsor'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="col-12 text-center text-muted">Henüz sponsor eklenmemiş.</div>
                    <?php } ?>
                </div>
            </div>
        </section>

    </div>

    <!-- Sponsor Modal -->
    <div class="modal fade" id="sponsorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content"
                style="background: rgba(10, 10, 15, 0.95); backdrop-filter: blur(20px); border: 1px solid var(--primary-dark); border-radius: 20px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="modalSponsorTitle"
                        style="color: var(--primary-color); font-weight: 700;">Sponsor Detayı</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-4 mb-md-0 text-center">
                            <div
                                style="border-radius: 15px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                                <img id="modalSponsorImage" src="" class="img-fluid"
                                    style="display: none; width: 100%; height: auto; object-fit: cover;">
                                <div id="modalSponsorImagePlaceholder"
                                    style="background-color: #111; height: 200px; display: none; align-items: center; justify-content: center;">
                                    <i class="fas fa-building" style="font-size: 50px; color: #333;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 ps-md-4">
                            <h3 id="modalSponsorName" class="text-white mb-3" style="font-weight: 700;"></h3>
                            <div class="mb-4">
                                <span class="badge" id="modalSponsorExpertise"
                                    style="background: var(--primary-color); color: #000; padding: 0.5em 1em; font-size: 0.9rem;"></span>
                            </div>
                            <div class="modal-description"
                                style="max-height: 250px; overflow-y: auto; color: #ddd; line-height: 1.8; padding-right: 10px;">
                                <p id="modalSponsorDesc"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-cyber px-5" data-bs-dismiss="modal">KAPAT</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comprehensive Footer -->
    <footer class="cyber-footer">
        <div class="container">
            <div class="row g-5">
                <!-- Brand Column -->
                <div class="col-lg-4 text-center text-lg-start">
                    <a href="#" class="footer-brand">
                        CYBER<span style="color: var(--primary-color);">CON</span>
                    </a>
                    <p class="text-muted mb-4">
                        Geleceğin siber güvenlik liderlerinin buluşma noktası.
                        Etik hacker kültürünü yaygınlaştırmak ve sektöre yön vermek için buradayız.
                    </p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                        <a href="https://chat.whatsapp.com/JIcCJaUQjleEyVqrUfqblH" target="_blank"
                            class="btn btn-outline-light btn-sm rounded-circle"
                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i
                                class="fab fa-whatsapp" style="font-size: 1.2rem;"></i></a>
                        <a href="https://www.linkedin.com/company/itu-infosec/" target="_blank"
                            class="btn btn-outline-light btn-sm rounded-circle"
                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i
                                class="fab fa-linkedin-in" style="font-size: 1.2rem;"></i></a>
                        <a href="https://www.instagram.com/ituinfosec/" target="_blank"
                            class="btn btn-outline-light btn-sm rounded-circle"
                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i
                                class="fab fa-instagram" style="font-size: 1.2rem;"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 text-center text-lg-start">
                    <h5 class="footer-heading">Hızlı Erişim</h5>
                    <a href="#hero-section" class="footer-link">Anasayfa</a>
                    <a href="#konusmacilar" class="footer-link">Konuşmacılar</a>
                    <a href="#sponsorlar" class="footer-link">Sponsorlar</a>
                    <a href="#" class="footer-link">SSS</a>
                </div>

                <!-- Contact -->
                <div class="col-lg-3 text-center text-lg-start">
                    <h5 class="footer-heading">İletişim</h5>
                    <p class="text-muted mb-2"><i class="fas fa-map-marker-alt me-2"
                            style="color: var(--primary-color);"></i>Bilişim Enstitüsü, İTÜ Ayazağa Kampüsü, İstanbul
                    </p>
                    <p class="text-muted mb-2"><i class="fas fa-envelope me-2" style="color: var(--primary-color);"></i>
                        infoseckulubu@itu.edu.tr
                    </p>
                </div>

                <!-- Newsletter -->
                <div class="col-lg-3 text-center text-lg-start">
                    <h5 class="footer-heading">Duyurular</h5>
                    <p class="text-muted small mb-3">En son duyurulardan haberdar olmak için sosyal medya hesaplarımızı
                        takip edin.</p>
                    <a href="https://linktr.ee/ituinfosec" class="btn btn-primary d-block w-100"
                        style="background: var(--primary-color); border: none; font-weight: 700;">GÖZ AT</a>
                </div>
            </div>

            <hr style="background: rgba(255,255,255,0.1); margin: 40px 0;">

            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="small text-muted mb-0">&copy; 2025 ITU CTF'26. Tüm hakları saklıdır.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="small text-muted mb-0">Designed & Developed by <a href="#"
                            style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Arda Koray
                            Kartal</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="AdminLTE/plugins/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global data
        window.sponsorsData = <?php echo json_encode($sponsors, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        function openSponsorModalById(id) {
            var sponsor = window.sponsorsData.find(s => s.id == id);
            if (sponsor) {
                openSponsorModal(sponsor);
            }
        }

        function openSponsorModal(sponsor) {
            document.getElementById('modalSponsorTitle').innerText = sponsor.title;
            document.getElementById('modalSponsorName').innerText = sponsor.title;
            // Handle newlines in description text safely
            // Using textContent for safety then replacing specific characters if needed, 
            // but for simplicity innerText works for basic text. For <br> support we need safe HTML.
            // Let's use innerText and standard DOM manipulation to avoid XSS.
            document.getElementById('modalSponsorDesc').innerText = sponsor.description;

            document.getElementById('modalSponsorExpertise').innerText = sponsor.expertise || 'Uzmanlık Belirtilmedi';

            const imgEl = document.getElementById('modalSponsorImage');
            const placeholderEl = document.getElementById('modalSponsorImagePlaceholder');

            let photoPath = sponsor.photo;
            if (photoPath && photoPath.indexOf('uploads/') === 0) {
                photoPath = 'admin/' + photoPath;
            }

            if (photoPath) {
                imgEl.src = photoPath;
                imgEl.style.display = 'block';
                placeholderEl.style.display = 'none';
            } else {
                imgEl.style.display = 'none';
                placeholderEl.style.display = 'flex';
            }

            var myModal = new bootstrap.Modal(document.getElementById('sponsorModal'));
            myModal.show();
        }
    </script>
</body>

</html>