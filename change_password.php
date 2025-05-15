<?php
// change_password.php - Şifre değiştirme sayfası
require_once "config.php";

// Kullanıcı girişi kontrolü
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Form değişkenleri ve hata mesajları
$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";
$success_message = "";

// Form gönderildiğinde
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Mevcut şifre kontrolü
    if(empty(trim($_POST["current_password"]))){
        $current_password_err = "Lütfen mevcut şifrenizi girin.";     
    } else{
        $current_password = trim($_POST["current_password"]);
        
        // Mevcut şifre doğru mu?
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        if(!password_verify($current_password, $row["password"])){
            $current_password_err = "Mevcut şifreniz hatalı.";
        }
    }
    
    // Yeni şifre kontrolü
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Lütfen yeni şifrenizi girin.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Şifre en az 6 karakter olmalıdır.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Yeni şifre tekrar kontrolü
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Lütfen şifrenizi tekrar girin.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Şifreler eşleşmiyor.";
        }
    }
    
    // Hata yoksa güncelleme işlemi
    if(empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)){
        
        // Şifre güncelleme SQL
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            if(mysqli_stmt_execute($stmt)){
                $success_message = "Şifreniz başarıyla değiştirildi.";
                
                // Tüm form alanlarını temizle
                $current_password = $new_password = $confirm_password = "";
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
    <title>Şifre Değiştir - Otobüs Bilet Sistemi</title>
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
                        <h4 class="card-title mb-0">Şifre Değiştir</h4>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($success_message)): ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">Mevcut Şifre</label>
                                <input type="password" name="current_password" class="form-control <?php echo (!empty($current_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $current_password; ?>">
                                <div class="invalid-feedback"><?php echo $current_password_err; ?></div>
                            </div>    
                            <div class="mb-3">
                                <label class="form-label">Yeni Şifre</label>
                                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                                <div class="invalid-feedback"><?php echo $new_password_err; ?></div>
                                <small class="text-muted">Şifreniz en az 6 karakter olmalıdır.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Yeni Şifre Tekrar</label>
                                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                                <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Şifreyi Değiştir
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