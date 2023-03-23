<?php
 
require_once("../../../assets/config/config.php");

// js header 
header("Content-Type: application/json; charset=utf-8");

// to prevent parse error
if(true){
	ini_set("display_errors", 0);
	ini_set("display_startup_errors", 0);
}

if(!isset($_SESSION["user_id"])){
    print_r(json_encode(["Error" => "Not Authorized"]));
}else{
    switch ($_GET["request"]) {
        case "getProducts":
            // get total count
            $sql = 
                "SELECT COUNT(*) AS count_total
                FROM products
                WHERE products.view = 1;";
            $res = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($res);
            $count_total = $row["count_total"];
            $count_total_filtered = $count_total;

            // check if search is set and update query
            $query = "WHERE products.view = 1 AND products.idpc = product_categories.id";
            if(!empty($_POST["search"]["value"])){
                $q = explode(" ", $_POST["search"]["value"]);
                for ($i=0; $i < count($q); $i++) { 
                    // add spaces between items if you want to ignore `motors` that finds a `motor seat`
                    $query .= " AND CONCAT(product_categories.name, products.title, products.size, products.manufacturer, products.code, products.price, products.stock, products.pack_amount) LIKE '%".$q[$i]."%'";
                }
                $sql = 
                    "SELECT COUNT(*) AS count_total
                    FROM products, product_categories
                    ".$query.";";
                $res = mysqli_query($con, $sql);
                $row = mysqli_fetch_assoc($res);
                $count_total_filtered = $row["count_total"];
            }

            // check if order is set and update query
            $map = [
                "product_categories.name",
                "products.title",
                "products.code",
                "products.price",
                "products.stock",
            ];
            if(!empty($_POST["order"])){
                $o = $_POST["order"];
                $order = [];
                for ($i=0; $i < count($o); $i++) { 
                    $order[] = $map[$o[$i]["column"]]." ".strtoupper($o[$i]["dir"]);
                }
                $query .= " ORDER BY ".implode(", ", $order);
            }

            // get all data
            $sql = 
                "SELECT product_categories.name, products.id, products.title, products.size, products.manufacturer, products.code, products.price, products.stock, products.pack_amount
                FROM products, product_categories
                ".$query."
                LIMIT ".$_POST["length"]."
                OFFSET ".$_POST["start"].";";
            $res = mysqli_query($con, $sql);
            $r = [];
            // loop thru all data and format if needed
            while($row = mysqli_fetch_assoc($res)){
                $btns = 
                    '<div class="dropdown show">
                        <a class="btn btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink'.$row["id"].'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs text-dark"></i></a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink'.$row["id"].'">
                            <a class="btn btn-primary w-100" href="?view='.$row["id"].'">Bekijken <i class="fa fa-search"></i></a>
                            <a class="btn btn-warning w-100 mt-1" href="?edit='.$row["id"].'">Bewerken <i class="fa fa-pencil-alt"></i></a>
                            <a class="btn btn-danger w-100 mt-1" href="?delete='.$row["id"].'">Verwijderen <i class="fa fa-trash"></i></a>                        
                        </div>
                    </div>';
                // store all data in a variable
                $r[] = [
                    "category_name" => $row["name"],
                    "title" => $row["title"],
                    "code" => $row["code"],
                    "price" => $row["price"],
                    "stock" => $row["stock"],
                    "btns" => $btns
                ];
            }  

            // return all data
            print_r(json_encode([
                "draw" => (int)$_POST["draw"],
                "recordsTotal" => (int)$count_total,
                "recordsFiltered" => (int)$count_total_filtered,
                "data" => $r
            ]));
            break; 
        case "getOrders":
            // get total count
            $sql = 
                "SELECT COUNT(*) AS count_total
                FROM orders;";
            $res = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($res);
            $count_total = $row["count_total"];
            $count_total_filtered = $count_total;

            // check if search is set and update query
            $query = "WHERE 1 = 1";
            if(!empty($_POST["search"]["value"])){
                $q = explode(" ", $_POST["search"]["value"]);
                for ($i=0; $i < count($q); $i++) { 
                    // add spaces between items if you want to ignore `motors` that finds a `motor seat`
                    $query .= " AND CONCAT(orders.name, orders.company_name, orders.email, orders.address, orders.postal_code, orders.city) LIKE '%".$q[$i]."%'";
                }
                $sql = 
                    "SELECT COUNT(*) AS count_total
                    FROM orders
                    ".$query.";";
                $res = mysqli_query($con, $sql);
                $row = mysqli_fetch_assoc($res);
                $count_total_filtered = $row["count_total"];
            }

            // check if order is set and update query
            $map = [
                "orders.timestamp_creation",
                "CONCAT(orders.name, orders.company_name)",
                "orders.email",
                "orders.phone",
                "CONCAT(orders.address, orders.postal_code, orders.city)",
                "orders.totalprice"
            ];
            if(!empty($_POST["order"])){
                $o = $_POST["order"];
                $order = [];
                for ($i=0; $i < count($o); $i++) { 
                    $order[] = $map[$o[$i]["column"]]." ".strtoupper($o[$i]["dir"]);
                }
                $query .= " ORDER BY ".implode(", ", $order);
            }

            // get all data
            $sql = 
                "SELECT orders.id, orders.timestamp_creation, orders.name, orders.company_name, orders.address, orders.postal_code, orders.city, orders.totalprice
                FROM orders
                ".$query."
                LIMIT ".$_POST["length"]."
                OFFSET ".$_POST["start"].";";
            $res = mysqli_query($con, $sql);
            $r = [];
            // loop thru all data and format if needed
            while($row = mysqli_fetch_assoc($res)){
                $name = $row["name"];
                if(!empty($row["company_name"])){
                    $name .= " | ".$row["company_name"];
                }
                $address = $row["address"].", ".$row["postal_code"]." ".$row["city"];
                $btns = 
                    '<div class="dropdown show">
                        <a class="btn btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink'.$row["id"].'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs text-dark"></i></a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink'.$row["id"].'">
                            <a class="btn btn-primary w-100" href="?view='.$row["id"].'">Bekijken <i class="fa fa-search"></i></a>            
                        </div>
                    </div>';
                // store all data in a variable
                $r[] = [
                    "id" => $row["id"],
                    "timestamp_creation" => $row["timestamp_creation"],
                    "name" => $name,
                    "address" => $address,
                    "totalprice" => str_replace(",00", ",-", "€".number_format($row["totalprice"], 2, ",", ".")),
                    "btns" => $btns
                ];
            }  
            
            // return all data
            print_r(json_encode([
                "draw" => (int)$_POST["draw"],
                "recordsTotal" => (int)$count_total,
                "recordsFiltered" => (int)$count_total_filtered,
                "data" => $r
            ]));
            break;
        case "getCategories":
            $r = [];
            $sql = 
                "SELECT COUNT(*) AS count_total
                FROM product_categories;";
            $res = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($res);
            $count_total = $row["count_total"];
            $count_total_filtered = $count_total;

            // check if search is set and update query
            $query = "WHERE product_categories.view = 1";
            if(!empty($_POST["search"]["value"])){
                $q = explode(" ", $_POST["search"]["value"]);
                for ($i=0; $i < count($q); $i++) { 
                    // add spaces between items if you want to ignore `motors` that finds a `motor seat`
                    $query .= " AND product_categories.name LIKE '%".$q[$i]."%'";
                }
                $sql = 
                    "SELECT COUNT(*) AS count_total
                    FROM product_categories
                    ".$query.";";
                $res = mysqli_query($con, $sql);
                $row = mysqli_fetch_assoc($res);
                $count_total_filtered = $row["count_total"];
            }

            // check if order is set and update query
            $map = [
                "product_categories.name"
            ];
            if(!empty($_POST["order"])){
                $o = $_POST["order"];
                $order = [];
                for ($i=0; $i < count($o); $i++) { 
                    $order[] = $map[$o[$i]["column"]]." ".strtoupper($o[$i]["dir"]);
                }
                $query .= " ORDER BY ".implode(", ", $order);
            }
            $query .= 
                " LIMIT ".$_POST["length"]."
                OFFSET ".$_POST["start"];

            $sql = 
                "SELECT product_categories.id, product_categories.name, product_categories.visible
                FROM product_categories
                ".$query.";";
            $res = mysqli_query($con, $sql);
            $categories = [];
            while($row = mysqli_fetch_assoc($res)){
                $btns = 
                    '<div class="dropdown show">
                        <a class="btn btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink'.$row["id"].'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs text-dark"></i></a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink'.$row["id"].'">
                            <a class="btn btn-primary w-100 mt-1" href="#" onclick=getSubCategory("'.$row["id"].'")>Subcategorieën <i class="fa fa-search"></i></a>
                            <a class="btn btn-warning w-100 mt-1" href="?editCategory='.$row["id"].'">Bewerken <i class="fa fa-pencil-alt"></i></a>
                            <a class="btn btn-danger w-100 mt-1" href="?delCategory='.$row["id"].'">Verwijderen <i class="fa fa-trash"></i></a>                        
                        </div>
                    </div>';
                $visible = '<i class="fa fa-times text-danger"></i>';
                if($row["visible"] == 1){
                    $visible = '<i class="fa fa-check text-success"></i>';
                }
                $r[] = [
                    "name" => $row["name"],
                    "visible" => $visible,
                    "btns" => $btns
                ];
            }
            // return all data
            print_r(json_encode([
                "draw" => (int)$_POST["draw"],
                "recordsTotal" => (int)$count_total,
                "recordsFiltered" => (int)$count_total_filtered,
                "data" => $r
            ]));
            break;  
        case "getSubCategories":
            $idpc = $_GET["idpc"];
            $r = [];
            $sql = 
                "SELECT COUNT(*) AS count_total
                FROM product_subcategories
                WHERE view = '1'
                AND idpc = '$idpc';";
            $res = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($res);
            $count_total = $row["count_total"];
            $count_total_filtered = $count_total;

            // check if search is set and update query
            $query = "WHERE product_subcategories.view = 1 AND product_subcategories.idpc = '$idpc'";
            if(!empty($_POST["search"]["value"])){
                $q = explode(" ", $_POST["search"]["value"]);
                for ($i=0; $i < count($q); $i++) { 
                    // add spaces between items if you want to ignore `motors` that finds a `motor seat`
                    $query .= " AND product_subcategories.name LIKE '%".$q[$i]."%'";
                }
                $sql = 
                    "SELECT COUNT(*) AS count_total
                    FROM product_subcategories
                    ".$query.";";
                $res = mysqli_query($con, $sql);
                $row = mysqli_fetch_assoc($res);
                $count_total_filtered = $row["count_total"];
            }

            // check if order is set and update query
            $map = [
                "product_subcategories.name"
            ];
            if(!empty($_POST["order"])){
                $o = $_POST["order"];
                $order = [];
                for ($i=0; $i < count($o); $i++) { 
                    $order[] = $map[$o[$i]["column"]]." ".strtoupper($o[$i]["dir"]);
                }
                $query .= " ORDER BY ".implode(", ", $order);
            }
            $query .= 
                " LIMIT ".$_POST["length"]."
                OFFSET ".$_POST["start"];

            $sql = 
                "SELECT product_subcategories.id, product_subcategories.name, product_subcategories.visible
                FROM product_subcategories
                ".$query.";";
            $res = mysqli_query($con, $sql);
            $categories = [];
            while($row = mysqli_fetch_assoc($res)){
                $btns = 
                    '<div class="dropdown show">
                        <a class="btn btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink'.$row["id"].'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cogs text-dark"></i></a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink'.$row["id"].'">
                            <a class="btn btn-warning w-100 mt-1" href="?editSubCategory='.$row["id"].'">Bewerken <i class="fa fa-pencil-alt"></i></a>
                            <a class="btn btn-danger w-100 mt-1" href="?delSubCategory='.$row["id"].'">Verwijderen <i class="fa fa-trash"></i></a>                        
                        </div>
                    </div>';
                $visible = '<i class="fa fa-times text-danger"></i>';
                if($row["visible"] == 1){
                    $visible = '<i class="fa fa-check text-success"></i>';
                }
                $r[] = [
                    "name" => $row["name"],
                    "visible" => $visible,
                    "btns" => $btns
                ];
            }
            // return all data
            print_r(json_encode([
                "draw" => (int)$_POST["draw"],
                "recordsTotal" => (int)$count_total,
                "recordsFiltered" => (int)$count_total_filtered,
                "data" => $r
            ]));
            break;
        case "getCategorySpecs":
            $idps = $_POST["idps"];

            $sql = 
                "SELECT product_category_specs.spec
                FROM product_category_specs
                WHERE product_category_specs.idps = ".$idps.";";
            $res = mysqli_query($con, $sql);
            $r = [];
            while($row = mysqli_fetch_assoc($res)){
                $r[] = $row["spec"];
            }
            print_r(json_encode($r));
            break; 
        case "getCategorieSubCategories":
            $idpc = $_POST["idpc"];

            $sql = 
                "SELECT product_subcategories.id, product_subcategories.name
                FROM product_subcategories
                WHERE product_subcategories.idpc = '$idpc';";
            $res = mysqli_query($con, $sql);
            $r = [];
            while($row = mysqli_fetch_assoc($res)){
                $r[] = $row;
            }
            print_r(json_encode($r));
            break;
        default:
            print_r(json_encode(["Error" => "Invalid Request"]));
            break; 
    }
    
    
}

?>