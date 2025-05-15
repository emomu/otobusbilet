<?php
// search_trips.php - Sefer arama sayfası
require_once "config.php";

// Parametreleri kontrol et
if(!isset($_GET['from']) || !isset($_GET['to']) || !isset($_GET['date'])) {
    header("location: index.php");
    exit;
}

$from_id = cleanInput($_GET['from']);
$to_id = cleanInput($_GET['to']);
$date = cleanInput($_GET['date']);

// Şehir isimlerini al
$sql = "SELECT name FROM cities WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $from_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$departure_city = mysqli_fetch_assoc($result)['name'];

mysqli_stmt_bind_param($stmt, "i", $to_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$arrival_city = mysqli_fetch_assoc($result)['name'];
mysqli_stmt_close($stmt);

// Seferleri sorgula
$sql = "SELECT t.id, t.departure_time, t.arrival_time, t.price, c.name as company_name, c.logo, 
        b.has_wifi, b.has_usb, b.has_entertainment, b.seat_count
        FROM trips t
        JOIN routes r ON t.route_id = r.id
        JOIN buses b ON t.bus_id = b.id
        JOIN companies c ON b.company_id = c.id
        WHERE r.departure_city_id = ? AND r.arrival_city_id = ? 
        AND DATE(t.departure_time) = ?
        ORDER BY t.departure_time";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iis", $from_id, $to_id, $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Otobüs Seferleri - <?php echo $departure_city; ?> - <?php echo $arrival_city; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <!-- Arama Bilgileri -->
        <div class="card mb-4 search-info-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="route-title"><?php echo $departure_city; ?> - <?php echo $arrival_city; ?></h4>
                        <p class="text-muted mb-0"><?php echo date('d.m.Y', strtotime($date)); ?></p>
                    </div>
                    <a href="index.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-search"></i> Yeni Arama
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Bulunan Seferler -->
        <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="trips-container">
                <?php while($trip = mysqli_fetch_assoc($result)): ?>
                    <div class="card mb-3 trip-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <img src="images/companies/<?php echo $trip['logo']; ?>" class="company-logo" alt="<?php echo $trip['company_name']; ?>">
                                    <div class="company-name"><?php echo $trip['company_name']; ?></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="trip-times">
                                        <div class="departure-time"><?php echo date('H:i', strtotime($trip['departure_time'])); ?></div>
                                        <div class="trip-duration-line">
                                            <span class="trip-duration">
                                                <?php 
                                                $departure = new DateTime($trip['departure_time']);
                                                $arrival = new DateTime($trip['arrival_time']);
                                                $interval = $departure->diff($arrival);
                                                echo $interval->format('%h sa %i dk');
                                                ?>
                                            </span>
                                        </div>
                                        <div class="arrival-time"><?php echo date('H:i', strtotime($trip['arrival_time'])); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="features">
                                        <?php if($trip['has_wifi']): ?>
                                            <span class="feature-icon" title="Ücretsiz WiFi">
                                                <i class="fas fa-wifi"></i>
                                            </span>
                                        <?php endif; ?>
                                        <?php if($trip['has_usb']): ?>
                                            <span class="feature-icon" title="USB Şarj">
                                                <i class="fas fa-plug"></i>
                                            </span>
                                        <?php endif; ?>
                                        <?php if($trip['has_entertainment']): ?>
                                            <span class="feature-icon" title="Eğlence Sistemi">
                                                <i class="fas fa-tv"></i>
                                            </span>
                                        <?php endif; ?>
                                        <span class="feature-icon" title="Koltuk Sayısı">
                                            <i class="fas fa-chair"></i> <?php echo $trip['seat_count']; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="price">₺<?php echo number_format($trip['price'], 2); ?></div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <a href="select_seat.php?trip_id=<?php echo $trip['id']; ?>" class="btn btn-primary btn-block">
                                        Koltuk Seç
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <h4 class="alert-heading">Sefer Bulunamadı!</h4>
                <p>Maalesef seçtiğiniz tarih ve güzergahta sefer bulunamadı. Lütfen başka bir tarih veya güzergah seçin.</p>
                <hr>
                <p class="mb-0">
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i> Yeni Arama Yap
                    </a>
                </p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include "footer.php"; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>