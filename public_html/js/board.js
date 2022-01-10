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

function sms_send() {
	var phone_tmp1 = $("input[name='phone1']").val();
	var phone_tmp2 = $("input[name='phone2']").val();
	var phone_tmp3 = $("input[name='phone3']").val();

	if( phone_tmp1 == "" || phone_tmp2 == "" || phone_tmp3 == "" ){
		alert("휴대폰번호를 올바르게 입력해주세요.");
		return false;
	}

	if( phone_tmp1.length != 3 || phone_tmp2.length != 4 || phone_tmp3.length != 4 ){
		alert("휴대폰번호를 올바르게 입력해주세요.");
		return false;
	}

	var data = phone_tmp1 + "-" + phone_tmp2 + "-" + phone_tmp3;

	$.ajax({
		type : "POST",
		url : "/ko_mall/inc/sms_ajax.php",
		data : {
			data_send : data
		},
		success : function(args) {
				alert(args);
		}
	});
}

function sms_auth() {
	var sms_auth = $("input[name='sms_auth']").val();

	if( sms_auth == ""){
		alert("인증번호를 올바르게 입력해주세요.");
		return false;
	}

	var phone_tmp1 = $("input[name='phone1']").val();
	var phone_tmp2 = $("input[name='phone2']").val();
	var phone_tmp3 = $("input[name='phone3']").val();

	if( phone_tmp1 == "" || phone_tmp2 == "" || phone_tmp3 == "" ){
		alert("휴대폰번호를 올바르게 입력해주세요.");
		return false;
	}

	if( phone_tmp1.length != 3 || phone_tmp2.length != 4 || phone_tmp3.length != 4 ){
		alert("휴대폰번호를 올바르게 입력해주세요.");
		return false;
	}

	var data = phone_tmp1 + "-" + phone_tmp2 + "-" + phone_tmp3;

	$.ajax({
		type : "POST",
		dataType: 'json',
		url : "/ko_mall/inc/sms_auth_ajax.php",
		data : {
			sms_auth : sms_auth,
			data_send : data
		},
		success : function(request) {
			alert(request.message);
			$("#auth_message").html(request.tag);
			if(request.result == "true"){
				$("#sms_auth2").val("Y");
			}
		}
	});
}



$(document).ready(function() {

	var board_sms = $("#sms_auth_").val();
	var idmn = $("#is_admin_").val();
	var mode = $("#mode").val();
	$("#sms_auth_").remove();
	$("#is_admin_").remove();

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
			var temp_name = $(this).attr("name");
			if (!$(':radio[name="'+temp_name+'"]:checked').val()) {
				alert($(this).attr('title') + "을 선택하세요.");
				$(this).find('input:radio').first().focus();
				chk = false;
				return false;
			}
		});
		if (!chk) { return false; }

		// 체크박스 버튼 체크
		$('.required_checkbox').each(function() {

			// 최소 한개 선택
			if (!$(this).find('input:checkbox:checked').length) {
				alert($(this).attr('title') + "을 선택하세요.");
				$(this).find('input:checkbox').first().focus();
				chk = false;
				return false;
			}

			// 최대 선택
			if ($(this).find('input:checkbox:checked').length > $(this).attr('limit')) {
				alert($(this).attr('title') + "는 " + $(this).attr('limit') + "개까지 선택이 가능합니다.");
				$(this).find('input:checkbox').first().focus();
				chk = false;
				return false;
			}
			/*
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


		if(board_sms == "Y" && mode != "modify" && !idmn){
			$('.auth_required').each(function() {
				if ($(this).val() != "Y") {
					alert("인증을 진행해주세요.");
					chk = false;
					return false;
				}
			});

			if (!chk) { return false; }
		}

	});

});





	$(document).on('click', 'a[href="#"]', function(e){
		e.preventDefault();
	});

	$(function(){
		$('.btn_del').click(function(){
			$("."+$(this).attr('id')).toggle('show');
		});

		$('.btn_reply').click(function(){
			$("."+$(this).attr('id')).toggle('show');
		});

        $('.btn_modify').click(function(){
            $("."+$(this).attr('id')).toggle('show');
        });

		$('.hide_all').click(function(){
				//$("."+$(this).attr('id')).toggle('hide');
				$(this).closest("div").toggle('hide');
		});
	});

	function reply_check(idx){
		var chk = true;

		if($("#"+"re_comment_name_"+idx).val() == "") {
			alert("이름을 작성하세요");
			chk = false;
			return false;
		}
		if (!chk) { return false; }


		if($("#"+"re_comment_pw_"+idx).val() == "") {
			alert("비밀번호를 작성하세요");
			chk = false;
			return false;
		}
		if (!chk) { return false; }

		if($("#"+"re_comment_text_"+idx).val() == "") {
			alert("내용을 작성하세요");
			chk = false;
			return false;
		}
		if (!chk) { return false; }
	}



$(document).ready(function() {
	var chk = true;
	$(".comment_del_form").submit(function() {
		if($(this).children("input[type=password]").val() == ""){
			alert($(this).children('input[type=password]').attr('title')+"을 입력하세요");
			chk = false;
			return false;
		}
		if (!chk) { return false; }
	});

	$(".comment_append_form").submit(function() {
		if($(this).children("input[type=password]").val() == ""){
			alert($(this).children('input[type=password]').attr('title')+"을 입력하세요");
			chk = false;
			return false;
		}
		if (!chk) { return false; }
	});

	$(".comment_write").submit(function() {

		if($(this).children("input[type=comm_name]").val() == ""){
			alert($(this).children('input[type=comm_name]').attr('title')+"을 입력하세요");
			chk = false;
			return false;
		}
		if (!chk) { return false; }

		if($(this).children("input[type=comm_password]").val() == ""){
			alert($(this).children('input[type=comm_password]').attr('title')+"을 입력하세요");
			chk = false;
			return false;
		}
		if (!chk) { return false; }

		if($(this).children("textarea[type=comm_comments]").val() == ""){
			alert($(this).children('textarea[type=comm_comments]').attr('title')+"을 입력하세요");
			chk = false;
			return false;
		}
		if (!chk) { return false; }
	});



});
