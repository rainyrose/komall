<?
	$program_id = trim($program_id);
	if(strpos($program_id, "(") !== false || strpos($program_id, ")")  !== false || strpos($program_id, "%")  !== false){
		error("비정상적인 접근입니다.");
		exit;
	}
	// $program_id = $content_default[ref_program];

	//program 기본정보 불러오기
	//프로그램 통합 관리 DB 보면됨
	$program_query = "SELECT * FROM koweb_program_config WHERE id = '$program_id'";

	$program_result = mysqli_query($connect, $program_query);
	$program = mysqli_fetch_array($program_result);
	$program_table = $program[refer_table];
	$program_file = $program[id];
	$program_title = $program[title];

	//program 기본 변수
	$http_host = $_SERVER['HTTP_HOST'];
	$request_uri = $_SERVER['REQUEST_URI'];
	$url = "http://" . $http_host . $request_uri;
	echo "<script type=\"text/javascript\" src=\"/js/program.js\"></script>";

//	include_once $_SERVER['DOCUMENT_ROOT'] . "/program/program_auth.php";

	$common_queryString = "$PHP_SELF?program_id=$program_id";
	$common_actionString = $_SERVER['PHP_SELF']."?program_id=$program_id";

	if(!$program_id || $program[refer_table] == ""){
		error("정상적인 경로로 접근하여 주세요.");
		exit;
	}

switch ($mode) {
	case "write" :
	case "modify" :
		//에디터 js 로 따로
		echo "<script type=\"text/javascript\" src=\"/js/smarteditor.js\"></script>";
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(쓰기) | $program[title]'; </script>";
		include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/form.html";
		break;

	case "write_proc" :
	case "modify_proc" :
		//에디터 js 로 따로
		echo "<script type=\"text/javascript\" src=\"/js/smarteditor.js\"></script>";
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(쓰기) | $program[title]'; </script>";
		include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/proc.php";
		break;

	case "view" :
		echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(상세보기) | $program[title]'; </script>";
		if($program[user_view] == "Y"){
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/user_view.html";
		} else {
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/view.html";
		}
		break;

	case "delete" : // 삭제
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(삭제) | $program[title]'; </script>";
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/proc.php";
		break;

	case "check" :
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(인증) | $program[title]'; </script>";
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/auth_check.html";
		break;

	case "comment_proc" :
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(코멘트작성) | $program[title]'; </script>";
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/proc.php";
		break;

	default:
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(목록) | $program[title]'; </script>";
		if($program[user_list] == "Y"){
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/user_list.html";
		} else {
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/list.html";
		}
		break;
	}
?>
