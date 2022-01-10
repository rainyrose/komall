<? 
include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";

$view_count = 0;
$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");
$return_url = $common_queryString;
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/program/".$program_id."/";

if($mode == "modify_proc"){

	//============================== 수정 ============================== //
	$update_query = "UPDATE $program_table SET contract_no = '$contract_no',
											company = '$company',
											title = '$title', 
											ctype = '$ctype',
											target_type = '$target_type', 
											cdate = '$cdate',
											cterm_start = '$cterm_start',
											cterm_end='$cterm_end', 
											price = '$price',
											target_name='$target_name'
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

	//============================== 등록 ============================== //
	mysqli_query($connect, "INSERT INTO $program_table SET contract_no = '$contract_no',
											company = '$company',
											title = '$title', 
											ctype = '$ctype',
											target_type = '$target_type', 
											cdate = '$cdate',
											cterm_start = '$cterm_start',
											cterm_end='$cterm_end', 
											price = '$price',
											target_name='$target_name',
											reg_date = '$reg_date'");

	alert("등록되었습니다.");
	url($return_url);

	//============================== 등록 끝 ============================== //
}

