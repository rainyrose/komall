<?
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/head.php";
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_mall/inc/auth_manager.php";
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_mall/program/auth_manager.php";

		$is_admin = true;
		if($core_id){
			$program_id = $core_id;
		}

		//program 기본정보 불러오기
		$program_query = "SELECT * FROM koweb_program_config WHERE id = '$program_id'";
		$program_result = mysqli_query($connect, $program_query);
		$program = mysqli_fetch_array($program_result);
		$program_table = $program[refer_table];
		$program_file = $program[id];
		$program_title = $program[title];

		$common_queryString = "$PHP_SELF?type=$_GET[type]&core=$_GET[core]&manager_type=$_GET[manager_type]&core_id=$program_id";
		$common_actionString = $_SERVER['PHP_SELF'] . "?type=program&core=manager_program&core_id=$program_id";

		//program 기본 변수
		$http_host = $_SERVER['HTTP_HOST'];
		$request_uri = $_SERVER['REQUEST_URI'];
		$url = "http://" . $http_host . $request_uri;

		//include_once $_SERVER['DOCUMENT_ROOT'] . "/js/program.js";
		echo "<script type=\"text/javascript\" src=\"/js/program.js\"></script>";
		//include_once $_SERVER['DOCUMENT_ROOT'] . "/program/program_auth.php";

		if(!$program_id || $program[refer_table] == ""){
			error("정상적인 경로로 접근하여 주세요.");
			exit;
		}

	switch ($mode) {
		case "write" :
		case "modify" :
			//에디터 js 로 따로 
			echo "<script src=\"/ko_editor/ckeditor.js\"></script>";
			echo "<script type=\"text/javascript\" src=\"/js/smarteditor.js\"></script>";
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(쓰기) | $program[title]'; </script>";
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/form.html";
			break;

		case "write_proc" :
		case "modify_proc" :
			//에디터 js 로 따로 
			echo "<script type=\"text/javascript\" src=\"/js/smarteditor.js\"></script>";
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(쓰기) | $program[title]'; </script>";
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/proc.php";
			break;

		case "view" :
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(상세보기) | $program[title]'; </script>";
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/view.html";
			break;

		case "delete" : // 삭제
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(삭제) | $program[title]'; </script>";
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/proc.php";
			break;

		case "check" :
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(인증) | $program[title]'; </script>";
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/auth_check.html";
			break;

		case "comment_proc" :
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(코멘트작성) | $program[title]'; </script>";
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/proc.php";
			break;

		default:
			echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(목록) | $program[title]'; </script>";
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/program/$program_id/list.html";
			break;
		}
	?>
