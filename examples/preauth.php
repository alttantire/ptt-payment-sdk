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

//### API Gateway
$gateway = new Gateway($environment, $clientId, $apiUser, $apiPass);

//### Sipariş Bilgileri
$amount=24990; // 249 TL 90 Kuruş
$installment=0; // Tek çekim
$orderId=""; // Sipariş numarası - Boş gönderildiğinde sistem tarafından otomatik üretilir
$description=""; // Opsiyonel sipariş açıklaması
$callbackUrl = "//".$_SERVER['HTTP_HOST'] . substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], "/"))."/payment-response.php"; // Ödeme işlem sonucunun döneceği adres - https://www.siteadresiniz.com/3D-sonuc.php

try {
    $preAuth = $gateway->threeDPreAuth($callbackUrl, $amount, $installment, $orderId, $description);
    $ThreeDSessionId = $preAuth->ThreeDSessionId;

    echo "<pre>".print_r($preAuth,true)."</pre>";

} catch (Exception $e) {
    print_r($e);
}