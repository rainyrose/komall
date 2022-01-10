<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";

$update_ajax_result = array();
$update_ajax_result[flag] = false;
if($mode == "ship_proc"){
    include_once $_SERVER[DOCUMENT_ROOT] .  "/ko_admin/reservation/proc.php";
    $update_ajax_result[flag] = true;
}else{
    $update_ajax_result[ment] = "잘못된 접근입니다.";
}
echo json_encode($update_ajax_result);
?>
