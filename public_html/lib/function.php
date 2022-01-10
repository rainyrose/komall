<?
function load_instagram($userId, $token, $limit = 12, $paging="", $pagingEndpoint=""){
	
	if($paging == "next"){
		$ADD_URL = "&next=".$pagingEndpoint;
	} else if($paging == "prev"){
		$ADD_URL = "&prev=".$pagingEndpoint;
	} else {
		$ADD_URL = "";
	}

	$url = "https://graph.instagram.com/".$userId."/media?fields=id,media_type,media_url,permalink,thumbnail_url,username,caption&limit=".$limit."&access_token=".$token.$ADD_URL;
	exit;
	echo $url;

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$result = curl_exec($curl);
	curl_close($curl);

	$results = json_decode($result, true);
	$result = $results['data'];


	//$result[next] = $results['paging']['next'];
	$result[next] = $results['paging']['cursors']['after'];
	$result[prev] = $results['paging']['cursors']['before'];

	return $result;

}



function get_instagramLongToken($appkey, $token){

	$longToken_url = "https://graph.instagram.com/access_token?grant_type=ig_exchange_token&client_secret=".$appkey."&access_token=".$token;

	$curl = curl_init($longToken_url);
	curl_setopt($curl, CURLOPT_POST,false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$result = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($result,true);

	$timestamp = strtotime("+50 days");
	$return[Redate]= date("Y-m-d", $timestamp);
	$return[access_token] = $result[access_token];

	return $return;
}


function get_instargramToken($appid, $appkey, $uri, $token){

	$url = "https://api.instagram.com/oauth/access_token";
	$post_array = array(
		'client_id'=> $appid, 
		'client_secret'=> $appkey,
		'grant_type'=>'authorization_code',
		'redirect_uri'=> $uri,
		'code'=> $token
	);

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST,true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_array);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$result = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($result,true);
	$shortToken = $result[access_token];

	$return = get_instagramLongToken($appkey, $result[access_token]);
	$return[user_id] = $result[user_id];

	return $return;
}



function convert_dollar2($price,$transfer){
	if($price == "free" || $price == "FREE" || $price == "Free") return "free";
	return round($price/$transfer,2);
}

function convert_dollar($price){
	global $site_pay;
	if($price == "free" || $price == "FREE" || $price == "Free") return "free";
	return round($price/$site_pay[transfer],2);
}

if (!defined(__only_float)){					//숫자입력체크 스크립트
	define(__only_float, "onblur=\"this.value=this.value.replace(/[\ㄱ-ㅎㅏ-ㅣ가-힣]/g,'');if (((event.keyCode<46)||(event.keyCode>57))&&(event.keyCode!=44)) event.returnValue=false;\"onKeyPress=\"this.value=this.value.replace(/[\ㄱ-ㅎㅏ-ㅣ가-힣]/g,'');if (((event.keyCode<46)||(event.keyCode>57))&&(event.keyCode!=44)) event.returnValue=false;\" ");
}

function get_paypal_authtoken($toid){
   $license_server = 'http://master.kohub.kr/_system/paypal_license.koweb';

   $params = http_build_query(array(
	 'sysauth' => PAYPAL_CLIENT,
	 'is_mode' => IS_PAYPAL_CODE_SANDBOX,
	 'toid' => $toid
   ));

   $opts = array(
	 CURLOPT_URL => $license_server . '?' . $params,
	 CURLOPT_RETURNTRANSFER => true,
	 CURLOPT_SSL_VERIFYPEER => false,
	 CURLOPT_SSLVERSION => 1,
	 CURLOPT_HEADER => false,
	 CURLOPT_HTTPHEADER => $headers
   );

   $curl_session = curl_init();
   curl_setopt_array($curl_session, $opts);
   $return_data = curl_exec($curl_session);
   $res = json_decode($return_data,true);

   return $res;

/*
   if(!$res[access_token]){
	   echo "KOWEB PAYPAL 연동 실패";
	   exit;
   } else {
	   return $res[access_token];
   }
*/
}


function num_rows($query){
	global $connect;
	$result = mysqli_query($connect,$query);
	return mysqli_num_rows($result);
}

function fetch_array($query){
	global $connect;
	$result = mysqli_query($connect,$query);
	return mysqli_fetch_array($result);
}
function query($query){
	global $connect;
	return mysqli_query($connect,$query);
}


function print_lnb2($connect, $site_language, $mode, $ref_group, $ref_no, $depth = 1, $limit){
	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";

	$ref_no_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_menu_config WHERE menu_id = '$ref_no'"));
	$ref_no = $ref_no_[no];

	$query = "SELECT * FROM koweb_menu_config WHERE delete_state ='N' AND depth = '$depth' AND ref_group='$ref_group' AND ref_no='$ref_no' AND category = '$site_language' $ADD_USE_DEVICE ORDER BY sort ASC";
	$result = mysqli_query($connect, $query);
	while($data = mysqli_fetch_array($result)){



		//메뉴타입이 content 일때
		if($data[use_type] == "content"){
			$content = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id = '$data[content_id]' LIMIT 1"));
			if($content[content_type] == "link"){
				$ADD_LINK = $content[ref_link];
				if($content[ref_target] == "_blank"){
					$ADD_TARGET = "target='_blank'";
				} else {
					$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
					unset($ADD_TARGET);
				}
			} else {
				$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
				unset($ADD_TARGET);
			}

		//메뉴타입이 link 일때 바라보는 link_menu가 새창URL이거나 URL일때 처리
		} else {
			$link_ = mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM koweb_menu_config WHERE menu_id = '$data[link_menu_id]' LIMIT 1"));
			$link_content = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id = '$link_[content_id]' LIMIT 1"));
			if($link_content[content_type] == "link"){
				if($link_content[ref_target] == "_blank"){
					$ADD_LINK = $link_content[ref_link];
					$ADD_TARGET = "target='_blank'";
				} else {
					$ADD_LINK = "/contents/".$link_[dir]."/page.html?mid=$data[menu_id]";
					unset($ADD_TARGET);
				}
			} else {
				$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
				unset($ADD_TARGET);
			}
		}

		$min_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MIN(sort) AS min_sort FROM koweb_menu_config WHERE ref_no='$data[ref_no]' AND depth='$data[depth]' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if($data[sort] == $min_sort[min_sort] && $data[depth] != "1") echo "<ul class='lnb'>";

		if($data[state] == "Y"){
			echo "<li><a href='".$ADD_LINK."' ".$ADD_TARGET." data-set-on2='".$data[menu_id]."'>$data[menu_title]</a>";
		}

		$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM koweb_menu_config WHERE delete_state ='N' AND ref_no='$data[no]' AND $data[depth]+1 <= '$limit' $ADD_USE_DEVICE"));

		$last_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MAX(sort) AS max_sort FROM koweb_menu_config WHERE state = 'Y' AND ref_no='$ref_no' AND depth='$data[depth]' AND $data[depth] <= '$limit' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if($sub[0]){
			$depth = $data[depth] + 1;
			print_lnb($connect, $site_language, $mode, $data[ref_group], $data[menu_id], $depth, $limit);
			if($data[depth] != "1") echo "</ul>";
		} else {
			echo "</li>";
		}
		if(($last_sort[max_sort] == $data[sort]) && $data[depth] != "1") echo "</ul>";
	}
}



//계층형 네비게이션 카운트 (2차 사용하는 애가 하나라도 있는지 확인)
function return_category_count($connect, $site_language, $mode){
	if($mode == "pc") {
		$ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	} else if($mode == "mob") {
		$ADD_USE_DEVICE = "AND use_device_mob = 'Y'";
	} else {
		$ADD_USE_DEVICE  = "AND use_device_pc = 'Y' AND use_device_mob = 'Y'";
	}

	$query = mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE state = 'Y' AND delete_state != 'Y' AND depth = '1' AND category = '$site_language' $ADD_USE_DEVICE ORDER BY sort ASC, ref_group ASC, depth ASC, ref_no ASC");

	$count = false;

	while($data = mysqli_fetch_array($query)){

		$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM koweb_product_category_config WHERE state = 'Y' AND delete_state ='N' AND ref_no='$data[no]' AND $data[depth]+1 <= '2' AND depth <>1 $ADD_USE_DEVICE"));

		if($sub[0]){
			$count = true;
		}
	}

	return $count;
}


