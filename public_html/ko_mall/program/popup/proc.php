<? 
include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";

$view_count = 0;
$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");
$return_url = $common_queryString;
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/program/".$program_id."/";

if($mode == "modify_proc"){

	//============================== 수정 ============================== //

	//파일 업로드
	if($_FILES[title_img][size] > 0){
		$title_img =  upload_file($dir, $_FILES[title_img][tmp_name], $_FILES[title_img][name]);
		$add_query .= "img='$title_img',";
	}

	$update_query = "UPDATE $program_table SET title = '$title',
											type = '$popup_type',
											contents = '$contents',
											link_url = '$link_url', 
											link_type = '$link_type',
											start_date = '$start_date', 
											$add_query
											end_date = '$end_date',
											state = '$state',
											width='$width', 
											height = '$height',
											position_width='$position_width',
											position_height='$position_height',
											zindex='$zindex',
											reg_date='$reg_date'
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

	//파일 업로드
	if($_FILES[title_img][size] > 0){
		$title_img =  upload_file($dir, $_FILES[title_img][tmp_name], $_FILES[title_img][name]);
	}
	
	mysqli_query($connect, "INSERT INTO $program_table VALUES ('', '$title', '$popup_type', '$contents', '$title_img', '$link_url','$link_type', '$start_date', '$end_date', '$state', '$width', '$height', '$zindex', '$position_width', '$position_height', '$reg_date')");

	alert("등록되었습니다.");
	url($return_url);

	//============================== 등록 끝 ============================== //
}

