<?php
// login.php - Kullanıcı giriş sayfası
require_once "config.php";
 
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Form gönderildiğinde
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Kullanıcı adı kontrolü
    if(empty(trim($_POST["username"]))){
        $username_err = "Lütfen kullanıcı adınızı girin.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Şifre kontrolü
    if(empty(trim($_POST["password"]))){
        $password_err = "Lütfen şifrenizi girin.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Giriş doğrulama
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                // Kullanıcı adı varsa
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Şifre doğruysa, oturum başlat
                            session_start();
                            
                            // Oturum değişkenlerini ata
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Ana sayfaya yönlendir
                            header("location: index.php");
                        } else{
                            // Şifre yanlışsa
                            $login_err = "Geçersiz kullanıcı adı veya şifre.";
                        }
                    }
                } else{
                    // Kullanıcı adı yoksa
                    $login_err = "Geçersiz kullanıcı adı veya şifre.";
                }
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
    <title>Giriş - Otobüs Bilet Sistemi</title>
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
                        <h4 class="card-title mb-0">Giriş Yap</h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        if(!empty($login_err)){
                            echo '<div class="alert alert-danger">' . $login_err . '</div>';
                        }        
                        ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">Kullanıcı Adı</label>
                                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                <div class="invalid-feedback"><?php echo $username_err; ?></div>
                            </div>    
                            <div class="mb-3">
                                <label class="form-label">Şifre</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                <div class="invalid-feedback"><?php echo $password_err; ?></div>
                            </div>
                            <div class="d-grid gap-2">
                                <input type="submit" class="btn btn-primary" value="Giriş Yap">
                                <a href="register.php" class="btn btn-link">Hesabınız yok mu? Kayıt olun</a>
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