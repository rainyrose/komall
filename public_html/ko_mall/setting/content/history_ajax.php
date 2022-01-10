<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_content_history";
		
	if (!$start) $start = 0;
	$scale = 5; // 리스트 수
	$page_scale	= 10; // 페이징 수

	$query = "SELECT * FROM $setting_table WHERE content_id = '$content_id' ORDER BY reg_date DESC LIMIT $start, $scale";


	$total_query = "SELECT * FROM $setting_table WHERE content_id = '$content_id'";
	$result = mysqli_query($connect, $query);
	$total_result = mysqli_query($connect, $total_query);
	$total = mysqli_num_rows($total_result);
?>
	<h2 class="mt20">히스토리</h2>
	<table class="table hover">
		<caption>히스토리 선택</caption>
		<colgroup>
			<col/>
			<col style="width:30%"/>
			<col style="width:20%"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="col">제목</th>									
				<th scope="col">시간</th>									
				<th scope="col">선택</th>									
			</tr>
		</thead>
		<tbody data-history-view>
		<?	while($data = mysqli_fetch_array($result)){ ?>
				<tr>
					<td data-add-menu="name"><?=$data[content_title]?></td>
					<td><?=$data[reg_date]?></td>
					<td><a href="javascript:;" onclick="javascript:history_setup(popLayer03, '<?=$data[no]?>');" class="button sm white">보기</a></td>
				</tr>
			<? } ?>
		</tbody>
	</table>
	<!-- page -->
	<div class="pagination">
	<?
		if ($total == 0) $total = 1;
		// 처음
		echo "<a href=\"javascript:board_ajax($content_id, 0);\" class=\"btn_first\">맨처음</a>";
		$page = floor($start / ($scale * $page_scale));

		if ($start + $scale >  $scale * $page_scale) {
			$pre_start = $start - $scale * $page_scale ;
			echo "<a href=\"javascript:board_ajax('".$content_id."','".$pre_start."');\" class=\"btn_prev\">이전</a>";
		}
		
		for ($vj = 0; $vj < $page_scale ; $vj++) {
			$ln = ($page * $page_scale + $vj) * $scale;
			$vk = $page * $page_scale + $vj + 1 ;
			$pageing = ($vk - 1) * $scale;
			if ($ln < $total) {
				if ($ln != $start) echo "<a href=\"javascript:board_ajax('".$content_id."','".$pageing."');\">$vk</a>";
				else echo "<span>$vk</span>";
			}
		}

		// 마지막
		$end_page = floor($total - $scale) + 1;
		if ($end_page <= 0)	$end_page = 0;

		if ($total > (($page + 1) * $scale * $page_scale)) {
			$n_start = ($page + 1) * $scale * $page_scale ;
			echo "<a href=\"javascript:board_ajax('".$content_id."','".$n_start."');\" class=\"btn_next\">다음</a>";
		}

		$end_page = ceil($total / $scale);
		if ($total) $end_start = ($end_page -1) * $scale;
		else $end_start = $end_page;

		echo "<a href=\"javascript:board_ajax('".$content_id."','".$end_start."');\" class=\"btn_last\">맨마지막</a>";
		?>
	</div>
	<!-- //page -->