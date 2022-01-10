
<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>-</title>
	</head>
	<body>
		<?
			include_once  $_SERVER['DOCUMENT_ROOT'] . "/head.php";
			include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_mall/auth_manager.php";
			$title = iconv("utf-8", "euckr", "주문 엑셀다운로드") .  "(" . date("Y.m.d") . ")";
			@header("Content-type: application/vnd.ms-excel; charset=utf-8");
			@header("Content-Disposition: attachment; filename=$title.xls");
			@header("Pragma: no-cache");
			@header("Expires: 0");
		?>

		<?
//if(!$_SESSION['is_admin']) error("관리자만 접근 가능합니다.");
?>
		<table class="bbsList" border="1">
			<caption>주문 엑셀 다운로드</caption>
			<colgroup>
				<col style="width:7%"/>
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
                <col />
			</colgroup>
			<thead>
				<tr>
					<th scope="col">No.</th>
                    <th scope="col">주문번호</th>
					<th scope="col">회원이름</th>
					<th scope="col">회원아이디</th>
					<th scope="col">회원전화번호</th>
                    <th scope="col">회원주소</th>
                    <th scope="col">주문자</th>
                    <th scope="col">주문전화번호</th>
                    <th scope="col">주문주소</th>
                    <th scope="col">주문 상품명</th>
				</tr>
			</thead>
			<tbody>
				<?
                    if($keyword){
                        $WHERE[] = " $search_key like '%$keyword%'";
                    }
                    if($start_date){
                        $start_date_tmp = $start_date . " 00:00:00";
                        $WHERE[] =  " reg_date >= '$start_date_tmp'";
                    }

                    if($end_date){
                        $end_date_tmp = $end_date . " 23:59:59";
                        $WHERE[] = " reg_date <= '$end_date_tmp'";
                    }

                    if($pay_type) {
                        $WHERE[] = " pay_type = '$pay_type'";
                    }

                    if($order_state){
                        $WHERE[] = " state = '$order_state'";
                    }

                    $WHERE_str = join(" AND ",$WHERE);
                    if($WHERE_str) $WHERE_str = " AND ".$WHERE_str;
					$p_query =  "SELECT * FROM koweb_order WHERE order_info='P' {$WHERE_str} ORDER BY reg_date DESC, no DESC";
					$p_result = mysqli_query($connect, $p_query);
					$total = mysqli_num_rows($p_result);
                    $total = $total+1;
					 while($p_row = mysqli_fetch_array($p_result)){
                         $query = "SELECT * FROM koweb_order WHERE order_id='{$p_row[order_id]}' ORDER BY order_info DESC,no ASC";
                         $result = mysqli_query($connect,$query);
                         $total = $total - 1;
                     while($row = mysqli_fetch_array($result)){
                         $member = mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM koweb_member WHERE id='{$row['member']}'"));

                         $options_ = explode("^", $row[options_info]);
                         $add_options_ = explode("^", $row[add_options_info]);

                         $options_ = array_filter($options_);
                         $add_options_ = array_filter($add_options_);

                         $product_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_product WHERE id='$row[ref_product]' LIMIT 1"));
				?>
				<tr>
					<td  style="text-align:center;mso-number-format:'\@';"><?=$row[order_info] == "P" ? $total : ""?></td>
                    <td  style="text-align:center;mso-number-format:'\@';"><?=$row[order_id]?></td>
					<td  style="text-align:center;"><?=$member[name]?></td>
					<td  style="text-align:center;"><?=$row[order_info] == "P" ? $row[member] : ""?></td>
					<td  style="text-align:center;"><?=$member[phone]?></td>
					<td  style="text-align:center;"><?=$row[order_info] == "P" ? "[".$member[zip]."] ".$member[address1]." ".$member[address2] : ""?></td>
					<td  style="text-align:center;"><?=$row[name]?></td>
					<td  style="text-align:center;"><?=$row[phone]?></td>
                    <td  style="text-align:center;"><?=$row[order_info] == "P" ? "[".$row[zip]."]".$row[address1]." ".$row[address2] : ""?></td>
                    <td  style="text-align:center;">
                    <?
                    foreach($options_ AS $o){
                        $options_detail = explode("|", $o);
                    echo $product_[product_title];
					if($product_[id] != $options_detail[0]){
                    	echo "[".$options_detail[1]."]"; //옵션명
					}
                    $options_detail[2]; //갯수
                    } ?>
                    </td>
				</tr>

                <?
                foreach($add_options_ AS $ao){
                        $add_options_detail = explode("|", $ao);
                        //$add_options_detail = [0]:추가옵션아이디, [1]추가옵션명, [2]수량, [3]적립포인트, [4]개별금액, [5] 총액
                ?>
                <tr>
                    <td  style="text-align:center;"></td>
                    <td  style="text-align:center;mso-number-format:'\@';"><?=$row[order_id]?></td>
                    <td  style="text-align:center;"></td>
                    <td  style="text-align:center;"></td>
                    <td  style="text-align:center;"></td>
                    <td  style="text-align:center;"></td>
                    <td  style="text-align:center;"></td>
                    <td  style="text-align:center;"></td>
                    <td  style="text-align:center;"></td>
                    <td  style="text-align:center;">[추가옵션 : <?=$add_options_detail[1]?>]</td>
                </tr>
                <? } ?>


			<? } ?>
            <? } ?>

			</tbody>
		</table>
</body>
</html>
