<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";

$query = "SELECT * FROM koweb_address WHERE member='{$_SESSION['member_id']}' AND main='1'";
$result = mysqli_query($connect,$query);
$row = mysqli_fetch_array($result);

echo json_encode($row);
