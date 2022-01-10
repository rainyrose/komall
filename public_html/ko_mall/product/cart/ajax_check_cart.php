<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";
if($_SESSION['order_type'] == "member"){
	$mem = get_member($_SESSION['member_id']);
    $member_id = $mem['id'];

}else{
    $member_id = $_SESSION['member_id'];

}

$product_id_col = $_POST[product_id_col];
if(!$product_id_col) $product_id_col[] = $product_id;

foreach ($product_id_col as $key => $product_id) {
    if($member_id){
        $cart_query ="SELECT * FROM koweb_cart WHERE member_id='{$member_id}' AND product_id='{$product_id}'";
        $cart_result = mysqli_query($connect,$cart_query);
        $cart_num_row = mysqli_num_rows($cart_result);
        if($cart_num_row){
            echo "true";
            exit;
        }
    }else{
        if($_SESSION['s_cart'][$product_id]['0']){
            echo "true";
            exit;
        }
    }

}
echo "false";
exit;
?>
