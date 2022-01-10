<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_product_category_config";
	//기본정보
	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE id = '$no'"));
	$load_check_ = mysqli_query($connect, "SELECT * FROM $setting_table WHERE state = 'Y' AND ref_no = '$default[no]' ORDER BY sort ASC");
	$load_check_r = mysqli_num_rows($load_check_);
	if($load_check_r > 0){
?>
	<select name="<?=$default[id]?>" id="<?=$default[id]?>" data-<?=$tmp_type?> data-cate="<?=$tmp_type?>">
		<option value="">선택해주세요</option>
		<? 
			$load_category_result = mysqli_query($connect, "SELECT * FROM $setting_table WHERE state = 'Y' AND ref_no = '$default[no]' AND no != '$default[no]' ORDER BY sort ASC");
			while($load_category = mysqli_fetch_array($load_category_result)){
		?>
			<option value="<?=$load_category[id]?>"><?=$load_category[title]?></option>
		<? } ?>
	</select>
<? } ?>