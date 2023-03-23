<?php

require_once("../assets/config/config.php");

if(isset($_GET["view"])){
    $header_btns .= 
        '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-chevron-left m-r-5"></i> Terug</a>';
    
    $ido = $_GET["view"];

    $sql = 
        "SELECT orders.name, orders.timestamp_creation, orders.totalprice, orders.name, orders.company_name, orders.email, orders.phone, orders.address, orders.postal_code, orders.city, orders.notes
        FROM orders
        WHERE orders.id = '$ido';";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);

    $timestamp_creation = $row["timestamp_creation"];
    $totalprice = $row["totalprice"];
    $name = $row["name"];
    $company_name = $row["company_name"];
    if(!empty($company_name)){
        $name .= " | ".$company_name;
    }
    $email = $row["email"];
    $phone = $row["phone"];
    $address = $row["address"].", ".$row["postal_code"]." ".$row["city"];
    $notes = $row["notes"];

    $sql = 
        "SELECT order_products.title, order_products.price, order_products.qty
        FROM order_products
        WHERE order_products.ido = '$ido';";
    $res = mysqli_query($con, $sql);
    $product_rows = "";
    $totalprice_products = 0;
    while($row = mysqli_fetch_assoc($res)){
        $subprice = $row["price"] * $row["qty"];
        $totalprice_products += $subprice;
        $product_rows .= 
            '<tr>
                <td>'.$row["title"].'</td>
                <td>€'.number_format($row["price"], 2, ",", ".").'</td>
                <td>'.$row["qty"].'x</td>
                <td>€'.number_format($subprice, 2, ",", ".").'</td>
            </tr>';
    }

    $cont .= 
        '<div class="col-12">
            <div class="card pt-2 pb-2 mb-5">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-12">
                            <h2>Bekijken</h2>
                        </div>
                        <div class="form-group col-5 col-md-2">
                            <label for="id">Ordernummer</label>
                            <input type="text" class="form-control" id="id" value="'.$ido.'" readonly>
                        </div>
                        <div class="form-group col-7 col-md-6">
                            <label for="timestamp_creation">Aanmaakdatum</label>
                            <input type="text" class="form-control" id="timestamp_creation" value="'.$timestamp_creation.'" readonly>
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label for="totalprice">Totaalbedrag</label>
                            <input type="text" class="form-control" id="totalprice" value="'.$totalprice.'" readonly>
                        </div>
                        <div class="form-group col-12 col-lg-6">
                            <label for="name">Naam</label>
                            <input type="text" class="form-control" id="name" value="'.$name.'" readonly>
                        </div>
                        <div class="form-group col-12 col-lg-6">
                            <label for="address">Adres</label>
                            <input type="text" class="form-control" id="address" value="'.$address.'" readonly>
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="email">E-mailadres</label>
                            <input type="email" class="form-control mb-2" id="email" value="'.$email.'" readonly>
                            <label for="phone">Telefoonnummer</label>
                            <input type="text" class="form-control" id="phone" value="'.$phone.'" readonly>
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label for="notes">Opmerkingen</label>
                            <textarea class="form-control" id="notes" style="height:108px;" readonly>'.$notes.'</textarea>
                        </div>
                        <div class="form-group col-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th colspan="4">Bestelde producten</th>
                                        </tr>
                                        <tr>
                                            <th>Titel</th>
                                            <th>Prijs</th>
                                            <th>Aantal</th>
                                            <th>Totaalprijs</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        '.$product_rows.'
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">Totaal: </th>
                                            <th>€'.number_format($totalprice_products, 2, ",", ".").'</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
}else{
    $cont .= 
        '<div class="col-12">
            <div class="card pt-2 pb-2 mb-5">
                <div class="card-body table-responsive">
                    <table class="table table-hover" id="tableOrders">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Aanmaakdatum</th>
                                <th>Naam</th>
                                <th>Adres</th>
                                <th>Bedrag</th>
                                <th>Actie</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>';
    
    $js .= 
        "var table = $('#tableOrders').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Dutch.json'
            },
            order: [[0, 'desc']],
            stateSave: true,
            columnDefs: [{
                orderable: false, 
                targets: [5] 
            }],
            columns: [ 
                { data: 'id' },
                { data: 'timestamp_creation' },
                { data: 'name' },
                { data: 'address' },
                { data: 'totalprice' },
                { data: 'btns' }
            ],
            lengthMenu: [
                [5, 10, 25, 50, 100, 500, 1000, 10000],
                [5, 10, 25, 50, 100, 500, 1000, 'Alles']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: './assets/php/ajax.php?request=getOrders',
                type: 'POST'
            }
        });";

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
    <title>Orders - MSK Stores</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon.png"/>
    <link rel="icon" type="image/png" href="./assets/img/favicon.png" sizes="16x16">

    <!-- CSS Vendor start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/atmos.min.css">                           <!-- Bootstrap + Admin CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,500,600">       <!--Google Font-->
    <!-- CSS Vendor end -->

    <!-- CSS start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/dataTables-bootstrap4.min.css">
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
    <script src="./assets/js/dataTables.min.js"></script>
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