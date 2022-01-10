<?
if($site_language == "eng"){
	if(!$mode) error("Abnormal approach.");
}else{
	if(!$mode) error("비정상적인 접근입니다.");
}

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

if($mode == "join_proc"){
	if($_SESSION['NAVER']){
		$id = "NAVER_".$_SESSION['NAVER']['id'];
		$password = hash("sha256", $id);

		$add_query .= ",type = 'NAVER'";
	} else if($_SESSION['KAKAO']){
		$id = "KAKAO_".$_SESSION['KAKAO']['id'];
		$password = hash("sha256", $id);

		$add_query .= ",type = 'KAKAO'";
	} else if($_SESSION['facebook_token']){
		$facebook_return = facebook_token_check($_SESSION['facebook_token'],"email,name");
		if($facebook_return[error]){
			error("Please again");
		}

		$id = "FB_".$facebook_return['id'];
		$password = $id;
		$add_query .= ",type = 'FB'";
	}else{
		preg_match_all('/[0-9]/', $password, $match_01);
		$numCk = count($match_01[0]);
		preg_match_all('/[a-z]/', $password, $match_02);
		$engCk = count($match_02[0]);
		preg_match_all("/[~!@\#$%^*\()\-=+_']/", $password, $match_03);
		$specialCk = count($match_03[0]);
		$stringLen = mb_strlen($password,"utf-8");
		if($stringLen < 8 || $stringLen > 12){
			error("비밀번호는 8~12자 사이로 입력해 주세요.");
		}
		if($numCk <= 0 || $engCk <= 0 || $specialCk <= 0){
			error("비밀번호는 영문,특수문자,숫자 조합 형태로 입력해 주세요.");
		}
		if($site_language == "eng"){
			if(isblank($password)) error("Please enter a password");
			if(isblank($id)) error("Please enter your ID");

			$password = hash("sha256", $password);

			if ($member_config_[use_captcha] == "Y"){
				if (isblank($_SESSION[rand_auth])) error("Abnormal approach");

				if($_SESSION[rand_auth] != $rand_auth_ ){
					error("The antispam number is invalid.");
					exit;
				}
			}
			$email_check = mysqli_num_rows(query("SELECT * FROM koweb_member WHERE email='{$email}'"));
			if($email_check > 0){
				error("There is a duplicate email.");
				exit;
			}
		}else{
			if(isblank($password)) error("비밀번호를 입력해주세요");
			if(isblank($id)) error("아이디를 입력해주세요");

			$password = hash("sha256", $password);

			if ($member_config_[use_captcha] == "Y"){
				if (isblank($_SESSION[rand_auth])) error("비정상적 접근");

				if($_SESSION[rand_auth] != $rand_auth_ ){
					error("스팸방지번호가 올바르지 않습니다.");
					exit;
				}
			}
			$email_check = mysqli_num_rows(query("SELECT * FROM koweb_member WHERE email='{$email}'"));
			if($email_check > 0){
				error("중복된 이메일이 있습니다.");
				exit;
			}
		}


		$add_query .= ",type = ''";
	}




	if($member_config_[use_member_apply] == "Y"){
		$level = 9;
	} else {
		$level = $member_config_[default_level];
	}

	if($_FILES[address_file][size] > 0){
		$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/member/";
		$add_query  .= ", address_file = '".upload_file($dir, $_FILES[address_file][tmp_name], $_FILES[address_file][name])."'";
	}

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
										  , state = 'Y'
										  , ip = '$ip'
			";
	// 정보 입력
	@mysqli_query($connect, $query);

	$point_query = "INSERT INTO koweb_point SET member = '$id'
									  , point = '$member_config_[use_join_point]'
									  , point_type = '회원가입'
									  , reg_date = '$reg_date'

		";

	// 정보 입력
	if($member_config_[use_join_point] > 0){
		@mysqli_query($connect, $point_query);
	}


	if($site_sms[sms_use] == "Y" && $site_sms[join_sms] == "Y"){
		$_SESSION[member_id] = $id;
		$str = trans_sms_order($connect, "", $site_sms[join_sms_content]);
		@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $phone, $str);
		unset($_SESSION[member_id]);
	}

	if($site_language == "eng"){
		$alert_txt = "Enrollment";
	}else{
		$alert_txt = "등록";
	}
	$ADD_ = "&return_mode=join";


} else if ($mode == "modify_proc"){

	if($password){
		preg_match_all('/[0-9]/', $password, $match_01);
		$numCk = count($match_01[0]);
		preg_match_all('/[a-z]/', $password, $match_02);
		$engCk = count($match_02[0]);
		preg_match_all("/[~!@\#$%^*\()\-=+_']/", $password, $match_03);
		$specialCk = count($match_03[0]);
		$stringLen = mb_strlen($password,"utf-8");
		if($stringLen < 8 || $stringLen > 12){
			error("비밀번호는 8~12자 사이로 입력해 주세요.");
		}
		if($numCk <= 0 || $engCk <= 0 || $specialCk <= 0){
			error("비밀번호는 영문,특수문자,숫자 조합 형태로 입력해 주세요.");
		}
		$password = hash("sha256", $password);
		$add_password = ", password='$password'";
	}

	$alredy_member = fetch_array("SELECT * FROM koweb_member WHERE id='$_SESSION[member_id]'");
	if($alredy_member['email'] != $email){
		$email_check = mysqli_num_rows(query("SELECT * FROM koweb_member WHERE email='{$email}'"));
		if($email_check > 0){
			if($site_language == "eng"){
				error("There is a duplicate email.");
				exit;
			}else{
				error("중복된 이메일이 있습니다.");
				exit;
			}
		}
	}


	$add_query .= ",type = ''";

	if($del_address_file == "Y"){
		$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/member/";
		$add_query .= ", address_file =  ''";
		@unlink($dir . $alredy_member[address_file]);
		@unlink($dir . "thumb_".$alredy_member[address_file]);
	}

	if($_FILES[address_file][size] > 0){
		$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/member/";
		$add_query  .= ", address_file = '".upload_file($dir, $_FILES[address_file][tmp_name], $_FILES[address_file][name])."'";
	}

	$query = "UPDATE koweb_member SET 	CI = '$_SESSION[CI]'
										, DI= '$_SESSION[CI]'
									  	$add_password
										$add_query
										, contents = '$contents'
										, reg_date = '$reg_date'
										, ip = '$ip'
										WHERE id='$_SESSION[member_id]'
			";

	@mysqli_query($connect, $query);
	if($site_language == "eng"){
	$alert_txt = "Modify";
	}else{
		$alert_txt = "수정";
	}

//로그인
} else if ($mode == "login_proc") {
	/*
	2020-06-04
	미로그인 상태일때 상품구매시 로그인후 다시 뷰페이지로 넘어가는 로직-> 바로 주문서로 넘어가도록 수정

	1.product.php $order_data 생성 및 url에 추가
	2.member_login.html에 order_data를 form태그안에 삽입
	3.order_data 를 parse_str한다음, 해당 데이터를 return_url에서 /로 자른 데이터에 연결 login_proc , guest_login_proc에 추가.

	*/
	parse_str(htmlspecialchars_decode($_POST[order_data]),$post_order_data);
	if($post_order_data[mode] == "order"){
		$return_url = current(explode("?",$return_url));
		$return_url = $return_url."?".http_build_query($post_order_data);
	}


	if($site_language == "eng"){
		if(!$password|| !$id){
			error("Please fill in ID / Password");
			exit;
		}
		$password = hash("sha256", $password);
		$query = "SELECT * FROM koweb_member WHERE id='$id' AND password='$password' AND type='$type' LIMIT 1";
		$result = mysqli_query($connect, $query);
		$check = mysqli_num_rows($result);

		if($check < 1){
			error("Please check your ID / Password.");
			exit;
		} else {
			$row = mysqli_fetch_array($result);
			if($row[state] != "Y"){
				error("This username has been suspended. Please contact your administrator.");
				exit;
			}
			$_SESSION['auth_level'] = $row[auth_level];
			$_SESSION['member_id'] = $row[id];
			$_SESSION['member_name'] = $row[name];
			$_SESSION['type'] = $row[type];
			$_SESSION['CI'] = $row[CI];
			$_SESSION['DI'] = $row[DI];
			$_SESSION[order_type] = "member";
			set_cart_from_session($_SESSION['member_id']);
		}
		$alert_txt = "login";
	}else{
		if(!$password|| !$id){
			error("아이디/패스워드를 작성해주세요");
			exit;
		}
		$password = hash("sha256", $password);
		$query = "SELECT * FROM koweb_member WHERE id='$id' AND password='$password' AND type='$type' LIMIT 1";
		$result = mysqli_query($connect, $query);
		$check = mysqli_num_rows($result);

		if($check < 1){
			error("아이디/패스워드를 확인해주세요.");
			exit;
		} else {
			$row = mysqli_fetch_array($result);
			if($row[state] != "Y"){
				error("사용이 정지 된 아이디입니다. 관리자에게 문의하세요.");
				exit;
			}
			$_SESSION['auth_level'] = $row[auth_level];
			$_SESSION['member_id'] = $row[id];
			$_SESSION['member_name'] = $row[name];
			$_SESSION['type'] = $row[type];
			$_SESSION['CI'] = $row[CI];
			$_SESSION['DI'] = $row[DI];
			$_SESSION[order_type] = "member";
			set_cart_from_session($_SESSION['member_id']);
		}
		$alert_txt = "로그인";
		$common_queryString = $return_url;
	}

//회원탈퇴
} else if($mode == "secession") {
	if($site_language == "eng"){
		if(isblank($_SESSION[member_id])) error("Please login");
	}else{
		if(isblank($_SESSION[member_id])) error("로그인 해주세요.");
	}
	
	if(!$password){
		if($site_language == "eng"){
			error("error");
			exit;
		}else{
			error("비정상적인 접근입니다.");
			exit;
		}
		
	}
	$password = hash("sha256", $password);
	$alredy_member = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM koweb_member WHERE id='$_SESSION[member_id]' AND password = '$password'"));

	if($alredy_member <= 0){
		if($site_language == "eng"){
			error("Wrong password");
			exit;
		}else{
			error("비밀번호가 잘못되었습니다.");
			exit;
		}
	}
		

	@mysqli_query($connect,"UPDATE koweb_member SET state='N' WHERE id='$_SESSION[member_id]' LIMIT 1");
	if($site_language == "eng"){
		$alert_txt = "secession";
	}else{
		$alert_txt = "탈퇴";
	}
	@session_destroy();
} else if($mode == "guest_login_proc") {
	/*
	2020-06-04
	미로그인 상태일때 상품구매시 로그인후 다시 뷰페이지로 넘어가는 로직-> 바로 주문서로 넘어가도록 수정

	1.product.php $order_data 생성 및 url에 추가
	2.member_login.html에 order_data를 form태그안에 삽입
	3.order_data 를 parse_str한다음, 해당 데이터를 return_url에서 /로 자른 데이터에 연결 login_proc , guest_login_proc에 추가.

	*/
	parse_str(htmlspecialchars_decode($_POST[order_data]),$post_order_data);
	if($post_order_data[mode] == "order"){
		$return_url = current(explode("?",$return_url));
		$return_url = $return_url."?".http_build_query($post_order_data);
	}

	$_SESSION[order_type] = "guest";
	$_SESSION[member_id] = rand_guest_id();
	set_cart_from_session($_SESSION[member_id]);
	unset($_SESSION[dcode]);
	unset($_SESSION[pcode]);
	if(empty($return_url)) $return_url = "/";
	$common_queryString = $return_url;
	if($site_language == "eng"){
		$alert_txt = "Connect with ID ".$_SESSION[member_id];
	}else{
		$alert_txt = "아이디 $_SESSION[member_id]로 " ." 접속";
	}

}  else if($mode == "guest_search_proc") {
	$pass = hash("sha256", $guest_password);
	$order_check_query = "SELECT * FROM koweb_order WHERE order_type='guest' AND member ='{$guest_id}' AND password='{$pass}'";
	$num_row = mysqli_num_rows(mysqli_query($connect,$order_check_query));
	if($site_language == "eng"){
		if($num_row <= 0) error("Please check your order ID and order inquiry password");
	}else{
		if($num_row <= 0) error("주문자 아이디,주문조회 비밀번호를 다시 확인해주세요");
	}
	$_SESSION[dcode] = $guest_id;
	$_SESSION[pcode] = hash("sha256", $guest_password);
	$_SESSION[order_type] = "search";
	set_cart_from_session($guest_id);
	unset($_SESSION[member_id]);

	// $common_queryString = $return_url;
	$common_queryString = "/member/member.html?mode=order";
	if($site_language == "eng"){
		$alert_txt = "Connect with ID ".$_SESSION[dcode];
	}else{
		$alert_txt = "아이디 $_SESSION[dcode]로 " ." 접속";
	}
} else {
	if($site_language == "eng"){
		error("Please use the correct connection path.");
	}else{
		error("올바른 접속경로를 이용해주시기 바랍니다.");
	}
	exit;
}
/*----------------------------------------------------------------------------*/
// 마무리
/*----------------------------------------------------------------------------*/
if($return_url2)$common_queryString = $return_url2;

if(!$common_queryString) $common_queryString = "/".$add_folder;
if($common_queryString != "/".$add_folder){
	if($ADD){
		$ADD .= "?return_url=".urlencode($return_url);
	}else{
		$ADD .= "&return_url=".urlencode($return_url);
	}
}

if($site_language == "eng"){
	alert($alert_txt);
}else{
	alert($alert_txt ."되었습니다.");
}
url($common_queryString.$ADD);

?>
