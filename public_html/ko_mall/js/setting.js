

//////////////////////////////////////////공  통/////////////////////////////////////////////

$(function() {
  $( ".datepicker" ).datepicker({
    dateFormat: 'yy-mm-dd',
    prevText: '이전 달',
    nextText: '다음 달',
    monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
    monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
    dayNames: ['일','월','화','수','목','금','토'],
    dayNamesShort: ['일','월','화','수','목','금','토'],
    dayNamesMin: ['일','월','화','수','목','금','토'],
    showMonthAfterYear: true,
    changeMonth: true,
    changeYear: true,
    showOn: "button",
    //yearSuffix: '년'
  });
});


//하위 메뉴등록 SETUP
function show_and_setup(el,  ref_no, ref_group, title){
	var $el = $(el);
	$el.fadeIn();
	$el.children('div').layerCenter();
	$('body').addClass('active');
	setTimeout(function(){
		$el.addClass('active');
	}, 100);

	$("[data-ref-title]").text(title);
	$("[data-menu-setting=ref_no]").val(ref_no);
	$("[data-menu-setting=ref_group]").val(ref_group);
	return false;
}

function showPopup(el, data){
	var $el = $(el);

	if($("[data-search-content]").length){
		$("[data-search-content]").val("");
		$("[data-add-menu=name]").parent("tr").show();
	}

	if($("[data-search-menu]").length){
		$("[data-search-menu]").val("");
		$("[data-add-menu=name]").parent("tr").show();
	}

	if($el.find($("#apply_hidden")).length){
		$el.find($("#apply_hidden")).val(data);
	}
	$el.fadeIn();
	$el.children('div').layerCenter();
	$('body').addClass('active');
	setTimeout(function(){
		$el.addClass('active');
	}, 100);
	return false;
}


