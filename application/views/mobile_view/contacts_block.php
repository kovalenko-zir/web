<div class="col-12">
    <h4 class="block_name">Contact info</h4>
</div>
<div id='contact_info_block' class="col-12 blocks">
    <h6 class="info"><span class="fir_name"><?php if ($user['user_name'] != "") {
                echo $user['user_name'];
            } else {
                echo 'First name';
            } ?> </span>




        <span class="la_name"><?php if ($user['user_surname'] != "") {
                echo $user['user_surname'];
            } else {
                echo 'Last name';
            } ?></span></h6>
    <h6 class="info"><span class="email"><?php if ($user['email'] != "") {
                echo $user['email'];
            } else {
                echo 'Email';
            } ?></span></h6>
    <img class="right_icon1" src="/public/images/right.png">
</div>