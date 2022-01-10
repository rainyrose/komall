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
			$list_query = "SELECT * FROM koweb_online_config WHERE id='$online_id'";
			$list_result = mysqli_query($connect, $list_query);
			$list_ = mysqli_fetch_array($list_result);

			$title = iconv("utf-8", "euckr", $list_[title]) .  "(" . date("Y.m.d") . ")";
			@header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
			@header("Content-Disposition: attachment; filename=$title.xls");
			@header("Pragma: no-cache");
			@header("Expires: 0");


			 for($i = 1; $i <= 10; $i++){ 
				$variable_ = $list_["variable_".$i];
				$variable_ = explode("|", $variable_);
				$tmp_name = $variable_[0];
				$tmp_type = $variable_[1];
				$tmp_state = $variable_[3];
				$tmp_id = $variable_[4];
				$tmp_view = $variable_[5];

				if($tmp_name){
					$view_array[] = $tmp_name; 
				}
				$row_array[] = "variable_".$i;
			}

		?>
		<table class="bbsList">
			<caption><?=$online[title]?></caption>
			<colgroup>
				<col style="width:7%"/>
				<? for ($j = 0; $j < count($view_array); $j++){ ?>
					<col />
				<? } ?>
					<col />
					<col />
					<col />
					<col />
			</colgroup>
			<thead>
				<tr>
					<th scope="col">No.</th>
					<? foreach ($view_array as $value){ ?>
						<th scope="col"><?=$value?></th>
					<? } ?>
						<th scope="col">전화번호</th>
						<th scope="col">이메일</th>
						<th scope="col">주소</th>
						<th scope="col">등록일자</th>
				</tr>
			</thead>
			<tbody>
				<?
					$query = "SELECT * FROM $online_table";
					$result = mysqli_query($connect, $query);
					$total = mysqli_num_rows($result);
					 while($row = mysqli_fetch_array($result)){ 
				?>
				<tr>
					<td><?=$total--?></td>
					<? foreach($row_array as $value){ ?>
					<?	if($row[$value]){ ?>
						<td><?=$row[$value]?></td>
					<? } ?>
					<? } ?>
						<td><?=$row[phone]?></td>
						<td><?=$row[email]?></td>
						<td><?=$row[zip]?> <?=$row[address1]?> <?=$row[address2]?></td>
						<td><?=$row[reg_date]?></td>
				</tr>
			<? } ?>
					
			</tbody>
		</table>
</body>
</html>







