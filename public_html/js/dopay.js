
var pay_flag = true;
function dopay(form_id){
    set_data(function(){
        $.ajax({
            type : "POST",
            url : "/ko_mall/INIPAY/open_ini_ajax.php",
            success : function(args) {
                $("#"+form_id).append(args);
                var oid = $("#oid").val();
                $("#orderid").val(oid);
                INIStdPay.pay(form_id);
            }
        });
    },form_id)
}

function set_data(callback,form_id){
    var param = $("#"+form_id).serialize();
    $.ajax({
        type : "POST",
        url : "/ko_mall/INIPAY/set_data_ajax.php",
        data : param,
        success : function(args) {
            callback();
        }
    });
}
