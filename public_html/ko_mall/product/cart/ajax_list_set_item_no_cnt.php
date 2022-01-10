<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";
if($_SESSION['order_type'] == "member"){
	$mem = get_member($_SESSION['member_id']);
    $member_id = $mem['id'];
}else{
    $member_id = $_SESSION['member_id'];
}

$reg_date = date("Y-m-d H:i:s");
$result['flag'] = "STOP";
$result['ment'] = print_r($_POST,true);

if(!$product_id_col) $product_id_col[] = $product_id;

foreach ($product_id_col as $key => $product_id) {
    $product = get_product($product_id);
    if(!$product){
        $result['ment'] = "해당 상품을 찾을수없습니다";
        echo json_encode($result);
        exit;
    }
	if($product['use_soldout'] == "Y" || get_stock($connect,$product_id) || $product['seller'] != "Y"){
        $result['ment'] = $product[product_title]. " 상품은 품절상태이므로 장바구니 일괄담기가 실패하였습니다.";
        echo json_encode($result);
        exit;
    }
}

foreach ($product_id_col as $key => $product_id) {
    $product = get_product($product_id);
    if(!$product){
        $result['ment'] = "해당 상품을 찾을수없습니다";
        echo json_encode($result);
        exit;
    }

    $options_query = "SELECT * FROM koweb_option_set WHERE ref_product = '{$product['no']}' AND option_type = 'P' ORDER BY sort ASC";
    $options_result = mysqli_query($connect, $options_query);
    $options_num = mysqli_num_rows($options_result);

	if(count($product_id_col) == 1 && $options_num != 0){
		$result['flag'] = "STOP";
		$result['ment'] = "옵션이 있는상품은 장바구니에 바로 담을수없습니다.";
		echo json_encode($result);
		exit;

	}

    if($options_num != 0){
        continue;
    }


    $cart_query ="SELECT * FROM koweb_cart WHERE member_id='{$member_id}' AND product_id='{$product_id}'";
    $cart_result = mysqli_query($connect,$cart_query);
    $cart_num_row = mysqli_num_rows($cart_result);
    $cart = mysqli_fetch_array($cart_result);
    if(!$member_id){
    	//session형태 => array("product_id"=>array("option_id"=>array("product_cnt"=>value,"add_option"=>value));
    	if($_SESSION['s_cart'][$product_id]['0']) $cart_num_row = true;
    }

    $options = $product['min_count'];

    if($cart_num_row == 0){

    	$cart_add_option = $add_options;

        $query =
        "INSERT INTO
            koweb_cart
        SET
            member_id='{$member_id}' ,
            product_id='{$product_id}' ,
            option_id = '' ,
            product_cnt = '1' ,
    		add_option = '{$cart_add_option}' ,
            reg_date='{$reg_date}'";
    }else{
    	if(!$member_id){
    		$cart['add_option'] = $_SESSION['s_cart'][$product_id]['0']['add_option'];
     	}
    	$cart_add_option_col = array();
    	$cart_add_option_list = explode("^",$cart['add_option']);
    	foreach ($cart_add_option_list as $key => $add_option) {
    		list($add_option_id,$add_option_cnt) = explode("|",$add_option);
    		$cart_add_option_col[$add_option_id] = $add_option_cnt;
    	}

    	$post_add_option_list = explode("^",$add_options);
    	foreach ($post_add_option_list as $key => $add_option) {
    		list($add_option_id,$add_option_cnt) = explode("|",$add_option);
    		$cart_add_option_col[$add_option_id] = $cart_add_option_col[$add_option_id] + $add_option_cnt;
    	}

    	$tmp_add_option_list;
    	foreach ($cart_add_option_col as $key => $value) {
    		if($key == "")continue;
    		if($value == "")continue;
    		$tmp_add_option_list[] = $key."|".$value;
    	}

    	$cart_add_option = join("^",$tmp_add_option_list);


        $query =
        "UPDATE
            koweb_cart
        SET
            product_cnt=product_cnt+1 ,
    		add_option = '{$cart_add_option}' ,
            reg_date='{$reg_date}'
        WHERE
            member_id='{$member_id}' AND
            product_id='{$product_id}'";
    }

    if($member_id) mysqli_query($connect,$query);
    else{
    	$_SESSION['s_cart'][$product_id]['0']['product_cnt'] += 1;
    }

}

$result['flag'] = "OK";
unset($result['ment']);
echo json_encode($result);
exit;
