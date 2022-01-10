<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_board_config";
	//기본정보
	$default = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting_table WHERE id='board_" . $board_id ."'"));

	if($default > 0){
		$result_array = array("result" => "false" );
	} else {
		$result_array = array("result" => "true" );
	}

	$result = json_encode($result_array);
	echo($result);
?>
