<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";
$option = $_POST[option];
$options_json = array_filter($option);
$j = 0;
$i = 0;

foreach($options_json AS $json){
	$option_flag = $json[option_flag];
	$options = $json[options];
	$add_options = $json[add_options];
	$product_id = $json[product_id];

	if($option_flag == "Y"){
		$options_data = explode("^", $options);
	} else {
		$options_data = $options;
		$options_data = array($id."|".$options);
	}

	$add_options_data = explode("^", $add_options);
	$add_options_data = array_filter($add_options_data);

	$options_data = array_filter($options_data);

	$product_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product WHERE id = '$product_id' LIMIT 1"));

	$options_price = array();

	foreach($options_data AS $odata){

		$option_detail = explode("|", $odata);

		$option_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_option_detail WHERE id='$option_detail[0]' AND ref_product = '$product_[no]' LIMIT 1"));

		if($option_[price_type] == "+"){
			$option_detail_price = $product_[price] + $option_[price];
		} else if($option_[price_type] == "-") {
			$option_detail_price = $product_[price] - $option_[price];
		} else {
			//옵션이 없을때
			$option_detail_price = $product_[price];
		}
		$option_price = ($option_detail_price * $option_detail[1]);
		$price_check += $option_price;

		if($mode == "WON"){
			$options_price[option][$i][option_detail_price] = number_format($option_detail_price);
			$options_price[option][$i][option_price] = number_format($option_price);
		} else if($mode == "USD"){
			$options_price[option][$i][option_detail_price] =  number_format($option_detail_price / $site_pay[transfer], 2);
			$options_price[option][$i][option_price] = number_format($option_price / $site_pay[transfer], 2);
		}
		$i++;
	}

	foreach($add_options_data AS $aodata){
		$add_option_detail = explode("|", $aodata);
		$add_option_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_option_detail WHERE id='$add_option_detail[0]' AND ref_product = '$product_[no]' ANd otype='add' LIMIT 1"));

		if($add_option_[price_type] == "+"){
			$add_option_detail_price = 0 + $add_option_[price];
		} else if($add_option_[price_type] == "-") {
			$add_option_detail_price = 0 - $add_option_[price];
		}
		$add_option_price = ($add_option_detail_price * $add_option_detail[1]);
		$price_check += $add_option_price;

		if($mode == "WON"){
			$options_price[add][$j][add_option_detail_price] = number_format($add_option_detail_price);
			$options_price[add][$j][add_option_price] = number_format($add_option_price);
		} else {
			$options_price[add][$j][add_option_detail_price] =  number_format($add_option_detail_price / $site_pay[transfer], 2);
			$options_price[add][$j][add_option_price] = number_format($add_option_price / $site_pay[transfer], 2);
		}
		$j++;
	}
}

if($mode == "WON"){
	//총구매금액
	$options_price[product_total] = number_format($price_check);
	//배송비
	$options_price[delivery_price] = number_format(get_delivery_price($connect, $product_id, $price_check));
	//추가배송비
	$options_price[delivery_add_price] = number_format(get_delivery_price($connect, $product_id, $price_check));

} else {
	//총구매금액
	$options_price[product_total] = number_format($price_check / $site_pay[transfer], 2);
	//배송비
	$options_price[delivery_price] = number_format( get_delivery_price($connect, $product_id, $price_check)  / $site_pay[transfer], 2 );
	//추가배송비
	$options_price[delivery_add_price] = number_format(get_delivery_price($connect, $product_id, $price_check) / $site_pay[transfer], 2);

}


//echo $options_price[option][0][option_detail_price];
echo json_encode($options_price);

?>