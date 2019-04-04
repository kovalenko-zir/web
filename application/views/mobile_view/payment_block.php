<div class="col-12">
    <h4 class="block_name">Payment method</h4>
</div>
<?php
if (!(empty($payment_cards))) {
    foreach ($payment_cards as $value) {
        echo '<div class="col-12 payment_method"><img class="mastercard_icon" src="/public/images/' . $value["type"] . '.png">●●●●  <span class="fourNum" >' . $value["fourNum"] . '</span><img class="checked_icon" src="/public/images/checked.png"></div>';}
}
?>

<div id="user_add_payment_method" class="col-12  blocks">Add credit/debit card</div>