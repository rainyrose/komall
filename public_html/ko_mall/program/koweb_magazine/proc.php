<? 
include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";

$view_count = 0;
$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");
$return_url = $common_queryString;
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/program/".$program_id."/";

if($mode == "modify_proc"){

	//============================== 수정 ============================== //
	$update_query = "UPDATE $program_table SET magazine_type = '$magazine_type',
											id = '$_SESSION[member_id]',
											name = '$name', 
											zip = '$zip',
											address1 = '$address1', 
											address2 = '$address2',
											reg_date = '$reg_date',
											ip='$_SERVER[REMOTE_ADDR]' 
										WHERE no='$no' LIMIT 1";

	$result = mysqli_query($connect, $update_query);

	alert("수정되었습니다.");
	url($return_url);

	//============================== 수정 끝 ============================== //

} else if($mode == "delete"){
	
	//============================== 삭제 ============================== //

	$delete_query = "DELETE FROM $program_table WHERE no='$no'";
	$result = mysqli_query($connect, $delete_query);
	alert("삭제되었습니다.");
	url($return_url);

	//============================== 삭제 끝 ============================== //

} else {

	//============================== 등록 ============================== //\
	$check = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $program_table WHERE id='$_SESSION[member_id]'"));
	if($check > 0){
		$query = "UPDATE $program_table SET magazine_type = '$magazine_type', name = '$name', zip = '$zip', address1 = '$address1', address2 = '$address2', reg_date = '$reg_date', ip='$_SERVER[REMOTE_ADDR]' WHERE id='$_SESSION[member_id]'";
	} else {
		$query = "INSERT INTO $program_table SET magazine_type = '$magazine_type', id = '$_SESSION[member_id]', name = '$name', zip = '$zip', address1 = '$address1', address2 = '$address2', reg_date = '$reg_date', ip='$_SERVER[REMOTE_ADDR]'";
	}

	mysqli_query($connect, $query);

	alert("등록되었습니다.");
	url($return_url);

	//============================== 등록 끝 ============================== //
}

