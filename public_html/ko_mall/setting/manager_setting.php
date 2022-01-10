<?
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/head.php";
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_mall/inc/auth_manager.php";
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_mall/setting/auth_manager.php";
?>
		<script type="text/javascript" src="/ko_mall/js/setting.js"></script>
		<script type="text/javascript"src="/ko_editor/ckeditor.js"></script>
		<script type="text/javascript" src="/js/smarteditor.js"></script>
<?
		// $common_queryString = "$PHP_SELF?type=$_GET[type]&core=$_GET[core]&manager_type=$_GET[manager_type]&search_key=$_GET[search_key]&keyword=$_GET[keyword]&start=$_GET[start]";
		$common_queryString = "$PHP_SELF?type=$_GET[type]&core=$_GET[core]&manager_type=$_GET[manager_type]&search_key=$_GET[search_key]&keyword=$_GET[keyword]";

		//기본정보 불러오기
		$setting_table = "koweb_".$_GET[manager_type]."_config";
		$setting_query = "SELECT * FROM ".$setting_table;
		$setting_result = mysqli_query($connect, $setting_query);
		$setting = mysqli_fetch_array($setting_result);

		// 기본 변수
		$http_host = $_SERVER['HTTP_HOST'];
		$request_uri = $_SERVER['REQUEST_URI'];
		$url = "http://" . $http_host . $request_uri;
		$mode = $_GET[mode] ?? $_POST[mode];

		if(!$_GET[core] || !$_GET[manager_type] || !$_GET[type] ){
			error("정상적인 경로로 접근하여 주세요.");
			exit;
		}

	switch ($mode) {
		case "write" :
		case "modify" :
		case "order_view" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_form.html";
			break;

		case "administrator" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_administrator.html";
			break;

		case "mall" :
		case "pay" :
		case "sms" :
		case "info" :
		case "private" :
		case "agreement" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_list_".$mode.".html";
			break;


		case "level" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_level.html";
			break;

		case "dept" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_dept.html";
			break;

		case "category" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_category.html";
			break;

		case "register" :
		case "register_modify" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_register.html";
			break;

		case "config" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_config.html";
			break;

		case "auth_board" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_board_list.html";
			break;


		case "auth_program" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_program_list.html";
			break;


		case "not_regist" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_management.html";
			break;

		case "write_proc" :
		case "modify_proc" :
		case "register_proc" :
		case "register_modify_proc" :
		case "dept_write_proc" :
		case "dept_modify_proc" :
		case "cate_write_proc" :
		case "cate_modify_proc" :
		case "cate_delete_proc" :
		case "sellerY" :
		case "sellerN" :
		case "showerY" :
		case "showerN" :
		case "excel" :
		case "option_change_number" :
		case "option_refund_number" :
		case "sort" :
		case "del_deli" :
		case "add_point" :
			//에디터 js 로 따로
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_proc.php";
			break;

		case "view" :
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_view.html";
			break;

		case "type1" :
		case "type2" :
		case "type3" :
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_detail.html";
			break;


		case "apply_proc" :
			include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_proc.php";
			break;

		case "delete" : // 삭제
		case "dept_delete" : // 삭제
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_proc.php";
			break;

		default:
			@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/setting/".$_GET[manager_type]."/".$_GET[manager_type]."_list.html";
			break;
		}
	?>
