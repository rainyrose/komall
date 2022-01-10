<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_member_item";
	$reg_date = date("Y-m-d H:i:s");

	//원본 데이터 형식 항목1^사용여부^필수선택|항목2^사용여부^필수선택
	$first_data = explode("|", $data_);
	
	//1차 가공후 데이터형식 [항목1^사용여부^필수선택, 항목2^사용여부^필수선택]
	foreach($first_data as $v){
		$detail_data = explode("^", $v);
		//2차 가공후 데이터 형식 = [항목1, 사용여부, 필수선택][항목2, 사용여부, 필수선택]
		$query = mysqli_query($connect, "UPDATE $setting_table SET item_requierd = '$detail_data[2]', item_use = '$detail_data[1]'  WHERE item = '$detail_data[0]'");
	}
?>