<script type="text/javascript">
$(document).ready(function(){
	$(document).bind('keydown',function(e){
		if ( e.keyCode == 123 /* F12 */) {
			//e.preventDefault();
			//alert("http ERROR : developer tools load error\r\ntarget: undefined;\r\nException in window.onload:Error:An error \r\nhas ocurredPHPplugin.30293");
			//e.returnValue = false;
		}
	});

});
</script>
<script type="text/javascript">
$(function(){
	var mid = "<?=$mid?>";
	$("[data-title-visual]").text("쇼핑하기");

	if("<?=$category?>" != ""){
		$("[data-title-content]").text($("[data-set-on="+"<?=$category?>"+"]").text());
	} else {
		$("[data-title-content]").text("제품구매");
	}
});
</script>

<?
if($_SESSION['order_type'] == "member"){
	$mem = get_member($_SESSION['member_id']);
	$member_id = $mem['id'];
}else{
	$member_id = $_SESSION['member_id'];
}

if(!$site_language) $site_language= "default";
if($category){
	$category_config_ = fetch_array("SELECT * FROM koweb_product_category_config WHERE id='{$category}'");
}else{
	$category_config_['category'] = $site_language;
}

if($category_config_['category'] == "default"){
	$add_folder = "";
	$add_board_id = "";
}else{
	$add_folder = $category_config_['category']."/";
	$add_board_id = "_".$category_config_['category'];
}

// $site_language =z

if(strpos($mode,"cart") === false && $mode != "view" && $mode != "" && $mode != "guest_view" && $mode != "guest_order_cancel" && $mode != "trade_request" && $mode != "return_request" && $mode != "trade_request_proc" && $mode != "return_request_proc"){
	if(empty($member_id)) {
		$return_url_ = $_SERVER['PHP_SELF'] ."?". $_SERVER['QUERY_STRING'];
		$return_url_ = rawurlencode($return_url_);
		/*
		2020-06-04
		미로그인 상태일때 상품구매시 로그인후 다시 뷰페이지로 넘어가는 로직-> 바로 주문서로 넘어가도록 수정

		1.product.php $order_data 생성 및 url에 추가
		2.member_login.html에 order_data를 form태그안에 삽입
		3.order_data 를 parse_str한다음, 해당 데이터를 return_url에서 /로 자른 데이터에 연결 login_proc , guest_login_proc에 추가.

		*/

		$order_data =  urlencode(http_build_query($_POST));
		url("/{$add_folder}member/member.html?return_url=$return_url_&orderdata=$order_data");
		exit;
	}

}
echo '<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>';
echo '<script type="text/javascript" src="/js/'.$add_folder.'product.js"></script>';
echo '<script type="text/javascript" src="/js/'.$add_folder.'share.js"></script>';
switch ($mode) {
	case "view" :
		echo "<script type='text/javascript'> $(\".gnb .product\").addClass(\"on\"); </script>";
		$title_product = get_product($id);
		$title_cat = mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM koweb_product_category_config WHERE id='$title_product[category]'"));
		echo "<script type='text/javascript'>$(function(){  $(\"[data-title-content]\").text(\"".$title_cat[title]."\"); });</script>";

        @include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/user/{$add_folder}view.html";
        break;
	case "order" :
		echo "<script type='text/javascript'> $(\".gnb .product\").addClass(\"on\"); </script>";
		if($site_language == "eng"){
			echo "<script type='text/javascript'>$(function(){  $(\"[data-title-content]\").text(\"Enter order information\"); });</script>";
		}else{
			echo "<script type='text/javascript'>$(function(){  $(\"[data-title-content]\").text(\"주문 정보입력\"); });</script>";
		}
        @include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/user/{$add_folder}order.html";
        break;
	case "order_proc" :
	case "trade_request_proc" :
	case "return_request_proc" :
        @include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/user/proc.php";
        break;
	case "order_view" :
	case "guest_view" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'>$(function(){  $(\"[data-title-content]\").text(\"Order history\"); });</script>";
		}else{
			echo "<script type='text/javascript'>$(function(){  $(\"[data-title-content]\").text(\"주문 내역\"); });</script>";
		}
        @include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/user/{$add_folder}order_view.html";
        break;

	case "trade_request" :
	case "return_request" :
		if($mode == "trade_request")	$page_sub_title = "교환요청";
		if($mode == "return_request")	$page_sub_title = "반품요청";
		if($site_language == "eng"){
			echo "<script type='text/javascript'>$(function(){  $(\"[data-title-content]\").text(\"{$mode}\"); });</script>";
		}else{
			echo "<script type='text/javascript'>$(function(){  $(\"[data-title-content]\").text(\"{$page_sub_title}\"); });</script>";
		}
        @include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/user/{$add_folder}request.html";
        break;
	case "order_cancel" :
	case "guest_order_cancel" :
        @include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/user/proc.php";
        break;
	case "address_list" :
	@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/user/address_list.html";
	break;
	case "cart" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> $(function(){  $(\"[data-title-visual]\").text(\"shopping basket\"); });</script>";
			echo "<script type='text/javascript'> $(function(){  $(\"[data-title-content]\").text(\"shopping basket\"); });</script>";
		}else{
			echo "<script type='text/javascript'> $(function(){  $(\"[data-title-visual]\").text(\"장바구니\"); });</script>";
			echo "<script type='text/javascript'> $(function(){  $(\"[data-title-content]\").text(\"장바구니\"); });</script>";
		}


        @include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/cart/{$add_folder}list.html";
        break;
	case "cart_del_proc" :
	case "cart_modify_proc" :
	case "cart_count_proc" :
	case "cart_option_proc" :
        @include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/cart/proc.php";
        break;
	case "wish" :
		if($site_language == "eng"){
			echo "<script type='text/javascript'> $(function(){  $(\"[data-title-visual]\").text(\"Product of interest\"); });</script>";
			echo "<script type='text/javascript'> $(function(){  $(\"[data-title-content]\").text(\"Product of interest\"); });</script>";
		}else{
			echo "<script type='text/javascript'>$(function(){   $(\"[data-title-visual]\").text(\"관심상품\"); });</script>";
			echo "<script type='text/javascript'>$(function(){   $(\"[data-title-content]\").text(\"관심상품\"); });</script>";
		}
        @include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/wish/{$add_folder}list.html";
        break;
	case "wish_save" :
	case "wish_del_proc" :
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/wish/proc.php";
		break;
	default:
		echo "<script type='text/javascript'> $(\".gnb .product\").addClass(\"on\"); </script>";
		//echo "<script type='text/javascript'> document.title = '" . end($history_title) . "(목록) | $program[title]'; </script>";
		if($category){
			echo "<script type='text/javascript'>$(function(){   $(\"[data-title-content]\").text($(\"[data-set-on='".$category."']\").text()); });</script>";
		} else {
			if($site_language == "eng"){
				echo "<script type='text/javascript'>$(function(){   $(\"[data-title-content]\").text(\"All products\"); });</script>";
			}else{
				echo "<script type='text/javascript'>$(function(){   $(\"[data-title-content]\").text(\"제품구매\"); });</script>";
			}
		}
		@include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_mall/product/user/{$add_folder}list.html";

		break;
	}
?>
