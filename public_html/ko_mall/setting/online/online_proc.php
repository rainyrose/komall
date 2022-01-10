<?
$view_count = 0;
$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");
//$return_url = explode("?", $return_url);
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/online_form/".$online_id."/";

foreach($dept_auth as $value){
	$dept_query .= $value."|";
}
$dept_query = substr($dept_query, 0, -1);

if($use_phone_r) $use_phone .= "|{$use_phone_r}";
if($use_email_r) $use_email .= "|{$use_email_r}";
if($use_addr_r) $use_addr .= "|{$use_addr_r}";



//============================== 수정 , 등록 공용 ============================== //
//배열에 차곡차곡
for($i = 1; $i <= 10; $i++){
	//값들
	if(${"variable_name_".$i}){
		${"variable_".$i} = ${"variable_name_".$i} ."|" . ${"variable_type_".$i} ."|" .${"required_".$i} ."|" . ${"variable_state_".$i} ."|" .${"variable_id_".$i}."|" .${"variable_view_".$i}."|".${"variable_search_".$i}."|".${"variable_type_option_".$i};
	} else {
		${"variable_".$i} = "";
	}
}

if($mode == "modify_proc"){

	//============================== 수정 ============================== //
	//업데이트
	$update_query = "UPDATE koweb_online_config SET id = '$id',
											title = '$title',
											variable_count = '$variable_count',
											use_phone = '$use_phone',
											use_addr = '$use_addr',
											use_file = '$use_file',
											use_file_count = '$use_file_count',
											use_email = '$use_email',
											use_access = '$use_access',
											use_member = '$use_member',
											use_private_agree = '$use_private_agree',
											use_view = '$use_view',
											use_password='$use_password',
											use_namecheck='$use_namecheck',
											private_text = '$private_text',
											variable_1 = '$variable_1',
											variable_2 = '$variable_2',
											variable_3 = '$variable_3',
											variable_4 = '$variable_4',
											variable_5 = '$variable_5',
											variable_6 = '$variable_6',
											variable_7 = '$variable_7',
											variable_8 = '$variable_8',
											variable_9 = '$variable_9',
											variable_10 = '$variable_10',
											sort = '$sort'
										WHERE no='$no' LIMIT 1";


	$result = mysqli_query($connect, $update_query);

	alert("수정되었습니다.");

	//============================== 수정 끝 ============================== //

} else if($mode == "delete"){

	//============================== 삭제 ============================== //
	
	//auth 삭제
	$online_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_online_config WHERE no='$no' LIMIT 1"));
	@mysqli_query($connect, "DELETE FROM koweb_auth_config WHERE auth_type = 'online' AND auth_id = '$online_[id]'");
	@mysqli_query($connect, "UPDATE koweb_content_config SET ref_online = '' WHERE ref_online = '$online_[id]'");
	
	$date_ = date("YmdHis");
	$rename_ = date("YmdHis")."_deleted_".$online_[id];
	@mysqli_query($connect, "RENAME TABLE $online_[id] TO $rename_");
	rename($_SERVER[DOCUMENT_ROOT] . "/upload/online/" . $online_[id], $_SERVER[DOCUMENT_ROOT] . "/upload/online/" . $rename_);

	$delete_query = "DELETE FROM koweb_online_config WHERE no='$no'";
	$result = mysqli_query($connect, $delete_query);

	alert("삭제되었습니다.");

	//============================== 삭제 끝 ============================== //

} else {

	$id = "koweb_online_".$id;

	// 프로그램 ID 중복 체크
	$check = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM koweb_online_config WHERE id='$id'"));
	if ($check[0])	error("중복된 프로그램 ID 입니다.");

	// 관리자 폴더 생성
	mkdir($_SERVER[DOCUMENT_ROOT] . "/ko_mall/online/" . $id);
	chmod($_SERVER[DOCUMENT_ROOT] . "/ko_mall/online/" . $id, 0777);
	mkdir($_SERVER[DOCUMENT_ROOT] . "/upload/online/" . $id);
	chmod($_SERVER[DOCUMENT_ROOT] . "/upload/online/" . $id, 0777);

	mysqli_query($connect, "INSERT INTO koweb_online_config VALUES ('', '$id', '$title', '$variable_count', '$use_phone', '$use_addr','$use_file', '$use_file_count', '$use_email', '$use_access', '$use_member', '$use_private_agree','$use_view', '$use_password', '$use_namecheck', '$private_text', '$variable_1', '$variable_2', '$variable_3', '$variable_4', '$variable_5', '$variable_6', '$variable_7', '$variable_8', '$variable_9', '$variable_10', 'N','N','N', '$sort')");
	$ref_no = mysqli_insert_id($connect);

	 //스키마
	$schema = "CREATE TABLE `$id` (
	  `no` int(11) NOT NULL AUTO_INCREMENT,
	  `id` varchar(50) DEFAULT '',
	  `CI` varchar(255) DEFAULT '',
	  `DI` varchar(255) DEFAULT '',
	  `name` varchar(50) NOT NULL,
	  `password` varchar(255) DEFAULT '',
	  `phone` varchar(50) DEFAULT '',
	  `email` varchar(50) DEFAULT '',
	  `zip` varchar(10) DEFAULT '',
	  `address1` varchar(255) DEFAULT '',
	  `address2` varchar(255) DEFAULT '',
	  `variable_1` text(0) DEFAULT '',
	  `variable_2` text(0) DEFAULT '',
	  `variable_3` text(0) DEFAULT '',
	  `variable_4` text(0) DEFAULT '',
	  `variable_5` text(0) DEFAULT '',
	  `variable_6` text(0) DEFAULT '',
	  `variable_7` text(0) DEFAULT '',
	  `variable_8` text(0) DEFAULT '',
	  `variable_9` text(0) DEFAULT '',
	  `variable_10` text(0) DEFAULT '',
	  `secret` char(1) DEFAULT '',
	  `reg_date` varchar(20) NOT NULL,
	  `memo_writer` varchar(50),
	  `memo` text(0),
	  `ip` varchar(20) NOT NULL,
	  PRIMARY KEY (`no`),
	  KEY `name` (`name`)
	);";

	/*----------------------------------------------------------------------------*/
	// 작업
	/*----------------------------------------------------------------------------*/
	// 생성
	@mysqli_query($connect, $schema);


	copy($_SERVER['DOCUMENT_ROOT']."/ko_mall/online/form.html", $_SERVER[DOCUMENT_ROOT] . "/ko_mall/online/" . $id . "/form.html");
	copy($_SERVER['DOCUMENT_ROOT']."/ko_mall/online/list.html", $_SERVER[DOCUMENT_ROOT] . "/ko_mall/online/" . $id . "/list.html");
	copy($_SERVER['DOCUMENT_ROOT']."/ko_mall/online/view.html", $_SERVER[DOCUMENT_ROOT] . "/ko_mall/online/" . $id . "/view.html");
	copy($_SERVER['DOCUMENT_ROOT']."/ko_mall/online/proc.php", $_SERVER[DOCUMENT_ROOT] . "/ko_mall/online/" . $id . "/proc.php");
	copy($_SERVER['DOCUMENT_ROOT']."/ko_mall/online/auth_check.html", $_SERVER[DOCUMENT_ROOT] . "/ko_mall/online/" . $id . "/auth_check.html");

	alert("등록되었습니다.");

	//============================== 등록 끝 ============================== //
}

?>
<script type="text/javascript">
	location.href = "<?=$common_queryString?>";
</script>