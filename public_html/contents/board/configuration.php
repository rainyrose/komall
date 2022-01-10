<?

if($mid != "member"){

	switch($menu_default[use_type]){
		case "content" :
			if(!$menu_default[menu_id]) {
				error("메뉴 연결이 정상적으로 설정되지 않았습니다.");
				exit;
			}
			$content_default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id = '$menu_default[content_id]' LIMIT 1"));
			$return = $_SERVER[DOCUMENT_ROOT]."/".$content_default[content_type]."/".$content_default[content_type].".php";
			include_once $return;
			break;

		case "link" :
			if(!$menu_default[link_menu_id]) {
				error("메뉴 연결이 정상적으로 설정되지 않았습니다.");
				exit;
			}
			$link_menu_id = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_menu_config WHERE menu_id = '$menu_default[link_menu_id]' LIMIT 1"));
			url("/contents/".$link_menu_id[dir]."/page.html?mid=$menu_default[link_menu_id]");
			break;
	}
} else{
	include_once $_SERVER['DOCUMENT_ROOT']."/member/member.php";
}

?>
