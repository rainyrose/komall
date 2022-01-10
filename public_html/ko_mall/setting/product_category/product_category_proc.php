<?
	$reg_date = date("Y-m-d H:i:s");
	$this_count = 0;

	if($mode == "write_proc"){
		//변수정리
		//depth 값이 1이면 대분류 (1차)
		if($depth == 1){
			//그룹넘버 정함
			$ref_group_tmp = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE delete_state != 'Y' AND depth = '1' GROUP BY ref_group ORDER BY ref_group DESC LIMIT 1"));
			$ref_group = $ref_group_tmp[ref_group] + 1;
			$ref_no = "";
			$depth = "1";
			$depth_history = "";
			$state = "N";
			$sort_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE depth='$depth' ORDER BY sort DESC LIMIT 1"));

		} else {
			//직전차수 정보 가져오기
			$query = "SELECT * FROM $setting_table WHERE no='$ref_no'";
			$result = mysqli_query($connect, $query);
			$prev = mysqli_fetch_array($result);
			$ref_group = $prev[ref_group];
			$ref_no = $prev[no];
			$depth = $prev[depth] + 1;
			$depth_history = $prev[depth_history];
			$product_category_id = $prev[id];
			$state = "N";
			$sort_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE depth='$depth' AND ref_group='$ref_group' AND category='$category' ORDER BY sort DESC LIMIT 1"));
		}

		$sort = $sort_[sort]+1;

		//값 저장
		mysqli_query($connect, "INSERT INTO $setting_table VALUES(''
													,'$category'
													,'$ref_group'
													, '$ref_no'
													, '$depth'
													, '$depth_history'
													, '$product_category_id'
													, '$title'
													, 'Y'
													, 'Y'
													, '$use_type'
													, '$use_realname'
													, '$use_sell'
													, '$use_19'
													, '$use_dept_auth'
													, '$accept_dept'
													, '$use_user_auth'
													, '$accept_user'
													, '$use_level_auth'
													, '$accept_level'
													, '$memo'
													, '$sort'
													, 'Y'
													, 'N'
													, '$reg_date'
													, '$_SESSION[member_id]')");

		$rowid = mysqli_insert_id($connect);

		if($depth == 1){
			$ref_no = mysqli_insert_id($connect);
			$product_category_id = sprintf('%03d',$ref_no);
			$depth_history = $product_category_id;
		 	mysqli_query($connect, "UPDATE $setting_table SET ref_no='$ref_no', depth_history='$depth_history', id='$product_category_id' WHERE no='$ref_no'");
		} else {
			$product_category_id .= sprintf('%03d',$rowid);
			$depth_history .= "|" . $product_category_id;
			mysqli_query($connect, "UPDATE $setting_table SET depth_history='$depth_history', id='$product_category_id' WHERE no='$rowid'");
		}
		alert("등록되었습니다.");
		url($common_queryString."&mode=write&category=$category");

	} else if($mode == "cate_write_proc"){

		mysqli_query($connect, "INSERT INTO koweb_product_category VALUES('',  '$title', '$category', '$state')");
		alert("카테고리 상품분류 카테고리가 등록 되었습니다.");
		url($common_queryString."&mode=$category&category=$category");

	} else if($mode == "cate_modify_proc"){

		mysqli_query($connect, "UPDATE koweb_product_category SET title = '$title', state = '$state' WHERE no = '$no'");
		alert("언어별 상품분류 카테고리가 수정 되었습니다.");
		url($common_queryString."&mode=$category&category=$category");

	} else if($mode == "cate_delete_proc"){
		mysqli_query($connect, "DELETE FROM koweb_product_category WHERE no='$no' AND category !='default'");
		alert("언어별 상품분류 카테고리가 삭제 되었습니다.");
		url($common_queryString."&mode=$category&category=$category");

	}

?>
