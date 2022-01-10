<span id="target_file_area">
<select name="target_file" title="파일경로" id="target_file" style="width:150px; height:200px; margin-left:10px; overflow:hidden; padding:3px;" multiple>
	<?
		$dir = opendir("$_SERVER[DOCUMENT_ROOT]"."/contents/".$variable[0]);
		while ($folder = readdir($dir)) {
			if ( $folder != "." && $folder != ".." ) { 
	?>
				<option value="<?=$folder?>"><?=$folder?></option>
	<? } 
	} ?>
</select>
</span>

<?
	$taget_file = $variable[0];
?>
<script type="text/javascript">
	var target = "";
	$("#target_file").change(function(){
		target = "/<?=$variable[0]?>/" + $("#target_file").val();
		$("#target_file_area").append("<input type=\"hidden\" name=\"target_file_path\" value=\""+ target +"\" id=\"target_file_path\" />");
	});
</script>


