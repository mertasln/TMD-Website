<?php
require_once(__DIR__ . "/../assets/config/config.php");

// Helper function to render a product card dynamically
function render_product_card($prod) {
    $id = (int)$prod["id"];
    $title = htmlspecialchars($prod["title"]);
    $price = "€ " . number_format((float)$prod["price"], 2, ",", ".");
    $code = htmlspecialchars($prod["code"] ?: "");

    // Image resolution
    $img_src = "assets/images/products/fallback/fallback.png";
    $img_dir = __DIR__ . "/assets/images/products/" . $id . "/0/SD/";
    if (file_exists($img_dir)) {
        $imgs = array_values(array_diff(scandir($img_dir), [".", ".."]));
        if (!empty($imgs)) {
            $img_src = "assets/images/products/" . $id . "/0/SD/" . $imgs[0];
        }
    }

    $in_cart = isset($_SESSION["cart"]) && in_array($id, $_SESSION["cart"]);
    $cart_btn_text = $in_cart ? 'In Winkelwagen' : 'Voeg Toe';
    $cart_btn_class = $in_cart ? 'btn-success text-white' : 'btn-cart';

    return '
    <div class="col-6 col-md-4 col-lg-3 mb-4">
        <div class="product product-9 text-center border rounded p-2 h-100 d-flex flex-column justify-content-between shadow-sm bg-white">
            <figure class="product-media bg-light rounded p-2 mb-2 d-flex align-items-center justify-content-center" style="height:200px; overflow:hidden;">
                <a href="products/' . dclean($title) . '">
                    <img src="' . $img_src . '" alt="' . $title . '" class="product-image img-fluid" style="max-height:180px; object-fit:contain;">
                </a>
            </figure>

            <div class="product-body d-flex flex-column flex-grow-1 justify-content-between">
                <div>
                    <div class="product-cat text-uppercase text-muted small mb-1">
                        ' . ($code ? 'Code: ' . $code : 'MSK STORES') . '
                    </div>
                    <h3 class="product-title font-weight-bold h6 mb-2">
                        <a href="products/' . dclean($title) . '" class="text-dark">' . $title . '</a>
                    </h3>
                </div>

                <div>
                    <div class="product-price font-weight-bold text-primary h5 my-2">
                        ' . $price . '
                    </div>
                    <div class="product-action my-1">
                        <a href="javascript:addToCart(' . $id . ');" class="btn btn-sm btn-outline-primary w-100 rounded-pill font-weight-bold ' . $cart_btn_class . '">
                            <i class="icon-shopping-cart mr-1"></i> <span>' . $cart_btn_text . '</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

// Fetch Featured / Dynamic products from database
$featured_products_html = "";
if ($con) {
    $res = mysqli_query($con, "SELECT id, title, code, price, stock FROM products WHERE view = 1 ORDER BY id DESC LIMIT 8");
    if ($res && mysqli_num_rows($res) > 0) {
        while ($prod = mysqli_fetch_assoc($res)) {
            $featured_products_html .= render_product_card($prod);
        }
    } else {
        $featured_products_html = '<div class="col-12 text-center py-5 text-muted"><i class="fa fa-info-circle mr-2"></i> Binnenkort nieuwe producten beschikbaar in onze shop!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
	<title>MSK STORES - Exclusieve Webshop</title>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Welkom bij MSK STORES. Uw partner voor hoogwaardige producten.">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/icons/favicon.ico">
    <meta name="theme-color" content="#ffffff">

    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/owl-carousel/owl.carousel.css">
    <link rel="stylesheet" href="assets/css/plugins/magnific-popup/magnific-popup.css">
    <!-- Main CSS File -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/skins/skin-demo-23.css">
    <link rel="stylesheet" href="assets/css/demos/demo-23.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
	<div class="page-wrapper">
		<div class="container page-container">

			<?php require_once("./header.php"); ?>

			<div class="page-content-div">
				<main class="main">
					<!-- Hero Banner Section -->
					<section class="hero-banner bg-gradient-dark text-white p-5 rounded my-4 shadow-sm" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                        <div class="row align-items-center py-4">
                            <div class="col-lg-8">
                                <span class="badge badge-primary px-3 py-2 text-uppercase mb-3 font-weight-bold">MSK STORES Webshop</span>
                                <h1 class="display-4 font-weight-bold text-white mb-3">Ontdek Onze Nieuwste Collectie</h1>
                                <p class="lead text-white-50 mb-4">Kwaliteit en uitstekende service. Bekijk ons gevarieerde assortiment en bestel eenvoudig online.</p>
                                <a href="shop/" class="btn btn-primary btn-lg rounded-pill px-4 shadow">
                                    <i class="fa fa-shopping-bag mr-2"></i> Shop Nu
                                </a>
                            </div>
                        </div>
					</section>

		            <!-- Featured Products Section -->
		            <section class="my-5">
				<div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
					<div>
                                <span class="text-uppercase text-muted font-weight-bold small">Populaire Artikelen</span>
                                <h2 class="h3 font-weight-bold text-dark m-0">Nieuwe & Uitgelichte Producten</h2>
                            </div>
                            <a href="shop/" class="btn btn-outline-primary rounded-pill btn-sm font-weight-bold px-3">
                                Bekijk Alle Producten <i class="fa fa-arrow-right ml-1"></i>
                            </a>
				</div>

                        <div class="row">
                            <?php echo $featured_products_html; ?>
                        </div>
		            </section>

                    <!-- Features Highlights -->
                    <section class="my-5 py-4 bg-light rounded shadow-sm">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="p-3">
                                    <i class="fa fa-truck fa-2x text-primary mb-3"></i>
                                    <h5 class="font-weight-bold text-dark">Snelle Levering</h5>
                                    <p class="text-muted small mb-0">Zorgvuldige verzending van al uw bestellingen</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="p-3">
                                    <i class="fa fa-shield-alt fa-2x text-primary mb-3"></i>
                                    <h5 class="font-weight-bold text-dark">Veilig Betalen</h5>
                                    <p class="text-muted small mb-0">Betrouwbare transacties en gegevensbescherming</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fa fa-headset fa-2x text-primary mb-3"></i>
                                    <h5 class="font-weight-bold text-dark">Uitstekende Service</h5>
                                    <p class="text-muted small mb-0">Onze klantenservice staat altijd voor u klaar</p>
                                </div>
                            </div>
                        </div>
                    </section>
				</main>

				<?php require_once("./footer.php"); ?>
			</div>
		</div>
	</div>

    <!-- Plugins JS File -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.hoverIntent.min.js"></script>
    <script src="assets/js/jquery.waypoints.min.js"></script>
    <script src="assets/js/superfish.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/bootstrap-input-spinner.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

    <script>
        function addToCart(id) {
            $.ajax({
                url: 'assets/php/ajax.php',
                type: 'post',
                data: { addToCart: id },
                success: function(response) {
                    window.location.reload();
                }
            });
        }

        function removeFromCart(id) {
            $.ajax({
                url: 'assets/php/ajax.php',
                type: 'post',
                data: { removeFromCart: id },
                success: function(response) {
                    window.location.reload();
                }
            });
        }
    </script>
</body>
</html>
