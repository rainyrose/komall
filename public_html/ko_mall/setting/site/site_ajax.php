<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_site_config";

	//기본정보
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_site_config WHERE lang = '$lang' ORDER BY no DESC LIMIT 1"));


	$result_array = array("title" => $default[title]
					,"lang" => $lang
					,"site_url" => $default[site_url]
					,"keyword_title" => $default[keyword_title]
					,"description" => $default[description]
					,"og_description" => $default[og_description]
					,"og_site_name" => $default[og_site_name]
					,"og_title" => $default[og_title]
			 );

	$result = json_encode($result_array);
	echo($result);
?>