function set_cart_from_session($member_id){
	if($_SESSION['s_cart']){
		global $connect;
		$reg_date = date("Y-m-d H:i:s");
		foreach ($_SESSION['s_cart'] as $p_id => $value) {
			foreach ($value as $o_id => $value2) {
				$WHERE = " member_id='{$member_id}' AND product_id = '{$p_id}' AND option_id='{$o_id}' ";
				$query = "SELECT * FROM koweb_cart WHERE $WHERE ORDER BY no ASC";
				$result = mysqli_query($connect,$query);
				$num_row = mysqli_num_rows($result);
				if($num_row > 0){
					$row = mysqli_fetch_array($result);
					$query = "UPDATE koweb_cart SET product_cnt='{$value2['product_cnt']}' , add_option='{$value2['add_option']}' , reg_date = '{$reg_date}' WHERE $WHERE";
				}else{
					$query = "INSERT INTO koweb_cart SET
								member_id='{$member_id}' ,
								product_id='{$p_id}' ,
								option_id='{$o_id}' ,
								product_cnt='{$value2['product_cnt']}' ,
								add_option='{$value2['add_option']}' ,
								reg_date = '{$reg_date}'";
				}
				mysqli_query($connect,$query);
			}
		}
		unset($_SESSION['s_cart']);
	}
}
//회원가입 = join, 회원아이디
//주문시 고객발송 = order_user, 주문번호
//주문시 관리자발송 = order_admin, 주문번호
//입금확인시 고객발송 = check, 주문번호
//상품배송시 고객발송 = delivery, 주문번호
function trans_sms_order($connect, $order ,$str){
	global $site;
	global $site_sms;
	global $site_pay;

	if($order){
		$query = "SELECT * FROM koweb_order WHERE order_id = '$order' AND order_info = 'P'";
		$result = mysqli_query($connect, $query);
		$row = mysqli_fetch_array($result);

		//주문금액
		$price_count = mysqli_fetch_array(mysqli_query($connect, "SELECT SUM(product_price) AS total FROM koweb_order WHERE order_id = '$order'"));
		$total_price = ($price_count[total] + $row[deli_price] + $row[deli_add_price]) - $row[use_point];

		//제품명
		//일단 몇건인지 확인
		$rcount = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM koweb_order WHERE order_id = '$order'"));


		if($rcount > 1){
			$rcount_title = $row[ref_product_title] . " 외 " . intval($rcount-1) . "건 ";
		} else {
			$rcount_title = $row[ref_product_title];
		}

		$id = $row[member];
		$name = $row[name];

	} else {
		$id = $_SESSION[member_id];
		//고객정보
		$mquery = "SELECT * FROM koweb_member WHERE id='$id' LIMIT 1";
		$mrow = mysqli_fetch_array(mysqli_query($connect, $mquery));
		$name = $mrow[name];
		if(!$name) $name = "비회원";
	}



	$arr = array("{사이트명}" => $site[title], "{고객명}" => $name, "{아이디}" => $id , "{주문번호}" => $order, "{주문금액}" => $total_price, "{입금액}" => $row[pay_price], "{택배회사}" => $row[deli_company], "{운송장번호}" => $row[deli_code], "{제품명}" => $rcount_title);

	foreach($arr AS $key => $value){
		$str  = str_replace($key, $value, $str);
	}

	return $str;
}


function get_stock($connect, $data){

	$return = false;

	$product = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product WHERE id='$data'"));
	$options = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM koweb_option_set WHERE ref_product='$product[no]' AND option_type='P'"));

	//옵션사용이면?
	if($options > 0){
		//디테일옵션 다찾기
		$detail_ = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM koweb_option_detail WHERE ref_product='$product[no]' AND (stock != '0' AND soldout != 'Y') AND otype='detail' AND state='Y'"));
		//하나도없으면 품절표시
		if($detail_ <= 0){
			$return = true;
		}
	} else {
	//옵션 미사용이면?
		if($product[use_soldout] == "Y"){
			$return = true;
		} else {
			if($product[stock_count] <= 0){
				$return = true;
			}
		}
	}


	if($product[use_soldout] == "Y"){
		$return = true;
	}



	return $return;
}

function get_stock_cnt($data,$type){
	global $connect;
	if($type == "detail"){
		$product = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_option_detail WHERE id='$data'"));
		$return_cnt = $product['stock'];
	}else{
		$product = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product WHERE id='$data'"));
		$return_cnt = $product['stock_count'];
	}

	return $return_cnt;
}

function get_micreotime(){
	list($microtime,$timestamp) = explode(' ',microtime());
	$return = $timestamp.substr($microtime, 2, 3);
	$return = strrev($return);

	return $return;
}
//아이디 자동생성
function rand_guest_id(){
	global $connect;
	list($microtime,$timestamp) = explode(' ',microtime());
    $return = $timestamp.substr($microtime, 2, 3);
	$return = strrev($return);

	$return = "G_".$return;

	return $return;
}

function get_member($id){
	global $connect;
	$query = "SELECT * FROM koweb_member WHERE id='{$id}'";
	$row = mysqli_fetch_array(mysqli_query($connect,$query));
	return $row;
}

function get_add_delivery_price($connect, $data){

	$query = "SELECT * FROM koweb_member WHERE id = '$data' AND state='Y' LIMIT 1";
	$result = mysqli_query($connect, $query);
	$row = mysqli_fetch_array($result);

	//추가배송비
	$query = "SELECT * FROM koweb_add_delivery_price WHERE start_zip <= '$row[zip]' AND end_zip >= '$row[zip]' ORDER BY price DESC LIMIT 1";
	$tquery = "SELECT * FROM koweb_add_delivery_price WHERE start_zip <= '$row[zip]' AND end_zip >= '$row[zip]'";
	$result = mysqli_query($connect,$query);
	$row = mysqli_fetch_array($result);
	$check = mysqli_num_rows(mysqli_query($connect, $tquery));

	if($check != 0){
		$delivery_add = $row[price];
	} else {
		$delivery_add = 0;
	}

	return $delivery_add;

}

function get_delivery_price($connect, $data, $price){
	$data_array = array();
	if(is_array($data)){
		$data_array = $data;
	}else{
		$data_array[] = $data;
	}

	$return_price = 0;
	$delivery;
	foreach ($data_array as $key => $data) {
		// code...
		$site = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_pay_config ORDER BY no DESC LIMIT 1"));
		$product = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product WHERE id = '$data' LIMIT 1"));
		if($product[deli_type] == "fix"){
			$delivery = $product[deli_price];
		}else if($product[deli_type] == "free"){
			$delivery = 0;
		}else{
			if($site[deli_type] == "free"){

				$delivery = 0;

			} else if($site[deli_type] == "def"){

				$deli_type = explode("|", $site[deli_price_type]);
				$deli_price = explode("|", $site[deli_price]);

				foreach($deli_type AS $key => $dtype){
					if($price >= $dtype){
						$delivery = $deli_price[$key];
					}
				}
				if($delivery == "") $delivery = array_pop($deli_price);

			} else if($site[deli_type] == "fix"){

				$delivery = $site[deli_price];
			}
		}

		if($return_price <= $delivery) $return_price = $delivery;
	}
	// if($data){
	// 	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product WHERE id = '$data' LIMIT 1"));
	// 	if($default != "fix" && $default != "free"){
	// 		$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_pay_config ORDER BY no DESC LIMIT 1"));
	// 	}
	// } else{
	// 	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_pay_config ORDER BY no DESC LIMIT 1"));
	// }
	//
	// if($default[deli_type] == "free"){
	//
	// 	$delivery = 0;
	//
	// } else if($default[deli_type] == "def"){
	//
	// 	$deli_type = explode("|", $default[deli_price_type]);
	// 	$deli_price = explode("|", $default[deli_price]);
	//
	// 	foreach($deli_type AS $key => $dtype){
	// 		if($price >= $dtype){
	// 			$delivery = $deli_price[$key];
	// 		}
	// 	}
	// 	if($delivery == "") $delivery = array_pop($deli_price);
	//
	// } else if($default[deli_type] == "fix"){
	//
	// 	$delivery = $default[deli_price];
	// }

	return $return_price;
}

//계층형 네비게이션 (쿼리수정)
function load_category($connect, $site_language, $mode, $limit_depth){
	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";

	$query = mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE state = 'Y' AND delete_state != 'Y' AND depth = '1' AND category = '$site_language' $ADD_USE_DEVICE ORDER BY sort ASC, ref_group ASC, depth ASC, ref_no ASC");

	while($data = mysqli_fetch_array($query)){
		if($data[no] == $no){
			$depth_no = $data[depth];
		}
		category($connect, $mode, $data[ref_group], $data[ref_no], '1', $limit_depth);
	}
}

//계층형 네비게이션 (쿼리수정)
function load_category2($connect, $site_language, $mode, $limit_depth){
	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";

	$query = mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE state = 'Y' AND delete_state != 'Y' AND depth = '1' AND category = '$site_language' $ADD_USE_DEVICE ORDER BY sort ASC, ref_group ASC, depth ASC, ref_no ASC");

	while($data = mysqli_fetch_array($query)){
		if($data[no] == $no){
			$depth_no = $data[depth];
		}
		category2($connect, $mode, $data[ref_group], $data[ref_no], '1', $limit_depth);
	}
}

//계층형 네비게이션 (쿼리수정)
function load_category_mob($connect, $site_language, $mode, $limit_depth, $category){
	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";

	if($category){
		$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE id = '$category'"));
		if($limit_depth != "1"){
			$query = mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE state = 'Y' AND delete_state != 'Y' AND ref_no = '$default[no]' AND depth != '$default[depth]' $ADD_USE_DEVICE ORDER BY sort ASC, ref_group ASC, depth ASC, ref_no ASC");
		} else {
			$query = mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE state = 'Y' AND delete_state != 'Y' AND ref_no != '$default[no]' AND depth = '$default[depth]' $ADD_USE_DEVICE ORDER BY sort ASC, ref_group ASC, depth ASC, ref_no ASC");
		}
	} else {
		$query = mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE state = 'Y' AND delete_state != 'Y' AND depth = '1' AND category = '$site_language' $ADD_USE_DEVICE ORDER BY sort ASC, ref_group ASC, depth ASC, ref_no ASC");
	}

	while($data = mysqli_fetch_array($query)){
		$ADD_LINK = "/product/product.html" . "?category=".$data[id];
		$ADD_TARGET = "";
		echo "<li><a href='".$ADD_LINK."' ".$ADD_TARGET." data-set-on='".$data[id]."'>$data[title]</a></li> ";
	}
}


