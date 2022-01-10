<?
if($site[sms_alert] == "Y"){
	if(return_sms_total($site[sms_id], $site[sms_key], 50) != ""){
		alert(return_sms_total($site[sms_id], $site[sms_key], 50));
	}
}


$row = mysqli_fetch_array(mysqli_query($connect, "SELECT count(distinct(order_id)) AS total_,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '입금대기') AS a1,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '입금확인중') AS a11,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '결제완료') AS a2,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '상품준비중') AS a3,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '배송준비') AS a4,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '배송중') AS a41,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '취소요청') AS a5,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '취소완료') AS a51,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '교환요청') AS a6,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '교환진행중') AS a61,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '반품요청') AS a7,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '반품진행중') AS a71,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND state = '반품완료') AS a72,
								(SELECT count(distinct(order_id)) FROM koweb_order WHERE order_info='P' AND (state = '주문완료' OR state = '배송완료')) AS a10 FROM koweb_order"));
?>

<div class="box_col02 dashboard">
	<div class="box">
		<h2>전체현황</h2>
		<ul class="status">
			<!-- 주문현황 -->
			<li>
				<em>입금대기</em>
				<span><?=$row[a1]?></span>
			</li>
			<li>
				<em>입금확인중</em>
				<span><?=$row[a11]?></span>
			</li>
			<li>
				<em>결제완료</em>
				<span><?=$row[a2]?></span>
			</li>
			<li>
				<em>상품준비중</em>
				<span><?=$row[a3]?></span>
			</li>
			<li>
				<em>배송준비</em>
				<span><?=$row[a4]?></span>
			</li>
			<li>
				<em>배송중</em>
				<span><?=$row[a41]?></span>
			</li>
			<li>
				<em>취소요청</em>
				<span><?=$row[a5]?></span>
			</li>
			<li>
				<em>취소완료</em>
				<span><?=$row[a51]?></span>
			</li>
			<li>
				<em>교환요청</em>
				<span><?=$row[a6]?></span>
			</li>
			<li>
				<em>교환진행중</em>
				<span><?=$row[a61]?></span>
			</li>
			<li>
				<em>반품요청</em>
				<span><?=$row[a7]?></span>
			</li>
			<li>
				<em>반품진행중</em>
				<span><?=$row[a71]?></span>
			</li>
			<li>
				<em>반품완료</em>
				<span><?=$row[a72]?></span>
			</li>
			<li>
				<em>배송완료</em>
				<span><?=$row[a10]?></span>
			</li>
			<!--<li>
				<em>부분취소</em>
				<span>8</span>
			</li>-->
			<!-- 상품현황 -->
		</ul>
<?
	$row2 = mysqli_fetch_array(mysqli_query($connect, "SELECT count(no) AS total_,
				(SELECT count(no) FROM koweb_product WHERE seller='Y') AS sellerY,
				(SELECT count(no) FROM koweb_product WHERE seller !='Y') AS sellerN,
				(SELECT count(no) FROM koweb_product WHERE shower='Y') AS showerY,
				(SELECT count(no) FROM koweb_product WHERE shower!='Y') AS showerN FROM koweb_product"));
?>
		<h3>상품현황</h3>
		<ul class="status">
			<li>
				<em>판매함</em>
				<span><?=$row2[sellerY]?></span>
			</li>
			<li>
				<em>판매안함</em>
				<span><?=$row2[sellerN]?></span>
			</li>
			<li>
				<em>진열함</em>
				<span><?=$row2[showerY]?></span>
			</li>
			<li>
				<em>진열안함</em>
				<span><?=$row2[showerN]?></span>
			</li>
		</ul>
	</div>

	<div class="box none">
		<div>
			<!-- 당일기준 -->
			<a href="#"><i><?=$row[a1]+$row[a11]?></i><em>주문</em></a>
			<a href="#"><i><?=$row[a2]?></i><em>입금</em></a>
			<a href="#"><i><?=$row[a4]+$row[a41]?></i><em>배송</em></a>
		</div>
		<div class="delivery">
			<h2>택배번호 조회</h2>
			<div>
				<input type="text" name="deli_code" id="deli_code" placeholder="운송장번호 입력"/>
				<button type="button" class="button sm gray" onclick="javascript:pop_deli();">검색</button>
				<p>입력후 엔터키를 누르세요.</p>
			</div>
			<script type="text/javascript">
			function pop_deli(){
				var url = "<?=$site_pay[$site_pay[deli_company]]?>"+$("#deli_code").val();
				window.open(url, "", "width=800, height=700, toolbar=no, menubar=no, scrollbars=no, resizable=yes");
			}
			</script>
		</div>
	</div>


	<div class="box" style="overflow:hidden; height:424px;">
		<h2>시스템공지</h2>
		<ul class="list">
			<?
			$system = mysqli_query($connect, "SELECT * FROM board_system ORDER BY no DESC LIMIT 7");
			while($system_row = mysqli_fetch_array($system)){
			?>
				<li><a href="/ko_mall/index.html?type=board&core=manager_board&core_id=board_system&start=&category=&search_key=&keyword=&mode=view&no=<?=$system_row[no]?>"><?=$system_row[title]?></a></li>

			<? } ?>
		</ul>
		<a href="/ko_mall/index.html?type=board&core=manager_board&core_id=board_system" class="button sm more">View</a>
	</div>


	<div class="box" style="overflow:hidden; height:424px;">
		<h2>이번주 접속자수 로그분석</h2>
		<div class="box_chart">
			<canvas id="myChart2"></canvas>
		</div>
		<?
		$sunday = count_calendar(0, 0);
		$saturday = count_calendar(0, 6);
		$cal_array = getDatesFromRange($sunday, $saturday);
		unset($date_format);

		$totals_array = array();
		foreach($cal_array as $v){
			$query = "SELECT day_total FROM koweb_statistics WHERE c_date='$v'";
			$result = mysqli_query($connect, $query);
			$todays_ = mysqli_fetch_array($result);
			$date_format .= "'".$v."',";
			$total_visitor .= "'".$todays_[day_total]."',";
		}


		$date_format = substr($date_format, 0, -1);
		$total_ = substr($total_visitor, 0, -1);
		?>
		<script>
			var options = {
				type: 'bar',
				data: {
					labels: [<?=$date_format?>], //X축 제목
					datasets: [{
						  label: '방문자 수',
						  defaultFontSize : 10,
						  backgroundColor: 'rgba(65,64,84,1)',
						  data: [<?=$total_?>],
							  borderWidth: 1
					}],
				},
				options: {
					scales: {
						  yAxes: [{
							ticks: {
								reverse: false
							}
						  }]
					}
				}
			};
			var ctx = document.getElementById('myChart2').getContext('2d');
			new Chart(ctx, options);
		</script>
		<a href="/ko_mall/index.html?type=setting&core=manager_setting&manager_type=statistics&detail=visitor" class="button sm more">View</a>
	</div>
	</div>

	<div class="box only">
	<h2>최근게시물</h2>
	<!-- 목록 16개 -->
	<ul class="list notice">
		<?
			if (!$bbs_date_start) $bbs_date_start = date("Y-m-d", strtotime("-3 days"));
			if (!$bbs_date_end) $bbs_date_end = date("Y-m-d");

			// 기본 검색
			//$WHERE = " WHERE reg_date BETWEEN '$bbs_date_start 00:00:00' AND '$bbs_date_end 23:59:59'";

			// 검색
			unset($query);
			$result_board = mysqli_query($connect, "SELECT id FROM koweb_board_config");
			while ($row_board = mysqli_fetch_array($result_board)) {
				$query .= "( SELECT '$row_board[id]' as board_title, title, name, reg_date, no FROM $row_board[id] $WHERE ) UNION ALL";
			}
			$query = substr($query , 0, -10);
			$query .= " ORDER BY reg_date DESC, no DESC LIMIT 16;";

			$result = mysqli_query($connect, $query);
			$total = mysqli_num_rows($result);

			while($row = mysqli_fetch_array($result)){
				$temp_no = $total - $i;
				$id = $row[0];
				// 게시판명
				$data = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_board_config WHERE id='$id' LIMIT 1"));

			?>
			<li>
				<!-- 카테고리 -->
				<em><?=$data[title]?></em>
				<a href="/ko_mall/index.html?type=board&core=manager_board&core_id=<?=$row[board_title]?>&amp;no=<?=$row[no]?>"><?=$row[title]?></a>
				<span><?=$row[reg_date]?></span>
			</li>
		<? } ?>
	</ul>
	<!-- <a href="#" class="button sm more">View</a> -->
</div>
