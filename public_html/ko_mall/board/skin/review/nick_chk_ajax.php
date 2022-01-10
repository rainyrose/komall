<?
	include $_SERVER[DOCUMENT_ROOT] . "/head.php";

	$query = "UPDATE koweb_board_config SET nick_chk='$val' WHERE id='$board_id' ";
	$result = mysqli_query($connect, $query);
?>