function category($connect, $mode, $ref_group, $ref_no, $depth = 1, $limit){

	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";
	global $mid;

	$query = "SELECT * FROM koweb_product_category_config WHERE state = 'Y' AND delete_state ='N' AND depth = '$depth' AND ref_group='$ref_group' AND ref_no='$ref_no' $ADD_USE_DEVICE ORDER BY sort ASC";

	$result = mysqli_query($connect, $query);

	while($data = mysqli_fetch_array($result)){

		$ADD_LINK = "/product/product.html" . "?category=".$data[id];
		$ADD_TARGET = "";

		$min_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MIN(sort) AS min_sort FROM koweb_product_category_config WHERE ref_no='$data[ref_no]' AND depth='$data[depth]' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if(($data[sort] == $min_sort[min_sort]) && $data[depth] != "1") echo "<ul>";

		echo "<li><a href='".$ADD_LINK."' ".$ADD_TARGET." data-set-on='".$data[id]."'>".htmlspecialchars_decode($data[title])."</a>";

		$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM koweb_product_category_config WHERE state = 'Y' AND delete_state ='N' AND ref_no='$data[no]' AND $data[depth]+1 <= '$limit' $ADD_USE_DEVICE"));

		$last_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MAX(sort) AS max_sort FROM koweb_product_category_config WHERE state = 'Y' AND ref_no='$ref_no' AND depth='$data[depth]' AND $data[depth]+1 <= '$limit' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if($sub[0]){
			$depth = $data[depth] + 1;
			category($connect, $mode, $data[ref_group], $data[no], $depth, $limit);
			if($data[depth] != $depth) echo "</ul>";
		} else {
			echo "</li>";
		}
	}
}

function category2($connect, $mode, $ref_group, $ref_no, $depth = 1, $limit){

	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";
	global $mid;

	$query = "SELECT * FROM koweb_product_category_config WHERE state = 'Y' AND delete_state ='N' AND depth = '$depth' AND ref_group='$ref_group' AND ref_no='$ref_no' $ADD_USE_DEVICE ORDER BY sort ASC";

	$result = mysqli_query($connect, $query);

	while($data = mysqli_fetch_array($result)){

		$add_folder = "";
		if($data[category] != "default"){
			$add_folder = $data[category]."/";
		}

		$ADD_LINK = "/".$add_folder."product/product.html" . "?category=".$data[id];
		$ADD_TARGET = "";

		$min_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MIN(sort) AS min_sort FROM koweb_product_category_config WHERE ref_no='$data[ref_no]' AND depth='$data[depth]' AND state='Y' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if(($data[sort] == $min_sort[min_sort]) && $data[depth] != "1") echo "<ul>";

		echo "<li><a href='".$ADD_LINK."' ".$ADD_TARGET." data-set-on='".$data[id]."'>".htmlspecialchars_decode($data[title])."</a>";

		$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM koweb_product_category_config WHERE state = 'Y' AND delete_state ='N' AND ref_no='$data[no]' AND $data[depth]+1 <= '$limit' AND depth <>1 $ADD_USE_DEVICE"));

		$last_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MAX(sort) AS max_sort FROM koweb_product_category_config WHERE ref_no='$ref_no' AND depth='$data[depth]' AND $data[depth]+1 <= '$limit' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if($sub[0]){
			$depth = $data[depth] + 1;
			category2($connect, $mode, $data[ref_group], $data[no], $depth, $limit);
			if($data[depth] != $depth) echo "</ul>";
		}
		echo "</li>";
	}
}


//카테고리 네비게이션
function print_navi_($connect, $str){


	$str = explode("|", substr($str, 0, -1));
	$before = "";
	foreach($str AS $str){
		$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE id = '$str'"));
		$load_ = mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE state = 'Y' AND ref_no = '$default[no]' ORDER BY sort ASC");

		$load_check_r = mysqli_num_rows($load_);
		if($load_check_r > 0){
			if(!$before) $before = $default[no];
			echo " <select name=\"\" id=\"\" data-set-pcate data-cate=\"set-pcate\">";
			echo "<option value=\"\">선택해주세요</option>";
			$load_category_result = mysqli_query($connect, "SELECT * FROM koweb_product_category_config WHERE state = 'Y' AND ref_group = '$default[ref_group]' AND depth = '$default[depth]' AND ref_no = '$before' ORDER BY sort ASC");
			while($load_category = mysqli_fetch_array($load_category_result)){
			?>
				<option value="<?=$load_category[id]?>" <? if($str == $load_category[id]) echo "selected"; ?>><?=$load_category[title]?></option>";
			<?
			}
			echo "</select>";
			$before = $default[no];
		}
	}

	return $before;
}

//아이디 자동생성
function rand_id($setting){
	$return = mt_rand(0, 21470714);
	$find = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting WHERE id = '$return'"));
	if($find > 0){
		$return = rand_id($setting);
	}

	return $return;
}

function recurse_delete_dir(string $dir) : int {
    $count = 0;

    // ensure that $dir ends with a slash so that we can concatenate it with the filenames directly
    $dir = rtrim($dir, "/\\") . "/";

    // use dir() to list files
    $list = dir($dir);

    // store the next file name to $file. if $file is false, that's all -- end the loop.
    while(($file = $list->read()) !== false) {
        if($file === "." || $file === "..") continue;
        if(is_file($dir . $file)) {
            unlink($dir . $file);
            $count++;
        } elseif(is_dir($dir . $file)) {
            $count += recurse_delete_dir($dir . $file);
        }
    }

    // finally, safe to delete directory!
    rmdir($dir);

    return $count;
}
function sendMail($to_name, $to_mail, $title, $content) {

	$nameFrom = "koweb";
	$mailFrom = $to_mail;
	$nameTo = $to_name;
	$mailTo = $to_mail;
	//$cc = "참조";
	//$bcc = "숨은참조";
	$subject = $title;
	$content = $content;

	$charset = "UTF-8";
	$nameFrom = "=?$charset?B?".base64_encode($nameFrom)."?=";
	$nameTo = "=?$charset?B?".base64_encode($nameTo)."?=";
	$subject = "=?$charset?B?".base64_encode($subject)."?=";

	$header = "Content-Type: text/html; charset=utf-8\r\n";
	$header .= "MIME-Version: 1.0\r\n";

	$header .= "Return-Path: <". $mailFrom .">\r\n";
	$header .= "From: ". $nameFrom ." <". $mailFrom .">\r\n";
	$header .= "Reply-To: <". $mailFrom .">\r\n";
	if ($cc) $header .= "Cc: ". $cc ."\r\n";
	if ($bcc) $header .= "Bcc: ". $bcc ."\r\n";

	$result = mail($mailTo, $subject, $content, $header, $mailFrom);
}


function GETmake_board_($connect){

	$return = array();
	$config_q = "SELECT * FROM koweb_board_config ORDER BY no DESC";
	$config_r = mysqli_query($connect, $config_q);
	$count = 0;
	while($config_ = mysqli_fetch_array($config_r)){
		//$return = array("title" => $config_[title], "id" => $config_[id]);
		$return[$config_[id]][title] = $config_[title];
		$return[$config_[id]][id] = $config_[id];
		$count++;
	}

	return $return;
}

function GETmake_form_action($str){
	//ex str = /index.html?mode=write&mid=$mid
	$str_tmp1 = explode("?", $str);

	//print_r($str_tmp1);
	$to_continue = strpos($str_tmp1[1], "&") ? true : false;

	if(!$to_continue) {
		$str_tmp2 = explode("=", $str_tmp1[1]);
		echo "<input type='hidden' name='".$str_tmp2[0]."' value='".$str_tmp2[1]."' />";
	} else {
		$str_tmp2 = explode("&", $str_tmp1[1]);
		foreach($str_tmp2 as $v){
			$v_tmp = explode("=", $v);
			echo "<input type='hidden' name='".$v_tmp[0]."' value='".$v_tmp[1]."' />";
		}
	}
}

//계층형 네비게이션 (쿼리수정)
function load_menu($connect, $site_language, $mode, $limit_depth){
	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";

	$query = mysqli_query($connect, "SELECT * FROM koweb_menu_config WHERE state = 'Y' AND delete_state != 'Y' AND depth = '1' AND category = '$site_language' $ADD_USE_DEVICE ORDER BY sort ASC, ref_group ASC, depth ASC, ref_no ASC");

	while($data = mysqli_fetch_array($query)){
		if($data[no] == $no){
			$depth_no = $data[depth];
		}
		menu($connect, $mode, $data[ref_group], $data[ref_no], '1', $limit_depth);
	}
}

