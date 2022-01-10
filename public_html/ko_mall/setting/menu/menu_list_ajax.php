<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_menu_config";
	$query = "SELECT * FROM $setting_table WHERE depth = '1' AND category='$category' ORDER BY sort ASC, ref_group ASC, depth ASC, ref_no ASC";
	$result = mysqli_query($connect, $query);
	$total = mysqli_num_rows($result);
	if($_POST[mode] == "tree") echo "<ul class=\"list\">";
	while($data = mysqli_fetch_array($result)){
		if($data[no] == $no){
			$depth_no = $data[depth];
		}
		print_menu($connect, $category, $_POST[mode], $setting_table, $data[ref_group], $data[ref_no]);

	}
	if($_POST[mode] == "tree") echo "</ul>";
?>
	<script type="text/javascript">

	var depth_no = "<?=$depth_no?>";

	$('.area_tree .list li').each(function(){
		if(!($(this).find("ul").length > 0)){
			$(this).children('.tree').removeClass('folder');
		}else{
			$(this).children('.tree').addClass('folder');
		}
	})

	$('.area_tree .list li a.folder').on('click',function(e){
		if($(this).parent().find('ul').length > 0){
			e.preventDefault();
			if($(this).parent().hasClass('active')){
				$(this).parent().removeClass('active');	
				if($(this).parent().find('ul').length > 0){
					$(this).parent().find('li').removeClass('active');
					$(this).text('열기');
				}
			}else{
				$(this).text('닫기');
				$(this).parent().addClass('active');
			}
		}else{
			$('.area_tree .list li a.tree').removeClass('on');
			$(this).addClass('on');
		}
	})

	$('.area_tree .btn.open').on('click',function(){
		$('.area_tree .list a.folder').parent().addClass('active');
		$(this).addClass('active');
		$('.area_tree .btn.close').removeClass('active');
		return false;
	});
	$('.area_tree .btn.close').on('click',function(){
		$('.area_tree .list a.folder').parent().removeClass('active');
		$('.area_tree .btn.open').removeClass('active');
		return false;
	});
	if("<?=$no?>"){
		$("[data-menu-set="+"<?=$no?>"+"]").addClass("on");
		$("[data-menu-set="+"<?=$no?>"+"]").parents("li").not(":first").addClass("active");
	}

	if("<?=$type?>" == "content"){
		$("[data-menu-set]").removeAttr( "data-menu-set" );
	} else {
		$("[data-content-set]").removeAttr( "data-content-set" );
	}
	</script>
