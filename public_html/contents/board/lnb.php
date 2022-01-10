<?
	$query_ = "SELECT * FROM koweb_menu_config WHERE menu_id = '$mid'";
	$result_ = mysqli_query($connect, $query_);
	$row_ = mysqli_fetch_array($result_);
	//$mid_length = floor(strlen($row_[menu_id]) / 2);
	$depth_history = explode("|", $row_[depth_history]);
?>
	<!-- lnb/ snb 노출 -->
	<div class="area_lnb">
		<div class="inr">
			<a href="/" class="btn_home">HOME</a>
			<!-- lnb -->
			<nav class="lnb">
				<? print_lnb($connect, $site_language, "pc", $row_[ref_group], $depth_history[0], 1, 2); ?>
			</nav>
			<!-- //lnb -->
			
			<!-- snb -->
			<div class="snb">
				<? print_lnb($connect, $site_language, "pc", $row_[ref_group], $depth_history[1], 3, 3); ?>

			</div>
			<!-- //snb -->
		</div>
	</div>

	<div class="area_tab">
			<? print_lnb($connect, $site_language, "pc", $row_[ref_group], $depth_history[2], 4, 4); ?>
	</div>