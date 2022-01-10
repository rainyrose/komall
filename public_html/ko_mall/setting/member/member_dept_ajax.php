<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_dept";
	$reg_date = date("Y-m-d H:i:s");
	$this_count = 0;
	
	//왜그런지 모르겠음. dept 초기화;
	$depth = $_POST[depth];

	if($mode == "write_proc"){
		if($depth == 1){
			$ref_group_tmp = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE state='Y' AND depth = '1' GROUP BY ref_group ORDER BY ref_group DESC LIMIT 1"));
			$ref_group = $ref_group_tmp[ref_group] + 1;
			$ref_no = "";
			$depth = "1";
			$depth_history = "";
		} else {
			//직전차수 정보 가져오기
			$query = "SELECT * FROM $setting_table WHERE no='$prev_'";
			$result = mysqli_query($connect, $query);
			$prev = mysqli_fetch_array($result);
			$ref_group = $prev[ref_group];
			$ref_no = $prev[no];
			$depth = $prev[depth] + 1;
		}

		$sort_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE depth='$depth' AND ref_group = '$ref_group' ORDER BY sort DESC LIMIT 1"));
		$sort = $sort_[sort]+1;
			
		//값 저장
		mysqli_query($connect, "INSERT INTO $setting_table VALUES('','$ref_group', '$ref_no', '$depth', '$depth_history', '$dept', '$dept_type', '$sort', 'Y', '$reg_date')");
		$rowid = mysqli_insert_id($connect);

		if($depth == 1){
			$ref_no = mysqli_insert_id($connect);
			$depth_history = $ref_no;
		 	mysqli_query($connect, "UPDATE $setting_table SET ref_no='$ref_no', depth_history='$depth_history' WHERE no='$ref_no'");
		} else {
			$depth_history = $prev[depth_history] . "|" . $rowid;
			mysqli_query($connect, "UPDATE $setting_table SET depth_history='$depth_history' WHERE no='$rowid'");
		}
		
		echo "정상적으로 등록 되었습니다.";

	} else if ($mode == "modify_proc"){
		mysqli_query($connect, "UPDATE $setting_table SET  dept='$dept' WHERE no='$prev_'");
		echo "정상적으로 수정 되었습니다.";

	} else if($mode == "sort") { 

		//기본정보
		$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE no='$no'"));

		if($sort_mode == "up"){
			$sort_WHERE = "AND sort < '$default[sort]' ORDER BY sort DESC";
		} else {
			$sort_WHERE = "AND sort > '$default[sort]' ORDER BY sort ASC";
		}

		$query = "SELECT * FROM $setting_table WHERE depth = '$default[depth]' AND state='Y' AND ref_group = '$default[ref_group]' $sort_WHERE LIMIT 1";
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


	} else if($mode == "delete") {

		$is_check_ = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting_table WHERE ref_no='$prev_' AND depth > 1"));
		if($is_check_ > 0){
			echo "해당 카테고리의 하위 카테고리가 존재합니다. 변경할 수 없습니다.";
			exit;
		}
		mysqli_query($connect, "DELETE FROM $setting_table WHERE no = '$prev_'"); 
		echo "정상적으로 삭제 되었습니다.";
	}

?>