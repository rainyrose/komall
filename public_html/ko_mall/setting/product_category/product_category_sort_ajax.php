<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_product_category_config";
	//기본정보
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE no='$no' AND category='$category'"));

	if($sort_mode == "up"){
		$sort_WHERE = "AND sort < '$default[sort]' ORDER BY sort DESC";
	} else {
		$sort_WHERE = "AND sort > '$default[sort]' ORDER BY sort ASC";
	}

	if($default[depth] != "1"){
		$query = "SELECT * FROM $setting_table WHERE depth = '$default[depth]' AND ref_group = '$default[ref_group]' $sort_WHERE LIMIT 1";
	} else {
		$query = "SELECT * FROM $setting_table WHERE depth = '$default[depth]' $sort_WHERE LIMIT 1";
	}

	$result = mysqli_query($connect, $query);
	$result2 = mysqli_query($connect, $query);
	$check_row = mysqli_fetch_array($result);	

	if($check_row[0]){
		$prev_data = mysqli_fetch_array($result2);
		$tmp_sort = "";
		$tmp_sort = $default[sort];
		$default[sort] = $prev_data[sort];
		$prev_data[sort] = $tmp_sort;

		if(!$prev_data[sort]) $prev_data[sort] = 1;
		$prev_update = mysqli_query($connect, "UPDATE $setting_table SET sort='$prev_data[sort]' WHERE no='$prev_data[no]'");
		$default = mysqli_query($connect, "UPDATE $setting_table SET sort='$default[sort]' WHERE no='$default[no]'");
	}
?>


