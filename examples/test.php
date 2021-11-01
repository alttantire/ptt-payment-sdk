<?php
/**
 *
 *   Posta ve Telgraf Teşkilatı A.Ş. Genel Müdürlüğü adına Alttantire Yazılım Çözümleri tarafından geliştirilmiştir.
 *   Tüm hakları Posta ve Telgraf Teşkilatı A.Ş. Genel Müdürlüğü'ne aittir.
 *
 * @author      Erdem KAYA <erdem@alttantire.com>
 * @site        <https//akilliesnaf.ptt.gov.tr/>
 * @date        2021
 *
 */

use AkilliEsnaf\Gateway;

include "../vendor/autoload.php";

$testCards = [];
$testCards[] = (object)['name' => 'John Doe', 'number' => "4159560047417732", 'expiry' => "08/24", 'cvv' => "123",];
$testCards[] = (object)['name' => 'John Doe', 'number' => "4119790155203496", 'expiry' => "04/24", 'cvv' => "579",];
$testCards[] = (object)['name' => 'John Doe', 'number' => "4119790166544284", 'expiry' => "04/24", 'cvv' => "961",];
$testCards[] = (object)['name' => 'John Doe', 'number' => "4090700101174272", 'expiry' => "12/22", 'cvv' => "104",];
$testCards[] = (object)['name' => 'John Doe', 'number' => "4090700090840057", 'expiry' => "11/22", 'cvv' => "592",];

$apiUser = "Entegrasyon_01";
$clientId = "1000000032";
$apiPass = "gkk4l2*TY112";

$gateway = new Gateway($clientId, $apiUser, $apiPass);

$orderId = "123127";
$result = $gateway->threeDPayment("http://www.example.com/return", 1000, "0", $orderId);
$ThreeDSessionId = $result->ThreeDSessionId;
//$ThreeDSessionId = "P3D314A7A6F394E5E8516564EB46058238268460411BC413F91A452C3C201044A";
//print_r([$result,$ThreeDSessionId]);

//$result = $gateway->threeDSessionResult($ThreeDSessionId);
//$result = $gateway->processThreeD($ThreeDSessionId,$orderId);

//print_r($result);
//$result = $gateway->history(date("Ymd"));
//$url = $gateway->getFrameUrl($ThreeDSessionId);
//iframe($url);

$card = $testCards[2];
$params = $gateway->getFormParams($ThreeDSessionId, $card->name, $card->number, $card->expiry, $card->cvv);
$url = $gateway->getFormUrl();

paymentForm($url, $params);

print_r($result);

die("ok");

function iframe($url)
{
    echo '<iframe src="' . $url . '" height="550" width="800"></iframe>';
}

function paymentForm($url, $params)
{
    echo '<form method="POST" action="' . $url . '">';
    foreach ($params as $key => $value) {
        echo '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
    }
    echo '<button type="submit">Gönder</button>';
    echo '</form>';
}
