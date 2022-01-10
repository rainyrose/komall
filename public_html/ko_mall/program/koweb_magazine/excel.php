<? include_once  $_SERVER['DOCUMENT_ROOT'] . "/head.php"; ?>
<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>-</title>
		<style>
			table { width:1800px; font-size:13px; border:0.5px solid black; }
			th { background-color:#e9eef5;}
		</style>
	</head>
	<body>
	<?
		$program_table = "koweb_magazine";
		$title = iconv("utf-8", "euckr", "매거진 신청자 목록_") . date("Y.m.d");
		header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
		header("Content-Disposition: attachment; filename=\"$title.xls\"");
		header("Pragma: no-cache");
		header("Expires: 0");
	?>
	<table border=1>
		<colgroup>
			<col style="width:7%"/>
			<col style="width:8%"/>
			<col style="width:8%" />
			<col style="width:10%"/>
			<col />
			<col style="width:12%"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="col">No.</th>
				<th scope="col">신청유형</th>
				<th scope="col">ID</th>
				<th scope="col">신청자</th>
				<th scope="col">주소</th>
				<th scope="col">등록일</th>
			</tr>
		</thead>
		<tbody>
			<?
				if($search_cat){
					$WHERE .= "AND magazine_type='$search_cat'";
				}

				if($keyword){
					if($search_key == ""){
						$WHERE .= "AND id LIKE '%$keyword%' OR name LIKE '%keyword%'";
					} else {
						$WHERE .= "AND $search_key LIKE '%$keyword%'";
					}
				}

				$total_query = "SELECT * FROM $program_table WHERE 1=1 $WHERE ORDER BY no DESC";
				$result = mysqli_query($connect, $total_query);
				$total = mysqli_num_rows($result);
				$query = "SELECT * FROM $program_table WHERE 1=1 $WHERE ORDER BY no DESC";
				$result2 = mysqli_query($connect, $query);
				$f_no = $total - $start;
				if($total > 0){
					while($row = mysqli_fetch_array($result2)){ 
					?>
					<tr>
						<td><?=$f_no--?></td>
						<td><?=$row[magazine_type]?></td>
						<td><?=$row[id]?></td>
						<td><?=$row[name]?></td>
						<td><?=$row[zip]?> <?=$row[address1]?> <?=$row[address2]?> </td>
						<td><?=$row[reg_date]?></td>
					</tr>
				<? } ?>
			<? } else { ?>
				<? 
					if($is_admin) $col = "6";
					else $col = "6";
				?>
				<tr>
					<td colspan="<?=$col?>">등록된 데이터가 없습니다.</td> 
				</tr>
			<? } ?>
		</tbody>
	</table>
</body>
</html>







