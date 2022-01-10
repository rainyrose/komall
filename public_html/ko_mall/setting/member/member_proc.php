<?
if(!$mode) error("비정상적인 접근입니다.");

//  ID 정리
$reg_date = date("Y-m-d H:i:s");
$phone = $phone1."-".$phone2."-".$phone3;

if($email2) $email3 = $email2;
$email = $email1."@".$email3;
$tel = $tel1."-".$tel2."-".$tel3;

$ip = $_SERVER['REMOTE_ADDR'];

//항목설정 세팅
$data_result = mysqli_query($connect, "SELECT * FROM koweb_member_item WHERE item_use = 'Y' ORDER BY no ASC");
while($data_ = mysqli_fetch_array($data_result)){
	$item_config_[$data_[item]] = array("keyname" => $data_[item]);
}

//회원설정 불러오기
$data_result2 = mysqli_query($connect, "SELECT * FROM koweb_member_config");
$member_config_ = mysqli_fetch_array($data_result2);

//필수값 체크, $[key]_required 변수형식 (예: name_required = "Y")
foreach($item_config_ as $v){
	//if($v[requierd] == "Y") echo $v[keyname];
	//${$v[keyname]."_required"} = "required";

	//불필요한정보삭제
	if($v[keyname] == "namecheck") continue;
	if($v[keyname] == "captcha") continue;
	if($v[keyname] == "private") continue;
	if($v[keyname] == "sinfo") continue;
	if($v[keyname] == "info_offer") continue;
	if($v[keyname] == "agree") continue;
	if($v[keyname] == "agree2") continue;

	//유선 / 휴대폰 -- 삭제
	if($v[keyname] == "tel"){
		${$v[keyname]} = str_replace("--", "", ${$v[keyname]});
	}

	//이메일 @ 삭제
	if($v[keyname] == "email"){
		if(${$v[keyname]} == "@"){
			${$v[keyname]} = str_replace("@", "", ${$v[keyname]});
		}
	}

	//유선 / 휴대폰 -- 삭제
	if($v[keyname] == "phone"){
		${$v[keyname]} = str_replace("010--", "", ${$v[keyname]});
		${$v[keyname]} = str_replace("011--", "", ${$v[keyname]});
		${$v[keyname]} = str_replace("016--", "", ${$v[keyname]});
		${$v[keyname]} = str_replace("017--", "", ${$v[keyname]});
		${$v[keyname]} = str_replace("018--", "", ${$v[keyname]});
		${$v[keyname]} = str_replace("019--", "", ${$v[keyname]});
	}

	if($v[keyname] == "address"){
		$add_query .= ", zip = '$zip', address1 = '$address1', address2 = '$address2'";
	} else {
		$add_query .= ", $v[keyname] = '${$v[keyname]}'";
	}
}

if($mode == "write_proc"){

	if(isblank($password)) error("비밀번호를 입력해주세요");
	if(isblank($id)) error("아이디를 입력해주세요");

	$password = hash("sha256", $password);

	$query = "INSERT INTO koweb_member SET id = '$id'
										  , password = '$password'
										  , dept = '$dept'
										  , CI = '$_SESSION[CI]'
										  , DI= '$_SESSION[CI]'
										 $add_query
										  , auth_level = '$level'
										  , contents = '$contents'
										  , is_admin = '$is_admin'
										  , reg_date = '$reg_date'
										  , state = '$state'
										  , type = ''
										  , ip = '$ip'
			";

	// 정보 입력
	@mysqli_query($connect, $query);
	$alert_txt = "등록";

} else if ($mode == "modify_proc"){

//	if(isblank($password)) error("비밀번호를 입력해주세요");
	if(isblank($id)) error("아이디를 입력해주세요");

	if($password){
		$password = hash("sha256", $password);
		$add_password = ", password='$password'";
	}

	$query = "UPDATE koweb_member SET id = '$id'
										  $add_password
										  , dept = '$dept'
										  , CI = '$_SESSION[CI]'
										  , DI= '$_SESSION[CI]'
										  , name = '$name'
										 $add_query
										  , auth_level = '$level'
										  , contents = '$contents'
										  , is_admin = '$is_admin'
										  , reg_date = '$reg_date'
										  , state = '$state'
										  , ip = '$ip'
										  WHERE no='$no'
			";

	@mysqli_query($connect, $query);

	$alert_txt = "수정";

} else if ($mode == "apply_proc"){
	$check_apply = $_POST[check_apply];

	foreach($check_apply as $value){

		@mysqli_query($connect, "UPDATE koweb_member  SET auth_level = '$member_config_[default_level]' WHERE no='$value'");
	}

	$alert_txt = "승인";

} else if($mode == "add_point"){

	echo "wow";


} else {

	$member = fetch_array("SELECT * FROM koweb_member WHERE no='{$no}'");
	@mysqli_query($connect, "DELETE FROM koweb_member WHERE no = '$no' LIMIT 1");
	@mysqli_query($connect, "DELETE FROM koweb_point WHERE member = '{$member['id']}'");
	$alert_txt = "삭제";
}

/*----------------------------------------------------------------------------*/
// 마무리
/*----------------------------------------------------------------------------*/
if($return_no != ""){
?>
	<script type="text/javascript">
	alert("<?=$alert_txt?> 되었습니다.");
	location.href = "?type=setting&core_id=setting&core=manager_setting&manager_type=dept&mode=view&no=<?=$return_no?>";
	</script>
<? } else { ?>
	<script type="text/javascript">
	alert("<?=$alert_txt?> 되었습니다.");
	location.href = "<?=$PHP_SELF?>?type=<?=$type?>&core=<?=$core?>&manager_type=<?=$manager_type?>";
	</script>
<? } ?>
