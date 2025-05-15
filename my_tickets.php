<?php
// my_tickets.php - Kullanıcının biletlerini gösteren sayfa
require_once "config.php";

// Kullanıcı girişi kontrolü
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Bilet iptali
if(isset($_GET['cancel']) && isset($_GET['ticket_id'])) {
    $ticket_id = cleanInput($_GET['ticket_id']);
    
    // Bilet bu kullanıcıya ait mi kontrol et
    $sql = "SELECT id FROM tickets WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $ticket_id, $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if(mysqli_stmt_num_rows($stmt) > 0) {
        // Bileti iptal et
        $sql = "UPDATE tickets SET is_cancelled = 1 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $ticket_id);
        mysqli_stmt_execute($stmt);
        
        // Yönlendirme yap
        header("location: my_tickets.php?success=1");
        exit;
    }
}

// Kullanıcının biletlerini getir
$sql = "SELECT t.id, t.pnr_code, t.passenger_name, t.purchase_time, t.is_cancelled,
        tr.departure_time, tr.arrival_time, tr.price,
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
        WHERE t.user_id = ?
        ORDER BY tr.departure_time DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Biletlerim - Otobüs Bilet Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <h2>Biletlerim</h2>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                Bilet iptal işlemi başarıyla gerçekleştirildi.
            </div>
        <?php endif; ?>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($ticket = mysqli_fetch_assoc($result)): ?>
                <?php 
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
                
                <div class="card mb-4 <?php echo $ticket['is_cancelled'] ? 'bg-light' : ''; ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo $ticket['departure_city'] . ' - ' . $ticket['arrival_city']; ?></h5>
                        <?php if($ticket['is_cancelled']): ?>
                            <span class="badge bg-danger">İptal Edildi</span>
                        <?php else: ?>
                            <?php 
                            $departure_time = new DateTime($ticket['departure_time']);
                            $now = new DateTime();
                            if($departure_time < $now):
                            ?>
                                <span class="badge bg-secondary">Tamamlandı</span>
                            <?php else: ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>PNR Kodu:</strong> <?php echo $ticket['pnr_code']; ?></p>
                                <p><strong>Yolcu:</strong> <?php echo $ticket['passenger_name']; ?></p>
                                <p><strong>Firma:</strong> <?php echo $ticket['company_name']; ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Tarih/Saat:</strong> <?php echo formatDate($ticket['departure_time']); ?></p>
                                <p><strong>Varış:</strong> <?php echo formatDate($ticket['arrival_time']); ?></p>
                                <p><strong>Koltuk No:</strong> <?php echo $ticket['seat_number']; ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Ücret:</strong> ₺<?php echo number_format($ticket['price'], 2); ?></p>
                                <p><strong>Satın Alma:</strong> <?php echo formatDate($ticket['purchase_time']); ?></p>
                                
                                <?php if($can_cancel): ?>
                                    <a href="my_tickets.php?cancel=1&ticket_id=<?php echo $ticket['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu bileti iptal etmek istediğinize emin misiniz?')">
                                        <i class="fas fa-times"></i> Bileti İptal Et
                                    </a>
                                <?php endif; ?>
                                
                                <?php if(!$ticket['is_cancelled']): ?>
                                    <a href="view_ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Bileti Görüntüle
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <h4 class="alert-heading">Henüz bilet satın almadınız!</h4>
                <p>Aktif veya geçmiş biletiniz bulunmamaktadır. Yeni bir bilet satın almak için aşağıdaki butonu kullanabilirsiniz.</p>
                <hr>
                <p class="mb-0">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-search"></i> Bilet Ara
                    </a>
                </p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include "footer.php"; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>