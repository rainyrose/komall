<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";

	//$set_data = json_decode($set_data);
	//$set_option = explode("|", $set_data);
	$data_tmp = htmlspecialchars_decode($set_data);
	$data = json_decode($data_tmp);
?>
<div class="btn_col">
	<a href="#" class="button sm white" id="all_deleted">삭제</a>
	<a href="javascript:;" onclick="javascript:showPopup(popLayer01, 'option');" class="button sm white">일괄설정</a>

	<div class="right">
		<a href="#" class="button sm white" data-btn-event="detail_check_id">자체코드품목 중복확인</a>
		<!--<a href="javascript:;" onclick="javascript:showPopup(popLayer02);" class="button sm white">품절표시문구</a>-->
	</div>
</div>

<table class="bbsList" data-detail-table>
	<caption>옵션품목생성</caption>
	<colgroup>
		<col style="width:5%"/>
		<col style="width:5%"/>
		<col />
		<col style="width:10%"/>
		<col style="width:15%"/>
		<col style="width:10%"/>
		<col style="width:10%"/>
		<col style="width:10%"/>
		<col style="width:10%"/>
	</colgroup>
	<thead>
		<tr>
			<th scope="col"><div class="designCheck noText"><input type="checkbox" name="all_selected" id="all_selected"/><label for="all_selected">전체선택</label></div></th>
			<th scope="col">순서</th>
			<th scope="col">품목명[품목코드]</th>
			<th scope="col">자체품목코드</th>
			<th scope="col">추가금액</th>
			<th scope="col">재고수량</th>
			<th scope="col">안전재고</th>
			<th scope="col">품절표시</th>
			<th scope="col">판매상태</th>
		</tr>
	</thead>
	<tbody>
