
function PrintElem(elem){
	Popup($(elem).html());
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

		});

		if (!chk) { return false; }

		//개인정보 정보제공 동의등, 동의가 필요한 항목일때
		if($("input[type=radio][data-radio-type=agree]").length){
			if($("input[type=radio][data-radio-type=agree]:checked").val() != "Y"){
				alert("개인정보 정보제공 동의에 동의하셔야 합니다.");
				$(this).find('input:radio[data-radio-type=agree]').first().focus();
				chk = false;
				return false;
			}
		}

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

$(function() {
	if($(".datepicker").length){
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
		});
	}
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
