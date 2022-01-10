<?
if($state != $_SESSION[kakao_state]){
    error("비정상적인 접근입니다.");
    exit;
}
if($error=="access_denied"){
    url("/member/page.html?mid=member");
}else if(!$code){
    error("잘못된 접근입니다.");
}

$kakao_url = "https://kauth.kakao.com/oauth/token";
$data = array(
    "grant_type" => "authorization_code",
    "client_id" => KAKAO_CLIENT_ID,
    "redirect_uri" => KAKAO_CALLBACK_URL,
    "code" => $code
);
$data = http_build_query($data);
$curl_header = array('Content-Type: application/x-www-form-urlencoded;charset=utf-8');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $kakao_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_header);
curl_setopt($ch, POST,true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,30);
$response = curl_exec($ch);
curl_close ($ch);

$responseArr = json_decode($response, true);

if(!$responseArr['access_token']){
    alert("카카오톡 로그인이 원활하게 진행되지 않았습니다. 관리자에게 문의해주세요");
    url("/member/member.html?mode=login");
}


$access_token = $responseArr['access_token'];
$kakao_url = "https://kapi.kakao.com/v2/user/me";

$curl_header = array('Authorization: Bearer '.$access_token);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $kakao_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_header);
curl_setopt($ch, POST,true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,30);
$response = curl_exec($ch);
curl_close ($ch);

$me_responseArr = json_decode($response, true);
if($me_responseArr['msg']){
    alert($me_responseArr['msg']);
    url("/member/member.html?mode=login");
}

$kakao_id = 'KAKAO_'.$me_responseArr['id'];
$kakao_access_key = $access_token;

$me_responseArr['response']['id'] = $me_responseArr['id'];
$me_responseArr['response']['name'] = $me_responseArr['kakao_account']['profile']['nickname'];
$me_responseArr['response']['email'] = $me_responseArr['kakao_account']['email'];

$type = "KAKAO";

$check_ = "SELECT * FROM koweb_member WHERE type='KAKAO' AND id='$kakao_id'";
$result_ = mysqli_query($connect, $check_);
$row_ = mysqli_num_rows($result_);

if($row_ <= 0){
    //회원이 없으니 회원가입 페이지로
    alert("가입되지 않은 회원입니다. 카카오로그인으로 회원가입을 진행합니다.");

    $mode = "agree";

} else {
    $id = $kakao_id;
    $password = $kakao_id;
    $mode = "login_proc";
    //로그인 처리
}

?>
