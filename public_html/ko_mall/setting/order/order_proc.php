<?
//error_reporting(E_ALL);

//ini_set("display_errors", 1);
	$con = mysqli_connect($host, $user, $passwd, $dataname) or die("not connected");
	mysqli_autocommit($con, FALSE);
	mysqli_begin_transaction($con, MYSQLI_TRANS_START_READ_WRITE);

	$reg_date = date("Y-m-d H:i:s");
	$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/product/";
	if($mode == "modify_proc"){

		$phone = $phone1."-".$phone2."-".$phone3;

		if($pay_price != "") $add_pay_price = ", pay_price = '$pay_price'";
		if($bank_info != "") $add_bank_info = ", bank_info = '$bank_info'";
		if($bank_name != "") $add_bank_name = ", bank_name = '$bank_name'";
		if($use_point != "") $add_use_point = ", use_point = '$use_point'";
		if($return_price != "") $return_price = ", return_price = '$return_price'";

		if($tel_product_price != "") $add_tel_product_price = ", product_price = '$tel_product_price'";
		if($tel_add_point != "") $add_tel_add_point = ", add_point = '$tel_add_point'";

		if($deli_company != "") $add_deli_company = ", deli_company='$deli_company'";
		if($deli_code != "") $add_deli_code = ", deli_code='$deli_code'";
		if($deli_price != "") $add_deli_price = ", deli_price='$deli_price'";
		if($deli_add_price != "") $add_deli_add_price = ", deli_add_price='$deli_add_price'";


		if($pay_date_1 && $pay_date_2 && $pay_date_3 && $pay_date_4) {
			$pay_date = $pay_date_1." ".$pay_date_2.":".$pay_date_3 . ":" .$pay_date_4;
			$add_pay_date = ", pay_date = '$pay_date'";
		}


		if($deli_date1 && $deli_date2 && $deli_date3 && $deli_date4) {
			$deli_date = $deli_date1." ".$deli_date2.":".$deli_date3 . ":" .$deli_date4;
			$add_deli_date = ", deli_date = '$deli_date'";
		}
		$add_admin_memo = ",admin_memo = '$admin_memo'";


		//포인트 차감
		// $default = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_order WHERE order_id = '$orderid' AND order_info='P' LIMIT 1"));
		$default = mysqli_fetch_array(mysqli_query($con,"SELECT *,
							(SELECT SUM(product_price) AS total FROM koweb_order WHERE order_id= '$orderid') AS product_total_price,
							(SELECT SUM(add_point) AS add_point FROM koweb_order WHERE order_id= '$orderid') AS add_point
				   FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'"));

		//완료 -> 배송으로 상태 변경시, 구매적립된 포인트를 원복, 및 완료처리될때도 이미 구매적립되있는부분 제거
		// L.78 구매적립 -> 상품구매로 변경
		// mysqli_query($con, "DELETE FROM koweb_point WHERE member='$rows[member]' AND order_id='$orderid' AND point_type='구매적립'");

		//포인트 변경시 작업
		if($default[state] != "취소완료" && $default[state] != "반품완료"){
			if(($default[use_point] != $use_point)){

				$point_result = mysqli_fetch_array(mysqli_query($con, "SELECT SUM(point) AS total FROM koweb_point WHERE member = '$default[member]' AND order_id != '$default[order_id]'"));

				if($point_result[total] < $use_point){

					alert("포인트가 부족합니다.");
					url($common_queryString."?orderid=$orderid");

				} else {

					try{
						//먼저 삭제
						// $point_delete = mysqli_query($con, "DELETE FROM koweb_point WHERE member = '$default[member]' AND order_id = '$default[order_id]'");
						// if(!$point_delete){
						// 	throw new Exception("point error1");
						// }

						//다시 넣기
						$point_diff = $default[use_point] - $use_point;
						if($point_diff != 0){
							$update_point = mysqli_query($con, "INSERT INTO koweb_point SET member='$default[member]', order_id='$default[order_id]', point_type='포인트 변동', point='$point_diff', reg_date='$reg_date'");
							if(!$update_point){
								throw new Exception("point error2");
							}
						}

					} catch(Exception $e){
						mysqli_rollback($con);
						error("포인트 작업중 에러가 발생하였습니다." . $e->getMessage());
						exit;
					}

				}
			}
		}

		//값 저장
		try{
			$results = mysqli_query($con, "UPDATE koweb_order SET state='$state'
																	$add_pay_price
																	$add_bank_info
																	$add_bank_name
																	$add_use_point
																	$return_price
																	$add_tel_product_price
																	$add_tel_add_point
																	$add_deli_company
																	$add_deli_code
																	$add_deli_price
																	$add_pay_date
																	$add_deli_date
																	$add_deli_add_price
																	$add_admin_memo
																WHERE member='$default[member]' AND order_id='$default[order_id]' AND order_info='P'");

			// $query = "SELECT (SELECT SUM(add_point) AS point FROM koweb_order WHERE order_id='$orderid') AS point, koweb_order.* FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'";
			// $result2 = mysqli_query($con, $query);
			// $rows = mysqli_fetch_array($result2);

			if($state == "주문완료" || $state == "배송완료"){
				if($default[state] != "주문완료" && $default[state] != "배송완료"){

					// //포인트 ++
					// if(!$rows[order_id]){
					// 	throw new Exception("point add order_id error");
					// }

					if($default[order_type] == "member"){
						// mysqli_query($connect, "DELETE FROM koweb_point WHERE member='$rows[member]' AND order_id='$orderid'");
						if($default['add_point'] > 0){
							$update_2 = mysqli_query($con, "INSERT koweb_point SET member='$default[member]', order_id='$orderid', point_type='구매적립', point='$default[add_point]', reg_date='$reg_date'");
							if(!$update_2){
								throw new Exception("point add update error");
							}
						}
					}
				}

			} else if($state == "취소완료"){
				// 환불 트랜지션
				mysqli_query($connect,"SET AUTOCOMMIT=0");
				mysqli_query($connect,"START TRANSACTION");

				if($default[state] != "취소완료"){
					//재고수정 옵션
					if($default[option_flag] == "Y"){
						$stock_target_table = "koweb_option_detail";
						$stock_target = "stock";
					} else {
						$stock_target_table = "koweb_product";
						$stock_target = "stock_count";
					}

					$target_tmp = $default[options_info];
					$target_ = explode("^", $target_tmp);

					foreach($target_ AS $t){
						$t = explode("|", $t);
						//옵션사용하는애인가?

						//재고수량 업데이트 (복구)
						$update3_ = mysqli_query($con, "UPDATE $stock_target_table SET $stock_target = $stock_target + $t[2] WHERE id = '$t[0]' LIMIT 1");

						if(!$update3_){
							throw new Exception("update3 error1 UPDATE $stock_target_table SET $stock_target = $stock_target + $t[2] WHERE id = '$t[0]' LIMIT 1");
						}
					}

					//재고수정 추가 옵션
					if($default[add_options_info]){
						$target_tmp = $default[add_options_info];
						$target_ = explode("^", $target_tmp);

						foreach($target_ AS $t){
							$t = explode("|", $t);
							//재고수량 업데이트 (복구)
							$update3_ = mysqli_query($con, "UPDATE koweb_option_detail SET stock = stock + $t[2] WHERE id = '$t[0]' LIMIT 1");

							if(!$update3_){
								throw new Exception("update3 error2 UPDATE koweb_option_detail SET stock = stock + $t[2] WHERE id = '$t[0]' LIMIT 1");
							}
						}
					}

					// //포인트 삭제
					// $point_delete = mysqli_query($con, "DELETE FROM koweb_point WHERE member = '$default[member]' AND order_id = '$default[order_id]'");
					// if(!$point_delete){
					// 	throw new Exception("point error1");
					// }

					//2020-05-26 포인트 회수 등 원복시에 delete가 아닌 포인트 회수로 변경
					if($default['use_point'] > 0){
						$point_ = mysqli_query($con, "INSERT INTO koweb_point SET member='{$default[member]}' ,order_id='{$orderid}',point_type='상품구매 취소',point='+{$default['use_point']}',reg_date='{$reg_date}'");
						if(!$point_){
							throw new Exception("point error1");
						}
					}

					//취소전 상태가 완료였다면, 적립포인트 회수
					if($default['state'] == "주문완료" || $default['state'] == "배송완료"){
						if($default['add_point'] > 0){
							$point_2 = mysqli_query($con, "INSERT INTO koweb_point SET member='{$default[member]}' ,order_id='{$orderid}',point_type='구매적립 회수',point='-{$default['add_point']}',reg_date='{$reg_date}'");
							if(!$point_2){
								throw new Exception("point error2");
							}
						}
					}

					if($state == "취소완료" && $default[pay_type] == "신용카드"){

						$_POST["CST_MID"] = $site_pay[uplus_shopid];
						$_POST["LGD_TID"] = $default[order_tid];
						$refund_price = $default[pay_price];
						include $_SERVER['DOCUMENT_ROOT']."/ko_mall/lguplus/refund.php";
						if(!$refund_flag){
							mysqli_query($connect,"ROLLBACK");
						}
					}
					mysqli_query($connect,"COMMIT");
				}
			} else {
				//기존 상태는 완료였으나, 상태를 완료,취사,환불 등이아닌 상태로 원복할때 구매적립 회수
				if($default['state'] == "주문완료" || $default['state'] == "배송완료"){
					if($default['add_point'] > 0){
						$point_2 = mysqli_query($con, "INSERT INTO koweb_point SET member='{$default[member]}' ,order_id='{$orderid}',point_type='구매적립 회수',point='-{$default['add_point']}',reg_date='{$reg_date}'");
						if(!$point_2){
							throw new Exception("point error2");
						}
					}
				}
				//포인트 삭제

				//2020-05-12 기가허브에서 주문관리-> 상태변경만해도 멤버 포인트가 초기화 되는현상. 위 if , else if 에서 예외처리가 됬다고 판단. 해당내용 주석처리.
				// $point_delete = mysqli_query($con, "DELETE FROM koweb_point WHERE member = '$default[member]' AND order_id = '$default[order_id]'");
				// if(!$point_delete){
				// 	throw new Exception("point error1");
				// }
			}

			if($results){
				mysqli_query($con, "UPDATE koweb_order SET state='$state' WHERE member='$default[member]' AND order_id='$default[order_id]'");

				foreach ($_POST[order_request_] as $key => $order_request_) {
					$update4_ = mysqli_query($con, "UPDATE koweb_order_request set comments='{$order_request_['comments']}' WHERE no='{$order_request_[no]}'");
					if(!$update4_){
						throw new Exception("update4 error");
					}
				}

				if($partial_cancle > 0){
					$_POST["CST_MID"] = $site_pay[uplus_shopid];
					$_POST["LGD_TID"] = $default[order_tid];
					$_POST["LGD_CANCELAMOUNT"] = $partial_cancle;				//부분취소 금액
					$refund_insert = mysqli_query($con, "INSERT INTO koweb_refund set order_id='{$orderid}',refund_price='{$partial_cancle}',reg_date='{$reg_date}'");
					if(!$refund_insert){
						throw new Exception("부분 취소(환불) 갱신에 실패하였습니다.");
					}
					$order_update = mysqli_query($con, "UPDATE koweb_order set return_price= return_price+'{$partial_cancle}' WHERE order_id='{$orderid}'");
					if(!$order_update){
						throw new Exception("주문서 부분 취소(환불) 갱신에 실패하였습니다.");
					}
					include $_SERVER['DOCUMENT_ROOT']."/ko_mall/lguplus/partial_refund.php";
					if(!$partial_refund_flag){
						throw new Exception("부분 취소(환불) 요청이 실패하였습니다.");
					}
				}

				mysqli_commit($con);
				alert("주문정보가 수정 되었습니다.");

				if($default[state] != $state){
					if($state == "입금대기"){
						$query = "SELECT * FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'";
						$result2 = mysqli_query($connect, $query);
						$rows = mysqli_fetch_array($result2);

						if($site_sms[sms_use] == "Y" && $site_sms[cons_sms] == "Y"){
							$str = trans_sms_order($connect, $orderid, $site_sms[cons_sms_content]);
							$str2 = trans_sms_order($connect, $orderid, $site_sms[cons_admin_sms_content]);
							@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $phone, $str);
							@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $site_sms[send_no], $str2);
						}

					} else if($state == "결제완료"){


						$query = "SELECT * FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'";
						$result2 = mysqli_query($connect, $query);
						$rows = mysqli_fetch_array($result2);

						if($site_sms[sms_use] == "Y" && $site_sms[deposit_sms] == "Y"){
							$str = trans_sms_order($connect, $orderid, $site_sms[deposit_sms_content]);
							@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no],$phone, $str);
						}

					} else if($state == "배송중"){

						$query = "SELECT * FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'";
						$result2 = mysqli_query($connect, $query);
						$rows = mysqli_fetch_array($result2);

						if($site_sms[sms_use] == "Y" && $site_sms[deli_sms] == "Y"){
							$str = trans_sms_order($connect, $orderid, $site_sms[deli_sms_content]);
							@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $phone, $str);
						}

					}
				}

			} else {
				throw new Exception("order SAVE error");
			}

		} catch(Exception $e){
				echo $e->getmessage();
				alert("주문정보가 정상적으로 수정되지 않았습니다.");
				//롤백
				mysqli_rollback($con);
				exit;
		}

		mysqli_close($con);
		url($common_queryString."&mode=order_view&orderid=$orderid");

	} else if($mode == "option_change_number" || $mode == "option_refund_number"){

		$query = "SELECT * FROM koweb_order WHERE no = '$no' LIMIT 1";
		$result = mysqli_query($con, $query);
		$row = mysqli_fetch_array($result);

		if($row[state] != "취소완료" && $row[state] != "반품완료"){

			try{


				if(!$row){
					throw new Exception("defailt data error");
				}

				if($change_type == "option"){

					$target_tmp = $row[options_info];
					$target2_tmp = $row[refund_info];
					$column_ =  "options_info";

				} else if($change_type == "add"){

					$target_tmp = $row[add_options_info];
					$target2_tmp = $row[add_refund_info];
					$column_ =  "add_options_info";

				} else {
					throw new Exception("change_type error");
					//에러발생
				}



				//optid == 옵션아이디, change_type == 옵션타입 ( 추가옵션 / 옵션 ) , $no = 옵션넘버
				$data_ = "";
				$target_ = explode("^", $target_tmp);
				$target2_ = explode("^", $target2_tmp);
				$product_price = 0;
				$refund_optid_col = array();
				if($mode == "option_refund_number"){
					$refund_number = $input_data;

					foreach($target_ AS $target_index => $t){
						$t = explode("|", $t);
						if($t[0] == $optid){
							$before_cnt = $t[2]+explode("|",$target2_[$target_index])[1];
							$input_data = $before_cnt-$refund_number;
						}
					}
				}
				// exit;
				foreach($target_ AS $target_index => $t){
					$t = explode("|", $t);
					if($t[0] == $optid){
						//옵션사용하는애인가?
						if($row[option_flag] == "Y"){

							//재고관련 변수정리
							$stock_target_table = "koweb_option_detail";
							$stock_target = "stock";

							//옵션정보 가져오기
							$options = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_option_detail WHERE id='$t[0]'"));

							//제품정보 가져오기
							$product = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_product WHERE no = '$options[ref_product]'"));

							//재고수량 체크
							if((($options[stock] + $t[2]) - $input_data) < 0){
								alert("재고 수량이 부족합니다.");
								throw new Exception("stock update error");
							}


							if($change_type != "add"){
								//옵션가격 ( 제품가격 + 옵션가격 )
								if($options[price_type] == "+"){
									$price = $product[price] + $options[price];
								} else {
									$price = $product[price] - $options[price];
									$t[4] = $price;
								}
							}else {
								$price = $options[price];
							}

							//총가격
							$t[5] = $price * $input_data;

							//포인트계산
							$t[3] = $t[5] * ($product[point_detail]/100);
							$product_price += $t[5];
						} else {

							//재고관련 변수정리
							$stock_target_table = "koweb_product";
							$stock_target = "stock_count";

							if($change_type == "option"){

								//옵션 사용 안하는 애인가??????
								$options = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_product WHERE id='$row[ref_product]'"));

								//제품정보 가져오기
								$product = $options;

							} else if($change_type == "add"){
								//옵션정보 가져오기
								$options = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_option_detail WHERE id='$t[0]'"));

								//제품정보 가져오기
								$product = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM koweb_product WHERE no = '$options[ref_product]'"));
							}


							//재고수량 체크
							if((($options[stock_count] + $t[2]) - $input_data) < 0){
								alert("재고 수량이 부족합니다.");
								throw new Exception("stock update error");
							}

							//옵션가격 ( 제품가격 )
							$price = $options[price];
							$t[4] = $price;

							//총가격
							$t[5] = $price * $input_data;

							//포인트계산
							$t[3] = $t[5] * ($product[point_detail]/100);
							$product_price += $t[5];
						}

						//아이디 | 제목 | 갯수 | 금액 | 개당가격 | 총금액
						$data_ .= $t[0] ."|".$t[1] . "|" . $input_data ."|".  $t[3]  ."|".  $t[4]."|". $t[5] ."|". $t[6]."^";
						//아이디 | 취소갯수
						$data2_ .= $t[0] ."|".$refund_number."^";


						//재고수량 업데이트 (복구)
						$update3_ = mysqli_query($con, "UPDATE $stock_target_table SET $stock_target = $stock_target + $t[2] WHERE no = '$options[no]' LIMIT 1");

						if(!$update3_){
							throw new Exception("update3 error");
						}

						//재고수량 업데이트 (차감)
						$update4_ = mysqli_query($con, "UPDATE $stock_target_table SET $stock_target = $stock_target - $input_data WHERE no = '$options[no]' LIMIT 1");

						if(!$update4_){
							throw new Exception("update4 error");
						}

					} else {
						$data_ .= $t[0] ."|".$t[1] . "|" . $t[2] ."|".  $t[3]  ."|".  $t[4]."|". $t[5]."^";
						if($target2_[$target_index]){
							$data2_ .= $target2_[$target_index]."^";
						}else{
							$data2_ .= $t[0]."|0^";
						}

					}

				}

				$data_ = substr($data_,0,-1);
				$data2_ = substr($data2_,0,-1);
				$refund_update_ = "";
				if($mode == "option_refund_number"){
					if($change_type == "option"){
						$refund_update_ = " , refund_info='{$data2_}'";
					} else if($change_type == "add"){
						$refund_update_ = " , add_refund_info='{$data2_}'";
					}
				}
				$update_ = mysqli_query($con, "UPDATE koweb_order SET $column_ = '$data_' {$refund_update_} WHERE no = '$no' LIMIT 1");

				if(!$update_){
					throw new Exception("update error");
				}

				$query2 = "SELECT * FROM koweb_order WHERE no = '$no' LIMIT 1";
				$result2 = mysqli_query($con, $query2);
				$row2 = mysqli_fetch_array($result2);
				$options_info = explode("^", $row2[options_info]);
				$add_options_info = explode("^", $row2[add_options_info]);


				//총 결제금액 업데이트

				//$row[product_price]
				$product_price = 0;
				$add_point = 0;
				foreach($options_info AS $oinf){
					$info = explode("|", $oinf);
					$product_price += $info[5];
					$add_point += $info[3];
				}

				foreach($add_options_info AS $aoinf){
					$ainfo = explode("|", $aoinf);
					$product_price += $ainfo[5];
					$add_point += $ainfo[3];
				}


				//포인트 적립 업데이트
				$add_point = $add_point;
				$update2_ = mysqli_query($con, "UPDATE koweb_order SET product_price = '$product_price' , add_point='$add_point' WHERE no = '$no' LIMIT 1");

				if(!$update2_){
					throw new Exception("update2 error");
				}

				$sum_query = "SELECT * FROM koweb_order WHERE order_id='{$row['order_id']}'";
				$sum_result = mysqli_query($con,$sum_query);
				$product_price = 0;
				$product_arr = array();
				while($sum_row = mysqli_fetch_array($sum_result)){
					$product_price += $sum_row['product_price'];
					$product_arr[] = $sum_row['ref_product'];
				}

				//배송비 업데이트
				$deli_price = get_delivery_price($con, $product_arr, $product_price);

				$update3_ = mysqli_query($con, "UPDATE koweb_order SET deli_price='$deli_price' WHERE order_id='{$row['order_id']}' AND order_info='P' LIMIT 1");

				if(!$update3_){
					throw new Exception("update3 error");
				}

				mysqli_commit($con);
				alert("수량이 정상적으로 수정 되었습니다.");

			} catch(Exception $e){

				alert("수량이 정상적으로 수정되지 않았습니다.");
				//echo $e->getMessage();
				//롤백
				mysqli_rollback($con);
				exit;
			}
		} else {
			alert("취소, 반품완료, 환불완료의 상태는 수량을 변경 할 수 없습니다.");
		}

		mysqli_close($con);
		url($common_queryString."&mode=order_view&orderid=$row[order_id]");
	}

?>
