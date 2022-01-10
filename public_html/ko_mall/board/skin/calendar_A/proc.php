<? 
//php 에러 error
if($mode != "delete" && $mode != "comment_proc"){
	include_once  $_SERVER['DOCUMENT_ROOT'] . "/head.php";
}

// 필드 체크
foreach (array_keys($_POST) as $value) {
	${$value} = sanitizeString($_POST[$value]);
	// ${$value} = sanitizemysqli($_POST[$value]);
}
$comm_comments = $_POST[comm_comments];
$comments = $_POST[comments];

$board = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_board_config WHERE id='$board_id' LIMIT 1"));
//$password = hash("sha256", $password);
$view_count = 0;
$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");

//$return_url = explode("?", $return_url);
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/".$board_id."/";

if($mode == "modify_proc"){

	//============================== 수정 ============================== //

	if (isblank($board_id)) error("비정상적 접근");
	if (isblank($name)) error("이름을 입력해 주세요.");
	if ($board[is_membership] != "Y"){ if(isblank($password)) error("비밀번호를 입력해 주세요."); }
	if (isblank($title)) error("제목을 입력해 주세요.");
	if (isblank($password)) error("비정상적 접근");
	if ($board[spam_auth] == "Y"){
		if (isblank($_SESSION[rand_auth])) error("비정상적 접근");

		if($_SESSION[rand_auth] != $rand_auth_ ){
			error("스팸방지번호가 올바르지 않습니다.");
			exit;
		}
	}

	if($board[is_membership] != "Y"){
		$password = hash("sha256", $password);
	} else {
		$password = hash("sha256", $_SESSION[member_id]);
	}

	if(!$is_admin){
		if($board[is_membership] == "Y"){ 
			$check_query = "SELECT * FROM $board_id WHERE no='$no' LIMIT 1";
			$check_result = mysqli_query($connect, $check_query);
			$check_row = mysqli_fetch_array($check_result);

			if($_SESSION[member_id] != $check_row[id] ){
				error("해당게시물은 관리자 및 작성자만 수정 가능합니다.");
				exit;
			}
		} 
	}

	//파일업로드
	for($i = 1; $i <= $board[file_count]; $i++){
		if($_FILES["file_".$i][size] > 0){
			if ($_FILES["file_".$i][size] > $board[file_limit_size] * 1024 * 1024) {
				error("첨부파일 ".$i."의 용량 제한은 $board[file_limit_size]M 입니다.");	
				exit;
			} else {
				${'file_'.$i} = upload_file($dir, $_FILES["file_".$i][tmp_name], $_FILES["file_".$i][name]);
				$add_query .= "file_".$i." =  '". ${'file_'.$i} . "',";
			}
		}
	}

	$modify_row = mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM $board_id WHERE no = '$no' LIMIT 1"));
	for($i = 1; $i <= $board[file_count]; $i++){
		if(${'del_'.$i} == "Y"){
			$add_query .= "file_".$i." =  '',";
			@unlink($dir . $modify_row['file_'.$i]);
			@unlink($dir . "thumb_".$modify_row['file_'.$i]);
		}
	}

	$update_query = "UPDATE $board_id SET id='$_SESSION[member_id]',
											CI='$_SESSION[CI]',
											DI='$_SESSION[DI]',
											name='$name',
											password='$password',
											phone='$phone',
											email='$email',
											zip='$zip',
											address1='$address1',
											address2='$address2',
											category='$category',
											title='$title',
											comments_type='$comments_type',
											tag_type='$tag_type',
											comments='$comments',
											etc='$etc',
											notice='$notice',
											secret='$secret',
											file_type='$file_type',
											$add_query
											ip='$ip',
											reply_state='$reply_state',
											reply_id='$reply_id',
											reply_name='$reply_name',
											reply_phone='$reply_phone',
											reply_email='$reply_email',
											reply_comments='$reply_comments',
											reply_ip='$reply_ip',
											reply_date='$reply_date',
											reply_file_1='$reply_file_1',
											reply_file_2='$reply_file_2',
											reply_file_3='$reply_file_3',
											reply_file_4='$reply_file_4',
											reply_file_5='$reply_file_5',
											hidden='$hidden',
											link='$link',
											metatag_content='$metatag_content',
											select_date='$select_date'
										WHERE no='$no' LIMIT 1";

	$result = mysqli_query($connect, $update_query);
	alert("수정되었습니다.");
	if($is_admin){
		url($_SERVER['PHP_SELF'] . "?type=board&core=manager_board&core_id=$board_id&keyword=$keyword&search_key=$search_key&amp;select_date=$select_date");
	} else {
		url($return_url."&start=$start&keyword=$keyword&search_key=$search_key&category=$category&amp;select_date=$select_date");
	}


	//============================== 수정 끝 ============================== //

} else if($mode == "delete"){
	
	//============================== 삭제 ============================== //

	if(!$is_admin){
		if($board[is_membership] != "Y"){ 
			//비밀번호가 없음
			if($password == "" ){
				url($return_url."&amp;mode=check&amp;return_mode=$mode&amp;no=$no");
				exit;
			}
			$password = hash("sha256", $password);
			$check_query = "SELECT * FROM $board_id WHERE no='$no' AND password='$password' LIMIT 1";
			$check_result = mysqli_query($connect, $check_query);
			$check_row = mysqli_num_rows($check_result);

			if($check_row < 1 ){
				error("해당게시물은 관리자 및 작성자만 삭제 가능합니다.");
				exit;
			}
			$delete_query = "DELETE FROM $board_id WHERE no='$no' AND password='$password'";
		} else {
			$check_query = "SELECT * FROM $board_id WHERE no='$no' LIMIT 1";
			$check_result = mysqli_query($connect, $check_query);
			$check_row = mysqli_fetch_array($check_result);

			if($_SESSION[member_id] != $check_row[id] ){
				error("해당게시물은 관리자 및 작성자만 삭제 가능합니다.");
				exit;
			} else {
				$delete_query = "DELETE FROM $board_id WHERE no='$no' AND id='$_SESSION[member_id]'";
			}
		}
	} else {
		$delete_query = "DELETE FROM $board_id WHERE no='$no'";
	}
	$result = mysqli_query($connect, $delete_query);

	alert("삭제되었습니다.");
	if($is_admin){
		url($_SERVER['PHP_SELF'] . "?type=board&core=manager_board&core_id=$board_id&keyword=$keyword&search_key=$search_key&amp;select_date=$select_date");
	} else {
		url($return_url."&start=$start&keyword=$keyword&search_key=$search_key&category=$category&amp;select_date=$select_date");
	}

	//============================== 삭제 끝 ============================== //

	
} else {

	//============================== 등록 ============================== //

	if (isblank($board_id)) error("비정상적 접근");
	if (isblank($name)) error("이름을 입력해 주세요.");
	if ($board[is_membership] != "Y"){ if(isblank($password)) error("비밀번호를 입력해 주세요."); }
	if (isblank($title)) error("제목을 입력해 주세요.");
	if ($board[spam_auth] == "Y"){
		if (isblank($_SESSION[rand_auth])) error("비정상적 접근");

		if($_SESSION[rand_auth] != $rand_auth_ ){
			error("스팸방지번호가 올바르지 않습니다.");
			exit;
		}
	}
	
	//파일 업로드
	for($i = 1; $i <= $board[file_count]; $i++){
		if($_FILES["file_".$i][size] > 0){
			if ($_FILES["file_".$i][size] > $board[file_limit_size] * 1024 * 1024) {
				alert("첨부파일 ".$i." 의 용량 제한은 $board[file_limit_size]M 입니다.");	
			} else {
				${'file_'.$i} = upload_file($dir, $_FILES["file_".$i][tmp_name], $_FILES["file_".$i][name]);
			}
		}
	}

	if ($board[is_membership] != "Y"){
		$password = hash("sha256", $password);
	} else {
		$password = hash("sha256", $_SESSION[member_id]);
	}

	$insert_query = "INSERT $board_id SET depth='$depth',
											ref_no='$ref_no',
											ref_group='$ref_group',
											id='$_SESSION[member_id]',
											CI='$_SESSION[CI]',
											DI='$_SESSION[DI]',
											name='$name',
											password='$password',
											phone='$phone',
											email='$email',
											zip='$zip',
											address1='$address1',
											address2='$address2',
											category='$category',
											title='$title',
											comments_type='$comments_type',
											tag_type='$tag_type',
											comments='$comments',
											etc='$etc',
											notice='$notice',
											secret='$secret',
											file_type='$file_type',
											file_1='$file_1',
											file_2='$file_2',
											file_3='$file_3',
											file_4='$file_4',
											file_5='$file_5',
											file_6='$file_6',
											file_7='$file_7',
											file_8='$file_8',
											file_9='$file_9',
											file_10='$file_10',
											reg_date = '$reg_date',
											ip='$ip',
											reply_state='$reply_state',
											reply_id='$reply_id',
											reply_name='$reply_name',
											reply_phone='$reply_phone',
											reply_email='$reply_email',
											reply_comments='$reply_comments',
											reply_ip='$reply_ip',
											reply_date='$reply_date',
											reply_file_1='$reply_file_1',
											reply_file_2='$reply_file_2',
											reply_file_3='$reply_file_3',
											reply_file_4='$reply_file_4',
											reply_file_5='$reply_file_5',
											view_count='$view_count',
											hidden='$hidden',
											link='$link',
											metatag_content='$metatag_content',
											select_date='$select_date'";

	mysqli_query($connect, $insert_query);
	$ref_no = mysqli_insert_id($connect);

	mysqli_query($connect, "UPDATE $board_id SET ref_no = '$ref_no' WHERE no='$ref_no' LIMIT 1");
	alert("등록되었습니다.");

	if($is_admin){
		url($_SERVER['PHP_SELF'] . "?type=board&core=manager_board&core_id=$board_id&keyword=$keyword&search_key=$search_key&amp;select_date=$select_date");
	} else {
		url($return_url."&start=$start&keyword=$keyword&search_key=$search_key&category=$category&amp;select_date=$select_date");
	}

	//============================== 등록 끝 ============================== //
}

