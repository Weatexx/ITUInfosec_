<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erişim Reddedildi | ITU CTF'26</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Cyber Theme -->
    <link rel="stylesheet" href="root/css/modern.css">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            text-align: center;
        }

        .error-card {
            background: rgba(255, 0, 85, 0.1);
            border: 1px solid rgba(255, 0, 85, 0.3);
            border-radius: 15px;
            padding: 3rem;
            max-width: 500px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 50px rgba(255, 0, 85, 0.2);
        }

        .icon-large {
            font-size: 5rem;
            color: #ff0055;
            margin-bottom: 1.5rem;
            text-shadow: 0 0 20px rgba(255, 0, 85, 0.5);
        }

        h1 {
            color: #fff;
            margin-bottom: 1rem;
            font-family: 'Montserrat', sans-serif;
        }

        p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
    <div class="cyber-bg"></div>
    <div class="grid-overlay"></div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 d-flex justify-content-center">
                <div class="error-card">
                    <div class="icon-large">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <h1>ERİŞİM REDDEDİLDİ</h1>
                    <p>Çok fazla hatalı giriş denemesi yapıldı.<br>IP adresiniz güvenlik nedeniyle geçici olarak
                        engellendi.</p>
                    <a href="index.php" class="btn btn-outline-light rounded-pill px-4">Anasayfaya Dön</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>