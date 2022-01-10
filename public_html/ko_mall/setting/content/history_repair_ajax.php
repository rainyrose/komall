<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_content_config";
	$reg_date = date("Y-m-d H:i:s");
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE no='$target_id'"));


	$history_update_ = mysqli_query($connect, "INSERT INTO koweb_content_history SET content_title='$default[content_title]'
													, content_id='$default[content_id]'
													, content_type='$default[content_type]'
													, web_content='$default[web_content]'
													, mob_content='$default[mob_content]'
													, ref_link='$default[ref_link]'
													, ref_target='$default[ref_target]'
													, ref_program='$default[ref_program]'
													, ref_board='$default[ref_board]'
													, ref_online='$default[ref_online]'
													, ref_product='$default[ref_product]'
													, memo='$default[memo]'
													, state='$default[state]'
													, reg_date='$default[reg_date]'
													, ip='$default[ip]'
													, writer='$_SESSION[member_id]'
							");


	$repair = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_history WHERE no='$history_no'"));

	if($default[ref_board]) $r_board = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_board_config WHERE id='$repair[ref_board]'"));
	if($default[ref_program]) $r_program = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_program_config WHERE id='$repair[ref_program]'"));
	if($default[ref_online]) $r_online = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_online_config WHERE id='$repair[ref_online]'"));
	if($default[ref_product]) $r_product = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE id='$repair[ref_product]'"));

	$update_ = mysqli_query($connect, "UPDATE $setting_table SET content_title='$repair[content_title]'
														, content_type='$repair[content_type]'
														, web_content='$repair[web_content]'
														, mob_content='$repair[mob_content]'
														, ref_link='$repair[ref_link]'
														, ref_target='$repair[ref_target]'
														, ref_program='$repair[ref_program]'
														, ref_board='$repair[ref_board]'
														, ref_online='$repair[ref_online]'
														, ref_product='$repair[ref_product]'
														, memo='$repair[memo]'
														, state='$repair[state]'
														, reg_date='$reg_date'
														, ip='$ip'
														, writer='$_SESSION[member_id]'
													WHERE content_id='$repair[content_id]'
								");
	if($update_) $history_result = true;
	else $history_result = false;
	$result_array = array("history_title" => $repair[content_title]
					,"content_title" => $repair[content_title] 
					,"content_type" => $repair[content_type] 
					,"web_content" => $repair[web_content] 
					,"mob_content" => $repair[mob_content] 
					,"ref_link" => $repair[ref_link] 
					,"ref_target" => $repair[ref_target] 
					,"ref_program" => $repair[ref_program] 
					,"ref_board" => $repair[ref_board] 
					,"ref_board_title" => $r_board[title] 
					,"ref_program_title" => $r_program[title] 
					,"ref_online" => $repair[ref_online] 
					,"ref_online_title" => $r_online[title] 
					,"ref_product" => $repair[ref_product] 
					,"ref_product_title" => $r_product[title] 
					,"memo" => $repair[memo] 
					,"state" => $repair[state] 
					,"reg_date" => $repair[reg_date] 
					,"ip" => $repair[ip] 
					,"writer" => $repair[writer] 
					,"history_result" => $history_result 
			 );

	$result = json_encode($result_array);
	echo($result);
?>
