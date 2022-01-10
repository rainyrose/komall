function PrintElem(elem){
	Popup($(elem).html());
}

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
			console.log(args);
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

function Popup(data){
	var mywindow = window.open('', 'print_area', 'height=400,width=600');
	mywindow.document.write('<html><head><title>my div</title>');
	mywindow.document.write("<link rel='stylesheet' type='text/css' href='/ko_admin/css/base.css'/>");
	mywindow.document.write("<link rel='stylesheet' type='text/css' href='/ko_admin/css/common.css'/>");
	mywindow.document.write("<link rel='stylesheet' type='text/css' href='/css/board.css'/>");
	mywindow.document.write('</head><body>');
	mywindow.document.write(data);
	mywindow.document.write("</body></html>");
	mywindow.print();
//	mywindow.document.close(); // IE >= 10에 필요
	//mywindow.focus(); // necessary for IE >= 10
	//mywindow.print();
//	mywindow.close();
	return true;
}

$(document).ready(function() {
	$('form').submit(function() {
		var chk = true;
		var mode = "<?=$mode?>";
		if(mode == "join"){
			var id_value = $("#id").val();
			var pwd_value = $("#password").val();
			var re_pwd_value = $("#password2").val();
			var id_length = $("#id").val().length;
			var pwd_length = $("#password").val().length;
			var id_pattern = new RegExp(/^[a-z0-9_]+$/);

			//id 길이
			if ( id_length < 4 || id_length > 20) {
				alert("아이디는 한글, 특수문자를 제외한 4~20자까지의 영문과 숫자로 입력해주세요.");
				$(this).focus();
				chk = false;
				return false;
			}

			if (!chk) { return false; }

			//id 패턴 (특수문자, 한글 제거)
			if (!id_pattern.test(id_value)) {
				alert("아이디는 영문, 숫자 혼합만 사용 가능합니다.");
				$(this).focus();
				chk = false;
				return false;
			}

			if (!chk) { return false; }

			//pw 길이
			if ( pwd_length < 4 || pwd_length > 12) {
				alert("비밀번호는 4~12자 사이의 영문과 숫자 조합 형태로 입력해 주세요.");
				$(this).focus();
				chk = false;
				return false;
			}

			if (!chk) { return false; }

			//pw 패턴 (영문, 숫자 조합형태)

			var num = pwd_value.search(/[0-9]/g);
			var eng = pwd_value.search(/[a-z]/ig);

			if(num < 0 || eng < 0){
				alert("비밀번호는 4~12자 사이의 영문과 숫자 조합 형태로 입력해 주세요.");
				$(this).focus();
				chk = false;
				return false;
			 }

			if (!chk) { return false; }

			//pw 확인
			if ( pwd_value !== re_pwd_value ) {
				alert("비밀번호와 비밀번호 확인의 값이 동일하지 않습니다.");
				$(this).focus();
				chk = false;
				return false;
			}

			if (!chk) { return false; }
		}


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

			if (!$(this).find('input:radio:checked').length) {
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

	});

});

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
	yearRange: "1940:",
	showOn: "button",
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
