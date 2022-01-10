<?
	$is_use_auth = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_online_config WHERE id='".$core_id."' LIMIT 1"));
	$is_use_level = $is_use_auth[use_auth_level];
	$is_use_dept = $is_use_auth[use_auth_dept];
	$is_use_person = $is_use_auth[use_auth_person];

	$is_auth_level = true;
	$is_auth_user = true;
	$is_auth_dept = true;

	$is_auth_level = ($is_use_level == "Y") ? auth_checked($connect, "check_level", $type, $core_id, $_SESSION[auth_level]) : true;
	$is_auth_dept = ($is_use_dept == "Y") ? auth_checked($connect, "check_dept", $type, $core_id, $_SESSION[member_dept]) : true;
	$is_auth_user = ($is_use_person == "Y") ? auth_checked($connect, "check_user", $type, $core_id, $_SESSION[member_id]) : true;

	if(!$is_auth_level || !$is_auth_dept || !$is_auth_user){
		error("접근권한이 없습니다.");
		exit;
	}
?>