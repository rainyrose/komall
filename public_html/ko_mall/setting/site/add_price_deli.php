<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_add_delivery_price";
	//기본정보


if($mode == "add"){

	$query = "INSERT $setting_table SET title='$title', start_zip = '$start_zip' , end_zip = '$end_zip' , price='$price'";
	$result = mysqli_query($connect, $query);

} else {
	
	$all_delete = explode("|", $deleted_data);
	foreach($all_delete AS $ad){
		$query = "DELETE FROM $setting_table WHERE no ='$ad'";
		$result = mysqli_query($connect, $query);
	}

}

	$rquery = "SELECT * FROM $setting_table ORDER BY no DESC";
	$rresult = mysqli_query($connect, $rquery);
	$total = mysqli_num_rows($rresult);
?>
<p>추가배송비 내역</p>
<div class="btn">
	<a href="#" class="button sm" data-btn-deli="add">추가</a>
	<a href="#" class="button sm white" data-btn-deli="del">선택삭제</a>
</div>
<table class="bbsList">
	<caption>추가배송비 내역</caption>
	<colgroup>
		<col style="width:7%;"/>
		<col/>
		<col style="width:25%;"/>
		<col style="width:20%;"/>
	</colgroup>
	<thead>
		<tr>
			<th scope="col"><div class="designCheck noText"><input type="checkbox" name="1" id="this_all" /><label for="this_all">전체선택</label></div></th>
			<th scope="col">지역명</th>
			<th scope="col">우편번호</th>
			<th scope="col">추가배송비</th>
		</tr>
	</thead>
	<tbody>
		<? if($total > 0){ ?>
		<? while($row = mysqli_fetch_array($rresult)){ ?>
			<tr>
				<td><div class="designCheck noText"><input type="checkbox" name="all_delete[]" id="all_<?=$row[no]?>" value="<?=$row[no]?>"/><label for="all_<?=$row[no]?>">한줄선택</label></div></td>
				<td><?=$row[title]?></td>
				<td><?=$row[start_zip]?> ~ <?=$row[end_zip]?></td>
				<td><?=number_format($row[price])?></td>
			</tr>
		<? } ?>
		<? } else { ?>
		<tr>
			<td colspan="4">등록 된 추가배송비 내역이 없습니다.</td>
		</tr>
		<? } ?>
	</tbody>
</table>					
