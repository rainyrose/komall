<? 
include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";

$view_count = 0;
$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");
$return_url = explode("?", $return_url);
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
	if (isblank($sort)) error("정렬값이 없습니다.");
	/* if (isblank($name)) error("이름을 입력해 주세요.");
	if (isblank($password)) error("비밀번호를 입력해 주세요.");
	if (isblank($title)) error("제목을 입력해 주세요.");
	*/

	//파일 업로드
	if($_FILES[banner_img][size] > 0){
		$banner_img =  upload_file($dir, $_FILES[banner_img][tmp_name], $_FILES[banner_img][name]);
		$add_query .= "banner_img = '$banner_img',";
	}


	$update_query = "UPDATE $program_table SET title = '$title',
											contents = '$contents',
											link_url = '$link_url', 
											link_type = '$link_type',
											$add_query
											state = '$state',
											reg_date = '$reg_date',
											sort='$sort',
											ip = '$ip'
										WHERE no='$no' LIMIT 1";

	$result = mysql_query($update_query);

	alert("수정되었습니다.");
	url($return_url[0]."?program_id=$program_id&amp;mode=list&amp;no=$no&amp;start=$start");

	//============================== 수정 끝 ============================== //

} else if($mode == "delete"){
	
	//============================== 삭제 ============================== //

	$delete_query = "DELETE FROM $program_table WHERE no='$no'";
	$result = mysql_query($delete_query);
	alert("삭제되었습니다.");
	url($return_url[0]."?program_id=$program_id");

	//============================== 삭제 끝 ============================== //

} else {

	//============================== 등록 ============================== //
	if (isblank($program_id)) error("비정상적 접근");
	if (isblank($title)) error("배너 제목을 입력해 주세요.");
	//if (isblank($category)) error("구분을 하나이상 작성해주세요.");
	if (isblank($link_url)) error("배너 URL을 입력해 주세요.");
	//if (isblank($_FILES[banner_img][size] <= 0)) error("배너 이미지를 등록해 주세요.");

	//파일 업로드
	if($_FILES[banner_img][size] > 0){
		$banner_img =  upload_file($dir, $_FILES[banner_img][tmp_name], $_FILES[banner_img][name]);
	}

	$sort_ = mysql_fetch_array(mysql_query("SELECT * FROM $program_table ORDER BY sort DESC LIMIT 1"));
	$sort = $sort_[sort]+1;

	mysql_query("INSERT INTO $program_table VALUES ('', '$title', '$link_type', '$link_url', '$contents', '$banner_img', '$state', '$reg_date', '$ip', '$sort')");

	alert("등록되었습니다.");
	url($return_url[0]."?program_id=$program_id");

	//============================== 등록 끝 ============================== //
}

