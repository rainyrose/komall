<?
	//$online_id = $content_default[ref_online];

	//online 기본정보 불러오기
	//프로그램 통합 관리 DB 보면됨

	$online_id = trim($online_id);
	if(strpos($online_id, "(") !== false || strpos($online_id, ")")  !== false || strpos($online_id, "%")  !== false){
		error("비정상적인 접근입니다.");
		exit;
	}



	$online_query = "SELECT * FROM koweb_online_config WHERE id = '$online_id'";
	$online_result = mysqli_query($connect, $online_query);
	$online = mysqli_fetch_array($online_result);
	$online_table = $online[id];
	$online_file = $online[id];
	$online_title = $online[title];

	//online 기본 변수
	$http_host = $_SERVER['HTTP_HOST'];
	$request_uri = $_SERVER['REQUEST_URI'];
	$url = "http://" . $http_host . $request_uri;
	//include_once $_SERVER['DOCUMENT_ROOT'] . "/js/online.js";
	echo "<script type=\"text/javascript\" src=\"/js/online.js\"></script>";
//	include_once $_SERVER['DOCUMENT_ROOT'] . "/online/online_auth.php";

	if(!$online_id || $online[id] == ""){
		error("정상적인 경로로 접근하여 주세요.");
		exit;
	}


	/*
	if($online[use_member] < $_SESSION['auth_level']){
		if(!$_SESSION[member_id]){
			alert("로그인이 필요한 서비스입니다.");
			url("/member/member.html?mode=login");
			exit;
		} else {
			if($online[use_member] == "3") error("정회원 이상만 신청 가능합니다.");
			else if($online[use_member] == "5") error("준회원 이상만 신청 가능합니다.");
			exit;
		}
	}
	*/

$common_queryString = $_SERVER['PHP_SELF']."?online_id=$online_id&start=$start&category=$category&search_key=$search_key&keyword=$keyword";
$common_actionString = $_SERVER['PHP_SELF']."?online_id=$online_id";

switch ($mode) {
	case "write" :
	case "modify" :

		$name_check = true;

		//현재 사이트가 본인인증을 사용하는가?
		if($online[use_namecheck] == "Y"){

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
		//에디터 js 로 따로
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(쓰기) | $online[title]'; </script>";
		include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/online/$online_id/form.html";
		break;

	case "write_proc" :
	case "modify_proc" :
		//에디터 js 로 따로
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(쓰기) | $online[title]'; </script>";
		include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/online/$online_id/proc.php";
		break;

	case "view" :
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(상세보기) | $online[title]'; </script>";
		include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/online/$online_id/view.html";
		break;

	case "delete" : // 삭제
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(삭제) | $online[title]'; </script>";
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/online/$online_id/proc.php";
		break;

	case "check" :
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(인증) | $online[title]'; </script>";
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
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/online/$online_id/auth_check.html";
		}
		break;

	case "comment_proc" :
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(코멘트작성) | $online[title]'; </script>";
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/online/$online_id/proc.php";
		break;

	default:
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(목록) | $online[title]'; </script>";
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/online/$online_id/list.html";
		break;
	}
?>
