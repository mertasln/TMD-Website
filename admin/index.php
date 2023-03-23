<?php

require_once("../assets/config/config.php");

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>Home - MSK Stores</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon.png"/>
    <link rel="icon" type="image/png" href="./assets/img/favicon.png" sizes="16x16">

    <!-- CSS Vendor start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/atmos.min.css">                           <!-- Bootstrap + Admin CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,500,600">       <!--Google Font-->
    <!-- CSS Vendor end -->

    <!-- CSS start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/pace.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/jquery-scrollbar.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/select2.min.css">
    <!-- CSS end -->

</head>
<body>
    <?php require_once("./header.php");?>
    <main>
        <section class="admin-content"> 
            <div class="container-fluid pull-up">
                <div class="row">
                    <?php echo $cont;?>
                </div>
            </div>
        </section>
    </main>

    <!-- JS Vendor Start -->
    <script src="./assets/js/jquery.min.js"></script>
    <script src="./assets/js/jquery-ui.min.js"></script>
    <script src="./assets/js/bootstrap-bundle.min.js"></script>
    <!-- JS Vendor End -->

    <!-- JS start -->
    <script src="./assets/js/pace.min.js"></script>
    <script src="./assets/js/jquery-scrollbar.min.js"></script>
    <script src="./assets/js/daterangepicker.js"></script>
    <script src="./assets/js/select2-full.min.js"></script>
    <script src="./assets/js/listjs.min.js"></script>
    <script src="./assets/js/atmos.min.js"></script>
    <script src="https://kit.fontawesome.com/f3f57d50bc.js" crossorigin="anonymous"></script>
    <!-- JS end -->

    <!-- Additional JS commands by PHP start -->
    <?php require_once("./assets/php/main.php");?>
    <!-- Additional JS commands by PHP end -->
</body>
</html>