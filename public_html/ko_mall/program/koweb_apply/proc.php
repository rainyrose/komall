<? 
//include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";
$view_count = 0;
$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");
$return_url = explode("?", $return_url);
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/program/".$program_id."/";
$now_date = date("Y-m-d");
$phone = $phone1."-".$phone2."-".$phone3;
if($email2) $email3 = $email2;
$email = $email1."@".$email3;

if($mode == "modify_proc"){

	//============================== 수정 ============================== //

	if (isblank($program_id)) error("비정상적 접근");
	/* if (isblank($name)) error("이름을 입력해 주세요.");
	if (isblank($password)) error("비밀번호를 입력해 주세요.");
	if (isblank($title)) error("제목을 입력해 주세요.");
	*/

	//파일 업로드
	if($_FILES[event_poster][size] > 0){
		$event_poster =  upload_file($dir, $_FILES[event_poster][tmp_name], $_FILES[event_poster][name]);
		$add_query .= "event_poster='$event_poster',";
	}

	foreach($item_tmp as $v){
		$temp_item .= $v . "^";
	}

	$temp_item = substr($temp_item, 0, -1);
	$temp_item .= "|";

	foreach($value_tmp as $v){
		$temp_item .= $v . "^";
	}

	$temp_item = substr($temp_item, 0, -1);


	$update_query = "UPDATE $program_table SET event_title = '$event_title',
											event_date = '$event_date',
											apply_start_date = '$apply_start_date', 
											apply_end_date = '$apply_end_date',
											event_addr = '$event_addr', 
											event_host = '$event_host', 
											event_support = '$event_support', 
											$add_query
											event_etc1 = '$event_etc1',
											event_etc2 = '$event_etc2', 
											event_contents1 = '$event_contents1',
											event_contents2 = '$event_contents2',
											event_item = '$temp_item',
											event_value = '$event_value',
											reg_date='$reg_date',
											sort = '$sort',
											state='$state',
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

} else if($mode == "apply_delete"){
	
	//============================== 삭제 ============================== //
	$check_ = mysql_fetch_array(mysql_query("SELECT * FROM koweb_event_person WHERE no='$person_no' LIMIT 1"));
	$delete_query = "DELETE FROM koweb_event_person WHERE no='$person_no'";
	$result = mysql_query($delete_query);
	alert("삭제되었습니다.");
	url($return_url[0]."?program_id=$program_id&mode=apply_list&no=$check_[ref_event]");


	//============================== 삭제 끝 ============================== //

} else if($mode == "apply_write_proc"){ 

	if($_SESSION[rand_auth] != $rand_auth_ ){
		error("스팸방지번호가 올바르지 않습니다.");
		exit;
	}
	$check_ = mysql_fetch_array(mysql_query("SELECT * FROM $program_table WHERE no='$no' LIMIT 1"));
	if($check_[apply_start_date] > $now_date || $check_[apply_end_date] < $now_date){
		error("사전등록신청이 기간이 아닙니다.\\r\\n\\r\\n해당 박람회의 사전등록기간은 \\r\\n\\r\\n$row[apply_start_date] ~ $row[apply_end_date]입니다.");
		exit;
	}

	$check_2 = mysql_num_rows(mysql_query("SELECT * FROM koweb_event_person WHERE name='$name' AND phone='$phone' AND ref_event='$no'"));
	
	if($check_2 > 0){
		error("동일한 정보의 신청자가 존재합니다. 무료관람신청확인 메뉴를 이용해주세요.");
		exit;
	}

	mysql_query("INSERT INTO koweb_event_person VALUES ('', '$no', '$name', '$company', '$rank','$phone', '$email', '$zip', '$address1', '$address2', '$addr_check','$sms_check', '$email_check', '$job', '$reg_date', '$ip')");
	
	alert("신청되었습니다.");
	url("/contents/03_information/program.html?program_id=koweb_event_info&mode=view&no=$no&amp;apply=$no");
} else if($mode == "apply_modify_proc"){ 

	$check_ = mysql_fetch_array(mysql_query("SELECT * FROM koweb_event_person WHERE no='$person_no' LIMIT 1"));
/*	if($now_date <= $check_[apply_start_date] || $now_date > $check_[apply_end_date]){
		error("신청 기간이 종료 되었습니다.");
		exit;
	}
*/
	$update_query = "UPDATE koweb_event_person SET name = '$name',
										company = '$company',
										rank = '$rank', 
										phone = '$phone', 
										email = '$email', 
										zip = '$zip', 
										address1 = '$address1',
										address2 = '$address2', 
										job = '$job'
									WHERE no='$person_no' LIMIT 1";

	mysql_query($update_query);
	
	alert("신청자정보가 수정되었습니다.");
	url($return_url[0]."?program_id=$program_id&mode=apply_list&no=$check_[ref_event]");

} else {

	foreach($item_tmp as $v){
		$temp_item .= $v . "^";
	}

	$temp_item = substr($temp_item, 0, -1);
	$temp_item .= "|";

	foreach($value_tmp as $v){
		$temp_item .= $v . "^";
	}

	$temp_item = substr($temp_item, 0, -1);

	//============================== 등록 ============================== //
	if (isblank($program_id)) error("비정상적 접근");
	//if (isblank($title)) error("팝업 제목을 입력해 주세요.");
	//if (isblank($category)) error("구분을 하나이상 작성해주세요.");
	//if (isblank($link_url)) error("팝업 URL을 입력해 주세요.");
	//if (isblank($_FILES[title_img][size] <= 0)) error("대표이미지를 등록해 주세요.");

	//파일 업로드
	if($_FILES[event_poster][size] > 0){
		$event_poster =  upload_file($dir, $_FILES[event_poster][tmp_name], $_FILES[event_poster][name]);
	}

	$sort_ = mysql_fetch_array(mysql_query("SELECT * FROM $program_table ORDER BY sort DESC LIMIT 1"));
	$sort = $sort_[sort]+1;

	mysql_query("INSERT INTO $program_table VALUES ('', '$event_title', '$event_date', '$apply_start_date', '$apply_end_date','$event_addr', '$event_host', '$event_support', '$event_poster', '$event_etc1', '$event_etc2','$event_contents1', '$event_contents2', '$temp_item', '$event_value', '$state', '$sort', '$reg_date', '$ip')");

	alert("등록되었습니다.");
	url($return_url[0]."?program_id=$program_id");
	//============================== 등록 끝 ============================== //
}

