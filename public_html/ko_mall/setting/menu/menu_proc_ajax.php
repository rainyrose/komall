<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_menu_config";

	$mode = $_POST[mode];
	if(!$mode) error("비정상적인 접근입니다.");

	foreach($_POST as $v){
		${$v} = $_POST[$v];
	}
		$menu_title = htmlspecialchars_decode($menu_title);

	//기본정보
	//$default = mysqli_fetch_array(mysqli_query("SELECT * FROM $setting_table WHERE no='$menu_no'"));
	$reg_date = date("Y-m-d H:i:s");
	if($mode == "modify"){
		$update_ = mysqli_query($connect, "UPDATE $setting_table SET menu_title='$menu_title'
														, use_device_pc='$use_device_pc'
														, use_device_mob='$use_device_mob'
														, use_type='$use_type'
														, state='$state'
														, memo='$memo'
														, content_id='$content_id'
														, link_menu_id='$link_menu_id'
														, description='$description'
														, og_description='$og_description'
														, og_sitename='$og_sitename'
														, og_title='$og_title'
														, reg_date='$reg_date'
														, reg_writer='$_SESSION[member_id]'
													WHERE menu_id='$menu_no'
								");
	} else if($mode == "delete") {
		$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE menu_id='$menu_no'"));
		$update_ = mysqli_query($connect, "UPDATE $setting_table SET delete_state ='Y', state='N' WHERE menu_id='$menu_no' OR (ref_group = '$default[ref_group]' AND depth_history LIKE '%$default[menu_id]%')");
		
		if($default[depth] == "1"){
			@rename($_SERVER[DOCUMENT_ROOT] . "/contents/" . $default[dir], $_SERVER[DOCUMENT_ROOT] . "/contents/DELETE_" . date(YmdHis) . "_" . $default[dir]);
		}

		//echo "SELET * FROM $setting_table  WHERE menu_id='$menu_no' OR (ref_group = '$default[ref_group]' AND depth_history LIKE '%$default[menu_id]%')";
	}
?>
