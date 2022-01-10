<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";
$query = "SELECT * FROM koweb_option_detail WHERE ref_product='{$product_id}' AND soldout !='Y' and type_id='{$type_id}'";
$result = mysqli_query($connect,$query);
$check = mysqli_num_rows($result);
$return_arr['flag'] = false;
if($check != 0){
    $row = mysqli_fetch_array($result);
    $return_arr['flag'] = "OK";
    $return_arr['id'] = $row['id'];
    $return_arr['price_type'] = $row['price_type'];
    $return_arr['price'] = $row['price'];
    $return_arr['stock'] = $row['stock'];
}
echo json_encode($return_arr);
