<?php

$cont = $header_btns = "";

$show_errors = true;
$coming_soon = false;
$under_construction = false;
if($show_errors == true){
	ini_set("display_errors", 1);
	ini_set("display_startup_errors", 1);
	error_reporting(E_ALL);	
}else{
	ini_set("display_errors", 0);
	ini_set("display_startup_errors", 0);
}

$con = mysqli_connect("212.227.115.76:3306", "mskstores.nl", "9q#Sjh442", "mskstores.nl");

// domain & subdomain in same session if not on localhost
if(!in_array($_SERVER["REMOTE_ADDR"], ["127.0.0.1", "::1"])) {
	ini_set("session.cookie_domain", ".mskstores.nl");
}

// init
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
date_default_timezone_set("Europe/Amsterdam");
mysqli_set_charset($con, "UTF8");

$js = "";

// get current directorry & page
$current_page = explode("/", $_SERVER["REQUEST_URI"]);
$current_dir = $current_page[count($current_page) - 2];
if(isset($current_page[count($current_page) - 1])){
	$current_page = $current_page[count($current_page) - 1];
	$current_page_ex = explode("?", $current_page);
	$current_page = $current_page_ex[0];
}else{
	$current_page = "";
}

// redirect if $coming_soon == true || $cunder_construction == true
if($coming_soon == true && !isset($_SESSION["user_id"]) && $current_page != "coming-soon.php" && $current_page != "login.php"){
	header("Location: https://www.mskstores.nl/coming-soon.php");
}elseif($under_construction == true && !isset($_SESSION["user_id"]) && $current_page != "under-construction.php" && $current_page != "login.php"){
	header("Location: https://www.mskstores.nl/under-construction.php");
}

// CART VARIABLES
if(!isset($_SESSION["cart"])){
	$_SESSION["cart"] = array();
}
$cart_products = "";

// get cart products
if(count($_SESSION["cart"]) > 0){
	for($i = 0; $i < count($_SESSION["cart"]); $i++){
		$id = $_SESSION["cart"][$i];

		$sql = "SELECT * FROM products WHERE id = '$id'";
		$result = mysqli_query($con, $sql);
		while($row = mysqli_fetch_assoc($result)){
			$cart_products .= '<div class="product">
								<div class="product-cart-details">
									<h4 class="product-title">
										<a>'.$row["title"].'</a>
									</h4>

									<span class="cart-product-info">
										<span class="cart-product-qty">1</span>
										x $84.00
									</span>
								</div><!-- End .product-cart-details -->

								<figure class="product-image-container">
									<a href="product.html" class="product-image">
										<img src="assets/images/products/product-1.jpg" alt="product">
									</a>
								</figure>
								<a href="javascript:removeFromCart('.$row["id"].');" class="btn-remove" title="Verwijderen"><i class="icon-close"></i></a>
							</div><!-- End .product -->';
		}
	}
}



// This function cleans the inputted string to a string that may be used as a directory name
// Returns false if string is not usable as a directory name

function dclean($str){
	// Array of forbidden directory names
	$forbidden_names = array("CON","PRN","AUX","NUL","COM1","COM2","COM3","COM4","COM5","COM6","COM7","COM8","COM9","LPT1","LPT2","LPT3","LPT4","LPT5","LPT6","LPT7","LPT8","LPT9");
	$forbidden_chars = array("<",">",":",'"',"/","\\","|","?", "*","}", "#");	// Array of forbidden characters for directories and "#" because it give errors sometimes
	
	while (substr($str,-1) == " " || substr($str,-1) == ".") {					// Loop that removes consecutive dots and spaces
		$str = rtrim($str, ".");												// Remove dot at end of string
		$str = trim($str);														// Remove space at beginning and ending of string
	}
	$str = str_replace($forbidden_chars, "", $str);								// Delete forbidden characters
	$str = preg_replace("/\s+/", " ", $str);									// Delete multiple spaces
	if(strlen($str) > 200){														// Get first 200 chars if filename is too long
		$str = substr($str, 0, 200);
	}
	$str = str_replace(" ", "-", $str);											// Replace spaces with dashes
	if (in_array(strtoupper($str), $forbidden_names) || empty($str)) {			// Return false if string is still fobidden or empty
		return false;
	}else{
		return strtolower($str);
	}
}


// This function cleans the inputted string to a string that may be used as a directory name
// Returns false if string is not usable for directory name

function finic_hash($str){
	return hash("sha512", crypt($str, "MsKStoRes"));
}

// formats the date in Dutch
function dateFormat($date, $n = true){
	$d = explode("-", $date);
	$m = ["Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December"];
	$k = $m[$d[1]-1];	// month name
	$e = " ";	// delimiter
	if($n == true){
		$k = $d[1];
		$e = "-";
	}
	return $d[2].$e.$k.$e.$d[0];
}

/*
	returns the roles of the current user by SESSION["user_id"]
	returns true or false if isset parameter
	
	ex1: checkRoles() => ARRAY
	ex2: checkRoles("admin") => BOOLEAN
	ex3: checkRoles(["admin", "accountant"]) => BOOLEAN 

*/
function checkRoles($str = ""){
	global $con;
	$r = [];
	if(isset($_SESSION["user_id"])){
		$id = $_SESSION["user_id"];
		$sql = 
			"SELECT user_roles.role
			FROM user_roles
			WHERE user_roles.idu = '$id';";
		$res = mysqli_query($con, $sql);
		while($row = mysqli_fetch_assoc($res)){
			$r[] = $row["role"];
		}
		// if parameter is array
		if(is_array($str)){
			if(count(array_diff($str, $r)) < count($str)){
				return true;
			}else{
				return false;
			}
		}else{
			if(!empty($str)){
				// if parameter is string
				if(in_array($str, $r)){
					return true;
				}else{
					return false;
				}
			}else{
				// if parameter is empty
				return false;
			}
		}
	}else{
		return false;
	}
}

?>