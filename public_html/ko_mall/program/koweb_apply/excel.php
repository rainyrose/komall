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
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/config_setup.php";  
		include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/auth_manager.php";  
		$event_ = mysql_fetch_array(mysql_query("SELECT * FROM $table WHERE no='$event'"));
		
		$title = iconv("utf-8", "euckr", $event_[event_title]) .  "(" . date("Y.m.d") . ")";
		
		@header("Content-type: application/vnd.ms-excel; charset=utf-8"); 
		@header("Content-Disposition: attachment; filename=\"$title.xls\"");
		@header("Pragma: no-cache");
		@header("Expires: 0");
	?>
	<h2><?=$event_[event_title]?></h2>
	<table border=1>
	<caption><?=$program[title]?></caption>
	<colgroup>
		<col style="width:5%"/>
		<col style="width:8%"/>
		<col style="width:12%"/>
		<col style="width:8%"/>
		<col style="width:10%"/>
		<col style="width:10%"/>
		<col />
		<col style="width:6%"/>
		<col style="width:12%"/>
	</colgroup>
	<thead>
		<tr>
			<th scope="col" style="background-color:#e9eef5;">No</th>
			<th scope="col" style="background-color:#e9eef5;">성명</th>
			<th scope="col" style="background-color:#e9eef5;">회사명</th>
			<th scope="col" style="background-color:#e9eef5;">직위</th>
			<th scope="col" style="background-color:#e9eef5;">이메일</th>
			<th scope="col" style="background-color:#e9eef5;">연락처</th>
			<th scope="col" style="background-color:#e9eef5;">주소</th>
			<th scope="col" style="background-color:#e9eef5;">직업</th>
			<th scope="col" style="background-color:#e9eef5;">등록일</th>
		</tr>
	</thead>
	<tbody>
		<?	

			if($key && $keyword){
				$WHERE .= "AND $key LIKE '%$keyword%'";
			}

			$query = "SELECT * FROM koweb_event_person WHERE 1=1 AND ref_event='$event' $WHERE ORDER BY no DESC";
			$total = mysql_num_rows(mysql_query("SELECT * FROM koweb_event_person WHERE 1=1 AND ref_event='$event' $WHERE"));
			$result = mysql_query($query);
			$f_no = $total - $start;
			while($row = mysql_fetch_array($result)){ 
		?>
		<tr>
			<td><?=$f_no--?></td>
			<td><?=$row[name]?></a> </td>
			<td><?=$row[company]?></td>
			<td><?=$row[rank]?></td>
			<td><?=$row[email]?></td>
			<td><?=$row[phone]?></td>
			<td><?=$row[zip]?> <?=$row[address1]?> <?=$row[address2]?></td>
			<td><?=$row[job]?></td>
			<td><?=$row[reg_date]?></td>
			
		</tr>
	<? } ?>
	</tbody>
</table>
</body>
</html>







