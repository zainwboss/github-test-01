<?php
require("config/main_function.php");
$secure = "sZ7BZ_hRE2tIps{";
$connection = connectDB($secure);

$strFileName = "master.txt";
$objFopen = fopen($strFileName, 'r') or die("Unable to open file!");
if ($objFopen) {
    while (!feof($objFopen)) {
        $file = fgets($objFopen, 4096);
        $file = explode(';', $file, -1);
        foreach ($file as $value) {
            // echo $value . '<br>';
            $data = explode('|', $value);
            $product_code = $data[0];
            $product_name = $data[1];
            $default_price = ($data[2]) ? $data[2] : 0;
            $stored_location = $data[3];
            $unit_code = $data[4];

            if ($product_code != '') {
                $sql_product = "SELECT * FROM tbl_product WHERE product_code ='$product_code'";
                $rs_product  = mysqli_query($connection, $sql_product) or die($connection->error);
                if ($rs_product->num_rows > 0) {
                    $row_product = mysqli_fetch_array($rs_product);

                    $sql_up_pro = "UPDATE tbl_product SET 
                     product_name = '$product_name' 
                    ,default_price = '$default_price'
                    ,stored_location = '$stored_location'
                    WHERE product_id = '{$row_product['product_id']}'";
                    $rs_up_pro = mysqli_query($connection, $sql_up_pro) or die($connection->error);
                } else {
                    $product_id = getRandomID(10, "tbl_product", "product_id");
                    $unit_id = getRandomID(10, "tbl_product_unit", "unit_id");

                    $sql_chk_unit = "SELECT * FROM tbl_unit WHERE unit_code ='$unit_code'";
                    $rs_chk_unit  = mysqli_query($connection, $sql_chk_unit) or die($connection->error);
                    if ($rs_chk_unit->num_rows == 0) {
                        $sql_unit = "INSERT INTO tbl_unit SET unit_code = '$unit_code',unit_name = '$unit_code'";
                        $rs_unit  = mysqli_query($connection, $sql_unit) or die($connection->error);
                    }

                    $sql_pro_unit = "INSERT INTO tbl_product_unit SET 
                         unit_id = '$unit_id' 
                        ,unit_code = '$unit_code'
                        ,product_id = '$product_id'
                        ,list_order = '1'
                        ,unit_name = '$unit_code'
                        ,transform_rate = '0'";
                    $rs_pro_unit  = mysqli_query($connection, $sql_pro_unit) or die($connection->error);

                    $sql_in_pro = "INSERT INTO tbl_product SET 
                        product_id = '$product_id' 
                        ,product_code = '$product_code' 
                        ,product_name = '$product_name'
                        ,stored_location = '$stored_location'
                        ,unit_id = '$unit_id'
                        ,default_price = '$default_price'";
                    $rs_in_pro = mysqli_query($connection, $sql_in_pro) or die($connection->error);
                }
            }
        }
    }
    fclose($objFopen);
}
