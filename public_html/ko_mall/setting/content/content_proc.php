<? 
	$reg_date = date("Y-m-d H:i:s");
	$ip = $_SERVER['REMOTE_ADDR'];
	$setting_table = "koweb_content_config";
	$web_content = $_POST[web_content];
	$mob_content = $_POST[mob_content];

	$web_content = stripslashes($web_content);    // '/' 삭제함
	$web_content = addslashes($web_content);   //   '/' 추가함

	if($mode == "write_proc"){
				$content_title = htmlspecialchars_decode($content_title);

		//값 저장
		@mysqli_query($connect, "INSERT INTO $setting_table VALUES(''
													, '$content_title'
													, '$content_id'
													, '$content_type'
													, '$web_content'
													, '$mob_content'
													, '$ref_link'
													, '$ref_target'
													, '$ref_program'
													, '$ref_board'
													, '$ref_online'
													, '$ref_product'
													, '$memo'
													, 'Y'
													, 'N'
													, '$sort'
													, '$reg_date'
													, '$ip'
													, '$_SESSION[member_id]')");

		$rowid = mysqli_insert_id($connect);


		//백업 등록
		@mysqli_query($connect, "INSERT INTO koweb_content_history VALUES(''
													, '$content_title'
													, '$content_id'
													, '$content_type'
													, '$web_content'
													, '$mob_content'
													, '$ref_link'
													, '$ref_target'
													, '$ref_program'
													, '$ref_board'
													, '$ref_online'
													, '$ref_product'
													, '$memo'
													, 'Y'
													, '$delete_state'
													, '$reg_date'
													, '$ip'
													, '$_SESSION[member_id]')");

		$content_id = sprintf('%04d',$rowid);
		mysqli_query($connect, "UPDATE $setting_table SET content_id='$content_id' WHERE no='$rowid'");
		mysqli_query($connect, "UPDATE koweb_content_history SET content_id='$content_id' WHERE no='$rowid'");
	
		alert("등록되었습니다.");

	}  else if($mode == "delete") {

		$is_check_ = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting_table WHERE ref_no='$no' AND depth > 1"));
		if($is_check_ > 0){
			error("해당 카테고리의 하위 카테고리가 존재합니다. 변경할 수 없습니다.");
			exit;
		}
		mysqli_query($connect, "DELETE FROM koweb_dept WHERE no = '$no'"); 
		alert("삭제되었습니다.");
	}

	url($common_queryString."&amp;mode=write");

?>