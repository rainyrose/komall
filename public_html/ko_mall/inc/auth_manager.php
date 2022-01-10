<?
	$accept = true;
	if(!$_SESSION['member_id']) $accept = false;
	if(!$_SESSION['auth_level']) $accept = false;
	
	$query = "SELECT is_admin FROM koweb_member WHERE id='$_SESSION[member_id]' LIMIT 1";
	$result = mysqli_query($connect, $query);
	$admin_row_ = mysqli_fetch_array($result);

	if($admin_row_[is_admin] != "Y") $accept = false;

	if(!$accept){
		echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densitydpi=medium-dpi\">";
		echo "<meta http-equiv='refresh' content='0; URL=/ko_mall/login.html' />";
		exit;
	}
	

?>
