<?php
// companies.php - Firmalar sayfası
require_once "config.php";

// Firmaları veritabanından çek
$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM buses WHERE company_id = c.id) as bus_count,
        (SELECT COUNT(*) FROM trips t JOIN buses b ON t.bus_id = b.id WHERE b.company_id = c.id) as trip_count
        FROM companies c 
        ORDER BY c.name";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Firmalar - Otobüs Bilet Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .company-card {
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 30px;
        }
        .company-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        /* Logo stilini değiştirme */
        .logo-container {
            height: 220px; /* Sabit yükseklik - logolar için yeterli alan */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .company-logo {
            max-width: 90%; /* Genişliği arttırma */
            max-height: 180px; /* Yüksekliği arttırma */
            object-fit: contain; /* Oranı koru */
            margin: 0 auto; /* Merkezde tut */
            display: block;
        }
        
        .company-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-top: 1px solid #eee;
        }
        .company-feature {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .company-feature i {
            margin-right: 8px;
            color: #007bff;
            width: 20px;
            text-align: center;
        }
        .company-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .stat-item {
            text-align: center;
            flex: 1;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 14px;
            color: #6c757d;
        }
        .page-header {
            background-color: #f5f5f5;
            padding: 40px 0;
            margin-bottom: 40px;
        }
        
        /* Firma açıklaması için stil */
        .card-body {
            height: 180px; /* Sabit yükseklik - açıklamaların düzenli görünmesi için */
            overflow: hidden; /* Uzun açıklamaları kes */
        }
        
        .card-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        
        .card-text {
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="page-header">
        <div class="container">
            <h1>Anlaşmalı Firmalarımız</h1>
            <p class="lead">Güvenli ve konforlu yolculuk için hizmet veren partner firmalarımız</p>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($company = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card company-card">
                            <div class="logo-container">
                                <img src="images/companies/<?php echo $company['logo']; ?>" class="company-logo" alt="<?php echo $company['name']; ?> Logo">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $company['name']; ?></h5>
                                <p class="card-text">
                                    <?php 
                                    // Firma için örnek açıklama
                                    $descriptions = [
                                        'Metro Turizm' => 'Türkiye\'nin en geniş otobüs ağına sahip firmalarından biri olan Metro Turizm, konforlu ve güvenli yolculuk imkanı sunmaktadır.',
                                        'Kamil Koç' => 'Yüksek standartlarda hizmet kalitesi ve profesyonel ekibiyle Kamil Koç, yolcularına güvenilir seyahat deneyimi yaşatmaktadır.',
                                        'Pamukkale Turizm' => 'Modern otobüs filosu ve alanında uzman personeliyle Pamukkale Turizm, kaliteli hizmeti uygun fiyata sunmaktadır.',
                                        'Nilüfer Turizm' => 'Müşteri memnuniyetini ön planda tutan Nilüfer Turizm, konforlu ve ekonomik seyahat seçenekleri sunmaktadır.'
                                    ];
                                    
                                    echo isset($descriptions[$company['name']]) ? $descriptions[$company['name']] : 'Güvenli ve konforlu yolculuk için hizmetinizdeyiz.';
                                    ?>
                                </p>
                            </div>
                            <div class="company-info">
                                <div class="company-feature">
                                    <i class="fas fa-wifi"></i>
                                    <span>Ücretsiz WiFi</span>
                                </div>
                                <div class="company-feature">
                                    <i class="fas fa-plug"></i>
                                    <span>USB Şarj</span>
                                </div>
                                <div class="company-feature">
                                    <i class="fas fa-tv"></i>
                                    <span>Kişisel Ekran</span>
                                </div>
                                <div class="company-feature">
                                    <i class="fas fa-coffee"></i>
                                    <span>İkram Servisi</span>
                                </div>
                                
                                <div class="company-stats">
                                    <div class="stat-item">
                                        <div class="stat-value"><?php echo $company['bus_count']; ?></div>
                                        <div class="stat-label">Otobüs</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value"><?php echo $company['trip_count']; ?></div>
                                        <div class="stat-label">Sefer</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value">
                                            <?php 
                                            // Rastgele yıldız puanı (4-5 arası)
                                            $rating = rand(40, 50) / 10;
                                            echo number_format($rating, 1);
                                            ?>
                                        </div>
                                        <div class="stat-label">Puan</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        Henüz kayıtlı firma bulunmamaktadır.
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Partner Firma Olmak İster misiniz?</h4>
                        <p>Otobüs Bilet Sistemi'ne firma olarak katılmak ve biletlerinizi satışa sunmak için bizimle iletişime geçebilirsiniz.</p>
                        <a href="contact.php" class="btn btn-primary">
                            <i class="fas fa-envelope"></i> İletişime Geç
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "footer.php"; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>