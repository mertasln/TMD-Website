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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <!-- CSS Vendor end -->
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; }
        .login-card { border-radius: 16px; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2); }
        .btn-primary-custom { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none; font-weight: 600; border-radius: 10px; }
        .btn-primary-custom:hover { background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%); }
    </style>

    <!-- CSS start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/pace.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/jquery-scrollbar.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/select2.min.css">
    <!-- CSS end -->

</head>
<body>

<main class="admin-main d-flex align-items-center justify-content-center min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card bg-white p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <div class="h2 font-weight-bold text-primary mb-2"><i class="fa fa-store mr-2"></i>MSK STORES</div>
                        <h4 class="font-weight-bold text-dark">Beheerderspaneel</h4>
                        <p class="text-muted small">Meld u aan om toegang te krijgen tot het dashboard</p>
                    </div>

                    <form class="needs-validation" method="post">
                        <div class="form-group mb-3">
                            <label for="username" class="font-weight-medium text-dark">Gebruikersnaam</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-user text-muted"></i></span>
                                </div>
                                <input type="text" required class="form-control border-left-0" placeholder="Vul uw gebruikersnaam in" name="username" id="username">
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="password" class="font-weight-medium text-dark">Wachtwoord</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-lock text-muted"></i></span>
                                </div>
                                <input type="password" name="password" placeholder="Vul uw wachtwoord in" required class="form-control border-left-0" id="password">
                            </div>
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary-custom text-white btn-block btn-lg py-3 shadow-sm mb-3">
                            <i class="fa fa-sign-in-alt mr-2"></i> Inloggen
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <small class="text-muted">&copy; <?php echo date("Y"); ?> MSK Stores. Alle rechten voorbehouden.</small>
                    </div>
                </div>
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