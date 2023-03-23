<?php

require_once("../assets/config/config.php");
require_once("./assets/php/page_creators/category.php");
require_once("./assets/php/page_creators/subcategory.php");

if(isset($_POST["addSubCategory"])){
    // id of subcategory
    $idpc = $_POST["addSubCategory"];
    // name of subcategory
    $name = mysqli_real_escape_string($con, $_POST["name"]);

    // name of category
    $sql = 
        "SELECT product_categories.name
        FROM product_categories
        WHERE product_categories.id = $idpc;";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);
    $category_name = $row["name"];

    if(!file_exists("../www/shop/".dclean($category_name)."/".dclean($name)."/")){
        $idpc = $_POST["addSubCategory"];
        $name = mysqli_real_escape_string($con, $_POST["name"]);
        $visible = 0;
        if(isset($_POST["visible"])){
            $visible = 1;
        }

        $specs = [];
        if(isset($_POST["specs"])){
            $specs = $_POST["specs"];
        }

        // create sql
        $sql = 
            "INSERT INTO product_subcategories (`name`, `idpc`, `visible`) VALUES ('$name', '$idpc', '$visible');";
        if(!mysqli_query($con, $sql)){
            $errors[] = mysqli_error($con);
        }

        // id of subcategory
        $idps = mysqli_insert_id($con);

        for ($i=0; $i < count($specs); $i++) { 
            $spec = $specs[$i];
            $sql = "INSERT INTO product_category_specs (`idps`, `spec`) VALUES ('$idps', '$spec')";
            if(!mysqli_query($con, $sql)){
                $errors[] = mysqli_error($con);
            }
        }

        // create page
        mkdir("../www/shop/".dclean($category_name)."/".dclean($name));
        $handle = fopen("../www/shop/".dclean($category_name)."/".dclean($name)."/index.php", "w");
        fwrite($handle, createPage_subcategory($idps));
        fclose($handle);

    } else {
        $errors[] = "Deze subcategorie bestaat al.";
    }

    // error message
    $text = '<h5>Subcategorie is succesvol toegevoegd.</h5>';
    if(!empty($errors)){
        $text = "<h5>Fouten tijdens het toevoegen van de subcategorie:</h5><pre>".print_r($errors, true)."</pre>";
    }

    // create content with message
    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Subcategorie toevoegen</h2>
                </div>
                <div class="card-body">
                    '.$text.'
                    <a href="?" class="btn btn-primary">Terug naar overzicht</a>
                </div>
            </div>
        </div>';
    
}elseif(isset($_POST["addCategory"])){
    $name = mysqli_real_escape_string($con, $_POST["name"]);
    if(!file_exists("../www/shop/".dclean($name)."/")){
        $visible = 0;
        if(isset($_POST["visible"])){
            $visible = 1;
        }

        // create sql
        $sql = 
            "INSERT INTO product_categories (`name`, `visible`) VALUES ('$name', '$visible');";
        if(!mysqli_query($con, $sql)){
            $errors[] = mysqli_error($con);
        }

        // id of category
        $idpc = mysqli_insert_id($con);

        // create page
        mkdir("../www/shop/".dclean($name));
        $file = fopen("../www/shop/".dclean($name)."/index.php", "w");
        $txt = createPage_category($idpc);
        fwrite($file, $txt);;
        fclose($file);
    } else {
        $errors[] = "Deze categorie bestaat al. (Check de prullenbak)";
    }

    // error message
    $text = '<h5>Categorie is succesvol toegevoegd.</h5>';
    if(!empty($errors)){
        $text = "<h5>Fouten tijdens het toevoegen van de categorie:</h5><pre>".print_r($errors, true)."</pre>";
    }

    // create content with message
    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Categorie toevoegen</h2>
                </div>
                <div class="card-body">
                    '.$text.'
                    <a href="?" class="btn btn-primary">Terug naar overzicht</a>
                </div>
            </div>
        </div>';
}elseif(isset($_POST["editSubCategory"])){
    $idps = $_POST["editSubCategory"];
    $name = mysqli_real_escape_string($con, $_POST["name"]);

    // get old name
    $sql = 
        "SELECT product_subcategories.name, product_categories.name AS product_category, product_categories.id
        FROM product_subcategories, product_categories
        WHERE product_subcategories.id  = '$idps'
        AND product_subcategories.idpc = product_categories.id;";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);
    $oldname = $row["name"];
    $nameC = $row["product_category"];

    if(!file_exists("../www/shop/".dclean($nameC)."/".dclean($name)."/") || $oldname == $name){
        rename("../www/shop/".dclean($nameC)."/".dclean($oldname), "../www/shop/".dclean($nameC)."/".dclean($name));
    
        $visible = 0;
        if(isset($_POST["visible"])){
            $visible = 1;
        }
        $specs = [];
        if(isset($_POST["specs"])){
            $specs = $_POST["specs"];
        }

        // create sql
        $sql = 
            "UPDATE product_subcategories 
            SET `name`='$name',
                `visible` = '$visible' 
            WHERE `id`= '$idps';";
        if(!mysqli_query($con, $sql)){
            $errors[] = mysqli_error($con);
        }

        $sql = "DELETE FROM product_category_specs WHERE `idps` = '$idps'";
        mysqli_query($con, $sql);

        for ($i=0; $i < count($specs); $i++) { 
            $spec = $specs[$i];
            $sql = "INSERT INTO product_category_specs (`idps`, `spec`) VALUES ('$idps', '$spec')";
            if(!mysqli_query($con, $sql)){
                $errors[] = mysqli_error($con);
            }
        }
    } else {
        $errors[] = "Deze subcategorie bestaat al. (Check de prullenbak)";
    }

    // error message
    $text = '<h5>Subcategorie is succesvol bewerkt.</h5>';
    if(!empty($errors)){
        $text = "<h5>Fouten tijdens het bewerken van de subcategorie:</h5><pre>".print_r($errors, true)."</pre>";
    }

    // create content with message
    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Subcategorie bewerken</h2>
                </div>
                <div class="card-body">
                    '.$text.'
                    <a href="?" class="btn btn-primary">Terug naar overzicht</a>
                </div>
            </div>
        </div>';
}elseif(isset($_POST["editCategory"])){
    $idpc = $_POST["editCategory"];
    $name = mysqli_real_escape_string($con, $_POST["name"]);

    // get old name
    $sql = 
        "SELECT product_categories.name
        FROM product_categories
        WHERE product_categories.id  = '$idpc';";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);
    $oldname = $row["name"];

    if(!file_exists("../www/shop/".dclean($_POST["name"])."/") || $oldname == $_POST["name"]){
        rename("../www/shop/".dclean($oldname), "../www/shop/".dclean($_POST["name"]));
        $visible = 0;
        if(isset($_POST["visible"])){
            $visible = 1;
        }

        // create sql
        $sql = 
            "UPDATE product_categories 
            SET `name`='$name',
                `visible` = '$visible' 
            WHERE `id`= '$idpc';";
        if(!mysqli_query($con, $sql)){
            $errors[] = mysqli_error($con);
        }
    } else {
        $errors[] = "Deze categorie bestaat al.";
    }

    // error message
    $text = '<h5>Categorie is succesvol bewerkt.</h5>';
    if(!empty($errors)){
        $text = "<h5>Fouten tijdens het bewerken van de Categorie:</h5><pre>".print_r($errors, true)."</pre>";
    }

    // create content with message
    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Categorie bewerken</h2>
                </div>
                <div class="card-body">
                    '.$text.'
                    <a href="?" class="btn btn-primary">Terug naar overzicht</a>
                </div>
            </div>
        </div>';
}elseif(isset($_POST["delSubCategory"])){
    $idps = $_POST["delSubCategory"];

    $sql = 
        "UPDATE product_subcategories
        SET `view` = 0
        WHERE `id` = '$idps';";
    if(!mysqli_query($con, $sql)){
        $errors[] = mysqli_error($con);
    }

    // error message
    $text = '<h5>Subcategorie is succesvol verwijderd.</h5>';
    if(!empty($errors)){
        $text = "<h5>Fouten tijdens het verwijderen van de subcategorie:</h5><pre>".print_r($errors, true)."</pre>";
    }

    // create content with message
    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Subcategorie Verwijderen</h2>
                </div>
                <div class="card-body">
                    '.$text.'
                    <a href="?" class="btn btn-primary">Terug naar overzicht</a>
                </div>
            </div>
        </div>';
}elseif(isset($_POST["delCategory"])){
    $idpc = $_POST["delCategory"];

    $sql = 
        "UPDATE product_categories
        SET `view` = 0
        WHERE `id` = '$idpc';";
    if(!mysqli_query($con, $sql)){
        $errors[] = mysqli_error($con);
    }

    // error message
    $text = '<h5>Categorie is succesvol verwijderd.</h5>';
    if(!empty($errors)){
        $text = "<h5>Fouten tijdens het verwijderen van de categorie:</h5><pre>".print_r($errors, true)."</pre>";
    }

    // create content with message
    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Categorie Verwijderen</h2>
                </div>
                <div class="card-body">
                    '.$text.'
                    <a href="?" class="btn btn-primary">Terug naar overzicht</a>
                </div>
            </div>
        </div>';
}elseif(isset($_GET["addSubCategory"])){
    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug</a>';
    $idpc = $_GET["addSubCategory"];

    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Toevoegen</h2>
                    <p class="m-b-0 text-muted">Vul hier de gegevens in van het product. Alle velden met een * zijn verplicht.</p>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-row">
                                    <div class="col-12">
                                        <h5>Gegevens</h5>
                                    </div>
                                    <div class="col-12 col-md-9 mb-3">
                                        <label for="name">Naam subcategorie*</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="m-b-10 text-center">
                                            <label for="visible">Zichtbaar</label>
                                            <label class="cstm-switch d-block">
                                                <input type="checkbox" class="cstm-switch-input" name="visible" id="visible" checked="" value="1" >
                                                <span class="cstm-switch-indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <h5>Specificaties</h5>
                                <div class="table-responsive">
                                    <table class="table" id="tableSpecs">
                                        <thead>
                                            <tr>
                                                <th>Specificatie</th>
                                                <th>Actie</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><input class="form-control" id="add_spec_value"></td>
                                                <td><button class="ml-1 btn btn-success" type="button" onclick="addSpecRow();">Rij toevoegen <i class="fa fa-plus"></i></button></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary btn-lg mt-3 float-right" value='.$idpc.' type="submit" name="addSubCategory">Subcategorie toevoegen</button>                           
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
}elseif(isset($_GET["addCategory"])){
    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug</a>';

    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Toevoegen</h2>
                    <p class="m-b-0 text-muted">Vul hier de gegevens in van het product. Alle velden met een * zijn verplicht.</p>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-row">
                                    <div class="col-12">
                                        <h5>Gegevens</h5>
                                    </div>
                                    <div class="col-12 col-md-9 mb-3">
                                        <label for="name">Naam categorie*</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="m-b-10 text-center">
                                            <label for="visible">Zichtbaar</label>
                                            <label class="cstm-switch d-block">
                                                <input type="checkbox" class="cstm-switch-input" name="visible" id="visible" checked="" value="1" >
                                                <span class="cstm-switch-indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary btn-lg mt-3 float-right" type="submit" name="addCategory">Categorie toevoegen</button>                           
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
}elseif(isset($_GET["editSubCategory"])){
    $idps = $_GET["editSubCategory"];
    $sql = 
        "SELECT product_subcategories.name, product_subcategories.visible
        FROM product_subcategories
        WHERE product_subcategories.id = '$idps';";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);

    $name = $row["name"];
    $visible = $row["visible"];

    $checked = "";
    if($visible == 1){
        $checked = " checked";
    }

    $sql = 
        "SELECT product_category_specs.spec
        FROM product_category_specs
        WHERE product_category_specs.idps = '$idps';";
    $res = mysqli_query($con, $sql);
    $specs = "";
    while($row = mysqli_fetch_assoc($res)){
        $specs .= 
            '<tr>
                <td><input type="text" class="form-control" name="specs[]" value="'.$row["spec"].'"></td>
                <td><button class="btn btn-danger" onclick="$(this).parent().parent().remove();"><i class="fa fa-trash"></i></button></td>
            </tr>';
    }

    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug</a>';

    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Toevoegen</h2>
                    <p class="m-b-0 text-muted">Vul hier de gegevens in van het product. Alle velden met een * zijn verplicht.</p>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-row">
                                    <div class="col-12">
                                        <h5>Gegevens</h5>
                                    </div>
                                    <div class="col-12 col-md-9 mb-3">
                                        <label for="name">Naam subcategorie*</label>
                                        <input type="text" class="form-control" id="name" name="name" required value="'.$name.'">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="m-b-10 text-center">
                                            <label for="visible">Zichtbaar</label>
                                            <label class="cstm-switch d-block">
                                                <input type="checkbox" class="cstm-switch-input" name="visible" id="visible"'.$checked.' value="1" >
                                                <span class="cstm-switch-indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <h5>Specificaties</h5>
                                <div class="table-responsive">
                                    <table class="table" id="tableSpecs">
                                        <thead>
                                            <tr>
                                                <th>Specificatie</th>
                                                <th>Actie</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$specs.'
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><input class="form-control" id="add_spec_value"></td>
                                                <td><button class="ml-1 btn btn-success" type="button" onclick="addSpecRow();">Rij toevoegen <i class="fa fa-plus"></i></button></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-warning btn-lg mt-3 float-right" type="submit" name="editSubCategory" value="'.$idps.'">Subcategorie bewerken</button>                           
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
}elseif(isset($_GET["editCategory"])){
    $idpc = $_GET["editCategory"];
    $sql = 
        "SELECT product_categories.name, product_categories.visible
        FROM product_categories
        WHERE product_categories.id = '$idpc';";
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($res);

    $name = $row["name"];
    $visible = $row["visible"];

    $checked = "";
    if($visible == 1){
        $checked = " checked";
    }

    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug</a>';

    $cont .= 
        '<div class="col-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <h2>Toevoegen</h2>
                    <p class="m-b-0 text-muted">Vul hier de gegevens in van het product. Alle velden met een * zijn verplicht.</p>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-row">
                                    <div class="col-12">
                                        <h5>Gegevens</h5>
                                    </div>
                                    <div class="col-12 col-md-9 mb-3">
                                        <label for="name">Naam Categorie*</label>
                                        <input type="text" class="form-control" id="name" name="name" required value="'.$name.'">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="m-b-10 text-center">
                                            <label for="visible">Zichtbaar</label>
                                            <label class="cstm-switch d-block">
                                                <input type="checkbox" class="cstm-switch-input" name="visible" id="visible"'.$checked.' value="1" >
                                                <span class="cstm-switch-indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-warning btn-lg mt-3 float-right" type="submit" name="editCategory" value="'.$idpc.'">Categorie bewerken</button>                           
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
}elseif(isset($_GET["delSubCategory"])){
    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug</a>';
    $cont .= 
        '<div class="col-12">
            <div class="card pt-2 pb-2 mb-5">
                <div class="card-body">
                    <form method="POST">
                        <h2>Subcategorie verwijderen</h2>
                        <p>Weet je zeker dat je deze subcategorie wilt verwijderen?</p>
                        <button class="btn btn-danger" name="delSubCategory" value="'.$_GET["delSubCategory"].'">Verwijderen <i class="fa fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>';
}elseif(isset($_GET["delCategory"])){
    $header_btns .= '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?"><i class="fa fa-arrow-left m-r-5"></i> Terug</a>';
    $cont .= 
        '<div class="col-12">
            <div class="card pt-2 pb-2 mb-5">
                <div class="card-body">
                    <form method="POST">
                        <h2>Categorie verwijderen</h2>
                        <p>Weet je zeker dat je deze categorie wilt verwijderen?</p>
                        <button class="btn btn-danger" name="delCategory" value="'.$_GET["delCategory"].'">Verwijderen <i class="fa fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>';
}else{
    $header_btns .= 
        '<a class="btn btn-light btn-outline-dark pl-3 pr-3" href="?addCategory"><i class="fa fa-plus m-r-5"></i> Toevoegen</a>
        <a class="btn btn-light btn-outline-dark pl-3 pr-3" href="javascript:exportExcel();"><i class="fa fa-table m-r-5"></i> Exporteren naar excel</a>';
    $cont .= 
        '<div class="col-12 col-md-6">
            <div class="card pt-2 pb-2 mb-5">
                <div class="card-body table-responsive">
                    <table class="table table-hover" id="tableCategories">
                        <thead>
                            <tr>
                                <th>Categorieën</th>
                                <th style="width:100px;">Zichtbaar</th>
                                <th style="width:100px;">Actie</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card pt-2 pb-2 mb-5">
                <div class="card-body table-responsive">
                    <a class="btn btn-light btn-outline-dark pl-3 pr-3 mb-2 d-none" id=subCategoryButton><i class="fa fa-plus m-r-5"></i> Subcategorie toevoegen</a>
                    <table class="table table-hover" id="tableSubCategory">
                        <thead>
                            <tr>
                                <th>Subcategorieën</th>
                                <th style="width:100px;">Zichtbaar</th>
                                <th style="width:100px;">Actie</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>';
    
    $js .= 
        "var table = $('#tableCategories').DataTable({
            stateSave: true,
            columnDefs: [{
                orderable: false, 
                targets: [1, 2] 
            }],
            columns: [ 
                { data: 'name' },
                { data: 'visible' },
                { data: 'btns' }
            ],
            lengthMenu: [
                [5, 10, 25, 50, 100, 500, 1000, 10000],
                [5, 10, 25, 50, 100, 500, 1000, 'Alles']
            ],
            processing: true,
            serverSide: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/nl-NL.json',
            },
            ajax: {
                url: './assets/php/ajax.php?request=getCategories',
                type: 'POST',
                error: function(err, status){
                    console.log(err);
                },
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
    <title>Categorieën - MSK Stores</title>
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon.png"/>
    <link rel="icon" type="image/png" href="./assets/img/favicon.png" sizes="16x16">

    <!-- CSS Vendor start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/atmos.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,500,600">
    <!-- CSS Vendor end -->

    <!-- CSS start -->
    <link rel="stylesheet" type="text/css" href="./assets/css/pace.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/jquery-scrollbar.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/daterangepicker.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/dataTables-bootstrap4.min.css">
    <!-- CSS end -->

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
    <script src="./assets/js/dataTables.min.js"></script>
    <script src="./assets/js/atmos.min.js"></script>
    <script src="https://kit.fontawesome.com/f3f57d50bc.js" crossorigin="anonymous"></script>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <!-- JS end -->

    <!-- Additional JS commands by PHP start -->
    <?php require_once("./assets/php/main.php");?>
    <!-- Additional JS commands by PHP end -->
    <script>
        function exportExcel(){
            // Create worksheet from HTML DOM TABLE
            var wb = XLSX.utils.table_to_book(document.getElementById('tableCategories'));
            
            // remove last column
            wb.Sheets.Sheet1['!cols'][2] = {hidden:true}

            // Export to file (start a download)
            XLSX.writeFile(wb, 'categories.xlsx');
        }

        function addSpecRow(){
            var value = $("#add_spec_value").val();
            if(value.length){
                $("#tableSpecs tbody").append(
                    `<tr>
                        <td><input type="text" class="form-control" name="specs[]" value="` + value + `"></td>
                        <td><button class="btn btn-danger" onclick="$(this).parent().parent().remove();"><i class="fa fa-trash"></i></button></td>
                    </tr>`
                );
                $("#add_spec_value").val("");
            }
        }

        function getSubCategory(idpc){
            $('#tableSubCategory').DataTable().destroy();
            $('#tableSubCategory').DataTable({
                stateSave: true,
                columnDefs: [{
                    orderable: false, 
                    targets: [1, 2] 
                }],
                columns: [ 
                    { data: 'name' },
                    { data: 'visible' },
                    { data: 'btns' }
                ],
                lengthMenu: [
                    [5, 10, 25, 50, 100, 500, 1000, 10000],
                    [5, 10, 25, 50, 100, 500, 1000, 'Alles']
                ],
                processing: true,
                serverSide: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/nl-NL.json',
                },
                ajax: {
                    url: `./assets/php/ajax.php?request=getSubCategories&idpc=${idpc}`,
                    type: 'POST',
                    complete: function(){
                        $("#subCategoryButton").removeClass("d-none");
                        $("#subCategoryButton").prop("href", `?addSubCategory=${idpc}`)
                    },
                    error: function(err, status){
                        console.log(err);
                    },
                }
            });
        }
    </script>
</body>
</html>