<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_order";
	$reg_date = date("Y-m-d H:i:s");

	$orderid = $target;

	if(!$target){error("비정상적인접근"); exit;}
	if(!$orderid){error("비정상적인접근"); exit;}
	if(!$data){error("비정상적인접근"); exit;}

	if($data == "취소" || $data == "환불완료" || $data == "반품완료"){

		$query = "SELECT * FROM koweb_order WHERE order_id = '$orderid' AND member = '$_SESSION[member_id]'";
		$result2 = mysqli_query($connect, $query);
		while($row = mysqli_fetch_array($result2)){

			if($row[state] == $data ){
				alert("이미 ".$data."한 결제건 입니다.");
				url($common_queryString."?mode=order_view&orderid=$orderid");
			}

			//일반회원이면 취소 후 상태변경 + 포인트 회수
			// $point_ = mysqli_query($connect, "DELETE FROM koweb_point WHERE order_id = '$orderid' AND member = '$_SESSION[member_id]' LIMIT 1");

			//상태변경
			$update_ = mysqli_query($connect, "UPDATE koweb_order SET state='$data', reg_date='$reg_date' WHERE 1=1 AND order_id='$orderid' AND member = '$_SESSION[member_id]' LIMIT 1");


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
		$point_query = "SELECT *,
							(SELECT SUM(product_price) AS total FROM koweb_order WHERE order_id= '$orderid') AS product_total_price,
							(SELECT SUM(add_point) AS add_point FROM koweb_order WHERE order_id= '$orderid') AS add_point
				   FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'";
		$point_result = mysqli_query($connect,$point_query);
		$point_row = mysqli_fetch_array($point_result);

		$point_ = mysqli_query($connect, "INSERT INTO koweb_point SET member='{$_SESSION['member_id']}' ,order_id='{$orderid}',point_type='상품구매 취소',point='+{$point_row['use_point']}',reg_date='{$reg_date}'");


		//취소전 상태가 완료였다면, 적립포인트 회수
		if($point_row['state'] == "완료"){
			$point_2 = mysqli_query($connect, "INSERT INTO koweb_point SET member='{$_SESSION['member_id']}' ,order_id='{$orderid}',point_type='구매적립 회수',point='-{$point_row['add_point']}',reg_date='{$reg_date}'");
		}

	} else if($data == "완료"){

		//포인트 ++
		$query = "SELECT (SELECT SUM(add_point) AS point FROM koweb_order WHERE order_id='$orderid') AS point, koweb_order.* FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'";
		$result2 = mysqli_query($connect, $query);
		$rows = mysqli_fetch_array($result2);

		if(!$rows[order_id]){

			error("에러");
			exit;
		}
		if($rows[order_type] == "member"){
			$update_2 = mysqli_query($connect, "INSERT koweb_point SET member='$rows[member]', order_id='$orderid', point_type='구매적립', point='$rows[point]', reg_date='$reg_date'");
		}
	} else {

		//포인트 ++
		$query = "SELECT (SELECT SUM(add_point) AS point FROM koweb_order WHERE order_id='$orderid') AS point, koweb_order.* FROM koweb_order WHERE order_id = '$orderid' AND order_info = 'P'";
		$result2 = mysqli_query($connect, $query);
		$rows = mysqli_fetch_array($result2);

		//포인트 삭제
		// $point_delete = mysqli_query($connect, "DELETE FROM koweb_point WHERE member = '$rows[member]' AND order_id = '$rows[order_id]'");
	}


	$query = "UPDATE $setting_table SET state='$data' WHERE order_id = '$target'";
	$result = mysqli_query($connect, $query);

	if($result){
		echo "상태값이 변경되었습니다.";
	} else {
		echo "오류가 발생하였습니다.";
	}

?>
