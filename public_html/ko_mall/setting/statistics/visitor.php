
	<!-- lnb -->
<nav class="lnb">
	<!-- 선택시 a태그 class="on" -->
	<ul>
		<li><a href="<?=$common_queryString?>&amp;detail=visitor" class="on">방문자수 통계</a></li>
		<li><a href="<?=$common_queryString?>&amp;detail=refer">접속경로 통계</a></li>
	</ul>
</nav>
<!-- //lnb -->
<div class="box pd">
	<h2>방문자수 통계</h2>
<?

	//입력될 년,월
	if(!$year) $year = date("Y");
	if(!$month) $month = date("m");

	//노출될 년월 스트링
	$year_str = $year == "all" ? "" : $year."년";
	$month_str = $month == "all" ? "" : $month."월";

/*
	//각 월의 마지막 날
	$day_end = date("t", mktime(0, 0, 0, $month, "1", $year));
*/


    //이전, 다음 체크
    // $check_ = mysqli_fetch_array(mysqli_query($connect, "SELECT count(*) AS next_, (SELECT count(*) FROM koweb_statistics WHERE c_date LIKE '$back_y-$back_m-%') AS prev_ FROM koweb_statistics WHERE c_date LIKE  '$next_y-$next_m-%'"));
    $sql =
    "SELECT
        (SELECT LEFT(c_date,7) FROM koweb_statistics WHERE LEFT(c_date,7) > '{$year}-{$month}' group by LEFT(c_date,7) order by LEFT(c_date,7) ASC limit 0,1) AS next_ ,
        (SELECT LEFT(c_date,7) FROM koweb_statistics WHERE LEFT(c_date,7) < '{$year}-{$month}' group by LEFT(c_date,7) order by LEFT(c_date,7) DESC limit 0,1) AS prev_
    FROM
        dual";
    $check_ = mysqli_fetch_array(mysqli_query($connect, $sql) );

    $prev_temp_ = explode("-", $check_['prev_']);
    $back_y = $prev_temp_[0];
    $back_m = $prev_temp_[1];
    $next_temp_ = explode("-", $check_['next_']);
    $next_y = $next_temp_[0];
    $next_m = $next_temp_[1];

    //select에 노출될 year
    $date_query = "SELECT LEFT(c_date,4) as c_date FROM koweb_statistics group by LEFT(c_date,4)";
    $result = mysqli_query($connect, $date_query);
    $year_list = array();
    while($date_tmp = mysqli_fetch_array($result)){
        $year_list[] = $date_tmp['c_date'];
    }

    if($year != "all"){ //year가 all이라면 month를 구할필요가 없음
        //select에 노출될 month
        $date_query = "SELECT MID(c_date,6,2) as c_date FROM koweb_statistics where LEFT(c_date,4)='{$year}' group by MID(c_date,6,2)";
        $result = mysqli_query($connect, $date_query);
        $month_list = array();
        while($date_tmp = mysqli_fetch_array($result)){
            $month_list[] = $date_tmp['c_date'];
        }
    }

	if($year == "all"){	//year선택이 all일때
		$standard_str = "년";

		//쿼리정리
		$query_select = "LEFT(c_date,4) as standard,sum(day_total) as day_total";
		$query_where = "";
		$query_group = "group by LEFT(c_date,4)";
	}else if($month=="all"){

		$standard_str = "월";

		//쿼리정리
		$query_select = "RIGHT(LEFT(c_date,7),2) as standard,sum(day_total) as day_total";
		$query_where = "where LEFT(c_date,4)='{$year}'";
		$query_group = "group by LEFT(c_date,7)";
	}else{

		$standard_str = "일";

		//쿼리정리
		$query_select = "koweb_statistics.*,RIGHT(c_date,2) as standard";
		$query_where = "where LEFT(c_date,7) = '{$year}-{$month}'";
		$query_group="";
	}
	$query = "SELECT {$query_select} FROM koweb_statistics {$query_where} {$query_group} ORDER BY LEFT(c_date,7) ASC";
	$result = mysqli_query($connect, $query);

	//table_data
	$table_data = array();

	while($row = mysqli_fetch_array($result)){
		//x축 text
		$date_format .= "'".$row['standard'].$standard_str."',";

		//x축에 해당하는 데이터 정수
		$total_ .= "'".$row['day_total']."',";
		$nums_total += $row['day_total'];

		//하단 list테이블 데이터
		$table_data[$row['standard']] = $row['day_total'];
	}

	$date_format = "";
	$total_ = "";
	$last_day = date("t",strtotime("{$year}-{$month}"));
	for($i = 1; $i <= $last_day; $i++ ){
		$pad_i = str_pad($i,2,"0",STR_PAD_LEFT);
		$date_format .= "'".$pad_i.$standard_str."',";

		if($table_data[$pad_i]){
			$total_ .= "'".$table_data[$pad_i]."',";
		}else{
			$total_ .= "'0',";
		}
	}


	$date_format = substr($date_format, 0, -1);
	$total_ = substr($total_, 0, -1);

