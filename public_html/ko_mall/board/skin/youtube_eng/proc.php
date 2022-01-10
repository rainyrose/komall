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

	//============================== Modify ============================== //

	if (isblank($board_id)) error("Abnormal approach");
	if (isblank($name)) error("Input your name, please.");
	if ($board[is_membership] != "Y"){ if(isblank($password)) error("Please enter a password."); }
	if (isblank($title)) error("Please enter the subject.");
	if ($board[spam_auth] == "Y"){
		if (isblank($_SESSION[rand_auth])) error("Abnormal approach");

		if($_SESSION[rand_auth] != $rand_auth_ ){
			error("The antispam number is invalid.");
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
				error("This post can only be edited by administrators and authors.");
				exit;
			}
		} 
	}

	//파일업로드
	for($i = 1; $i <= $board[file_count]; $i++){
		if($_FILES["file_".$i][size] > 0){
			if ($_FILES["file_".$i][size] > $board[file_limit_size] * 1024 * 1024) {
				error("Attachments ".$i."의 용량 제한은 $board[file_limit_size]M 입니다.");	
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
											metatag_content='$metatag_content',
											link='$link'
										WHERE no='$no' LIMIT 1";

	$result = mysqli_query($connect, $update_query);

	alert("It is changed.");
	url($return_url);

	//============================== Modify 끝 ============================== //

} else if($mode == "delete"){
	
	//============================== delete ============================== //
	if(!$is_admin){
		if($board[is_membership] != "Y"){ 
			//password가 없음
			if($password == "" ){
				url($return_url."&amp;mode=check&amp;return_mode=$mode&amp;no=$no");
				exit;
			}

			$password = hash("sha256", $password);
			$check_query = "SELECT * FROM $board_id WHERE no='$no' AND password='$password' LIMIT 1";
			$check_result = mysqli_query($connect, $check_query);
			$check_row = mysqli_num_rows($check_result);

			if($check_row < 1 ){
				error("This post can only be deleted by the administrator and author.");
				exit;
			}

			$delete_query = "DELETE FROM $board_id WHERE no='$no' AND password='$password'";
		} else {
			$check_query = "SELECT * FROM $board_id WHERE no='$no' LIMIT 1";
			$check_result = mysqli_query($connect, $check_query);
			$check_row = mysqli_fetch_array($check_result);

			if($_SESSION[member_id] != $check_row[id] ){
				error("This post can only be deleted by the administrator and author.");
				exit;
			} else {
				$delete_query = "DELETE FROM $board_id WHERE no='$no' AND id='$_SESSION[member_id]'";
			}
		}
		$check2_select = "SELECT * FROM $board_id WHERE ref_no='$no' AND depth > 0";
		$check_ = mysqli_num_rows(mysqli_query($connect, $check2_select));

		if($check_ > 0) { 
			error("There is a reply registered in the lower part and cannot be deleted.");
			exit;
		}

	} else {
		$check2_select = "SELECT * FROM $board_id WHERE ref_no='$no' AND depth > 0";
		$check_ = mysqli_num_rows(mysqli_query($connect, $check2_select));

		if($check_ > 0) { 
		//	error("There is a reply registered in the lower part and cannot be deleted.");
		//	exit;
		}

		$delete_query = "DELETE FROM $board_id WHERE no='$no'";
	}

	$result = mysqli_query($connect, $delete_query);

	alert("Deleted");
	if($is_admin){
		url($_SERVER['PHP_SELF'] . "?type=board&core=manager_board&core_id=$board_id&keyword=$keyword&search_key=$search_key");
	} else {
		url($return_url."&start=$start&keyword=$keyword&search_key=$search_key&category=$category");
	}

	//============================== delete 끝 ============================== //

} else if($mode == "comment_proc"){

	//============================== 코멘트 ============================== //


	if($comment_mode != "delete"){
		if (isblank($board_id)) error("Abnormal approach");
		if (isblank($comm_name)) error("Input your name, please.");
		//if (!$auth_comment) error("코멘트 Enrollment 권한이 없습니다.");
	}

	if($board[is_membership] != "Y"){
		if (isblank($comm_password)) error("Please enter a password.");
	}

	if($comment_mode == "delete"){

			//=========== 코멘트delete ===========//
		if(!$is_admin){
			if($board[is_membership] != "Y"){
				$comm_password = hash("sha256", $comm_password);
				$check_row = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM board_comment WHERE no = '$comm_no' LIMIT 1"));
				if($check_row[password] != $comm_password) error("Please check your password.");
				$DEL_WHERE = "AND password='$comm_password'";
			} else {
				if(!$_SESSION[member_id]){ error("Please login"); }
				$check_row = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM board_comment WHERE no = '$comm_no' LIMIT 1"));
				if($check_row[id] != $_SESSION[member_id]) error("You can only delete comments that you have registered");
				$DEL_WHERE = "AND id = '$_SESSION[member_id]'";
			}
		}

		$comment_query = "DELETE FROM board_comment  WHERE no='$comm_no' AND board_id='$board_id' $DEL_WHERE";
		$comment_result = mysqli_query($connect, $comment_query);

		if($comment_result){
			alert("Comments have been deleted.");
			$_SESSION[this_info] = hash("sha256", $ref_board_no);
			url($return_url."&mode=view&no=$ref_board_no");
		} else {
			alert("Deletion of comment failed because an error occurred.");
			url($return_url."&mode=view&no=$ref_board_no");
		}

		//=========== 코멘트delete 끝===========//

	} else if ($comment_mode == "append"){

		//=========== 코멘트의 코멘트 (코멘트추가)===========//

		if ($board[is_membership] != "Y"){
			$comm_password = hash("sha256", $comm_password);
		} else {
			$comm_password = hash("sha256", $_SESSION[member_id]);
		}

		$comment_query = "INSERT INTO board_comment VALUES('', '$_SESSION[member_id]', '$board_id', '$ref_board_no', '', '', '', '$CI', '$DI', '$comm_name', '$comm_password', '$comm_phone', '$comm_email', '$comm_zip', '$comm_address1', '$comm_address2', ' $comm_title', '$comm_comments', '$comm_file_1', '$comm_file_2' ,'$comm_file_3', '$reg_date' , '$REMOTE_ADDR')";
		$comment_result = mysqli_query($connect, $comment_query);

		$check_row = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM board_comment WHERE ref_group = '$ref_group' ORDER BY ref_depth DESC LIMIT 1"));
		if($check_row[ref_depth]){
			$ref_depth = $check_row[ref_depth] + 1;
		} else {
			$ref_depth = 1;
		}

		$bref = mysqli_insert_id($connect);
		$update_query = "UPDATE board_comment SET ref_no = '$ref_no', ref_depth='$ref_depth', ref_group='$ref_group' WHERE no='$bref'";
		$update_result = mysqli_query($connect, $update_query);

		if($update_query){
			alert("Your comment has been registered.");
			$_SESSION[this_info] = hash("sha256", $ref_board_no);
			url($return_url."&mode=view&no=$ref_board_no");
		} else {
			alert("Comment registration failed due to an error");
			url($return_url."&mode=view&no=$ref_board_no");
		}

		//=========== 코멘트의 코멘트 (코멘트추가) 끝===========//

	} else {

	//=========== 코멘트Enrollment ===========//
		if ($board[is_membership] != "Y"){
			$comm_password = hash("sha256", $comm_password);
		} else {
			$comm_password = hash("sha256", $_SESSION[member_id]);
		}

		$comment_query = "INSERT INTO board_comment VALUES('', '$_SESSION[member_id]', '$board_id', '$ref_board_no', '', '', '', '$CI', '$DI', '$comm_name', '$comm_password', '$comm_phone', '$comm_email', '$comm_zip', '$comm_address1', '$comm_address2', ' $comm_title', '$comm_comments', '$comm_file_1', '$comm_file_2' ,'$comm_file_3', '$reg_date' , '$REMOTE_ADDR')";

		$comment_result = mysqli_query($connect, $comment_query);
		$bref = mysqli_insert_id($connect);
		$update_query = "UPDATE board_comment SET ref_no = '$bref', ref_group = '$bref', ref_depth = '1' WHERE no='$bref'";
		$update_result = mysqli_query($connect, $update_query);

		if($comment_result){
			alert("Your comment has been registered.");
			$_SESSION[this_info] = hash("sha256", $ref_board_no);
			url($return_url."&mode=view&no=$no");
		} else {
			alert("Comment registration failed due to an error");
			url($return_url."&mode=view&no=$no");
		}
	}
	//=========== 코멘트Enrollment 끝===========//

	//============================== 코멘트 끝 ============================== //

} else if($mode == "reply_proc") {

	//=========== answerEnrollment ===========//

	$depth = 1;
	$ref_group = $ref_no;

	if(!$ref_no){
		error("정상적인 접근이 아닙니다.");
		exit;
	}

	//파일 업로드
	for($i = 1; $i <= $board[file_count]; $i++){
		if($_FILES["file_".$i][size] > 0){
			if ($_FILES["file_".$i][size] > $board[file_limit_size] * 1024 * 1024) {
				alert("Attachments".$i." 의 용량 제한은 $board[file_limit_size]M 입니다.");	
			} else {
				${'file_'.$i} = upload_file($dir, $_FILES["file_".$i][tmp_name], $_FILES["file_".$i][name]);
			}
		}
	}
	
	$check_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $board_id WHERE no='$ref_no' ORDER BY depth DESC LIMIT 1"));
	if($check_[depth] != 0){
		$depth = $check_[depth] + 1;
	} else {
		$depth = 1;
	}

	if ($board[is_membership] != "Y"){
		$password = hash("sha256", $password);
	} else {
		$password = hash("sha256", $_SESSION[member_id]);
	}
	$insert_query = "INSERT $board_id SET depth='$depth',
											ref_no='$ref_no',
											ref_group='$check_[ref_group]',
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
											metatag_content='$metatag_content',
											hidden='$hidden',
											link='$link'";
	mysqli_query($connect, $insert_query);
	alert("You are registered.");

	url($return_url);

	//=========== answerEnrollment 끝 ===========//
	
	
} else {

	//============================== Enrollment ============================== //

	if (isblank($board_id)) error("Abnormal approach");
	if (isblank($name)) error("Input your name, please.");
	if ($board[is_membership] != "Y"){ if(isblank($password)) error("Please enter a password."); }
	if (isblank($title)) error("Please enter the subject.");
	if ($board[spam_auth] == "Y"){
		if (isblank($_SESSION[rand_auth])) error("Abnormal approach");

		if($_SESSION[rand_auth] != $rand_auth_ ){
			error("The antispam number is invalid.");
			exit;
		}
	}
		
	//파일 업로드
	for($i = 1; $i <= $board[file_count]; $i++){
		if($_FILES["file_".$i][size] > 0){
			if ($_FILES["file_".$i][size] > $board[file_limit_size] * 1024 * 1024) {
				alert("Attachments ".$i." 의 용량 제한은 $board[file_limit_size]M 입니다.");	
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
											metatag_content='$metatag_content',
											link='$link'";
	mysqli_query($connect, $insert_query);
	$ref_no = mysqli_insert_id($connect);

	mysqli_query($connect, "UPDATE $board_id SET ref_no = '$ref_no', ref_group = '$ref_no' WHERE no='$ref_no' LIMIT 1");
	alert("You are registered.");
	url($return_url);

	//============================== Enrollment 끝 ============================== //
}

