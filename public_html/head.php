<?
$http_host = $_SERVER['HTTP_HOST'];
$get_host = $http_host;

$all_url = "https://";

// if(strpos($http_host,"www") !== 0){
// if($http_host != "demomall.kohub.kr"){
// 	$get_host = "demomall.kohub.kr";
// }
$all_url = $all_url.$http_host.$_SERVER['PHP_SELF']."?".http_build_query($_GET);
if( !isset($_SERVER["HTTPS"]) || $_SERVER['HTTPS'] == "" ){
	include $_SERVER[DOCUMENT_ROOT] . "/../db/db.php";
	$connect = mysqli_connect($host, $user, $passwd, $dataname) or die("not connected");
	$site = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_site_config ORDER BY no DESC LIMIT 1"));
	include $_SERVER[DOCUMENT_ROOT] . "/inc/top.html";
	// header("Location:".$all_url);
    echo "<script>window.location.href='".$all_url."';</script>";
	exit;
}
include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/session_function.php";
@ session_start_samesite();
// ================ SSL 있을떄 위 주석풀기  ==================/

// SSL 없을때
// session_start();
//


//@ header('Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate');
@ header("Pragma:no-cache"); //HTTP1.0
@ header("Cache-Control:no-cache, must-revalidate"); //HTTP1.1
@ header('Content-type: text/html; charset=utf-8');
include $_SERVER[DOCUMENT_ROOT] . "/../db/db.php";
$connect = mysqli_connect($host, $user, $passwd, $dataname) or die("not connected");

include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/function.php";
include $_SERVER['DOCUMENT_ROOT']."/lib/product.php";
include $_SERVER['DOCUMENT_ROOT']."/inc/count.php";

// 모든데이터 체크 ( 배열은 따로 추가 )
foreach (array_keys($_REQUEST) as $value) {
${$value} = sanitizeString($_REQUEST[$value]);
	// ${$value} = sanitizemysqli($_POST[$value]);
}


//페이팔 실결제 일때 값 비워주세요.
//테스트일땐 SANDBOX.로 변경
define('IS_PAYPAL_CODE_SANDBOX','SANDBOX.');
define('PAYPAL_CLIENT_ID','AXroK-b_pcA5R5WE0itHfqGbWGMXWrv1utO4Q61COL5Vt9OmQ9hoSEmQr5HkX8HOe-DvHz0WBrIAb4_7');
define('PAYPAL_CLIENT',PAYPAL_CLIENT_ID.':ECWNuxfX667NIgXJsQdnx-d8hUk68-cCxrteJ5gEWuu7ESd1FsBoNJiZIqFGRAEjRMLvUCEiv0X88mKY');

/*

if(!$_SESSION['member_id']){
	if(!$_SESSION['guest_id']) $_SESSION['guest_id'] = rand_guest_id();
	if($_SESSION['order_type'] != "guest"){
		$_SESSION['order_type'] = "guest";
	}
}else{
	unset($_SESSION['guest_id']);
	if($_SESSION['order_type'] != "member"){
		$_SESSION['order_type']="member";
	}
}

*/
//사이트 기본설정
$site_default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_site_config ORDER BY no DESC LIMIT 1"));
//등록된 mid값이 있는 지 확인 후 변수대입, 없으면 기본설정
// $is_site = mysqli_fetch_array(mysqli_query($connect, "SELECT description, og_description, og_sitename, og_title, content_id FROM koweb_menu_config WHERE menu_id='$mid' LIMIT 1"));

//외국어 사이트 기본설정
$is_lang_tmp = explode("/", $_SERVER['PHP_SELF']);
$lang_ = $is_lang_tmp[1];
$menu_langs = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM koweb_product_category WHERE state = 'Y' AND category = '$lang_'"));

if($menu_langs > 0){
	$langwhere = "WHERE lang = '$lang_'";
} else {
	$langwhere = "WHERE lang = 'default'";
}
$site_default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_site_config $langwhere ORDER BY no DESC LIMIT 1"));

