<?php
// contact.php - İletişim sayfası
require_once "config.php";

// İletişim formu kontrol
$name = $email = $subject = $message = "";
$name_err = $email_err = $subject_err = $message_err = "";
$success_message = "";

// Form gönderildiğinde
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Ad Soyad kontrolü
    if(empty(trim($_POST["name"]))){
        $name_err = "Lütfen adınızı ve soyadınızı girin.";     
    } else{
        $name = trim($_POST["name"]);
    }
    
    // Email kontrolü
    if(empty(trim($_POST["email"]))){
        $email_err = "Lütfen email adresinizi girin.";     
    } else {
        $email = trim($_POST["email"]);
        // Email formatı kontrolü
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $email_err = "Geçersiz email formatı.";
        }
    }
    
    // Konu kontrolü
    if(empty(trim($_POST["subject"]))){
        $subject_err = "Lütfen mesaj konusunu girin.";     
    } else{
        $subject = trim($_POST["subject"]);
    }
    
    // Mesaj kontrolü
    if(empty(trim($_POST["message"]))){
        $message_err = "Lütfen mesajınızı girin.";     
    } else{
        $message = trim($_POST["message"]);
    }
    
    // Hata yoksa mesaj gönderme (örnek)
    if(empty($name_err) && empty($email_err) && empty($subject_err) && empty($message_err)){
        // Gerçekte buraya mail gönderme kodları gelebilir
        // mail($to, $subject, $message_body, $headers);
        
        $success_message = "Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.";
        $name = $email = $subject = $message = ""; // Formu temizle
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İletişim - Otobüs Bilet Sistemi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
        }
        .contact-page {
            padding: 60px 0;
        }
        .contact-info-item {
            margin-bottom: 25px;
        }
        .contact-info-item i {
            font-size: 24px;
            color: #007bff;
            margin-right: 15px;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border-radius: 50%;
            background-color: rgba(0, 123, 255, 0.1);
        }
        .contact-info-text {
            display: inline-block;
            vertical-align: middle;
        }
        .contact-info-text h5 {
            margin-bottom: 5px;
        }
        .contact-form {
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }
        .map-container {
            height: 400px;
            margin-top: 40px;
            border-radius: 10px;
            overflow: hidden;
        }
        .page-header {
            background-color: #f5f5f5;
            padding: 40px 0;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    
    <div class="page-header">
        <div class="container">
            <h1>İletişim</h1>
            <p class="lead">Sorularınız ve önerileriniz için bizimle iletişime geçebilirsiniz</p>
        </div>
    </div>
    
    <div class="contact-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <h3 class="mb-4">İletişim Bilgileri</h3>
                    
                    <div class="contact-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="contact-info-text">
                            <h5>Adres</h5>
                            <p>Atatürk Caddesi No:123<br>Beşiktaş, İstanbul, Türkiye</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <i class="fas fa-phone"></i>
                        <div class="contact-info-text">
                            <h5>Telefon</h5>
                            <p>+90 (212) 123 4567<br>+90 (212) 987 6543</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <i class="fas fa-envelope"></i>
                        <div class="contact-info-text">
                            <h5>E-posta</h5>
                            <p>info@otobusbilet.com<br>destek@otobusbilet.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <i class="fas fa-clock"></i>
                        <div class="contact-info-text">
                            <h5>Çalışma Saatleri</h5>
                            <p>Pazartesi - Cumartesi: 09:00 - 18:00<br>Pazar: Kapalı</p>
                        </div>
                    </div>
                    
                    <div class="social-links mt-4">
                        <h5 class="mb-3">Sosyal Medya</h5>
                        <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-info me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-danger me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-outline-primary"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-7">
                    <div class="contact-form">
                        <h3 class="mb-4">Bize Mesaj Gönderin</h3>
                        
                        <?php if(!empty($success_message)): ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">Ad Soyad</label>
                                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                                <div class="invalid-feedback"><?php echo $name_err; ?></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">E-posta</label>
                                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                <div class="invalid-feedback"><?php echo $email_err; ?></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Konu</label>
                                <input type="text" name="subject" class="form-control <?php echo (!empty($subject_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $subject; ?>">
                                <div class="invalid-feedback"><?php echo $subject_err; ?></div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mesaj</label>
                                <textarea name="message" class="form-control <?php echo (!empty($message_err)) ? 'is-invalid' : ''; ?>" rows="5"><?php echo $message; ?></textarea>
                                <div class="invalid-feedback"><?php echo $message_err; ?></div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Mesaj Gönder
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="map-container">
                <!-- Google Harita ekleme (örnek) -->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3009.1803771427437!2d29.0047259!3d41.0418377!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab7a24975fe5d%3A0x25e50a22560abc4b!2sBe%C5%9Fikta%C5%9F%2C%20Istanbul!5e0!3m2!1sen!2str!4v1652788948684!5m2!1sen!2str" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
    
    <?php include "footer.php"; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>
</html>