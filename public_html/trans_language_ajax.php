<? 
	$tmp = json_decode($_POST[data], true);
	
	foreach($tmp AS $tp){
		$vvv .= $tp['ttext']."|";
	}

	$site_language = $_POST[lang];
	$text = strip_tags($vvv);
	$text = substr($text, 0, -1);

	$result_array = array();


	$client_id = "bzi_c0ASJb9hM8DSomls"; // 네이버 개발자센터에서 발급받은 CLIENT ID
	$client_secret = "hZRhdUYQnK";// 네이버 개발자센터에서 발급받은 CLIENT SECRET
	$encText = urlencode($text);
	$postvars = "source=ko&target=".$site_language."&text=".$encText;
	$url = "https://openapi.naver.com/v1/papago/n2mt";
	$is_post = true;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, $is_post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
	$headers = array();
	$headers[] = "X-Naver-Client-Id: ".$client_id;
	$headers[] = "X-Naver-Client-Secret: ".$client_secret;
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$response = curl_exec ($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close ($ch);
	if($status_code == 200) {
		$rr = json_decode($response,true);

		$matching_tmp = explode("|", $rr[message][result][translatedText]);
		foreach($matching_tmp AS $key => $value){
			array_push($result_array, $tmp[$key]['ttext']."|".ltrim($value));
		}


			
		//echo $rr[message][result][translatedText];
		//print_r($result_array);

	}
	$result = json_encode($result_array);
	echo($result);

?>
