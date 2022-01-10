<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_product_category_config";
	//기본정보
	$no = substr($no, 0, -1);
	
	$default_query = mysqli_query($connect, "SELECT product.*, category.*, product.id AS pid FROM koweb_product_category_config AS category, koweb_product AS product WHERE category.state = 'Y' AND product.category_navi LIKE '$no%' AND product.category = category.id AND product.no != '$now_no'");

	$load_check_r = mysqli_num_rows($default_query);
if($load_check_r > 0){
		echo "<ul>";
		$refer_count = 1;
		while($row = mysqli_fetch_array($default_query)){
?>
			<li data-refer-info="<?=$row[pid]?>" data-refer-catev="<?=$row[category_navi]?>">
				<!-- 대표이미지 -->
				<span class="img" style="background-image:url(/upload/product/<?=$row[title_img]?>);"></span>
				<!-- 상품명 -->
				<em><i><?=$row[product_title]?></i></em>
				<a href="#" class="button white" data-btn-event="add_refer">추가</a>
			</li>
<? }  
		echo "</ul>";
		$refer_count++;
} else { ?>
<p class="no_data">검색 된 상품이 없습니다.</p>
<? } ?>