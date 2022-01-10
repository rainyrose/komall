<?
// 게시판 ID 정리
$id = trim("board_" . $id);
$reg_date = date("Y-m-d H:i:s");
$category_tmp = $_POST[category_tmp];
$category_detail = $_POST[category_detail];

foreach($category_tmp as $value){
	if($value){
		$category_detail .= $value ."|";
	}
}

$category_detail = substr($category_detail, 0, -1);
if(!$use_comment) $use_comment = "N";
if(!$use_category) $use_category = "N";
if(!$use_reply) $use_reply = "N";
if(!$list_limit) $list_limit = "15";
if(!$file_limit_size) $file_limit_size = "10";

if($mode == "register_proc"){

	if($skin == "instagram"){
		$instagram = get_instargramToken($instagram_appid, $instagram_appkey, $instagram_uri, $instagram_shortToken);
		//$instagram_shortToken = $instagram[shortToken];
		$instagram_userId = $instagram[user_id];
		$instagram_tokenRedate = $instagram[Redate];
		$instagram_accessToken = $instagram[access_token];
	}

	// 게시판 ID 중복 체크
	$check = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM koweb_board_config WHERE id='$id'"));
	//if ($check[0])	error("중복된 게시판 ID 입니다.");

	//신규 스킨 추가나 컬럼 추가 될시 아래 add_query 구문에 추가하세요 * NULL 허용 필수 *
	$add_query = "`money` varchar(10) DEFAULT NULL,
	`contact_type` varchar(10) DEFAULT NULL,
	`term` varchar(100) DEFAULT NULL,
	`area` varchar(100) DEFAULT NULL,
	`area_hidden` varchar(100) DEFAULT NULL,
	`metatag_content` text(0) DEFAULT NULL,
	`select_date` varchar(32) DEFAULT NULL,";

	if($skin=="review"){
		$add_query .="`review_score` varchar(50) DEFAULT NULL,";
		$add_query .="`order_id` varchar(255) DEFAULT NULL,";
	}

	// 게시판 스키마
	$schema = "CREATE TABLE `$id` (
	  `no` int(11) NOT NULL AUTO_INCREMENT,
	  `depth` int(2) DEFAULT '1',
	  `ref_no` int(50) DEFAULT '0',
	  `ref_group` int(50) DEFAULT '0',
	  `id` varchar(50) DEFAULT '',
	  `CI` varchar(255) DEFAULT '',
	  `DI` varchar(255) DEFAULT '',
	  `name` varchar(50) NOT NULL,
	  `password` varchar(255) DEFAULT '',
	  `phone` varchar(50) DEFAULT '',
	  `email` varchar(50) DEFAULT '',
	  `zip` varchar(10) DEFAULT '',
	  `address1` varchar(255) DEFAULT '',
	  `address2` varchar(255) DEFAULT '',
	  `category` varchar(255) DEFAULT '',
	  `title` varchar(255) NOT NULL,
	  `comments_type` varchar(10) DEFAULT '',
	  `tag_type` varchar(10) DEFAULT '',
	  `comments` text,
	  `etc` text,
	  `notice` char(1) DEFAULT '',
	  `secret` char(1) DEFAULT '',
	  `file_type` varchar(255) DEFAULT 'zip|jpg|jpeg|png|gif|bmp',
	  `file_1` varchar(255) DEFAULT '',
	  `file_2` varchar(255) DEFAULT '',
	  `file_3` varchar(255) DEFAULT '',
	  `file_4` varchar(255) DEFAULT '',
	  `file_5` varchar(255) DEFAULT '',
	  `file_6` varchar(255) DEFAULT '',
	  `file_7` varchar(255) DEFAULT '',
	  `file_8` varchar(255) DEFAULT '',
	  `file_9` varchar(255) DEFAULT '',
	  `file_10` varchar(255) DEFAULT '',
	  `reg_date` varchar(20) NOT NULL,
	  `ip` varchar(20) NOT NULL,
	  `reply_state` char(1) DEFAULT '',
	  `reply_id` varchar(50) DEFAULT '',
	  `reply_name` varchar(50) DEFAULT '',
	  `reply_phone` varchar(50) DEFAULT '',
	  `reply_email` varchar(50) DEFAULT '',
	  `reply_comments` text,
	  `reply_ip` varchar(20) DEFAULT '',
	  `reply_date` varchar(20) DEFAULT '',
	  `reply_file_1` varchar(255) DEFAULT '',
	  `reply_file_2` varchar(255) DEFAULT '',
	  `reply_file_3` varchar(255) DEFAULT '',
	  `reply_file_4` varchar(255) DEFAULT '',
	  `reply_file_5` varchar(255) DEFAULT '',
	  `view_count` int(11) DEFAULT '0',
	  `hidden` char(1) DEFAULT '',
	  `link` varchar(255) DEFAULT NULL,
	  $add_query
	  PRIMARY KEY (`no`),
	  KEY `name` (`name`),
	  KEY `title` (`title`)
	);";

	/*----------------------------------------------------------------------------*/
	// 작업
	/*----------------------------------------------------------------------------*/
	// 게시판 생성
	@mysqli_query($connect, $schema);

	foreach($dept_auth as $value){
		$dept_query .= $value."|";
	}
	$dept_query = substr($dept_query, 0, -1);

	if($skin == "area"){
		$use_category = "Y";
		$category_detail = "서울|인천|경기|대전|충남|충북|강원|광주|전남|전북|대구|경북|경남|울산|부산|제주";
	}

	$sort_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_board_config ORDER BY sort DESC LIMIT 1"));
	$sort = $sort_[sort]+1;

	// 게시판 정보 입력
	@mysqli_query($connect, "INSERT INTO koweb_board_config VALUES('', '$id', '$title', '$skin', '$auth_write', '$auth_read', '$auth_reply', '$auth_delete', '$auth_comment', '$use_category', '$category_detail', '$use_comment', '$use_reply', '$always_secret', '$file_count', '$file_limit_size', '$list_limit', '$sms_auth', '$spam_auth', '$namecheck_auth', '$is_membership', '$use_auth_level','$use_auth_person', '$use_auth_dept', '$instagram_uri', '$instagram_appid', '$instagram_appkey', '$instagram_userId', '$instagram_shortToken', '$instagram_accessToken', '$instagram_tokenRedate',
	'$sort','$reg_date', '$state')");

	// 첨부파일 폴더 생성
	mkdir($_SERVER[DOCUMENT_ROOT] . "/upload/" . $id);
	chmod($_SERVER[DOCUMENT_ROOT] . "/upload/" . $id, 0777);

	$alert_txt = "등록";

} else if ($mode == "sort") {

		//기본정보
		$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_board_config WHERE no='$no'"));

		if($sort_mode == "up"){
			$sort_WHERE = "AND sort < '$default[sort]' ORDER BY sort DESC";
		} else {
			$sort_WHERE = "AND sort > '$default[sort]' ORDER BY sort ASC";
		}

		$query = "SELECT * FROM koweb_board_config WHERE 1=1 $sort_WHERE LIMIT 1";
		$result = mysqli_query($connect, $query);
		$result2 = mysqli_query($connect, $query);
		$check_row = mysqli_fetch_array($result);

		if($check_row[0]){
			$prev_data = mysqli_fetch_array($result2);
			$tmp_sort = "";
			$tmp_sort = $default[sort];
			$default[sort] = $prev_data[sort];
			$prev_data[sort] = $tmp_sort;

			if(!$prev_data[sort]) $prev_data[sort] = 1;
			$prev_update = mysqli_query($connect, "UPDATE koweb_board_config SET sort='$prev_data[sort]' WHERE no='$prev_data[no]'");
			$default = mysqli_query($connect, "UPDATE koweb_board_config SET sort='$default[sort]' WHERE no='$default[no]'");
		}

		alert("정렬이 변경 되었습니다.");

} else if ($mode == "register_modify_proc"){

	foreach($dept_auth as $value){
		$dept_query .= $value."|";
	}
	$dept_query = substr($dept_query, 0, -1);

	//기존게시판정보 불러오기
	$board_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_board_config WHERE no='$no' LIMIT 1"));

	//지도형게시판으로 수정하였을시, 기존에 지도게시판이었으면 카테고리 건들필요 없음, 기존에 지도게시판이 아니었다면 카테고리내용 자동변경
	if($skin == "area"){
		if($board_[skin] != "area"){
			$use_category = "Y";
			$category_detail = "서울|인천|경기|대전|충남|충북|강원|광주|전남|전북|대구|경북|경남|울산|부산|제주";
		}
	}

	if($skin == "instagram"){
		if(($board_[instagram_appid] != $instagram_appid) || ($board_[instagram_appid] != $instagram_appkey) || ($board_[instagram_shortToken] != $instagram_shortToken)){
			$instagram = get_instargramToken($instagram_appid, $instagram_appkey, $instagram_uri, $instagram_shortToken);
			//$instagram_shortToken = $instagram[shortToken];
			$instagram_userId = $instagram[user_id];
			$instagram_tokenRedate = $instagram[Redate];
			$instagram_accessToken = $instagram[access_token];
		} 
	}

	@mysqli_query($connect, "UPDATE koweb_board_config  SET title='$title', skin='$skin', auth_write='$auth_write', auth_read='$auth_read', auth_reply='$auth_reply', auth_delete='$auth_delete', auth_comment='$auth_comment', use_category='$use_category', category_detail='$category_detail', use_comment='$use_comment', use_reply='$use_reply', always_secret = '$always_secret' , file_count='$file_count', file_limit_size='$file_limit_size', list_limit='$list_limit', reg_date='$reg_date', sms_auth='$sms_auth', spam_auth='$spam_auth', namecheck_auth='$namecheck_auth', is_membership = '$is_membership', use_auth_level='$use_auth_level',  use_auth_person='$use_auth_person', use_auth_dept='$use_auth_dept', sort='$sort', instagram_uri = '$instagram_uri', instagram_appid = '$instagram_appid', instagram_appkey='$instagram_appkey', instagram_accessToken = '$instagram_accessToken', reg_date='$reg_date', state='$state', instagram_tokenRedate='$instagram_tokenRedate', instagram_userId = '$instagram_userId', instagram_shortToken= '$instagram_shortToken' WHERE no='$no'");

	$alert_txt = "수정";

} else {
	//게시판 정보
	$board_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_board_config WHERE no='$no' LIMIT 1"));
	$rename_ = date("YmdHis")."_deleted_".$board_[id];
	@mysqli_query($connect, "RENAME TABLE $board_[id] TO $rename_");
	@rename($_SERVER[DOCUMENT_ROOT] . "/upload/" . $board_[id], $_SERVER[DOCUMENT_ROOT] . "/upload/" . $rename_);
	@mysqli_query($connect, "DELETE FROM koweb_board_config WHERE no = '$no' LIMIT 1");

	//auth 삭제
	@mysqli_query($connect, "DELETE FROM koweb_auth_config WHERE auth_type = 'board' AND auth_id = '$board_[id]'");

	//content 업데이트
	@mysqli_query($connect, "UPDATE koweb_content_config SET ref_board = '' WHERE ref_board = '$board_[id]'");

	$alert_txt = "삭제";
}

/*----------------------------------------------------------------------------*/
// 마무리
/*----------------------------------------------------------------------------*/
?>
<script type="text/javascript">
alert("<?=$alert_txt?> 되었습니다.");
location.href = "<?=$PHP_SELF?>?type=<?=$_GET[type]?>&core=<?=$_GET[core]?>&manager_type=<?=$_GET[manager_type]?>&amp;start=<?=$$_GET[start]?>&amp;search_key=<?=$$_GET[search_key]?>&amp;keyword=<?=$$_GET[keyword]?>";
</script>
