<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tüm sayfalarda ortak stil ve fontlar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <div class="d-flex align-items-center">
                    <div class="logo-icon bg-white rounded-circle p-2 me-2">
                        <i class="fas fa-bus text-primary"></i>
                    </div>
                    <span class="font-weight-bold">Otobüs Bilet</span>
                </div>
            </a>
            
            <!-- Mobile Menu Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Main Navigation -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <!-- Menu Items -->
                <ul class="navbar-nav nav-tabs-custom">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' || basename($_SERVER['PHP_SELF']) == '') ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home me-1"></i> Ana Sayfa    
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'companies.php' ? 'active' : ''; ?>" href="companies.php">
                            <i class="fas fa-building me-1"></i> Firmalar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="contact.php">
                            <i class="fas fa-envelope me-1"></i> İletişim
                        </a>
                    </li>
                </ul>
                
                <!-- Auth Buttons -->
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <!-- User Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION["username"]); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user text-primary me-2"></i> Profilim</a></li>
                                <li><a class="dropdown-item" href="my_tickets.php"><i class="fas fa-ticket-alt text-primary me-2"></i> Biletlerim</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt text-danger me-2"></i> Çıkış Yap</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Login & Register -->
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-light" href="login.php">
                                <i class="fas fa-sign-in-alt"></i> Giriş Yap
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light text-primary" href="register.php">
                                <i class="fas fa-user-plus"></i> Üye Ol
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<style>
    /* Header Styles */
    .navbar {
        padding: 10px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .logo-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Özel sekmeler */
    .nav-tabs-custom {
        margin-left: 20px;
        border-bottom: none;
    }
    
    .nav-tabs-custom .nav-link {
        border: none;
        color: rgba(255, 255, 255, 0.8);
        padding: 10px 20px;
        position: relative;
        transition: all 0.3s;
        margin: 0 5px;
        border-radius: 0;
    }
    
    .nav-tabs-custom .nav-link:hover {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .nav-tabs-custom .nav-link.active {
        color: #fff;
        background-color: transparent;
        font-weight: bold;
    }
    
    .nav-tabs-custom .nav-link.active::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
        background-color: #fff;
        border-radius: 3px 3px 0 0;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        transform: scaleX(0.8);
        animation: indicatorIn 0.3s forwards;
    }
    
    @keyframes indicatorIn {
        to {
            transform: scaleX(1);
        }
    }
    
    /* Diğer stiller */
    .navbar .btn {
        padding: 8px 16px;
    }
    .dropdown-menu {
        margin-top: 10px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-radius: 8px;
    }
    .dropdown-item {
        padding: 8px 20px;
    }
</style>