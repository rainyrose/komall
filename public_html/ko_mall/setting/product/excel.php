
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
			$title = iconv("utf-8", "euckr", "제품 엑셀다운로드") .  "(" . date("Y.m.d") . ")";
			@header("Content-type: application/vnd.ms-excel; charset=utf-8");
			@header("Content-Disposition: attachment; filename=$title.xls");
			@header("Pragma: no-cache");
			@header("Expires: 0");
		?>

		<?
//if(!$_SESSION['is_admin']) error("관리자만 접근 가능합니다.");
?>
		<table class="bbsList" border="1">
			<caption>제품 엑셀 다운로드</caption>
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
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
				<col />
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
					<th scope="col">제품분류</th>
					<th scope="col">판매여부</th>
					<th scope="col">노출여부</th>
			
					<th scope="col">노출순서</th>
					<th scope="col">제품명</th>
					<th scope="col">제품ID</th>
					<th scope="col">간략설명</th>
					<th scope="col">제조사</th>
					<th scope="col">원산지</th>
					<th scope="col">브랜드</th>
					<th scope="col">규격</th>
					<!--<th scope="col">내용</th>-->
					<th scope="col">제품가격</th>
					<th scope="col">과세구분</th>
					<th scope="col">포인트적립금</th>
					<th scope="col">최소구매수량</th>
					<th scope="col">최대구매수량</th>
					<th scope="col">품절여부</th>
					<th scope="col">재고수량</th>
					<th scope="col">대표이미지</th>
					<th scope="col">이미지1</th>
					<th scope="col">이미지2</th>
					<th scope="col">이미지3</th>
					<th scope="col">이미지4</th>
					<th scope="col">이미지5</th>
					<th scope="col">이미지6</th>
					<th scope="col">이미지7</th>
					<th scope="col">이미지8</th>
					<th scope="col">이미지9</th>
					<th scope="col">이미지10</th>
					<th scope="col">등록일</th>
				</tr>
			</thead>
			<tbody>
				<?
					$query =  "SELECT * FROM koweb_product ORDER BY sort ASC";
					$result = mysqli_query($connect, $query);
					$total = mysqli_num_rows($result);
					 while($row = mysqli_fetch_array($result)){
						 if($row[tax_type] == "1"){
							$row[tax_type] = "과세";
						 } else {
							$row[tax_type] = "비과세";
						 }

						 if(!$row[point_detail]){
							$row[point_detail] = "0";
						 }
				?>
				<tr>
					<td  style="text-align:center;"><?=$total--?></td>
					<td  style="text-align:center;"><?=$row[category]?></td>
					<td  style="text-align:center;"><?=$row[seller]?></td>
					<td  style="text-align:center;"><?=$row[shower]?></td>
					<td  style="text-align:center;"><?=$row[sort]?></td>
					<td  style="text-align:center;"><?=$row[product_title]?></td>
					<td  style="text-align:center;"><?=$row[id]?></td>
					<td  style="text-align:center;"><?=$row[simple_info]?></td>
					<td  style="text-align:center;"><?=$row[manufacturer]?></td>
					<td  style="text-align:center;"><?=$row[origin]?></td>
					<td  style="text-align:center;"><?=$row[brand]?></td>
					<td  style="text-align:center;"><?=$row[model]?></td>
					<!--<td  style="text-align:center;"><?=$row[web_content]?></td>-->
					<td  style="text-align:center;"><?=$row[price]?></td>
					<td  style="text-align:center;"><?=$row[tax_type]?></td>
					<td  style="text-align:center;"><?=number_format($row[point_detail])?>%</td>
					<td  style="text-align:center;"><?=$row[min_count]?></td>
					<td  style="text-align:center;"><?=$row[max_count]?></td>
					<td  style="text-align:center;"><?=$row[use_soldout]?></td>
					<td  style="text-align:center;"><?=$row[stock_count]?></td>
					<td  style="text-align:center;"><?=$row[title_img]?></td>
					<td  style="text-align:center;"><?=$row[img_1]?></td>
					<td  style="text-align:center;"><?=$row[img_2]?></td>
					<td  style="text-align:center;"><?=$row[img_3]?></td>
					<td  style="text-align:center;"><?=$row[img_4]?></td>
					<td  style="text-align:center;"><?=$row[img_5]?></td>
					<td  style="text-align:center;"><?=$row[img_6]?></td>
					<td  style="text-align:center;"><?=$row[img_7]?></td>
					<td  style="text-align:center;"><?=$row[img_8]?></td>
					<td  style="text-align:center;"><?=$row[img_9]?></td>
					<td  style="text-align:center;"><?=$row[img_10]?></td>
					<td  style="text-align:center;"><?=$row[reg_date]?></td>
				</tr>
			<? } ?>

			</tbody>
		</table>
</body>
</html>
