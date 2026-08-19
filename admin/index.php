<?php

require_once("../assets/config/config.php");

// Fetch KPI statistics
$total_orders = 0;
$total_revenue = 0.00;
$total_products = 0;
$low_stock_count = 0;
$total_categories = 0;

if ($con) {
    // Orders & Revenue
    $res = mysqli_query($con, "SELECT COUNT(*) AS total_orders, IFNULL(SUM(totalprice), 0) AS total_revenue FROM orders");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $total_orders = (int)$row["total_orders"];
        $total_revenue = (float)$row["total_revenue"];
    }

    // Products
    $res = mysqli_query($con, "SELECT COUNT(*) AS total_products FROM products WHERE view = 1");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $total_products = (int)$row["total_products"];
    }

    // Low stock products (stock <= 5)
    $res = mysqli_query($con, "SELECT COUNT(*) AS low_stock FROM products WHERE view = 1 AND stock <= 5");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $low_stock_count = (int)$row["low_stock"];
    }

    // Categories
    $res = mysqli_query($con, "SELECT COUNT(*) AS total_cats FROM product_categories WHERE view = 1");
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $total_categories = (int)$row["total_cats"];
    }
}

// Header buttons
$header_btns = '
    <a href="./products.php?add" class="btn btn-primary mr-2 shadow-sm mb-2"><i class="fa fa-plus-circle mr-1"></i> Nieuw Product</a>
    <a href="./categories.php?addCategory" class="btn btn-outline-light mr-2 shadow-sm mb-2"><i class="fa fa-folder-plus mr-1"></i> Nieuwe Categorie</a>
    <a href="../" target="_blank" class="btn btn-dark shadow-sm mb-2"><i class="fa fa-external-link-alt mr-1"></i> Bekijk Webshop</a>
';

// Recent Orders HTML
$recent_orders_html = "";
if ($con) {
    $res = mysqli_query($con, "SELECT id, timestamp_creation, name, company_name, totalprice FROM orders ORDER BY id DESC LIMIT 5");
    if ($res && mysqli_num_rows($res) > 0) {
        while ($order = mysqli_fetch_assoc($res)) {
            $customer = htmlspecialchars($order["name"]);
            if (!empty($order["company_name"])) {
                $customer .= " <small class='text-muted'>(" . htmlspecialchars($order["company_name"]) . ")</small>";
            }
            $formatted_price = "€ " . number_format((float)$order["totalprice"], 2, ",", ".");
            $date = htmlspecialchars($order["timestamp_creation"]);

            $recent_orders_html .= "
                <tr>
                    <td class='font-weight-bold'>#" . (int)$order["id"] . "</td>
                    <td>{$date}</td>
                    <td>{$customer}</td>
                    <td class='font-weight-bold text-success'>{$formatted_price}</td>
                    <td>
                        <a href='./orders.php?view=" . (int)$order["id"] . "' class='btn btn-sm btn-outline-primary'>
                            <i class='fa fa-eye'></i> Bekijken
                        </a>
                    </td>
                </tr>";
        }
    } else {
        $recent_orders_html = "<tr><td colspan='5' class='text-center text-muted py-4'>Geen bestellingen gevonden.</td></tr>";
    }
} else {
    $recent_orders_html = "<tr><td colspan='5' class='text-center text-muted py-4'>Kan geen verbinding maken met de database.</td></tr>";
}

// Low Stock Items HTML
$low_stock_html = "";
if ($con) {
    $res = mysqli_query($con, "SELECT id, title, code, stock, price FROM products WHERE view = 1 AND stock <= 5 ORDER BY stock ASC LIMIT 5");
    if ($res && mysqli_num_rows($res) > 0) {
        while ($prod = mysqli_fetch_assoc($res)) {
            $title = htmlspecialchars($prod["title"]);
            $code = htmlspecialchars($prod["code"] ?: "-");
            $stock = (float)$prod["stock"];
            $badge_class = $stock == 0 ? "badge-danger" : "badge-warning";
            $stock_text = $stock == 0 ? "Uit voorraad (0)" : "Lage voorraad ({$stock})";

            $low_stock_html .= "
                <tr>
                    <td>
                        <div class='font-weight-bold'>{$title}</div>
                        <small class='text-muted'>Code: {$code}</small>
                    </td>
                    <td><span class='badge {$badge_class} p-2'>{$stock_text}</span></td>
                    <td>
                        <a href='./products.php?edit=" . (int)$prod["id"] . "' class='btn btn-sm btn-light border'>
                            <i class='fa fa-edit'></i> Aanpassen
                        </a>
                    </td>
                </tr>";
        }
    } else {
        $low_stock_html = "<tr><td colspan='3' class='text-center text-muted py-4'><i class='fa fa-check-circle text-success mr-1'></i> Alle producten zijn voldoende op voorraad!</td></tr>";
    }
}

