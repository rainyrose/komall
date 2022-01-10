<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";
if($_SESSION['order_type'] == "member"){
	$mem = get_member($_SESSION['member_id']);
    $member_id = $mem['id'];
}else{
    $member_id = $_SESSION['member_id'];
}
$product = get_product($product_id);
$reg_date = date("Y-m-d H:i:s");
$result['flag'] = false;

$count_query = "SELECT count(*) as cnt FROM koweb_option_set WHERE ref_product = '{$product['no']}' AND option_type='P'";
$count_result = mysqli_query($connect,$count_query);
$count_row = mysqli_fetch_array($count_result);

if($count_row['cnt'] > 0){
    $result['ment'] = "Please add items to the cart on the detail page.";
    echo json_encode($result);
    exit;
}

$count_query = "SELECT count(*) as cnt FROM koweb_cart WHERE product_id = '{$product['id']}' AND member_id='{$member_id}'";
$count_result = mysqli_query($connect,$count_query);
$count_row = mysqli_fetch_array($count_result);

if($count_row['cnt'] > 0){
    $result['ment'] = "This item is already in your cart.";
    echo json_encode($result);
    exit;
}

$cart_query = "INSERT INTO koweb_cart SET member_id='{$member_id}' , product_id='{$product['id']}' , product_cnt='1' , reg_date='{$reg_date}'";
mysqli_query($connect,$cart_query);
$result['flag'] = true;
$result['ment'] = "Add to cart";
echo json_encode($result);
exit;
