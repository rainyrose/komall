<script type="text/javascript">
	jQuery('.scrollbar-inner').scrollbar();
</script>
<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_auth_config";
	//기본정보


if($mode != "load"){
	$default = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM $setting_table WHERE auth_type='$auth_type' AND auth_id='$auth_id'"));

	if($allow_level) $allow_level = str_replace("||", "|", "|" . $allow_level . "|");
	if($allow_user) $allow_user = str_replace("||", "|", "|" . $allow_user . "|");
	if($allow_dept) $allow_dept = str_replace("||", "|", "|" . $allow_dept . "|");
	
	$update_ = mysqli_query($connect, "UPDATE koweb_".$auth_type."_config SET use_auth_level = '$use_auth_level', use_auth_person = '$use_auth_person', use_auth_dept = '$use_auth_dept' WHERE id='$auth_id'");

	if($default > 0){
		$result = mysqli_query($connect, "UPDATE $setting_table SET  allow_level = '$allow_level', allow_dept = '$allow_dept', allow_user = '$allow_user' WHERE auth_type='$auth_type' AND auth_id='$auth_id'");
	} else {
		$result = mysqli_query($connect, "INSERT INTO $setting_table VALUES('', '$auth_type', '$auth_id', '$allow_level', '$allow_dept', '$allow_user')");
	}

} else { 

	$default = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM $setting_table WHERE auth_type='$auth_type' AND auth_id='$auth_id'"));
	$type_ = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM koweb_".$auth_type."_config WHERE id='$auth_id' LIMIT 1")); 
	
	$on_display_dept = "";
	$on_display_level = "";
	$on_display_person = "";

	if($type_[use_auth_dept] != "Y") $on_display_dept="display:none;";
	if($type_[use_auth_level] != "Y") $on_display_level="display:none;";
	if($type_[use_auth_person] != "Y") $on_display_person="display:none;";
?>

	<div class="popBox">
		<!-- title -->
		<h2 data-auth-target><?=$type_[title]?></h2>
		<!-- scroll -->
		<div class="scrollbar-inner">			
			<div class="popinBox">
				<input type="hidden" name="level_auth_person" value="<?=$default[allow_level]?>" data-access-person="koweb_member_level" />
				<input type="hidden" name="department_auth_person" value="<?=$default[allow_dept]?>" data-access-person="koweb_department" />
				<input type="hidden" name="member_auth_person" value="<?=$default[allow_user]?>" data-access-person="koweb_member" />
				<input type="hidden" name="auth_t" value="" data-auth-type="<?=$auth_type?>" />
				<input type="hidden" name="auth_d" value="" data-auth-id="<?=$auth_id?>" />

				<table class="table">
					<caption></caption>
					<tbody>
						<tr>
							<th scope="row">부서별 접근권한</th>
							<td class="tal">
								<div class="designRadio">
									<input type="radio" name="use_dept_auth" value="Y" id="use_dept_authY" data-auth="use_dept_auth" /><label for="use_dept_authY" >사용</label>
									<input type="radio" name="use_dept_auth" value="N" id="use_dept_authN" data-auth="use_dept_auth" /><label for="use_dept_authN" >미사용</label>
								</div>
							</td>
						</tr>
						<tr data-auth-control="use_dept_auth" style="<?=$on_display_dept?>">
							<td class="tal area_dragg" colspan="2">
								<ul data-auth-info="Y" data-info-type="koweb_department" class="box01">
									<li>허용</li> 
									<?=denied_auth($connect, "allow_dept", $default[allow_dept], true);?>
								</ul>
								<ul data-auth-info="N" data-info-type="koweb_department" class="box02">
									<li>불가</li> 
									<?=denied_auth($connect, "allow_dept", $default[allow_dept], false);?>
								</ul>
							</td>
						</tr>
						<tr>
							<th scope="row">등급별 접근권한</th>
							<td class="tal area_dragg">
								<div class="designRadio">
									<input type="radio" name="use_level_auth" value="Y" id="use_level_authY" data-auth="use_level_auth" /><label for="use_level_authY" >사용</label>
									<input type="radio" name="use_level_auth" value="N" id="use_level_authN" data-auth="use_level_auth" /><label for="use_level_authN" >미사용</label>
								</div>
							</td>
						</tr>
						<tr data-auth-control="use_level_auth" style="<?=$on_display_level?>">
							<td class="tal area_dragg" colspan="2">
								<ul data-auth-info="Y" data-info-type="koweb_member_level" class="box01">
									<li>허용</li> 
									<?=denied_auth($connect, "allow_level", $default[allow_level], true);?>
								</ul>
								<ul data-auth-info="N" data-info-type="koweb_member_level" class="box02">
									<li>불가</li> 
									<?=denied_auth($connect, "allow_level", $default[allow_level], false);?>
								</ul>
							
							</td>
						</tr>
						<tr>
							<th scope="row">개별 접근권한</th>
							<td class="tal">
								<div class="designRadio">
									<input type="radio" name="use_user_auth" value="Y" id="use_user_authY" data-auth="use_user_auth" /><label for="use_user_authY" >사용</label>
									<input type="radio" name="use_user_auth" value="N" id="use_user_authN" data-auth="use_user_auth" /><label for="use_user_authN" >미사용</label>
								</div>
							</td>
						</tr>
						<tr data-auth-control="use_user_auth" style="<?=$on_display_person?>">
						<td class="tal area_dragg" colspan="2">
							<ul data-auth-info="Y" data-info-type="koweb_member" class="box01">
								<li>허용</li> 
								<?=denied_auth($connect, "allow_user", $default[allow_user], true);?>
							</ul>
							<ul data-auth-info="N" data-info-type="koweb_member" class="box02">
								<li>불가</li> 
								<?=denied_auth($connect, "allow_user", $default[allow_user], false);?>
							</ul>
						</td>
						</tr>
					</tbody>
				</table>
				
				<div class="btn_area">
					<input type="submit" class="button lg" value="저장" data-auth-proc/>
					<a href="#" class="button lg white btn_close" onclick="javascript:$('#popLayer01').hide();">닫기</a>
				</div>
			</div>
		</div>
	</div>

<? } ?>
	<script type="text/javascript">
		$("input:radio[name='use_dept_auth']:radio[value='<?=$type_[use_auth_dept]?>']").attr("checked",true); 
		$("input:radio[name='use_level_auth']:radio[value='<?=$type_[use_auth_level]?>']").attr("checked",true); 
		$("input:radio[name='use_user_auth']:radio[value='<?=$type_[use_auth_person]?>']").attr("checked",true); 
	</script>