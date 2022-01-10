<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_site_config";

	//기본정보
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_mall_config WHERE lang = '$lang' ORDER BY no DESC LIMIT 1"));


	$result_array = array("company" => $default[company]
					,"lang" => $lang
					,"company_regist_no" => $default[company_regist_no]
					,"ceo_name" => $default[ceo_name]
					,"company_phone" => $default[company_phone]
					,"fax_no" => $default[fax_no]
					,"t_no" => $default[t_no]
					,"b_no" => $default[b_no]
                    ,"company_zip" => $default[company_zip]
                    ,"company_address" => $default[company_address]
                    ,"it_name" => $default[it_name]
                    ,"it_mail" => $default[it_mail]
                    ,"company_bank" => htmlspecialchars_decode($default[company_bank], ENT_NOQUOTES)
			 );

	$result = json_encode($result_array);
	echo($result);
?>