// Build $cont content
$cont = '
<div class="col-12 mb-4">
    <div class="row">
        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 dash-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted font-weight-bold small">Totale Omzet</div>
                            <div class="h3 font-weight-bold mb-0 text-primary mt-1">€ ' . number_format($total_revenue, 2, ",", ".") . '</div>
                        </div>
                        <div class="dash-icon-box bg-primary-soft text-primary">
                            <i class="fa fa-euro-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 dash-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted font-weight-bold small">Bestellingen</div>
                            <div class="h3 font-weight-bold mb-0 text-dark mt-1">' . $total_orders . '</div>
                        </div>
                        <div class="dash-icon-box bg-info-soft text-info">
                            <i class="fa fa-shopping-bag fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 dash-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted font-weight-bold small">Actieve Producten</div>
                            <div class="h3 font-weight-bold mb-0 text-dark mt-1">' . $total_products . '</div>
                        </div>
                        <div class="dash-icon-box bg-success-soft text-success">
                            <i class="fa fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock / Categories Card -->
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 dash-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted font-weight-bold small">Lage Voorraad Alerts</div>
                            <div class="h3 font-weight-bold mb-0 ' . ($low_stock_count > 0 ? 'text-danger' : 'text-success') . ' mt-1">' . $low_stock_count . '</div>
                        </div>
                        <div class="dash-icon-box ' . ($low_stock_count > 0 ? 'bg-danger-soft text-danger' : 'bg-warning-soft text-warning') . '">
                            <i class="fa fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Section: Recent Orders & Stock Alerts -->
<div class="col-lg-8 mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
            <h5 class="m-0 font-weight-bold text-dark"><i class="fa fa-receipt text-primary mr-2"></i>Laatste Bestellingen</h5>
            <a href="./orders.php" class="btn btn-sm btn-outline-primary">Bekijk Alle Bestellingen</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Order #</th>
                            <th>Datum</th>
                            <th>Klant</th>
                            <th>Bedrag</th>
                            <th>Actie</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $recent_orders_html . '
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-4 mb-4">
    <!-- Low Stock Alert Box -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="m-0 font-weight-bold text-dark"><i class="fa fa-boxes text-warning mr-2"></i>Voorraad Waarschuwingen</h5>
            <a href="./products.php" class="btn btn-sm btn-light">Beheer</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0">
                    <tbody>
                        ' . $low_stock_html . '
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="m-0 font-weight-bold text-dark"><i class="fa fa-bolt text-warning mr-2"></i>Snelkoppelingen</h5>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                <a href="./products.php?add" class="list-group-item list-group-item-action d-flex align-items-center px-0">
                    <div class="icon-shape bg-primary-soft text-primary rounded-circle mr-3 p-2">
                        <i class="fa fa-plus"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold text-dark">Nieuw product toevoegen</div>
                        <small class="text-muted">Voeg eenvoudig nieuwe items toe aan uw catalogus</small>
                    </div>
                </a>
                <a href="./categories.php" class="list-group-item list-group-item-action d-flex align-items-center px-0">
                    <div class="icon-shape bg-info-soft text-info rounded-circle mr-3 p-2">
                        <i class="fa fa-tags"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold text-dark">Categorieën beheren</div>
                        <small class="text-muted">Beheer hoofdcategorieën en subcategorieën</small>
                    </div>
                </a>
                <a href="./orders.php" class="list-group-item list-group-item-action d-flex align-items-center px-0">
                    <div class="icon-shape bg-success-soft text-success rounded-circle mr-3 p-2">
                        <i class="fa fa-truck"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold text-dark">Bestellingen verwerken</div>
                        <small class="text-muted">Bekijk en verwerk binnenkomende orders</small>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
';

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
    <title>Dashboard - MSK Stores Admin</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon.png"/>
    <link rel="icon" type="image/png" href="./assets/img/favicon.png" sizes="16x16">

    <!-- CSS Vendor start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/atmos.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <!-- CSS Vendor end -->

    <!-- CSS start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/pace.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/jquery-scrollbar.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/select2.min.css">
    <!-- CSS end -->

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f4f6f9;
        }
        .bg-primary-soft { background-color: rgba(13, 110, 253, 0.12); }
        .bg-info-soft { background-color: rgba(13, 202, 240, 0.12); }
        .bg-success-soft { background-color: rgba(25, 135, 84, 0.12); }
        .bg-warning-soft { background-color: rgba(255, 193, 7, 0.15); }
        .bg-danger-soft { background-color: rgba(220, 53, 69, 0.12); }
        .dash-stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 12px;
        }
        .dash-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
        }
        .dash-icon-box {
            width: 54px;
            height: 54px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border-radius: 12px;
        }
        .icon-shape {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
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
