<?php

require_once("../assets/config/config.php");
require_once("./assets/php/page_creators/product.php");

$errors = [];

if(isset($_POST["add"])){
    // get variables
    $idpc = $_POST["idpc"];
    $idps = $_POST["idps"];
    $title = mysqli_real_escape_string($con, $_POST["title"]);
    $description = $_POST["description"];
    $code = mysqli_real_escape_string($con, $_POST["code"]);
    $price = mysqli_real_escape_string($con, $_POST["price"]);
    $stock = mysqli_real_escape_string($con, $_POST["stock"]);
    $spec_names = $_POST["spec_names"];
    $spec_values = $_POST["spec_values"];

    if(!file_exists("../www/products/".dclean($title)."")){
        // add product to database
        $sql = 
            "INSERT INTO products (`idpc`, `idps`, `title`, `description`, `code`, `price`, `stock`) 
                VALUES ('$idpc', '$idps', '$title', '$description', '$code', '$price', '$stock');";
        if(!mysqli_query($con, $sql)){
            $errors[] = "SQL error: ".mysqli_error($con);
        }

        // id product
        $idp = mysqli_insert_id($con);

        // add product_specs
        for ($i=0; $i < count($spec_names); $i++) { 
            $spec_name = $spec_names[$i];
            $spec_value = $spec_values[$i];
            if(!empty($spec_name) && !empty($spec_value)){
                $sql = 
                    "INSERT INTO product_specs (`idp`, `name`, `value`)
                        VALUES ('$idp', '$spec_name', '$spec_value');";
                mysqli_query($con, $sql);
            }
        }

        if(!empty($idp)){
            if(!mkdir("../www/assets/images/products/".$idp)){
                $errors[] = "Could not create folder for product images";
            }
        }else{
            $errors[] = "Could not get product id";
        }

        // create page
        mkdir("../www/products/".dclean($title));
        $file = fopen("../www/products/".dclean($title)."/index.php", "w");
        $txt = createPage_product($idp);
        fwrite($file, $txt);;
        fclose($file);

        // continue with images if no errors
        if(empty($errors)){
            if($_FILES["images"]["size"][0] > 0){
                for ($i=0; $i < count($_FILES["images"]["tmp_name"]); $i++) { 
                    mkdir("../www/assets/images/products/".$idp."/".$i);
                    mkdir("../www/assets/images/products/".$idp."/".$i."/SD");
                    mkdir("../www/assets/images/products/".$idp."/".$i."/HD");
                    $img = $_FILES["images"]["tmp_name"][$i];
                    // get extension
                    $exten = pathinfo($_FILES["images"]["name"][$i], PATHINFO_EXTENSION);
                    // create name            
                    $img_name = dclean($title).".".$exten;
                    // get size
                    $ext = getimagesize($img);
                    $height_mul = $ext[0] / $ext[1];
                    // new width and height without changing ratio
                    if($ext[0] > 300){
                        $new_height_sd = 300;
                    }else{
                        $new_height_sd = $ext[0];
                    }
                    if($ext[0] > 1800){
                        $new_height_hd = 1800;
                    }else{
                        $new_height_hd = $ext[0];
                    }
                    $new_width_sd = $new_height_sd * $height_mul;
                    $new_width_hd = $new_height_hd * $height_mul;
                    // frame
                    $frame_sd = imagecreatetruecolor($new_width_sd, $new_height_sd);
                    $frame_hd = imagecreatetruecolor($new_width_hd, $new_height_hd);
                    // switch per img extension for img creation
                    switch(strtolower($ext["mime"])){
                        case "image/png":
                            $kmg = imagecreatefrompng($img);
                            $kek_sd = imagecolorallocate($frame_sd, 255, 255, 255);
                            $kek_hd = imagecolorallocate($frame_hd, 255, 255, 255);
                            imagefill($frame_sd, 0, 0, $kek_sd);
                            imagefill($frame_hd, 0, 0, $kek_hd);
                        break;
                        case "image/jpeg":
                            $kmg = imagecreatefromjpeg($img);
                        break;
                        case "image/gif":
                            $kmg = imagecreatefromgif($img);
                            $kek_sd = imagecolorallocate($frame_sd, 255, 255, 255);
                            $kek_hd = imagecolorallocate($frame_hd, 255, 255, 255);
                            imagefill($frame_sd, 0, 0, $kek_sd);
                            imagefill($frame_hd, 0, 0, $kek_hd);
                        break;
                        case "image/webp":
                            $kmg = imagecreatefromwebp($img);
                        break;
                        // error for not supported extensions
                        default: $errors[] = "Bestand wordt niet ondersteund. DATA: img = ".$img;
                    }
                    // if no errors occured create and move the img
                    if(empty($errors)){
                        imagecopyresampled($frame_sd, $kmg, 0, 0, 0, 0, $new_width_sd, $new_height_sd, $ext[0], $ext[1]);
                        imagecopyresampled($frame_hd, $kmg, 0, 0, 0, 0, $new_width_hd, $new_height_hd, $ext[0], $ext[1]);
                        imagejpeg($frame_sd, "../www/assets/images/products/".$idp."/".$i."/SD/".$img_name, 100);
                        imagejpeg($frame_hd, "../www/assets/images/products/".$idp."/".$i."/HD/".$img_name, 100);
                        imagedestroy($frame_sd);
                        imagedestroy($frame_hd);
                    }
                }
            }
        }else{
            $errors[] = "Foto's konden niet worden toegevoegd.";
        }
    } else {
        $errors[] = "Deze product bestaat al. (Check de prullenbak)";
    }

    // error message
    $text = "<h5>Product is succesvol toegevoegd.</h5>";
    if(!empty($errors)){
        $text = "<h5>Fouten tijdens het toevoegen van het product:</h5><pre>".print_r($errors, true)."</pre>";
    }

    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug naar overzicht</a>';

    // create content with message
    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Toevoegen</h2>
                </div>
                <div class="card-body">
                    '.$text.'
                </div>
            </div>
        </div>';
}elseif(isset($_POST["edit"])){
    $idp = $_POST["edit"];
    $idpc = $_POST["idpc"];
    $idps = !empty($_POST["idps"]) ? $_POST["idps"] : "";

    // get variables
    $title = mysqli_real_escape_string($con, $_POST["title"]);
    $description = $_POST["description"];
    $code = mysqli_real_escape_string($con, $_POST["code"]);
    $price = mysqli_real_escape_string($con, $_POST["price"]);
    $stock = mysqli_real_escape_string($con, $_POST["stock"]);
    $spec_names = $_POST["spec_names"];
    $spec_values = $_POST["spec_values"];

    // get old name
    $sql = 
        "SELECT products.title
        FROM products
        WHERE products.id  = '$idp';";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);
    $oldname = $row["title"];

    if(!file_exists("../www/products/".dclean($title)."/") || $oldname == $title){
        rename("../www/products/".dclean($oldname), "../www/products/".dclean($title));
        $sql = 
            "UPDATE products
            SET `idpc` = '$idpc',
                `idps` = '$idps',
                `title` = '$title',
                `description` = '$description',
                `code` = '$code',
                `price` = '$price',
                `stock` = '$stock'
            WHERE `id` = '$idp';";
        if(!mysqli_query($con, $sql)){
            $errors[] = mysqli_error($con);
        }

        // first remove all product_specs
        $sql = "DELETE FROM product_specs WHERE `idp` = '$idp';";
        if(!mysqli_query($con, $sql)){
            $errors[] = mysqli_error($con);
        }
        // then add product_specs
        for ($i=0; $i < count($spec_names); $i++) { 
            $spec_name = $spec_names[$i];
            $spec_value = $spec_values[$i];
            if(!empty($spec_name) && !empty($spec_value)){
                $sql = 
                    "INSERT INTO product_specs (`idp`, `name`, `value`)
                        VALUES ('$idp', '$spec_name', '$spec_value');";
                mysqli_query($con, $sql);
            }
        }

        // if imgs got deleted
        if(isset($_POST["images_del"])){
            for ($i=0; $i < count($_POST["images_del"]); $i++) { 
                $del_img_sd = array_values(array_diff(scandir("../www/assets/images/products/".$idp."/".$_POST["images_del"][$i]."/SD"), array(".", "..")));
                $del_img_hd = array_values(array_diff(scandir("../www/assets/images/products/".$idp."/".$_POST["images_del"][$i]."/HD"), array(".", "..")));
                unlink("../www/assets/images/products/".$idp."/".$_POST["images_del"][$i]."/SD/".$del_img_sd[0]);
                unlink("../www/assets/images/products/".$idp."/".$_POST["images_del"][$i]."/HD/".$del_img_hd[0]);
                rmdir("../www/assets/images/products/".$idp."/".$_POST["images_del"][$i]."/SD/");
                rmdir("../www/assets/images/products/".$idp."/".$_POST["images_del"][$i]."/HD/");
                rmdir("../www/assets/images/products/".$idp."/".$_POST["images_del"][$i]);
            }
        }
        // if new images
        if($_FILES["images"]["size"][0] > 0){
            $j = 0;
            for ($i=0; $j < count($_FILES["images"]["tmp_name"]); $i++) {
                if(!file_exists("../www/assets/images/products/".$idp."/".$i)){
                    mkdir("../www/assets/images/products/".$idp."/".$i);
                    mkdir("../www/assets/images/products/".$idp."/".$i."/SD");
                    mkdir("../www/assets/images/products/".$idp."/".$i."/HD");

                    $img = $_FILES["images"]["tmp_name"][$j];
                    // get extension
                    $exten = pathinfo($_FILES["images"]["name"][$j], PATHINFO_EXTENSION);
                    // create name            
                    $img_name = dclean($title).".".$exten;
                    // get size
                    $ext = getimagesize($img);
                    $height_mul = $ext[0] / $ext[1];
                    // new width and height without changing ratio
                    if($ext[0] > 300){
                        $new_height_sd = 300;
                    }else{
                        $new_height_sd = $ext[0];
                    }
                    if($ext[0] > 1800){
                        $new_height_hd = 1800;
                    }else{
                        $new_height_hd = $ext[0];
                    }
                    $new_width_sd = $new_height_sd * $height_mul;
                    $new_width_hd = $new_height_hd * $height_mul;
                    // frame
                    $frame_sd = imagecreatetruecolor($new_width_sd, $new_height_sd);
                    $frame_hd = imagecreatetruecolor($new_width_hd, $new_height_hd);
                    // switch per img extension for img creation
                    switch(strtolower($ext["mime"])){
                        case "image/png":
                            $kmg = imagecreatefrompng($img);
                            $kek_sd = imagecolorallocate($frame_sd, 255, 255, 255);
                            $kek_hd = imagecolorallocate($frame_hd, 255, 255, 255);
                            imagefill($frame_sd, 0, 0, $kek_sd);
                            imagefill($frame_hd, 0, 0, $kek_hd);
                        break;
                        case "image/jpeg":
                            $kmg = imagecreatefromjpeg($img);
                        break;
                        case "image/gif":
                            $kmg = imagecreatefromgif($img);
                            $kek_sd = imagecolorallocate($frame_sd, 255, 255, 255);
                            $kek_hd = imagecolorallocate($frame_hd, 255, 255, 255);
                            imagefill($frame_sd, 0, 0, $kek_sd);
                            imagefill($frame_hd, 0, 0, $kek_hd);
                        break;
                        case "image/webp":
                            $kmg = imagecreatefromwebp($img);
                        break;
                        // error for not supported extensions
                        default: $errors[] = "Bestand wordt niet ondersteund. DATA: img = ".$img;
                    }
                    // if no errors occured create and move the img
                    if(empty($errors)){
                        imagecopyresampled($frame_sd, $kmg, 0, 0, 0, 0, $new_width_sd, $new_height_sd, $ext[0], $ext[1]);
                        imagecopyresampled($frame_hd, $kmg, 0, 0, 0, 0, $new_width_hd, $new_height_hd, $ext[0], $ext[1]);
                        imagejpeg($frame_sd, "../www/assets/images/products/".$idp."/".$i."/SD/".$img_name, 100);
                        imagejpeg($frame_hd, "../www/assets/images/products/".$idp."/".$i."/HD/".$img_name, 100);
                        imagedestroy($frame_sd);
                        imagedestroy($frame_hd);
                    }
                    $j++;
                }
            }
        }
    }else{
        $errors[] = "Deze product bestaat al. (Check de prullenbak)";
    }
    
    // error message
    $text = "<h5>Product is succesvol bewerkt.</h5>";
    if(!empty($errors)){
        $text = "<h5>Fouten tijdens het bewerken van het product:</h5><pre>".print_r($errors, true)."</pre>";
    }

    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug naar overzicht</a>';

    // create content with message
    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Bewerken</h2>
                </div>
                <div class="card-body">
                    '.$text.'
                </div>
            </div>
        </div>';
}elseif(isset($_POST["delete"])){
    $idp = $_POST["delete"];

    $sql = 
        "UPDATE products
        SET `view` = 0
        WHERE `id` = '$idp';";
    if(!mysqli_query($con, $sql)){
        $errors[] = mysqli_error($con);
    }

    // error message
    $text = "<h5>Product is succesvol verwijderd.</h5>";
    if(!empty($errors)){
        $text = "<h5>Fouten tijdens het verwijderen van het product:</h5><pre>".print_r($errors, true)."</pre>";
    }

    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug naar overzicht</a>';

    // create content with message
    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Verwijderen</h2>
                </div>
                <div class="card-body">
                    '.$text.'
                </div>
            </div>
        </div>';
}elseif(isset($_GET["add"])){
    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug</a>';

    $category_options = "";
    $sql = 
        "SELECT product_categories.id, product_categories.name
        FROM product_categories
        WHERE product_categories.view = 1
        ORDER BY product_categories.name ASC;";
    $res = mysqli_query($con, $sql);
    while($row = mysqli_fetch_assoc($res)){
        $category_options .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
    }

    $js .= 
        'addRowSpecs();
        tinymce.init({
            selector: "#description"
        });';

    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Toevoegen</h2>
                        <p class="m-b-0 text-muted">Vul hier de gegevens in voor het product. Alle velden met een * zijn verplicht.</p>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" id="productsForm">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-row">
                                    <div class="col-12 mb-2">
                                        <h5>Productgegevens</h5>
                                        <hr class="w-100">
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <label for="idpc">Categorie <i class="fa fa-spinner fa-spin d-none" id="category_loader"></i></label>
                                        <select class="form-control" id="idpc" name="idpc" required="true">
                                            <option value="">Selecteer een categorie</option>
                                            '.$category_options.'
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <label for="idps">Subcategorie <i class="fa fa-spinner fa-spin d-none" id="subcategory_loader"></i></label>
                                        <select class="form-control" id="idps" name="idps">
                                            <option value="">Selecteer een subcategorie</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <label for="title">Titel</label>
                                        <input class="form-control" id="title" name="title" type="text" required="true">
                                    </div>
                                    <div class="col-12 col-md-4 mb-2">
                                        <label for="code">Code</label>
                                        <input class="form-control" id="code" name="code" type="text">
                                    </div>
                                    <div class="col-12 col-md-4 mb-2">
                                        <label for="price">Prijs</label>
                                        <input class="form-control" id="price" name="price" type="number" step=".01" min="0" required="true">
                                    </div>
                                    <div class="col-12 col-md-4 mb-2">
                                        <label for="stock">Voorraad</label>
                                        <input class="form-control" id="stock" name="stock" type="number" step=".001" min="0" required="true">
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label for="images">Afbeeldingen</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="images">Max. 5Mb</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="images" name="images[]" accept="image/*" multiple>
                                                <label class="custom-file-label" for="images">Kies afbeeldingen</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 mb-2">
                                        <label for="description">Beschrijving</label>
                                        <textarea class="form-control" rows="12" name="description" id="description" aria-describedby="descriptionHelp" placeholder="Beschrijving"></textarea>
                                    </div>
                                </div>  
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-row">
                                    <div class="col-12 mb-2">
                                        <h5>Specificaties</h5>
                                        <hr class="w-100">
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="tableSpecs">
                                                <thead>
                                                    <tr>
                                                        <th>Naam</th>
                                                        <th>Waarde</th>
                                                        <th><i class="fa fa-trash text-danger"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3"><button type="button" class="btn btn-success w-100" onclick="addRowSpecs();">Rij toevoegen</button></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <button class="btn btn-primary btn-lg mt-3 float-right" type="submit" name="add">Toevoegen</button>
                            </div>
                        </div>
                    </form> 
                </div>
            </div>
        </div>';
}elseif(isset($_GET["edit"])){
    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug</a>';
    
    $category_options = "";
    $sql = 
        "SELECT product_categories.id, product_categories.name
        FROM product_categories
        WHERE product_categories.view = 1
        ORDER BY product_categories.name ASC;";
    $res = mysqli_query($con, $sql);
    while($row = mysqli_fetch_assoc($res)){
        $category_options .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
    }

    $idp = $_GET["edit"];
    $sql = 
        "SELECT products.idpc, products.idps, products.title, products.description, products.code, products.price, products.stock
        FROM products
        WHERE products.id = '$idp';";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);

    $idps = $row["idps"];
    $idpc = $row["idpc"];

    $title = htmlentities($row["title"], ENT_QUOTES);
    $description = $row["description"];
    $code = htmlentities($row["code"], ENT_QUOTES);
    $price = htmlentities($row["price"], ENT_QUOTES);
    $stock = htmlentities($row["stock"], ENT_QUOTES);

    $subcategory_options = "";
    $sql = 
        "SELECT product_subcategories.id, product_subcategories.name
        FROM product_subcategories
        WHERE product_subcategories.view = 1
        AND product_subcategories.idpc = '$idpc'
        ORDER BY product_subcategories.name ASC;";
    $res = mysqli_query($con, $sql);
    while($row = mysqli_fetch_assoc($res)){
        $subcategory_options .= '<option value="'.$row["id"].'">'.$row["name"].'</option>';
    }

    $sql = 
        "SELECT product_specs.name, product_specs.value
        FROM product_specs
        WHERE product_specs.idp = '$idp';";
    $res = mysqli_query($con, $sql);
    while($row = mysqli_fetch_assoc($res)){
        $name = htmlentities($row["name"], ENT_QUOTES);
        $value = htmlentities($row["value"], ENT_QUOTES);
        $js .= 'addRowSpecs("'.$name.'", "'.$value.'");';
    }
    
    $js .= 
        '$("#idpc").val('.$idpc.');
        tinymce.init({
            selector: "#description"
        });
        $("#idps").val('.$idps.');';

    // get all current images (in case of deleting)
    $dirs = array_values(array_diff(scandir("../www/assets/images/products/".$idp), ["..", "."]));
    $images = "";
    if(count($dirs) > 0){
        for ($i=0; $i < count($dirs); $i++) {
            $dir = $dirs[$i];
            $image = array_values(array_diff(scandir("../www/assets/images/products/".$idp."/".$dir."/SD"), ["..", "."]));
            $images .= 
                '<div class="col-6 col-md-4">
                    <label class="image-box-custom mb-0">
                        <div class="image-box-custom-wrapper d-none">
                            <i class="fa fa-trash text-danger"></i>
                        </div>
                        <input type="checkbox" name="images_del[]" class="d-none" value="'.$dir.'">
                        <span class="image-box-custom-content">
                            <img src="https://www.mskstores.nl/assets/images/products/'.$idp.'/'.$dir.'/SD/'.$image[0].'">
                        </span>
                    </label>
                </div>';
        }
    }else{
        $images .= 
            '<div class="col-12">
                <small>Er zijn geen afbeeldingen om te verwijderen.</small>
            </div>';
    } 

    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Bewerken</h2>
                        <p class="m-b-0 text-muted">Vul hier de gegevens in voor het product. Alle velden met een * zijn verplicht.</p>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" id="productsForm">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-row">
                                    <div class="col-12 mb-2">
                                        <h5>Productgegevens</h5>
                                        <hr class="w-100">
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <label for="idpc">Categorie <i class="fa fa-spinner fa-spin d-none" id="category_loader"></i></label>
                                        <select class="form-control" id="idpc" name="idpc" required="true">
                                            <option value="">Selecteer een categorie</option>
                                            '.$category_options.'
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <label for="idps">Subcategorie <i class="fa fa-spinner fa-spin d-none" id="subcategory_loader"></i></label>
                                        <select class="form-control" id="idps" name="idps">
                                            <option value="">Selecteer een subcategorie</option>
                                            '.$subcategory_options.'
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <label for="title">Titel</label>
                                        <input class="form-control" id="title" value="'.$title.'"  name="title" type="text" required="true">
                                    </div>
                                    <div class="col-12 col-md-4 mb-2">
                                        <label for="code">Code</label>
                                        <input class="form-control" id="code" value="'.$code.'" name="code" type="text">
                                    </div>
                                    <div class="col-12 col-md-4 mb-2">
                                        <label for="price">Prijs</label>
                                        <input class="form-control" id="price" value="'.$price.'" name="price" type="number" step=".01" min="0" required="true">
                                    </div>
                                    <div class="col-12 col-md-4 mb-2">
                                        <label for="stock">Voorraad</label>
                                        <input class="form-control" id="stock" value="'.$stock.'" name="stock" type="number" step=".001" min="0" required="true">
                                    </div>
                                    <div class="col-12 mb-2">
                                        <label for="images">Afbeeldingen</label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="images">Max. 5Mb</span>
                                            </div>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="images" name="images[]" accept="image/*" multiple>
                                                <label class="custom-file-label" for="images">Kies afbeeldingen</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="images_del">Huidige afbeeldingen (klik om te verwijderen)</label>
                                        <div class="form-group">
                                            <div class="row gutters-sm">
                                                '.$images.'
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 mb-2">
                                        <label for="description">Beschrijving</label>
                                        <textarea class="form-control" rows="12" name="description" id="description" aria-describedby="descriptionHelp" placeholder="Beschrijving">
                                            '.$description.'
                                        </textarea>
                                    </div>
                                </div>  
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-row">
                                    <div class="col-12 mb-2">
                                        <h5>Specificaties</h5>
                                        <hr class="w-100">
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="tableSpecs">
                                                <thead>
                                                    <tr>
                                                        <th>Naam</th>
                                                        <th>Waarde</th>
                                                        <th><i class="fa fa-trash text-danger"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3"><button type="button" class="btn btn-success w-100" onclick="addRowSpecs();">Rij toevoegen</button></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <button class="btn btn-warning btn-lg mt-3 float-right" type="submit" name="edit" value="'.$idp.'">Bewerken</button>
                            </div>
                        </div>
                    </form> 
                </div>
            </div>
        </div>';
}elseif(isset($_GET["delete"])){
    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug</a>';

    $idp = $_GET["delete"];

    $cont .= 
        '<div class="col-12">
            <div class="card pt-2 pb-2 mb-5">
                <div class="card-body">
                    <form method="POST">
                        <h2>Verwijderen</h2>
                        <p>Weet je zeker dat je dit product wilt verwijderen?</p>
                        <button class="btn btn-danger" name="delete" value="'.$idp.'">Verwijderen <i class="fa fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>';
}elseif(isset($_GET["view"])){
    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug</a>';

    $idp = $_GET["view"];

    $cont .= 
        '<div class="col-12">
            <div class="card pt-2 pb-2 mb-5">
                <div class="card-body">
                    '.$idp.'
                </div>
            </div>
        </div>';
}else{
    $header_btns .= 
        '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?add"><i class="fa fa-plus m-r-5"></i> Toevoegen</a>';
    $cont .= 
        '<div class="col-12">
            <div class="card pt-2 pb-2 mb-5">
                <div class="card-body table-responsive">
                    <table class="table table-hover" id="tableProducts">
                        <thead>
                            <tr>
                                <th>Categorie</th>
                                <th>Titel</th>
                                <th>Code</th>
                                <th>Prijs</th>
                                <th>Voorraad</th>
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
        "var table = $('#tableProducts').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Dutch.json'
            },
            order: [[1, 'asc']],
            stateSave: true,
            columnDefs: [{
                orderable: false, 
                targets: [5] 
            }],
            columns: [ 
                { data: 'category_name' },
                { data: 'title' },
                { data: 'code' },
                { data: 'price' },
                { data: 'stock' },
                { data: 'btns' }
            ],
            lengthMenu: [
                [5, 10, 25, 50, 100, 500, 1000, 10000],
                [5, 10, 25, 50, 100, 500, 1000, 'Alles']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: './assets/php/ajax.php?request=getProducts',
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
    <title>Producten - MSK Stores</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon.png"/>
    <link rel="icon" type="image/png" href="./assets/img/favicon.png" sizes="16x16">

    <!-- CSS Vendor start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/atmos.min.css">                           <!-- Bootstrap + Admin CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,500,600">       <!--Google Font-->
    <!-- CSS Vendor end -->

    <!-- CSS start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/pace.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/jquery-scrollbar.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/dataTables-bootstrap4.min.css">
    <!-- CSS end -->

    <style>
        .image-box-custom-wrapper{
            position: absolute;
            height: 100%;
            width: calc(100% - 30px);
            left: 15px;
            top: 0;
            background: rgba(0,0,0,0.3);
        }
        .image-box-custom-wrapper i{
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center; 
            font-size: 30px;
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
    <script src="./assets/js/dataTables.min.js"></script>
    <script src="./assets/js/pace.min.js"></script>
    <script src="./assets/js/jquery-scrollbar.min.js"></script>
    <script src="./assets/js/daterangepicker.js"></script>
    <script src="./assets/js/select2-full.min.js"></script>
    <script src="./assets/js/listjs.min.js"></script>
    <script src="./assets/js/atmos.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/doo5djb40mmauhzfpvjl0ajsy52sipun4pxr623t30tw1wbq/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://kit.fontawesome.com/f3f57d50bc.js" crossorigin="anonymous"></script>
    <!-- JS end -->

    <script>
        // delete images
        $(".image-box-custom input").on("change", function(){
            if($(this).is(":checked")){
                $(this).prev().removeClass("d-none");
            }else{
                $(this).prev().addClass("d-none");
            }
        });

        $("#idpc").on("change", function(){
            var idpc = $(this).val();
            if(idpc != ""){
                $("#category_loader").removeClass("d-none");
                $.ajax({
                    url: "./assets/php/ajax.php?request=getCategorieSubCategories",
                    type: "POST",
                    data: {
                        idpc: idpc
                    },
                    success: function(data){
                        $("#idps").empty();
                        $("#idps").append("<option value=''>Selecteer een subcategorie</option>");
                        if(data.length > 0){
                            for (let i = 0; i < data.length; i++) {
                                $("#idps").append("<option value='"+data[i].id+"'>"+data[i].name+"</option>");
                            }
                        }              
                    },
                    complete: function(){
                        $("#category_loader").addClass("d-none");
                    },
                    error: function(err){
                        console.log(err);
                    }
                });
            }
        });

        // change category
        $("#idps").on("change", function(){
            var idps = $(this).val();
            if(idps != ""){
                $("#subcategory_loader").removeClass("d-none");
                $.ajax({
                    url: "./assets/php/ajax.php?request=getCategorySpecs",
                    type: "POST",
                    data: {
                        idps: idps
                    },
                    success: function(data){
                        $("#tableSpecs tbody").empty();
                        if(data.length > 0){
                            for (let i = 0; i < data.length; i++) {
                                addRowSpecs(data[i]);
                            }
                        }else{
                            addRowSpecs();
                        }
                    },
                    complete: function(){
                        $("#subcategory_loader").addClass("d-none");
                    },
                    error: function(err){
                        console.log(err);
                    }
                });
            } else {
                $("#tableSpecs tbody").empty();
                addRowSpecs();
            }
        });

        function addRowSpecs(name = "", value = ""){
            $("#tableSpecs tbody").append(
                `<tr>
                    <td><input class="form-control" name="spec_names[]" value="` + name + `" required></td>
                    <td><input class="form-control" name="spec_values[]" value="` + value + `" required></td>
                    <td><button type="button" class="btn" onclick="$(this).parent().parent().remove();"><i class="fa fa-trash text-danger"></i></button></td>
                </tr>`
            );
        }
    </script>

    <!-- Additional JS commands by PHP start -->
    <?php require_once("./assets/php/main.php");?>
    <!-- Additional JS commands by PHP end -->

</body>
</html>