<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";
if($_SESSION['order_type'] == "member"){
	$mem = get_member($_SESSION['member_id']);
    $member_id = $mem['id'];
}else{
    $member_id = $_SESSION['member_id'];
}

$reg_date = date("Y-m-d H:i:s");
$result['flag'] = false;

$product = get_product($product_id);
if(!$product){
    $result['ment'] = "I can't find that product";
    echo json_encode($result);
    exit;
}

///////////////////////////    MAX_COUNT 체크 //////////////
$options_ = explode("^",$options);
$error_flag = false;
$proc_count = 0;
if($option_flag == "Y"){
    foreach ($options_ as $key => $value) {
        list($option['id'],$option['cnt']) = explode("|",$value);
        $proc_count += $option['cnt'];
    }
}else{
    $proc_count += $options;
}

if(!$member_id){
	$cart['cnt'] = 0;
	//session형태 => array("product_id"=>array("option_id"=>array("product_cnt"=>value,"add_option"=>value));
	foreach ($_SESSION['s_cart'][$product_id] as $p_id => $value) {
		foreach ($value as $o_id => $value2) {
			$cart['cnt'] += $value2['product_cnt'];
		}
	}
}else{
	$cart_query ="SELECT sum(product_cnt) as cnt FROM koweb_cart WHERE member_id='{$member_id}' AND product_id='{$product_id}' group by product_id";
	$cart_result = mysqli_query($connect,$cart_query);
	$cart_num_row = mysqli_num_rows($cart_result);
	$cart = mysqli_fetch_array($cart_result);
}



$this_count = $cart['cnt'] + $proc_count;

if($product['max_count']){
	if($product['max_count'] < $this_count){
		$result['ment'] = "You may not add more than ".($product['max_count']+1)." items to your cart. [Currently: {$cart['cnt']}]";
		$result['flag'] = "STOP";
	    echo json_encode($result);
	    exit;
	}
}
///////////////////////////    MAX_COUNT 체크 END //////////////

if($option_flag == "Y"){
	$i = 0;
    foreach ($options_ as $key => $value) {
		$i++;
        list($option['id'],$option['cnt']) = explode("|",$value);

        $option_check_query = "SELECT * from koweb_option_detail WHERE id = '{$option['id']}'";
        $option_check_result = mysqli_query($connect,$option_check_query);
        $option_check_num = mysqli_num_rows($option_check_result);

        if($option_check_num < 1){
            $error_flag = true;
			continue;
        }

        $cart_query ="SELECT * FROM koweb_cart WHERE member_id='{$member_id}' AND product_id='{$product_id}' AND option_id='{$option['id']}'";
        $cart_result = mysqli_query($connect,$cart_query);
        $cart_num_row = mysqli_num_rows($cart_result);
		$cart = mysqli_fetch_array($cart_result);

		if(!$member_id){
			//session형태 => array("product_id"=>array("option_id"=>array("product_cnt"=>value,"add_option"=>value));
			if($_SESSION['s_cart'][$product_id][$option['id']]) $cart_num_row = 1;
		}

        if($cart_num_row == 0){
			if($i == 1){
				$cart_add_option = $add_options;
			}

            $query =
	            "INSERT INTO
	                koweb_cart
	            SET
	                member_id='{$member_id}' ,
	                product_id='{$product_id}' ,
	                option_id = '{$option['id']}' ,
	                product_cnt = '{$option['cnt']}' ,
					add_option = '{$cart_add_option}' ,
	                reg_date='{$reg_date}'";
        }else{
			if(!$member_id){
				$cart['add_option'] = $_SESSION['s_cart'][$product_id]['0']['add_option'];
				$option['cnt'] += $_SESSION['s_cart'][$product_id][$option['id']]['product_cnt'];
		 	}

			if($i == 1){
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
			}

			if(!$member_id) $option['cnt'] += $_SESSION['s_cart'][$product_id][$option['id']]['product_cnt'];

			$query =
            "UPDATE
                koweb_cart
            SET
                product_cnt=product_cnt+'{$option['cnt']}' ,
				add_option = '{$cart_add_option}' ,
                reg_date='{$reg_date}'
            WHERE
                member_id='{$member_id}' AND
                product_id='{$product_id}' AND
				option_id='{$option['id']}'";
        }

		if($member_id) mysqli_query($connect,$query);
		else{
			$_SESSION['s_cart'][$product_id][$option['id']]['product_cnt'] = $option['cnt'];
			$_SESSION['s_cart'][$product_id][$option['id']]['add_option'] = $cart_add_option;
		}
		$cart_add_option = "";
    }

    if($error_flag){
        $result['flag'] = "OK";
        $result['ment'] = "There is an option not found, except in the shopping cart.";
        echo json_encode($result);
        exit;
    }
}else{
    $cart_query ="SELECT * FROM koweb_cart WHERE member_id='{$member_id}' AND product_id='{$product_id}'";
    $cart_result = mysqli_query($connect,$cart_query);
    $cart_num_row = mysqli_num_rows($cart_result);
	$cart = mysqli_fetch_array($cart_result);
	if(!$member_id){
		//session형태 => array("product_id"=>array("option_id"=>array("product_cnt"=>value,"add_option"=>value));
		if($_SESSION['s_cart'][$product_id]['0']) $cart_num_row = 1;
	}

    if($cart_num_row == 0){

		$cart_add_option = $add_options;

        $query =
        "INSERT INTO
            koweb_cart
        SET
            member_id='{$member_id}' ,
            product_id='{$product_id}' ,
            option_id = '' ,
            product_cnt = '{$options}' ,
			add_option = '{$cart_add_option}' ,
            reg_date='{$reg_date}'";
    }else{
		if(!$member_id){
			$cart['add_option'] = $_SESSION['s_cart'][$product_id]['0']['add_option'];
			$options += $_SESSION['s_cart'][$product_id]['0']['product_cnt'];
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
            product_cnt=product_cnt+'{$options}' ,
			add_option = '{$cart_add_option}' ,
            reg_date='{$reg_date}'
        WHERE
            member_id='{$member_id}' AND
            product_id='{$product_id}'";
    }

	if($member_id) mysqli_query($connect,$query);
	else{
		$_SESSION['s_cart'][$product_id]['0']['product_cnt'] = (string)$options;
		$_SESSION['s_cart'][$product_id]['0']['add_option'] = $cart_add_option;
	}
}

$result['flag'] = "OK";
$result['ment'] = "Add to cart.";
echo json_encode($result);
exit;
// echo json_encode($test);
