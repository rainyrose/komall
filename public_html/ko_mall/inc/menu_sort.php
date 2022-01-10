<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_".$database."_config";

	if($database == "koweb_main_visual"){
		$setting_table = $database;
	}

	//기본정보
	$tmp = explode("|", $sort_data);
	$count = 1;
	foreach($tmp as $v){
		$update_ = mysqli_query($connect, "UPDATE $setting_table SET sort='$count' WHERE no='$v'");
		$count++;
	}
?>
