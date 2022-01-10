<? 
include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";

$view_count = 0;
$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");
$return_url = $common_queryString;
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/program/".$program_id."/";

/*
// 관리자 페이지 체크
if ($PHP_SELF ==  "/koweb_manager/site/manager_site.php" || $return_url[0] == "/koweb_manager/site/manager_site.php") {
	$is_admin = true;
} else {
	$is_admin = false;
}
*/

if($mode == "modify_proc"){

	//============================== 수정 ============================== //

	if (isblank($program_id)) error("비정상적 접근");
	/* if (isblank($name)) error("이름을 입력해 주세요.");
	if (isblank($password)) error("비밀번호를 입력해 주세요.");
	if (isblank($title)) error("제목을 입력해 주세요.");
	*/

	$update_query = "UPDATE $program_table SET title = '$title',
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
	if (isblank($program_id)) error("비정상적 접근");
	@mysqli_query($connect, "INSERT INTO koweb_page_metatag VALUES('', '$site_url', '$description', '$og_description', '$og_site_name', '$og_title')");

	alert("등록되었습니다.");
	url($return_url);
	//============================== 등록 끝 ============================== //
}

