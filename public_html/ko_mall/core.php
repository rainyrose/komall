<!-- 로딩 -->
<div id="div_ajax_load_image" class="area_loding" style="display:none">
	<img src="/ko_mall/images/content/loading.gif" class="img" alt="loading">
</div>
<!-- //로딩 -->
<?
if(!$_GET[type] || !$_GET[core]) {
	$_GET[type] = "dashboard";
	$_GET[core] = "dashboard";
}
$include_url = $_SERVER['DOCUMENT_ROOT'] . "/ko_mall/$_GET[type]/$_GET[core].php";

include_once "$include_url";

?>