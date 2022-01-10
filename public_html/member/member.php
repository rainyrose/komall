<?
	//member 기본정보 불러오기
	//프로그램 통합 관리 DB 보면됨
	$member_table = "koweb_member";
	//member 기본 변수
	$http_host = $_SERVER['HTTP_HOST'];
	//$common_queryString = $_SERVER['REQUEST_URI'];

	if(!$site_language) $site_language= "default";
	if($site_language == "default"){
		$add_folder = "";
		$add_board_id = "";
	}else{
		$add_folder = $site_language."/";
		$add_board_id = "_".$site_language;
	}


	$common_queryString = $_SERVER['PHP_SELF']."?start=$start&category=$category&search_key=$search_key&keyword=$keyword";
	$common_actionString = $_SERVER['PHP_SELF']."?";

	$url = "http://" . $http_host . $common_queryString;


	//include_once $_SERVER['DOCUMENT_ROOT'] . "/js/member.js";
	echo "<script type=\"text/javascript\" src=\"/js/member.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"/js/namecheck.js\"></script>";
	//include_once $_SERVER['DOCUMENT_ROOT'] . "/js/namecheck.js";

	//회원설정 불러오기
	$data_result2 = mysqli_query($connect, "SELECT * FROM koweb_member_config");
	$member_config_ = mysqli_fetch_array($data_result2);

	//SNS 로그인 플레그
	$naver_login_flag = false;
	$kakao_login_flag = false;
	$facebook_login_flag = false;
	$scmeme = "https";
	if( !isset($_SERVER["HTTPS"]) || $_SERVER['HTTPS'] == "" ){
		$scmeme = "http";
	}
	if($naver_login_flag){
		$naver_callback_url = "{$scmeme}://{$_SERVER['HTTP_HOST']}/member/member.html?mode=naver";
		define('NAVER_CLIENT_ID', "nHnRzEJuYE4gMC5Rq7iy");
		define('NAVER_CLIENT_SECRET', "Apat7R9tT8");
		define('NAVER_CALLBACK_URL', $naver_callback_url);
	}
	if($kakao_login_flag){
		$kakao_callback_url = "{$scmeme}://{$_SERVER['HTTP_HOST']}/member/member.html?mode=kakao";
		define('KAKAO_CLIENT_ID', "0a79720310688893a0330fd3ba688324");
		define('KAKAO_CALLBACK_URL', $kakao_callback_url);
	}

	if($mode == "naver" || $mode == "kakao"){
		include_once $_SERVER['DOCUMENT_ROOT']."/member/{$mode}_callback.php";

		if($_SESSION['sns_orderdata']) $_POST[order_data] = $_SESSION['sns_orderdata'];
		if($_SESSION['sns_return_url']) $return_url = $_SESSION['sns_return_url'];
		if($_SESSION['sns_return_url2']) $return_url2 = $_SESSION['sns_return_url2'];
		unset($_SESSION['sns_orderdata']);
		unset($_SESSION['sns_return_url']);
		unset($_SESSION['sns_return_url2']);
	}else{
		$naver_state = md5(microtime() . mt_rand());
		$_SESSION['naver_state'] = $naver_state;
		$naver_apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=".NAVER_CLIENT_ID."&redirect_uri=".urlencode($naver_callback_url)."&state=".$naver_state;

		$kakao_state = md5(microtime() . mt_rand());
		$_SESSION['kakao_state'] = $kakao_state;
		$kakao_apiURL = "https://kauth.kakao.com/oauth/authorize?client_id=".KAKAO_CLIENT_ID."&redirect_uri=".KAKAO_CALLBACK_URL."&response_type=code&state=".$kakao_state;
	}



	if($facebook_login_flag && $facebook == "Y" && $mode == "login_proc"){

		$access_token = $_SESSION['facebook_token'];
		$facebook_return = facebook_token_check($access_token,"email,name");
		$type = "FB";
		$id = "FB_".$facebook_return['id'];
		$password = $id;
		$password_hash = hash("sha256", $password);

		$query = "SELECT * FROM koweb_member WHERE id='$id' AND password='$password_hash' AND type='{$type}' LIMIT 1";
		$result = mysqli_query($connect, $query);
		$check = mysqli_num_rows($result);

		if(!$check) $mode = "agree";
	}

