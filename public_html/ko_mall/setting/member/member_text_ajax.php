<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_member_item";
	$reg_date = date("Y-m-d H:i:s");

	if($mode == "load"){
		$result = mysqli_query($connect, "SELECT contents FROM $setting_table WHERE item = '$item'");
		$row = mysqli_fetch_array($result);
		echo htmlspecialchars_decode($row[contents]);
	} else {
		$query = mysqli_query($connect, "UPDATE $setting_table SET contents ='$content' WHERE item = '$item'");
		$result = mysqli_query($connect, "SELECT contents FROM $setting_table WHERE item = '$item'");
		$row = mysqli_fetch_array($result);
		echo htmlspecialchars_decode($row[contents]);
	}
?>