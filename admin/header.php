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
                    <span class="menu-label">
                        <span class="menu-name">'.$page["nl"].'</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder fa '.$page["icon"].'"></i>
                    </span>
                </a>
            </li>';
    }
}

if(!checkRoles($current_page_permission)){
    // redirect if no permission to view current page
    header("Location: ./login.php?logout");
}else{
    // get name and surname
    $sql = 
        "SELECT users.name, users.surname
        FROM users
        WHERE users.id = '".$_SESSION["user_id"]."';";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);
    
    $fullname_user = $row["name"]." ".$row["surname"];
    $initials_user = "";
    $initials_user_ex = explode(" ", $fullname_user);
    for ($i=0; $i < count($initials_user_ex); $i++) { 
        $initials_user .= $initials_user_ex[$i][0];
    }
}

?>

<aside class="admin-sidebar">
    <div class="admin-sidebar-brand">
        <!-- begin sidebar branding-->
        <a href="https://www.mskstores.nl" class="py-3">
            <img class="admin-brand-logo" src="https://admin.mskstores.nl/assets/img/logo.png" alt="Logo MSK Stores">
        </a>
        <!-- end sidebar branding-->
        <div class="ml-auto">
            <!-- sidebar close for mobile device-->
            <a href="#" class="admin-close-sidebar"><i class="fa fa-times"></i></a>
        </div>
    </div>
    <div class="admin-sidebar-wrapper js-scrollbar">
        <ul class="menu">
            <?php echo $header_items;?>
            <li class="menu-item bg">
                <a href="./login.php?logout" class="menu-link">
                    <span class="menu-label">
                        <span class="menu-name text-danger">Uitloggen</span>
                    </span>
                    <span class="menu-icon">
                        <i class="icon-placeholder fa fa-sign-out text-danger"></i>
                    </span>
                </a>
            </li>
        </ul>
    </div>
</aside>
<header class="admin-header">
    <a href="#" class="sidebar-toggle" data-toggleclass="sidebar-open" data-target="body"><i class="fa fa-bars"></i></a>
    <nav class=" mr-auto my-auto">
        <ul class="nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" data-target="#siteSearchModal" data-toggle="modal" href="#">
                    <i class="fa fa-search fa-lg"></i>
                </a>
            </li>
        </ul>
    </nav>
    <nav class=" ml-auto">
        <ul class="nav align-items-center">
            <li class="nav-item">
                <div class="dropdown">
                    <a href="#" class="nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                        <i class="fa fa-bell fa-lg"></i>
                        <span class="notification-counter"></span>
                    </a>
                    <div class="dropdown-menu notification-container dropdown-menu-right">
                        <div class="d-flex p-all-15 bg-white justify-content-between border-bottom ">
                            <a href="#!" class="fa fa-cog text-muted"></a>
                            <span class="h5 m-0">Meldingen</span>
                            <a href="#!" class="fa fa-trash text-muted"></a>
                        </div>
                        <div class="notification-events bg-gray-300">
                            <div class="text-overline m-b-5">vandaag</div>
                            <a href="#" class="d-block m-b-10">
                                <div class="card">
                                    <div class="card-body"> <i class="fa fa-check text-success"></i> Binnenkort...</div>
                                </div>
                            </a>
                            <a href="#" class="d-block m-b-10">
                                <div class="card">
                                    <div class="card-body"> <i class="fa fa-file text-primary"></i> Binnenkort...</div>
                                </div>
                            </a>
                            <a href="#" class="d-block m-b-10">
                                <div class="card">
                                    <div class="card-body">
                                        <i class="fa fa-times text-danger"></i> Binnenkort...</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown ">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-sm avatar-online">
                        <span class="avatar-title rounded-circle bg-dark"><?php echo $initials_user;?></span>
                    </div>
                </a>
                <div class="dropdown-menu  dropdown-menu-right">
                    <a class="dropdown-item"> <?php echo $fullname_user;?></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#"> <i class="fa fa-user"></i> Profile</a>
                    <a class="dropdown-item" href="#"> <i class="fa fa-cogs"></i> Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="login.php?logout"> <i class="fa fa-sign-out-alt"></i> Logout</a>
                </div>
            </li>
        </ul>
    </nav>
</header>
<div class="bg-dark p-t-80 admin-preheader">
    <div class="container-fluid m-b-30">
        <div class="row">
            <div class="col-12 text-white p-t-40 p-b-100">
                <h1><?php echo $page_name;?></h1>
                <p class="opacity-75"></p>
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