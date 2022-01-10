<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_member_config";
	$reg_date = date("Y-m-d H:i:s");
	if($use_level_auth) $level_auth = str_replace("||", "|", "|" . $use_level_auth . "|");
	if($use_user_auth) $user_auth = str_replace("||", "|", "|" . $use_user_auth . "|");
	if($use_dept_auth) $dept_auth = str_replace("||", "|", "|" . $use_dept_auth . "|");

	$query = mysqli_query($connect, "UPDATE $setting_table SET use_member_apply ='$use_member_apply'
															, default_level='$default_level'
															, use_namecheck='$use_namecheck'
															, use_captcha='$use_captcha'
															, use_level_auth='$level_auth'
															, use_user_auth='$user_auth'
															, use_dept_auth='$dept_auth'
															, use_join_point='$use_join_point'
															WHERE no = '1'");
?>