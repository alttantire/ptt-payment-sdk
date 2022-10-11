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

$testCards = [];
$testCards[] = (object)['name' => 'John Doe', 'number' => "4119790155203496", 'expiry' => "04/24", 'cvv' => "579",];
$testCards[] = (object)['name' => 'John Doe', 'number' => "4119790166544284", 'expiry' => "04/24", 'cvv' => "961",];
$testCards[] = (object)['name' => 'John Doe', 'number' => "4090700101174272", 'expiry' => "12/22", 'cvv' => "104",];
$testCards[] = (object)['name' => 'John Doe', 'number' => "4090700090840057", 'expiry' => "11/22", 'cvv' => "592",];
?>

<hr/>
<div class="container" >
    <div class="row">

        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <h3>Test Kartları</h3>

        </div>

    </div>
    <div class="row">

        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <table class="table table-hover">
                <thead>
                <th>İsim Soyisim</th>
                <th>Kart Numarası</th>
                <th>Son Kullanım Tarihi</th>
                <th>CVV</th>
                </thead>

                <tbody>

                <?php foreach ($testCards as $card):?>

                    <tr>
                        <td><?php echo $card->name;?></td>
                        <td><?php echo $card->number;?></td>
                        <td><?php echo $card->expiry;?></td>
                        <td><?php echo $card->cvv;?></td>

                    </tr>

                <?php endforeach;?>

                </tbody>
            </table>
        </div>
    </div>


</div>