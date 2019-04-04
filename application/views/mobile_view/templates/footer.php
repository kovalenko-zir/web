</div><div id="checkout_button_block"><div class="col-10 offset-1 underline">

</div>
<div class="col-<?php if ($purchased && $page_name === 'digital') {
    echo 12;
} else {
    echo 5;
} ?> offset-1 total" <?php if ($purchased && $page_name === 'digital') {
    echo 'style="padding-left:32%"';
} ?>><?php if ($purchased && $page_name === 'digital') {
        echo 'PURCHASED';
    } else {
        echo 'Total';
    } ?></div>
<div class="col-5 total total_art_cost" <?php if ($purchased && $page_name === 'digital') {
    echo 'style="display:none"';
} ?> ><?php if ($page_name === 'digital') {
        if ($item['digitalPrice'] === 'FREE') {
            echo $item['digitalPrice'];
        } else {
            echo '$' . $item['digitalPrice'];
        }

    } else {
        echo '$' . $item['physicalPrice'];
    }

    ?></div>

<div id="<?php if ($page_name === 'digital' && $item['digitalPrice'] === 'FREE') {
    echo 'free_sender_button';
} else if ($page_name === 'digital' && $purchased) {
    echo 'purchased_item_button';
} else {
    echo 'checkout_button';
} ?>"
     class="offset-1 col-10 checkout_button <?php if ($page_name === 'digital' &&($item['digitalPrice'] === 'FREE' || $purchased)) {
         echo '';} else { echo 'not_active';} ?>"><?php if ($page_name === 'digital' && $item['digitalPrice'] === 'FREE') {
        echo 'Get download link';
    } else if ($purchased && $page_name === 'digital') {
        echo 'Get purchased link';
    } else {
        echo 'Checkout';
    } ?></div>

</div>
</div>
</div>

<div id="wrapper_ci" class="container-fluid">
    <div class="row">
        <div class="col-12 input_blocks">
            <input id='first_name' class='ci_inputs field' placeholder="First name" value="<?php echo $user['user_name'] ?>">
        </div>
        <div class="col-12 input_blocks mt-3 input_blocks_end">
            <input id='last_name' class='ci_inputs field' placeholder="Last name" value="<?php echo $user['user_surname'] ?>">
        </div>
        <div class="col-12 input_blocks mt-3 input_blocks_end">
            <input id='order_email' class='ci_inputs field' placeholder="Email" value="<?php echo $user['email'] ?>">
        </div>
        <div class="col-12 mt-3 error_description">
            <span style="display:none"></span>
        </div>
    </div>
</div>
<div id="wrapper_si" class="container-fluid">
    <div class="row">
        <div class="col-12 input_blocks">
<!--            <input id="order_country" class='si_inputs' placeholder="Country" value="--><?php //echo $user['country'] ?><!--">-->
<?php
    $empty=0;
    $empty_country='';

    if (empty($user['country'])){
            $empty_country='empty_country';
        };



    echo '<select id="order_country" class="si_inputs field '.$empty_country.'" >';

    foreach($country_list as $value){
        if ($value['UNTERM_English_Short']!==''){


            echo '<option ';
            if ($user['country']===$value['UNTERM_English_Short']){
                echo 'selected ';
                $empty++;
            }
            echo 'value="'.$value['UNTERM_English_Short'].'">'.$value['UNTERM_English_Short'].'</option>';

        }
    }
    if ($empty===0){
        echo '<option selected class="remove_this_option" value="">Country</option>';
    }
?>

            </select>
        </div>
        <div class="col-12 mt-3 input_blocks">
            <input id="order_city" class='si_inputs field' placeholder="City" value="<?php echo $user['city'] ?>">
        </div>
        <div class="col-12 mt-3 input_blocks">
            <input id="order_zip_code" class='si_inputs field' placeholder="Zip Code" value="<?php echo $user['zip_code'] ?>">
        </div>
        <div class="col-12 mt-3 input_blocks">
            <input id="order_adress" class='si_inputs field ' placeholder="Address line 1"
                   value="<?php echo $user['adress'] ?>">
        </div>
        <div class="col-12 mt-3 input_blocks input_blocks_end">
            <input id="order_appartment" class='si_inputs field' placeholder="Address line 2"
                   value="<?php echo $user['appartment'] ?>">
        </div>
        <div class="col-12 mt-3 error_description">
            <span style="display:none"></span>
        </div>
    </div>
</div>
<div id="wrapper_pi" class="container-fluid">
    <div class="row">
        <div class="col-12 input_blocks">
            <input id="cardholder_name" class='pi_inputs field' placeholder="Cardholder name">
        </div>
        <div class="col-12 mt-3 input_blocks">
            <input id="card_number" class='pi_inputs field' placeholder="Card number" type="tel">
        </div>
        <div class="col-12 mt-3 input_blocks input_blocks_end">
            <input id="expiration_date" class='pi_inputs field' type="tel" placeholder="Expiration date">
        </div>
        <div class="col-12 mt-3 input_blocks input_blocks_end">
            <input id="cvv"  class='pi_inputs field' type="password" placeholder="CVV">
        </div>
        <div class="col-12 mt-3 error_description">
            <span style="display:none"></span>
        </div>
    </div>
</div>
<div id="wrapper_loader"><div class="lds-dual-ring"></div></div>
<div id="wrapper_congrats" class="container-fluid">
    <div class="row ">
        <div id="congrats_image_row" class="col-8 offset-2">
            <img id="congrats_image" src="/public/images/Pablo.png" class="img-fluid" alt="Responsive image"></div>
        <div class="col-12  congrats_header justify-content-center"><h4>Congratulations</h4></div>
        <div class="col-12 p-3 congrats_text justify-content-center"><h3></h3></div>
    </div>
</div>
<input type="hidden" id="user_identificator" name="user_identificator" value="<?php echo $user['id'] ?>">
<input type="hidden" id="item_identificator" name="item_identificator" value="<?php echo $item['item_id'] ?>">
<input type="hidden" id="type" name="type_identificator" value="<?php echo $page_name ?>">
<input type="hidden" id="app_identificator" name="app_identificator" value="<?php echo $clear_data['app_id'] ?>">
<input type="hidden" id="device_identificator" name="device_identificator" value="<?php echo $clear_data['device_id'] ?>">
<input type="hidden" id="access_token" name="access_token" value="<?php echo $clear_data['access_token'] ?>">
<input type="hidden" id="total_art_cost" value="<?php if ($page_name === 'digital') {
    echo $item['digitalPrice'];
} else {
    echo $item['physicalPrice'];
}
?>">
<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
        crossorigin="anonymous"></script>
<script src="/public/js/jquery.maskedinput.js"></script>
<script src="/public/js/Checkout.js"></script>
<script src="/public/js/Validator.js"></script>
<script src="/public/js/main.js"></script>
</body>
</html>