<?php
// register.php - Kullanıcı kayıt sayfası
require_once "config.php";

$username = $password = $confirm_password = $email = $full_name = $phone = "";
$username_err = $password_err = $confirm_password_err = $email_err = $full_name_err = $phone_err = "";

// Form gönderildiğinde
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Kullanıcı adını kontrol et
    if(empty(trim($_POST["username"]))){
        $username_err = "Lütfen bir kullanıcı adı girin.";
    } else{
        // SQL sorgusu
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            $param_username = trim($_POST["username"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Bu kullanıcı adı zaten alınmış.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Hata oluştu! Lütfen daha sonra tekrar deneyin.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // Email kontrolü
    if(empty(trim($_POST["email"]))){
        $email_err = "Lütfen email adresinizi girin.";     
    } else{
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            $param_email = trim($_POST["email"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "Bu email adresi zaten kullanılıyor.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Hata oluştu! Lütfen daha sonra tekrar deneyin.";
            }

            mysqli_stmt_close($stmt);
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
    
    // Şifre kontrolü
    if(empty(trim($_POST["password"]))){
        $password_err = "Lütfen bir şifre girin.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Şifre en az 6 karakter olmalıdır.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Şifre tekrar kontrolü
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Lütfen şifrenizi tekrar girin.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Şifreler eşleşmiyor.";
        }
    }
    
    // Hatalar kontrol edilmeden önce veritabanına ekleme
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err) && empty($full_name_err) && empty($phone_err)){
        
        // Kayıt SQL
        $sql = "INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "sssss", $param_username, $param_password, $param_email, $param_full_name, $param_phone);
            
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Şifreyi hashle
            $param_email = $email;
            $param_full_name = $full_name;
            $param_phone = $phone;
            
            if(mysqli_stmt_execute($stmt)){
                // Başarılı ise giriş sayfasına yönlendir
                header("location: login.php");
            } else{
                echo "Hata oluştu! Lütfen daha sonra tekrar deneyin.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol - Otobüs Bilet Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">Kayıt Ol</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">Kullanıcı Adı</label>
                                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                <div class="invalid-feedback"><?php echo $username_err; ?></div>
                            </div>    
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                <div class="invalid-feedback"><?php echo $email_err; ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ad Soyad</label>
                                <input type="text" name="full_name" class="form-control <?php echo (!empty($full_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $full_name; ?>">
                                <div class="invalid-feedback"><?php echo $full_name_err; ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                                <div class="invalid-feedback"><?php echo $phone_err; ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Şifre</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                                <div class="invalid-feedback"><?php echo $password_err; ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Şifre Tekrar</label>
                                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                                <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
                            </div>
                            <div class="d-grid gap-2">
                                <input type="submit" class="btn btn-primary" value="Kayıt Ol">
                                <a href="login.php" class="btn btn-link">Zaten üye misiniz? Giriş yapın</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "footer.php"; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>