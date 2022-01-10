<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_product_category_config";

	$mode = $_POST[mode];
	if(!$mode) error("비정상적인 접근입니다.");

	foreach($_POST as $v){
		${$v} = $_POST[$v];
	}
	
	if(!$use_sell) $use_sell = "N";
	if(!$use_realname) $use_realname = "N";
	if(!$use_19) $use_19 = "N";
	
	//기본정보
	$reg_date = date("Y-m-d H:i:s");
	if($mode == "modify"){
		$update_ = mysqli_query($connect, "UPDATE $setting_table SET title='$title'
														, use_device_pc='Y'
														, use_device_mob='Y'
														, use_type='$use_type'
														, use_realname='$use_realname'
														, use_sell='Y'
														, use_19='N'
														, state='$state'
														, memo='$memo'
														, reg_date='$reg_date'
														, reg_writer='$_SESSION[member_id]'
													WHERE id='$no'
								");
	} else if($mode == "delete") {
		$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE id='$no'"));
		$update_ = mysqli_query($connect, "UPDATE $setting_table SET delete_state ='Y', state='N' WHERE id='$no' OR (ref_group = '$default[ref_group]' AND depth_history LIKE '%$default[id]%')");
	}
?>
