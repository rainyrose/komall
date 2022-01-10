<?
include_once $_SERVER['DOCUMENT_ROOT'] . "/site_setup.php"; 

if(!$admin_password || !$admin_id){
	alert_to_admin("아이디/패스워드를 작성해주세요");
	exit;
}
$password_enc = hash("sha256", $admin_password);

$query = "SELECT * FROM koweb_member WHERE id='$admin_id' AND password='$password_enc'  LIMIT 1";
$result = mysql_query($query);
$check = mysql_num_rows($result);

if($check < 1){
	alert_to_admin("아이디/패스워드를 확인해주세요.");
	exit;
} else {
	$admin_row = mysql_fetch_array($result);
	$_SESSION['auth_level'] = $admin_row[auth_level];
	$_SESSION['member_id'] = $admin_row[id];
	$_SESSION['member_name'] = $admin_row[name];
	$_SESSION['member_dept'] = $admin_row[dept];
	if($_SESSION[auth_level] == "1") $_SESSION['is_admin'] = true;

	//$dept_type = "SELECT * FROM koweb_dept WHERE no='$admin_row[dept]' LIMIT 1";
	//$dept_type_result = mysql_query($dept_type);
	//$info_dpet = mysql_fetch_array($dept_type_result);
	//$_SESSION['member_type'] = $info_dpet[dept_type];
	alert("로그인되었습니다.");
	echo "<script type='text/javascript'> parent.location.href='./index.html'; </script>";
} 
?>