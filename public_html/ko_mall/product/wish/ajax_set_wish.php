<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";
if($_SESSION['order_type'] == "member"){
	$mem = get_member($_SESSION['member_id']);
    $member_id = $mem['id'];
}else{
    $member_id = $_SESSION['member_id'];
}
$result['flag'] = false;
if(!$member_id){
    $return_url_ = $_SERVER['PHP_SELF'] ."?". $_SERVER['QUERY_STRING'];
	$return_url_ = rawurlencode($return_url_);
    $result['url'] = "/member/member.html?return_url=";
    echo json_encode($result);
    exit;
}
$check_query = "SELECT * FROM koweb_wish WHERE member_id='{$member_id}' AND product_id='{$id}'";
$check_result = mysqli_query($connect,$check_query);
$check_num_row = mysqli_num_rows($check_result);
if($check_num_row > 0){
	$result['flag'] = true;
	echo json_encode($result);
	exit;
 }

$insert_query = "INSERT INTO koweb_wish SET member_id='{$member_id}', product_id='{$id}',reg_date='{$reg_date}'";
mysqli_query($connect,$insert_query);
$result['flag'] = true;
echo json_encode($result);
