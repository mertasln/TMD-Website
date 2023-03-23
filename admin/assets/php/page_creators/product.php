<?php

function createPage_product($idp){
    return 
        '<?php 
        require_once("../../../assets/config/config.php");
    
        $id = '.$idp.';
        $cont = "";
        $breadcrumbs = "";
        $categories_product_footer = "";
        $product_specs = "";
    
        //Check if product has subcategory
        $sql = 
            "SELECT products.id
            FROM products, product_subcategories
            WHERE products.id = \'$id\'
            AND products.idps = product_subcategories.id;";
        $res = mysqli_query($con, $sql);
        $row  = mysqli_fetch_assoc($res);
        if(empty($row["id"])){
            $sql = 
                "SELECT products.id AS id, products.title, products.description, products.size, products.manufacturer, products.code, products.price, product_categories.id AS idpc, product_categories.name AS pc_name
                FROM products, product_categories 
                WHERE products.id = \'$id\'
                AND product_categories.id = products.idpc
                AND products.view = 1;";
        }else{
            $sql = 
            "SELECT products.id AS id, products.title, products.description, products.size, products.manufacturer, products.code, products.price, product_categories.id AS idpc, product_categories.name AS pc_name, product_subcategories.id AS idps, product_subcategories.name AS ps_name
            FROM products, product_categories, product_subcategories 
            WHERE products.id = \'$id\'
            AND product_categories.id = products.idpc
            AND product_subcategories.id = products.idps
            AND products.view = 1;";
        }
        $res = mysqli_query($con, $sql);
        $row  = mysqli_fetch_assoc($res);
    
        // set product info
        $product_title = $row["title"];
        $product_description = $row["description"];
        $product_size = $row["size"];
        $product_manufacturer = $row["manufacturer"];
        $product_code = $row["code"];
        $product_price = $row["price"];
        $category_name = $row["pc_name"];
        $subcategory_name = "";
        $category_seperator = "";
        if(!empty($row["ps_name"])){
            $subcategory_name = $row["ps_name"];
            $category_seperator = ", ";
        }
    
        if(!empty($category_name)){
            $breadcrumbs .= \'<li class="breadcrumb-item"><a href="https://mskstores.nl/shop/\'.strtolower($category_name).\'">\'.$category_name.\'</a></li>\';
            $categories_product_footer .= \'<a href="https://mskstores.nl/shop/\'.strtolower($category_name).\'">\'.$category_name.\'</a>\'.$category_seperator.\'\';
        }
        if(!empty($subcategory_name)){
            $breadcrumbs .= \'<li class="breadcrumb-item"><a href="https://mskstores.nl/shop/\'. strtolower($category_name).\'/\'. strtolower($subcategory_name).\'">\'.strtolower($subcategory_name).\'</a></li>\';
            $categories_product_footer .= \'<a href="https://mskstores.nl/shop/\'. strtolower($category_name).\'/\'. strtolower($subcategory_name).\'">\'.strtolower($subcategory_name).\'</a>\';
        }
    
        $sql = 
            "SELECT *
            FROM product_specs
            WHERE idp = \'$id\';";
        $res = mysqli_query($con, $sql);
        while($row  = mysqli_fetch_assoc($res)){
            $product_specs .= "<li>".$row[\'name\'].": ".$row[\'value\']."</li>";
        }
    
        if(isset($_GET["add"])){
            $js .= "$(\'#confirmationModal\').modal(\'show\');";
            $js .= "$(\'#modal-title\').html(\'Product toegevoegd aan winkelwagen\');";
        }
    
        if(isset($_GET["remove"])){
            $js .= "$(\'#confirmationModal\').modal(\'show\');";
            $js .= "$(\'#modal-title\').html(\'Product verwijderd uit winkelwagen\');";
        }
    
        $product_price = \'€\' . number_format($product_price, 2, \',\', \'.\');
    
        $img = 
            \'
            <img style="max-height: 240px; object-fit:contain;" src="../../assets/images/products/fallback/fallback.png" alt="image not found" class="product-image">
            \';
            
        if(file_exists(\'../../assets/images/products/\'.$id.\'/0/SD/\')){
            $img_name = array_values(array_diff(scandir(\'../../assets/images/products/\'.$id.\'/0/SD/\'), [".", ".."]));
            $img = 
                \'
                <img id="product-zoom" style="object-fit:contain;" src="../../assets/images/products/\'.$id.\'/0/SD/\'.$img_name[0].\'" alt="\'.$img_name[0].\'" class="product-image" data-zoom-image="../../assets/images/products/\'.$id.\'/0/SD/\'.$img_name[0].\'">
                \';
        }
    
        $cont .= 
            \'<div class="container">
                <div class="product-details-top mb-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="product-gallery product-gallery-vertical">
                            <div class="row">
                                    <figure class="product-main-image">
                                        \'.$img.\'
                                    </figure><!-- End .product-main-image -->
                                </div><!-- End .row -->
                            </div><!-- End .product-gallery -->
                        </div><!-- End .col-md-6 -->
    
                        <div class="col-md-6">
                            <div class="product-details product-details-centered">
                                <h1 class="product-title">\'.$product_title.\'</h1><!-- End .product-title -->
    
                                <div class="product-price">
                                    \'.$product_price.\'
                                </div><!-- End .product-price -->
    
                                <div class="product-content">
                                    <ul>\'.$product_specs.\'</ul>
                                </div><!-- End .product-content -->
    
                                <div class="product-details-action">
                                    <div class="details-action-col">
                                        <a href="javascript:addToCart(\'.$id.\');" class="btn-product btn-cart"><span>Voeg toe aan winkelwagen</span></a>
                                    </div><!-- End .details-action-col -->
                                </div><!-- End .product-details-action -->
    
                                <div class="product-details-footer">
                                    <div class="product-cat">
                                        <span>Categorie:</span>
                                        \'.$categories_product_footer.\'
                                    </div><!-- End .product-cat -->
                                </div><!-- End .product-details-footer -->
                            </div><!-- End .product-details -->
                        </div><!-- End .col-md-6 -->
                    </div><!-- End .row -->
                </div><!-- End .product-details-top -->
    
                <div class="product-details-tab">
                    <ul class="nav nav-pills justify-content-center" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="product-desc-link" data-toggle="tab" role="tab" aria-controls="product-desc-tab" aria-selected="true">Beschrijving</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="product-desc-tab" role="tabpanel" aria-labelledby="product-desc-link">
                            <div class="product-desc-content">
                                <h3>Product Informatie</h3>
                                \'.$product_description.\'
                            </div><!-- End .product-desc-content -->
                        </div><!-- .End .tab-pane -->
                    </div><!-- End .tab-content -->
                </div><!-- End .product-details-tab -->
            </div><!-- End .container -->\';
        
    ?>
    
    
    <!DOCTYPE html>
    <html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>:: MSK STORES :: Shop</title>
        <meta name="description" content="">
    
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-WGZG2N494N"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag(\'js\', new Date());
    
            gtag(\'config\', \'G-WGZG2N494N\');
        </script>
        
        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="../../assets/images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../../assets/images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon/favicon-16x16.png">
        <link rel="manifest" href="../../assets/images/favicon/site.webmanifest">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="../../assets/images/icons/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <!-- Plugins CSS File -->
        <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
        <!-- Main CSS File -->
        <link rel="stylesheet" href="../../assets/css/style.css">
        <link rel="stylesheet" href="../../assets/css/plugins/owl-carousel/owl.carousel.css">
        <link rel="stylesheet" href="../../assets/css/plugins/magnific-popup/magnific-popup.css">
        <link rel="stylesheet" href="../../assets/css/plugins/nouislider/nouislider.css">
    </head>
    
    <body>
        <div class="page-wrapper">
            <?php require_once("../../header.php"); ?>
    
            <main class="main">
                <nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0">
                    <div class="container d-flex align-items-center">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="https://www.mskstores.nl/">Home</a></li>
                            <?php echo $breadcrumbs; ?>
                        </ol>
                    </div><!-- End .container -->
                </nav><!-- End .breadcrumb-nav -->
    
                <div class="page-content mt-2">
                    <?php echo $cont; ?>
                </div><!-- End .page-content -->
            </main>
    
            <div class="modal fade show" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="cta bg-image" style="background-image: url(assets/images/backgrounds/cta/bg-3.jpg);background-position: center right;">
                                <div class="cta-wrapper cta-text text-center">
                                    <h3 class="cta-title" id="modal-title"></h3><!-- End .cta-title -->
                                    <p class="cta-desc">U kunt verder winkelen of direct afrekenen.</p><!-- End .cta-desc -->
                            
                                    <a href="../../winkelwagen" class="btn btn-primary btn-rounded"><span>Winkelwagen</span><i class="icon-long-arrow-right"></i></a>
                                    <button onclick="$(\'#confirmationModal\').modal(\'hide\')" class="btn btn-outline-primary-2 btn-rounded mt-2"><span>Verder winkelen</span><i class="icon-long-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php require_once("../../footer.php"); ?>
        </div>
        <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
    
        <script>
    
            function addToCart(id){
                $.ajax({
                    url: \'../../assets/php/ajax.php\',
                    type: \'post\',
                    data: {addToCart: id},
                    success: function(response) {
                        if(response["status"] == "success") {
                            window.location.href = "./?add";
                        }
                    }
                });
            }
    
            function removeFromCart(id){
                $.ajax({
                    url: \'../../assets/php/ajax.php\',
                    type: \'post\',
                    data: {removeFromCart: id},
                    success: function(response) {
                        if(response["status"] == "success") {
                            window.location.href = "./?remove";
                        }
                    }
                });
            }
        </script>
    
        <!-- Plugins JS File -->
        <script src="../../assets/js/jquery.min.js"></script>
        <script src="../../assets/js/bootstrap.bundle.min.js"></script>
        <script src="../../assets/js/jquery.hoverIntent.min.js"></script>
        <script src="../../assets/js/jquery.waypoints.min.js"></script>
        <script src="../../assets/js/superfish.min.js"></script>
        <script src="../../assets/js/owl.carousel.min.js"></script>
        <script src="../../assets/js/wNumb.js"></script>
        <script src="../../assets/js/bootstrap-input-spinner.js"></script>
        <script src="../../assets/js/jquery.elevateZoom.min.js"></script>
        <script src="../../assets/js/jquery.magnific-popup.min.js"></script>
        <script src="../../assets/js/nouislider.min.js"></script>
        
        <!-- Main JS File -->
        <script src="../../assets/js/main.js"></script>
        <?php require_once("../../assets/js/main.php"); ?>
    </body>
    
    </html>';
}

?>