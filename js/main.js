// main.js - Ana JavaScript dosyası

$(document).ready(function() {
    // Tarih seçicisi için bugünün tarihini minimum olarak ayarla
    $('input[type="date"]').prop('min', function() {
        return new Date().toISOString().split('T')[0];
    });
    
    // Nereden-Nereye seçimlerinin aynı olmamasını sağla
    $('select[name="from"], select[name="to"]').change(function() {
        let fromVal = $('select[name="from"]').val();
        let toVal = $('select[name="to"]').val();
        
        if(fromVal && toVal && fromVal === toVal) {
            alert("Kalkış ve varış noktaları aynı olamaz!");
            $(this).val("");
        }
    });
    
    // Bilet arama formu gönderildiğinde kontroller
    $('#searchForm').submit(function(e) {
        let fromVal = $('select[name="from"]').val();
        let toVal = $('select[name="to"]').val();
        let dateVal = $('input[name="date"]').val();
        
        if(!fromVal || !toVal || !dateVal) {
            e.preventDefault();
            alert("Lütfen tüm alanları doldurun!");
            return false;
        }
        
        if(fromVal === toVal) {
            e.preventDefault();
            alert("Kalkış ve varış noktaları aynı olamaz!");
            return false;
        }
        
        return true;
    });
    
    // Giriş animasyonları
    $('.feature-box, .popular-route, .trip-card').each(function(i) {
        let element = $(this);
        setTimeout(function() {
            element.addClass('show');
        }, i * 100);
    });
});

// PNR sorgulama fonksiyonu
function checkPNR() {
    let pnrCode = $('#pnrCode').val().trim();
    
    if(!pnrCode) {
        alert("Lütfen PNR kodunu girin!");
        return false;
    }
    
    // PNR sorgulaması yap (AJAX ile)
    $.ajax({
        url: 'check_pnr.php',
        type: 'POST',
        data: {pnr: pnrCode},
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Bileti göster
                $('#pnrResult').html(response.html).removeClass('d-none');
                $('#pnrForm').addClass('d-none');
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert("Bir hata oluştu! Lütfen daha sonra tekrar deneyin.");
        }
    });
    
    return false;
}