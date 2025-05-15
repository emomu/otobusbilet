<?php
// index.php - Ana sayfa
require_once "config.php";

// Şehirleri çek
$sql = "SELECT * FROM cities ORDER BY name";
$cities = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Otobüs Bilet Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <!-- Ana Slider -->
    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/slider/slider1.jpg" class="d-block w-100" alt="Otobüs seyahat">
                <div class="carousel-caption">
                    <h1>Güvenli ve Konforlu Yolculuk</h1>
                    <p>En iyi otobüs firmaları ile seyahat edin</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/slider/slider2.jpg" class="d-block w-100" alt="Otobüs seyahat">
                <div class="carousel-caption">
                    <h1>Uygun Fiyatlı Biletler</h1>
                    <p>Ekonomik fiyatlarla seyahat etmenin keyfini çıkarın</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/slider/slider3.jpg" class="d-block w-100" alt="Otobüs seyahat">
                <div class="carousel-caption">
                    <h1>7/24 Canlı Destek</h1>
                    <p>Bilet değişikliği ve iptali için 7/24 destek alın</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
    
    <!-- Bilet Arama Formu -->
    <div class="container search-container">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="card-title mb-4">Bilet Ara</h4>
                <form id="searchForm" action="search_trips.php" method="get">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Nereden</label>
                            <select class="form-select" name="from" required>
                                <option value="">Şehir Seçin</option>
                                <?php while($city = mysqli_fetch_assoc($cities)): ?>
                                    <option value="<?php echo $city['id']; ?>"><?php echo $city['name']; ?></option>
                                <?php endwhile; ?>
                                <?php mysqli_data_seek($cities, 0); // Tekrar başa dön ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nereye</label>
                            <select class="form-select" name="to" required>
                                <option value="">Şehir Seçin</option>
                                <?php while($city = mysqli_fetch_assoc($cities)): ?>
                                    <option value="<?php echo $city['id']; ?>"><?php echo $city['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tarih</label>
                            <input type="date" class="form-control" name="date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-12">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Bilet Ara</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Özellikler -->
    <div class="container mt-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <i class="fas fa-bus fa-4x mb-3 text-primary"></i>
                    <h3>Geniş Firma Ağı</h3>
                    <p>Türkiye'nin en büyük otobüs firmalarıyla bilet alın.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <i class="fas fa-money-bill-wave fa-4x mb-3 text-primary"></i>
                    <h3>Uygun Fiyatlar</h3>
                    <p>En uygun fiyatlarla biletinizi alın, tasarruf edin.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-box">
                    <i class="fas fa-lock fa-4x mb-3 text-primary"></i>
                    <h3>Güvenli Ödeme</h3>
                    <p>SSL sertifikalı güvenli ödeme altyapısı.</p>
                </div>
            </div>
        </div>
    </div>

    
    <?php include "footer.php"; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>