//계층형 네비게이션 (쿼리수정)
function load_menu2($connect, $site_language, $mode, $limit_depth){
	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";

	$query = mysqli_query($connect, "SELECT * FROM koweb_menu_config WHERE state = 'Y' AND delete_state != 'Y' AND depth = '1' AND category = '$site_language' $ADD_USE_DEVICE ORDER BY sort ASC, ref_group ASC, depth ASC, ref_no ASC");

	while($data = mysqli_fetch_array($query)){
		if($data[no] == $no){
			$depth_no = $data[depth];
		}
		menu2($connect, $mode, $data[ref_group], $data[ref_no], '1', $limit_depth);
	}
}

function print_lnb($connect, $site_language, $mode, $ref_group, $ref_no, $depth = 1, $limit){
	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";

	$ref_no_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_menu_config WHERE menu_id = '$ref_no'"));
	$ref_no = $ref_no_[no];

	$query = "SELECT * FROM koweb_menu_config WHERE delete_state ='N' AND depth = '$depth' AND ref_group='$ref_group' AND ref_no='$ref_no' AND category = '$site_language' $ADD_USE_DEVICE ORDER BY sort ASC";
	$result = mysqli_query($connect, $query);
	while($data = mysqli_fetch_array($result)){



		//메뉴타입이 content 일때
		if($data[use_type] == "content"){
			$content = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id = '$data[content_id]' LIMIT 1"));
			if($content[content_type] == "link"){
				$ADD_LINK = $content[ref_link];
				if($content[ref_target] == "_blank"){
					$ADD_TARGET = "target='_blank'";
				} else {
					$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
					unset($ADD_TARGET);
				}
			} else {
				$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
				unset($ADD_TARGET);
			}

		//메뉴타입이 link 일때 바라보는 link_menu가 새창URL이거나 URL일때 처리
		} else {
			$link_ = mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM koweb_menu_config WHERE menu_id = '$data[link_menu_id]' LIMIT 1"));
			$link_content = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id = '$link_[content_id]' LIMIT 1"));
			if($link_content[content_type] == "link"){
				if($link_content[ref_target] == "_blank"){
					$ADD_LINK = $link_content[ref_link];
					$ADD_TARGET = "target='_blank'";
				} else {
					$ADD_LINK = "/contents/".$link_[dir]."/page.html?mid=$data[menu_id]";
					unset($ADD_TARGET);
				}
			} else {
				$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
				unset($ADD_TARGET);
			}
		}

		$min_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MIN(sort) AS min_sort FROM koweb_menu_config WHERE ref_no='$data[ref_no]' AND depth='$data[depth]' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if($data[sort] == $min_sort[min_sort] && $data[depth] != "1") echo "<ul>";

		if($data[state] == "Y"){
			echo "<li><a href='".$ADD_LINK."' ".$ADD_TARGET." data-set-on='".$data[menu_id]."'>$data[menu_title]</a>";
		}

		$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM koweb_menu_config WHERE delete_state ='N' AND ref_no='$data[no]' AND $data[depth]+1 <= '$limit' $ADD_USE_DEVICE"));

		$last_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MAX(sort) AS max_sort FROM koweb_menu_config WHERE state = 'Y' AND ref_no='$ref_no' AND depth='$data[depth]' AND $data[depth] <= '$limit' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if($sub[0]){
			$depth = $data[depth] + 1;
			print_lnb($connect, $site_language, $mode, $data[ref_group], $data[menu_id], $depth, $limit);
			if($data[depth] != "1") echo "</ul>";
		} else {
			echo "</li>";
		}
		if(($last_sort[max_sort] == $data[sort]) && $data[depth] != "1") echo "</ul>";
	}
}


function menu($connect, $mode, $ref_group, $ref_no, $depth = 1, $limit){

	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";

	$query = "SELECT * FROM koweb_menu_config WHERE state = 'Y' AND delete_state ='N' AND depth = '$depth' AND ref_group='$ref_group' AND ref_no='$ref_no' $ADD_USE_DEVICE ORDER BY sort ASC";
	$result = mysqli_query($connect, $query);
	while($data = mysqli_fetch_array($result)){
		//메뉴타입이 content 일때
		if($data[use_type] == "content"){
			$content = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id = '$data[content_id]' LIMIT 1"));
			if($content[content_type] == "link"){
				$ADD_LINK = $content[ref_link];
				if($content[ref_target] == "_blank"){
					$ADD_TARGET = "target='_blank'";
				} else {
					$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
					unset($ADD_TARGET);
				}
			} else {
				$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
				unset($ADD_TARGET);
			}

		//메뉴타입이 link 일때 바라보는 link_menu가 새창URL이거나 URL일때 처리
		} else {
			$link_ = mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM koweb_menu_config WHERE menu_id = '$data[link_menu_id]' LIMIT 1"));
			$link_content = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id = '$link_[content_id]' LIMIT 1"));
			if($link_content[content_type] == "link"){
				if($link_content[ref_target] == "_blank"){
					$ADD_LINK = $link_content[ref_link];
					$ADD_TARGET = "target='_blank'";
				} else {
					$ADD_LINK = "/contents/".$link_[dir]."/page.html?mid=$data[menu_id]";
					unset($ADD_TARGET);
				}
			} else {
				$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
				unset($ADD_TARGET);
			}
		}

		$min_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MIN(sort) AS min_sort FROM koweb_menu_config WHERE ref_no='$data[ref_no]' AND depth='$data[depth]' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if($data[sort] == $min_sort[min_sort] && $data[depth] != "1") echo "<ul>";

		echo "<li><a href='".$ADD_LINK."' ".$ADD_TARGET." data-set-on='".$data[menu_id]."'>$data[menu_title]</a>";

		$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM koweb_menu_config WHERE state = 'Y' AND delete_state ='N' AND ref_no='$data[no]' AND $data[depth]+1 <= '$limit' $ADD_USE_DEVICE"));
		$last_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MAX(sort) AS max_sort FROM koweb_menu_config WHERE state = 'Y' AND ref_no='$ref_no' AND depth='$data[depth]' AND $data[depth]+1 <= '$limit' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if($sub[0]){
			$depth = $data[depth] + 1;
			menu($connect, $mode, $data[ref_group], $data[no], $depth, $limit);
			if($data[depth] != "1") echo "</ul>";
		} else {
			echo "</li>";
		}
		if(($last_sort[max_sort] == $data[sort]) && $data[depth] != "1") echo "</ul>";

	}
}

function menu2($connect, $mode, $ref_group, $ref_no, $depth = 1, $limit){

	if($mode == "pc") $ADD_USE_DEVICE = "AND use_device_pc = 'Y'";
	else $ADD_USE_DEVICE = "AND use_device_mob = 'Y'";

	$query = "SELECT * FROM koweb_menu_config WHERE state = 'Y' AND delete_state ='N' AND depth = '$depth' AND ref_group='$ref_group' AND ref_no='$ref_no' $ADD_USE_DEVICE ORDER BY sort ASC";
	$result = mysqli_query($connect, $query);
	while($data = mysqli_fetch_array($result)){
		//메뉴타입이 content 일때
		if($data[use_type] == "content"){
			$content = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id = '$data[content_id]' LIMIT 1"));
			if($content[content_type] == "link"){
				$ADD_LINK = $content[ref_link];
				if($content[ref_target] == "_blank"){
					$ADD_TARGET = "target='_blank'";
				} else {
					$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
					unset($ADD_TARGET);
				}
			} else {
				$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
				unset($ADD_TARGET);
			}

		//메뉴타입이 link 일때 바라보는 link_menu가 새창URL이거나 URL일때 처리
		} else {
			$link_ = mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM koweb_menu_config WHERE menu_id = '$data[link_menu_id]' LIMIT 1"));
			$link_content = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id = '$link_[content_id]' LIMIT 1"));
			if($link_content[content_type] == "link"){
				if($link_content[ref_target] == "_blank"){
					$ADD_LINK = $link_content[ref_link];
					$ADD_TARGET = "target='_blank'";
				} else {
					$ADD_LINK = "/contents/".$link_[dir]."/page.html?mid=$data[menu_id]";
					unset($ADD_TARGET);
				}
			} else {
				$ADD_LINK = "/contents/".$data[dir]."/page.html?mid=$data[menu_id]";
				unset($ADD_TARGET);
			}
		}

		$min_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MIN(sort) AS min_sort FROM koweb_menu_config WHERE ref_no='$data[ref_no]' AND depth='$data[depth]' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if($data[sort] == $min_sort[min_sort] && $data[depth] != "1") echo "<ul class='sub_menu'>";

		echo "<li><a href='".$ADD_LINK."' ".$ADD_TARGET." data-set-on='".$data[menu_id]."'>$data[menu_title]</a>";

		$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM koweb_menu_config WHERE state = 'Y' AND delete_state ='N' AND ref_no='$data[no]' AND $data[depth]+1 <= '$limit' $ADD_USE_DEVICE"));
		$last_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MAX(sort) AS max_sort FROM koweb_menu_config WHERE state = 'Y' AND ref_no='$ref_no' AND depth='$data[depth]' AND $data[depth]+1 <= '$limit' AND delete_state != 'Y' $ADD_USE_DEVICE"));

		if($sub[0]){
			$depth = $data[depth] + 1;
			menu2($connect, $mode, $data[ref_group], $data[no], $depth, $limit);
			if($data[depth] != "1") echo "</ul>";
		} else {
			echo "</li>";
		}
		if(($last_sort[max_sort] == $data[sort]) && $data[depth] != "1") echo "</ul>";

	}
}