<?

	function data_load_process($array){

		$option_tmp = explode("|", $array[0]);
		$option_tmp1 = explode("|", $array[1]);
		$option_tmp2 = explode("|", $array[2]);
		$tr_count = 1;
		$ac2_count = 0;
		$ac2_count1 = 0;
		$ac2_count2 = 0;

		foreach($option_tmp AS $omp){
			$array_check = explode("|", $omp);
			foreach($array_check AS $ar){
				$array_check2 = explode("^", $ar);
				if($array_check2[2] == "N"){
					$ac2_count++;
				}
			}
		}
		if(count($option_tmp) == $ac2_count) {
			$array[0] = "";
		}

		foreach($option_tmp1 AS $omp){
			$array_check = explode("|", $omp);
			foreach($array_check AS $ar){
				$array_check2 = explode("^", $ar);
				if($array_check2[2] == "N"){
					$ac2_count1++;
				}
			}
		}

		if(count($option_tmp1) == $ac2_count1) {
			$array[1] = "";
		}

		foreach($option_tmp2 AS $omp){
			$array_check = explode("|", $omp);
			foreach($array_check AS $ar){
				$array_check2 = explode("^", $ar);
				if($array_check2[2] == "N"){
					$ac2_count2++;
				}
			}
		}

		if(count($option_tmp2) == $ac2_count2) {
			$array[2] = "";
		}

		$array_tmp = array_filter($array);
		$array = array();
		foreach($array_tmp AS $key=>$val){
			array_push($array, $val);
		}

		$count = count($array);
		$option_tmp = explode("|", $array[0]);

		foreach($option_tmp as $o){
			$option = explode("^", $o);
			if($option[2] != "N"){
				//$o[1] 1차 옵션명
				if($count > 1){
					$option_tmp1 = explode("|", $array[1]);
					foreach($option_tmp1 as $o1){
						$option1 = explode("^", $o1);
							//3차 있을때
						if($array[2]){
							$option_tmp2 = explode("|", $array[2]);
							foreach($option_tmp2 as $o2){
								$option2 = explode("^", $o2);

								if($option2[2] != "N" && $option1[2] != "N"){
									echo "<tr data-detail-info=\"".$tr_count."\">";
									echo "<td><div class=\"designCheck noText\"><input type=\"checkbox\" name=\"detail_del\" value=\"".$tr_count."\" id=\"detail_del_".$tr_count."\"  data-detail-selector=\"".$tr_count."\"/><label for=\"detail_del_".$tr_count."\">선택</label></div></td>";
									echo "<td>";
									echo "<em class=\"icon_drag\">마우스드레그하시면 순서변경이 가능합니다</em>";
									echo "</td>";
									echo "<td class=\"tal\">";
									if($option[0] != "none"){
										echo "<div data-review-color=\"\" style=\"background:".$option[0]."\"></div> ";
										echo "<input type=\"hidden\" name=\"option_color1[]\" value=\"$option[0]\"> ";
									}
									if($option1[0] != "none"){
										echo "<div data-review-color=\"\" style=\"background:".$option1[0]."\"></div> ";
										echo "<input type=\"hidden\" name=\"option_color2[]\" value=\"$option1[0]\"> ";
									}
									if($option2[0] != "none"){
										echo "<div data-review-color=\"\" style=\"background:".$option2[0]."\"></div> ";
										echo "<input type=\"hidden\" name=\"option_color3[]\" value=\"$option2[0]\"> ";
									}

									echo $option[1]." / " . $option1[1] . " / " . $option2[1] . " <i>[자동생성]</i>";
									echo "<input type=\"hidden\" name=\"option_title[]\" value=\"$option[1] &sol; $option1[1] &sol; $option2[1]\"> ";
									echo "</td>";
									echo "<td><input type=\"text\" name=\"option_id[]\" value=\"\"  data-detail-input=\"option_id\" class=\"inputFull\"/>";
									echo "<input type=\"hidden\" name=\"current_option_id[]\" value=\"\" class=\"inputFull\"/></td>";
									echo "<td>";
									echo "<select name=\"option_type[]\" data-detail-input=\"type\">";
									echo "<option value=\"+\">+</option>";
									echo "<option value=\"-\">-</option>";
									echo "</select> ";
									echo "<input type=\"text\" name=\"option_price[]\" data-detail-input=\"price\" class=\"input100\" />";
									echo "</td>";
									echo "<td><input type=\"text\" name=\"stock[]\" data-detail-input=\"stock\" value=\"\" class=\"inputFull\"/></td>";
									echo "<td><input type=\"text\" name=\"safe_stock[]\" data-detail-input=\"safety_stock\" value=\"\" class=\"inputFull\"/></td>";
									echo "<td><div class=\"designCheck noText\">";
									echo "<input type=\"hidden\" name=\"use_soldout_value[]\" data-detail-soldout value=\"N\"/>";
									echo "<input type=\"checkbox\" data-detail-input=\"use_soldout\" name=\"option_use_sold[]\" id=\"ous_".$tr_count."\"  value=\"Y\"/><label for=\"ous_".$tr_count."\">품절사용</label></div>";
									echo "</td>";
									echo "<td>";
									echo "<select name=\"detail_state[]\" id=\"\" class=\"inputFull\" data-detail-input=\"state\" >";
									echo "<option value=\"Y\">판매함</option>";
									echo "<option value=\"N\">판매안함</option>";
									echo "</select>";
									echo "</td>";
									echo "</tr>";
									$tr_count++;
								}
							}
						//2차까지만 있을때
						} else {
							if($option1[2] != "N"){
								echo "<tr data-detail-info=\"".$tr_count."\">";
								echo "<td><div class=\"designCheck noText\"><input type=\"checkbox\" name=\"detail_del\" value=\"".$tr_count."\" id=\"detail_del_".$tr_count."\"  data-detail-selector=\"".$tr_count."\"/><label for=\"detail_del_".$tr_count."\">선택</label></div></td>";
								echo "<td>";
								echo "<em class=\"icon_drag\">마우스드레그하시면 순서변경이 가능합니다</em>";
								echo "</td>";
								echo "<td class=\"tal\">";
								if($option[0] != "none"){
									echo "<div data-review-color=\"\" style=\"background:".$option[0]."\"></div> ";
									echo "<input type=\"hidden\" name=\"option_color1[]\" value=\"$option[0]\"> ";
								}
								if($option1[0] != "none"){
									echo "<div data-review-color=\"\" style=\"background:".$option1[0]."\"></div> ";
									echo "<input type=\"hidden\" name=\"option_color2[]\" value=\"$option1[0]\"> ";
								}

								echo $option[1]." / " . $option1[1] . " <i>[자동생성]</i>";
								echo "<input type=\"hidden\" name=\"option_title[]\" value=\"$option[1] &sol; $option1[1]\"> ";
								echo "</td>";
								echo "<td><input type=\"text\" name=\"option_id[]\" value=\"\"  data-detail-input=\"option_id\" class=\"inputFull\"/>";
								echo "<input type=\"hidden\" name=\"current_option_id[]\" value=\"\" class=\"inputFull\"/></td>";
								echo "<td>";
								echo "<select name=\"option_type[]\" data-detail-input=\"type\">";
								echo "<option value=\"+\">+</option>";
								echo "<option value=\"-\">-</option>";
								echo "</select> ";
								echo "<input type=\"text\" name=\"option_price[]\" data-detail-input=\"price\" class=\"input100\" />";
								echo "</td>";
								echo "<td><input type=\"text\" name=\"stock[]\" data-detail-input=\"stock\" value=\"\" class=\"inputFull\"/></td>";
								echo "<td><input type=\"text\" name=\"safe_stock[]\" data-detail-input=\"safety_stock\" value=\"\" class=\"inputFull\"/></td>";
								echo "<td><div class=\"designCheck noText\">";
								echo "<input type=\"hidden\" name=\"use_soldout_value[]\" data-detail-soldout value=\"N\"/>";
								echo "<input type=\"checkbox\" data-detail-input=\"use_soldout\" name=\"option_use_sold[]\" id=\"ous_".$tr_count."\"  value=\"Y\"/><label for=\"ous_".$tr_count."\">품절사용</label></div>";
								echo "</td>";
								echo "<td>";
								echo "<select name=\"detail_state[]\" id=\"\" class=\"inputFull\" data-detail-input=\"state\" >";
								echo "<option value=\"Y\">판매함</option>";
								echo "<option value=\"N\">판매안함</option>";
								echo "</select>";
								echo "</td>";
								echo "</tr>";
								$tr_count++;
							}
						}
					}
				} else {
					if($option[2] != "N" && $option[2]){
						echo "<tr data-detail-info=\"".$tr_count."\">";
						echo "<td><div class=\"designCheck noText\"><input type=\"checkbox\" name=\"detail_del\" data-detail-selector=\"".$tr_count."\" value=\"".$tr_count."\" id=\"detail_del_".$tr_count."\"/><label for=\"detail_del_".$tr_count."\">선택</label></div></td>";
						echo "<td>";
						echo "<em class=\"icon_drag\">마우스드레그하시면 순서변경이 가능합니다</em>";
						echo "</td>";
						echo "<td class=\"tal\">";
						if($o[0] != "none"){
							echo "<div data-review-color=\"\" style=\"background:".$option[0]."\"></div> ";
							echo "<input type=\"hidden\" name=\"option_color1[]\" value=\"$option[0]\"> ";
						}
						echo $option[1] . " <i>[자동생성]</i>";
						echo "<input type=\"hidden\" name=\"option_title[]\" value=\"$option[1]\"> ";
						echo "</td>";
						echo "<td><input type=\"text\" name=\"option_id[]\" value=\"\"  data-detail-input=\"option_id\" class=\"inputFull\"/>";
						echo "<input type=\"hidden\" name=\"current_option_id[]\" value=\"\" class=\"inputFull\"/></td>";
						echo "<td>";
						echo "<select name=\"option_type[]\" data-detail-input=\"type\">";
						echo "<option value=\"+\">+</option>";
						echo "<option value=\"-\">-</option>";
						echo "</select> ";
						echo "<input type=\"text\" name=\"option_price[]\" data-detail-input=\"price\" class=\"input100\" />";
						echo "</td>";
						echo "<td><input type=\"text\" name=\"stock[]\" data-detail-input=\"stock\" value=\"\" class=\"inputFull\"/></td>";
						echo "<td><input type=\"text\" name=\"safe_stock[]\" data-detail-input=\"safety_stock\" value=\"\" class=\"inputFull\"/></td>";
						echo "<td><div class=\"designCheck noText\">";
						echo "<input type=\"hidden\" name=\"use_soldout_value[]\" data-detail-soldout value=\"N\"/>";
						echo "<input type=\"checkbox\" data-detail-input=\"use_soldout\" name=\"option_use_sold[]\" id=\"ous_".$tr_count."\"  value=\"Y\"/><label for=\"ous_".$tr_count."\">품절사용</label></div>";
						echo "</td>";
						echo "<td>";
						echo "<select name=\"detail_state[]\" id=\"\" class=\"inputFull\" data-detail-input=\"state\" >";
						echo "<option value=\"Y\">판매함</option>";
						echo "<option value=\"N\">판매안함</option>";
						echo "</select>";
						echo "</td>";
						echo "</tr>";
						$tr_count++;
					}
				}
			}
		}
	}
	data_load_process($data);
?>
</tbody>
</table>
