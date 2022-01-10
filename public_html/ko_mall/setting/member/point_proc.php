<? include_once  $_SERVER['DOCUMENT_ROOT'] . "/head.php"; ?>
<?
//  ID 정리
$reg_date = date("Y-m-d H:i:s");

if($email2) $email3 = $email2;
$email = $email1."@".$email3;
$tel = $tel1."-".$tel2."-".$tel3;

$ip = $_SERVER['REMOTE_ADDR'];



if($mode == "add_point"){
	switch($add_point_option){
			case "+" :
				$query = "INSERT koweb_point SET member = '$data' , point_type = '$point_type', point = '$add_point', reg_date='$reg_date'";
			break;
			case "-" : 
				$query = "INSERT koweb_point SET member = '$data' , point_type = '$point_type', point = -'$add_point', reg_date='$reg_date'";
			break;
	}

	$result = mysqli_query($connect, $query);
} else {
	$query = "DELETE FROM koweb_point WHERE no = '$no' AND member = '$data'";
	$result = mysqli_query($connect, $query);

}

/*----------------------------------------------------------------------------*/
// 마무리
/*----------------------------------------------------------------------------*/
?>

<script type="text/javascript">
alert("처리 되었습니다.");
location.href = "/ko_mall/setting/member/member_point.html?data=<?=$data?>";
</script>
