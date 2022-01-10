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
			$state = "Y";
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
			$menu_id = $prev[menu_id];
			$state = "Y";
			$dir_ = $prev[dir];
			$sort_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE depth='$depth' AND ref_group='$ref_group' AND category='$category' ORDER BY sort DESC LIMIT 1"));
		}

		$sort = $sort_[sort]+1;
	$menu_title = htmlspecialchars_decode($menu_title);

		//값 저장
		mysqli_query($connect, "INSERT INTO $setting_table VALUES(''
													,'$category'
													,'$ref_group'
													, '$ref_no'
													, '$depth'
													, '$depth_history'
													, '$menu_id'
													, '$menu_title'
													, '$use_device_pc'
													, '$use_device_mob'
													, '$use_type'
													, '$content_id'
													, '$link_menu_id'
													, '$description'
													, '$og_description'
													, '$og_sitename'
													, '$og_title'
													, '$use_dept_auth'
													, '$accept_dept'
													, '$use_user_auth'
													, '$accept_user'
													, '$use_level_auth'
													, '$accept_level'
													, '$dir_'
													, '$memo'
													, '$sort'
													, '$state'
													, 'N'
													, '$reg_date'
													, '$_SESSION[member_id]')");

		$rowid = mysqli_insert_id($connect);

		if($depth == 1){
			$ref_no = mysqli_insert_id($connect);
			$menu_id = sprintf('%03d',$ref_no);
			$depth_history = $menu_id;
		 	mysqli_query($connect, "UPDATE $setting_table SET ref_no='$ref_no', depth_history='$depth_history', menu_id='$menu_id' WHERE no='$ref_no'");

			if($category != "default"){
				$path_to = $_SERVER[DOCUMENT_ROOT] . "/" . $category . "/contents/" . $dir_;
				$path_to2 = "/".$category;
			} else {
				$path_to = $_SERVER[DOCUMENT_ROOT] . "/contents/" . $dir_;
				$path_to2 = "";

			}

			mkdir($path_to);
			chmod($path_to, 0777);

			copy($_SERVER['DOCUMENT_ROOT'].$path_to2."/inc/default_setting/page.html", $path_to . "/page.html");
			copy($_SERVER['DOCUMENT_ROOT'].$path_to2."/inc/default_setting/configuration.php", $path_to . "/configuration.php");
			copy($_SERVER['DOCUMENT_ROOT'].$path_to2."/inc/default_setting/default.php", $path_to . "/default.php");
			copy($_SERVER['DOCUMENT_ROOT'].$path_to2."/inc/default_setting/lnb.php", $path_to . "/lnb.php");

		} else {
			$menu_id .= sprintf('%03d',$rowid);
			$depth_history .= "|" . $menu_id;
			mysqli_query($connect, "UPDATE $setting_table SET depth_history='$depth_history', menu_id='$menu_id' WHERE no='$rowid'");
		}
		alert("등록되었습니다.");
		url("/ko_mall/index.html?type=setting&core=manager_setting&manager_type=menu&mode=write&amp;category=$category");

	} else if($mode == "cate_write_proc"){

		$checker_ = mysqli_num_rows(mysqli_query($connect, "SELECT  * FROM koweb_menu_category WHERE WHERE menu_category ='$menu_category' LIMIT 1"));
		if($checker_ > 0){
			error("동알한 디렉토리명이 존재합니다.");
			exit;
		}


			mkdir($_SERVER['DOCUMENT_ROOT'] . "/$menu_category/");
		chmod($_SERVER[DOCUMENT_ROOT] . "/$menu_category/" , 0777);

		@mkdir($_SERVER[DOCUMENT_ROOT] . "/$menu_category/contents");
		@chmod($_SERVER[DOCUMENT_ROOT] . "/$menu_category/contents", 0777);

		@mkdir($_SERVER[DOCUMENT_ROOT] . "/$menu_category/inc");
		@chmod($_SERVER[DOCUMENT_ROOT] . "/$menu_category/inc", 0777);

		@mkdir($_SERVER[DOCUMENT_ROOT] . "/$menu_category/inc/default_setting");
		@chmod($_SERVER[DOCUMENT_ROOT] . "/$menu_category/inc/default_setting", 0777);

		copy($_SERVER['DOCUMENT_ROOT']."/inc/default_setting/page.html", $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/default_setting" . "/page.html") or die("error1");
		@chmod($_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/default_setting" . "/page.html", 0755);

		copy($_SERVER['DOCUMENT_ROOT']."/inc/default_setting/configuration.php", $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/default_setting" . "/configuration.php") or die("error2");
		@chmod($_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/default_setting" . "/configuration.php", 0755);

		copy($_SERVER['DOCUMENT_ROOT']."/inc/default_setting/default.php", $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/default_setting" . "/default.php") or die("error3");
		@chmod($_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/default_setting" . "/default.php", 0755);

		copy($_SERVER['DOCUMENT_ROOT']."/inc/default_setting/lnb.php", $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/default_setting" . "/lnb.php") or die("error4");
		@chmod($_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/default_setting" . "/lnb.php", 0755);

		copy($_SERVER['DOCUMENT_ROOT']."/inc/header.html", $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/header.html") or die("error4");
		@chmod($_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/header.html", 0755);

		copy($_SERVER['DOCUMENT_ROOT']."/inc/top.html", $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/top.html") or die("error4");
		@chmod($_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/top.html", 0755);

		copy($_SERVER['DOCUMENT_ROOT']."/inc/count.php", $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/count.php") or die("error4");
		@chmod($_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/count.php", 0755);

		copy($_SERVER['DOCUMENT_ROOT']."/inc/footer.html", $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/footer.html") or die("error4");
		@chmod($_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/footer.html", 0755);

		//header 수정 재생성
		$filename = $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/header.html";
		$fp = fopen($filename, "r") or die("파일열기에 실패하였습니다");
		$buffer = fread($fp, filesize($filename));
		//echo "<pre>".htmlspecialchars($buffer)."</pre>";
		$buffer = str_replace('<? $site_language = "default"; ?>', '<? $site_language = "'.$menu_category.'"; ?>', $buffer);
		fclose($fp);

		$f = @fopen($filename,'w');
		@fwrite($f,$buffer);
		@fclose($f);

		//default.php 수정
		$filename = $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/default_setting/default.php";
		$fp = fopen($filename, "r") or die("파일열기에 실패하였습니다");
		$buffer = fread($fp, filesize($filename));
		//echo "<pre>".htmlspecialchars($buffer)."</pre>";
		$buffer = str_replace('$site_language = "default";', '$site_language = "'.$menu_category.'";', $buffer);
		fclose($fp);

		$f = @fopen($filename,'w');
		@fwrite($f,$buffer);
		@fclose($f);

		//page.html 수정
		$filename = $_SERVER['DOCUMENT_ROOT'] . "/" . $menu_category . "/inc/default_setting/page.html";
		$fp = fopen($filename, "r") or die("파일열기에 실패하였습니다");
		$buffer = fread($fp, filesize($filename));
		//echo "<pre>".htmlspecialchars($buffer)."</pre>";
		$buffer = str_replace('."/inc/top.html";', '."/'.$menu_category.'/inc/top.html";', $buffer);
		$buffer = str_replace('."/inc/header.html";', '."/'.$menu_category.'/inc/header.html";', $buffer);
		fclose($fp);
		$f = @fopen($filename,'w');
		@fwrite($f,$buffer);
		@fclose($f);

		mysqli_query($connect, "INSERT INTO koweb_menu_category VALUES('',  '$menu_title', '$menu_category', '$state')");

		alert("메뉴분류가 등록 되었습니다.");
		url("/ko_mall/index.html?type=setting&core=manager_setting&manager_type=menu&mode=category&amp;category=$category");

	} else if($mode == "cate_modify_proc"){

		mysqli_query($connect, "UPDATE koweb_menu_category SET menu_title = '$menu_title', state = '$state' WHERE no = '$no'");
		alert("메뉴분류가 수정 되었습니다.");
		url("/ko_mall/index.html?type=setting&core=manager_setting&manager_type=menu&mode=category&amp;category=$category");

	} else if($mode == "cate_delete_proc"){

		$checker_ = mysqli_fetch_array(mysqli_query($connect, "SELECT  * FROM koweb_menu_category WHERE no ='$no' LIMIT 1"));
		recurse_delete_dir($_SERVER['DOCUMENT_ROOT'] . "/" .$checker_[menu_category]);
		mysqli_query($connect, "DELETE FROM koweb_menu_category WHERE no='$no' AND menu_category !='default'");

		alert("메뉴분류가 삭제 되었습니다.");
		url("/ko_mall/index.html?type=setting&core=manager_setting&manager_type=menu&mode=category&amp;category=$category");

	}


?>
