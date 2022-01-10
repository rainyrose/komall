<?
	$reg_date = date("Y-m-d H:i:s");
	$this_count = 0;

	$setting_table = "koweb_product";
	$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/product/";

	if(!$min_count){
		$min_count = 1;
	}

	//$product_title = $_POST['product_title'];
	$product_title = addslashes($_POST['product_title']);

	if($mode == "write_proc"){

		//분류
		//$category_navi = substr($pcate_navication, 0, -1);
		$category_navi = $pcate_navication;
		$category = end(explode("|", substr($category_navi, 0, -1)));

		//정렬
		if(!$sort){
			$sort_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE category='$category' ORDER BY sort DESC LIMIT 1"));
			$sort = $sort_[sort]+1;
		}

		//아이디
		if($direct_id == "Y" && $id != ""){
			//직접입력시
			$id_ = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting_table WHERE id = '$id'"));
			if($id_ > 0){
				error("중복된 아이디 입니다.");
				exit;
			} else {
				$id = $id;
			}

		} else {
			$id_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table ORDER BY id DESC LIMIT 1"));
			$id = rand_id($setting_table);
		}

		//배송정보

		if($deli_type != "default"){
			if($deli_type == "def"){
				$deli_price_type = $_POST['deli_price_type'];
				$deli_price = $_POST['deli_price'];

				foreach($deli_price_type as $value){
					if($value){
						$deli_price_type_tmp .= $value ."|";
					}
				}
				foreach($deli_price as $value){
					if($value){
						$deli_price_tmp .= $value ."|";
					}
				}

				$deli_price_type = substr($deli_price_type_tmp, 0, -1);
				$deli_price = substr($deli_price_tmp, 0, -1);
			} else {
				$deli_price = $_POST[deli_price][0];
			}
		}


	//파일업로드
	for($i = 1; $i <= 9; $i++){
		if($_FILES["img_".$i][size] > 0){
			if ($_FILES["img_".$i][size] > 2 * 1024 * 1024) {
				error("첨부파일 ".$i."의 용량 제한은 2MB 입니다.");
				exit;
			} else {
				${'img_'.$i} = upload_file($dir, $_FILES["img_".$i][tmp_name], $_FILES["img_".$i][name]);
				$add_query .= ", img_".$i." =  '". ${'img_'.$i} . "'";
			}
		}
	}

	if($_FILES[title_img][size] > 0){
		if ($_FILES[title_img][size] > 2 * 1024 * 1024) {
			error("첨부파일의 용량 제한은 2MB 입니다.");
			exit;
		} else {
			$title_img = upload_file($dir, $_FILES[title_img][tmp_name], $_FILES[title_img][name]);
		}
	}

		//값 저장
		mysqli_query($connect, "INSERT INTO $setting_table SET category = '$category'
													, category_navi = '$category_navi'
													, seller = '$seller'
													, shower = '$shower'
													, hit = '$hit'
													, pick = '$pick'
													, new = '$new'
													, best = '$best'
													, discount = '$discount'
													, sort = '$sort'
													, product_title = '$product_title'
													, id = '$id'
													, user_id = '$user_id'
													, simple_info = '$simple_info'
													, use_telform = '$use_telform'
													, manufacturer = '$manufacturer'
													, origin = '$origin'
													, brand = '$brand'
													, model = '$model'
													, web_content = '$web_content'
													, mob_content = '$mob_content'
													, product_memo = '$product_memo'
													, set_type = '$set_type'
													, price = '$price'
													, origin_price = '$origin_price'
													, tax_type = '$tax_type'
													, limit_type = '$limit_type'
													, limit_1st = '$limit_1st'
													, point_type = '$point_type'
													, point_detail = '$point_detail'
													, use_soldout = '$use_soldout'
													, use_smsalram = '$use_smsalram'
													, min_count = '$min_count'
													, max_count = '$max_count'
													, stock_count = '$stock_count'
													, use_stock_alram = '$use_stock_alram'
													, stock_alram = '$stock_alram'
													, option_set1 = '$option_set1'
													, option_set2 = '$option_set2'
													, option_set3 = '$option_set3'
													, title_img = '$title_img'
													$add_query
													, refer_product = '$refer_product'
													, deli_type = '$deli_type'
													, deli_price_type = '$deli_price_type'
													, deli_price = '$deli_price'
													, reg_date = '$reg_date'");

		$rowid = mysqli_insert_id($connect);

		//옵션SET
		$count = 1;
		$title = $_POST[title];

		$parents_title = $_POST[parents_title];
		$parents_use_color = $_POST[parents_use_color_value];
		$option_title = $_POST[option_title];

		$option_title_col = array();

		foreach($parents_title AS $key => $v){

			$options_query_ = "INSERT INTO koweb_option_set SET  option_type = 'P', ref_product='$rowid', title='$parents_title[$key]', use_color='$parents_use_color[$key]', sort='$count', reg_date='$reg_date'";

			$options_set = mysqli_query($connect, $options_query_);
			$options_set_id = mysqli_insert_id($connect);
			$option_category = "option".$count."_";

			foreach($_POST["$option_category"."option_title"] AS $key2 => $v2){
				$option_title = $_POST["$option_category"."option_title"][$key2];
				$color_value = $_POST["$option_category"."color_value"][$key2];
				$option_state = $_POST["$option_category"."option_state_value"][$key2];
				$option_title_col[$key][] = preg_replace("/\s+/", "", $option_title);
				$options_value_ .= $color_value."|".$option_title."|".$option_state."^";
				$options_set_query_ = "INSERT INTO koweb_option_set SET option_type ='C', ref_product='$rowid', ref_parents = '$options_set_id', title='$title[$key]', use_color='$use_color[$key]', option_title='$option_title', color_value='$color_value', option_state = '$option_state', sort='$count', reg_date='$reg_date'";
				$options_set = mysqli_query($connect, $options_set_query_);
			}
		$count++;
		}

		//디테일옵션 SET
		$option_title = $_POST[option_title];
		$option_id = $_POST[option_id];
		$option_type = $_POST[option_type];
		$option_price = $_POST[option_price];
		$stock = $_POST[stock];
		$option_color1 = $_POST[option_color1];
		$option_color2 = $_POST[option_color2];
		$option_color3 = $_POST[option_color3];
		$safe_stock = $_POST[safe_stock];
		$option_use_sold = $_POST[use_soldout_value];
		$detail_state = $_POST[detail_state];

	foreach($option_id AS $key => $v){
			//ex)초기값 : 양말발가락/빨강 , 양말/빨강 등의 값
			$target = preg_replace("/\s+/", "", $option_title[$key]);
			$id_make_ = array();
			//$option_title_col는 array( array(0=>(0=>양말발가락,1=>양말),1=>(0=>빨강)) )의 형태.
			foreach ($option_title_col as $option_index => $option_title_array) {
				// usort($option_title_array,'str_resort');
				//$option_title_value는 양말발가락 혹은 양말 같은 개인의 값
				foreach ($option_title_array as $option_title_index => $option_title_value) {
					//양말발가락/빨강 길이 - 양말
					$v_len = mb_strlen($option_title_value,"utf-8");
					$t_len = mb_strlen($target,"utf-8");

					//처음값이 target과 title_value가 일치하는지 확인
					if(strpos($target,$option_title_value) === 0){
						$minus_len = $t_len-$v_len;

						//공백제거한 옵션 => 해당 텍스트부터 끝까지 잘라냄.
						$target_tmp = mb_substr($target,-$minus_len,NULL,'utf-8');
						// 그러면 '/'(슬래시) 가 남게되는데, 그걸 확인. 첫글자가 '/' 가 아니라면 내가 원하는값이 아님
						$target_tmp = mb_substr($target_tmp, 0,1,'utf-8');
						//단 텍스트 길이가 일치하는 경우, 옵션이 하나뿐인경우라서 필터에서 제외된다.
						if($target_tmp != "/" && $minus_len != 0) continue;

						//아래의 루프는 양말/발가락 <= 자체가 항목명일수도 있는데, 양말 이라는 필터로 위의 필터에 모두 부합하기에 필터에 걸리지않는다.
						//해당 예외사항을 걸러내기위해 최종적으로 항목의 각 텍스트 길이를 비교하고, 양말/발가락 의 텍스트가 클수밖에 없기때문에 해당사항에 일치되면 루프를 넘긴다.
						foreach ($option_title_array as $option_title_index2 => $option_title_value2) {
							if($option_title_value == $option_title_value2) continue;
							if(strpos($target,(string)$option_title_value2) === 0){
								if(mb_strlen($option_title_value,"UTF-8") < mb_strlen($option_title_value2,"UTF-8")){
									continue 2;
								}
							}
						}

						//공백제거한 옵션 - 해당 텍스트부터 끝까지 잘라냄.
						$target = mb_substr($target,-$minus_len,NULL,'utf-8');
						// 그러면 '/'(슬래시) 가 남게되는데, 그걸 제거
						$target = mb_substr($target, 1,NULL,'utf-8');
						$id_make_[] =  (string)$option_title_value;

						//$minus_len가 0이란것은 최종 value랑 target이랑 일치한다는 뜻이므로, id_make 최종완료시킴.
						if($minus_len == 0) break 2;
					}
				}


			}

			$id_make = join("|",$id_make_);
			// $id_make = str_replace("/", "|", $option_title[$key]);
			// $id_make = preg_replace("/\s+/", "", $id_make);
			$current_id = rand_id("koweb_option_detail");

			if(!$option_id[$key]){
				$option_id[$key] = $current_id;
			} else {
				$current_id = $option_id[$key];
			}


			$addi_query = "INSERT INTO koweb_option_detail SET ref_set = '$options_set_id', ref_product = '$rowid', option_color1 = '$option_color1[$key]', option_color2 = '$option_color2[$key]', option_color3 = '$option_color3[$key]',
			otype='detail', title='$option_title[$key]', type_id='$id_make', current_id='$current_id', id = '$option_id[$key]',	price_type = '$option_type[$key]', price = '$option_price[$key]'
			, stock = '$stock[$key]', safe_stock = '$safe_stock[$key]', soldout='$option_use_sold[$key]', sort='$sort', state = '$detail_state[$key]', reg_date = '$reg_date'";

			mysqli_query($connect, $addi_query);
		}
		//추가옵션SET
		$additional_item_title = $_POST[additional_item_title];
		$additional_item_id = $_POST[additional_item_id];
		$additional_item_type = $_POST[additional_item_type];
		$additional_item_price = $_POST[additional_item_price];
		$additional_item_stock = $_POST[additional_item_stock];
		$additional_item_safe_stock = $_POST[additional_item_safe_stock];
		$additional_item_soldout = $_POST[additional_item_soldout_value];
		$additional_item_state = $_POST[additional_item_state];


		foreach($additional_item_title AS $key => $v){

			$current_id = rand_id("koweb_option_detail");

			if(!$additional_item_id[$key]){
				$additional_item_id[$key] = $current_id;
			} else {
				$current_id = $option_id[$key];
			}

			if(!$additional_item_stock[$key]){
				$additional_item_stock[$key] = 0;
			}

			if(!$additional_item_stock[$key]){
				$additional_item_stock[$key] = 0;
				$additional_item_soldout[$key] = "Y";
			}

			if(!$additional_item_safe_stock[$key]){
				$additional_item_safe_stock[$key] = 0;
			}


			$addi_query = "INSERT INTO koweb_option_detail SET ref_set = '', ref_product = '$rowid', otype='add', title='$additional_item_title[$key]', type_id='', current_id='$current_id', id = '$additional_item_id[$key]',
			price_type = '$additional_item_type[$key]', price = '$additional_item_price[$key]', stock = '$additional_item_stock[$key]', safe_stock = '$additional_item_safe_stock[$key]', soldout='$additional_item_soldout[$key]', sort='$sort', state = '$additional_item_state[$key]', reg_date = '$reg_date'";

			mysqli_query($connect, $addi_query);
		}

		if($_SERVER[REMOTE_ADDR] == "106.242.31.74"){
			$directi_query = "INSERT INTO koweb_option_detail SET ref_product = '$rowid', otype='direct', title='$direct_title',state = '$direct_state', reg_date = '$reg_date'";
			mysqli_query($connect, $directi_query);
		}

		//상품요약정보
		$simple_info_query = "INSERT INTO koweb_simple_info SET type='$set_type', ref_product = '$rowid',";
		for($i = 1; $i <= 20; $i++){
			$simple_info_query .= " field_".$i." = '".${"field_".$i}."',";
		}

		$simple_info_query = substr($simple_info_query, 0, -1);
		$simple_result = mysqli_query($connect, $simple_info_query);

		alert("등록되었습니다.");
		url($common_queryString);

	} else if($mode == "modify_proc"){

		$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE no = '$no'"));

		//분류
		//$category_navi = substr($pcate_navication, 0, -1);
		if($pcate_navication){
			$category_navi = $pcate_navication;
			$category = end(explode("|", substr($category_navi, 0, -1)));
			//$add_query_cat = ", category_navi = '$category_navi'";
			//$add_query_cat_navi = ", category = '$category'";
		}

		//정렬
		if(!$sort){
			$sort_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE category='$category' ORDER BY sort DESC LIMIT 1"));
			$sort = $sort_[sort]+1;
		}

		//아이디
		if($direct_id == "Y" && $id != $default[id]){
			//직접입력시
			$id_ = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting_table WHERE id = '$id'"));
			if($id_ > 0){
				error("중복된 아이디 입니다.");
				exit;
			} else {
				$id = $id;
				$add_query_id = ", id = '$id'";
			}
		}

		//배송정보
		if($deli_type != "default"){
			if($deli_type == "def"){
				$deli_price_type = $_POST['deli_price_type'];
				$deli_price = $_POST['deli_price'];

				foreach($deli_price_type as $value){
					if($value){
						$deli_price_type_tmp .= $value ."|";
					}
				}
				foreach($deli_price as $value){
					if($value){
						$deli_price_tmp .= $value ."|";
					}
				}

				$deli_price_type = substr($deli_price_type_tmp, 0, -1);
				$deli_price = substr($deli_price_tmp, 0, -1);
			} else {
				$deli_price = $_POST[deli_price][0];
			}
		}

	//파일업로드
	for($i = 1; $i <= 9; $i++){
		if($_FILES["img_".$i][size] > 0){
			if ($_FILES["img_".$i][size] > 2 * 1024 * 1024) {
				error("첨부파일 ".$i."의 용량 제한은 2MB 입니다.");
				exit;
			} else {
				${'img_'.$i} = upload_file($dir, $_FILES["img_".$i][tmp_name], $_FILES["img_".$i][name]);
				$add_query .= ", img_".$i." =  '". ${'img_'.$i} . "'";
			}
		}
	}

	if($_FILES[title_img][size] > 0){
		if ($_FILES[title_img][size] > 2 * 1024 * 1024) {
			error("첨부파일의 용량 제한은 2MB 입니다.");
			exit;
		} else {
			$title_img = upload_file($dir, $_FILES[title_img][tmp_name], $_FILES[title_img][name]);
			$add_title_query = ", title_img='$title_img'";
		}
	}

	$modify_row = mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM $setting_table WHERE no = '$no' LIMIT 1"));
	if($del_title_img == "Y"){
		$add_title_query .= ", title_img =  ''";
		@unlink($dir . $modify_row[title_img]);
		@unlink($dir . "thumb_".$modify_row[title_img]);
	}
	for($i = 1; $i <= 9; $i++){
		if(${'del_'.$i} == "Y"){
			$add_query .= ", img_".$i." =  ''";
			@unlink($dir . $modify_row["img_".$i]);
			@unlink($dir . "thumb_".$modify_row["img_".$i]);
		}
	}

		//값 저장
		mysqli_query($connect, "UPDATE $setting_table SET category = '$category'
													, category_navi = '$category_navi'
													, seller = '$seller'
													, shower = '$shower'
													, hit = '$hit'
													, pick = '$pick'
													, new = '$new'
													, best = '$best'
													, discount = '$discount'
													, sort = '$sort'
													, product_title = '$product_title'
													$add_query_id
													, user_id = '$user_id'
													, simple_info = '$simple_info'
													, use_telform = '$use_telform'
													, manufacturer = '$manufacturer'
													, origin = '$origin'
													, brand = '$brand'
													, model = '$model'
													, web_content = '$web_content'
													, mob_content = '$mob_content'
													, product_memo = '$product_memo'
													, set_type = '$set_type'
													, price = '$price'
													, origin_price = '$origin_price'
													, tax_type = '$tax_type'
													, limit_type = '$limit_type'
													, limit_1st = '$limit_1st'
													, point_type = '$point_type'
													, point_detail = '$point_detail'
													, use_soldout = '$use_soldout'
													, use_smsalram = '$use_smsalram'
													, min_count = '$min_count'
													, max_count = '$max_count'
													, stock_count = '$stock_count'
													, use_stock_alram = '$use_stock_alram'
													, stock_alram = '$stock_alram'
													, option_set1 = '$option_set1'
													, option_set2 = '$option_set2'
													, option_set3 = '$option_set3'
													$add_title_query
													$add_query
													, refer_product = '$refer_product'
													, deli_type = '$deli_type'
													, deli_price_type = '$deli_price_type'
													, deli_price = '$deli_price'
												WHERE no = '$no'");


		$rowid = $no;
		//옵션 초기화

		$delete_set = mysqli_query($connect, "DELETE FROM koweb_option_set WHERE ref_product = '$no'");
		$delete_detail = mysqli_query($connect, "DELETE FROM koweb_option_detail WHERE ref_product = '$no'");

		//옵션SET
		$count = 1;
		$title = $_POST[title];

		$parents_title = $_POST[parents_title];
		$parents_use_color = $_POST[parents_use_color_value];
		$option_title = $_POST[option_title];

		$option_title_col = array();

		foreach($parents_title AS $key => $v){
			$options_query_ = "INSERT INTO koweb_option_set SET  
										option_type = 'P', 
										ref_product='$rowid', 
										title='$parents_title[$key]', 
										use_color='$parents_use_color[$key]', 
										sort='$count', 
										reg_date='$reg_date'
								";
			$options_set = mysqli_query($connect, $options_query_);
			$options_set_id = mysqli_insert_id($connect);
			$option_category = "option".$count."_";

			foreach($_POST["$option_category"."option_title"] AS $key2 => $v2){
				$option_title = $_POST["$option_category"."option_title"][$key2];			// 옵션값 항목
				$color_value = $_POST["$option_category"."color_value"][$key2];				// 색상표 선택
				$option_state = $_POST["$option_category"."option_state_value"][$key2];		// 사용함/사용안함

				$option_title_col[$key][] = preg_replace("/\s+/", "", (string)$option_title);
				$options_value_ .= $color_value."|".$option_title."|".$option_state."^";

				$options_set_query_ = "INSERT INTO koweb_option_set SET 
											option_type ='C', 
											ref_product='$rowid', 
											ref_parents = '$options_set_id', 
											title='$title[$key]', 
											use_color='$use_color[$key]', 
											option_title='$option_title', 
											color_value='$color_value', 
											option_state = '$option_state', 
											sort='$count', 
											reg_date='$reg_date'
									";
				$options_set = mysqli_query($connect, $options_set_query_);

				// echo $options_set_query_;
				// echo "<br />";
			}
			$count++;
		}

		//디테일옵션 SET
		$option_title = $_POST[option_title];
		$option_id = $_POST[option_id];
		$option_type = $_POST[option_type];
		$option_price = $_POST[option_price];
		$stock = $_POST[stock];
		$option_color1 = $_POST[option_color1];
		$option_color2 = $_POST[option_color2];
		$option_color3 = $_POST[option_color3];
		$safe_stock = $_POST[safe_stock];
		$option_use_sold = $_POST[use_soldout_value];
		$detail_state = $_POST[detail_state];

		foreach($option_id AS $key => $v){
			$target = preg_replace("/\s+/", "", $option_title[$key]);
			$id_make_ = array();

			foreach ($option_title_col as $option_index => $option_title_array) {
				// usort($option_title_array,'str_resort');

				foreach ($option_title_array as $option_title_index => $option_title_value) {
					$v_len = mb_strlen($option_title_value,"utf-8");
					$t_len = mb_strlen($target,"utf-8");

					if(strpos($target,$option_title_value) === 0){
						$minus_len = $t_len-$v_len;

						//공백제거한 옵션 => 해당 텍스트부터 끝까지 잘라냄.
						$target_tmp = mb_substr($target,-$minus_len,NULL,'utf-8');
						// 그러면 '/'(슬래시) 가 남게되는데, 그걸 확인. 첫글자가 '/' 가 아니라면 내가 원하는값이 아님
						$target_tmp = mb_substr($target_tmp, 0,1,'utf-8');
						if($target_tmp != "/" && $minus_len != 0) continue;

						foreach ($option_title_array as $option_title_index2 => $option_title_value2) {
							if($option_title_value == $option_title_value2) continue;
							if(strpos($target,(string)$option_title_value2) === 0){
								if(mb_strlen($option_title_value,"UTF-8") < mb_strlen($option_title_value2,"UTF-8")){
									continue 2;
								}
							}
						}

						//공백제거한 옵션 - 해당 텍스트부터 끝까지 잘라냄.
						$target = mb_substr($target,-$minus_len,NULL,'utf-8');
						// 그러면 '/'(슬래시) 가 남게되는데, 그걸 제거
						$target = mb_substr($target, 1,NULL,'utf-8');
						$id_make_[] =  (string)$option_title_value;

						//$minus_len가 0이란것은 최종 value랑 target이랑 일치한다는 뜻이므로, id_make 최종완료시킴.
						if($minus_len == 0) break 2;
					}
				}
			}

			$id_make = join("|",$id_make_);
			// $id_make = str_replace("/", "|", $option_title[$key]);
			// $id_make = preg_replace("/\s+/", "", $id_make);
			// echo $id_make;
			// echo "<BR>";
			$current_id = rand_id("koweb_option_detail");

			if(!$option_id[$key]){
				$option_id[$key] = $current_id;
			} else {
				$current_id = $option_id[$key];
			}
			if(!$stock[$key]) $stock[$key] = 0;
			$addi_query = "INSERT INTO koweb_option_detail SET 
									ref_set = '$options_set_id', 
									ref_product = '$rowid', 
									option_color1 = '$option_color1[$key]', 
									option_color2 = '$option_color2[$key]', 
									option_color3 = '$option_color3[$key]', 
									otype='detail', 
									title='$option_title[$key]', 
									type_id='$id_make', 
									current_id='$current_id', 
									id = '$option_id[$key]', 
									price_type = '$option_type[$key]', 
									price = '$option_price[$key]', 
									stock = '$stock[$key]', 
									safe_stock = '$safe_stock[$key]', 
									soldout='$option_use_sold[$key]', 
									sort='$sort', 
									state = '$detail_state[$key]', 
									reg_date = '$reg_date' 
							";
			mysqli_query($connect, $addi_query);
		}

		// print_r($option_title_col);
		// 추가옵션SET
		$additional_item_title = $_POST[additional_item_title];
		$additional_item_id = $_POST[additional_item_id];
		$additional_item_type = $_POST[additional_item_type];
		$additional_item_price = $_POST[additional_item_price];
		$additional_item_stock = $_POST[additional_item_stock];
		$additional_item_safe_stock = $_POST[additional_item_safe_stock];
		$additional_item_soldout = $_POST[additional_item_soldout_value];
		$additional_item_state = $_POST[additional_item_state];

		foreach($additional_item_title AS $key => $v){

			$current_id = rand_id("koweb_option_detail");

			if(!$additional_item_id[$key]){
				$additional_item_id[$key] = $current_id;
			} else {
				$current_id = $additional_item_id[$key];
			}

			if(!$additional_item_stock[$key]){
				$additional_item_stock[$key] = 0;
			}

			if(!$additional_item_stock[$key]){
				$additional_item_stock[$key] = 0;
				$additional_item_soldout[$key] = "Y";
			}

			if(!$additional_item_safe_stock[$key]){
				$additional_item_safe_stock[$key] = 0;
			}

			$addi_query = "INSERT INTO koweb_option_detail SET 
									ref_set = '', 
									ref_product = '$rowid', 
									otype='add', 
									title='$additional_item_title[$key]', 
									type_id='', 
									current_id='$current_id', 
									id = '$additional_item_id[$key]',
									price_type = '$additional_item_type[$key]', 
									price = '$additional_item_price[$key]', 
									stock = '$additional_item_stock[$key]', 
									safe_stock = '$additional_item_safe_stock[$key]', 
									soldout='$additional_item_soldout[$key]', 
									sort='$sort', 
									state = '$additional_item_state[$key]', 
									reg_date = '$reg_date'
							";

			mysqli_query($connect, $addi_query);
		}

		if($_SERVER[REMOTE_ADDR] == "106.242.31.74"){
			$directi_query = "INSERT INTO koweb_option_detail SET ref_product = '$rowid', otype='direct', title='$direct_title',state = '$direct_state', reg_date = '$reg_date'";
			mysqli_query($connect, $directi_query);
		}

		//상품요약정보
		$simple_info_query = "INSERT INTO koweb_simple_info SET type='$set_type', ref_product = '$rowid',";
		for($i = 1; $i <= 20; $i++){
			$simple_info_query .= " field_".$i." = '".${"field_".$i}."',";
		}

		$simple_info_query = substr($simple_info_query, 0, -1);
		$simple_result = mysqli_query($connect, $simple_info_query);

		alert("수정되었습니다.");
		if($referer_url){
			url($referer_url);
		}else{
			url($common_queryString);
		}
	} else if($mode == "delete"){


		$info_check = $_GET[info_check];

		foreach($info_check AS $no){
			//echo "DELETE FROM koweb_simple_info WHERE ref_product = '$no'";
			//상품요약정보 삭제
			$simple_info_del = mysqli_query($connect, "DELETE FROM koweb_simple_info WHERE ref_product = '$no'");

			//옵션삭제 SET
			$set_info_del = mysqli_query($connect, "DELETE FROM koweb_option_set WHERE ref_product = '$no'");

			//옵션 디테일 삭제
			$detail_info_del = mysqli_query($connect, "DELETE FROM koweb_option_detail WHERE ref_product = '$no'");

			//상품 삭제
			$product_info_del = mysqli_query($connect, "DELETE FROM koweb_product WHERE no = '$no'");
		}

		alert("삭제가 완료되었습니다.");
		url($common_queryString."&category=$category&category_navi=$category_navi&search_key=$search_key&keyword=$keyword&start=&shower=$shower&seller=$seller");
	} else if($mode == "sellerY"){
		$info_check = $_GET[info_check];

		foreach($info_check AS $no){
			$product_info_del = mysqli_query($connect, "UPDATE koweb_product SET seller = 'Y' WHERE no = '$no'");
		}
		alert("판매상태가 완료되었습니다.");
		url($common_queryString."&category=$category&category_navi=$category_navi&search_key=$search_key&keyword=$keyword&start=&shower=$shower&seller=$seller");

	} else if($mode == "sellerN"){
		$info_check = $_GET[info_check];

		foreach($info_check AS $no){
			$product_info_del = mysqli_query($connect, "UPDATE koweb_product SET seller = 'N' WHERE no = '$no'");
		}
		alert("판매상태가 변경되었습니다.");
		url($common_queryString."&category=$category&category_navi=$category_navi&search_key=$search_key&keyword=$keyword&start=&shower=$shower&seller=$seller");

	} else if($mode == "showerY"){
		$info_check = $_GET[info_check];

		foreach($info_check AS $no){
			$product_info_del = mysqli_query($connect, "UPDATE koweb_product SET shower = 'Y' WHERE no = '$no'");
		}
		alert("진열상태가 변경되었습니다.");
		url($common_queryString."&category=$category&category_navi=$category_navi&search_key=$search_key&keyword=$keyword&start=&shower=$shower&seller=$seller");

	} else if($mode == "showerN"){
		$info_check = $_GET[info_check];

		foreach($info_check AS $no){
			$product_info_del = mysqli_query($connect, "UPDATE koweb_product SET shower = 'N' WHERE no = '$no'");
		}
		alert("진열상태가 변경되었습니다.");
		url($common_queryString."&category=$category&category_navi=$category_navi&search_key=$search_key&keyword=$keyword&start=&shower=$shower&seller=$seller");

	} else if($mode == "excel"){
	//	alert("삭제가 완료되었습니다.");
		//url($common_queryString."&category=$category&category_navi=$category_navi&search_key=$search_key&keyword=$keyword&start=&shower=$shower&seller=$seller");

	}


function str_resort($a,$b){
	return strlen($b)-strlen($a);
}