switch ($mode) {

	//약관동의
	case "agree" :
		//에디터 js 로 따로
		if(!$step) $step = 1;
		if($step != 1) $step = 1;
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(Sign Up) | $site[og_title]'; </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(회원가입) | $site[og_title]'; </script>";
		}
		include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_agree.html";

		if($site_language == "eng"){
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"Sign Up\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"Sign Up\"); </script>";
		}else{
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"회원가입\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"회원가입\"); </script>";
		}
		break;

	case "agreement" :
	case "private" :
	case "guide" :
	if($mode == "agreement")	$page_sub_title = "이용약관";
	if($mode == "private")	$page_sub_title = "개인정보처리방침";
	if($mode == "guide")	$page_sub_title = "이용안내";

		echo "<script type='text/javascript'> document.title = '" . end($history_title) . "({$page_sub_title}) | $site[og_title]'; </script>";
		include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}{$mode}.html";

		echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"{$page_sub_title}\"); </script>";
		echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"{$page_sub_title}\"); </script>";
		break;




	//회원가입 폼
	case "join" :
		//본인인증 여부 체크 변수
		$name_check = true;
		echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(회원가입) | $site[og_title]'; </script>";

		if($site_language == "eng"){
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"Sign Up\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"Sign Up\"); </script>";
		}else{
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"회원가입\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"회원가입\"); </script>";
		}

		//step 이 2가 아니면 약관동의로 다시 넘어간다 ( 주소를 이용 바로 들어오는 것 방지 )
		if($step != 2){
			alert("회원가입 약관에 동의 하셔야 진행 하실 수 있습니다.");
			include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_agree.html";
		}


		//현재 사이트가 본인인증을 사용하는가?
		if($member_config_[use_namecheck] == "Y"){

			//정상적인 CI 와 DI 값이 세션에 존재하면 본인인증 변수를 true 처리 한다.
			if($_SESSION[CI] && $_SESSION[DI]){
				$name_check = true;

			//CI 및 DI값이 없다면 본인인증 안한것이니 본인인증 페이지로 넘긴다.
			} else {
				//본인인증 변수를 false 로 처리 한다음 본인인증 진행
				$name_check = false;

				//TODO 본인인증
				//url 로 리턴 변수까지 다 넣고 날렸다가~~~return url로 돌아오면 됨.
				$return_url = $common_queryString;
				$param1 = "mode|".$mode;
				$param2 = "step|".$step;
				$param3 = "";
				url("/inc/namecheck.html?return_url=$common_queryString&param1=$param1&param2=$param2&param3=$param3&param4=$param4&param5=$param5");
				break;
			}
		}

		//본인인증 변수가 true 일 경우 form 으로. 아니면 다시 약관동의로
		if($name_check){
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_form.html";
		} else {
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_agree.html";
		}
		break;

	//회원가입 처리
	case "join_proc" :
		echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(인증) | $site[og_title]'; </script>";
		if($step != 3){
			error("비정상적인 접근입니다.");
			exit;
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/member_proc.php";
		break;

	//로그인
	case "login" :
		//에디터 js 로 따로
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(login) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"login\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"login\"); </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(로그인) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"로그인\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"로그인\"); </script>";
		}
		if($_SESSION['auth_level'] && $_SESSION['member_id']){
			if(!$return_mode){
				//alert("이미 로그인되어있습니다.");
			}
			url("/");

		} else {
		//로그인 하지 않았으면 로그인 페이지
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_login.html";
		}
		break;

	//로그인 처리
	case "login_proc" :
	case "guest_login_proc" :
	case "guest_search_proc" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(Login processing) | $site[og_title]'; </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(로그인처리) | $site[og_title]'; </script>";
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/member_proc.php";
		break;

	//로그아웃
	case "logout" : // 삭제
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(delete) | $site[og_title]'; </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(삭제) | $site[og_title]'; </script>";
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/member_logout.php";
		break;

	//마이페이지 - 회원정보 수정
	case "modify" :
		if($site_language == "eng"){
			if(isblank($_SESSION['member_id'])) error("This is an unusual approach.");
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(Edit member information) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"Edit member information\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"Edit member information\"); </script>";
		}else{
			if(isblank($_SESSION['member_id'])) error("비정상적인 접근입니다.");
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(회원정보 수정) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"회원정보 수정\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"회원정보 수정\"); </script>";
		}
		echo "<script type='text/javascript'> $(\".gnb .mypage\").addClass(\"on\"); </script>";
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_form.html";
		break;

	//마이페이지 - 회원정보 수정 처리
	case "modify_proc" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(Edit member information) | $site[og_title]'; </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(회원정보 수정) | $site[og_title]'; </script>";
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/member_proc.php";
		break;

	//회원탈퇴
	case "secession" :
		if($site_language == "eng"){
			if(isblank($_SESSION['member_id'])) error("This is an unusual approach.");
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(Withdrawal) | $site[og_title]'; </script>";
		}else{
			if(isblank($_SESSION['member_id'])) error("비정상적인 접근입니다.");
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(회원탈퇴) | $site[og_title]'; </script>";
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/member_proc.php";
		break;

	//본인인증
	case "check" :
		if($site_language == "eng"){
			if(isblank($_SESSION['member_id'])) error("This is an unusual approach.");
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(certification) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"Member authentication\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"Member authentication\"); </script>";
		}else{
			if(isblank($_SESSION['member_id'])) error("비정상적인 접근입니다.");
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(인증) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"회원인증\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"회원인증\"); </script>";
		}

		if($member_config_[use_namecheck] == "Y"){
			//TODO 본인인증
			//url 로 리턴 변수까지 다 넣고 날렸다가~~~return url로 돌아오면 됨.
			$return_url = $common_queryString;
			$param1 = "mode|".$mode;
			$param2 = "return_mode|".$mode;
			$param3 = "";
			url("/inc/namecheck.html?return_url=$common_queryString&param1=$param1&param2=$param2&param3=$param3&param4=$param4&param5=$param5");
			break;
		} else {
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_auth.html";
		}
		break;

	//아이디 / 비밀번호 찾기
	case "find_id" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(find ID) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"Find ID \"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"Find ID \"); </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(아이디 찾기) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"아이디 찾기\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"아이디 찾기\"); </script>";
		}
		if($member_config_[use_namecheck] == "Y"){
			//TODO 본인인증
			//url 로 리턴 변수까지 다 넣고 날렸다가~~~return url로 돌아오면 됨.
			$return_url = $common_queryString;
			$param1 = "mode|"."find_proc";
			$param2 = "step|".$step;
			$param3 = "return_mode|"."find_id";
			url("/inc/namecheck.html?return_url=$common_queryString&param1=$param1&param2=$param2&param3=$param3&param4=$param4&param5=$param5");
			break;
		} else {
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_find_id.html";
		}
		break;

	case "find_pw" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(find Password) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"Find Password\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"Find Password\"); </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(비밀번호 찾기) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"비밀번호 찾기\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"비밀번호 찾기\"); </script>";
		}
		if($member_config_[use_namecheck] == "Y"){
			//TODO 본인인증
			//url 로 리턴 변수까지 다 넣고 날렸다가~~~return url로 돌아오면 됨.
			$return_url = $common_queryString;
			$param1 = "mode|"."find_proc";
			$param2 = "step|".$step;
			$param3 = "return_mode|"."find_pw";
			url("/inc/namecheck.html?return_url=$common_queryString&param1=$param1&param2=$param2&param3=$param3&param4=$param4&param5=$param5");
			break;
		} else {
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_find_pw.html";
		}
		break;

	case "find_proc" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(find Password) | $site[og_title]'; </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(비밀번호 찾기) | $site[og_title]'; </script>";
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_find_proc.php";
		break;

	case "intro" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(my page) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"my page\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"my page\"); </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(마이페이지) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"마이페이지\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"마이페이지\"); </script>";
		}
		echo "<script type='text/javascript'> $(\".gnb .mypage\").addClass(\"on\"); </script>";
		if($_SESSION[order_type] == "search" || $_SESSION[order_type] == "guest"){
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}order_list.html";
		} else {
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}intro.html";
		}
		break;

	case "qna" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(my page) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"Product Inquiry\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"Product Inquiry\"); </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(마이페이지) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"상품문의\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"상품문의\"); </script>";
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}qna.html";
		break;

	case "point" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(my page) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"Point accumulation status\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"Point accumulation status\"); </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(마이페이지) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"포인트 누적현황\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"포인트 누적현황\"); </script>";
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}point.html";
		break;

	case "order" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(my page) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"Recent orders\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"Recent orders\"); </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(마이페이지) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"최근주문내역\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"최근주문내역\"); </script>";
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}order_list.html";
		break;

	case "review" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(my page) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"Reviews\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"Reviews\"); </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(마이페이지) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"사용후기\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"사용후기\"); </script>";
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}review.html";
		break;
	//기본??
	default:
		if($site_language == "eng"){
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(login) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"login\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"login\"); </script>";
		}else{
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(로그인) | $site[og_title]'; </script>";
			echo "<script type='text/javascript'> $(\"[data-title-visual]\").text(\"로그인\"); </script>";
			echo "<script type='text/javascript'> $(\"[data-title-content]\").text(\"로그인\"); </script>";
		}
		//로그인 한 상태라면 마이페이지
		if($_SESSION['auth_level'] && $_SESSION['member_id']){
		//	alert("이미 로그인되어있습니다.");
			url("/".$add_folder);
		} else {
		//로그인 하지 않았으면 로그인 페이지
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/member/{$add_folder}member_login.html";
		}
		break;
	}
?>
