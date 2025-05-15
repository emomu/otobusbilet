<?php
// complete_booking.php - Rezervasyon tamamlama sayfası
require_once "config.php";

// Kullanıcı girişi kontrolü
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Form gönderilmiş mi kontrol et
if($_SERVER["REQUEST_METHOD"] != "POST"){
    header("location: index.php");
    exit;
}

// Form verilerini al
$trip_id = cleanInput($_POST['trip_id']);
$seat_id = cleanInput($_POST['seat_id']);
$passenger_name = cleanInput($_POST['passenger_name']);
$passenger_tc = cleanInput($_POST['passenger_tc']);
$passenger_gender = cleanInput($_POST['passenger_gender']);

// Sefer bilgilerini al
$sql = "SELECT t.price, t.departure_time, t.arrival_time, 
        c1.name as departure_city, c2.name as arrival_city,
        co.name as company_name, s.seat_number
        FROM trips t
        JOIN routes r ON t.route_id = r.id
        JOIN cities c1 ON r.departure_city_id = c1.id
        JOIN cities c2 ON r.arrival_city_id = c2.id
        JOIN buses b ON t.bus_id = b.id
        JOIN companies co ON b.company_id = co.id
        JOIN seats s ON s.id = ?
        WHERE t.id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $seat_id, $trip_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header("location: index.php");
    exit;
}

$trip = mysqli_fetch_assoc($result);

// PNR kodu oluştur
$pnr_code = generatePNR();

// Bilet kaydını oluştur
$sql = "INSERT INTO tickets (trip_id, user_id, seat_id, passenger_name, passenger_tc, passenger_gender, pnr_code) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iiissss", $trip_id, $_SESSION["id"], $seat_id, $passenger_name, $passenger_tc, $passenger_gender, $pnr_code);

if(mysqli_stmt_execute($stmt)){
    $ticket_id = mysqli_insert_id($conn);
} else{
    // Hata durumunda
    echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bilet Rezervasyonu Tamamlandı - Otobüs Bilet Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .ticket-card {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        .ticket-header {
            border-bottom: 2px dashed #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .ticket-qr {
            text-align: center;
            margin: 20px 0;
        }
        .ticket-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .ticket-label {
            font-weight: bold;
            color: #6c757d;
        }
        .ticket-value {
            font-weight: bold;
        }
        .print-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <div class="alert alert-success">
            <h4 class="alert-heading">Rezervasyon Başarılı!</h4>
            <p>Bilet rezervasyonunuz başarıyla tamamlandı. Bilet detaylarınız aşağıda yer almaktadır.</p>
        </div>
        
        <div class="ticket-card">
            <div class="ticket-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3><?php echo $trip['company_name']; ?></h3>
                        <h5><?php echo $trip['departure_city'] . ' - ' . $trip['arrival_city']; ?></h5>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="pnr-code">
                            <span class="ticket-label">PNR Kodu:</span>
                            <span class="ticket-value"><?php echo $pnr_code; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="ticket-info">
                        <div>
                            <span class="ticket-label">Yolcu:</span>
                            <span class="ticket-value"><?php echo $passenger_name; ?></span>
                        </div>
                        <div>
                            <span class="ticket-label">Koltuk No:</span>
                            <span class="ticket-value"><?php echo $trip['seat_number']; ?></span>
                        </div>
                    </div>
                    
                    <div class="ticket-info">
                        <div>
                            <span class="ticket-label">Tarih:</span>
                            <span class="ticket-value"><?php echo date('d.m.Y', strtotime($trip['departure_time'])); ?></span>
                        </div>
                        <div>
                            <span class="ticket-label">Saat:</span>
                            <span class="ticket-value"><?php echo date('H:i', strtotime($trip['departure_time'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="ticket-info">
                        <div>
                            <span class="ticket-label">Varış Saati:</span>
                            <span class="ticket-value"><?php echo date('H:i', strtotime($trip['arrival_time'])); ?></span>
                        </div>
                        <div>
                            <span class="ticket-label">Ücret:</span>
                            <span class="ticket-value">₺<?php echo number_format($trip['price'], 2); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="ticket-qr">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $pnr_code; ?>" alt="QR Kod">
                        <div><small>Bilet bilgileriniz için QR kodu taratın</small></div>
                    </div>
                </div>
            </div>
            
            <div class="text-center print-button">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Bileti Yazdır
                </button>
                <a href="my_tickets.php" class="btn btn-outline-primary">
                    <i class="fas fa-ticket-alt"></i> Biletlerim
                </a>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Önemli Bilgiler</h5>
                <ul>
                    <li>Lütfen yolculuk saatinden en az 30 dakika önce terminalde olunuz.</li>
                    <li>Bilet iptali için yolculuktan en az 3 saat öncesine kadar "Biletlerim" sayfasından iptal işlemi yapabilirsiniz.</li>
                    <li>Bilet değişikliği için müşteri hizmetlerini arayınız.</li>
                    <li>Evcil hayvan, yanıcı ve patlayıcı maddeler otobüse alınmamaktadır.</li>
                    <li>İndirimli biletlerde kimlik ibrazı zorunludur.</li>
                </ul>
            </div>
        </div>
    </div>
    
    <?php include "footer.php"; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>