<script type="text/javascript">
//<![CDATA[
	// 쿠키 체크
	function getCookie(name)
	{
			var nameOfCookie = name + "=";
			var x = 0;
			while ( x <= document.cookie.length )
			{
					var y = (x+nameOfCookie.length);
					if ( document.cookie.substring( x, y ) == nameOfCookie ) {
							if ( (endOfCookie=document.cookie.indexOf( ";", y )) == -1 )
									endOfCookie = document.cookie.length;
							return unescape( document.cookie.substring( y, endOfCookie ) );
					}
					x = document.cookie.indexOf( " ", x ) + 1;
					if ( x == 0 )
							break;
			}
			return "";

	}

	<?
	/*----------------------------------------------------------------------------*/
	// 팝업창 호출
	/*----------------------------------------------------------------------------*/
	$today = date("Y-m-d");
	$result	= mysqli_query($connect, "SELECT * FROM koweb_popup WHERE '$today' BETWEEN start_date AND end_date  AND type='default' AND state = 'Y' ORDER BY start_date ASC, end_date ASC, no ASC");
	while ($row = mysqli_fetch_array($result)) {
		
		$cookie = "pop_" . $row[no];
		$row[width] = $row[width] + 20;
		$row[height] = $row[height] + 20;

		if (!$_COOKIE[$cookie]) {
	?>
		window.open("/popup/popup.html?no=<?=$row[no]?>", "<?=$cookie?>", "width=<?=$row[width]?>,height=<?=$row[height]?>,top=<?=$row[position_width]?>,left=<?=$row[position_height]?>,scrollbars");

	<? } } ?>
//]]>
</script>