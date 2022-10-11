<?php
/**
 *
 *   Posta ve Telgraf Teşkilatı A.Ş. Genel Müdürlüğü adına Alttantire Yazılım Çözümleri tarafından geliştirilmiştir.
 *   Tüm hakları Posta ve Telgraf Teşkilatı A.Ş. Genel Müdürlüğü'ne aittir.
 *
 * @author      Alttantire Yazılım Çözümleri <info@alttantire.com>
 * @site        <https//akilliesnaf.ptt.gov.tr/>
 * @date        2022
 *
 */

use AkilliEsnaf\Gateway;
include "../../../../vendor/autoload.php";

//### Sanal POS Üye İşyeri Ayarları
$apiUser = "Entegrasyon_01"; // Api kullanıcı adınız
$clientId = "1000000032"; // Api müşteri numaranız
$apiPass = "gkk4l2*TY112"; // Api şifreniz
$environment = "TEST"; // "LIVE" - Gerçek ortam | "TEST" - Test ortam
$callback_url = "//".$_SERVER['HTTP_HOST'] . substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "/"))."/payment-response.php"; // Ödeme işlem sonucunun döneceği adres - https://www.siteadresiniz.com/3D-sonuc.php

//### Sipariş Bilgileri
$orderId = "20221011999"; // Sipariş numarası her sipariş için tekil olmalıdır. Boş bırakıldığında sistem tarafından üretilir
$amount = 1090; // 10 TL 90 kuruş
$instalment = 0; // Taksit sayısı - Tek çekim için 0

//### API Gateway
$gateway = new Gateway($environment, $clientId, $apiUser, $apiPass);
$payment = $gateway->threeDPayment($callback_url, $amount, $instalment, $orderId);

?>

<html xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>PTT Akıllı Esnaf 3D Ödeme - Örnek</title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <style>
        body {
            margin-top: 20px;
        }
        .panel-title {
            display: inline;
            font-weight: bold;
        }
        .checkbox.pull-right {
            margin: 0;
        }
        .pl-ziro {
            padding-left: 0px;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="row">
        <form role="form" method="post" action="<?php echo $gateway->getFormUrl();?>">

            <input type="hidden" name="ThreeDSessionId" value="<?php echo $payment->ThreeDSessionId;?>">

            <div class="col-xs-12 col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Ödeme Formu
                    </h3>
                </div>
                <div class="panel-body">
                        <div class="form-group">
                            <label for="cardNumber">
                                Kart Üzerindeki İsim Soyisim</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="CardHolderName" placeholder="İsim Soyisim" required autofocus/>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cardNumber">
                                Kart Bilgileri</label>
                            <div class="input-group">
                                <input type="text" class="form-control"  name="CardNo" placeholder="Kredi Kart Numarası" required autofocus/>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6 col-md-6 pull-left">
                                <div class="form-group">
                                    <label for="cvCode">
                                        Son Kullanım Tarihi</label>
                                    <input type="text" class="form-control" name="ExpireDate"placeholder="AA/YY" required/>
                                </div>
                            </div>
                            <div class="col-xs-6 col-md-6 pull-right">
                                <div class="form-group">
                                    <label for="cvCode">
                                        CVV</label>
                                    <input type="text" class="form-control" name="Cvv"  placeholder="CVV" required/>
                                </div>
                            </div>
                        </div>

                </div>
            </div>
            <ul class="nav nav-pills nav-stacked">
                <li class="active"><a href="#"><span class="badge pull-right " style="font-size: 15px">
                           <?php echo number_format($amount/100, 2, '.', '');?> TL</span> Ödenecek Tutar</a>
                </li>
            </ul>
            <br/>
            <button type="submit" class="btn btn-success btn-lg btn-block">3D ile Ödemeyi Tamamla</button>
        </div>

        </form>
    </div>
</div>

<?php include("test-credit-cards.php");?>

</body>
</html>
