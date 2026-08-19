<?php
if (!isset($con)) {
    require_once(__DIR__ . "/../assets/config/config.php");
}

// Get Cart items & Total
$cart_count = 0;
$cart_total = 0.00;
$cart_items_html = "";

if (isset($_SESSION["cart"]) && is_array($_SESSION["cart"]) && count($_SESSION["cart"]) > 0) {
    $cart_count = count($_SESSION["cart"]);
    $cart_ids = array_map('intval', $_SESSION["cart"]);
    $in_clause = implode(",", $cart_ids);

    if (!empty($in_clause) && $con) {
        $res = mysqli_query($con, "SELECT id, title, price FROM products WHERE id IN ($in_clause) AND view = 1");
        if ($res) {
            while ($item = mysqli_fetch_assoc($res)) {
                $item_id = (int)$item["id"];
                $item_title = htmlspecialchars($item["title"]);
                $item_price = (float)$item["price"];
                $cart_total += $item_price;
                $formatted_item_price = "€ " . number_format($item_price, 2, ",", ".");

                // Image check
                $img_src = "assets/images/products/fallback/fallback.png";
                $img_dir = __DIR__ . "/assets/images/products/" . $item_id . "/0/SD/";
                if (file_exists($img_dir)) {
                    $imgs = array_values(array_diff(scandir($img_dir), [".", ".."]));
                    if (!empty($imgs)) {
                        $img_src = "assets/images/products/" . $item_id . "/0/SD/" . $imgs[0];
                    }
                }

                $cart_items_html .= '
                    <div class="product">
                        <div class="product-cart-details">
                            <h4 class="product-title">
                                <a href="products/' . dclean($item_title) . '">' . $item_title . '</a>
                            </h4>
                            <span class="cart-product-info">
                                <span class="cart-product-qty">1</span> x ' . $formatted_item_price . '
                            </span>
                        </div>
                        <figure class="product-image-container">
                            <a href="products/' . dclean($item_title) . '" class="product-image">
                                <img src="' . $img_src . '" alt="' . $item_title . '" style="max-height:60px; object-fit:contain;">
                            </a>
                        </figure>
                        <a href="javascript:removeFromCart(' . $item_id . ');" class="btn-remove" title="Verwijderen"><i class="icon-close"></i></a>
                    </div>';
            }
        }
    }
}

if (empty($cart_items_html)) {
    $cart_items_html = '<div class="p-3 text-center text-muted">Uw winkelwagen is leeg.</div>';
}

$formatted_cart_total = "€ " . number_format($cart_total, 2, ",", ".");

// Fetch dynamic Categories & Subcategories for navigation
$categories_nav_html = "";
if ($con) {
    $cat_res = mysqli_query($con, "SELECT id, name FROM product_categories WHERE view = 1 AND visible = 1 ORDER BY name ASC");
    if ($cat_res) {
        while ($cat = mysqli_fetch_assoc($cat_res)) {
            $cat_id = (int)$cat["id"];
            $cat_name = htmlspecialchars($cat["name"]);
            $cat_slug = dclean($cat["name"]);

            // Subcategories
            $sub_res = mysqli_query($con, "SELECT id, name FROM product_subcategories WHERE idpc = $cat_id AND view = 1 AND visible = 1 ORDER BY name ASC");
            $sub_html = "";
            if ($sub_res && mysqli_num_rows($sub_res) > 0) {
                while ($sub = mysqli_fetch_assoc($sub_res)) {
                    $sub_name = htmlspecialchars($sub["name"]);
                    $sub_slug = dclean($sub["name"]);
                    $sub_html .= '<li><a href="shop/' . $cat_slug . '/' . $sub_slug . '">' . $sub_name . '</a></li>';
                }
            }

            if (!empty($sub_html)) {
                $categories_nav_html .= '
                    <li>
                        <a href="shop/' . $cat_slug . '" class="sf-with-ul">' . $cat_name . '</a>
                        <ul>' . $sub_html . '</ul>
                    </li>';
            } else {
                $categories_nav_html .= '<li><a href="shop/' . $cat_slug . '">' . $cat_name . '</a></li>';
            }
        }
    }
}
?>

