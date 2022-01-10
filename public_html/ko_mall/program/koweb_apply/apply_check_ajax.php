<? 
include_once  $_SERVER['DOCUMENT_ROOT'] . "/ko_admin/config_setup.php"; 

$phone = $phone1."-".$phone2."-".$phone3;
?>
<table class="table" style="font-size:13px;">
	<caption><?=$program[title]?></caption>
	<colgroup>
		<col style="width:5%"/>
		<col />
		<col style="width:25%"/>
	</colgroup>
	<thead>
		<tr>
			<th scope="col">No</th>
			<th scope="col">박람회명</th>
			<th scope="col">신청일자</th>
		</tr>
	</thead>
	<tbody>
		<?	
			$query = "SELECT person.ref_event AS ref_event, person.reg_date AS reg_date, info.* FROM koweb_event_person AS person, koweb_event_info AS info WHERE person.name = '$name' AND person.phone='$phone' AND person.ref_event = info.no ORDER BY person.no DESC";
			$total = mysql_num_rows(mysql_query("SELECT * FROM koweb_event_person WHERE name = '$name' AND phone='$phone'"));
			$result = mysql_query($query);
			$f_no = $total - $start;
			if($total > 0){

				while($row = mysql_fetch_array($result)){ 
				$event_info = mysql_fetch_array(mysql_query("SELECT * FROM koweb_event_info WHERE no = '$row[ref_event]'"));
				if($event_info)
			?>
				<tr>
					<td><?=$f_no--?></td>
					<td class="tal"><?=$event_info[event_title]?></td>
					<td><?=$event_info[reg_date]?></td>
				</tr>
			<? } ?>
		<? } else { ?>
			<tr>
				<td colspan="6">신청내역이 존재하지 않습니다.</td>
			</tr>
		<? } ?>
	</tbody>
</table>