$site = $site_default;
// $site[description] = ($is_site[description]) ? $is_site[description] : $site[description];
// $site[og_description] = ($is_site[og_description]) ? $is_site[og_description] : $site[og_description];
// $site[og_sitename] = ($is_site[og_sitename]) ? $is_site[og_sitename] : $site[og_sitename];
// $site[og_title] = ($is_site[og_title]) ? $is_site[og_title] : $site[og_title];
$site_pay = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_pay_config ORDER BY no DESC LIMIT 1"));
$site_sms = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_sms_config ORDER BY no DESC LIMIT 1"));

$site[img_url] = $img_url[title_img];
//택배 기본설정
$site_pay["경동택배"] = "https://kdexp.com/basicNewDelivery.kd?barcode=";
$site_pay["대신택배"] = "http://home.daesinlogistics.co.kr/daesin/jsp/d_freight_chase/d_general_process2.jsp?billno1=";
$site_pay["동부택배"] = "http://www.dongbups.com/delivery/delivery_search_view.jsp?item_no=";
$site_pay["로젠택배"] = "https://www.ilogen.com/web/personal/trace/";
$site_pay["우체국"] = "http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=";
$site_pay["이노지스택배"] = "http://www.innogis.co.kr/tracking_view.asp?invoice=";
$site_pay["한진택배"] = "http://www.hanjin.co.kr/Delivery_html/inquiry/result_waybill.jsp?wbl_num=";
$site_pay["롯데택배"] = "https://www.lotteglogis.com/open/tracking?invno=";
$site_pay["CJ대한통운"] = "https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no=";
$site_pay["CVSnet편의점택배"] = "http://was.cvsnet.co.kr/_ver2/board/ctod_status.jsp?invoice_no=";
$site_pay["KG옐로우캡택배"] = "http://www.yellowcap.co.kr/custom/inquiry_result.asp?invoice_no=";
$site_pay["KGB택배"] = "http://www.kgbls.co.kr/sub5/trace.asp?f_slipno=";
$site_pay["KG로지스"] = "http://www.kglogis.co.kr/contents/waybill.jsp?item_no=";
$site_pay["건영택배"] = "http://www.kunyoung.com/goods/goods_01.php?mulno=";
$site_pay["호남택배"] = "http://www.honamlogis.co.kr/04estimate/songjang_list.php?c_search1=";


//컨텐츠 타입 확인 ( 게시판, 온라인폼, 프로그램 )
if(strpos($_SERVER['REQUEST_URI'], "board_id") !== false){
	$content_info[content_type] = "board";
	$content_info[ref_board] = $_GET[board_id];

} else if(strpos($_SERVER['REQUEST_URI'], "program_id") !== false){
	$content_info[content_type] = "program";
	$content_info[ref_program] = $_GET[program_id];

} else if(strpos($_SERVER['REQUEST_URI'], "online_id") !== false){
	$content_info[content_type] = "online";
	$content_info[ref_online] = $_GET[online_id];
}

//$content_info = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id='$is_site[content_id]' LIMIT 1"));