<div class="header sticky-header shadow-sm">
    <div class="header-left">
        <button class="mobile-menu-toggler">
            <span class="sr-only">Menu openen</span>
            <i class="icon-bars"></i>
        </button>
        
        <div class="header-search header-search-extended header-search-visible d-none d-lg-block">
            <form action="shop/" method="get">
                <div class="header-search-wrapper search-wrapper-wide">
                    <label for="q" class="sr-only">Zoeken</label>
                    <input type="search" class="form-control" name="q" id="q" placeholder="Zoek in ons assortiment..." required>
                    <button class="search-toggle" type="submit"><i class="icon-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="header-right">
        <div class="account">
            <a href="admin/" title="Beheerderspaneel" class="d-flex align-items-center">
                <i class="icon-user"></i>
                <span class="ml-1 d-none d-md-inline small font-weight-bold">Admin</span>
            </a>
        </div>

        <div class="dropdown cart-dropdown">
            <div class="dropdown-link">
                <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
                    <i class="icon-shopping-cart"></i>
                    <span class="cart-count badge bg-primary text-white"><?php echo $cart_count; ?></span>
                </a>
            </div>

            <div class="dropdown-menu dropdown-menu-right shadow border-0">
                <div class="dropdown-cart-products">
                    <?php echo $cart_items_html; ?>
                </div>

                <div class="dropdown-cart-total border-top pt-2">
                    <span>Totaal</span>
                    <span class="cart-total-price text-primary font-weight-bold"><?php echo $formatted_cart_total; ?></span>
                </div>

                <div class="dropdown-cart-action">
                    <a href="cart.html" class="btn btn-primary">Winkelwagen</a>
                    <a href="checkout.html" class="btn btn-outline-primary-2"><span>Afrekenen</span><i class="icon-long-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <p class="price font-weight-bold ml-2 text-primary mb-0"><?php echo $formatted_cart_total; ?></p>
    </div>
</div>

<div class="side-div">
    <div class="header sidebar border-right">
        <div class="header-top py-3 text-center border-bottom">
            <a href="index.php" class="logo text-decoration-none">
                <span class="h3 font-weight-bold text-primary mb-0"><i class="fa fa-store mr-2"></i>MSK STORES</span>
            </a>
        </div>
        <div class="header-main">
            <nav class="main-nav">
                <ul class="menu sf-arrows">
                    <li class="active">
                        <a href="index.php">Home</a>
                    </li>
                    <?php echo $categories_nav_html; ?>
                    <li>
                        <a href="about.html">Over Ons</a>
                    </li>
                    <li>
                        <a href="contact.html">Contact</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="mobile-menu-overlay"></div>
<div class="mobile-menu-container mobile-menu-light">
    <div class="mobile-menu-wrapper">
        <span class="mobile-menu-close"><i class="icon-close"></i></span>

        <form action="shop/" method="get" class="mobile-search">
            <label for="mobile-search" class="sr-only">Zoeken</label>
            <input type="search" class="form-control" name="q" id="mobile-search" placeholder="Zoek in MSK Stores..." required>
            <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
        </form>

        <nav class="mobile-nav">
            <ul class="mobile-menu">
                <li class="active">
                    <a href="index.php">Home</a>
                </li>
                <?php echo $categories_nav_html; ?>
                <li>
                    <a href="about.html">Over Ons</a>
                </li>
                <li>
                    <a href="contact.html">Contact</a>
                </li>
            </ul>
        </nav>

        <div class="social-icons mt-4">
            <a href="#" class="social-icon" target="_blank" title="Facebook"><i class="icon-facebook-f"></i></a>
            <a href="#" class="social-icon" target="_blank" title="Instagram"><i class="icon-instagram"></i></a>
        </div>
    </div>
</div>
