<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_content_config";
	$web_content = $_POST[web_content];
	$web_content = stripslashes($web_content);    // '/' 삭제함
	$web_content = addslashes($web_content);   //   '/' 추가함
	$mob_content = $_POST[mob_content];
	$reg_date = date("Y-m-d H:i:s");
	if($mode == "modify"){
	//기본정보
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE content_id='$content_id'"));
	$history_update_ = mysqli_query($connect, "INSERT INTO koweb_content_history SET content_title='$default[content_title]'
													, content_id='$default[content_id]'
													, content_type='$default[content_type]'
													, web_content='$default[web_content]'
													, mob_content='$default[mob_content]'
													, ref_link='$default[ref_link]'
													, ref_target='$default[ref_target]'
													, ref_program='$default[ref_program]'
													, ref_board='$default[ref_board]'
													, ref_online='$default[ref_online]'
													, ref_product='$default[ref_product]'
													, memo='$default[memo]'
													, state='$default[state]'
													, reg_date='$reg_date'
													, ip='$default[ip]'
													, writer='$_SESSION[member_id]'
							");


	$reg_date = date("Y-m-d H:i:s");
	$ip = $_SERVER['REMOTE_ADDR'];
	//$state = 'Y';
			$content_title = htmlspecialchars_decode($content_title);

	$update_ = mysqli_query($connect, "UPDATE $setting_table SET content_title='$content_title'
													, content_type='$content_type'
													, web_content='$web_content'
													, mob_content='$mob_content'
													, ref_link='$ref_link'
													, ref_target='$ref_target'
													, ref_program='$ref_program'
													, ref_board='$ref_board'
													, ref_online='$ref_online'
													, ref_product='$ref_product'
													, memo='$memo'
													, state='Y'
													, reg_date='$reg_date'
													, ip='$ip'
													, writer='$_SESSION[member_id]'
												WHERE content_id='$content_id'
							");
	} else {
		$menu_find_query = mysqli_query($connect, "SELECT * FROM koweb_menu_config WHERE content_id = '$content_id'");
		while($menu_find = mysqli_fetch_array($menu_find_query)){
				$update_ = mysqli_query($connect, "UPDATE koweb_menu_config SET content_id='' WHERE menu_id='$menu_find[menu_id]'");
		}
		$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE content_id='$content_id'"));
		$history_update_ = mysqli_query($connect, "INSERT INTO koweb_content_history SET content_title='$default[content_title]'
													, content_id='$default[content_id]'
													, content_type='$default[content_type]'
													, web_content='$default[web_content]'
													, mob_content='$default[mob_content]'
													, ref_link='$default[ref_link]'
													, ref_target='$default[ref_target]'
													, ref_program='$default[ref_program]'
													, ref_board='$default[ref_board]'
													, ref_online='$default[ref_online]'
													, ref_product='$default[ref_product]'
													, memo='$default[memo]'
													, state='$default[state]'
													, reg_date='$reg_date'
													, ip='$default[ip]'
													, writer='$_SESSION[member_id]'
							");

		$reg_date = date("Y-m-d H:i:s");
		$ip = $_SERVER['REMOTE_ADDR'];
		//$state = 'Y';
		$update_ = mysqli_query($connect, "UPDATE $setting_table SET  delete_state='Y' WHERE content_id='$content_id'");
	}
?>
