<?php

// pages object
$pages = [
    [
        "name" => "home",
        "url" => "",
        "nl" => "Home",
        "icon" => "fa-home",
        "permission" => ["admin"]
    ],[
        "name" => "orders",
        "url" => "orders.php",
        "nl" => "Orders",
        "icon" => "fa-clipboard",
        "permission" => ["admin"]
    ],[
        "name" => "products",
        "url" => "products.php",
        "nl" => "Producten",
        "icon" => "fa-box",
        "permission" => ["admin"]
    ],[
        "name" => "categories",
        "url" => "categories.php",
        "nl" => "Categorieën",
        "icon" => "fa-boxes",
        "permission" => ["admin"]
    ]
];

// search for current page
$page_name = $pages[array_search($current_page, array_column($pages, "url"))]["nl"];

// create content
$header_items = "";
$current_page_permission = [];
for ($i=0; $i < count($pages); $i++) { 
    $page = $pages[$i];
    $active = "";
    if($current_page == $page["url"]){
        $active = " active";
        $current_page_permission = $page["permission"];
    }
    // checks if user has permission for the page
    if(checkRoles($page["permission"])){
        // hide page if no permission
        $header_items .= 
            '<li class="menu-item'.$active.'" id="panel-link-'.$page["name"].'">
                <a href="./'.$page["url"].'" class="menu-link">
                    <span class="menu-icon mr-2">
                        <i class="icon-placeholder fa '.$page["icon"].'"></i>
                    </span>
                    <span class="menu-label">
                        <span class="menu-name">'.$page["nl"].'</span>
                    </span>
                </a>
            </li>';
    }
}

if(!checkRoles($current_page_permission)){
    // redirect if no permission to view current page
    header("Location: ./login.php?logout");
    exit();
}else{
    // get name and surname
    $fullname_user = "Admin User";
    $initials_user = "AU";
    if(isset($_SESSION["user_id"])){
        $user_id_safe = mysqli_real_escape_string($con, $_SESSION["user_id"]);
        $sql =
            "SELECT users.name, users.surname
            FROM users
            WHERE users.id = '$user_id_safe';";
        $res = mysqli_query($con, $sql);
        if($res && $row = mysqli_fetch_assoc($res)){
            $fullname_user = trim(($row["name"] ?? "")." ".($row["surname"] ?? ""));
            if(empty($fullname_user)){
                $fullname_user = "Admin User";
            }
            $initials_user = "";
            $initials_user_ex = explode(" ", $fullname_user);
            for ($i=0; $i < count($initials_user_ex); $i++) {
                if(!empty($initials_user_ex[$i])){
                    $initials_user .= mb_substr($initials_user_ex[$i], 0, 1);
                }
            }
            if(empty($initials_user)){
                $initials_user = "A";
            }
        }
    }
}

?>

<aside class="admin-sidebar shadow-sm">
    <div class="admin-sidebar-brand px-4 py-3 d-flex align-items-center justify-content-between border-bottom">
        <a href="./" class="d-flex align-items-center text-decoration-none">
            <span class="font-weight-bold text-primary h4 mb-0"><i class="fa fa-store mr-2"></i>MSK STORES</span>
        </a>
        <div class="ml-auto d-lg-none">
            <a href="#" class="admin-close-sidebar text-muted"><i class="fa fa-times"></i></a>
        </div>
    </div>
    <div class="admin-sidebar-wrapper js-scrollbar">
        <ul class="menu py-3">
            <?php echo $header_items;?>
            <li class="menu-item mt-4 border-top pt-3">
                <a href="./login.php?logout" class="menu-link text-danger">
                    <span class="menu-icon mr-2">
                        <i class="icon-placeholder fa fa-sign-out-alt text-danger"></i>
                    </span>
                    <span class="menu-label">
                        <span class="menu-name">Uitloggen</span>
                    </span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<header class="admin-header shadow-sm bg-white">
    <a href="#" class="sidebar-toggle" data-toggleclass="sidebar-open" data-target="body"><i class="fa fa-bars"></i></a>
    <div class="mr-auto my-auto pl-3 d-none d-md-block">
        <span class="text-muted font-weight-medium">Beheerderspaneel</span>
    </div>
    <nav class="ml-auto">
        <ul class="nav align-items-center">
            <li class="nav-item mr-3">
                <a href="../" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fa fa-external-link-alt mr-1"></i> Naar Webshop
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-sm mr-2">
                        <span class="avatar-title rounded-circle bg-primary text-white font-weight-bold"><?php echo $initials_user;?></span>
                    </div>
                    <span class="font-weight-bold text-dark d-none d-sm-inline"><?php echo $fullname_user;?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow border-0 mt-2">
                    <div class="dropdown-header font-weight-bold text-dark"><?php echo $fullname_user;?></div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="login.php?logout"><i class="fa fa-sign-out-alt mr-2 text-danger"></i> Uitloggen</a>
                </div>
            </li>
        </ul>
    </nav>
</header>

<div class="bg-gradient-dark p-t-80 admin-preheader text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    <div class="container-fluid m-b-30">
        <div class="row align-items-center">
            <div class="col-md-8 p-t-30 p-b-80">
                <h1 class="font-weight-bold text-white mb-2"><?php echo $page_name;?></h1>
                <p class="text-white-50 mb-0">Welkom in het MSK Stores beheerderspaneel.</p>
            </div>
            <div class="col-md-4 text-md-right p-t-30 p-b-80">
                <?php echo $header_btns;?>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-slide-left  fade" id="siteSearchModal" tabindex="-1" role="dialog" aria-labelledby="siteSearchModal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body p-all-0" id="site-search">
                <button type="button" class="close light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="form-dark bg-dark text-white p-t-60 p-b-20 bg-dots" >
                    <h3 class="text-uppercase text-center fw-300 "> Zoeken</h3>
                    <div class="container-fluid">
                        <div class="col-md-10 p-t-10 m-auto">
                            <input type="search" placeholder="Zoeken door het paneel" class=" search form-control form-control-lg">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="bg-dark text-muted container-fluid p-b-10 text-center text-overline">resultaten</div>
                    <div class="list-group list">
                        <div class="list-group-item d-flex align-items-center">
                            <div class="m-r-20">
                                <div class="avatar avatar-sm">
                                    <img class="avatar-img rounded-circle" src="https://admin.mskstores.nl/assets/img/logo.png" alt="Voorbeeld">
                                </div>
                            </div>
                            <div>
                                <div class="name">Binnenkort...</div>
                                <div class="text-muted">Binnenkort...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>