function print_category($connect, $ref_group, $ref_no, $depth = 1){
	$query = "SELECT * FROM koweb_dept WHERE state = 'Y' AND depth = '$depth' AND ref_group='$ref_group' AND ref_no='$ref_no' AND state='Y' ORDER BY sort ASC";
	$result = mysqli_query($connect, $query);
	while($data = mysqli_fetch_array($result)){
		$position = "style='padding-left:" . ($data[depth]-1) * 105 . "px;'";
		$position2 = "";
		if($data[depth] != "1"){
			//$position2 = "<strong>└</strong> ";
		} else {
			$position = "style='font-weight:bold'";
		}

		$count_query = "SELECT * FROM koweb_dept WHERE depth_history LIKE '%$data[no]%'";
		$count_result = mysqli_query($connect,$count_query);
		$member_count = 0;
		while($count = mysqli_fetch_array($count_result)){
			//$member_query = "SELECT count(*) as CNT FROM koweb_member WHERE dept='$count[no]'";
			//$tmp_member_count = mysqli_fetch_array(mysqli_query($member_query));
			//$member_count += number_format($tmp_member_count[CNT]);
		}
		$add_button = "(".number_format($member_count).")";

	?>
		<tr class="<?=$folding?>">
			<td  <?=$position?>><a href="?type=setting&core_id=setting&core=manager_setting&manager_type=dept&mode=view&no=<?=$data[no]?>"><?=$position2?><?=$data[dept]?>
				<?=$add_button?></a>
			</td>
			<td class="tac">
				<a href="?type=setting&core_id=setting&core=manager_setting&manager_type=dept&mode=sort&no=<?=$data[no]?>&sort_mode=up" class="sm button white"/>△</a>
				<a href="?type=setting&core_id=setting&core=manager_setting&manager_type=dept&mode=sort&no=<?=$data[no]?>&sort_mode=down" class="sm button gray"/>▽<a/>
			</td>
			<td class="tac">
				<a href="?type=setting&manager_type=dept&mode=modify&core=manager_setting&no=<?=$data[no]?>" class="sm button"/>수정</a>
				<a href="?type=setting&manager_type=dept&mode=delete&core=manager_setting&no=<?=$data[no]?>" class="sm button"/>삭제</a>
			</td>
		</tr>
	<?
		$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM koweb_dept WHERE ref_no='$data[no]'"), 0);
		if($sub[0]){
			$depth = $data[depth] + 1;
			print_category($connect, $data[ref_group], $data[no], $depth);
		}
	}
}
function auth_checked($connect, $type, $auth_type, $auth_id, $auth_string){
	$return_;

	switch($type){
		case "check_level" :
		case "check_dept" :
		case "check_user" :
			$use_database = "koweb_auth_config";
			$item1 = "auth_id";
			if($type == "check_level") {
				$item2 = "allow_level";
			}
			if($type == "check_dept") {
				$item2 = "allow_dept";
			}
			if($type == "check_user") {
				$item2 = "allow_user";
				$member_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_member WHERE id='$auth_string'"));
				$auth_string = $member_[no];
			}

			$ADD_ = "AND auth_id='$auth_id'";
			if($auth_id == "administrator"){
				$ADD_ = "";
			}

			$denied_query = "SELECT * FROM $use_database WHERE $item2 LIKE CONCAT('%|".$auth_string."|%') AND auth_type = '$auth_type' $ADD_";
			$denied_result = mysqli_query($connect, $denied_query);
			$denied = mysqli_num_rows($denied_result);
		break;
	}
	if($denied > 0){
		$return_ = true;
	} else {
		$return_ = false;
	}

	if($_SESSION['member_id'] == "koweb" || $_SESSION['member_id'] == "admin" || $_SESSION['member_id'] == "koweb_pm") $return_ = true;

	return $return_;


}

function denied_auth($connect, $type, $auth_string, bool $v){

	$return_;

	if(!$v) $ADD_ = "NOT";
	else unset($ADD_);

	switch($type){
		case "allow_level" :
			$use_database = "koweb_member_level";
			$item1 = "target.level";
			$item2 = "target.level_title";
			$WHERE = "AND admin_auth = 'ADMIN'";
			$WHERE .= "AND target.level != '1'";

			break;
		case "allow_dept" :
			$use_database = "koweb_dept";
			$item1 = "target.no";
			$item2 = "target.dept";
			$WHERE = "";
			break;
		case "allow_user" :
			$use_database = "koweb_member";
			$item1 = "target.no";
			$item2 = "target.name";
			$WHERE = "AND is_admin = 'Y' ";
			$WHERE .= "AND id != 'koweb'";
			break;
	}

	$denied_query = "SELECT $item1, $item2 FROM $use_database AS target WHERE '$auth_string' $ADD_ LIKE CONCAT('%|', $item1 ,'|%') $WHERE ORDER BY $item1 ASC";

	$denied_result = mysqli_query($connect, $denied_query);
	while($denied = mysqli_fetch_row($denied_result)){
		$return_ .= "<li data-info=\"$denied[0]\" data-info-type=\"$type\">$denied[1]</li>";
	}
	return $return_;
}

function print_dept($connect, $tables, $ref_group, $ref_no, $depth = 1){
	$query = "SELECT * FROM $tables WHERE 1=1 AND depth = '$depth' AND ref_group='$ref_group' AND ref_no='$ref_no' AND state = 'Y' ORDER BY sort ASC";
	$result = mysqli_query($connect, $query);
	while($data = mysqli_fetch_array($result)){
		if($data[depth] > 1 ) {
			$depth_data =  "data-add-menu=\"depth0".($data[depth]-1)."\"";
			$depth_img = "<i class=\"depth\"><img src=\"/ko_mall/images/content/icon_depth.png\"/></i>";
		} else {
			unset($depth_data);
			unset($depth_img);
		}
	?>
		<tr <?=$depth_data?>>
			<td data-dept-info="<?=$data[no]?>" data-add-menu="name" <?=$state_line?>><?=$depth_img?><span data-ori-title><?=$data[dept]?></span></td>
			<td data-button-area="<?=$data[no]?>">
				<a href="javascript:;" onclick="javascript:dept_and_setup(popLayer01, <?=$data[no]?>, <?=$data[ref_group]?>, '<?=$data[dept]?>');" class="button sm blue">하위부서등록</a>
				<a href="#" class="button sm gray" data-dept-button="modify">수정</a>
				<a href="#" class="button sm white" data-dept-button="delete">삭제</a>
			</td>
		</tr>
	<?
		$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM $tables WHERE ref_no='$data[no]' AND state = 'Y'"));
		if($sub[0] > 0){
			$depth = $data[depth] + 1;
			print_dept($connect, $tables, $data[ref_group], $data[no], $depth);
		}
	}
}


