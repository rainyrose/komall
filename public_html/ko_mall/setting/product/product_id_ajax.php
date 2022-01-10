<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";

	if($mode == "modify"){
		$WHERE = "AND no != '$no'";
		$WHERE_OP = "AND ref_product != '$no'";
	}

	if($data_type == "check_id"){
		$setting_table = "koweb_product";
		$default = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting_table WHERE 1=1 AND id ='" . $id ."' $WHERE"));
		if($default > 0){
			$result_array = array("result" => "false" );
		} else {
			$result_array = array("result" => "true" );
		}

	} else if($data_type == "detail_check_id"){
		$setting_table = "koweb_option_detail";
		
		$id = substr($id, 0, -1);
		$id_data = explode("|", $id);

		$result_count = 0;
		$result_array = array("result" => "");
		$id_text= "";
		 
		$num = array_count_values($id_data);
		 
		foreach( $num AS $key => $value ){
			if($value > 1){
				$id_text .= $key . "|";
				$result_array[result] = "false";
				$result_count++;
			}

		}

		if($result_count > 0){
			$result_array[result_ids] = substr($id_text, 0, -1);
			$result = json_encode($result_array);
			echo($result);
			exit;
		}

		foreach($id_data AS $ida){
			$default = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting_table WHERE 1=1 AND id ='" . $ida ."' AND otype = 'detail' $WHERE_OP"));
			if($default > 0){
				$result_count++;
				$id_text .= $ida . "|";
			} 
		}
		
		if($result_count > 0){
			$result_array[result] = "false";
		} else {
			$result_array[result] = "true";
		}

		$result_array[result_ids] = substr($id_text, 0, -1);

	} else {
		$setting_table = "koweb_option_detail";
		
		$id = substr($id, 0, -1);
		$id_data = explode("|", $id);

		$result_count = 0;
		$result_array = array("result" => "");
		$id_text= "";

			 
		$num = array_count_values($id_data);
		 
		foreach( $num AS $key => $value ){
			if($value > 1){
				$id_text .= $key . "|";
				$result_array[result] = "false";
				$result_count++;
			}

		}

		if($result_count > 0){
			$result_array[result_ids] = substr($id_text, 0, -1);
			$result = json_encode($result_array);
			echo($result);
			exit;
		}

		foreach($id_data AS $ida){
			$default = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting_table WHERE 1=1 AND id ='" . $ida ."' AND otype = 'add' $WHERE_OP"));
			if($default > 0){
				$result_count++;
				$id_text .= $ida . "|";
			} 
		}
		
		if($result_count > 0){
			$result_array[result] = "false";
		} else {
			$result_array[result] = "true";
		}

		$result_array[result_ids] = substr($id_text, 0, -1);
	}

	$result = json_encode($result_array);
	echo($result);
?>