?>

	<div class="area_calendar type02 status">
		<div class="control">
			<div>
				<? if($check_[prev_] != 0){?>
					<a href="<?=$common_queryString2?>&amp;year=<?=$back_y?>&month=<?=$back_m?>" class="btn_prev">이전달</a>
				<? } ?>

				<select name="year" id="year" data-date-change="year">
					<option <?=$year=="all" ? "selected" : ""?> value="all">전체</option>
					<? foreach ($year_list as $year_val) { ?>
					<option <?=$year==$year_val ? "selected" : ""?> value="<?=$year_val?>"><?=$year_val?></option>
					<? } ?>
				</select>

				<select name="month" id="month" data-date-change="month">
					<option <?=$month=="all" ? "selected" : ""?> value="all">전체</option>
					<? foreach ($month_list as $month_val) { ?>
					<option <?=$month==sprintf('%02d', $month_val) ? "selected" : ""?> value="<?=sprintf('%02d', $month_val)?>"><?=sprintf('%02d', $month_val)?></option>
					<? } ?>
				</select>
				<? if($check_[next_] != 0){?>
					<a href="<?=$common_queryString2?>&amp;year=<?=$next_y?>&month=<?=$next_m?>"  class="btn_next">다음달</a>
				<? } ?>
			</div>
			<script type="text/javascript">
				$(function(){
					$("[data-date-change]").change(function(){
						var year_val = $("#year").val();
						var month_val = $("#month").val();

						//year선택이고 all이라면 month도 all로 강제 전환
						if(year_val=="all" || $(this).data("date-change")=="year"){
							$("#month").val("all");
							month_val = $("#month").val();
						}

						location.href="/ko_mall/index.html?type=setting&core=manager_setting&manager_type=statistics&detail=visitor&year="+year_val+"&month="+month_val;
					});
				});
			</script>
			<p class="total"><?=$year_str?> <?=$month_str?> 전체 방문자수 :  <?=number_format($nums_total)?></p>
			<a href="<?=$common_queryString2?>" class="btn_today button">이번달 보기</a>
		</div>

		<div class="chart">
			<canvas id="myChart" style="width:80vw;height:60vh;"></canvas>
			<script>
				var options = {
					type: 'bar',
					data: {
						labels: [<?=$date_format?>], //X축 제목
						datasets: [{
							  label: '방문자 수',
							  defaultFontSize : 15,
							  backgroundColor: 'rgba(178,197,255,1)',
							  data: [<?=$total_?>],
								  borderWidth: 1
						}]
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
				var ctx = document.getElementById('myChart').getContext('2d');
				new Chart(ctx, options);
			</script>
		</div>
		<?
		//오늘날짜 정보
		$now_date = date("Y-m-d");
		if(!$select_date) $select_date = $now_date;
		$now_week = date("w", time());
		$now_data = explode("-", $now_date);

		$n_y = $now_data[0];	//년
		$n_m = $now_data[1];	//월
		$n_d = $now_data[2];	//일

		if(!$year || !$month){
			$calendar_day = date("Y-m-d");
		} else {
			$calendar_day = date($year . "-" . $month . "-01");
		}

		$calendar_data = explode("-", $calendar_day);   //날짜를 년월일로 배열저장
		$calendar_y = $calendar_data[0];	//년
		$calendar_m = $calendar_data[1];	//월
		$calendar_d = $calendar_data[2];	//일

		$year  = $calendar_y;
		$month = $calendar_m;

		$sday_end = date("t", mktime(0, 0, 0, $calendar_m, $calendar_d, $calendar_y));  //마지막날짜구하기

		//매월 1월 12월 다음 이전 년도
		$now_YM = intval($calendar_y . $calendar_m);	//달력 년월
		$n_YM   = intval($n_y . $n_m);		//현재 년월

		$time = strtotime($year.'-'.$month.'-01');
		list($tday, $sweek) = explode('-', date('t-w', $time));  // 총 일수, 시작요일
		$tweek = ceil(($tday + $sweek) / 7);  // 총 주차
		$lweek = date('w', strtotime($year.'-'.$month.'-'.$tday));  // 마지막요일
		$week = array("sun","mon","tue","web","thu","fri","sat");
		?>
		<!-- 달력 -->
		<div class="calendar">
			<!-- today표시 td class="today" 선택시 class="on" -->
			<table>
				<caption>일,월,화,수,목,금,토 요일별 정보를 제공하는 표</caption>
				<thead>
					<tr>
						<th scope="col" data-calendar="sun">일</th>
						<th scope="col" data-calendar="mon">월</th>
						<th scope="col" data-calendar="tue">화</th>
						<th scope="col" data-calendar="web">수</th>
						<th scope="col" data-calendar="thu">목</th>
						<th scope="col" data-calendar="fri">금</th>
						<th scope="col" data-calendar="sat">토</th>
					</tr>
				</thead>
				<tbody>
				<? for ($n=1, $i=0; $i<$tweek; $i++) { ?>
					<tr>
					<?
						for ($k=0; $k<7; $k++) {

							//1일 이전 빈값
							if (($i == 0 && $k < $sweek) || ($i == $tweek-1 && $k > $lweek)) {
								echo "<td></td>";
								continue;
							}
							$n++;
							$date = $n-1;
							$trans_date = sprintf("%02d", $date);
							$date_time = $year."-".$month."-".$trans_date;
							$data_week = $week[date('w', strtotime($date_time))];
							//today class 추가
							if($date_time == $now_date) $add_class = "today";
							else unset($add_class);

							echo "<td data-calendar=\"$data_week\" class=\"$add_class\"><div><em>$date</em>";

							$query = "SELECT * FROM koweb_statistics WHERE 1=1 AND c_date = '$date_time'";
							$result = mysqli_query($connect, $query);
							$total = mysqli_num_rows($result);
							if($total > 0){
								$row = mysqli_fetch_array($result);
									echo "<span>$row[day_total]</span>";
							}
							echo "</div></td>";
						?>
						</td>
					<? } ?>
					</tr>
				<? } ?>
				</tbody>
			</table>

		</div>
		<!-- //달력 -->
	</div>
</div>
