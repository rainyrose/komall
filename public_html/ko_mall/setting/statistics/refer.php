
	<!-- lnb -->
<nav class="lnb">
	<!-- 선택시 a태그 class="on" -->
	<ul>
		<li><a href="<?=$common_queryString?>&amp;detail=visitor">방문자수 통계</a></li>
		<li><a href="<?=$common_queryString?>&amp;detail=refer" class="on">접속경로 통계</a></li>
	</ul>
</nav>
<!-- //lnb -->
<div class="box pd">
<h2>접속경로 통계</h2>
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
        (SELECT concat(year,'-',month) FROM koweb_statistics_refer WHERE (year >= '{$year}' and month > '{$month}') or year > '{$year}' group by year,month order by year ASC ,month ASC limit 0,1) AS next_ ,
        (SELECT concat(year,'-',month) FROM koweb_statistics_refer WHERE (year <= '{$year}' and month < '{$month}') or year < '{$year}' group by year,month order by year DESC,month DESC limit 0,1) AS prev_
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
    $date_query = "SELECT year FROM koweb_statistics_refer group by year";
    $result = mysqli_query($connect, $date_query);
    $year_list = array();
    while($date_tmp = mysqli_fetch_array($result)){
        $year_list[] = $date_tmp['year'];
    }

    if($year != "all"){ //year가 all이라면 month를 구할필요가 없음
        //select에 노출될 month
        $date_query = "SELECT month FROM koweb_statistics_refer where year='{$year}' group by month";
        $result = mysqli_query($connect, $date_query);
        $month_list = array();
        while($date_tmp = mysqli_fetch_array($result)){
            $month_list[] = $date_tmp['month'];
        }
    }


	if($year == "all"){	//year선택이 all일때
		//쿼리정리
		$query_select = "r_url as standard,count(*) as url_cnt";
		$query_where = "";
		$query_group = "group by r_url";
        $query_order = "";
	}else if($month=="all"){
		//쿼리정리
		$query_select = "r_url as standard,count(*) as url_cnt";
		$query_where = "where year='{$year}'";
		$query_group = "group by r_url";
        $query_order = "";
	}else{
		//쿼리정리
		$query_select = "r_url as standard,count(*) as url_cnt";
		$query_where = "where year='{$year}' and month='{$month}'";
		$query_group = "group by r_url";
        $query_order = "";
	}
	$query = "SELECT {$query_select} FROM koweb_statistics_refer {$query_where} {$query_group} {$query_order} ORDER BY url_cnt DESC LIMIT 25";
	$result = mysqli_query($connect, $query);

	//table_data
	$table_data = array();

	while($row = mysqli_fetch_array($result)){
		//x축 text
		$date_format .= "'".str_cut_ending_utf8($row['standard'],30,'...')."',";

		//x축에 해당하는 데이터 정수
		$total_ .= "'".$row['url_cnt']."',";
		$nums_total += $row['url_cnt'];

		//하단 list테이블 데이터
		$table_data[$row['standard']] = $row['url_cnt'];
	}
	$date_format = substr($date_format, 0, -1);
	$total_ = substr($total_, 0, -1);

?>

<div class="area_calendar type02 status">
	<div class="control">
		<div>
			<? if($check_[prev_] != 0){?>
				<a href="<?=$common_queryString?>&amp;year=<?=$back_y?>&month=<?=$back_m?>&detail=refer" class="btn_prev">이전달</a>
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
				<a href="<?=$common_queryString?>&amp;year=<?=$next_y?>&month=<?=$next_m?>&detail=refer"  class="btn_next">다음달</a>
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

					location.href="/ko_mall/index.html?type=setting&core=manager_setting&manager_type=statistics&detail=refer&year="+year_val+"&month="+month_val;
				});
			});
		</script>
		<!--<p class="total"><?=$year_str?> <?=$month_str?> 전체 방문자수 :  <?=number_format($nums_total)?></p>-->
		<a href="<?=$common_queryString2?>" class="btn_today button">이번달 보기</a>
	</div>
	<canvas id="myChart" style="width:80vw;height:60vh;"></canvas>
	<script>
		var options = {
			type: 'horizontalBar',
			data: {
				labels: [<?=$date_format?>], //X축 제목
				datasets: [{
					  label: '접속경로 수',
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

	<h2>접속경로 상세</h2>
	<table class="bbsList">
		<caption>히스토리 선택</caption>
		<colgroup>
			<col style="width:20%">
			<col style="width:15%">
		</colgroup>
		<thead>
			<tr>
				<th scope="col">접속경로</th>
				<th scope="col">접속경로</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($table_data as $key => $value) {
				$href = "href='{$key}' target='_blank'";
				if($key == "직접 접속 및 즐겨찾기") $href="href='javascript:void(0)'";
				?>
				<tr>
					<td class="tal" style="padding-left:40px;" title='<?=$key?>'><a <?=$href?>><?=str_cut_ending_utf8($key,100,'...')?><a/></td>
					<td><?=$value?></td>
				</tr>
			<? } ?>

		</tbody>
	</table>
	</div>
</div>
