<?php if ($page_name === 'digital') {
    if ($purchased) {
        $price = 'PURCHASED';
    } else if ($item['digitalPrice'] === 'FREE') {
        $price = $item['digitalPrice'];
    } else {
        $price = '$' . $item['digitalPrice'];
    }

} else {
    $price = '$' . $item['physicalPrice'];
}

?>

<div id="wrapper" class="container-fluid">
    <div class="row no-gutters">
        <div class="col-5 img_holder">
            <img src="<?php echo $item['img_preview'] ?>" style="width:100%">
        </div>
        <div class=" col-7 pl-3 art_header">
            <h4 class="art_name"><?php echo $item['artName'] ?></h4>
            <h4 class="artist_name"><?php echo $item['artistName'] ?></h4>
            <h4 class="art_cost"><span
                        style="display:inline-block;width:49%; text-align: left"><?php echo ucfirst($page_name)?> copy</span> <span
                        style="display:inline-block;width:49%; text-align: right"><?php echo $price?></span></h4>
        </div>
    </div>


    <div class="row ">
        <div id="min_height_block">