switch($content_info[content_type]){

	case "board" :
		$board_title = mysqli_fetch_array(mysqli_query($connect, "SELECT id, title FROM koweb_board_config WHERE id='$content_info[ref_board]'"));

		if($mode == "view"){
			$set_board = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $board_title[id] WHERE no = '$no' LIMIT 1"));

			if(!$set_board[metatag_content]) $set_board[metatag_content] = strip_tags($set_board[content]);

			$site[title] = $site[keyword_title] . " - " . $set_board[title] . $set_board[metatag_content]  ." - " . $set_board[reg_date] ." (자세히보기)";

			$site[description] = $site[keyword_title] . " - " . $set_board[title] ." " . $set_board[metatag_content] . " " .$set_board[etc] . " - " . $set_board[reg_date];
			$site[og_title] = $site[title];
		//	$site[og_description] = $site[title] = $site[keyword_title] . " - " . $set_board[title] ." " . $set_board[metatag_content]  . $set_board[etc] . " - " . $set_board[reg_date];
			$site[og_description] = $site[description];

		} else if($mode == "write"){
			$site[title] = $site[keyword_title] . " - " . $board_title[title] . " (작성)";
		} else if($mode == "modify"){
			$site[title] = $site[keyword_title] . " - " . $board_title[title] . " (수정)";
		} else if ($mode == "auth"){
			$site[title] = $site[keyword_title] . " - " . $board_title[title] . " (인증)";
		} else {
			$site[title] = $site[keyword_title] . " - " . $board_title[title] . " (목록)";
		}
	break;

	case "program" :
		$program_title = mysqli_fetch_array(mysqli_query($connect, "SELECT id, title FROM koweb_program_config WHERE id='$content_info[ref_program]'"));
		 if($mode == "view"){
			$set_program = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $program_title[id] WHERE no = '$no' LIMIT 1"));
			$site[title] = $site[keyword_title] . " - " . $set_program[title] ." - " . $set_program[reg_date] ." (자세히보기)";
			$site[description] = $site[title] = $site[keyword_title] . " - " . $set_program[title] ." " . $set_program[metatag_content]  . " - " . $set_program[reg_date];
			$site[og_description] = $site[title] = $site[keyword_title] . " - " . $set_program[title] ." - " . $set_program[reg_date];
		} else if($mode == "write"){
			$site[title] = $site[keyword_title] . " - " . $program_title[title] . " (작성)";
		} else if($mode == "modify"){
			$site[title] = $site[keyword_title] . " - " . $program_title[title] . " (수정)";
		} else if ($mode == "auth"){
			$site[title] = $site[keyword_title] . " - " . $program_title[title] . " (인증)";
		} else {
			$site[title] = $site[keyword_title] . " - " . $program_title[title] . " (목록)";
		}
	break;

	case "online" :
		$online_title = mysqli_fetch_array(mysqli_query($connect, "SELECT id, title FROM koweb_online_config WHERE id='$content_info[ref_online]'"));
		if($mode == "view"){
			$set_online = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $online_title[id] WHERE no = '$no' LIMIT 1"));
			$site[title] = $site[keyword_title] . " - " . $set_online[title] ." - " . $set_online[reg_date] ." (자세히보기)";
			$site[description] = $site[title] = $site[keyword_title] . " - " . $set_online[title] ." " . $set_online[metatag_content]  . " - " . $set_online[reg_date];
			$site[og_description] = $site[title] = $site[keyword_title] . " - " . $set_online[title] ." " . $set_online[metatag_content]  . " - " . $set_online[reg_date];
		} else if($mode == "write"){
			$site[title] = $site[keyword_title] . " - " . $online_title[title] . " (작성)";
		} else if($mode == "modify"){
			$site[title] = $site[keyword_title] . " - " . $online_title[title] . " (수정)";
		} else if ($mode == "auth"){
			$site[title] = $site[keyword_title] . " - " . $online_title[title] . " (인증)";
		} else {
			$site[title] = $site[keyword_title] . " - " . $online_title[title] . " (목록)";
		}
	break;
}
	$img_url = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product WHERE id = '$id' LIMIT 1"));
	$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
	$img_url_src = $img_url[title_img];
	if($img_url_src){
		$img_urls = $protocol  . $_SERVER['HTTP_HOST'] . "/upload/product/" . $img_url[title_img];
	}
	$site[og_title] =  $site[title] . " " . $img_url[product_title] . " " . $img_url[simple_info];
	// $site[og_title] = "DEBUG : ".$site[og_title];
	// $site[og_title] = print_r($_REQUEST,true);

	$mall_config = fetch_array("SELECT * FROM koweb_mall_config order by no desc");
	$pay_config = fetch_array("SELECT * FROM koweb_pay_config order by no desc");

	$mobile_agent = '/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS|Opera)/';
	if(preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])) {
		$mobile_agent_ = true;
		if($_SESSION['pc_to_mobile']) $mobile_agent_ = false;
	} else {
		$mobile_agent_ = false;
	}

?>
