<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_content_config";
	//기본정보
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE no='$content_no'"));

	if($menu_type == "link"){
		$menu_tmp = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_menu_config WHERE menu_id='$menu_info'"));
		$menu_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_menu_config WHERE menu_id='$menu_tmp[link_menu_id]'"));
		$result_array = array("menu_type" => "link", "content_title" => $menu_[menu_title]);
	} else {
		if($default[ref_board]) $ref_board = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_board_config WHERE id='$default[ref_board]'"));
		if($default[ref_program]) $ref_program = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_program_config WHERE id='$default[ref_program]'"));
		if($default[ref_online]) $ref_online = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_online_config WHERE id='$default[ref_online]'"));
		if($default[ref_product]) $ref_product = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE id='$default[ref_product]'"));

		$result_array = array("menu_type" => "content"
						,"content_title" => $default[content_title]
						,"content_id" => $default[content_id] 
						,"content_type" => $default[content_type] 
						,"web_content" => $default[web_content] 
						,"mob_content" => $default[mob_content] 
						,"ref_link" => $default[ref_link] 
						,"ref_target" => $default[ref_target] 
						,"ref_program" => $default[ref_program] 
						,"ref_program_title" => $ref_program[title] 
						,"ref_board" => $default[ref_board] 
						,"ref_board_title" => $ref_board[title] 
						,"ref_board" => $default[ref_board] 
						,"ref_online_title" => $ref_online[title] 
						,"ref_online" => $default[ref_online] 
						,"ref_product_title" => $ref_product[title] 
						,"ref_product" => $default[ref_product] 
						,"memo" => $default[memo] 
						,"state" => $default[state] 
				 );
		}


	$result = json_encode($result_array);
	echo($result);
?>