function print_menu($connect, $category, $mode, $tables, $ref_group, $ref_no, $depth = 1){
	$query = "SELECT * FROM $tables WHERE 1=1 AND depth = '$depth' AND ref_group='$ref_group' AND ref_no='$ref_no' AND delete_state != 'Y' AND category = '$category' ORDER BY sort ASC";
	$result = mysqli_query($connect, $query);

	///////////////////////////////////////////////////while 시작 ///////////////////////////////////////////////////
	while($data = mysqli_fetch_array($result)){


		if($data[use_device_pc] == "Y") $device_pc = "<i class=\"web\">web</i>";
		else unset($device_pc);

		if($data[use_device_mob] == "Y") $device_mob = "<i class=\"mob\">mobile</i>";
		else unset($device_mob);

		if($data[state] != "Y") $state_line = "style=\"text-decoration:line-through; color:red;\"";
		else unset($state_line);

		if($data[use_type] == "content"){
			if(!$data[content_id]) {
				$menu_find = "<i class=\"nocon\"><span>연결된 컨텐츠가 없습니다</span></i>";
			} else {
				$con_checker = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_content_config WHERE content_id='$data[content_id]'"));

				if($con_checker[content_type] != "contents" && $con_checker[content_type] != "link"){
					if(!$con_checker["ref_".$con_checker[content_type]]){
						if($con_checker[content_type] == "board") $tx = "게시판";
						else if($con_checker[content_type] == "program") $tx = "프로그램";
						else if($con_checker[content_type] == "online") $tx = "온라인 신청프로그램";
						else unset($tx);
						$menu_find = "<i class=\"nocon\"><span>연결된 컨텐츠의 ".$tx."이 없습니다</span></i>";
					} else {
						unset($menu_find);
					}
				} else {
					unset($menu_find);
				}
			}
		} else {
			if(!$data[link_menu_id]) {
				$menu_find = "<i class=\"nocon\"><span>연결된 컨텐츠가 없습니다</span></i>";
			} else {
				unset($menu_find);
			}

		}

		//////////////////////////////////////////////////// 트리형 ///////////////////////////////////////////////////////
		if($mode == "tree"){

			$min_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MIN(sort) AS min_sort FROM $tables WHERE ref_no='$ref_no' AND depth='$data[depth]' AND category = '$category' AND delete_state != 'Y'"));

			if($data[sort] == $min_sort[min_sort] && $data[depth] != "1") echo "<ul>";
	?>
			<li><a href="#" class="tree">열기</a><a href="#" data-menu-set="<?=$data[menu_id]?>" data-content-set="<?=$data[content_id]?>" data-menu-type="<?=$data[use_type]?>" data-menu-info="<?=$data[menu_id]?>" data-content-info="<?=$data[content_id]?>" <?=$state_line?>><?=htmlspecialchars_decode($data[menu_title])?><?=$device_pc?><?=$device_mob?> <?=$menu_find?></a>
	<?
			$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM $tables WHERE ref_no='$data[no]' AND no != '$data[no]' AND delete_state != 'Y' AND category = '$category'"));
			$last_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MAX(sort) AS max_sort FROM $tables WHERE state = 'Y' AND ref_no='$ref_no' AND depth='$data[depth]' AND delete_state != 'Y' AND category = '$category'"));
			if($sub[0]){
				$depth = $data[depth] + 1;
				print_menu($connect, $category, $mode, $tables, $data[ref_group], $data[no], $depth);
			} else {
				echo "</li>";
			}

			if(($last_sort[max_sort] == $data[sort]) && $data[depth] != "1") echo "</ul>";

			//if($data[sort] != "1" && $depth != "1") echo "</li>";

		//////////////////////////////////////////////////// 리스트형 ///////////////////////////////////////////////////////
		} else if($mode == "list"){
			if($data[depth] > 1 ) {
				$depth_data =  "data-add-menu=\"depth0".($data[depth]-1)."\"";
				$depth_img = "<i class=\"depth\"><img src=\"/ko_mall/images/content/icon_depth.png\"/></i>";
			} else {
				unset($depth_data);
				unset($depth_img);
			}
	?>
			<tr <?=$depth_data?>>
				<td data-add-menu="name" <?=$state_line?>><?=$depth_img?><?=$data[menu_title]?></td>
				<td>
					<a href="#" data-menu-sort="up" data-menu-setvalue="<?=$data[no]?>" class="btn_up">순서위로</a>
					<a href="#" data-menu-sort="down" data-menu-setvalue="<?=$data[no]?>" class="btn_down">순서아래로</a>
				</td>
				<td><a href="javascript:;" onclick="javascript:show_and_setup(popLayer01, <?=$data[no]?>, <?=$data[ref_group]?>, '<?=$data[menu_title]?>');" class="button blue">하위등록</a></td>
			</tr>
	<?
			$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM $tables WHERE ref_no='$data[no]' AND delete_state != 'Y' AND category = '$category'"));
			if($sub[0]){
				$depth = $data[depth] + 1;
				print_menu($connect, $category, $mode, $tables, $data[ref_group], $data[no], $depth);
			}
		///////////////////////////////////////////////////while 종료 ///////////////////////////////////////////////////
		} else if($mode == "small_list"){
			if($data[depth] > 1 ) {
					$depth_data =  "data-add-menu=\"depth0".($data[depth]-1)."\"";
					$depth_img = "<i class=\"depth\"><img src=\"/ko_mall/images/content/icon_depth.png\"/></i>";
				} else {
					unset($depth_data);
					unset($depth_img);
				}
		?>
			<tr <?=$depth_data?>>
				<td data-add-menu="name"><?=$depth_img?> <?=$data[menu_title]?></td>
				<td><a href="#" class="button sm gray btn_close" data-menu-select="<?=$data[menu_id]?>" data-menu-title="<?=$data[menu_title]?>">선택</a></td>
			</tr>
	<?
			$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM $tables WHERE ref_no='$data[no]' AND delete_state != 'Y'"));
			if($sub[0]){
				$depth = $data[depth] + 1;
				print_menu($connect, $category, $mode, $tables, $data[ref_group], $data[no], $depth);
			}
		}
	}
}



function print_product_category($connect, $category, $mode, $tables, $ref_group, $ref_no, $depth = 1){
	$query = "SELECT * FROM $tables WHERE 1=1 AND depth = '$depth' AND ref_group='$ref_group' AND ref_no='$ref_no' AND delete_state != 'Y' AND category = '$category' ORDER BY sort ASC";
	$result = mysqli_query($connect, $query);

	///////////////////////////////////////////////////while 시작 ///////////////////////////////////////////////////
	while($data = mysqli_fetch_array($result)){

		if($data[use_device_pc] == "Y") $device_pc = "<i class=\"web\">web</i>";
		else unset($device_pc);

		if($data[use_device_mob] == "Y") $device_mob = "<i class=\"mob\">mobile</i>";
		else unset($device_mob);

		if($data[state] != "Y") $state_line = "style=\"text-decoration:line-through; color:red;\"";
		else unset($state_line);

		//////////////////////////////////////////////////// 트리형 ///////////////////////////////////////////////////////
		if($mode == "tree"){

			$min_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MIN(sort) AS min_sort FROM $tables WHERE ref_no='$ref_no' AND depth='$data[depth]' AND category = '$category' AND delete_state != 'Y'"));

			if($data[sort] == $min_sort[min_sort] && $data[depth] != "1") echo "<ul>";
	?>
			<li><a href="#" class="tree">열기</a><a href="#" data-pcate-set="<?=$data[id]?>" data-pcate-type="<?=$data[use_type]?>" data-pcate-info="<?=$data[id]?>" <?=$state_line?>><?=htmlspecialchars_decode($data[title])?> <?=$menu_find?></a>
	<?
			$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM $tables WHERE ref_no='$data[no]' AND no != '$data[no]' AND delete_state != 'Y' AND category = '$category'"));
			$last_sort = mysqli_fetch_array(mysqli_query($connect, "SELECT MAX(sort) AS max_sort FROM $tables WHERE 1=1 AND ref_no='$ref_no' AND depth='$data[depth]' AND delete_state != 'Y' AND category = '$category'"));
			if($sub[0]){
				$depth = $data[depth] + 1;
				print_product_category($connect, $category, $mode, $tables, $data[ref_group], $data[no], $depth);
			} else {
				echo "</li>";
			}

			if(($last_sort[max_sort] == $data[sort]) && $data[depth] != "1") echo "</ul>";

			//if($data[sort] != "1" && $depth != "1") echo "</li>";

		//////////////////////////////////////////////////// 리스트형 ///////////////////////////////////////////////////////
		} else if($mode == "list"){
			if($data[depth] > 1 ) {
				$depth_data =  "data-add-menu=\"depth0".($data[depth]-1)."\"";
				$depth_img = "<i class=\"depth\"><img src=\"/ko_mall/images/content/icon_depth.png\"/></i>";
			} else {
				unset($depth_data);
				unset($depth_img);
			}
	?>
			<tr <?=$depth_data?>>
				<td data-add-menu="name" <?=$state_line?>><?=$depth_img?><?=$data[title]?></td>
				<td>
					<a href="#" data-pcate-sort="up" data-pcate-setvalue="<?=$data[no]?>" class="btn_up">순서위로</a>
					<a href="#" data-pcate-sort="down" data-pcate-setvalue="<?=$data[no]?>" class="btn_down">순서아래로</a>
				</td>
				<td><a href="javascript:;" onclick="javascript:show_and_setup(popLayer01, <?=$data[no]?>, <?=$data[ref_group]?>, '<?=$data[title]?>');" class="button blue">하위등록</a></td>
			</tr>
	<?
			$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM $tables WHERE ref_no='$data[no]' AND delete_state != 'Y' AND category = '$category'"));
			if($sub[0]){
				$depth = $data[depth] + 1;
				print_product_category($connect, $category, $mode, $tables, $data[ref_group], $data[no], $depth);
			}
		///////////////////////////////////////////////////while 종료 ///////////////////////////////////////////////////
		} else if($mode == "small_list"){
			if($data[depth] > 1 ) {
					$depth_data =  "data-add-menu=\"depth0".($data[depth]-1)."\"";
					$depth_img = "<i class=\"depth\"><img src=\"/ko_mall/images/content/icon_depth.png\"/></i>";
				} else {
					unset($depth_data);
					unset($depth_img);
				}
		?>
			<tr <?=$depth_data?>>
				<td data-add-menu="name"><?=$depth_img?> <?=$data[title]?></td>
				<td><a href="#" class="button sm gray btn_close" data-menu-select="<?=$data[id]?>" data-menu-title="<?=$data[title]?>">선택</a></td>
			</tr>
	<?
			$sub = mysqli_fetch_array(mysqli_query($connect, "SELECT COUNT(*) FROM $tables WHERE ref_no='$data[no]' AND delete_state != 'Y'"));
			if($sub[0]){
				$depth = $data[depth] + 1;
				print_product_category($connect, $category, $mode, $tables, $data[ref_group], $data[no], $depth);
			}
		}
	}
}

function master_sms_send($target, $ref_company_no){
	$co = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_contract WHERE no='$ref_company_no' LIMIT 1"));
	$target_tel = return_tel($target);
	$target_name = return_person($target);
	sms_send($site[sms_id], $site[sms_key], "010-2508-5133", $target_tel, "[]" . " $co[sitename] 요청사항이 등록되었습니다. 확인해주세요.");
}

