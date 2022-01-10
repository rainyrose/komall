<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_product_simple_info_data";
	$query = "SELECT * FROM $setting_table WHERE category = '$category' ORDER BY no ASC";
	$result = mysqli_query($connect, $query);
	$count = 1;
	while($row = mysqli_fetch_array($result)){
		echo "<tr><th scope=\"row\">".$row[field_name]."</th><td><input type=\"text\" name=\"field_" . $count ."\" id=\"field_".$count."\" class=\"inputFull\" value=\"상품페이지 참고\"/> <p class=\"info\">".$row[field_txt]."</p></td></tr>";
		$count++;
	} 
?>

