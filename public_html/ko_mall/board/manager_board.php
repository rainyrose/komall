<?
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/head.php";  
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_mall/inc/auth_manager.php";
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_mall/board/auth_manager.php";
		
		if($core_id == "board_system" && $tm == "1"){
?>
			<nav class="lnb"">
				<!-- 선택시 a태그 class="on" -->
				<ul>
					<li><a href="/ko_mall/index.html?type=board&core=manager_board&core_id=board_system" class="on">시스템공지</a></li>
					<li><a href="/ko_mall/index.html?type=setting&core=manager_setting&manager_type=site">사이트관리</a></li>
				</ul>
			</nav>
<?
		}
		echo "<script src=\"/ko_editor/ckeditor.js\"></script>";
		$is_admin = true;
		if($core_id){
			$board_id = $core_id;
		}

		//board 기본정보 불러오기
		$board_query = "SELECT * FROM koweb_board_config WHERE id = '$board_id'";
		$board_result = mysqli_query($connect, $board_query);
		$board = mysqli_fetch_array($board_result);
		$skin = $board[skin];

		//board 기본 변수
		$http_host = $_SERVER['HTTP_HOST'];
		$request_uri = $_SERVER['REQUEST_URI'];
		$url = "http://" . $http_host . $request_uri;

		echo "<script type=\"text/javascript\" src=\"/js/board.js\"></script>";
		include_once $_SERVER['DOCUMENT_ROOT'] . "/board/board_auth.php";

		if(!$board_id || $board[skin] == ""){
			error("정상적인 경로로 접근하여 주세요.");
			exit;
		}
		$common_queryString = $_SERVER['PHP_SELF'] . "?type=board&core=manager_board&core_id=$board_id&start=$start&category=$category&search_key=$search_key&keyword=$keyword";
		$common_actionString = $_SERVER['PHP_SELF'] . "?type=board&core=manager_board&core_id=$board_id";

		switch ($mode) {
			case "write" :
			case "modify" :
			case "reply" :
				//에디터 js 로 따로 
				echo "<script type=\"text/javascript\" src=\"/js/smarteditor.js\"></script>";
				echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(쓰기) | $board[title]'; </script>";
				include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/board/skin/$skin/form.html";
				break;
		
			case "modify_proc" :
			case "write_proc" :
			case "reply_proc" :
				echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(쓰기) | $board[title]'; </script>";
				include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/board/skin/$skin/proc.php";
				break;

			case "view" :
				echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(상세보기) | $board[title]'; </script>";
				include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/board/skin/$skin/view.html";
				break;

			case "delete" : // 삭제
				echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(삭제) | $board[title]'; </script>";
				@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/board/skin/$skin/proc.php";
				break;

			case "check" :
				echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(인증) | $board[title]'; </script>";
				@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/board/skin/$skin/auth_check.html";
				break;

			case "comment_proc" :
				echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(코멘트작성) | $board[title]'; </script>";
				@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/board/skin/$skin/proc.php";
				break;

			default:
				echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(목록) | $board[title]'; </script>";
				@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/board/skin/$skin/list.html";
				break;
		}
		?>
