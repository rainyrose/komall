<?
include_once  "../head.php";

#########################################################################
# 접속관련 쿠키정보가 없다면 쿠키 생성 및 통계 데이터 추가
#########################################################################

# 테이블정보
$cookie_domain = "check";
$table_counter = "koweb_statistics";
//$table_referer = "cms_referer";
//$table_referer_path = "cms_referer_path";

if (!$_COOKIE[check]) {
	### 쿠키 생성
	@setcookie("check", "check_ok", 0, "/");

	### 기본 설정
	$c_date = date("Y-m-d");
	$c_time = "h_".date("H");

	### 접속통계 입력
	# 오늘 통계데이터가 존재하지 않는다면 추가
	$date_exists = @mysqli_num_rows(mysqli_query($connect,"SELECT no FROM $table_counter WHERE c_date='$c_date'"));
	if (!$date_exists) mysqli_query($connect,"INSERT INTO $table_counter SET c_date='$c_date'");

	# 접속 브라우져 확인
	if (preg_match("/MSIE/", $_SERVER[HTTP_USER_AGENT])) {
		if (preg_match("/11.0/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_ie110";
		else if (preg_match("/10.0/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_ie100";
		else if (preg_match("/9.0/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_ie90";
		else if (preg_match("/8.0/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_ie80";
		else if (preg_match("/7.0/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_ie70";
		else if (preg_match("/6.0/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_ie60";
		else if (preg_match("/5.5/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_ie55";
		else if (preg_match("/5.0/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_ie50";
		else $browser = "bs_etc";
	}
	else if (preg_match("/SamsungBrowser/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_samsung";
	else if (preg_match("/Chrome/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_chrome";
	else if (preg_match("/Safari/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_safari";
	else if (preg_match("/Firefox/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_firefox";
	else if (preg_match("/Opera/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_opera";
	else if (preg_match("/Android/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_android";
	else if (preg_match("/iPhone/", $_SERVER[HTTP_USER_AGENT])) $browser = "bs_iphone";
	else $browser = "bs_etc";



	# 접속 운영체제 확인
	if (preg_match("/95/", $_SERVER[HTTP_USER_AGENT])) $os = "os_win95";
	else if (preg_match("/98/", $_SERVER[HTTP_USER_AGENT])) $os = "os_win98";
	else if (preg_match("/ME/", $_SERVER[HTTP_USER_AGENT])) $os = "os_winme";
	else if (preg_match("/NT 4./", $_SERVER[HTTP_USER_AGENT])) $os = "os_winnt";
	else if (preg_match("/NT 5.0/", $_SERVER[HTTP_USER_AGENT])) $os = "os_win2000";
	else if (preg_match("/NT 5.1/", $_SERVER[HTTP_USER_AGENT])) $os = "os_winxp";
	else if (preg_match("/NT 5.2/", $_SERVER[HTTP_USER_AGENT])) $os = "os_win2003";
	else if (preg_match("/NT 6.0/", $_SERVER[HTTP_USER_AGENT])) $os = "os_vista";
	else if (preg_match("/NT 6.1/", $_SERVER[HTTP_USER_AGENT])) $os = "os_win7";
	else if (preg_match("/NT 6.2/", $_SERVER[HTTP_USER_AGENT])) $os = "os_win8";
	else if (preg_match("/NT 10/", $_SERVER[HTTP_USER_AGENT])) $os = "os_win10";
	else if (preg_match("/Mac/", $_SERVER[HTTP_USER_AGENT])) $os = "os_mac";
	else if (preg_match("/Linux/", $_SERVER[HTTP_USER_AGENT])) $os = "os_linux";
	else if (preg_match("/sunOS/", $_SERVER[HTTP_USER_AGENT])) $os = "os_sun";
	else $os = "os_etc";

	# 통계값 추가
	mysqli_query($connect,"UPDATE $table_counter SET $c_time=$c_time+1, day_total=day_total+1, $browser=$browser+1, $os=$os+1 WHERE c_date='$c_date'");

	$table_referer = "koweb_statistics_refer";
	$table_referer_path = "cms_referer_path";

	### 접속경로 입력
	# 10,000개 이상일 경우 1,000개 단위로 삭제
	$referer_num = @mysqli_num_rows(mysqli_query($connect,"SELECT no FROM $table_referer"));
	if ($referer_num > 10000) {
		$refer_sql = mysqli_query($connect, "SELECT r_idx FROM $table_referer ORDER BY r_idx asc LIMIT 1000");
		while ($refer_row = mysqli_fetch_array($connect, $refer_sql)) {
			//mysqli_query($connect,"DELETE FROM $table_referer WHERE r_idx=$refer_row[r_idx]");
		}
	}

	# 경로값 추가
	$r_date = date("Y-m-d H:i:s");
	$r_year = date("Y");
	$r_month = date("m");
	$r_day = date("d");
	$r_hour = date("H");
	$r_min = date("i");
	$r_sec = date("s");
	$r_url = $_SERVER['HTTP_REFERER'];
	$r_ip = $_SERVER['REMOTE_ADDR'];
	$r_url_domain = getDomainName($r_url);

	if(!$r_url){
			$r_url = "직접 접속 및 즐겨찾기";
	}
	if(!$r_url_domain){
			$r_url_domain = "직접 접속 및 즐겨찾기";
	}
	mysqli_query($connect, "INSERT INTO $table_referer SET r_date='$r_date', year = '$r_year', month='$r_month', day = '$r_day', hour='$r_hour', min='$r_min', sec='$r_sec', r_url='$r_url', r_url_domain='$r_url_domain', r_ip='$r_ip'");
}
?>
