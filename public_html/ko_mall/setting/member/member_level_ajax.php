<? 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_member_level";
	$reg_date = date("Y-m-d H:i:s");
	if($mode == "modify"){
		$query = mysqli_query($connect, "UPDATE $setting_table SET level_title ='$title'
																	, admin_auth='$admin_auth'
																WHERE level = '$level'
							 ");

	} else if($mode == "delete") {
		if($level == "1" || $level == "10") {
			alert("총괄관리자와 비회원은 삭제할 수 없습니다.");
		} else {
			//멤버검색해서, 해당 등급이있으면 삭제불가
			$query = mysqli_query($connect, "DELETE FROM $setting_table WHERE level = '$level' LIMIT 1");
		}
	} else {
		if($check > 0){
			alert("동일한 레벨의 등급이 존재합니다.");
		} else {
			$query = mysqli_query($connect, "INSERT INTO $setting_table VALUES('', '$level', '$title', '$admin_auth')");
			echo "INSERT INTO $setting_table VALUES('', '$level', '$title', '$admin_auth')";
			exit;
		}
	}
?>
	<? 

	$level_query = "SELECT * FROM koweb_member_level ORDER BY level ASC";
	$level_result = mysqli_query($connect, $level_query);
	while($level = mysqli_fetch_array($level_result)){
		if($level[admin_auth]) $admin_state = "<i class=\"icon_admin\">Admin</i>";
		else unset($admin_state);
	?>
		<tr>
			<td><?=$level[level]?></td>
			<td data-level-info="<?=$level[level]?>"><?=$admin_state?> <span data-ori-title><?=$level[level_title]?></span></td>
			<td data-button-area="<?=$level[level]?>" data-level-type="<?=$level[admin_auth]?>">
				<a href="#" class="button sm gray" data-level-button="modify">수정</a> 
				<? if($level[level] != 1 && $level[level] != 10){ ?>
				<a href="#" class="button sm white" data-level-button="delete">삭제</a>
				<? } ?>
			</td>
		</tr>
<? } ?>