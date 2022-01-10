function LPad(digit, size, attatch) {
    var add = "";
    digit = digit.toString();

    if (digit.length < size) {
        var len = size - digit.length;
        for (i = 0; i < len; i++) {
            add += attatch;
        }
    }
    return add + digit;
}


function makeoid() {
	var now = new Date();
	var years = now.getFullYear();
	var months = LPad(now.getMonth() + 1, 2, "0");
	var dates = LPad(now.getDate(), 2, "0");
	var hours = LPad(now.getHours(), 2, "0");
	var minutes = LPad(now.getMinutes(), 2, "0");
	var seconds = LPad(now.getSeconds(), 2, "0");
	var timeValue = years + months + dates + hours + minutes + seconds;
	//document.getElementById("LGD_OID").value = "test_" + timeValue;
	//document.getElementById("LGD_TIMESTAMP").value = timeValue;
	return timeValue;
}

/*
* 인증요청 처리
*/

function get_result(data){

	return data;
}

function set_session(callback,param){
    $.ajax({
        type : "POST",
        data : $("#LGD_PAYINFO").serialize(),
        url : "/ko_mall/tm/payreq_set_session.php",
        success : function(args) {
                callback(param);
        }, error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR.responseText);
        }
    });
}

function pay_result(obj){

//	console.log(obj.LGD_CUSTOM_USABLEPAY);

	var CST_PLATFORM = obj.CST_PLATFORM;
	var CST_MID = obj.CST_MID;
	var LGD_OID = obj.LGD_OID;
	var LGD_AMOUNT = obj.LGD_AMOUNT;
	var LGD_BUYER = obj.LGD_BUYER;
	var LGD_PRODUCTINFO = obj.LGD_PRODUCTINFO;
	var LGD_BUYEREMAIL = obj.LGD_BUYEREMAIL;
	var LGD_CUSTOM_USABLEPAY = obj.LGD_CUSTOM_USABLEPAY;
	var LGD_WINDOW_TYPE = obj.LGD_WINDOW_TYPE;
	var LGD_CUSTOM_SWITCHINGTYPE = obj.LGD_CUSTOM_SWITCHINGTYPE;
	var LGD_CUSTOM_USABLEPAY = obj.LGD_CUSTOM_USABLEPAY;
    var IS_MOBILE = obj.IS_MOBILE;

    var ajax_url = "/ko_mall/lguplus/payreq_crossplatform.php";
    if(IS_MOBILE == true){
        ajax_url = "/ko_mall/tm/payreq_crossplatform.php";
    }



	$.ajax({
		type : "POST",
		data : {
			CST_PLATFORM : CST_PLATFORM,
			CST_MID : CST_MID,
			LGD_OID : LGD_OID,
			LGD_AMOUNT : LGD_AMOUNT,
			LGD_BUYER : LGD_BUYER,
			LGD_PRODUCTINFO : LGD_PRODUCTINFO,
			LGD_BUYEREMAIL : LGD_BUYEREMAIL,
			LGD_CUSTOM_USABLEPAY : LGD_CUSTOM_USABLEPAY,
			LGD_WINDOW_TYPE : LGD_WINDOW_TYPE,
			LGD_CUSTOM_SWITCHINGTYPE : LGD_CUSTOM_SWITCHINGTYPE,
            IS_MOBILE : IS_MOBILE
		},
		url : ajax_url,
		success : function(args) {
            function setCredit(param){
                $("#areag").html("");
                $("#areag").html(param);
            }
            if(IS_MOBILE == true){
                // setCredit(args)
                set_session(setCredit,args);
            }else{
                setCredit(args);
            }
		}, error: function(jqXHR, textStatus, errorThrown) {
			console.log(jqXHR.responseText);
		}
	});

}

function doPay(platform, paytype) {

	if(paytype == "paypal"){
		alert("paypal");
	} else {
		// OID, TIMESTAMP 생성
		var oid_ = $("input[name=order_id]").val();
		var platform_ = platform;
		var name_ = $("input[name=name]").val();
		var option_ = $("input[name=option_]").val();
		var email_ = $("input[name=email1]").val() + "@" + $("input[name=email2]").val();
		var time_ = makeoid();
		var lgd_window_type_ = "iframe";
		var lgd_custom_switchingtype_ = "IFRAME";
		var point_ = $("input[name=use_point]").val();
		var zip_ = $("input[name=zip]").val();
		var paytype = paytype;

		var data_ = JSON.stringify({CST_PLATFORM:platform_,
								   LGD_BUYER:name_,
								   OPTIONS:option_,
								   LGD_BUYEREMAIL:email_,
								   LGD_OID:oid_,
								   LGD_TIMESTAMP: time_,
								   LGD_WINDOW_TYPE : lgd_window_type_,
								   LGD_CUSTOM_SWITCHINGTYPE : lgd_custom_switchingtype_,
								   LGD_CUSTOM_USABLEPAY : paytype,
								   zip : zip_,
								   point : point_,
								  });

		$.ajax({
			type : "POST",
			data : {
				data_info : data_
			},
			url : "/ko_mall/product/user/ajax_payment.php",
			dataType : "json",
			success : function(args) {
				//console.log(args);
				pay_result(args);
			}, error: function(jqXHR, textStatus, errorThrown) { console.log(jqXHR.responseText); }
		});
	}


	// 결제창 호출
	//document.getElementById("LGD_PAYINFO").submit();
}
