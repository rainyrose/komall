<?
include_once $_SERVER['DOCUMENT_ROOT']."/head.php";
if(!$_SESSION['s_cart']) $_SESSION['s_cart'] = array();
array_push($_SESSION['s_cart'],$_POST);
?>
