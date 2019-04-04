<div class="col-12">
    <h4 class="block_name">Shipping info</h4>
</div>
<div id='shipping_info_block' class="col-12  blocks">
    <h6 class="info"><span class="zip_code"><?php if ($user['zip_code']!="") {echo $user['zip_code'];} else {echo '<span class="gray">Zip code</span>';}?></span></h6>
    <h6 class="info"><span class="country"><?php if ($user['country']!="") {echo $user['country'];} else {echo '<span class="gray">Country</span>';}?></span></h6>
    <h6 class="info"><span class="city"><?php if ($user['city']!="") {echo $user['city'];} else {echo '<span class="gray">City</span>';}?></span></h6>
    <h6 class="info"><span class="address_line_one"><?php if ($user['adress']!="") {echo $user['adress'];} else {echo '<span class="gray">Adress line 1</span>';}?></span></h6>
    <h6 class="info"><span class="address_line_two"><?php if ($user['appartment']!="") {echo $user['appartment'];} else {echo '<span class="gray">Adress line 2</span>';}?></span></h6>
    <img class="right_icon2" src="/public/images/right.png">

</div>