<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ram May Cry - Horse Marketplace</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #ffffff;
            color: #172033;
            overflow-x: hidden;
        }

        .navbar {
            height: 74px;
            padding: 0 8%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            box-shadow: 0 3px 18px rgba(0, 0, 0, 0.04);
            position: relative;
            z-index: 10;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #0b5364;
            font-size: 24px;
            font-weight: 800;
        }

        .logo-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #0b5364;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-circle img {
            width: 45px;
            height: 45px;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 48px;
        }

        .nav-menu a,
        .login-link {
            text-decoration: none;
            color: #172033;
            font-size: 15px;
            font-weight: 500;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 26px;
        }

        .register-btn {
            text-decoration: none;
            background: #0b5364;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            box-shadow: 0 8px 16px rgba(11, 83, 100, 0.22);
        }

        .hero {
            min-height: calc(100vh - 74px);
            position: relative;
            overflow: hidden;
            background: #ffffff;
            padding: 0 8%;
            display: flex;
            align-items: center;
        }

        .hero-content {
            width: 44%;
            position: relative;
            z-index: 3;
            margin-top: -40px;
        }

        .hero-content h1 {
            font-size: 46px;
            line-height: 1.18;
            font-weight: 800;
            letter-spacing: -1px;
            color: #1d2638;
            margin-bottom: 24px;
        }

        .hero-content p {
            max-width: 540px;
            font-size: 16px;
            line-height: 1.9;
            color: #66708a;
            margin-bottom: 34px;
        }

        .hero-buttons {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .primary-btn {
            text-decoration: none;
            background: #0b5364;
            color: #ffffff;
            padding: 15px 28px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            box-shadow: 0 14px 26px rgba(11, 83, 100, 0.22);
        }

        .secondary-btn {
            text-decoration: none;
            background: #ffffff;
            color: #0b5364;
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            border: 1.5px solid #0b5364;
        }

        .hero-visual {
            width: 20%;
            height: 100%;
            position: absolute;
            right: 5%;
            top: 0;
            z-index: 2;
        }

        .circle-bg {
            width: 560px;
            height: 390px;
            background: #147789;
            border-radius: 55% 45% 48% 52%;
            position: absolute;
            right: 70px;
            top: 155px;
            z-index: 1;
        }

        .horse-img {
            position: absolute;
            right: 115px;
            top: 105px;
            width: 590px;
            max-width: 95%;
            z-index: 2;
            filter: drop-shadow(0 24px 34px rgba(0, 0, 0, 0.22));
        }

        .wave {
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 100%;
            z-index: 1;
        }

        .wave svg {
            width: 100%;
            height: 135px;
            display: block;
        }

        @media (max-width: 992px) {
            .nav-menu {
                display: none;
            }

            .hero {
                padding: 60px 7% 170px;
                display: block;
                min-height: auto;
            }

            .hero-content {
                width: 100%;
                margin-top: 0;
                text-align: center;
            }

            .hero-content h1 {
                font-size: 36px;
            }

            .hero-content p {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                justify-content: center;
            }

            .hero-visual {
                position: relative;
                width: 100%;
                height: 360px;
                right: auto;
                margin-top: 30px;
            }

            .circle-bg {
                width: 360px;
                height: 250px;
                left: 50%;
                right: auto;
                top: 55px;
                transform: translateX(-50%);
            }

            .horse-img {
                width: 390px;
                left: 50%;
                right: auto;
                top: 20px;
                transform: translateX(-50%);
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('landing') }}" class="logo">
            <div class="logo-circle">
                <img src="{{ asset('material/img/sendiri/logo_app_white.png') }}" alt="Logo">
            </div>
            OreNoAiba
        </a>

        <div class="nav-menu">
            <a href="#beranda">Beranda</a>
            <a href="#keunggulan">Keunggulan</a>
            <a href="#fitur">Fitur</a>
            <a href="#tentang">Tentang</a>
        </div>

        <div class="nav-actions">
            <a href="{{ route('login') }}" class="login-link">Masuk</a>
            <a href="{{ route('register') }}" class="register-btn">Buat Akun</a>
        </div>
    </nav>

    <section class="hero" id="beranda">
        <div class="hero-content">
            <h1>
                Nikmati Kemudahan<br>
                Membeli Kuda<br>
                Berkualitas!
            </h1>

            <p>
                Temukan kuda terbaik dari peternakan terpercaya. Proses
                pembelian, lisensi, transaksi, dan kawin silang dapat dilakukan
                lebih mudah melalui Ram May Cry.
            </p>

            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="primary-btn">
                    Mulai Sekarang
                </a>

                <a href="{{ route('login') }}" class="secondary-btn">
                    Masuk Akun
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="circle-bg"></div>

            <img
                src="{{ asset('material/img/sendiri/horserun.png') }}"
                alt="horserun"
                class="horse-img">
        </div>

        <div class="wave">
            <svg viewBox="0 0 1440 260" preserveAspectRatio="none">
                <path
                    fill="#0b5364"
                    d="M0,170 C220,145 350,160 520,160 C720,160 850,160 1030,140 C1220,120 1340,95 1440,85 L1440,260 L0,260 Z">
                </path>
            </svg>
        </div>
    </section>

</body>
</html>
