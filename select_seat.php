<?php
// select_seat.php - Koltuk seçim sayfası
require_once "config.php";

// Kullanıcı girişi kontrolü
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Sefer ID kontrolü
if(!isset($_GET['trip_id'])) {
    header("location: index.php");
    exit;
}

$trip_id = cleanInput($_GET['trip_id']);

// Sefer bilgilerini al
$sql = "SELECT t.id, t.departure_time, t.arrival_time, t.price, 
        c1.name as departure_city, c2.name as arrival_city,
        co.name as company_name, b.seat_count, b.id as bus_id
        FROM trips t
        JOIN routes r ON t.route_id = r.id
        JOIN cities c1 ON r.departure_city_id = c1.id
        JOIN cities c2 ON r.arrival_city_id = c2.id
        JOIN buses b ON t.bus_id = b.id
        JOIN companies co ON b.company_id = co.id
        WHERE t.id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $trip_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header("location: index.php");
    exit;
}

$trip = mysqli_fetch_assoc($result);

// Dolu koltukları al
$sql = "SELECT s.seat_number, t.passenger_gender 
        FROM tickets t
        JOIN seats s ON t.seat_id = s.id
        WHERE t.trip_id = ? AND t.is_cancelled = 0";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $trip_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$occupied_seats = [];
while($row = mysqli_fetch_assoc($result)) {
    $occupied_seats[$row['seat_number']] = $row['passenger_gender'];
}

// Tüm koltukları getir
$sql = "SELECT id, seat_number, is_window FROM seats WHERE bus_id = ? ORDER BY seat_number";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $trip['bus_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$seats = [];
while($row = mysqli_fetch_assoc($result)) {
    $seats[$row['seat_number']] = $row;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Koltuk Seçimi - Otobüs Bilet Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .seat {
            width: 40px;
            height: 40px;
            margin: 5px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .seat.available {
            background-color: #dee2e6;
            color: #495057;
        }
        .seat.selected {
            background-color: #28a745;
            color: white;
        }
        .seat.occupied-male {
            background-color: #007bff;
            color: white;
            cursor: not-allowed;
        }
        .seat.occupied-female {
            background-color: #ff69b4;
            color: white;
            cursor: not-allowed;
        }
        .seat.window {
            border: 2px solid #17a2b8;
        }
        .bus-layout {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .seat-legend {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin: 0 10px;
        }
        .legend-box {
            width: 20px;
            height: 20px;
            margin-right: 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">Koltuk Seçimi</h4>
                <div class="trip-details mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Güzergah:</strong> <?php echo $trip['departure_city'] . ' - ' . $trip['arrival_city']; ?></p>
                            <p><strong>Firma:</strong> <?php echo $trip['company_name']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tarih/Saat:</strong> <?php echo formatDate($trip['departure_time']); ?></p>
                            <p><strong>Fiyat:</strong> ₺<?php echo number_format($trip['price'], 2); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="seat-legend">
                    <div class="legend-item">
                        <div class="legend-box" style="background-color: #dee2e6;"></div>
                        <span>Boş</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box" style="background-color: #28a745;"></div>
                        <span>Seçili</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box" style="background-color: #007bff;"></div>
                        <span>Erkek</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box" style="background-color: #ff69b4;"></div>
                        <span>Kadın</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box" style="border: 2px solid #17a2b8;"></div>
                        <span>Cam Kenarı</span>
                    </div>
                </div>
                
                <div class="bus-layout">
                    <div class="text-center mb-3">
                        <div class="driver-seat">
                            <i class="fas fa-user-tie fa-2x"></i>
                            <br>
                            <small>Şoför</small>
                        </div>
                    </div>
                    
                    <div class="seat-container text-center">
                        <?php 
                        // Otobüs düzeni - 2+1 düzeni için örnek
                        $total_rows = ceil($trip['seat_count'] / 3);
                        
                        for($row = 1; $row <= $total_rows; $row++) {
                            echo '<div class="seat-row">';
                            
                            // Sol taraf (2 koltuk)
                            for($i = 1; $i <= 2; $i++) {
                                $seat_num = ($row - 1) * 3 + $i;
                                if($seat_num <= $trip['seat_count']) {
                                    renderSeat($seat_num, $seats, $occupied_seats);
                                } else {
                                    echo '<div class="seat-placeholder"></div>';
                                }
                            }
                            
                            // Koridor
                            echo '<div class="corridor"></div>';
                            
                            // Sağ taraf (1 koltuk)
                            $seat_num = ($row - 1) * 3 + 3;
                            if($seat_num <= $trip['seat_count']) {
                                renderSeat($seat_num, $seats, $occupied_seats);
                            } else {
                                echo '<div class="seat-placeholder"></div>';
                            }
                            
                            echo '</div>';
                        }
                        
                        function renderSeat($seat_num, $seats, $occupied_seats) {
                            $is_window = isset($seats[$seat_num]) && $seats[$seat_num]['is_window'] ? 'window' : '';
                            $seat_id = isset($seats[$seat_num]) ? $seats[$seat_num]['id'] : '';
                            
                            if(isset($occupied_seats[$seat_num])) {
                                $gender_class = $occupied_seats[$seat_num] == 'E' ? 'occupied-male' : 'occupied-female';
                                echo "<div class='seat $gender_class $is_window' data-seat-number='$seat_num' data-seat-id='$seat_id'>$seat_num</div>";
                            } else {
                                echo "<div class='seat available $is_window' data-seat-number='$seat_num' data-seat-id='$seat_id'>$seat_num</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <form id="bookingForm" action="complete_booking.php" method="post">
                    <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                    <input type="hidden" name="seat_id" id="selected_seat_id" value="">
                    
                    <div class="mb-3">
                        <label class="form-label">Seçilen Koltuk</label>
                        <input type="text" class="form-control" id="selected_seat_display" readonly value="Henüz koltuk seçilmedi">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Yolcu Adı Soyadı</label>
                        <input type="text" class="form-control" name="passenger_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">TC Kimlik No</label>
                        <input type="text" class="form-control" name="passenger_tc" pattern="\d{11}" maxlength="11">
                        <small class="text-muted">İsteğe bağlı, 11 haneli TC kimlik numarası</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Cinsiyet</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="passenger_gender" id="gender_male" value="E" required>
                                <label class="form-check-label" for="gender_male">Erkek</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="passenger_gender" id="gender_female" value="K" required>
                                <label class="form-check-label" for="gender_female">Kadın</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="bookButton" disabled>
                            Rezervasyon Yap
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include "footer.php"; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Koltuk seçimi
            $('.seat.available').click(function() {
                // Önceki seçili koltuğu temizle
                $('.seat.selected').removeClass('selected').addClass('available');
                
                // Bu koltuğu seç
                $(this).removeClass('available').addClass('selected');
                
                // Form alanlarını güncelle
                var seatNumber = $(this).data('seat-number');
                var seatId = $(this).data('seat-id');
                
                $('#selected_seat_display').val(seatNumber);
                $('#selected_seat_id').val(seatId);
                
                // Rezervasyon butonunu etkinleştir
                $('#bookButton').prop('disabled', false);
            });
            
            // Form gönderildiğinde koltuk seçimi kontrolü
            $('#bookingForm').submit(function(e) {
                if($('#selected_seat_id').val() === '') {
                    e.preventDefault();
                    alert('Lütfen bir koltuk seçin!');
                }
            });
        });
    </script>
</body>
</html>