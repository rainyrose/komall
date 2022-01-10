<script type="text/javascript">
	function setCookie( name, value, expirehours ) {
		var todayDate = new Date();
		todayDate.setHours( todayDate.getHours() + expirehours );
		document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";"
	}
	function closeWin(no) {
		document.getElementById("layer_"+no).style.display = "none";

		if(document.getElementById("layer_"+no)){
			document.getElementById("layer_"+no).style.display = "none";

		}
	}
	function todaycloseWin(no) {
		var cookie_name = "layer_"+no;
		setCookie( cookie_name, "done" , 24 );
		//document.getElementById(no).style.display = "none";
		$("#layer_"+no).css("display", "none");
	}
</script>

<?
	$result	= mysqli_query($connect, "SELECT * FROM koweb_popup WHERE '$today' BETWEEN start_date AND end_date AND type='layer' AND state = 'Y' ORDER BY start_date ASC, end_date ASC, no ASC");
	while ($row = mysqli_fetch_array($result)) {
	if(!$row[link_url]){
		$row[link_url] = "javascript:void(0)";
		$row[link_type] = "";
	}
?>
<div id="layer_<?=$row[no]?>" class="layerPop" style="left:<?=$row[position_width]?>px; top:<?=$row[position_height]?>px; width:<?=$row[width]?>px; z-index:<?=$row[zindex]?>;">
	<? if($row[img]){ ?>
	<a href="<?=$row[link_url]?>" target="<?=$row[link_type]?>"><img src="/upload/program/popup/<?=$row[img]?>" alt="<?=$row[title]?>"/></a>
	<? } ?>
	<?=htmlspecialchars_decode($row[contents])?>
	<div class="btn">
		<button onclick="todaycloseWin('<?=$row[no]?>');">오늘 하루 열지 않기</button>
		<button onclick="closeWin('<?=$row[no]?>');">X 닫기</button>
	</div>
</div>

<script type="text/javascript">
	cookiedata = document.cookie;
	if ( cookiedata.indexOf("layer_"+"<?=$row[no]?>") < 0 ){
		document.getElementById("layer_<?=$row[no]?>").style.display = "block";
	} else {
		document.getElementById("layer_<?=$row[no]?>").style.display = "none";
	}
</script>
<? } ?>