function return_sms_total($sms_id, $sms_key, $total){
	//잔여건수 조회
	$sms_url = "http://sslsms.cafe24.com/sms_remain.php"; // 전송요청 URL
	$sms['user_id'] = base64_encode($sms_id); // SMS 아이디
	$sms['secure'] = base64_encode($sms_key) ;//인증키
	$sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
	$host_info = explode("/", $sms_url);
	$host = $host_info[2];
	$path = $host_info[3];
	srand((double)microtime()*1000000);
	$boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
	// 헤더 생성
	$header = "POST /".$path ." HTTP/1.0\r\n";
	$header .= "Host: ".$host."\r\n";
	$header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

	// 본문 생성
	foreach($sms AS $index => $value){
		$data .="--$boundary\r\n";
		$data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
		$data .= "\r\n".$value."\r\n";
		$data .="--$boundary\r\n";
	}
	$header .= "Content-length: " . strlen($data) . "\r\n\r\n";

	$fp = fsockopen($host, 80);

	if ($fp) {
		fputs($fp, $header.$data);
		$rsp = '';
		while(!feof($fp)) {
			$rsp .= fgets($fp,8192);
		}
		fclose($fp);
		$msg = explode("\r\n\r\n",trim($rsp));
		$Count = $msg[1]; //잔여건수

		if($Count < $total){
			$Count = "현재 SMS 잔여건수가 $total 건 이하입니다.\\r\\n현재 잔여건수 : $Count 건";
		} else {
			$Count = "";
		}
		//echo $Count;
	} else {
		//echo "Connection Failed";
	}

	return $Count;
}



//금액계산
function return_price($mode, $dept, $selector, $start, $end, $key, $keyword, $select_center){
	//$mode : complete, incomplete, total
	//$selector : contract_money, down_payment, balance_payment

	if($select_center){
		$count_center = 0;
		foreach($select_center as $v){
			if($count_center == 0){
				$CENTER_SELECT_WHERE .= "AND (";
			}
			$CENTER_SELECT_WHERE .= " center_name = '$v' OR";
			$count_center++;
		}

		$CENTER_SELECT_WHERE = substr($CENTER_SELECT_WHERE, 0, -3);
		$CENTER_SELECT_WHERE .= ")";
	}

	$tmp_selector = explode("_", $selector);
	$selector_deposit = $tmp_selector[0]."_deposit_date";

	switch($mode){

		case "complete" :
			$WHERE = "AND $selector_deposit != ''";
			break;

		case "incomplete" :
			$WHERE = "AND $selector_deposit = ''";
			break;

		case "total" :
			$WHERE = "";
			break;
	}

	if($start){
		$DATE_WHERE = "AND contract_date >= '$start' ";
	}

	if($end){
		$DATE_WHERE .= "AND contract_date <= '$end'";
	}

	if($key && $keyword){
		$SEARCH_WHERE = "AND $key LIKE '%$keyword%'";
	}

	if($dept){
		$DEPT_WHERE = "AND center_dept = '$dept'";
	}


	$query = "SELECT SUM($selector) AS $selector FROM koweb_contract WHERE 1=1 $WHERE $DATE_WHERE $SEARCH_WHERE $CENTER_SELECT_WHERE $DEPT_WHERE";


	$result = mysqli_query($connect, $query);
	$row = mysqli_fetch_array($result);

	return $row[$selector];
	//return $query;
}


//직원정보
function return_person($no, $mode){
	//직원 상세
	$query = "SELECT * FROM koweb_member WHERE no='$no'";
	$row = mysqli_fetch_array(mysqli_query($connect, $query));
	//echo $row[name];

	//직원 소속(소속 부서)
	$dept = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_dept WHERE no='$row[dept]'"));

	//직원 소속(지역)
	$tmp_dept = explode("|", $dept[depth_history]);
	$first_dept = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_dept WHERE no='$tmp_dept[0]'"));

	if($mode == "center"){

		$result = $first_dept[dept];

	} else if($mode == "dept"){
		$result = $dept[dept];
	} else {
		if($row[name]){
			$result = "[" . $first_dept[dept] . "] " . $row[name];
		} else {
			$result = "";
		}
	}

	return $result;
}

//직원정보
function return_tel($no){
	//직원 상세
	$query = "SELECT * FROM koweb_member WHERE no='$no'";
	$row = mysqli_fetch_array(mysqli_query($connect, $query));

	//직원 소속(소속 부서)
	//$dept = mysqli_fetch_array(mysqli_query("SELECT * FROM koweb_dept WHERE no='$row[dept]'"));
	$result = $row[phone];

	return $result;
}

//계층형 네비게이션 (쿼리수정)
function category_navigator($ref_group, $ref_no, $depth = 1, $no, $v){
	$query = "SELECT * FROM koweb_dept WHERE state = 'Y' AND depth = '$depth' AND ref_group='$ref_group'";
	$result = mysqli_query($connect, $query);

	while($data = mysqli_fetch_array($result)){

		$v .= $data[dept];

		if($depth != $data[depth]){
			$v .= " > ";
			$depth = $data[depth] + 1;
			category_navigator($data[ref_group], $data[ref_no], $depth, $v);
		}
	}
	return $v;
}

//s = 보내는사람, r = 받는사람, message = 내용
function sms_send($sms_id, $key, $s, $r, $message){
	$sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS 전송요청 URL
	// $sms_url = "http://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL
	$sms['user_id'] = base64_encode($sms_id); //SMS 아이디.
	$sms['secure'] = base64_encode($key) ;//인증키
	$smsType = "S";

	$send_info = explode("-", $s);
	$reserve_info = explode("-", $r);


	//장문은 L , 단문은 S
	if( $_POST['smsType'] == "L"){
		  $sms['subject'] =  base64_encode($_POST['subject']);
	}

	//받는번호
	$sms['rphone'] = base64_encode($r);

	//보내는번호
	$sms['sphone1'] = base64_encode($send_info[0]);
	$sms['sphone2'] = base64_encode($send_info[1]);
	$sms['sphone3'] = base64_encode($send_info[2]);

	//메세지 90바이트까지. (단문)
	$sms['msg'] = base64_encode(stripslashes($message));
	$temp_date = date("Ymd");
	$now_h = date("H");
	$w_array = array("일","월","화","수","목","금","토");

	//18시 이후 문자는 다음날 09시에
	//$temp_date = date("20181210");
	//$now_h = "19";
/*
	if($now_h < "09"){
		//오늘 날짜 구하기
		$now_tmp_date = date("Ymd", strtotime($temp_date));
		//오늘 요일 구하기
		$now_w = $w_array[date('w', strtotime($now_tmp_date))];

		if($now_w == "토"){
			$_POST['rdate'] =  date("Ymd", strtotime("+2 day", strtotime($temp_date)));
			$_POST['rtime'] = "090000";
		} else if($now_w == "일"){
			$_POST['rdate'] =  date("Ymd", strtotime("+1 day", strtotime($temp_date)));
			$_POST['rtime'] = "090000";
		} else {
			$_POST['rdate'] = $now_tmp_date;
			$_POST['rtime'] = "090000";
		}
	} else if($now_h >= 18){
		$next_tmp_date = date("Ymd", strtotime("+1 day", strtotime($temp_date)));
		$next_w = $w_array[date('w', strtotime($next_tmp_date))];
		//$next_w = "일";
		if($next_w == "토"){
			$_POST['rdate'] =  date("Ymd", strtotime("+2 day", strtotime($temp_date)));
			$_POST['rtime'] = "090000";
		} else if($next_w == "일"){
			$_POST['rdate'] =  date("Ymd", strtotime("+1 day", strtotime($temp_date)));
			$_POST['rtime'] = "090000";
		} else {
			$_POST['rdate'] = $next_tmp_date;
			$_POST['rtime'] = "090000";
		}
	}
*/
	//예약일자 * Ymd * 예)20180113
	$sms['rdate'] = base64_encode($_POST['rdate']);
	//예약시간 * His * 예)173000
	$sms['rtime'] = base64_encode($_POST['rtime']);

	$sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.

	//전송후 이동할 페이지
	$sms['returnurl'] = base64_encode($_POST['returnurl']);
	$returnurl = $_POST['returnurl'];

	//테스트 용
	$sms['testflag'] = base64_encode($_POST['testflag']);

	//이름삽입번호 ( 메시지에 받는사람 이름넣을때 )
	//<input type="type" name="destination" value="010-000-0000|홍길동,010-000-0000|김영희">
	//<input type="type" name="msg" value="{name}님, 주문하신 물품이 배송되었습니다.">
	$sms['destination'] = strtr(base64_encode($_POST['destination']), '+/=', '-,');


	//반복 설정 ( 원하는경우 Y )
	$sms['repeatFlag'] = base64_encode($_POST['repeatFlag']);

	//반본 횟수
	$sms['repeatNum'] = base64_encode($_POST['repeatNum']);

	//반복시간 15분이상 부터 가능
	$sms['repeatTime'] = base64_encode($_POST['repeatTime']);

	$sms['smsType'] = base64_encode("S"); // LMS일경우 L
	$nointeractive = $_POST['nointeractive']; //사용할 경우 : 1, 성공시 대화상자(alert)를 생략

	$host_info = explode("/", $sms_url);
	$host = $host_info[2];
	$path = $host_info[3]."/".$host_info[4];

	srand((double)microtime()*1000000);
	$boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
	//print_r($sms);

	// 헤더 생성
	$header = "POST /".$path ." HTTP/1.0\r\n";
	$header .= "Host: ".$host."\r\n";
	$header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

	// 본문 생성
	foreach($sms AS $index => $value){
		$data .="--$boundary\r\n";
		$data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
		$data .= "\r\n".$value."\r\n";
		$data .="--$boundary\r\n";
	}
	$header .= "Content-length: " . strlen($data) . "\r\n\r\n";

	$oCurl = curl_init();
	$url =  "https://sslsms.cafe24.com/smsSenderPhone.php";
	$aPostData['userId'] = $sms_id; // SMS 아이디
	$aPostData['passwd'] = $key; // 인증키
	curl_setopt($oCurl, CURLOPT_URL, $url);
	curl_setopt($oCurl, CURLOPT_POST, 1);
	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($oCurl, CURLOPT_POSTFIELDS, $aPostData);
	curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
	$ret = curl_exec($oCurl);
	//echo $ret;
	curl_close($oCurl);


	$fp = fsockopen($host, 80);

	if ($fp) {
		fputs($fp, $header.$data);
		$rsp = '';
		while(!feof($fp)) {
			$rsp .= fgets($fp,8192);
		}
		fclose($fp);
		$msg = explode("\r\n\r\n",trim($rsp));
		$rMsg = explode(",", $msg[1]);
		$Result= $rMsg[0]; //발송결과
		$Count= $rMsg[1]; //잔여건수
	} else {
		$alert = "Connection Failed";
	}


	return $Result;
}

