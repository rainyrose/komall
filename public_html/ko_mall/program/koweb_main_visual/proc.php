<? 
include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";

$view_count = 0;
$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");
$return_url = "/ko_mall/index.html?type=program&core=manager_program&core_id=koweb_main_visual";
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

	//파일 업로드
	if($_FILES[pc_img][size] > 0){ 
			if($_FILES[main_img][size] > 2 * 1024 * 1024){ 
				error("2MB 이상 파일을 첨부 할 수 없습니다.");
				exit;
			}
		$pc_img =  upload_file($dir, $_FILES[pc_img][tmp_name], $_FILES[pc_img][name]); 
		$add_query1 = "pc_img='$pc_img', ";
	}

	if($_FILES[mob_img][size] > 0){ 
			if($_FILES[mob_img][size] > 2 * 1024 * 1024){ 
				error("2MB 이상 파일을 첨부 할 수 없습니다.");
				exit;
			}
		$mob_img =  upload_file($dir, $_FILES[mob_img][tmp_name], $_FILES[mob_img][name]); 
		$add_query2 = "mob_img='$mob_img', ";
	}

	$update_query = "UPDATE $program_table SET $add_query1
											$add_query2
											title = '$title',
											link='$link',
											reg_date='$reg_date'
										WHERE no='$no' LIMIT 1";

	$result = mysqli_query($connect, $update_query);

	alert("수정되었습니다.");
	url($return_url);

	//============================== 수정 끝 ============================== //

} else if($mode == "delete"){
	
	//============================== 삭제 ============================== //


	$tmp_sort = mysqli_fetch_array(mysqli_query("SELECT * FROM $program_table WHERE no = '$no'"));
	$update_query = "UPDATE $program_table SET sort = sort -1 WHERE sort > '$tmp_sort[sort]'";
	mysqli_query($connect, $update_query);
	
	$delete_query = "DELETE FROM $program_table WHERE no='$no'";
	$result = mysqli_query($connect, $delete_query);
	@unlink($dir."/".$tmp_sort[title_img1]);
	@unlink($dir."/thumb_".$tmp_sort[title_img1]);

	alert("삭제되었습니다.");
	url($return_url);

	//============================== 삭제 끝 ============================== //

} else if($mode == "sort"){
	
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $program_table WHERE no='$no'"));



	if($sort_mode == "up"){
		$sort_WHERE = "AND sort < '$default[sort]' ORDER BY sort DESC";
	} else {
		$sort_WHERE = "AND sort > '$default[sort]' ORDER BY sort ASC";
	}

	$query = "SELECT * FROM $program_table WHERE 1=1 $sort_WHERE LIMIT 1";
	$result = mysqli_query($connect, $query);
	$result2 = mysqli_query($connect, $query);
	$check_row = mysqli_fetch_array($result);	

	if($check_row[0]){
		$prev_data = mysqli_fetch_array($result2);
		$tmp_sort = "";
		$tmp_sort = $default[sort];
		$default[sort] = $prev_data[sort];
		$prev_data[sort] = $tmp_sort;

		if(!$prev_data[sort]) $prev_data[sort] = 1;
		$prev_update = mysqli_query($connect, "UPDATE $program_table SET sort='$prev_data[sort]' WHERE no='$prev_data[no]'");
		$default = mysqli_query($connect, "UPDATE $program_table SET sort='$default[sort]' WHERE no='$default[no]'");
	}

	alert("처리되었습니다.");
	url($return_url);

	//============================== 삭제 끝 ============================== //

}  else {

	//============================== 등록 ============================== //
	if (isblank($program_id)) error("비정상적 접근");
	//if (isblank($title)) error("팝업 제목을 입력해 주세요.");
	//if (isblank($category)) error("구분을 하나이상 작성해주세요.");
	//if (isblank($link_url)) error("팝업 URL을 입력해 주세요.");
	//if (isblank($_FILES[title_img][size] <= 0)) error("대표이미지를 등록해 주세요.");

	if($_FILES[pc_img][size] > 0){
		if($_FILES[pc_img][size] > 2 * 1024 * 1024){ 
			error("2MB 이상 파일을 첨부 할 수 없습니다.");
			exit;
		}
		$pc_img =  upload_file($dir, $_FILES[pc_img][tmp_name], $_FILES[pc_img][name]);
	}

	if($_FILES[mob_img][size] > 0){
		if($_FILES[mob_img][size] > 2 * 1024 * 1024){ 
			error("2MB 이상 파일을 첨부 할 수 없습니다.");
			exit;
		}
		$mob_img =  upload_file($dir, $_FILES[mob_img][tmp_name], $_FILES[mob_img][name]);
	}


	$tmp_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $program_table ORDER BY sort DESC LIMIT 1"));
	$sort = $tmp_sort[sort]+1;

	mysqli_query($connect, "INSERT INTO $program_table VALUES ('', '$title', '$pc_img', '$mob_img', '$link', '$sort', '$reg_date')");
	
	alert("등록되었습니다.");
	url($return_url);

	//============================== 등록 끝 ============================== //
}

