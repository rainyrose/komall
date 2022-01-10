<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_content_config";
	$query = "SELECT * FROM $setting_table WHERE 1=1 AND delete_state != 'Y' ORDER BY sort ASC";
	$result = mysqli_query($connect, $query);
	$total = mysqli_num_rows($result);
	while($data = mysqli_fetch_array($result)){

		if($data[content_type] != "contents" && $data[content_type] != "link"){
			if(!$data["ref_".$data[content_type]]){
				if($data[content_type] == "board") $tx = "게시판";
				else if($data[content_type] == "program") $tx = "프로그램";
				else if($data[content_type] == "online") $tx = "온라인 신청프로그램";
				else if($data[content_type] == "product") $tx = "상품 분류";
				else unset($tx);
				$ADD_ = "<span style='margin-left:13px; font-size:12px; font-weight:bold; color:red;'> ! 등록된 ".$tx."이 없습니다</span>";
				//$ADD_ = "<i class=\"nocon\"><span>등록된 ".$tx."이 없습니다</span></i>";
			} else {
				unset($ADD_);
			}
		} else {
			unset($ADD_);
		}
?>
	<li data-sort-name="<?=$data[no]?>" data-content-set="<?=$data[content_id]?>">
		<a href="javascript:void(0);"><?=$data[content_title]?> <?=$ADD_?></a>
	</li>
<?
	}
?>

