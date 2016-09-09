<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
}
require_once '../config.php';

if (isset ($_POST['action'])) {

    switch ($_POST['action']) {
        case 'addNewItem':
            
            $itemName = htmlspecialchars($_POST['itemName']);
            $code = htmlspecialchars($_POST['code']);
            $description = htmlspecialchars($_POST['description']);
            $price = htmlspecialchars($_POST['price']);
            $stock = htmlspecialchars($_POST['stock']);
            $date1 = htmlspecialchars($_POST['date1']);
            $courseType = htmlspecialchars($_POST['courseType']);
            $servingTime = htmlspecialchars($_POST['servingTime']);
            $file = $_POST['fileToUpload'];
            
            $no_fileToUpload = true;
            $fileToUpload_success = false;
            $fileNameUpdate = ' ';
            
            $output = array();
            
            //upload image if given by user
            if (is_uploaded_file($_FILES[$file]["tmp_name"])) {
                
                $no_fileToUpload = FALSE;
                
                //get the next auto-increment value and pass that as one of the arguments to validateImage.  
                $result = mysqli_query($dbConn,"SHOW TABLE STATUS FROM ".$dbDatabase." WHERE name = 'products'");
                $data = mysqli_fetch_assoc($result);
                $next_id = $data['Auto_increment'];
                $file_prefix = "PDT_ID_".$next_id."_";

                $validImage = validateImage($file,$file_prefix);

                if($validImage['status'] == "COMPLETE") {
                    $fileToUpload_success = TRUE;
                    $imagePath = "'".$validImage['fileName']."'";
                } else {
                    $fileToUpload_success = FALSE;
                    $output[0] = 0;
                    $output[1] = "Error: ".$validImage['errorMsg'];
                }
            } else {
                $imagePath = 'null';
            }
            
            if($no_fileToUpload || $fileToUpload_success) {

                $sql = "INSERT INTO products (name_txt, sku_txt, description_txt, price_float, stock_int, meal_type_ind, serving_time_ind, available_from_dt, file_path_txt, created_dt, updated_dt) VALUES('$itemName','$code','$description',$price,$stock,'$courseType','$servingTime','$date1',$imagePath,now(),now())";
                $result = mysqli_query($dbConn, $sql);

                if($result) {
                    $output[0] = 1;
                    $output[1] = 'New item ' . $itemName.' added to the Inventory';
                } else {
                    $output[0] = 0;
                    $output[1] = 'Database error: '+  mysqli_error($dbConn);
                    $errorlogmessage = "\n------------------------".date('m/d/Y h:i:s a', time())."---------------\nMysqli error: ".  mysqli_error($dbConn)." \n While executing ".$sql."\n------------------------";
                    $errorlogmessage .= "Error while inserting into product table";
                    $file_pointer = fopen("../errorlog.txt", "a");
                    fwrite($file_pointer, $errorlogmessage);			
                    fclose($file_pointer);
                }
                
                if ($fileToUpload_success && ($output[0] == 0)) {
                    if (!unlink($validImage['fileName'])) { //delete the file if DB update was unsuccessful
                        $output[0] = 0;
                        $output[1] .= "; Error deleting the image";
                    }
                }
                
            } else {
                $output[0] = 0;
                $output[1] = "Error: ".$validImage["errorMsg"];
            }
            
            
            echo json_encode($output);
            break;
        
        case 'editItem':

            $id = $_POST['itemId'];
            $itemNameUS_e = strtoupper(htmlspecialchars($_POST['itemNameUS_e']));
            $itemNameUK_e = strtoupper(htmlspecialchars($_POST['itemNameUK_e']));
            $descriptionUS_e = strtoupper(htmlspecialchars($_POST['descriptionUS_e']));
            $descriptionUK_e = strtoupper(htmlspecialchars($_POST['descriptionUK_e']));
            $price_e = $_POST['price_e'];
            
            $file = $_POST['fileToUpload'];
            $no_fileToUpload = true;
            $fileToUpload_success = false;
            $fileNameUpdate = ' ';
            
            $output = array();
            
            if (is_uploaded_file($_FILES[$file]["tmp_name"])) {
                
                $no_fileToUpload = FALSE;
                $validImage = validateImage($file);

                if($validImage['status'] == "COMPLETE") {
                    $fileToUpload_success = TRUE;
                    $fileNameUpdate = " , image = '".$validImage['fileName']."' ";
                } else {
                    $fileToUpload_success = FALSE;
                    $output[0] = 0;
                    $output[1] = "Error: ".$validImage['errorMsg'];
                }
            }
            
            if($no_fileToUpload || $fileToUpload_success) {
                $sql = "UPDATE a2_items SET item_name_us='$itemNameUS_e',item_name_uk='$itemNameUK_e',description_us='$descriptionUS_e',description_uk='$descriptionUK_e',price=$price_e ".$fileNameUpdate." WHERE id = $id";

                $result = mysqli_query($dbConn, $sql);

                if($result) {
                    $output[0] = 1;
                    $output[1] = 'Item ' . $itemNameUS_e.' is updated in the Inventory';
                } else if (mysqli_errno($dbConn)){
                    $output[0] = 0;
                    $output[1] = $itemNameUS_e.  ' or '.$itemNameUK_e. ' is duplicate of other inventory item.  So, no update done.';
                } else {
                    $output[0] = 0;
                    $output[1] = 'Database error: '+  mysqli_error($dbConn);
                }
                
                if ($fileToUpload_success && ($output[0] == 0)) {
                    if (!unlink($validImage['fileName'])) { //delete the file if DB update was unsuccessful
                        $output[0] = 0;
                        $output[1] .= "; Error deleting the image";
                    }
                }
            }
            echo json_encode($output);
            break;
        
        case 'deleteItem':
            
            $id = $_POST['id'];
            $output = array();
            
            $result1 = mysqli_query($dbConn, "SELECT * FROM a2_items WHERE id = $id");
            
            $row = mysqli_fetch_assoc($result1);
                        
            $result = mysqli_query($dbConn,"DELETE FROM a2_items WHERE id = $id");
            
            if($result) {
                if(unlink($row['image'])) {                
                    $output[0] = 1;
                    $output[1] = "Entry deleted";
                } else {
                    $output[0] = 0;
                    $output[1] = "Table record deleted.  Error deleting file : " .$row['image'];
                }
            } else {
                $output[0] = 0;
                $output[1] = 'Error: '.mysqli_error($dbConn);
            }
            
            echo json_encode($output);
        break;

        case "getProductList":
            
            $productSQL = "SELECT * FROM products";
            $productRes = mysqli_query($dbConn, $productSQL);
            
            $output = array();
            
            if (mysqli_num_rows($productRes) > 0) {
                $output[0] = 1;
                while ($product = mysqli_fetch_assoc($productRes)) {
                    
                    if ($product['file_path_txt'] == null) {
                        $image = "";
                    } else {
                        $image = "<img src='".$product['file_path_txt']."' alt='Item photo' width='100' height='100' />";
                    }
                    
                    array_push($output, array("id"=>$product['product_id'],
                                        "name"=>$product['name_txt'],
                            "sku"=>$product['sku_txt'],
                            "description"=>$product['description_txt'],
                            "price"=>$product['price_float'],
                            "stock"=>$product['stock_int'],
                            "meal_type"=>$product['meal_type_ind'],
                            "serving_time"=>$product['serving_time_ind'],
                            "availability"=>$product['available_from_dt'],
                            "image"=>$image,
                            "action"=>"<button class='btn btn-primary' id='editItem' type='submit'>Edit</button>&nbsp<button class='btn btn-danger' id='deleteItem' type='submit'>Delete</button>"));
                }
            } else {
                $output[0] = 0;
                $output[1] = "No items in inventory";
            }
            
            echo json_encode($output);
            break;
            
        default:
            break;
    }
}
 
function validateImage($file,$prefix) {
    
    $target_dir = "images/";
    $target_file = $target_dir . $prefix .basename($_FILES[$file]["name"]);
    
    $validUpload = 1;
    $errorMessage = '';
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    
    $check = getimagesize($_FILES[$file]["tmp_name"]);
    if ($check !== false) {
        $validUpload = 1;
    } else {
        $validUpload = 0;
        $errorMessage .= ";Fake Image ";
    }
    
    if(file_exists($target_file)) {
        $validUpload = 0;
        $errorMessage .= ";File name already exists";
    }
    
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $validUpload = 0;
        $errorMessage .= ";Only JPG, PNG, JPEG, GIF files are allowed";
    }
    
    if($validUpload == 1) {
        if(move_uploaded_file($_FILES[$file]["tmp_name"], $target_file)) {
            $validUpload = 1;
        } else {
            $errorMessage = "Error uploading file";
            $validUpload = 0;
        }
    } 
    
    if($validUpload == 1) {
        $imageUpload["status"] = "COMPLETE";
        $imageUpload["fileName"] = $target_file;
    } else {
        $imageUpload["status"] = "ERROR";
        $imageUpload["errorMsg"] = $errorMessage; 
    }
    
    return $imageUpload;
}