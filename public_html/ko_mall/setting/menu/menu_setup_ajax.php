<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_menu_config";
	//기본정보
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE menu_id='$menu_no'"));
	if($default[content_id]){
		$content_ = mysqli_fetch_array(mysqli_query($connect, "SELECT content_title FROM koweb_content_config WHERE content_id = '$default[content_id]'"));
	}

	if($default[link_menu_id]){
		$menu_ = mysqli_fetch_array(mysqli_query($connect, "SELECT menu_title FROM $setting_table WHERE menu_id = '$default[link_menu_id]' LIMIT 1"));
	}

	if($default[category] == "default"){ 
		$view_href = "/contents/$default[dir]/page.html?mid=$default[menu_id]";
	} else { 
		$view_href = "/$default[category]/contents/$default[dir]/page.html?mid=$default[menu_id]";
	}

	$result_array = array("menu_title" => $default[menu_title]
					,"use_device_pc" => $default[use_device_pc] 
					,"use_device_mob" => $default[use_device_mob] 
					,"use_type" => $default[use_type] 
					,"state" => $default[state] 
					,"memo" => $default[memo] 
					,"content_id" => $default[content_id] 
					,"link_menu_id" => $default[link_menu_id] 
					,"link_title" => $menu_[menu_title] 
					,"content_id" => $default[content_id] 
					,"content_title" => $content_[content_title] 
					,"description" => $default[description] 
					,"og_description" => $default[og_description] 
					,"og_sitename" => $default[og_sitename]
					,"og_title" => $default[og_title]
					,"dir" => $default[dir]
					,"view_content" => $view_href
					,"category" => $default[category]
			 );

	$result = json_encode($result_array);
	echo($result);
?>
