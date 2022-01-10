<?
	$query_ = "SELECT * FROM koweb_menu_config WHERE menu_id = '$mid'";
	$result_ = mysqli_query($connect, $query_);
	$row_ = mysqli_fetch_array($result_);
	//$mid_length = floor(strlen($row_[menu_id]) / 2);
	$depth_history = explode("|", $row_[depth_history]);
	if(!$mode) $mode = "login";
?>	
	<!-- lnb/ snb 노출 -->
	<div class="area_lnb">
		<div class="inr">
			<a href="/" class="btn_home">HOME</a>
			<!-- lnb -->
			<nav class="lnb">
				<button type="button" title="2차메뉴열기"><span>일반현황</span></button>
				<ul>
					<? if($_SESSION[member_id]){?>
					<li><a href="/member/page.html?mid=member&mode=check&return_mode=modify" id="modify" data-member-lnb="check">정보수정</a></li>
					<li><a href="/member/page.html?mid=member&mode=check&return_mode=secession" id="secession">회원탈퇴</a></li>
					<li><a href="/member/page.html?mid=member&mode=logout" id="logout">로그아웃</a></li>
					<? } else {  ?>
					<li><a href="/member/page.html?mid=member&mode=agree" id="agree" data-member-lnb="join">회원가입</a></li>
					<li><a href="/member/page.html?mid=member" id="login">로그인</a></li>
					<li><a href="/member/page.html?mid=member&mode=find_id" id="find_id">아이디찾기</a></li>
					<li><a href="/member/page.html?mid=member&mode=find_pw" id="find_pw">비밀번호 찾기</a></li>
					<? } ?>
				</ul>
			</nav>
			<script type="text/javascript">
				$("#"+"<?=$mode?>").addClass("on");
				$("[data-member-lnb="+"<?=$mode?>"+"]").addClass("on");
			</script>

			<!-- //lnb -->
			
			<!-- snb -->
			<!--<div class="snb">
				<button type="button" title="3차메뉴열기"><span>일반현황</span></button>
				<? print_lnb($connect, $site_language, "pc", $row_[ref_group], $depth_history[1], 3, 3); ?>
			</div>
			<script type="text/javascript">
				if(!$("div .snb").find("ul").length){
					$("div .snb").hide();
				}
			</script>
			-->
			<!-- //snb -->
		</div>
	</div>
<!--
	<div class="area_tab">
		<? print_lnb($connect, $site_language, "pc", $row_[ref_group], $depth_history[2], 4, 4); ?>
	</div>
	<script type="text/javascript">
		if(!$("div .area_tab").find("ul").length){
			$("div .area_tab").hide();
		}
	</script>
	-->