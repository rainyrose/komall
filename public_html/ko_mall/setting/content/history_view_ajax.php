<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_content_history";
	//기본정보
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE no='$history_no'"));
	if($default[ref_board]) $r_board = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_board_config WHERE id='$default[ref_board]'"));
	if($default[ref_program]) $r_program = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_program_config WHERE id='$default[ref_program]'"));
	if($default[ref_online]) $r_online = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_online_config WHERE id='$default[ref_online]'"));
	if($default[ref_product]) $r_product = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE id='$default[ref_product]'"));

	$result_array = array("history_title" => $default[content_title]
					,"content_type" => $default[content_type] 
					,"web_content" => $default[web_content] 
					,"mob_content" => $default[mob_content] 
					,"ref_link" => $default[ref_link] 
					,"ref_target" => $default[ref_target] 
					,"ref_program" => $default[ref_program] 
					,"ref_board" => $default[ref_board] 
					,"ref_board_title" => $r_board[title] 
					,"ref_program_title" => $r_program[title] 
					,"ref_online" => $default[ref_online] 
					,"ref_online_title" => $r_online[title] 
					,"ref_product" => $default[ref_product] 
					,"ref_product_title" => $r_product[title] 
					,"memo" => $default[memo] 
					,"state" => $default[state] 
					,"reg_date" => $default[reg_date] 
					,"ip" => $default[ip] 
					,"writer" => $default[writer] 
					,"history_no" => $default[no] 
					,"target_id" => $default[content_id] 
			 );

	$result = json_encode($result_array);
	echo($result);
?>
