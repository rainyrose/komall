<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_product_category_config";
	//기본정보
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE id='$no'"));


	$result_array = array("title" => htmlspecialchars_decode($default[title])
					,"use_device_pc" => $default[use_device_pc]
					,"use_device_mob" => $default[use_device_mob]
					,"use_type" => $default[use_type]
					,"use_realname" => $default[use_realname]
					,"use_sell" => $default[use_sell]
					,"use_19" => $default[use_19]
					,"state" => $default[state]
					,"memo" => $default[memo]
					,"category" => $default[category]
					,"cateogry_id" => $default[id]
			 );

	$result = json_encode($result_array);
	echo($result);
?>
