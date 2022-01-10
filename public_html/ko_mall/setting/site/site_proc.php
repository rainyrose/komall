<?
// 프로그램 ID 정리
$reg_date = date("Y-m-d H:i:s");
$ip = $_SERVER['REMOTE_ADDR'];
if($mode == "write_proc"){
	// 프로그램 정보 입력
	if($tmode == "mall"){
		// $company_phone = $company_phone1."|".$company_phone2;
		@mysqli_query($connect, "INSERT INTO koweb_mall_config VALUES('', '$company', '$company_regist_no', '$ceo_name', '$company_phone', '$fax_no', '$t_no', '$b_no', '$company_zip', '$company_address', '$it_name', '$it_mail','$company_bank', '$reg_date', '$state','$category')");

	} else if($tmode == "pay"){

	$transfer = str_replace(",","",$transfer);

	if($use_uplus == "Y"){

		if(!$uplus_shopid || !$uplus_mertkey){
			error("상점아이디 와 MERT KEY를 입력하세요");
			exit;
		}

		$query = "SELECT * FROM koweb_pay_config ORDER BY no DESC";
		$result = mysqli_query($connect, $query);
		$row = mysqli_fetch_array($result);

		if($row[uplus_shopid] && $row[uplus_mertkey]){
			//mall.conf 수정
			$filename = $_SERVER['DOCUMENT_ROOT']."/../lguplus/conf/mall.conf";
			$fp = fopen($filename, "r") or die("파일열기에 실패하였습니다");
			$buffer = fread($fp, filesize($filename));
			$buffer = str_replace("$row[uplus_shopid] = $row[uplus_mertkey]", "$uplus_shopid = $uplus_mertkey", $buffer);
			fclose($fp);

			$f = @fopen($filename,'w');
			@fwrite($f,$buffer);
			@fclose($f);
		} else {
			//mall.conf 수정
			$filename = $_SERVER['DOCUMENT_ROOT']."/../lguplus/conf/mall.conf";
			$fp = fopen($filename, "r") or die("파일열기에 실패하였습니다");
			$buffer = fread($fp, filesize($filename));
			//echo "<pre>".htmlspecialchars($buffer)."</pre>";
			$buffer = str_replace("koweb = koweb", "$uplus_shopid = $uplus_mertkey", $buffer);
			fclose($fp);

			$f = @fopen($filename,'w');
			@fwrite($f,$buffer);
			@fclose($f);

		}
	}

		if($deli_type == "def"){
			$deli_price_type = $_POST['deli_price_type'];
			$deli_price = $_POST['deli_price'];
			$deli_price_type_ = $deli_price_type;

			//print_r($_POST['deli_price']);

			sort($deli_price_type);
			$i = 0;
			foreach($deli_price_type as $value){
				$ims_key = array_search($value, $deli_price_type_);
				$deli_price_[$i] = $deli_price[$ims_key];
				$i++;

			}
			$deli_price = $deli_price_;

			foreach($deli_price_type as $value){
				if($value != ""){
					$deli_price_type_tmp .= $value ."|";
				}
			}
			foreach($deli_price as $value){
				if($value != ""){
					$deli_price_tmp .= $value ."|";
				}
			}

			$deli_price_type = substr($deli_price_type_tmp, 0, -1);
			$deli_price = substr($deli_price_tmp, 0, -1);
		} else {
			$deli_price = $_POST[deli_price][0];
			if(!$deli_price){
				$deli_price = 0;
			}
		}

		@mysqli_query($connect, "INSERT INTO koweb_pay_config VALUES('', 'Y', '$use_direct_content', '$use_uplus', '$uplus_shopid',
		'$uplus_mertkey', '$uplus_type', '$use_phone_pay', '$use_tel_pay', '$use_culture_pay','$deli_company', '$deli_type', '$deli_price_type', '$deli_price', '$deli_content', '$deli_return_content', '$limit_', '$reg_date', '$sinfo1','$sinfo2','$sinfo3','$sinfo4'
		,'$transfer', '$state')");

	} else if($tmode == "sms"){


		@mysqli_query($connect, "INSERT INTO koweb_sms_config VALUES('', '$sms_use', '$send_no', '$join_sms', '$join_sms_content', '$cons_sms','$cons_sms_content', '$cons_admin_sms', '$cons_admin_sms_content', '$deposit_sms', '$deposit_sms_content', '$deli_sms', '$deli_sms_content', '$reg_date', '$state')");



	} else if($tmode == "info"){

		//회원가입안내
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='regist_info' AND lang='{$lang}' AND category='info'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$regist_info', reg_date='$reg_date', ip='$ip' WHERE id='regist_info' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='회원가입안내' , contents = '$regist_info', reg_date='$reg_date', ip='$ip' ,id='regist_info' ,lang='{$lang}' , category='info' , state='Y'");
		}

		//주문안내
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='order_info' AND lang='{$lang}' AND category='info'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$order_info', reg_date='$reg_date', ip='$ip' WHERE id='order_info' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='주문안내' , contents = '$order_info', reg_date='$reg_date', ip='$ip' , id='order_info' , lang='{$lang}' , category='info' , state='Y'");
		}

		//결제안내
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='pay_info' AND lang='{$lang}' AND category='info'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$pay_info', reg_date='$reg_date', ip='$ip' WHERE id='pay_info' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='결제안내' , contents = '$pay_info', reg_date='$reg_date', ip='$ip' , id='pay_info' , lang='{$lang}' , category='info' , state='Y'");
		}

		//배송안내
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='deli_info' AND lang='{$lang}' AND category='info'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$deli_info', reg_date='$reg_date', ip='$ip' WHERE id='deli_info' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='배송안내' , contents = '$deli_info', reg_date='$reg_date', ip='$ip' , id='deli_info' , lang='{$lang}' , category='info' , state='Y'");
		}

		//교환안내
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='ex_info' AND lang='{$lang}' AND category='info'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$ex_info', reg_date='$reg_date', ip='$ip' WHERE id='ex_info' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='교환안내' , contents = '$ex_info', reg_date='$reg_date', ip='$ip' , id='ex_info' , lang='{$lang}' , category='info' , state='Y'");
		}

		//환불안내
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='refund_info' AND lang='{$lang}' AND category='info'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$refund_info', reg_date='$reg_date', ip='$ip' WHERE id='refund_info' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='환불안내' , contents = '$refund_info', reg_date='$reg_date', ip='$ip' , id='refund_info' , lang='{$lang}' , category='info' , state='Y'");
		}

		//적립금 및 포인트 안내
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='point_info' AND lang='{$lang}' AND category='info'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$point_info', reg_date='$reg_date', ip='$ip' WHERE id='point_info' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='적립금 및 포인트 안내' , contents = '$point_info', reg_date='$reg_date', ip='$ip' , id='point_info' , lang='{$lang}' , category='info' , state='Y'");
		}


	} else if($tmode == "private"){

		//개인정보 처리방침 기본내용
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='private_info' AND lang='{$lang}' AND category='private'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$private_info', reg_date='$reg_date', ip='$ip' WHERE id='private_info' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='개인정보 처리방침 기본내용' , contents = '$private_info', reg_date='$reg_date', ip='$ip' , id='private_info' , lang='{$lang}' , category='private' , state='Y'");
		}

		//개인정보 수집 및 이용동의
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='private_agree_regist' AND lang='{$lang}' AND category='private'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$private_agree_regist', reg_date='$reg_date', ip='$ip' WHERE id='private_agree_regist' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='개인정보 수집 및 이용동의' , contents = '$private_agree_regist', reg_date='$reg_date', ip='$ip' , id='private_agree_regist' , lang='{$lang}' , category='private' , state='Y'");
		}

		//비회원 구매시
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='private_agree_notmem' AND lang='{$lang}' AND category='private'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$private_agree_notmem', reg_date='$reg_date', ip='$ip' WHERE id='private_agree_notmem' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='비회원 구매시' , contents = '$private_agree_notmem', reg_date='$reg_date', ip='$ip' , id='private_agree_notmem' , lang='{$lang}' , category='private' , state='Y'");
		}

		//비회원 게시판 글 작성 시
		/*
		@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$private_notmem_write', reg_date='$reg_date', ip='$ip' WHERE id='private_notmem_write'");
		*/
	} else if($tmode == "agreement"){

		//이용약관
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='agreement' AND lang='{$lang}' AND category='agreement'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$agreement', reg_date='$reg_date', ip='$ip' WHERE id='agreement' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='이용약관' , contents = '$agreement', reg_date='$reg_date', ip='$ip' , id='agreement' , lang='{$lang}' , category='agreement' , state='Y'");
		}

		//쇼핑정보 수신동의 약관
		if(mysqli_num_rows(query("SELECT * FROM koweb_terms_config  WHERE id='shopping_agree' AND lang='{$lang}' AND category='agreement'")) > 0){
			@mysqli_query($connect, "UPDATE koweb_terms_config SET contents = '$shopping_agree', reg_date='$reg_date', ip='$ip' WHERE id='shopping_agree' AND lang='{$lang}'");
		}else{
			@mysqli_query($connect, "INSERT INTO koweb_terms_config SET title='쇼핑정보 수신동의 약관' , contents = '$shopping_agree', reg_date='$reg_date', ip='$ip' , id='shopping_agree' , lang='{$lang}' , category='agreement' , state='Y'");
		}
	} else {
		@mysqli_query($connect, "INSERT INTO koweb_site_config VALUES('', '$site_url', '$title', '$keyword_title', '$description', '$og_description', '$og_site_name', '$og_title', '$use_member', '$this_url', '$member_level', '$use_member_okey', '$use_namecheck', '$namecheck_key', '$naver_tag', '$google_tag', '$use_sms', '$sms_id', '$sms_key', '$sms_alert', 'Y', '$administrator', '$administrator_mail', '$reg_date', '$state','$category')");
	}

	$alert_txt = "사이트관리가 등록";
}

/*----------------------------------------------------------------------------*/
// 마무리
/*----------------------------------------------------------------------------*/
?>
<script type="text/javascript">
alert("<?=$alert_txt?> 되었습니다.");
location.href = "<?=$common_queryString?>&mode=<?=$tmode?>&lang=<?=$lang?>";
</script>
