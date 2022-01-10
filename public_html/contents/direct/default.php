<?
	$site_language = "default";
	$menu_default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_menu_config WHERE menu_id = '$mid' LIMIT 1"));
?>
