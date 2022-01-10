<?
	include_once $_SERVER['DOCUMENT_ROOT']."/head.php";
	$con = mysqli_connect($host, $user, $passwd, $dataname) or die("not connected");

	mysqli_autocommit($con, FALSE);
	mysqli_begin_transaction($con, MYSQLI_TRANS_START_READ_WRITE);

	$reg_date = date("Y-m-d H:i:s");
	$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/product/";

	if($mode == "order_proc"){

		$common_queryString = "/".$add_folder."product/product.html";
		$add_delivery_price = 0;

		$adp_query = "SELECT * FROM koweb_add_delivery_price WHERE start_zip <= '$zip' AND end_zip >= '$zip' ORDER BY price DESC LIMIT 1";
		$adp_query2 = "SELECT * FROM koweb_add_delivery_price WHERE start_zip <= '$zip' AND end_zip >= '$zip'";
		$adp_result = mysqli_query($con,$adp_query);
		$adp_row = mysqli_fetch_array($adp_result);
		$adp_check = mysqli_num_rows(mysqli_query($con, $adp_query2));

		if($adp_check != 0){
			$add_delivery_price =  $adp_row[price];
		} else {
			$add_delivery_price = "0";
		}

		try{
			if($pay_type == "무통장입금"){

				$state = "입금대기";

			} else if($pay_type == "paypal"){

				$state = "결제완료";

				### access token 가져오기 ####

				$response = get_paypal_authtoken($toid);

				if(!$response['flag']){
					error($response['ment']);
				}

				$order_id = $response['order_id'];

				if(!$order_id) error("orderid error");


			} else {
				//-- 카드결제
				$configPath = $_SERVER['DOCUMENT_ROOT']."/../lguplus"; //LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정.
				/*
				 *************************************************
				 * 1.최종결제 요청 - BEGIN
				 *  (단, 최종 금액체크를 원하시는 경우 금액체크 부분 주석을 제거 하시면 됩니다.)
				 *************************************************
				 */
				$CST_PLATFORM               = $_POST["CST_PLATFORM"];
				$CST_MID                    = $_POST["CST_MID"];
				$LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
				$LGD_PAYKEY                 = $_POST["LGD_PAYKEY"];

			   require_once($_SERVER['DOCUMENT_ROOT']."/ko_mall/lguplus/lgdacom/XPayClient.php");

				// (1) XpayClient의 사용을 위한 xpay 객체 생성
				// (2) Init: XPayClient 초기화(환경설정 파일 로드)
				// configPath: 설정파일
				// CST_PLATFORM: - test, service 값에 따라 lgdacom.conf의 test_url(test) 또는 url(srvice) 사용
				//				- test, service 값에 따라 테스트용 또는 서비스용 아이디 생성
				$xpay = new XPayClient($configPath, $CST_PLATFORM);

				// (3) Init_TX: 메모리에 mall.conf, lgdacom.conf 할당 및 트랜잭션의 고유한 키 TXID 생성
				if (!$xpay->Init_TX($LGD_MID)) {
					if($site_language == "eng"){
						throw new Exception("Payment failed.");
					}else{
						throw new Exception("결제가 실패하였습니다.");
					}
				}
				$xpay->Set("LGD_TXNAME", "PaymentByKey");
				$xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);

				//금액을 체크하시기 원하는 경우 아래 주석을 풀어서 이용하십시요.
				//$DB_AMOUNT = "DB나 세션에서 가져온 금액"; //반드시 위변조가 불가능한 곳(DB나 세션)에서 금액을 가져오십시요.
				//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
				//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);

				/*
				 *************************************************
				 * 1.최종결제 요청(수정하지 마세요) - END
				 *************************************************
				 */

				/*
				 * 2. 최종결제 요청 결과처리
				 *
				 * 최종 결제요청 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
				 */
				// (4) TX: lgdacom.conf에 설정된 URL로 소켓 통신하여 최종 인증요청, 결과값으로 true, false 리턴
				if ($xpay->TX()) {

					$keys = $xpay->Response_Names();
					foreach($keys as $named) {
					//	echo $name . " = " . $xpay->Response($name, 0) . "<br/>";
					}

					//echo "<p>";

					// (5) DB에 요청 결과 처리
					if( "0000" == $xpay->Response_Code() ) {

						$pay_state = "결제완료";
						$xpay->Response("LGD_AMOUNT",0);
						if($pay_type == "계좌이체"){
							$bank_name = $xpay->Response("LGD_ACCOUNTOWNER",0);
						}

						//	echo "결제요청이 완료되었습니다.  <br/>";
						//	echo "TX 통신 응답코드 = " . $xpay->Response_Code() . "<br/>";		//통신 응답코드("0000" 일 때 통신 성공)
						//	echo "TX 통신 응답메시지 = " . $xpay->Response_Msg() . "<p>";

						//	echo "거래번호 : " . $xpay->Response("LGD_TID",0) . "<br/>";
						//	echo "상점아이디 : " . $xpay->Response("LGD_MID",0) . "<br/>";
						//	echo "상점주문번호 : " . $xpay->Response("LGD_OID",0) . "<br/>";
						//	echo "결제금액 : " . $xpay->Response("LGD_AMOUNT",0) . "<br/>";
						//	echo "결과코드 : " . $xpay->Response("LGD_RESPCODE",0) . "<br/>";	//LGD_RESPCODE 가 반드시 "0000" 일때만 결제 성공, 그 외는 모두 실패
						//	echo "결과메세지 : " . $xpay->Response("LGD_RESPMSG",0) . "<p>";

						$pay_price = $xpay->Response("LGD_AMOUNT",0);
						$order_tid = $xpay->Response("LGD_TID",0);
						$pay_date = $reg_date;
						$state = "결제완료";

						//신용카드결제금액

						//승인일시


						//통신상의 문제가 없을시
						//최종결제요청 결과 성공 DB처리(LGD_RESPCODE 값에 따라 결제가 성공인지, 실패인지 DB처리)
						//echo "최종결제요청 결과 성공 DB처리하시기 바랍니다.<br/>";

						//최종결제요청 결과를 DB처리합니다. (결제성공 또는 실패 모두 DB처리 가능)
						//상점내 DB에 어떠한 이유로 처리를 하지 못한경우 false로 변경해 주세요.
						//$isDBOK = true;
						//if( !$isDBOK ) {
							//echo "<p>";


							//echo "TX Rollback Response_code = " . $xpay->Response_Code() . "<br/>";
							//echo "TX Rollback Response_msg = " . $xpay->Response_Msg() . "<p>";

						//	if( "0000" == $xpay->Response_Code() ) {
							//	echo "자동취소가 정상적으로 완료 되었습니다.<br/>";
							//}else{
							//	echo "자동취소가 정상적으로 처리되지 않았습니다.<br/>";
							//}
						//}
					}else{
						//통신상의 문제 발생(최종결제요청 결과 실패 DB처리)
						if($site_language == "eng"){
							throw new Exception("Payment failed.");
						}else{
							throw new Exception("결제가 실패하였습니다.");
						}

					}
				} else {
					if($site_language == "eng"){
						throw new Exception("Payment failed.");
					}else{
						throw new Exception("결제가 실패하였습니다.");
					}
				}
			}

		//-->

			$add_query = "";

			if(!$option_) {
				if($site_language == "eng"){
					throw new Exception("This is an unusual approach..");
				}else{
					throw new Exception("비정상적인 접근입니다.");
				}
			}

			//이미 존재하는 주문번호일시
			$check_order = "SELECT * FROM koweb_order WHERE order_id = '$order_id'";
			$check_order_r = mysqli_num_rows(mysqli_query($con, $check_order));

			if($check_order_r > 0){
				if($site_language == "eng"){
					throw new Exception("Order number already exists.");
				}else{
					throw new Exception("이미 존재하는 주문번호입니다.");
				}
			}


			//옵션 변수정의
			$options_json = json_decode($_POST[option_], true);
			$options_json = array_filter($options_json);

			//비회원주문일시 비밀번호
			if($_SESSION[order_type] == "guest"){
				if(!$password) {
					if($site_language == "eng"){
						throw new Exception("Please enter a password");
					}else{
						throw new Exception("비밀번호를 입력해주세요");
					}
				}

				$password = hash("sha256", $password);
				if($email2) $email3 = $email2;
				else $email3 = $tmp_mail;
				$email = $email1."@".$email3;
			}
			if(!$phone)	$phone = $phone1."-".$phone2."-".$phone3;


			//포인트 차감
			if($use_point && $use_point != "0"){
				$point_result = mysqli_fetch_array(mysqli_query($con, "SELECT SUM(point) AS total FROM koweb_point WHERE member = '$_SESSION[member_id]'"));
				if($point_result[total] < $use_point){
					if($site_language == "eng"){
						throw new Exception("There are not enough points");
					}else{
						throw new Exception("보유포인트가 부족합니다");
					}
				}
			} else {
				$use_point = 0;
			}

			//상품
			$total_price = 0;
			$deli_price = 0;
			$product_total_price = 0;
			$p_count = 0;

			//옵션 부분 처리
			$product_id_array = array();
			foreach($options_json AS $ojson){
				$product_id = $ojson[product_id];
				$product_id_array[] = $product_id;
			}
			foreach($options_json AS $ojson){
				$options_info = "";
				$add_options_info = "";
				$product_price = 0;

				$product_id = $ojson[product_id];
				$options = $ojson[options];
				$add_options = $ojson[add_options];
				$option_flag = $ojson[option_flag];
				$direct_option = $ojson[direct_option];

				$product_ = get_product($product_id);
				
				//옵션변수생성
				if($option_flag == "Y"){
					$options_info_ = explode("^", $options);
				} else {
					$options_info_ = $options;
					$options_info_ = array($product_id."|".$options_info_);
				}
				$options_info_ = array_filter($options_info_);

					foreach($options_info_ AS $oinfo){

						$options_ = explode("|", $oinfo);

						if($option_flag == "Y"){
							$options_tmp = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_option_detail WHERE id='$options_[0]'"));
							if($options_tmp && $options_tmp['state'] != "Y"){
								throw new Exception("[".$product_['product_title']."] 상품의 {$options_tmp['title']}옵션은 판매하지못하는 상품입니다.");
							}
						} else {
							$options_tmp = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_product WHERE id='$options_[0]'"));
							$options_tmp[title] = $options_tmp[product_title];
							$options_tmp[stock] = $options_tmp[stock_count];
						}

						if($options_tmp[stock] == "0" || ($options_tmp[stock] - $options_[1]) < 0){
							if($site_language == "eng"){
								throw new Exception("There is not enough stock.");
							}else{
								throw new Exception("재고수량이 부족합니다.");
							}
						}

						//옵션인지 아닌지 체크
						//옵션이 아닐때
						if($option_flag != "Y"){
							$product_all_count = $options_[1];
						//옵션일때
						} else {
							$product_all_count = 0;
							foreach ($options_json as $tmp_ojson) {
								if($tmp_ojson[product_id] == $product_id){
									$c_option_info_ = explode("^",$tmp_ojson['options']);
									foreach ($c_option_info_ as $c_option_) {
										$t_count = end(explode("|",$c_option_));
										$product_all_count += $t_count;
									}
								} else {
									continue;
								}
							}

						}
						if($product_[min_count] && $product_[min_count] > $product_all_count){
							//error($product_['product_title']."최소구매 상품갯수를 충족하지 못한 상품이 있습니다.");
							if($site_language == "eng"){
								throw new Exception($product_['product_title']."Some products do not meet the minimum number of purchased products.");
							}else{
								throw new Exception($product_['product_title']."최소구매 상품갯수를 충족하지 못한 상품이 있습니다.");
							}
							//exit;
						}

						if($product_[max_count] && $product_[max_count] < $product_all_count){
							//error("최대구매 상품갯수를 초과한 상품이 있습니다.");
							if($site_language == "eng"){
								throw new Exception("Some products exceed the maximum number of purchased products.");
							}else{
								throw new Exception("최대구매 상품갯수를 초과한 상품이 있습니다.");
							}
							//exit;
						}


						if($option_flag != "Y"){
							//재고수량 (상품)
							if($options_tmp[stock] != "0" || ($options_tmp[stock] - $options_[1]) > 0){
								$stock_product_ = mysqli_query($con, "UPDATE koweb_product SET stock_count = stock_count - $options_[1] WHERE id='$product_[id]' LIMIT 1");

								if(!$stock_product_){
									if($site_language == "eng"){
										throw new Exception("Inventory update failed.");
									}else{
										throw new Exception("재고수량 업데이트에 실패하였습니다.");
									}
								}

								$options_tmp2 = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_product WHERE id='$options_[0]'"));

								if($options_tmp2[stock_alram] && $options_tmp2[use_stock_alram] == "Y"){
									if($options_tmp2[stock] <= $options_tmp2[stock_alram]){
										$stock_str = $product_[product_title] ." 수량이 ".$options_tmp2[stock_alram]."개 이하 입니다";
										@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $site_sms[send_no], $stock_str);
									}
								}

								if($options_tmp2[stock_count] == 0){
									$sotck_count_update = mysqli_query($con, "UPDATE koweb_product SET use_soldout = 'Y' WHERE id='$product_[id]' LIMIT 1");

									if(!$sotck_count_update){
										if($site_language == "eng"){
											throw new Exception("Out of stock update failed.");
										}else{
											throw new Exception("품절 업데이트에 실패하였습니다.");
										}
									}
								}
							} else {
								if($site_language == "eng"){
									throw new Exception("This item is out of stock.");
								}else{
									throw new Exception("재고가 부족한 상품입니다.");
								}
							}

						} else {
							//재고수량 (옵션)
							if($options_tmp[stock] != "0" || ($options_tmp[stock] - $options_[1]) > 0){

								$options_stock_product_ = mysqli_query($con, "UPDATE koweb_option_detail SET stock = stock - $options_[1] WHERE id='$options_[0]' LIMIT 1");
								if(!$options_stock_product_){
									if($site_language == "eng"){
										throw new Exception("Inventory quantity update failed.");
									}else{
										throw new Exception("재고수량 업데이트에 실패하였습니다.");
									}
								}

								$options_tmp2 = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_option_detail WHERE id='$options_[0]'"));

								if($options_tmp2[safe_stock]){
									if($options_tmp2[stock] <= $options_tmp2[safe_stock]){
										$stock_str = $product_[product_title] ." ". $options_tmp2[title]." 수량이 ".$options_tmp2[safe_stock]."개 이하 입니다";
										@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $site_sms[send_no], $stock_str);
									}
								}

								if($options_tmp2[stock] == 0){
									$sotck_count_update = mysqli_query($con, "UPDATE koweb_option_detail SET soldout = 'Y' WHERE id='$options_[0]' LIMIT 1");

									if(!$sotck_count_update){
										if($site_language == "eng"){
											throw new Exception("Out of stock update failed.");
										}else{
											throw new Exception("품절 업데이트에 실패하였습니다.");
										}
									}
								}

							} else {
								if($site_language == "eng"){
									throw new Exception("This option is out of stock.");
								}else{
									throw new Exception("재고가 부족한 옵션 입니다.");
								}
							}
						}

						if($options_tmp[price_type] == "+"){
							$option_detail_price = $product_[price] + $options_tmp[price];

						} else if($options_tmp[price_type] == "-") {
							$option_detail_price = $product_[price] - $options_tmp[price];
						} else {
							//옵션이 없을때
							$option_detail_price = $product_[price];
						}

						$options_price = $option_detail_price * $options_[1];
						$options_point = $options_price * ($product_[point_detail] / 100);
						$product_price += $options_price;


						//옵션아이디 / 옵션명 / 수량 / 적립금 / 개별금액 / 토탈금액 / 상품기본가격
						//10517487|A / 2 / 레드|5|17500|35000|175000^21239606|B / 1 / 블루|4|15000|37500|150000
						$options_info .= $options_[0] ."|".$options_tmp[title] . "|" . $options_[1] ."|". $options_point  ."|". $option_detail_price."|". $options_price."|".$product_[price]."^";

						//옵션 인설트
					}

					$options_info = substr($options_info, 0, -1);


					//추가상품옵션



					//추가옵션 변수생성
					$add_options_info = "";
					$add_options_info_ = explode("^", $add_options);
					$add_options_info_ = array_filter($add_options_info_);

						if(count($add_options_info_) > 0){
							foreach($add_options_info_ AS $oinfo){

								$add_options_ = explode("|", $oinfo);
								$add_options_tmp = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_option_detail WHERE id='$add_options_[0]'"));

								//추가옵션 재고수량이 모자랄때
								if($add_options_tmp[stock] == "0" || ($add_options_tmp[stock] - $add_options_[1]) < 0){
									if($site_language == "eng"){
										throw new Exception("The stock of additional options is insufficient.");
									}else{
										throw new Exception("추가옵션 재고수량이 부족합니다.");
									}
								}

								//추가옵션 재고수량이 있을때
								if($add_options_tmp[stock] != "0" || ($add_options_tmp[stock] - $add_options_[1]) > 0){

									//재고 만큼 수량변경
									$options_stock_product_ = mysqli_query($con, "UPDATE koweb_option_detail SET stock = stock - $add_options_[1] WHERE id='$add_options_[0]' LIMIT 1");

									if(!$options_stock_product_){
										if($site_language == "eng"){
											throw new Exception("Failed to update additional options inventory.");
										}else{
											throw new Exception("추가옵션 재고수량 업데이트에 실패하였습니다.");
										}
									}

									$add_options_tmp2 = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_option_detail WHERE id='$add_options_[0]'"));

									if($add_options_tmp2[safe_stock]){
										if($add_options_tmp2[stock] <= $add_options_tmp2[safe_stock]){
											$stock_str = $product_[product_title] ." ". $add_options_tmp2[title]." 수량이 ".$add_options_tmp2[safe_stock]."개 이하 입니다";
											@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $site_sms[send_no], $stock_str);
										}
									}

									//재고가 0이라면 품절 UPDATE
									if($add_options_tmp2[stock] == 0){
										$sotck_count_update = mysqli_query($con, "UPDATE koweb_option_detail SET soldout = 'Y' WHERE id='$add_options_[0]' LIMIT 1");
										if(!$sotck_count_update){
											if($site_language == "eng"){
												throw new Exception("Failed to update additional options.");
											}else{
												throw new Exception("추가옵션 상품품절 업데이트에 실패하였습니다.");
											}
										}
									}

								//재고가 있으나, 재고가 현재 주문량보다 부족할때
								} else {
									if($site_language == "eng"){
										throw new Exception("Current inventory is less or less than the order quantity.");
									}else{
										throw new Exception("현재 재고가 주문량보다 적거나 없습니다.");
									}
								}

								$add_options_price = $add_options_tmp[price] * $add_options_[1];
								$add_options_point = $add_options_price * ($product_[point_detail] / 100);
								$product_price += $add_options_price;

								//옵션아이디 / 옵션명 / 수량 / 적립금 / 개별금액 / 토탈금액
								$add_options_info .= $add_options_[0] . "|" . $add_options_tmp[title] . "|" .$add_options_[1] . "|" . $add_options_point  ."|" . $add_options_tmp[price] ."|". $add_options_price."^";
							}
							$add_options_info = substr($add_options_info, 0, -1);
						}


					if($product_[point_type] == "2"){
						$option_point = ($product_price * ($product_[point_detail] / 100));
					}

					$product_total_price += $product_price;
					//total_price = ($deli_price + $product_price) - $use_point;

					if($p_count == (count($options_json)-1)){
						$deli_price = get_delivery_price($con, $product_id_array, $product_total_price);
						$add_query = ", order_info = 'P'
									, pay_price = '$pay_price'
									, pay_date = '$pay_date'
									, deli_price = '$deli_price'
									, email = '$email'
									, address_type = '$address_type'
									, name = '$name'
									, zip = '$zip'
									, address1 = '$address1'
									, address2 = '$address2'
									, address3 = '$address3'
									, phone = '$phone'
									, cash_paper_type = '$cash_paper_type'
									, cash_paper_method = '$cash_paper_method'
									, cash_paper_data = '$cash_paper_data'
									, deli_add_price = '$add_delivery_price'
									, memo = '$memo'
									, use_point = '$use_point'
									, pay_type = '$pay_type'
									, bank_name = '$bank_name'
									, bank_info = '$bank_info'
									";
					} else {
						$add_query = "";
					}

					//값 저장

					if($site_language == "eng"){
						$add_price_type = ", price_type = 'USD'";
						$add_price_type .= ", transfer = '{$site_pay[transfer]}'";
					}


					if($product_[use_telform] == "Y"){
						$add_query .=",option_type='tel'";
					}

					$ref_product_title = addslashes($product_[product_title]);
					$options_info = addslashes($options_info);
					$add_options_info = addslashes($add_options_info);
					$results = mysqli_query($con, "INSERT INTO koweb_order SET order_type = '$_SESSION[order_type]'
																, member = '$_SESSION[member_id]'
																, password = '$password'
																, option_flag = '$option_flag'
																$add_query
																$add_price_type
																, total_price = '$total_price'
																, product_price = '$product_price'
																, add_point = '$option_point'
																, ref_product = '$product_id'
																, ref_product_title = '$ref_product_title'
																, options_info = '$options_info'
																, add_options_info = '$add_options_info'
																, direct_options_info = '$direct_option'
																, order_id = '$order_id'
																, order_tid = '$order_tid'
																, deli_code = ''
																, state = '$state'
																, reg_date = '$reg_date'");



					$rowid = mysqli_insert_id($con);
					$p_count++;

			}

			if($results){
				$query = "SELECT * FROM koweb_order WHERE order_id = '$order_id' AND order_info = 'P'";
				$result2 = mysqli_query($con, $query);
				$rows = mysqli_fetch_array($result2);

				if($use_point != "0" && $_SESSION[order_type] != "guest"){
					$update_point = mysqli_query($con, "INSERT INTO koweb_point SET member='$_SESSION[member_id]', order_id='$order_id', point_type='상품구매', point='-$use_point', reg_date='$reg_date'");
					if(!$update_point){
						if($site_language == "eng"){
							throw new Exception("Failed to award points.");
						}else{
							throw new Exception("포인트 부여에 실패하였습니다.");
						}
					}
				}


//				if($site_sms[sms_use] == "Y" && $site_sms[join_sms] == "Y"){
//					$str = trans_sms_order($con, $order_id, $site_sms[cons_sms_content]);
//					$str2 = trans_sms_order($con, $order_id, $site_sms[cons_admin_sms_content]);
//
//					@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $rows[phone], $str);
//					@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $site_sms[send_no], $str2);
//				}

				if($site_sms[sms_use] == "Y" && $site_sms[cons_sms] == "Y"){
					$str = trans_sms_order($con, $order_id, $site_sms[cons_sms_content]);
					@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $rows[phone], $str);
				}

				if($site_sms[sms_use] == "Y" && $site_sms[cons_admin_sms] == "Y"){
					$str2 = trans_sms_order($con, $order_id, $site_sms[cons_admin_sms_content]);
					@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $site_sms[send_no], $str2);
				}

				if($_SESSION[order_type] == "guest"){
					$_SESSION[dcode] = $_SESSION[member_id];
					$_SESSION[pcode] = $password;
					unset($_SESSION[member_id]);

					$return_mode = "guest_view";
				} else {
					$return_mode = "order_view";
				}

				if($address_type == "3"){
					$address_query = "SELECT count(*) as cnt FROM koweb_address WHERE member='{$_SESSION['member_id']}'";
					$address_result = mysqli_query($con,$address_query);
					$address_row = mysqli_fetch_array($address_result);
					$add_address = "";
					if($address_row['cnt'] == "0"){
						$add_address = ", main='1'";
					}

					mysqli_query($con,"INSERT INTO koweb_address SET member='{$_SESSION['member_id']}'
														,name='$name'
														, zip = '$zip'
														, address1 = '$address1'
														, address2 = '$address2'
														, phone = '$phone'
														$add_address");
				}
				$del_query = "DELETE FROM koweb_cart WHERE member_id='{$_SESSION['member_id']}' ";
				mysqli_query($con,$del_query);



				mysqli_commit($con);
				if($site_language == "eng"){
					alert("Your order has been completed.");
				}else{
					alert("주문이 완료 되었습니다.");
				}

				mysqli_close($con);
				url($common_queryString."?mode=$return_mode&orderid=$order_id");
			} else {
				if($site_language == "eng"){
					throw new Exception("Order failed.");
				}else{
					throw new Exception("주문이 실패하였습니다.");
				}
			}

		} catch(Exception $e){
			alert($e->getMessage());
			if(!$product_id) $product_id = $id;
			//롤백

			//결제 롤백
			if($pay_type != "무통장입금" && $pay_type != "paypal"){
				if($site_language == "eng"){
					$xpay->Rollback("Rollback processing due to store DB processing failure [TID:" . $xpay->Response("LGD_TID",0) . ",MID:" . $xpay->Response("LGD_MID",0) . ",OID:" . $xpay->Response("LGD_OID",0) . "]");
				}else{
					$xpay->Rollback("상점 DB처리 실패로 인하여 Rollback 처리 [TID:" . $xpay->Response("LGD_TID",0) . ",MID:" . $xpay->Response("LGD_MID",0) . ",OID:" . $xpay->Response("LGD_OID",0) . "]");
				}
			}
			mysqli_rollback($con);
			mysqli_close($con);


			//결제 롤백 끝
			url($common_queryString."?mode=view&id=$product_id");
			exit;
		}

	} else if($mode == "modify_proc"){


	} else if($mode == "order_cancel"){

		$query = "SELECT * FROM koweb_order WHERE order_id = '$orderid' AND member = '$_SESSION[member_id]'";
		$result = mysqli_query($con, $query);

		while($row = mysqli_fetch_array($result)){

			if($row[state] == "취소요청" || $row[state] == "취소완료"){
				if($site_language == "eng"){
					alert("This is a canceled payment.");
				}else{
					alert("이미 취소한 결제건 입니다.");
				}
				url($common_queryString."?mode=order_view&orderid=$orderid");
			}

			//일반회원이면 취소 후 상태변경 + 포인트 회수
			// $point_ = mysqli_query($con, "DELETE FROM koweb_point WHERE member = '$_SESSION[member_id]' AND order_id = '$orderid' LIMIT 1");

			$options_info_tmp = explode("^", $row[options_info]);
			$options_info_tmp = array_filter($options_info_tmp);


			foreach($options_info_tmp AS $odata){
				$options_info = explode("|", $odata);
				$options_info = array_filter($options_info);

				if($row[option_flag] != "Y"){
					//재고수량 원상복귀 (상품)
					$stock_product_ = mysqli_query($con, "UPDATE koweb_product SET stock_count = stock_count + $options_info[2] WHERE id='$row[ref_product]' LIMIT 1");

					$checked_options = mysqli_fetch_array(mysqli_query($con, "SELECT stock_count FROM koweb_product WHERE id='$row[ref_product]' LIMIT 1"));
					if($checked_options[stock_count] > 0){
						$options_stock_soldout = mysqli_query($con, "UPDATE koweb_product SET use_soldout='N' WHERE id='$row[ref_product]' LIMIT 1");
					}

				} else {
					//재고수량 원상복귀 (옵션)
					$options_stock_product_ = mysqli_query($con, "UPDATE koweb_option_detail SET stock = stock + $options_info[2] WHERE id='$options_info[0]' LIMIT 1");

					$checked_options = mysqli_fetch_array(mysqli_query($con, "SELECT stock FROM koweb_option_detail WHERE id='$options_info[0]' LIMIT 1"));
					if($checked_options[stock] > 0){
						$options_stock_soldout = mysqli_query($con, "UPDATE koweb_option_detail SET soldout='N' WHERE id='$options_info[0]' LIMIT 1");
					}

				}
			}

			$add_options_info_tmp = explode("^", $row[add_options_info]);
			$add_options_info_tmp = array_filter($add_options_info_tmp);

			foreach($add_options_info_tmp AS $adata){

				$add_options_info = explode("|", $adata);
				$add_options_info = array_filter($add_options_info);

				if($add_options_info[0]){
					//재고수량 원상복귀 (추가옵션)
					$options_stock_product_ = mysqli_query($con, "UPDATE koweb_option_detail SET stock = stock + $add_options_info[2] WHERE id='$add_options_info[0]' LIMIT 1");

					$add_checked_options = mysqli_fetch_array(mysqli_query($con, "SELECT stock FROM koweb_option_detail WHERE id='$add_options_info[0]' LIMIT 1"));
					if($add_checked_options[stock] > 0){
						$add_options_stock_soldout = mysqli_query($con, "UPDATE koweb_option_detail SET soldout='N' WHERE id='$add_options_info[0]' LIMIT 1");
					}
				}
			}
		}

		//2020-05-26 포인트 회수 등 원복시에 delete가 아닌 포인트 회수로 변경
		$point_query = "SELECT *,
							(SELECT SUM(product_price) AS total FROM koweb_order WHERE order_id= '$orderid') AS product_total_price,
							(SELECT SUM(add_point) AS add_point FROM koweb_order WHERE order_id= '$orderid') AS add_point
				   FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'";
		$point_result = mysqli_query($con,$point_query);
		$point_row = mysqli_fetch_array($point_result);

		if($row[state] == "취소완료"){
			$point_ = mysqli_query($con, "INSERT INTO koweb_point SET member='{$_SESSION['member_id']}' ,order_id='{$orderid}',point_type='상품구매 취소',point='+{$point_row['use_point']}',reg_date='{$reg_date}'");
		}


		//취소전 상태가 완료였다면, 적립포인트 회수
		if($point_row['state'] == "주문완료" || $point_row['state'] == "배송완료"){
			$point_2 = mysqli_query($con, "INSERT INTO koweb_point SET member='{$_SESSION['member_id']}' ,order_id='{$orderid}',point_type='구매적립 회수',point='-{$point_row['add_point']}',reg_date='{$reg_date}'");
		}

		//상태변경
		$update_ = mysqli_query($con, "UPDATE koweb_order SET state='취소요청' WHERE 1=1 AND member = '$_SESSION[member_id]' AND order_id='$orderid'");

		mysqli_commit($con);
		mysqli_close($con);

		if($site_language == "eng"){
			alert("Order cancellation processing is complete.");
		}else{
			alert("주문취소 처리가 완료되었습니다.");
		}
		url($common_queryString."?mode=order_view&orderid=$orderid");

	} else if($mode == "guest_order_cancel"){

		$dcode = $_SESSION[dcode];
		$pcode = $_SESSION[pcode];

		$query = "SELECT * FROM koweb_order WHERE order_id = '$orderid' AND member = '$dcode' AND password='$pcode'";
		$result = mysqli_query($con, $query);

		while($row = mysqli_fetch_array($result)){

			if($row[state] == "취소요청" || $row[state] == "취소완료"){
				if($site_language == "eng"){
					alert("This is a canceled payment.");
				}else{
					alert("이미 취소한 결제건 입니다.");
				}
				url($common_queryString."?mode=order_view&orderid=$orderid");
			}

			$options_info_tmp = explode("^", $row[options_info]);
			$options_info_tmp = array_filter($options_info_tmp);


			foreach($options_info_tmp AS $odata){
				$options_info = explode("|", $odata);
				$options_info = array_filter($options_info);

				if($row[option_flag] != "Y"){
					//재고수량 원상복귀 (상품)
					$stock_product_ = mysqli_query($con, "UPDATE koweb_product SET stock_count = stock_count + $options_info[2] WHERE id='$row[ref_product]' LIMIT 1");

					$checked_options = mysqli_fetch_array(mysqli_query($con, "SELECT stock_count FROM koweb_product WHERE id='$row[ref_product]' LIMIT 1"));
					if($checked_options[stock_count] > "0"){
						$options_stock_soldout = mysqli_query($con, "UPDATE koweb_product SET use_soldout='N' WHERE id='$row[ref_product]' LIMIT 1");
					}

				} else {
					//재고수량 원상복귀 (옵션)
					$options_stock_product_ = mysqli_query($con, "UPDATE koweb_option_detail SET stock = stock + $options_info[2] WHERE id='$options_info[0]' LIMIT 1");

					$checked_options = mysqli_fetch_array(mysqli_query($con, "SELECT stock FROM koweb_option_detail WHERE id='$options_info[0]' LIMIT 1"));
					if($checked_options[stock] > 0){
						$options_stock_soldout = mysqli_query($con, "UPDATE koweb_option_detail SET soldout='N' WHERE id='$options_info[0]' LIMIT 1");
					}

				}
			}

			$add_options_info_tmp = explode("^", $row[add_options_info]);
			$add_options_info_tmp = array_filter($add_options_info_tmp);

			foreach($add_options_info_tmp AS $adata){

				$add_options_info = explode("|", $adata);
				$add_options_info = array_filter($add_options_info);

				if($add_options_info[0]){
					//재고수량 원상복귀 (추가옵션)
					$options_stock_product_ = mysqli_query($con, "UPDATE koweb_option_detail SET stock = stock + $add_options_info[2] WHERE id='$add_options_info[0]' LIMIT 1");

					$add_checked_options = mysqli_fetch_array(mysqli_query($con, "SELECT stock FROM koweb_option_detail WHERE id='$add_options_info[0]' LIMIT 1"));
					if($add_checked_options[stock] > 0){
						$add_options_stock_soldout = mysqli_query($con, "UPDATE koweb_option_detail SET soldout='N' WHERE id='$add_options_info[0]' LIMIT 1");
					}
				}
			}
		}

		//상태변경
		$update_ = mysqli_query($con, "UPDATE koweb_order SET state='취소요청' WHERE 1=1 AND member = '$dcode' AND password = '$pcode' AND order_id='$orderid'");

		mysqli_commit($con);
		mysqli_close($con);

		if($site_language == "eng"){
			alert("Order cancellation processing is complete.");
		}else{
			alert("주문취소 처리가 완료되었습니다.");
		}
		url($common_queryString."?mode=guest_view&orderid=$orderid");
	}else if($mode == "return_request_proc"){

	if(!$orderid) error("잘못된 접근입니다.");
	$query = "INSERT INTO koweb_order_request SET orderid='{$orderid}' , title='{$title}' , contents='{$contents}' , type='return' , reg_date='{$reg_date}'";
	mysqli_query($connect,$query);
	$query = "SELECT * FROM koweb_order WHERE order_id='{$orderid}' AND order_info='P'";
	$result = mysqli_query($connect,$query);
	$row = mysqli_fetch_array($result);
	if($row[state] == "주문완료" || $row[state] == "배송완료"){
		if($row['add_point'] > 0)
		$point_2 = mysqli_query($connect, "INSERT INTO koweb_point SET member='{$row[member]}' ,order_id='{$orderid}',point_type='구매적립 회수',point='-{$row['add_point']}',reg_date='{$reg_date}'");
	}
	$query = "UPDATE koweb_order SET state='반품요청' WHERE order_id='{$orderid}'";
	mysqli_query($connect,$query);
	alert("반품신청이 완료되었습니다.\\n고객센터에서 확인 후 연락드리겠습니다.");
	url("/member/member.html?mode=order");

	}else if($mode == "trade_request_proc"){

		if(!$orderid) error("잘못된 접근입니다.");
		$query = "INSERT INTO koweb_order_request SET orderid='{$orderid}' , title='{$title}' , contents='{$contents}' , type='trade' , reg_date='{$reg_date}'";
		mysqli_query($connect,$query);
		$query = "SELECT * FROM koweb_order WHERE order_id='{$orderid}' AND order_info='P'";
		$result = mysqli_query($connect,$query);
		$row = mysqli_fetch_array($result);
		if($row[state] == "주문완료" || $row[state] == "배송완료"){
			if($row['add_point'] > 0)
			$point_2 = mysqli_query($connect, "INSERT INTO koweb_point SET member='{$row[member]}' ,order_id='{$orderid}',point_type='구매적립 회수',point='-{$row['add_point']}',reg_date='{$reg_date}'");
		}
		$query = "UPDATE koweb_order SET state='교환요청' WHERE order_id='{$orderid}'";
		mysqli_query($connect,$query);
		alert("교환신청이 완료되었습니다.\\n고객센터에서 확인 후 연락드리겠습니다.");
		url("/member/member.html?mode=order");

	}
