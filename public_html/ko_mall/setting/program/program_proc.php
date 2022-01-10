<? 
// 프로그램 ID 정리
$reg_date = date("Y-m-d H:i:s");

foreach($dept_auth as $value){
	$dept_query .= $value."|";
}
$dept_query = substr($dept_query, 0, -1);

if($mode == "write_proc"){

	// 프로그램 ID 중복 체크
	$check = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM $setting_table WHERE id='$id'"));
	if ($check[0])	error("중복된 프로그램 ID 입니다.");

	// 관리자 폴더 생성
	mkdir($_SERVER[DOCUMENT_ROOT] . "/ko_admin/program/" . $id);
	chmod($_SERVER[DOCUMENT_ROOT] . "/ko_admin/program/" . $id, 0777);

	mkdir($_SERVER[DOCUMENT_ROOT] . "/upload/program/" . $id);
	chmod($_SERVER[DOCUMENT_ROOT] . "/upload/program/" . $id, 0777);

	$sort_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table ORDER BY sort DESC LIMIT 1"));
	$sort = $sort_[sort]+1;

	// 프로그램 정보 입력
	@mysqli_query($connect, "INSERT INTO $setting_table VALUES ('', '$id', '$title', '$refer_table', '$user_view', '$user_list', 'N', 'N', 'N', '$is_member', '$memo', '$sort', '$reg_date', '$state')");

	$alert_txt = "등록";

} else if ($mode == "modify_proc"){
	@mysqli_query($connect, "UPDATE $setting_table SET title='$title', refer_table = '$refer_table', user_view='$user_view', user_list='$user_list', is_member= '$is_member', state='$state'   WHERE no='$no'");

	$alert_txt = "수정";
} else {

	//auth 삭제
	$program_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_program_config WHERE no='$no' LIMIT 1"));
	@mysqli_query($connect, "DELETE FROM koweb_auth_config WHERE auth_type = 'program' AND auth_id = '$program_[id]'");
	@mysqli_query($connect, "UPDATE koweb_content_config SET ref_program = '' WHERE ref_program= '$program_[id]'");

	$date_ = date("YmdHis");
	$rename_ = date("YmdHis")."_deleted_".$program_[id];
	@mysqli_query($connect, "RENAME TABLE $program_[id] TO $rename_");
	rename($_SERVER[DOCUMENT_ROOT] . "/upload/program/" . $program_[id], $_SERVER[DOCUMENT_ROOT] . "/upload/program/" . $rename_);

	@mysqli_query($connect, "DELETE FROM $setting_table WHERE no = '$no' LIMIT 1");
	$alert_txt = "삭제";
}

/*----------------------------------------------------------------------------*/
// 마무리
/*----------------------------------------------------------------------------*/
?>
<script type="text/javascript">
	alert("<?=$alert_txt?> 되었습니다.");
	location.href = "<?=$PHP_SELF?>?type=<?=$type?>&core=<?=$core?>&manager_type=<?=$manager_type?>&amp;keyword=<?=$keyword?>&amp;search_key=<?=$search_key?>&amp;keyword=<?=$keyword?>";
</script>