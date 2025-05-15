<?php
// profile.php - Kullanıcı profil sayfası
require_once "config.php";

// Kullanıcı girişi kontrolü
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Kullanıcı bilgilerini çekme
$sql = "SELECT username, email, full_name, phone, created_at FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim - Otobüs Bilet Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">Kullanıcı Profili</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                            <h5 class="mt-3"><?php echo htmlspecialchars($user["full_name"]); ?></h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kullanıcı Adı:</label>
                                <p class="form-control bg-light"><?php echo htmlspecialchars($user["username"]); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Email:</label>
                                <p class="form-control bg-light"><?php echo htmlspecialchars($user["email"]); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Telefon:</label>
                                <p class="form-control bg-light"><?php echo htmlspecialchars($user["phone"]); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kayıt Tarihi:</label>
                                <p class="form-control bg-light"><?php echo date('d.m.Y', strtotime($user["created_at"])); ?></p>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-3">
                            <a href="edit_profile.php" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Profili Düzenle
                            </a>
                            <a href="change_password.php" class="btn btn-outline-primary">
                                <i class="fas fa-key"></i> Şifre Değiştir
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow mt-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">Son Biletler</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        // Son biletleri çek
                        $sql = "SELECT t.id, t.purchase_time, t.pnr_code, tr.departure_time, tr.arrival_time,
                                c1.name as departure_city, c2.name as arrival_city
                                FROM tickets t
                                JOIN trips tr ON t.trip_id = tr.id
                                JOIN routes r ON tr.route_id = r.id
                                JOIN cities c1 ON r.departure_city_id = c1.id
                                JOIN cities c2 ON r.arrival_city_id = c2.id
                                WHERE t.user_id = ? AND t.is_cancelled = 0
                                ORDER BY t.purchase_time DESC LIMIT 3";
                        
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        
                        if(mysqli_num_rows($result) > 0) {
                            while($ticket = mysqli_fetch_assoc($result)) {
                                echo '<div class="card mb-2">';
                                echo '<div class="card-body">';
                                echo '<div class="d-flex justify-content-between align-items-center">';
                                echo '<div>';
                                echo '<h6>' . htmlspecialchars($ticket["departure_city"]) . ' - ' . htmlspecialchars($ticket["arrival_city"]) . '</h6>';
                                echo '<small class="text-muted">PNR: ' . htmlspecialchars($ticket["pnr_code"]) . ' | Tarih: ' . date('d.m.Y', strtotime($ticket["departure_time"])) . '</small>';
                                echo '</div>';
                                echo '<a href="view_ticket.php?id=' . $ticket["id"] . '" class="btn btn-sm btn-outline-primary">Bileti Görüntüle</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="alert alert-info">Henüz bilet satın almadınız.</div>';
                        }
                        ?>
                        
                        <div class="text-center mt-3">
                            <a href="my_tickets.php" class="btn btn-outline-primary btn-sm">Tüm Biletlerim</a>
                        </div>
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