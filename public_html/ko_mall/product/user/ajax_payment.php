<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";

//echo $data_info;
//$data = htmlspecialchars_decode($data_info);
//echo htmlspecialchars_decode($data_info);
//echo json_decode($data);

$result_array = json_decode($_POST['data_info'], true);
$options_json = json_decode($_SESSION[options_checker], true);
$options_json = array_filter($options_json);
//상품
$total_price = 0;
$deli_price = 0;
$p_count = 0;

$add_delivery_price = 0;
$use_point = $result_array[point];
$adp_query = "SELECT * FROM koweb_add_delivery_price WHERE start_zip <= '$result_array[zip]' AND end_zip >= '$result_array[zip]' ORDER BY price DESC LIMIT 1";
$adp_query2 = "SELECT * FROM koweb_add_delivery_price WHERE start_zip <= '$result_array[zip]' AND end_zip >= '$result_array[zip]'";
$adp_result = mysqli_query($connect,$adp_query);
$adp_row = mysqli_fetch_array($adp_result);
$adp_check = mysqli_num_rows(mysqli_query($connect, $adp_query2));

if($adp_check != 0){
	$add_delivery_price =  $adp_row[price];
} else {
	$add_delivery_price = "0";
}

//옵션 부분 처리
$product_id_array = array();
$product_price = 0;
foreach($options_json AS $ojson){
	$options_info = "";
	$add_options_info = "";

	$product_id = $ojson[product_id];
	$product_id_array[] = $product_id;
	$options = $ojson[options];
	$add_options = $ojson[add_options];
	$option_flag = $ojson[option_flag];

	$product_ = get_product($product_id);

	//옵션변수생성
	if($option_flag == "Y"){
		$options_info_ = explode("^", $options);
	} else {
		$options_info_ = $options;
		$options_info_ = array($product_id."|".$options_info_);
	}
	$options_info_ = array_filter($options_info_);

		foreach($options_info_ AS $oinfo){

			$options_ = explode("|", $oinfo);

			if($option_flag == "Y"){
				$options_tmp = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_option_detail WHERE id='$options_[0]'"));
			} else {
				$options_tmp = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product WHERE id='$options_[0]'"));
				$options_tmp[title] = $options_tmp[product_title];
				$options_tmp[stock] = $options_tmp[stock_count];
			}


			//옵션인지 아닌지 체크
			//옵션이 아닐때
			if($option_flag != "Y"){
				$product_all_count = $options_[1];

			//옵션일때
			} else {
				$product_all_count = 0;
				foreach ($options_json as $tmp_ojson) {
					if($tmp_ojson[product_id] == $product_id){
						$count = end(explode("|", $tmp_ojson['options']));
						$product_all_count += $count;
					} else {
						continue;
					}
				}
			}

			if($options_tmp[price_type] == "+"){
				$option_detail_price = $product_[price] + $options_tmp[price];

			} else if($options_tmp[price_type] == "-") {
				$option_detail_price = $product_[price] - $options_tmp[price];
			} else {
				//옵션이 없을때
				$option_detail_price = $product_[price];
			}

			$options_price = $option_detail_price * $options_[1];
			$options_point = $options_price * ($product_[point_detail] / 100);
			$product_price += $options_price;
			//옵션 인설트
		}

		//추가옵션 변수생성
		$add_options_info = "";
		$add_options_info_ = explode("^", $add_options);
		$add_options_info_ = array_filter($add_options_info_);

		if(count($add_options_info_) > 0){
			foreach($add_options_info_ AS $oinfo){

				$add_options_ = explode("|", $oinfo);
				$add_options_tmp = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_option_detail WHERE id='$add_options_[0]'"));

				$add_options_price = $add_options_tmp[price] * $add_options_[1];
				$add_options_point = $add_options_price * ($product_[point_detail] / 100);
				$product_price += $add_options_price;
			}
		}

	if($product_[point_type] == "2"){
		$option_point = ($product_price * ($product_[point_detail] / 100));
	}



	if($p_count == 0){
		$title_ = $options_tmp[title];
	} else {

	}

	//값 저장
	$p_count++;
}

if($p_count > 1){
	$title_ .= " 외 ".$p_count."건";
}

$deli_price = get_delivery_price($connect, $product_id_array, $product_price);
$total_price = ($deli_price + $product_price + $add_delivery_price) - $use_point;

$result_array[LGD_AMOUNT] = $total_price;
$result_array[LGD_PRODUCTINFO] = $title_;
$result_array[CST_MID] = $site_pay[uplus_shopid];
$result_array[IS_MOBILE] = $mobile_agent_;

$result = json_encode($result_array);
echo($result);

?>
