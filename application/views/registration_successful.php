<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>REGISTRATION COMPLETED</title>
    <link rel="stylesheet" href="/public/css/font.css" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="/public/css/letters.css" type="text/css">
</head>
<body>
<div id="wrapper" class="container-fluid">

    <div class="row no-gutters">
        <div  id='image_holder' class="offset-1 col-10 pb-1 ">
            <img src="/public/images/BG_md.png" alt="congratulations-screen" class="img-fluid">
        </div>
        <div id='congratulations' class="col-12 pb-5 pt-5">
            <?php echo $header ?>
        </div>
        <div class="congratulations_text offset-1 col-10">
            <?php echo $message ?>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>

</body>
</html>