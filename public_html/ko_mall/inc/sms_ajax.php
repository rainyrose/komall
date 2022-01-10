<?
/*----------------------------------------------------------------------------*/
include_once  $_SERVER['DOCUMENT_ROOT'] . "/head.php";  
//include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";  
/*----------------------------------------------------------------------------*/

$check_code = mt_rand(000000,999999);

$check_tmp = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM koweb_auth_sms WHERE check_phone = '$data_send'"));
if($check_tmp > 0){
	$del_check = mysqli_query($connect,"DELETE FROM koweb_auth_sms WHERE check_phone = '$data_send'");
}

$insert_ = "INSERT INTO koweb_auth_sms VALUES('' , '$data_send' , '$check_code', 'Y')";
$insert_result = mysqli_query($connect, $insert_);

$sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS 전송요청 URL
// $sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL
$sms['user_id'] = base64_encode($site[sms_id]); //SMS 아이디.
$sms['secure'] = base64_encode($site[sms_key]) ;//인증키
$smsType = "S";

//장문은 L , 단문은 S
if( $_POST['smsType'] == "L"){
	  $sms['subject'] =  base64_encode($_POST['subject']);
}
//받는번호
$sms['rphone'] = base64_encode($data_send);
//보내는번호
$sms['sphone1'] = base64_encode("010");
$sms['sphone2'] = base64_encode("2508");
$sms['sphone3'] = base64_encode("5133");

//메세지 90바이트까지. (단문)
$sms['msg'] = base64_encode(stripslashes("$site[title]"." 인증번호 : [" .$check_code."]"));

//예약일자 * Ymd * 예)20180113
$sms['rdate'] = base64_encode($_POST['rdate']);

//예약시간 * His * 예)173000
$sms['rtime'] = base64_encode($_POST['rtime']);
$sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.

//전송후 이동할 페이지
$sms['returnurl'] = base64_encode($_POST['returnurl']);
$returnurl = $_POST['returnurl'];

//테스트 용
$sms['testflag'] = base64_encode($_POST['testflag']);

//이름삽입번호 ( 메시지에 받는사람 이름넣을때 ) 
//<input type="type" name="destination" value="010-000-0000|홍길동,010-000-0000|김영희">
//<input type="type" name="msg" value="{name}님, 주문하신 물품이 배송되었습니다.">
$sms['destination'] = strtr(base64_encode($_POST['destination']), '+/=', '-,');


//반복 설정 ( 원하는경우 Y )
$sms['repeatFlag'] = base64_encode($_POST['repeatFlag']);

//반본 횟수
$sms['repeatNum'] = base64_encode($_POST['repeatNum']);

//반복시간 15분이상 부터 가능
$sms['repeatTime'] = base64_encode($_POST['repeatTime']);

$sms['smsType'] = base64_encode($_POST['smsType']); // LMS일경우 L
$nointeractive = $_POST['nointeractive']; //사용할 경우 : 1, 성공시 대화상자(alert)를 생략

$host_info = explode("/", $sms_url);
$host = $host_info[2];
$path = $host_info[3]."/".$host_info[4];

srand((double)microtime()*1000000);
$boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
//print_r($sms);

// 헤더 생성
$header = "POST /".$path ." HTTP/1.0\r\n";
$header .= "Host: ".$host."\r\n";
$header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

// 본문 생성
foreach($sms AS $index => $value){
	$data .="--$boundary\r\n";
	$data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
	$data .= "\r\n".$value."\r\n";
	$data .="--$boundary\r\n";
}
$header .= "Content-length: " . strlen($data) . "\r\n\r\n";


$oCurl = curl_init();
$url =  "https://sslsms.cafe24.com/smsSenderPhone.php";
$aPostData['userId'] = $site[sms_id]; // SMS 아이디
$aPostData['passwd'] = $site[sms_key]; // 인증키
curl_setopt($oCurl, CURLOPT_URL, $url);
curl_setopt($oCurl, CURLOPT_POST, 1);
curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($oCurl, CURLOPT_POSTFIELDS, $aPostData);
curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
$ret = curl_exec($oCurl);
//echo $ret;
curl_close($oCurl);


$fp = fsockopen($host, 80);

if ($fp) {
	fputs($fp, $header.$data);
	$rsp = '';
	while(!feof($fp)) {
		$rsp .= fgets($fp,8192);
	}
	fclose($fp);
	$msg = explode("\r\n\r\n",trim($rsp));
	$rMsg = explode(",", $msg[1]);
	$Result= $rMsg[0]; //발송결과
	$Count= $rMsg[1]; //잔여건수

	//발송결과 알림
	if($Result=="success") {
		$alert = "인증번호가 전송 되었습니다.";
		//$alert .= " 잔여건수는 ".$Count."건 입니다.";
	}
	else if($Result=="reserved") {
		$alert = "성공적으로 예약되었습니다.";
		//$alert .= " 잔여건수는 ".$Count."건 입니다.";
	}
	else if($Result=="3205") {
		$alert = "잘못된 번호형식입니다.";
	}

	else if($Result=="0044") {
		$alert = "스팸문자는발송되지 않습니다.";
	}

	else {
		$alert = "[Error]".$Result;
	}
}
else {
	$alert = "Connection Failed";
}



if($nointeractive=="1" && ($Result!="success" && $Result!="Test Success!" && $Result!="reserved") ) {
//	echo "<script>alert('".$alert ."')</script>";
}
else if($nointeractive!="1") {
	//echo "<script>alert('".$alert."')</script>";
	echo $alert;
}

//echo "<script>location.href='".$returnurl."';</script>";
	


?>
