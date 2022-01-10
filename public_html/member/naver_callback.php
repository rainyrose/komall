<?
	if($state != $_SESSION[naver_state]){
		error("비정상적인 접근입니다...");
		exit;
	}
	$naver_curl = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=".NAVER_CLIENT_ID."&client_secret=".NAVER_CLIENT_SECRET."&redirect_uri=".urlencode(NAVER_CALLBACK_URL)."&code=".$code."&state=".$state;

	$is_post = false;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $naver_curl);
	curl_setopt($ch, CURLOPT_POST, $is_post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec ($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close ($ch);
    $responseArr = json_decode($response, true);
	if($status_code == 200) {
		$responseArr = json_decode($response, true);
		$_SESSION['naver_access_token'] = $responseArr['access_token'];
		$_SESSION['naver_refresh_token'] = $responseArr['refresh_token'];

		$me_headers = array(
			'Content-Type: application/json',
			sprintf('Authorization: Bearer %s', $responseArr['access_token'])
		);


		$me_is_post = false;
		$me_ch = curl_init();
		curl_setopt($me_ch, CURLOPT_URL, "https://openapi.naver.com/v1/nid/me");
		curl_setopt($me_ch, CURLOPT_POST, $me_is_post);
		curl_setopt($me_ch, CURLOPT_HTTPHEADER, $me_headers);
		curl_setopt($me_ch, CURLOPT_RETURNTRANSFER, true);
		$me_response = curl_exec ($me_ch);
		$me_status_code = curl_getinfo($me_ch, CURLINFO_HTTP_CODE);
		curl_close ($me_ch);

		$me_responseArr = json_decode($me_response, true);

		if($me_status_code == 200){
			$naver_id = 'NAVER_'.$me_responseArr['response']['id'];
			$naver_name = $me_responseArr['response']['name'];
			$naver_email = $me_responseArr['response']['email'];
			$naver_access_key = $responseArr['access_token'];

			$type = "NAVER";

			$check_ = "SELECT * FROM koweb_member WHERE type='NAVER' AND id='$naver_id'";
			$result_ = mysqli_query($connect, $check_);
			$row_ = mysqli_num_rows($result_);

			if($row_ <= 0){
                //회원이 없으니 회원가입 페이지로
                alert("가입되지 않은 회원입니다. 네이버로그인으로 회원가입을 진행합니다.");

                $mode = "agree";

			} else {
                $id = $naver_id;
                $password = $naver_id;
                $mode = "login_proc";
                //로그인 처리
			}

		} else {
			error("회원정보를 가져오지 못했습니다.");
			exit;
		}
	} else {
		error("토큰값을 가져오지 못했습니다.");
		exit;
	}
?>
