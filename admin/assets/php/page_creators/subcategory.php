<?php

function createPage_subcategory($ids){
    return 
        '<?php 
        require_once("../../../../assets/config/config.php");
    
        $id = '.$ids.';
    
        $cont = "";
        $category_name = "";
        $total_products = 0;
        $filters = "";
        $sizes = array();
        $sort = "ORDER BY products.title ASC";
    
        // CATEGORY INFO
    
        $sql = "SELECT 
                    product_subcategories.name
                FROM
                    product_subcategories
                WHERE
                    product_subcategories.id = \'$id\';";
        $res = mysqli_query($con, $sql);
        // set category name
        while($row = mysqli_fetch_assoc($res)){
            $category_name = $row[\'name\'];
        }
    
        if(isset($_GET["add"])){
            $js .= "$(\'#confirmationModal\').modal(\'show\');";
            $js .= "$(\'#modal-title\').html(\'Product toegevoegd aan winkelwagen\');";
        }
    
        if(isset($_GET["remove"])){
            $js .= "$(\'#confirmationModal\').modal(\'show\');";
            $js .= "$(\'#modal-title\').html(\'Product verwijderd uit winkelwagen\');";
        }
    
        // filters
        $sql = "SELECT 
                    products.id, products.title, products.size 
                FROM
                    products
                WHERE
                    products.view = 1;";
        $res = mysqli_query($con, $sql);
        while($row = mysqli_fetch_assoc($res)){
            // get every size only once in sizes array
            if(!in_array($row[\'size\'], $sizes)){
                $sizes[] = $row[\'size\'];
            }
        }
    
        for($i = 0; $i < count($sizes); $i++){
            $filters .= \'<div class="filter-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="size-\'.$sizes[$i].\'" name="filter" value="\'.$sizes[$i].\'">
                                <label class="custom-control-label" for="size-\'.$sizes[$i].\'">\'.$sizes[$i].\'</label>
                            </div><!-- End .custom-checkbox -->
                        </div><!-- End .filter-item -->\';
        }
    
        // if isset sort
        if(isset($_GET[\'sort\'])){
            $sort = $_GET[\'sort\'];
            switch($sort){
                case \'alfabetisch-asc\':
                    $sort = "ORDER BY products.title ASC";
                    $js .= "document.getElementById(\'sortby\').value = \'alfabetisch-asc\';";
                    break;
                case \'alfabetisch-desc\':
                    $sort = "ORDER BY products.title DESC";
                    $js .= "document.getElementById(\'sortby\').value = \'alfabetisch-desc\';";
                    break;
                case \'size-asc\':
                    $sort = "ORDER BY products.size ASC";
                    $js .= "document.getElementById(\'sortby\').value = \'size-asc\';";
                    break;
                case \'size-desc\':
                    $sort = "ORDER BY products.size DESC";
                    $js .= "document.getElementById(\'sortby\').value = \'size-desc\';";
                    break;
                default:
                    $sort = "ORDER BY products.title ASC";
                    $js .= "document.getElementById(\'sortby\').value = \'alfabetisch-asc\';";
                    break;
            }
        }
    
    
        // if isset get filters
        if(isset($_GET[\'filters\'])){
            // put filters in array
            $filters_passed = explode(",", $_GET[\'filters\']);
    
            for($i = 0; $i < count($filters_passed); $i++){
                $js .= "document.getElementById(\'size-".$filters_passed[$i]."\').checked = true;";
            }
    
            $sql = "SELECT 
                    products.id, products.title, products.size 
                FROM
                    products
                WHERE
                    products.view = 1
                AND
                    products.size IN (\"".implode("\",\"", $filters_passed)."\") ".$sort.";";
            $res = mysqli_query($con, $sql);
            while($row = mysqli_fetch_assoc($res)){
                $total_products++;
    
                $cont .= \'<div class="col-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2">
                            <div class="product">
                                <figure class="product-media">
                                    <a>
                                        <img src="../../../assets/images/products/product-1.jpg" alt="\'.$row[\'title\'].\'" class="product-image">
                                    </a>
                                    <div class="product-action action-icon-top">\';
                                        if(isset($_SESSION[\'cart\'])){
                                            if(!in_array($row[\'id\'], $_SESSION[\'cart\'])){
                                                $cont .= \'<a href="javascript:addToCart(\'.$row[\'id\'].\');" class="btn-product btn-cart"><span>Voeg toe aan winkelwagen</span></a>\';
                                            }else{
                                                $cont .= \'<a href="javascript:removeFromCart(\'.$row[\'id\'].\');" class="btn-product btn-cart"><span style="color: red;">Verwijder uit winkelwagen</span></a>\';
                                            }
                                        }
                            $cont .= \'</div><!-- End .product-action -->
                                </figure><!-- End .product-media -->
    
                                <div class="product-body">
                                    <div class="product-cat">
                                        <a>\'.$row[\'size\'].\'</a>
                                    </div><!-- End .product-cat -->
                                    <h3 class="product-title"><a>\'.$row[\'title\'].\'</a></h3><!-- End .product-title -->
                                    <div class="product-price">
                                    </div><!-- End .product-price -->
                                </div><!-- End .product-body -->
                            </div><!-- End .product -->
                        </div><!-- End .col-sm-6 col-lg-4 col-xl-3 -->\';
            }
        } else {
            $sql = "SELECT 
                        products.id, products.title, products.size 
                    FROM
                        products
                    WHERE
                        products.idps = \'$id\'
                    AND
                        products.view = 1 ".$sort.";";
    
            $res = mysqli_query($con, $sql);
            while($row = mysqli_fetch_assoc($res)){
                $img = 
                    \'<a>
                    <img style="max-height: 240px; object-fit:contain;" src="../../../assets/images/products/fallback/fallback.png" alt="image not found" class="product-image">
                    </a>\';
                if(file_exists(\'../../../assets/images/products/\'.$row[\'id\'].\'/0/SD/\')){
                    $img_name = array_values(array_diff(scandir(\'../../../assets/images/products/\'.$row[\'id\'].\'/0/SD/\'), [".", ".."]));
                    $img = 
                        \'<a>
                            <img style="max-height: 240px; object-fit:contain;" src="../../../assets/images/products/\'.$row[\'id\'].\'/0/SD/\'.$img_name[0].\'" alt="\'.$img_name[0].\'" class="product-image">
                        </a>\';
                }
                
    
                $total_products++;
    
                $cont .= \'<div class="col-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2">
                            <div class="product">
                                <figure class="product-media" style="background-color: unset;">
                                    \'.$img.\'
                                    <div class="product-action action-icon-top">\';
                                        if(isset($_SESSION[\'cart\'])){
                                            if(!in_array($row[\'id\'], $_SESSION[\'cart\'])){
                                                $cont .= \'<a href="javascript:addToCart(\'.$row[\'id\'].\');" class="btn-product btn-cart"><span>Voeg toe aan winkelwagen</span></a>\';
                                            }else{
                                                $cont .= \'<a href="javascript:removeFromCart(\'.$row[\'id\'].\');" class="btn-product btn-cart"><span style="color: red;">Verwijder uit winkelwagen</span></a>\';
                                            }
                                        }
                            $cont .= \'</div><!-- End .product-action -->
                                </figure><!-- End .product-media -->
    
                                <div class="product-body">
                                    <div class="product-cat">
                                        <a>\'.$row[\'size\'].\'</a>
                                    </div><!-- End .product-cat -->
                                    <h3 class="product-title"><a>\'.$row[\'title\'].\'</a></h3><!-- End .product-title -->
                                    <div class="product-price">
                                    </div><!-- End .product-price -->
                                </div><!-- End .product-body -->
                            </div><!-- End .product -->
                        </div><!-- End .col-sm-6 col-lg-4 col-xl-3 -->\';
            }
        }
        
    
    
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
        <link rel="apple-touch-icon" sizes="180x180" href="../../../assets/images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../../../assets/images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../../../assets/images/favicon/favicon-16x16.png">
        <link rel="manifest" href="../../../assets/images/favicon/site.webmanifest">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="../../../assets/images/icons/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <!-- Plugins CSS File -->
        <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
        <!-- Main CSS File -->
        <link rel="stylesheet" href="../../../assets/css/style.css">
        <link rel="stylesheet" href="../../../assets/css/plugins/owl-carousel/owl.carousel.css">
        <link rel="stylesheet" href="../../../assets/css/plugins/magnific-popup/magnific-popup.css">
        <link rel="stylesheet" href="../../../assets/css/plugins/nouislider/nouislider.css">
    </head>
    
    <body>
        <div class="page-wrapper">
            <?php require_once("../../../header.php"); ?>
    
            <main class="main">
                <div class="page-header text-center" style="background-image: url(\'../../../assets/images/page-header-bg.jpg\')">
                    <div class="container-fluid">
                        <h1 class="page-title"><?php echo $category_name; ?><span>Shop</span></h1>
                    </div><!-- End .container-fluid -->
                </div><!-- End .page-header -->
                <nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
                    <div class="container-fluid">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Shop</li>
                        </ol>
                    </div><!-- End .container-fluid -->
                </nav><!-- End .breadcrumb-nav -->
    
                <div class="page-content">
                    <div class="container-fluid">
                        <div class="toolbox">
                            <div class="toolbox-left">
                                <a href="./#" class="sidebar-toggler d-none"><i class="icon-bars"></i>Filters</a>
                            </div><!-- End .toolbox-left -->
    
                            <div class="toolbox-center">
                                <div class="toolbox-info">
                                    <?php echo $total_products; ?></span> Producten
                                </div><!-- End .toolbox-info -->
                            </div><!-- End .toolbox-center -->
    
                            <div class="toolbox-right">
                                <div class="toolbox-sort">
                                    <label for="sortby">Sorteren op:</label>
                                    <div class="select-custom">
                                        <select name="sortby" id="sortby" class="form-control">
                                            <option value="alfabetisch-asc" selected="selected">Alfabetisch (A-Z)</option>
                                            <option value="alfabetisch-desc">Alfabetisch (Z-A)</option>
                                            <option value="size-asc">Size (Groot naar Klein)</option>
                                            <option value="size-desc">Size (Klein naar Groot)</option>
                                        </select>
                                    </div>
                                </div><!-- End .toolbox-sort -->
                            </div><!-- End .toolbox-right -->
                        </div><!-- End .toolbox -->
    
                        <div class="products">
                            <div class="row">
                                <?php echo $cont; ?>
                            </div><!-- End .row -->
                        </div><!-- End .products -->
    
                        <div class="sidebar-filter-overlay"></div><!-- End .sidebar-filter-overlay -->
                        <aside class="sidebar-shop sidebar-filter">
                            <div class="sidebar-filter-wrapper">
                                <div class="widget widget-clean">
                                    <label><i class="icon-close"></i>Filters</label>
                                    <a href="./#" class="sidebar-filter-clear">Verwijderen</a>
                                </div><!-- End .widget -->
                                <div class="widget widget-collapsible">
                                    <h3 class="widget-title">
                                        <a data-toggle="collapse" href="./#widget-1" role="button" aria-expanded="true" aria-controls="widget-1">
                                            Dimensies
                                        </a>
                                    </h3><!-- End .widget-title -->
    
                                    <div class="collapse show" id="widget-1">
                                        <div class="widget-body">
                                            <div class="filter-items filter-items-count">
                                                <?php echo $filters; ?>
                                            </div><!-- End .filter-items -->
                                            <a href="javascript:filterProducts();" class="btn btn-link btn-underline w-100 mt-5">Toepassen</a>
                                        </div><!-- End .widget-body -->
                                    </div><!-- End .collapse -->
                                </div><!-- End .widget -->
                            </div><!-- End .sidebar-filter-wrapper -->
                        </aside><!-- End .sidebar-filter -->
                    </div><!-- End .container-fluid -->
                </div><!-- End .page-content -->
            </main><!-- End .main -->
    
            <!-- CONFIRMATION MODAL -->
            <div class="modal fade show" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="cta bg-image" style="background-image: url(assets/images/backgrounds/cta/bg-3.jpg);background-position: center right;">
                                <div class="cta-wrapper cta-text text-center">
                                    <h3 class="cta-title" id="modal-title"></h3><!-- End .cta-title -->
                                    <p class="cta-desc">U kunt verder winkelen of direct afrekenen.</p><!-- End .cta-desc -->
                            
                                    <a href="../../../winkelwagen" class="btn btn-primary btn-rounded"><span>Winkelwagen</span><i class="icon-long-arrow-right"></i></a>
                                    <button onclick="$(\'#confirmationModal\').modal(\'hide\')" class="btn btn-outline-primary-2 btn-rounded mt-2"><span>Verder winkelen</span><i class="icon-long-arrow-right"></i></button>
                                </div><!-- End .cta-wrapper -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <?php require_once("../../../footer.php"); ?>
        </div><!-- End .page-wrapper -->
        <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
    
        <script>
            function filterProducts() {
                var filters = [];
                var checkboxes = document.querySelectorAll(\'input[name="filter"]:checked\');
                if(checkboxes.length > 0) {    
                    for (var i = 0; i < checkboxes.length; i++) {
                        filters.push(checkboxes[i].value);
                    }
                    window.location.href = "index.php?filters=" + filters.join(",");
                } else {
                    window.location.href = "index.php";
                }
            }
    
            // if #sortby is changed
            document.getElementById("sortby").addEventListener("change", function() {
                var sort = this.value;
                // check current url for filters
                var url = new URL(window.location.href);
                var filters = url.searchParams.get("filters");
                if(filters) {
                    window.location.href = "index.php?sort=" + sort + "&filters=" + filters;
                } else {
                    window.location.href = "index.php?sort=" + sort;
                }
            });
    
            function addToCart(id){
                $.ajax({
                    url: \'../../../assets/php/ajax.php\',
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
                    url: \'../../../assets/php/ajax.php\',
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
        <script src="../../../assets/js/jquery.min.js"></script>
        <script src="../../../assets/js/bootstrap.bundle.min.js"></script>
        <script src="../../../assets/js/jquery.hoverIntent.min.js"></script>
        <script src="../../../assets/js/jquery.waypoints.min.js"></script>
        <script src="../../../assets/js/superfish.min.js"></script>
        <script src="../../../assets/js/owl.carousel.min.js"></script>
        <script src="../../../assets/js/wNumb.js"></script>
        <script src="../../../assets/js/bootstrap-input-spinner.js"></script>
        <script src="../../../assets/js/jquery.magnific-popup.min.js"></script>
        <script src="../../../assets/js/nouislider.min.js"></script>
        <!-- Main JS File -->
        <script src="../../../assets/js/main.js"></script>
        <?php require_once("../../../assets/js/main.php"); ?>
    </body>
    
    </html>';
}

?>