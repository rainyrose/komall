<? 
include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";

$view_count = 0;
$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");
$return_url = explode("?", $return_url);
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/program/".$program_id."/";

/*
// 관리자 페이지 체크
if ($PHP_SELF ==  "/koweb_manager/program/manager_program.php" || $return_url[0] == "/koweb_manager/program/manager_program.php") {
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

	//파일업로드
	for($i = 1; $i <= 5; $i++){

		if($_FILES["file_".$i][size] > 0){
			${'file_'.$i} = upload_file($dir, $_FILES["file_".$i][tmp_name], $_FILES["file_".$i][name]);
			$add_query .= "file_".$i." =  '". ${'file_'.$i} . "',";
		}

		if(${'file_del_'.$i} && !${'file_'.$i}){
			$add_query .= "file_".$i." =  '',";
		}
	}

	//파일 업로드
	if($_FILES[title_img][size] > 0){
		$title_img =  upload_file($dir, $_FILES[title_img][tmp_name], $_FILES[title_img][name]);
		$add_query .= "title_img='$title_img',";
	}
	
	if($title_img_del && !$title_img){
		$add_query .= "title_img='',";
	}

	foreach($category as $value){
		$category_tmp .= $value ."|";
	}
	$category = substr($category_tmp, 0, -1);

	$update_query = "UPDATE $program_table SET title = '$title',
											work_category = '$work_category',
											category = '$category', 
											contents = '$contents',
											link_url = '$link_url', 
											$add_query
											complete_date = '$complete_date',
											sort_value = '$sort_value',
											show_main = '$show_main',
											reg_date = '$reg_date',
											ip='$ip', 
											state = '$state',
											search_category='$search_category'
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
	if (isblank($title)) error("사이트 제목을 입력해 주세요.");
	//if (isblank($category)) error("구분을 하나이상 작성해주세요.");
	if (isblank($link_url)) error("사이트 주소를 입력해 주세요.");
	//if (isblank($_FILES[title_img][size] <= 0)) error("대표이미지를 등록해 주세요.");
	
	//파일 업로드
	for($i = 1; $i <= 12; $i++){
		if($_FILES["file_".$i][size] > 0){
			${'file_'.$i} = upload_file($dir, $_FILES["file_".$i][tmp_name], $_FILES["file_".$i][name]);
		}
	}

	//파일 업로드
	$title_img =  upload_file($dir, $_FILES[title_img][tmp_name], $_FILES[title_img][name]);

	foreach($category as $value){
		$category_tmp .= $value ."|";
	}

	$category = substr($category_tmp, 0, -1);
	
	mysql_query("INSERT INTO $program_table VALUES ('', '$title', '$work_category', '$category', '$contents','$link_url', '$title_img', '$file_1', '$file_2', '$file_3', '$file_4','$file_5', '$file_6', '$file_7', '$file_8', '$file_9', '$file_10', '$file_11', '$file_12', '$complete_date', '$sort_value','$show_main','$reg_date', '$ip', 'Y', '$search_category')");

	alert("등록되었습니다.");
	url($return_url[0]."?program_id=$program_id");

	//============================== 등록 끝 ============================== //
}

