<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_order";
	$reg_date = date("Y-m-d H:i:s");

	$orderid = $target;

	if(!$target){error("비정상적인접근"); exit;}
	if(!$orderid){error("비정상적인접근"); exit;}
	if(!$data){error("비정상적인접근"); exit;}

	//완료 -> 배송으로 상태 변경시, 구매적립된 포인트를 원복, 및 완료처리될때도 이미 구매적립되있는부분 제거
	// $query = "SELECT (SELECT SUM(add_point) AS point FROM koweb_order WHERE order_id='$orderid') AS point, koweb_order.* FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'";
	// $result2 = mysqli_query($con, $query);
	// $rows = mysqli_fetch_array($result2);
	// mysqli_query($con, "DELETE FROM koweb_point WHERE member='$rows[member]' AND order_id='$orderid' AND point_type='구매적립'");

	$default = mysqli_fetch_array(mysqli_query($connect,"SELECT *,
						(SELECT SUM(product_price) AS total FROM koweb_order WHERE order_id= '$orderid') AS product_total_price,
						(SELECT SUM(add_point) AS add_point FROM koweb_order WHERE order_id= '$orderid') AS add_point
			   FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'"));

	if($data == "취소완료" || $data == "반품완료"){

		$query = "SELECT * FROM koweb_order WHERE order_id = '$orderid'";
		$result2 = mysqli_query($connect, $query);
		while($row = mysqli_fetch_array($result2)){

			if($row[state] == $data ){
				alert("이미 ".$data."한 결제건 입니다.");
				url($common_queryString."?mode=order_view&orderid=$orderid");
			}

			//일반회원이면 취소 후 상태변경 + 포인트 회수
			// $point_ = mysqli_query($connect, "DELETE FROM koweb_point WHERE order_id = '$orderid' LIMIT 1");

			//상태변경
			mysqli_query($connect,"SET AUTOCOMMIT=0");
			mysqli_query($connect,"START TRANSACTION");
			$update_ = mysqli_query($connect, "UPDATE koweb_order SET state='$data' WHERE 1=1 AND order_id='$orderid' LIMIT 1");


			$options_info_tmp = explode("^", $row[options_info]);
			$options_info_tmp = array_filter($options_info_tmp);

			foreach($options_info_tmp AS $odata){
				$options_info = explode("|", $odata);
				$options_info = array_filter($options_info);

				if($row[option_flag] != "Y"){
					//재고수량 원상복귀 (상품)
					$stock_product_ = mysqli_query($connect, "UPDATE koweb_product SET stock_count = stock_count + $options_info[2] WHERE id='$row[ref_product]' LIMIT 1");

				} else {
					//재고수량 원상복귀 (옵션)
					$options_stock_product_ = mysqli_query($connect, "UPDATE koweb_option_detail SET stock = stock + $options_info[2] WHERE id='$options_info[0]' LIMIT 1");
				}
			}


			$add_options_info_tmp = explode("^", $row[add_options_info]);
			$add_options_info_tmp = array_filter($add_options_info_tmp);

			foreach($add_options_info_tmp AS $adata){
				$add_options_info = explode("|", $adata);
				$add_options_info = array_filter($add_options_info);

				if(count($add_options_info) > 0){
					//재고수량 원상복귀 (추가옵션)
					$options_stock_product_ = mysqli_query($connect, "UPDATE koweb_option_detail SET stock = stock + $add_options_info[2] WHERE id='$add_options_info[0]' LIMIT 1");
				}
			}
		}


		//2020-05-26 포인트 회수 등 원복시에 delete가 아닌 포인트 회수로 변경
		if($default['use_point'] > 0)
		$point_ = mysqli_query($connect, "INSERT INTO koweb_point SET member='{$default['member']}' ,order_id='{$orderid}',point_type='상품구매 취소',point='+{$default['use_point']}',reg_date='{$reg_date}'");


		//취소전 상태가 완료였다면, 적립포인트 회수
		if($default['state'] == "주문완료" || $default['state'] == "배송완료"){
			if($default['add_point'] > 0)
			$point_2 = mysqli_query($connect, "INSERT INTO koweb_point SET member='{$default['member']}' ,order_id='{$orderid}',point_type='구매적립 회수',point='-{$default['add_point']}',reg_date='{$reg_date}'");
		}

		if($data == "취소완료" && $default[pay_type] == "신용카드"){

			$_POST["CST_MID"] = $site_pay[uplus_shopid];
			$_POST["LGD_TID"] = $default[order_tid];
			$refund_price = $default[pay_price];
			include $_SERVER['DOCUMENT_ROOT']."/ko_mall/lguplus/refund.php";
			if(!$refund_flag){
				mysqli_query($connect,"ROLLBACK");
			}
		}
		mysqli_query($connect,"COMMIT");

	} else if($data == "주문완료" || $data == "배송완료"){

		if(!$default[order_id]){

			error("에러");
			exit;
		}
		if($default[order_type] == "member"){
			if($default['add_point'] > 0)
			$update_2 = mysqli_query($connect, "INSERT koweb_point SET member='$default[member]', order_id='$orderid', point_type='구매적립', point='$default[add_point]', reg_date='$reg_date'");
		}

	}else{
		if($data == "입금대기"){

			if($site_sms[sms_use] == "Y" && $site_sms[cons_sms] == "Y"){
				$str = trans_sms_order($connect, $orderid, $site_sms[cons_sms_content]);
				$str2 = trans_sms_order($connect, $orderid, $site_sms[cons_admin_sms_content]);

				@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $default[phone], $str);
				@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $site_sms[send_no], $str2);
			}

		} else if($data == "결제완료"){

			if($site_sms[sms_use] == "Y" && $site_sms[deposit_sms] == "Y"){
				$str = trans_sms_order($connect, $orderid, $site_sms[deposit_sms_content]);
				@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $default[phone], $str);
			}

		} else if($data == "배송중"){

			if($site_sms[sms_use] == "Y" && $site_sms[deli_sms] == "Y"){
				$str = trans_sms_order($connect, $orderid, $site_sms[deli_sms_content]);
				@sms_send($site[sms_id], $site[sms_key], $site_sms[send_no], $default[phone], $str);
			}

		}
		//포인트 삭제
		//$point_delete = mysqli_query($connect, "DELETE FROM koweb_point WHERE member = '$rows[member]' AND order_id = '$rows[order_id]'");
		if($default['state'] == "주문완료" || $default['state'] == "배송완료"){
			if($default['add_point'] > 0)
			$point_2 = mysqli_query($connect, "INSERT INTO koweb_point SET member='{$default[member]}' ,order_id='{$orderid}',point_type='구매적립 회수',point='-{$default['add_point']}',reg_date='{$reg_date}'");
		}

	}


	$query = "UPDATE $setting_table SET state='$data' WHERE order_id = '$target'";
	$result = mysqli_query($connect, $query);

	if($result){
		echo "상태값이 변경되었습니다.";
	} else {
		echo "오류가 발생하였습니다.";
	}

?>