//회원 아이디/패스워드 유효성검사
function check_infomation(data_type, mode, data){
	$.ajax({
		type : "POST",
		url : "/ko_mall/inc/data_check_ajax.php",
		dataType : "json",
		data : {
			data_type : data_type,
			type : mode,
			variable : data
		},
		success : function(args) {
			if(args.result == "true"){
				if(mode == "id"){
					if("<?=$mode?>" != "modify"){
						var id_length = data.length;
						var id_pattern = new RegExp(/^[a-z0-9_]+$/);

						if (!id_pattern.test(data)) {
							$("[data-result="+mode+"]").html("아이디는 영문, 숫자 혼합만 사용 가능합니다.");
							$("[data-"+mode+"-check]").data(mode+"-check",false);
							return false;
						}
						if(id_length < 4 || id_length > 20){
							$("[data-result="+mode+"]").html("아이디는 한글, 특수문자를 제외한 4~20자까지의 영문과 숫자로 입력해주세요.");
							$("[data-"+mode+"-check]").data(mode+"-check",false);
							return false;
						}
					}
				} else if(mode == "password"){
                    var pwd_length = data.length;
					var num = data.search(/[0-9]/g);
					var eng = data.search(/[a-z]/ig);
					var special = data.search(/[~!@\#$%^*\()\-=+_']/gi);

					if(("<?=$mode?>" == "modify" && pwd_length > 0) || "<?=$mode?>" != "modify"){
						if ( pwd_length < 8 || pwd_length > 12) {
							$("[data-result="+mode+"]").html("비밀번호는 8~12자 사이로 입력해 주세요.");
							$("[data-"+mode+"-check]").data(mode+"-check",false);
							return false;
						}

						if(num < 0 || eng < 0 || special < 0){
							$("[data-result="+mode+"]").html("비밀번호는 영문, 특수문자(~!@\#$%^*\()\-=+_'), 숫자 조합 형태로 입력해 주세요.");
							$("[data-"+mode+"-check]").data(mode+"-check",false);
							return false;
						}
					}
				}

				$("[data-"+mode+"-check]").data(mode+"-check",true);

				if(mode == "password" && "<?=$mode?>" == "modify" && pwd_length == "0"){
					$("[data-result="+mode+"]").html("");
				} else {
					$("[data-result="+mode+"]").html("등록가능한 "+mode+"입니다.");
				}

			} else if(args.result == "not_data"){
				$("[data-"+mode+"-check]").data(mode+"-check",false);
				$("[data-result="+mode+"]").html(mode+"를 입력해주세요.");
			} else {
				$("[data-"+mode+"-check]").data(mode+"-check",false);
				$("[data-result="+mode+"]").html("등록 불가능한 "+mode+"입니다.");
			}
		}
	});
}

//메뉴 디렉토리, 중복검사
function check_infomation2(data_type, mode, category, data){
	$.ajax({
		type : "POST",
		url : "/ko_mall/inc/data_check_ajax2.php",
		dataType : "json",
		data : {
			data_type : data_type,
			category : category,
			type : mode,
			variable : data
		},
		success : function(args) {
			if(args.result == "true"){
				if(mode == "id"){
					if("<?=$mode?>" != "modify"){
						var id_length = data.length;
						var id_pattern = new RegExp(/^[a-z0-9_]+$/);

						if (!id_pattern.test(data)) {
							$("[data-result="+mode+"]").html("아이디는 영문, 숫자 혼합만 사용 가능합니다.");
							$("[data-"+mode+"-check]").data(mode+"-check",false);
							return false;
						}
						if(id_length < 4 || id_length > 20){
							$("[data-result="+mode+"]").html("아이디는 한글, 특수문자를 제외한 4~20자까지의 영문과 숫자로 입력해주세요.");
							$("[data-"+mode+"-check]").data(mode+"-check",false);
							return false;
						}
					}
				} else if(mode == "password"){
					var pwd_length = data.length;
					var num = data.search(/[0-9]/g);
					var eng = data.search(/[a-z]/ig);

					if(("<?=$mode?>" == "modify" && pwd_length > 0) || "<?=$mode?>" != "modify"){
						if ( pwd_length < 4 || pwd_length > 12) {
							$("[data-result="+mode+"]").html("비밀번호는 4~12자 사이로 입력해 주세요.");
							$("[data-"+mode+"-check]").data(mode+"-check",false);
							return false;
						}

						if(num < 0 || eng < 0){
							$("[data-result="+mode+"]").html("비밀번호는 영문과 숫자 조합 형태로 입력해 주세요.");
							$("[data-"+mode+"-check]").data(mode+"-check",false);
							return false;
						}
					}
				}

				$("[data-"+mode+"-check]").data(mode+"-check",true);

				if(mode == "password" && "<?=$mode?>" == "modify" && pwd_length == "0"){
					$("[data-result="+mode+"]").html("");
				} else {
					$("[data-result="+mode+"]").html("등록가능한 "+mode+"입니다.");
				}

			} else if(args.result == "not_data"){
				$("[data-"+mode+"-check]").data(mode+"-check",false);
				$("[data-result="+mode+"]").html(mode+"를 입력해주세요.");
			} else {
				$("[data-"+mode+"-check]").data(mode+"-check",false);
				$("[data-result="+mode+"]").html("등록 불가능한 "+mode+"입니다.");
			}
		}
	});
}


//////////////////////////////////////////상품분류/////////////////////////////////////////////

//상품분류 순서이동
$(document).on("click","[data-pcate-sort]",function(){
//	$("[data-menu-sort]").click(function(){

	if($(this).data("pcate-sort") == "up"){
		var sort_mode = "up";
	} else {
		var sort_mode = "down";
	}
	var no = $(this).data("pcate-setvalue");
	var category = $("#category").val();

	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/product_category/product_category_sort_ajax.php",
		data : {
			sort_mode : sort_mode,
			no : no,
			category : category
		},
		success : function(args) {
			//console.log(args);
			//location.reload();
			product_category_list("list", category, no);

		},
		beforeSend: function () {
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
});

//상품분류 뽑기
function product_category_list(mode, category, no, type){
	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/product_category/product_category_list_ajax.php",
		data : {
			category : category,
			mode : mode,
			no : no,
			type : type
		},
		success : function(args) {
			//console.log(args);
			$("[data-list-view]").html(args);
			//location.reload();
		},
		beforeSend: function () {
		//$("#div_ajax_load_image").center();
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
}

//상품분류 카테고리등록
$(document).on("click","[data-pcate-probutton]",function(){

	$("#popLayer02").fadeIn();
	$("#popLayer02").children('div').layerCenter();
	$('body').addClass('active');
	setTimeout(function(){
		$("#popLayer02").addClass('active');
	}, 100);

	$("input[type=hidden][name=mode]").val("cate_write_proc");
	$("input[type=hidden][name=no]").val("");
	$("#category").val("");
	return false;
});

//상품분류 카테고리수정
$(document).on("click","[data-pcate-modi]",function(){

	var data_id = $(this).data("pcate-modi");
	var data_title = $(this).data("cate-title");
	var menu_tit = $(this).data("me-title");

	$("#popLayer02").fadeIn();
	$("#popLayer02").children('div').layerCenter();
	$('body').addClass('active');
	setTimeout(function(){
		$("#popLayer02").addClass('active');
	}, 100);

	$("input[type=hidden][name=mode]").val("cate_modify_proc");
	$("input[type=hidden][name=no]").val(data_id);
	$("#category").parent("td").text(data_title);
	$("#category").remove();
	$("#title").val(menu_tit);
	$("input:radio[name='state']:radio[value='"+$(this).data("cate-stat")+"']").attr("checked",true);
	return false;
});

//분류 클릭시 set
$(document).on("click","[data-pcate-set]",function(){
	//$("[data-set-value]").click(function(){
	var no = $(this).data("pcate-set");

	$("input[type=radio], input[type=checkbox]").each(function(){
		$(this).prop("checked", false);
	});
	$("[data-pcate-set]").each(function(){
		$(this).removeClass("on");
	});

	$(this).addClass("on");

	$.ajax({
		type : "POST",
		dataType : "json",
		url : "/ko_mall/setting/product_category/product_category_setup_ajax.php",
		data : {
			no :no
		},
		success : function(args) {
			$("#title").val(args.title);
			//체크박스 + 라디오는 초기화 후
			$(":checkbox[name='use_device_pc'][value='"+args.use_device_pc+"']").prop("checked", true);
			$(":checkbox[name='use_device_mob'][value='"+args.use_device_mob+"']").prop("checked", true);
			$("input:radio[name='state']:radio[value='"+args.state+"']").prop("checked",true);
			$("#memo").val(args.memo);
            $("#category_id").text(args.cateogry_id);
			$("[data-pcate-proc]").data("pcate-proc", no);
			$("input:radio[name='use_type']:radio[value='"+args.use_type+"']").prop("checked",true);
			$("input:radio[name='use_realname']:radio[value='"+args.use_realname+"']").prop("checked",true);
			$("input:radio[name='use_sell']:radio[value='"+args.use_sell+"']").prop("checked",true);
			$("input:radio[name='use_19']:radio[value='"+args.use_19+"']").prop("checked",true);
			$("input:radio[name='state']:radio[value='"+args.state+"']").attr("checked",true);
		},
		beforeSend: function () {
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
});

//분류 처리
$(document).on("click","[data-pcate-proc]",function(){
	var no = $(this).data("pcate-proc");
	var mode = $(this).data("pcateproc-mode");
	var title = $("#title").val();
	var use_type = $("input[name='use_type']:checked").val();
	var use_device_pc = $("#use_device_pc:checked").val();
	var use_device_mob = $("#use_device_mob:checked").val();
	var state = $("input[type=radio][name=state]:checked").val();
	var use_realname = $("input[type=radio][name=use_realname]:checked").val();
	var use_19 = $("input[type=radio][name=use_19]:checked").val();
	var use_sell = $("input[type=radio][name=use_sell]:checked").val();
	var memo = $("#memo").val();
	var category = $("#category").val();

	if(mode == "delete"){
		if(!confirm("상위 분류 삭제시 하위 분류 및 상품 모두 사용이 불가능합니다.\r\n\r\n삭제된 데이터는 복구 하실 수 없습니다.\r\n\r\n해당 분류가 사이트에서 표시 되지 않게 하시려면 노출을 사용안함으로 설정해주세요.\r\n\r\n\r\n정말 삭제하시겠습니까?")){
			return false;
		}
	}
	if(!no) {
		alert("메뉴를 선택해주세요");
		return false;
	}

	if(mode == "modify"){
		if(!title) {
			alert("분류명을 입력해주세요");
			return false;
		}
		if(state != "Y" && state != "N") {
			alert("노출상태를 선택해주세요");
			return false;
		}

		if(use_realname != "Y" && use_19 == "Y"){
			alert("본인인증을 사용하지 않으면 성인제한선택을 이용할 수 없습니다.")
			return false;
		}
	}

	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/product_category/product_category_proc_ajax.php",
		data : {
			category : category,
			mode : mode,
			no : no,
			title : title,
			use_device_pc : use_device_pc,
			use_device_mob : use_device_mob,
			use_type : use_type,
			use_realname : use_realname,
			use_19 : use_19,
			use_sell : use_sell,
			state : state,
			memo : memo
		},
		success : function(args) {

			product_category_list("tree", category, no);
			//$("[data-set-value=9]").css("background-color","red");
			//alert($("[data-set-value="+menu_no+"]").data("set-value"));
		},
		beforeSend: function () {
		//$("#div_ajax_load_image").center();
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
});

//////////////////////////////////////////메뉴/////////////////////////////////////////////

//메뉴 뽑기
function menu_list(mode, category, no, type){
	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/menu/menu_list_ajax.php",
		data : {
			category : category,
			mode : mode,
			no : no,
			type : type
		},
		success : function(args) {
			//console.log(args);
			$("[data-list-view]").html(args);
			//location.reload();
		},
		beforeSend: function () {
		//$("#div_ajax_load_image").center();
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
}

//컨텐츠관리 메뉴 뽑기
function content_list(){
	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/content/content_list_ajax.php",
		success : function(args) {
			//console.log(args);
			$("[data-content-vlist]").html(args);
			//location.reload();
		},
		beforeSend: function () {
		//$("#div_ajax_load_image").center();
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
}

//메뉴분류 등록
$(document).on("click","[data-cate-probutton]",function(){


	$("#popLayer02").fadeIn();
	$("#popLayer02").children('div').layerCenter();
	$('body').addClass('active');
	setTimeout(function(){
		$("#popLayer02").addClass('active');
	}, 100);

	$("input[type=hidden][name=mode]").val("cate_write_proc");
	$("input[type=hidden][name=no]").val("");
	$("#menu_category").val("");
	return false;
});

//메뉴분류 수정
$(document).on("click","[data-cate-modi]",function(){

	var data_id = $(this).data("cate-modi");
	var data_title = $(this).data("cate-title");
	var menu_tit = $(this).data("me-title");

	$("#popLayer02").fadeIn();
	$("#popLayer02").children('div').layerCenter();
	$('body').addClass('active');
	setTimeout(function(){
		$("#popLayer02").addClass('active');
	}, 100);

	$("input[type=hidden][name=mode]").val("cate_modify_proc");
	$("input[type=hidden][name=no]").val(data_id);
	$("#menu_category").parent("td").text(data_title);
	$("#menu_category").remove();
	$("#menu_title").val(menu_tit);
	$("input:radio[name='state']:radio[value='"+$(this).data("cate-stat")+"']").attr("checked",true);
	return false;
});

//메뉴관리 메뉴 순서이동
$(document).on("click","[data-menu-sort]",function(){
//	$("[data-menu-sort]").click(function(){

	if($(this).data("menu-sort") == "up"){
		var sort_mode = "up";
	} else {
		var sort_mode = "down";
	}
	var menu_no = $(this).data("menu-setvalue");
	var category = $("#category").val();

	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/menu/menu_sort_ajax.php",
		data : {
			sort_mode : sort_mode,
			menu_no : menu_no,
			category : category
		},
		success : function(args) {
			//console.log(args);
			//location.reload();
			menu_list("list", category, menu_no);

		},
		beforeSend: function () {
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
});

//메뉴 클릭시 set
$(document).on("click","[data-menu-set]",function(){
	//$("[data-set-value]").click(function(){
	var menu_no = $(this).data("menu-set");

	$("input[type=radio], input[type=checkbox]").each(function(){
		$(this).prop("checked", false);
	});
	$("[data-menu-set]").each(function(){
		$(this).removeClass("on");
	});

	$(this).addClass("on");

	$.ajax({
		type : "POST",
		dataType : "json",
		url : "/ko_mall/setting/menu/menu_setup_ajax.php",
		data : {
			menu_no : menu_no
		},
		success : function(args) {
			$("#menu_title").val(args.menu_title);
			//체크박스 + 라디오는 초기화 후

			$(":checkbox[name='use_device_pc'][value='"+args.use_device_pc+"']").prop("checked", true);
			$(":checkbox[name='use_device_mob'][value='"+args.use_device_mob+"']").prop("checked", true);
			$("input:radio[name='state']:radio[value='"+args.state+"']").prop("checked",true);
			$("#memo").val(args.memo);
			$("#content_id").val(args.content_id);
			$("#link_menu_id").val(args.link_menu_id);
			$("#link_title").val(args.link_title);
			$("#content_id").val(args.content_id);
			$("#content_title").val(args.content_title);
			$("#description").val(args.description);
			$("#og_description").val(args.og_description);
			$("#og_sitename").val(args.og_sitename);
			$("#og_title").val(args.og_title);
			$("[data-menu-proc]").data("menu-proc", menu_no);
			$("input:radio[name='use_type']:radio[value='"+args.use_type+"']").prop("checked",true);
			$("input:radio[name='state']:radio[value='"+args.state+"']").attr("checked",true);
			$("[data-view-cont]").attr("href", args.view_content);
			$("[data-cont-url]").text(args.view_content);
		},
		beforeSend: function () {
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
});

//메뉴 처리
$(document).on("click","[data-menu-proc]",function(){
//	$("[data-proc-value]").click(function(){
	var menu_no = $(this).data("menu-proc");
	var mode = $(this).data("proc-mode");
	var menu_title = $("#menu_title").val();
	var content_id = $("#content_id").val();
	var link_menu_id = $("#link_menu_id").val();
	var use_type = $("input[name='use_type']:checked").val();
	var use_device_pc = $("#use_device_pc:checked").val();
	var use_device_mob = $("#use_device_mob:checked").val();
	var state = $("input[type=radio][name=state]:checked").val();
	var memo = $("#memo").val();
	var description = $("#description").val();
	var og_description = $("#og_description").val();
	var og_sitename = $("#og_sitename").val();
	var og_title = $("#og_title").val();
	var content_id = $("#content_id").val();
	var category = $("#category").val();

	if(mode == "delete"){
		if(!confirm("상위 메뉴 삭제시 하위 메뉴 모두 사용이 불가능합니다.\r\n\r\n삭제된 데이터는 복구 하실 수 없습니다.\r\n\r\n해당 메뉴가 사이트에서 표시 되지 않게 하시려면 노출을 사용안함으로 설정해주세요.\r\n\r\n\r\n정말 삭제하시겠습니까?")){
			return false;
		}
	}
	if(!menu_no) {
		alert("메뉴를 선택해주세요");
		return false;
	}
	if(mode == "modify"){
		if(!menu_title) {
			alert("메뉴명을 입력해주세요");
			return false;
		}
		if(!use_type){
			alert("메뉴타입을 선택해주세요.");
			return false;
		}
		if(state != "Y" && state != "N") {
			alert("노출상태를 선택해주세요");
			return false;
		}
		if(use_type == "link" && link_menu_id == ""){
		//	alert("연결할 메뉴를 선택해주세요.");
		//	return false;
		}
		if(use_type == "content" && content_id == ""){
		//	alert("연결할 컨텐츠를 선택해주세요.");
		//	return false;
		}
	}

	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/menu/menu_proc_ajax.php",
		data : {
			category : category,
			mode : mode,
			menu_no : menu_no,
			menu_title : menu_title,
			use_device_pc : use_device_pc,
			use_device_mob : use_device_mob,
			content_id : content_id,
			link_menu_id : link_menu_id,
			use_type : use_type,
			state : state,
			memo : memo,
			description : description,
			og_description : og_description,
			og_sitename : og_sitename,
			og_title : og_title
		},
		success : function(args) {

			menu_list("tree", category, menu_no);
			//$("[data-set-value=9]").css("background-color","red");
			//alert($("[data-set-value="+menu_no+"]").data("set-value"));
		},
		beforeSend: function () {
		//$("#div_ajax_load_image").center();
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
});

//메뉴 컨텐츠 선택 셋업
$(function(){
	$("[data-board-select]").click(function(){
		$("#ref_board").val($(this).data("board-select"));
		$("#board_title").val($(this).data("board-title"));
	});

	$("[data-online-select]").click(function(){
		$("#ref_online").val($(this).data("online-select"));
		$("#online_title").val($(this).data("online-title"));
	});

	$("[data-program-select]").click(function(){
		$("#ref_program").val($(this).data("program-select"));
		$("#program_title").val($(this).data("program-title"));
	});


	$("[data-product-select]").click(function(){
		$("#ref_product").val($(this).data("product-select"));
		$("#product_title").val($(this).data("product-title"));
	});

	$("[data-content-select]").click(function(){
		$("#content_id").val($(this).data("content-select"));
		$("#content_title").val($(this).data("content-title"));
	});

	$("[data-menu-select]").click(function(){
		$("#link_menu_id").val($(this).data("menu-select"));
		$("#link_title").val($(this).data("menu-title"));
	});
});


//////////////////////////////////////////컨텐츠/////////////////////////////////////////////

//히스토리 보드
function board_ajax(id, start){
	var page = start;
	if(!id){
		var id = $("[data-content-proc]").data("content-proc");
	}
	if( !page ) page = 0;

	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/content/history_ajax.php",
		data : {
			start : page,
			content_id : id
		},
		success: function (data) {
			$("[data-histroy-view]").html(data);
		},
		error : function( jqXHR, textStatus, errorThrown ) {
		//	alert( textStatus );
		//	alert( errorThrown );
		},beforeSend: function () {
		//$("#div_ajax_load_image").center();
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
}

// 컨텐츠 SETUP
$(document).on("click","[data-content-set]",function(){
	//$("[data-set-value]").click(function(){
	var content_no = $(this).data("content-set");
	var menu_type = $(this).data("menu-type");
	var menu_info = $(this).data("menu-info");

	$("input[type=radio], input[type=checkbox]").each(function(){
		$(this).prop("checked", false);
	});

	$("[data-content-set]").each(function(){
		$(this).removeClass("on");
	});

	$(this).addClass("on");
	$.ajax({
		type : "POST",
		dataType : "json",
		url : "/ko_mall/setting/content/content_setup_ajax.php",
		data : {
			content_no : content_no,
			menu_type : menu_type,
			menu_info : menu_info
		},
		success : function(args) {
			//console.log(args);
			//$("#state:radio[value='"+args.state+"']").attr("checked",true);
			//체크박스 + 라디오는 초기화 후
			$("[data-no-condiv]").css("display", "none");
			$("[data-detail-conview]").css("display","");

			if(menu_type == "link"){
				$("#link_menu_title").val(args.content_title);
				$("[data-location-menu]").data("location-menu", menu_info);
				$("[data-type-is=link]").css("display","");
				$("[data-type-is=content]").css("display","none");

			} else {
				if(args.content_title != null){
					$("[data-type-is=link]").css("display","none");
					$("[data-type-is=content]").css("display","");
					$("#content_title").val(args.content_title);
					$("#content_id").val(args.content_id);
					$("#web_content").val(args.web_content);
					$("#mob_content").val(args.mob_content);
					$("#ref_link").val(args.ref_link);
					//$("#ref_target").val(args.ref_target);
					$("#ref_program").val(args.ref_program);
					$("#ref_board").val(args.ref_board);
					$("#ref_online").val(args.ref_online);
					$("#memo").val(args.memo);
					$("#board_title").val(args.ref_board_title);
					$("#program_title").val(args.ref_program_title);
					$("#product_title").val(args.ref_product_title);
					$("#online_title").val(args.ref_online_title);
					if($("#smart_content_mob").length && $("#smart_content").length){
						CKEDITOR.instances.smart_content.setData(args.web_content);
						CKEDITOR.instances.smart_content_mob.setData(args.mob_content);
					}

					$("[data-content-proc]").data("content-proc", content_no);
					$(":checkbox[name='ref_target'][value='"+args.ref_target+"']").prop("checked", true);
					$("input:radio[name='content_type']:radio[value='"+args.content_type+"']").prop("checked",true);
					$("[data-type-view]").css("display","none");
					$("[data-type-view="+args.content_type+"]").css("display","");
					$("input:radio[name='state']:radio[value='"+args.state+"']").prop("checked",true);

					board_ajax(args.content_id, 0);
				} else {
					$("[data-no-condiv]").css("display", "");
					$("[data-detail-conview]").css("display","none");
					$("[data-no-condivhref]").attr("href","/ko_mall/index.html?type=setting&core=manager_setting&manager_type=menu&display="+menu_info);
				}
			}
		},
		beforeSend: function () {
		//$("#div_ajax_load_image").center();
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
});

//컨텐츠 처리
$(document).on("click","[data-content-proc]",function(){
//	$("[data-proc-value]").click(function(){
	var content_no = $(this).data("content-proc");
	var mode = $(this).data("proc-mode");
	var content_title = $("#content_title").val();
	var content_id = content_no;
	var content_type = $("input[type=radio][name=content_type]:checked").val();
	var web_content = CKEDITOR.instances.smart_content.getData();
	var mob_content = CKEDITOR.instances.smart_content_mob.getData();
	var ref_link = $("#ref_link").val();
	var ref_target = $("#ref_target:checked").val();
	var ref_program = $("#ref_program").val();
	var ref_board = $("#ref_board").val();
	var ref_online = $("#ref_online").val();
	var ref_product = $("#ref_product").val();
	var memo = $("#memo").val();
	var state = $("input[type=radio][name=state]:checked").val();

	if(!content_no) {
		alert("컨텐츠를 선택해주세요");
		return false;
	}

	if(mode == "delete"){
		if(!confirm("해당 컨텐츠가 등록된 메뉴가 있을시 컨텐츠가 정상적으로 표시되지 않습니다.\r\n\r\n\r\n정말 삭제하시겠습니까?")){
			return false;
		}
	} else {
		if(!content_title) {
			alert("컨텐츠명을 입력해주세요");
			return false;
		}

		if(!content_type){
			alert("컨텐츠타입을 선택해주세요.");
			return false;
		}
	}
/*
	if(state != "Y" && state != "N") {
		alert("노출상태를 선택해주세요");
		return false;
	}
*/
	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/content/content_proc_ajax.php",
		data : {
			mode : mode,
			content_no : content_no,
			content_title : content_title,
			content_id : content_id,
			content_type : content_type,
			web_content : web_content,
			mob_content : mob_content,
			ref_link : ref_link,
			ref_target : ref_target,
			ref_program : ref_program,
			ref_board : ref_board,
			ref_online : ref_online,
			ref_product : ref_product,
			memo : memo,
			state : state
		},
		success : function(args) {
			content_list();
			board_ajax(content_id, 0);
			//menu_list("tree", content_no);
			//$("[data-set-value=9]").css("background-color","red");
			//alert($("[data-set-value="+menu_no+"]").data("set-value"));

			if(mode == "delete"){
				location.reload();
			}

		},
		beforeSend: function () {
		//$("#div_ajax_load_image").center();
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
});

//히스토리 보기 SETUP
function history_setup(el, history_no){

	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/content/history_view_ajax.php",
		dataType : "json",
		data : {
			history_no : history_no
		},
		success : function(args) {
			$("#history_title").val(args.history_title);
			$("#histroy_type").val(args.content_type);
			$("#history_memo").val(args.memo);
			$("#history_board").val(args.ref_board_title);
			$("#history_board_id").val(args.ref_board);
			$("#history_program").val(args.ref_program_title);
			$("#history_program_id").val(args.ref_program);
			$("#history_online").val(args.ref_online_title);
			$("#history_online_id").val(args.ref_online);
			$("#history_product").val(args.ref_product_title);
			$("#history_product_id").val(args.ref_product);
			$("#history_url").val(args.ref_link);
			$("[data-history-type]").each(function(){
				$(this).css("display", "none");
			});
			$("[data-history-type="+args.content_type+"]").css("display","");
			$("[data-history-repair]").data("history-repair", args.history_no);
			$("[data-target-id]").data("target-id", args.target_id);

			$(":checkbox[name='history_target'][value='"+args.ref_target+"']").prop("checked", true);

			CKEDITOR.instances.history_content.setData(args.web_content);
			CKEDITOR.instances.history_content_mob.setData(args.mob_content);

			//console.log(args);
		},
		beforeSend: function () {
		//$("#div_ajax_load_image").center();
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();

			var $el = $(el);
			$el.fadeIn();
			$el.children('div').layerCenter();
			$('body').addClass('active');
			setTimeout(function(){
				$el.addClass('active');
			}, 100);

		}
	});
}

//히스토리 처리
$(document).on("click","[data-history-repair]",function(){
//	$("[data-proc-value]").click(function(){
	var history_no = $(this).data("history-repair");
	var target_id = $(this).data("target-id");

		console.log(history_no);

	if(!history_no) {
		alert("컨텐츠를 선택해주세요 NOT HNO");
		return false;
	}

	if(!target_id) {
		alert("올바르지않은 접근입니다. NOT TID");
		return false;
	}

	$.ajax({
		type : "POST",
		url : "/ko_mall/setting/content/history_repair_ajax.php",
		dataType : "json",
		data : {
			history_no : history_no,
			target_id : target_id
		},
		success : function(args) {
			if(args.history_result == true){
				$("#content_title").val(args.content_title);
				$("#content_id").val(args.content_id);
				$("#web_content").val(args.web_content);
				$("#mob_content").val(args.mob_content);
				$("#ref_link").val(args.ref_link);
				//$("#ref_target").val(args.ref_target);
				$("#ref_program").val(args.ref_program);
				$("#ref_board").val(args.ref_board);
				$("#ref_online").val(args.ref_online);
				$("#online_title").val(args.ref_online_title);
				$("#program_title").val(args.ref_program_title);
				$("#ref_product").val(args.ref_product);
				$("#product_title").val(args.ref_product_title);
				$("#memo").val(args.memo);
				$("#state").val(args.state);
				$("#board_title").val(args.ref_board_title);
				CKEDITOR.instances.smart_content.setData(args.web_content);
				CKEDITOR.instances.smart_content_mob.setData(args.mob_content);

				$("input:radio[name='content_type']:radio[value='"+args.content_type+"']").prop("checked",true);
				$("[data-type-view]").css("display","none");
				$("[data-type-view="+args.content_type+"]").css("display","");
				content_list();
				board_ajax(target_id, 0);
				alert("복원 되었습니다.");
			} else {
				alert("복원에 실패하였습니다.");

			}

			$('.popBox').parent('div').removeClass('active').fadeOut();
			$('body').removeClass('active');
		},
		beforeSend: function () {
		//$("#div_ajax_load_image").center();
			$("#div_ajax_load_image").show();
		},
		complete: function () {
			$("#div_ajax_load_image").hide();
		}
	});
});

//히스토리 처리
$(document).on("click","[data-location-menu]",function(){
	var target_id = $(this).data("location-menu");
	//alert(target_id);
	location.href = "/ko_mall/index.html?type=setting&core=manager_setting&manager_type=menu&display="+target_id;

});

$(document).ready(function() {
	$('form').submit(function() {
		var chk = true;

		//셀렉트 체크
		$('.required_select').each(function() {
			if (!$(this).val()) {
				alert($(this).attr('title') + "을 선택하세요.");
				$(this).focus();
				chk = false;
				return false;
			}
		});
		if (!chk) { return false; }

		// 라디오 버튼 체크
		$('.required_radio').each(function() {

			var radio_name = $(this).attr("name");

			if(!$("input[type=radio][name="+radio_name+"]").is(":checked")){
				alert($(this).attr('title') + "을 선택하세요.");
				$(this).find('input:radio').first().focus();
				chk = false;
				return false;
			}
			//개인정보 정보제공 동의등, 동의가 필요한 항목일때
			if($("input[type=radio][data-radio-type=agree]").length){
				if($("input[type=radio][data-radio-type=agree]:checked").val() != "Y"){
					alert($(this).attr('title') + "에 동의하셔야 합니다.");
					$(this).find('input:radio').first().focus();
					chk = false;
					return false;
				}
			}
		});

		if (!chk) { return false; }

		// 체크박스 버튼 체크
		$('.required_checkbox').each(function() {

			var checkbox_type = $(this).data("checkbox-type");

			//최소한개 선택
			if(!$("input[type=checkbox][data-checkbox-type="+checkbox_type+"]").is(":checked")){
				alert($(this).attr('title') + "을 선택하세요.");
				$(this).find('input:checkbox').first().focus();
				chk = false;
				return false;
			}

		/*
			// 최대 선택
			if ($(this).find('input:checkbox:checked').length > $(this).attr('limit')) {
				alert($(this).attr('title') + "는 " + $(this).attr('limit') + "개까지 선택이 가능합니다.");
				$(this).find('input:checkbox').first().focus();
				chk = false;
				return false;
			}


			// 필수 선택 수 적용
			if ($(this).find('input:checkbox:checked').length != $(this).attr('limit')) {
				alert($(this).attr('title') + "는 " + $(this).attr('limit') + "개까지 선택하셔야 합니다.");
				$(this).find('input:checkbox').first().focus();
				chk = false;
				return false;
			}
		*/

		});

		if (!chk) { return false; }

		$('.required').each(function() {
			if (!$(this).val()) {
				alert($(this).attr('title') + "을 입력하세요.");
				$(this).focus();
				chk = false;
				return false;
			}
		});

		if (!chk) { return false; }
	});
});

//임시값 담아두는 배열
var _tempTypeOption = new Array();
function changeTypeOptionDisable(obj){
	//다음 Element (타입세부 필드)
	var nextEl = obj.parentElement.nextElementSibling;
	var nextInput = nextEl.firstElementChild;
	var objCnt = nextInput.name.split('_').pop();

	switch (obj.value) {
		case "radio":
		case "select":
			//임시값을 되돌림
			if(_tempTypeOption[objCnt]) nextInput.value = _tempTypeOption[objCnt];
			nextInput.disabled = false;

			break;
		default:
			//임시값 배열에 없다면 임시값에 입력
			if(!_tempTypeOption[objCnt]) _tempTypeOption[objCnt] = nextInput.value;
			nextInput.disabled = true;
			nextInput.value = "";
	}
}

function printOrder(orderId){
var windowopen = window.open("/ko_mall/setting/order/print.html?orderid="+orderId,"주문서");
}
