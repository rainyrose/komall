<?
//include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_mall/auth_manager.php";

$ip = $REMOTE_ADDR;
if (!$reg_date) $reg_date = date("Y-m-d H:i:s");
//$return_url = explode("?", $return_url);
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/online/".$online_id."/";
$phone = $phone1."-".$phone2."-".$phone3;
if($email2) $email3 = $email2;
$email = $email1."@".$email3;

if($mode == "modify_proc"){

	//============================== 수정 ============================== //

	if (isblank($online_id)) error("비정상적 접근");

	//변수 만들기
	$add_query = "";
	for($i = 1; $i <= 10; $i++){
		//변수명 생성
		$tmp_variable = "variable_".$i;

		if(${"variable_type_".$i} == "file"){
			if($_FILES[$tmp_variable][size] > 0){
				${"variable_".$i} = upload_file($dir, $_FILES[$tmp_variable][tmp_name], $_FILES[$tmp_variable][name]);
			}
		}
		$add_query .=  ", ".$tmp_variable." = '".${"variable_".$i}."'";
	}
	
	if($password) {
		$password = hash("sha256", $password);
		$add_password = " password = '$password',";
	}

	$update_query = "UPDATE $online_table SET
											$add_password
											phone = '$phone',
											email = '$email',
											zip = '$zip',
											address1 = '$address1',
											address2 = '$address2'
											$add_query,
											secret = '$secret',
											reg_date = '$reg_date',
											ip = '$ip'
										WHERE no='$no' LIMIT 1";

	$result = mysqli_query($connect, $update_query);

	alert("수정되었습니다.");
	url($return_url);

	//============================== 수정 끝 ============================== //

} else if($mode == "delete"){

	//============================== 삭제 ============================== //

		//============================== 삭제 ============================== //
	if(!$is_admin){
		if($online[use_member] == "10"){ 
			//비밀번호가 없음
			if($password == "" ){
				url($return_url."&amp;mode=check&amp;return_mode=$mode&amp;no=$no");
				exit;
			}
			$password = hash("sha256", $password);
			$check_query = "SELECT * FROM $online_id WHERE no='$no' AND password='$password' LIMIT 1";
			$check_result = mysqli_query($connect, $check_query);
			$check_row = mysqli_num_rows($check_result);

			if($check_row < 1 ){
				error("해당 내역은 관리자 및 작성자만 삭제 가능합니다.");
				exit;
			}
			$delete_query = "DELETE FROM $online_id WHERE no='$no' AND password='$password'";
		} else {
			$check_query = "SELECT * FROM $online_id WHERE no='$no' LIMIT 1";
			$check_result = mysqli_query($connect, $check_query);
			$check_row = mysqli_fetch_array($check_result);

			if($_SESSION[member_id] != $check_row[id] ){
				error("해당 내역은 관리자 및 작성자만 삭제 가능합니다.");
				exit;
			} else {
				$delete_query = "DELETE FROM $online_id WHERE no='$no' AND id='$_SESSION[member_id]'";
			}
		}
	} else {
		$delete_query = "DELETE FROM $online_id WHERE no='$no'";
	}

	$result = mysqli_query($connect, $delete_query);
	alert("삭제되었습니다.");
	url($common_queryString);

	//============================== 삭제 끝 ============================== //

} else if($mode == "memo_write_proc"){

	//============================== 메모등록 ============================== //
	mysqli_query($connect, "INSERT INTO online_comment SET id='$online_id', writer='$_SESSION[member_id]', ref_no='$ref_no', content='$content', reg_date='$reg_date', ip='$ip'");
	alert("처리되었습니다.");
	url($return_url."&amp;mode=view&amp;no=$ref_no");

	//============================== 메모등록 끝 ============================== //
} else if($mode == "memo_delete"){

	//============================== 메모삭제 ============================== //
	if($_SESSION[auth_level] != "1"){
		$check = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM online_comment WHERE id='online_id' AND no='$comm_no'"));
		if($_SESSION[member_id] != $check[writer]){
			error("자신이 작성한 메모만 삭제 가능합니다.");
			exit;
		}
	}
	mysqli_query($connect, "DELETE FROM online_comment WHERE id='$online_id' AND no='$comm_no' LIMIT 1");
	
	alert("메모가 삭제 되었습니다.");
	url($return_url."&amp;mode=view&amp;no=$ref_no");
	//============================== 메모삭제 끝 ============================== //
} else {

	//============================== 등록 ============================== //
	if (isblank($online_id)) error("비정상적 접근");

	//변수 만들기
	$add_query = "";
	for($i = 1; $i <= 10; $i++){
		//변수명 생성
		$tmp_variable = "variable_".$i;

		if(${"variable_type_".$i} == "file"){
			if($_FILES[$tmp_variable][size] > 0){
				${"variable_".$i} = upload_file($dir, $_FILES[$tmp_variable][tmp_name], $_FILES[$tmp_variable][name]);
			}
		}
		$add_query .=  ", '".${"variable_".$i}."'";
	}
	if($password) $password = hash("sha256", $password);

	mysqli_query($connect, "INSERT INTO $online_table VALUES ('', '$_SESSION[member_id]', '$_SESSION[member_ci]', '$_SESSION[member_di]', '$_SESSION[member_name]', '$password', '$phone', '$email', '$zip', '$address1', '$address2' $add_query , '', '$reg_date', '$_SESSION[member_id]', '$memo', '$ip')");

	alert("등록되었습니다.");
	url($return_url);

	//============================== 등록 끝 ============================== //
}
