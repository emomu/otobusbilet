<?php
// view_ticket.php - Bilet görüntüleme sayfası
require_once "config.php";

// Kullanıcı girişi kontrolü
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Bilet ID kontrolü
if(!isset($_GET['id'])) {
    header("location: my_tickets.php");
    exit;
}

$ticket_id = cleanInput($_GET['id']);

// Biletin bu kullanıcıya ait olduğunu kontrol et
$sql = "SELECT t.*, tr.departure_time, tr.arrival_time, tr.price,
        c1.name as departure_city, c2.name as arrival_city,
        co.name as company_name, s.seat_number
        FROM tickets t
        JOIN trips tr ON t.trip_id = tr.id
        JOIN routes r ON tr.route_id = r.id
        JOIN cities c1 ON r.departure_city_id = c1.id
        JOIN cities c2 ON r.arrival_city_id = c2.id
        JOIN seats s ON t.seat_id = s.id
        JOIN buses b ON s.bus_id = b.id
        JOIN companies co ON b.company_id = co.id
        WHERE t.id = ? AND t.user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $ticket_id, $_SESSION["id"]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header("location: my_tickets.php");
    exit;
}

$ticket = mysqli_fetch_assoc($result);

// Bileti iptal edebilme durumu
$can_cancel = false;
if(!$ticket['is_cancelled']) {
    $departure_time = new DateTime($ticket['departure_time']);
    $now = new DateTime();
    $interval = $now->diff($departure_time);
    
    // Sefer saatine 3 saat veya daha fazla varsa iptal edilebilir
    if($departure_time > $now && ($interval->days > 0 || $interval->h >= 3)) {
        $can_cancel = true;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bilet Detayı - Otobüs Bilet Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .ticket-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .ticket-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
            border-radius: 10px 10px 0 0;
        }
        .ticket-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin: 0;
        }
        .ticket-status {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            font-size: 0.875rem;
        }
        .ticket-status.active {
            background-color: #28a745;
        }
        .ticket-status.completed {
            background-color: #6c757d;
        }
        .ticket-status.cancelled {
            background-color: #dc3545;
        }
        .ticket-body {
            padding: 20px;
        }
        .ticket-info {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .ticket-info-item {
            width: 33.333%;
            padding: 10px 15px;
        }
        .ticket-label {
            font-weight: bold;
            color: #6c757d;
            margin-bottom: 5px;
            display: block;
        }
        .ticket-value {
            font-weight: bold;
            color: #212529;
            font-size: 1.1rem;
        }
        .ticket-actions {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        @media print {
            .ticket-actions, .navbar, .footer, .no-print {
                display: none !important;
            }
            .container {
                width: 100%;
                max-width: 100%;
            }
            body {
                background-color: white;
            }
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Bilet Detayları</h2>
                    <a href="my_tickets.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Biletlerim
                    </a>
                </div>
                
                <div class="ticket-card">
                    <div class="ticket-header">
                        <h3 class="ticket-title"><?php echo $ticket['departure_city']; ?> - <?php echo $ticket['arrival_city']; ?></h3>
                        <?php if($ticket['is_cancelled']): ?>
                            <span class="ticket-status cancelled">İptal Edildi</span>
                        <?php else: ?>
                            <?php 
                            $departure_time = new DateTime($ticket['departure_time']);
                            $now = new DateTime();
                            if($departure_time < $now):
                            ?>
                                <span class="ticket-status completed">Tamamlandı</span>
                            <?php else: ?>
                                <span class="ticket-status active">Aktif</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="ticket-body">
                        <div class="ticket-info">
                            <div class="ticket-info-item">
                                <span class="ticket-label">PNR Kodu:</span>
                                <span class="ticket-value"><?php echo $ticket['pnr_code']; ?></span>
                            </div>
                            <div class="ticket-info-item">
                                <span class="ticket-label">Tarih/Saat:</span>
                                <span class="ticket-value"><?php echo date('d.m.Y H:i', strtotime($ticket['departure_time'])); ?></span>
                            </div>
                            <div class="ticket-info-item">
                                <span class="ticket-label">Ücret:</span>
                                <span class="ticket-value">₺<?php echo number_format($ticket['price'], 2); ?></span>
                            </div>
                            
                            <div class="ticket-info-item">
                                <span class="ticket-label">Yolcu:</span>
                                <span class="ticket-value"><?php echo $ticket['passenger_name']; ?></span>
                            </div>
                            <div class="ticket-info-item">
                                <span class="ticket-label">Varış:</span>
                                <span class="ticket-value"><?php echo date('d.m.Y H:i', strtotime($ticket['arrival_time'])); ?></span>
                            </div>
                            <div class="ticket-info-item">
                                <span class="ticket-label">Satın Alma:</span>
                                <span class="ticket-value"><?php echo date('d.m.Y H:i', strtotime($ticket['purchase_time'])); ?></span>
                            </div>
                            
                            <div class="ticket-info-item">
                                <span class="ticket-label">Firma:</span>
                                <span class="ticket-value"><?php echo $ticket['company_name']; ?></span>
                            </div>
                            <div class="ticket-info-item">
                                <span class="ticket-label">Koltuk No:</span>
                                <span class="ticket-value"><?php echo $ticket['seat_number']; ?></span>
                            </div>
                            <?php if(!empty($ticket['passenger_tc'])): ?>
                            <div class="ticket-info-item">
                                <span class="ticket-label">TC Kimlik No:</span>
                                <span class="ticket-value"><?php echo $ticket['passenger_tc']; ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-center mt-4 mb-3 no-print">
                            <div class="qr-code">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $ticket['pnr_code']; ?>" alt="QR Kod">
                                <div class="mt-2"><small>Bilet bilgileriniz için QR kodu taratın</small></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ticket-actions">
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="fas fa-print"></i> Yazdır
                        </button>
                        
                       
                        
                        <?php if($can_cancel): ?>
                            <a href="my_tickets.php?cancel=1&ticket_id=<?php echo $ticket['id']; ?>" class="btn btn-danger" onclick="return confirm('Bu bileti iptal etmek istediğinize emin misiniz?')">
                                <i class="fas fa-times"></i> Bileti İptal Et
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card mb-4 no-print">
                    <div class="card-body">
                        <h5 class="card-title">Önemli Bilgiler</h5>
                        <ul class="mb-0">
                            <li>Lütfen yolculuk saatinden en az 30 dakika önce terminalde olunuz.</li>
                            <li>Bilet iptali için yolculuktan en az 3 saat öncesine kadar "Bileti İptal Et" butonunu kullanabilirsiniz.</li>
                            <li>Bilet değişikliği için müşteri hizmetlerini arayınız.</li>
                            <li>Evcil hayvan, yanıcı ve patlayıcı maddeler otobüse alınmamaktadır.</li>
                            <li>İndirimli biletlerde kimlik ibrazı zorunludur.</li>
                        </ul>
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