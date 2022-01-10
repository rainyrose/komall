<?
include  "../head.php"; 

$board_id = strip_tags($board_id);
$file = strip_tags($file);

$board_id = str_replace("/", "", $board_id);
$board_id = str_replace("\\", "", $board_id);
$board_id = str_replace("..", "", $board_id);

$file = str_replace("/", "", $file);
$file = str_replace("\\", "", $file);
$file = str_replace("..", "", $file);

if (isblank($board_id))	{
	error("비정상적 접근1");
	exit;
}

if (isblank($file))	{
	error("비정상적 접근2");
	exit;
}

if (!file_exists($_SERVER[DOCUMENT_ROOT] . "/upload/" . $board_id . "/" . $file)) {
	error("해당 파일이 없습니다.");
	exit;
}

/*----------------------------------------------------------------------------*/
// 정리
/*----------------------------------------------------------------------------*/

if (!preg_match("/7.0/", $HTTP_USER_AGENT)) {
	header("Cache-Control: private, must-revalidate"); 
}

Header("Content-Disposition: attachment; filename=" . rawurlencode($file));
Header("Content-Type: application/octet-stream");
// Header("Content-Length: " . filesize($_SERVER[DOCUMENT_ROOT] . $ROOT . "/board/data/$board_id/$file")); 
Header("Pragma: no-cache"); 
Header("Expires: 0"); 

if (is_file($_SERVER[DOCUMENT_ROOT] . "/upload/" . $board_id . "/" . $file)) { 
	$fp = fopen($_SERVER[DOCUMENT_ROOT] . "/upload/" . $board_id . "/" . $file, "rb"); 
	if (!fpassthru($fp)) { 
		fclose($fp); 
	}
}

?>