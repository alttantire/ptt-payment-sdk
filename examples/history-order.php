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
/*
 * İlgili tarihteki tüm işlem geçmişini listeler.
 * Sipariş numarası gönderilmesi durumunda o siparişin tüm işlem geçmişini listeler.
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

/*
 * Sipariş numarası dolu gönderildiğinde o siparişe ait tüm işlemleri listeler.
 * Sipariş numarası boş gönderildiğinde o tarihteki tüm siparişleri listeler
*/
$orderId = "20221011999";

/*
 * Sipariş tarihi integer olmalıdır.
 * Listesi istenen tarih YYYYAAGG formatında yazılmalıdır.
*/
$date = 20221011;

try {
    $paymentCheck = $gateway->history($date, $orderId, $page = 1, $pageSize = 10);
} catch (Exception $e) {
    print_r($e);
}

echo "<pre>";
print_r($paymentCheck);
