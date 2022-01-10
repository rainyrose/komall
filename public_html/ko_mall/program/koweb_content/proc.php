<?
include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";

$ip = $_SERVER['REMOTE_ADDR'];
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
	/* if (isblank($name)) error("이름을 입력해 주세요.");
	if (isblank($password)) error("비밀번호를 입력해 주세요.");
	if (isblank($title)) error("제목을 입력해 주세요.");
	*/


	$query = "UPDATE koweb_content SET modi_id='{$_SESSION['member_id']}' , title='{$title}' , contents='{$contents}' , modi_date='{$reg_date}' , ip='{$ip}' WHERE no='{$no}'";
    query($query);


	alert("수정되었습니다.");
	url($return_url[0]."?type=program&core=manager_program&program_id=$program_id");

	//============================== 수정 끝 ============================== //

} else if($mode == "delete"){

	//============================== 삭제 ============================== //

	$delete_query = "DELETE FROM $program_table WHERE no='$no'";
	$result = query($delete_query);
	alert("삭제되었습니다.");
	url($return_url[0]."?type=program&core=manager_program&program_id=$program_id");

	//============================== 삭제 끝 ============================== //

} else {

	//============================== 등록 ============================== //
	if (isblank($program_id)) error("비정상적 접근");
	if (isblank($title)) error("타이틀을 입력해 주세요.");

	$query = "INSERT INTO koweb_content SET id='{$_SESSION['member_id']}' , title='{$title}' , contents='{$contents}' , reg_date='{$reg_date}' , ip='{$ip}'";
    query($query);

	alert("등록되었습니다.");
	url($return_url[0]."?type=program&core=manager_program&program_id=$program_id");

	//============================== 등록 끝 ============================== //
}
