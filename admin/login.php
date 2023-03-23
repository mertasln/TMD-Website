<?php

require_once("../assets/config/config.php");

if(isset($_POST["submit"])){
    // ADMIN LOGIN
    $sql = 
        "SELECT users.id, users.username, users.password
        FROM users;";
    $res = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        if($row["username"] == $_POST["username"] && $row["password"] == finic_hash($_POST["password"])){
            $_SESSION["user_id"] = $row["id"];
            header("Location: ./");
        }
    }
}elseif(isset($_GET["logout"])){
    unset($_SESSION["user_id"]);
}elseif(isset($_SESSION["user_id"])){
    header("Location: ./");
}

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>Login - MSK Stores</title>
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
<body class="jumbo-page">

<main class="admin-main  ">
    <div class="container-fluid">
        <div class="row ">
            <div class="col-lg-4  bg-white">
                <div class="row align-items-center m-h-100">
                    <div class="mx-auto col-md-8">
                        <div class="p-b-20 text-center">
                            <p>
                                <img src="./assets/img/logo.png" width="240" alt="Logo MSK Stores">
                            </p>
                            <p class="admin-brand-content">
                            </p>
                        </div>
                        <h3 class="text-center p-b-20 fw-400">Inloggen</h3>
                        <form class="needs-validation" method="post">
                            <div class="form-row">
                                <div class="form-group floating-label col-md-12">
                                    <label for="username">Uw gebruikersnaam</label>
                                    <input type="text" required class="form-control" placeholder="Uw gebruikersnaam" name="username" id="username">
                                </div>
                                <div class="form-group floating-label col-md-12">
                                    <label for="password">Uw wachtwoord</label>
                                    <input type="password" name="password" placeholder="Uw wachtwoord" required class="form-control " id="password">
                                </div>
                            </div>

                            <button type="submit" name="submit" class="btn btn-danger btn-block btn-lg">Inloggen</button>

                        </form>
                    </div>

                </div>
            </div>
            <div class="col-lg-8 d-none d-md-block bg-cover" style="background-image: url('./assets/img/bg-login.jpg');">

            </div>
        </div>
    </div>
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