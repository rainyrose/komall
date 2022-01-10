<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	if($data_type == "member"){
		$setting_table = "koweb_member";
		$checker_ = $type;
	} else if($data_type == "menu"){
		$setting_table = "koweb_menu_config";
		$checker_ = "dir";
		$WHERE = " AND depth='1'";
	}

	//기본정보
	if($type == "id" || $type == "email"){
		$default = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting_table WHERE 1=1 AND $checker_ ='" . $variable ."' $WHERE"));
		//data_type이 메뉴일경우 해당 디렉토리가 있는지 확인한다.
		if($data_type == "menu"){
			if (is_dir($_SERVER['DOCUMENT_ROOT'] . "/contents/".$variable)){
				$default = 1;
			}
		}

		if(!$variable) {
			$result_array = array("result" => "not_data");
		} else {
			if($default > 0){
				$result_array = array("result" => "false" );
			} else {
				$result_array = array("result" => "true" );
			}
		}
	} else {
		$result_array = array("result" => "true" );
	}
	$result = json_encode($result_array);
	echo($result);
?>