//온라인폼 생성 insert function
 function insert_variable($mode, $database, $ref_no, $name, $type, $order, $view_order) {

	if(!$mode) $result = "insert_variable not mode";
	if(!$database) $result = "insert_variable not database";
	if(!$ref_no) $result = "insert_variable not ref_no";
	if(!$name) $result = "insert_variable not name";
	if(!$type) $result = "insert_variable not type";
	if(!$order) $result = "insert_variable not order";
	if(!$view_order) $result = "insert_variable not view_order";

	if($mode == "update"){
		$function = mysqli_query($connect, "UPDATE $database SET ref_no = '$ref_no', variable_name = '$name', variable_type='$type', state='Y', order='$order', view_order='$view_order', order='$order' WHERE no='$no'");
	} else if($mode == "insert"){
		$function = mysqli_query($connect, "INSERT INTO $database VALUES('', '$ref_no', '$name', '$type', 'Y', '$order', '$view_order', '$order')");
	}

	return $result;
}

//URL 도메인 구하기 정규식
 function getDomainName($url) {
	$value = strtolower(trim($url));
	$url_patten = '/^(?:(?:[a-z]+):\/\/)?((?:[a-z\d\-]{2,}\.)+[a-z]{2,})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?$/i';
	$domain_patten = '/([a-z\d\-]+(?:\.(?:asia|info|name|mobi|com|net|org|biz|tel|xxx|kr|co|so|me|eu|cc|or|pe|ne|re|tv|jp|tw|daeguweb.gethompy.com)){1,2})(?::\d{1,5})?(?:\/[^\?]*)?(?:\?.+)?$/i';

	if (preg_match($url_patten, $value)){
		preg_match($domain_patten, $value, $matches);
		$host = (!$matches[1]) ? $value : $matches[1];
	}
	return $host;
}

//두날짜 사이 구하기
function getDatesFromRange($a,$b,$x=0,$dates=array()){
    while(end($dates)!=$b && $x=array_push($dates,date("Y-m-d",strtotime("$a +$x day"))));
    return $dates;

	//getDatesFromRange( '2010-10-01', '2010-10-05' );
}


//매주 월~금 날짜 구하기
function count_calendar($count, $week){

	$count = str_replace("--", "-", $count);
	$count_result = $count * 7;
	$today_num = date('w');

	$firstday = date('Y-m-d',strtotime('-'.(($today_num - 1)) + $count_result . ' days'));
	$lastday = date('Y-m-d',strtotime('+'.((7 - $today_num)) + $count_result . ' days'));

	if($week == "0"){
		return $firstday;
	} else {
		return $lastday;
	}
	//count_calendar(0, 1) -> 이번주 월요일
	//count_calendar(0, 1) -> 이번주 금요일
	//count_calendar(1, 1) -> 다음주 월요일
	//count_calendar(2, 5) -> 2 주후 금요일

}

//유효성 검사
function sanitizeString($var) {
		$var = trim($var);
		// $var = htmlspecialchars($var, ENT_QUOTES); // 한글 인코딩(EUC-KR) 지원
		$var = htmlspecialchars($var); // 한글 인코딩(EUC-KR) 지원
		// $var = htmlentities($var); // 한글 인코딩(EUC-KR) 지원 안됨
		$var = stripslashes($var);    // '/' 삭제함
		$var = addslashes($var);   //   '/' 추가함
		$var = strip_tags($var);   //   내용중 태그 삭제
		// $var = escapeshellcmd($var);

	return $var;
}

// 첨부 파일
function upload_file($dir, $tmp_name, $name) {
	// 확장자 체크
	$ext = end(explode(".", strtolower($name)));
	$ext_able = array("gif", "jpg", "jpeg", "png", "zip", "alz", "rar", "txt", "doc", "docx", "hwp", "psd", "xls", "xlsx", "csv", "ppt", "pptx", "bmp", "asf", "wmv", "wma", "pdf", "flv", "swf", "mp4", "mp3");
	if (!in_array($ext, $ext_able)) error("등록 가능한 파일 형식이 아닙니다.");

	// 파일명 인증
	$name = str_replace("\'", "", $name);
	$name = str_replace("\"", "", $name);
	$name = str_replace("..", "", $name);
	while (file_exists($dir . $name)) {
		$name =  rand("10000", "99999")."_".$name;
	}
	move_uploaded_file($tmp_name, $dir . $name);

	// 썸네일 만들기
	if ($ext == "gif" || $ext == "jpg" || $ext == "png") {
		// 사용법 : thumnail(원본파일명, 저장파일명, 저장위치, 가로크기, 세로크기)
		@thumnail($dir . $name,  "thumb_" . $name, $dir, 600, 600);
	}
	return $name;
}

// 썸네일
// 사용법 : thumnail(원본파일명, 저장파일명, 저장위치, 가로크기, 세로크기)
function thumnail($file, $save_filename, $save_path, $max_width, $max_height) {

	// 전송받은 이미지 정보를 받는다
	$img_info = @getImageSize($file);

	// 전송받은 이미지의 포맷값 얻기 (gif, jpg, png)
	if ($img_info[2] == 1) {
		$src_img = @ImageCreateFromGif($file);
	} else if ($img_info[2] == 2) {
		$src_img = @ImageCreateFromJPEG($file);
	} else if($img_info[2] == 3) {
		$src_img = @ImageCreateFromPNG($file);
	} else {
		return 0;
	}

	// 전송받은 이미지의 실제 사이즈 값얻기
	$img_width = $img_info[0];
	$img_height = $img_info[1];

	if ($img_width <= $max_width) {
		$max_width = $img_width;
		$max_height = $img_height;
	}

	if ($img_width > $max_width) {
		$max_height = ceil(($max_width / $img_width) * $img_height);
	}

	// 새로운 트루타입 이미지를 생성
	$dst_img = @imagecreatetruecolor($max_width, $max_height);

	// R255, G255, B255 값의 색상 인덱스를 만든다
	@ImageColorAllocate($dst_img, 255, 255, 255);

	// 이미지를 비율별로 만든후 새로운 이미지 생성
	@ImageCopyResampled($dst_img, $src_img, 0, 0, 0, 0, $max_width, $max_height, ImageSX($src_img), ImageSY($src_img));

	// 알맞는 포맷으로 저장
	if ($img_info[2] == 1) {
		@ImageInterlace($dst_img);
		@ImageGif($dst_img, $save_path.$save_filename);
	} else if ($img_info[2] == 2) {
		@ImageInterlace($dst_img);
		@ImageJPEG($dst_img, $save_path.$save_filename);
	} else if($img_info[2] == 3) {
		@ImagePNG($dst_img, $save_path.$save_filename);
	}

	// 임시 이미지 삭제
	@ImageDestroy($dst_img);
	@ImageDestroy($src_img);
}

// 에러 표시
function error($message) {
	echo "
			<script type='text/javascript'>
				alert(\"$message\");
				history.go(-1);
			</script>
		";

	exit;
}

// 공백검사
function isblank($str) {
	$temp = str_replace("　", "", $str);
	$temp = str_replace("\n", "", $temp);
	$temp = strip_tags($temp);
	$temp = str_replace("&nbsp;", "", $temp);
	$temp = str_replace(" ", "", $temp);
	if (preg_match ("/[^[:space:]]/", $temp)) return false;
	return true;
}

// 경고창 표시
function alert($message) {
	echo "
			<script type='text/javascript'>
				alert(\"$message\");
			</script>
		";
}

function alert_to_admin($message) {
	echo "
			<script type='text/javascript'>
				alert(\"$message\");
				location.href=\"/ko_admin/login.html\";
			</script>
		";
}

// 이동
function url($url) {
	echo "<meta http-equiv='refresh' content='0;url=$url' >";
	exit;
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/lib/str_function.php";
?>
