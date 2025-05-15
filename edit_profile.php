<?php
// edit_profile.php - Profil düzenleme sayfası
require_once "config.php";

// Kullanıcı girişi kontrolü
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Form değişkenleri ve hata mesajları
$email = $full_name = $phone = "";
$email_err = $full_name_err = $phone_err = "";
$success_message = "";

// Kullanıcı bilgilerini çek
$sql = "SELECT email, full_name, phone FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Form verileri ile değişkenleri doldur
$email = $user["email"];
$full_name = $user["full_name"];
$phone = $user["phone"];

// Form gönderildiğinde
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Email kontrolü
    if(empty(trim($_POST["email"]))){
        $email_err = "Lütfen email adresinizi girin.";     
    } else {
        $email = trim($_POST["email"]);
        // Email formatı kontrolü
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $email_err = "Geçersiz email formatı.";
        } else {
            // Email başkası tarafından kullanılıyor mu?
            $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $email, $_SESSION["id"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) > 0){
                $email_err = "Bu email adresi zaten kullanılıyor.";
            }
        }
    }
    
    // Ad Soyad kontrolü
    if(empty(trim($_POST["full_name"]))){
        $full_name_err = "Lütfen adınızı ve soyadınızı girin.";     
    } else{
        $full_name = trim($_POST["full_name"]);
    }
    
    // Telefon kontrolü
    if(empty(trim($_POST["phone"]))){
        $phone_err = "Lütfen telefon numaranızı girin.";     
    } else{
        $phone = trim($_POST["phone"]);
    }
    
    // Hata yoksa güncelleme işlemi
    if(empty($email_err) && empty($full_name_err) && empty($phone_err)){
        
        // Güncelleme SQL
        $sql = "UPDATE users SET email = ?, full_name = ?, phone = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sssi", $param_email, $param_full_name, $param_phone, $param_id);
            
            $param_email = $email;
            $param_full_name = $full_name;
            $param_phone = $phone;
            $param_id = $_SESSION["id"];
            
            if(mysqli_stmt_execute($stmt)){
                $success_message = "Profil bilgileriniz başarıyla güncellendi.";
            } else{
                echo "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profil Düzenle - Otobüs Bilet Sistemi</title>
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
                        <h4 class="card-title mb-0">Profil Düzenle</h4>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($success_message)): ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">Kullanıcı Adı</label>
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($_SESSION["username"]); ?>" readonly>
                                <small class="text-muted">Kullanıcı adı değiştirilemez.</small>
                            </div>    
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>">
                                <div class="invalid-feedback"><?php echo $email_err; ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ad Soyad</label>
                                <input type="text" name="full_name" class="form-control <?php echo (!empty($full_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($full_name); ?>">
                                <div class="invalid-feedback"><?php echo $full_name_err; ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($phone); ?>">
                                <div class="invalid-feedback"><?php echo $phone_err; ?></div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Değişiklikleri Kaydet
                                </button>
                                <a href="profile.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Vazgeç
                                </a>
                            </div>
                        </form>
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