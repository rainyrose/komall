<?
require_once dirname(__FILE__).'/TPAY.LIB.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" href="css/sample.css" type="text/css" media="screen" />
<title>tPay 인터넷결제</title>
<!-- ajax방식(가맹점승인방식은 별도의 라이브러리 지원이 없으므로 아래코드 혹은 가맹점 자체 개발을 하여 호출해주셔야 합니다.)
<script type="text/javascript" src="./js/jquery-1.7.2.js"></script>
<script type="text/javascript">
	function resultConfirm(){
		$.ajax({
			type:"POST"
			,dataType:"json"
			,data:{"tid":"tid", "result":"000", "paymentType" : "2"}
			,url:"https://webtx.tpay.co.kr/resultConfirm"
		});
	}
</script>
 -->
</head>
<body>
<?
//상점(회원가) 페이지가 EUC-KR일 경우 한글깨짐방지
encoding("UTF-8", "EUC-KR", &$_POST); 

print_r($_POST);

//webTx에서 받은 결과값들
$payMethod = $_POST['payMethod'];
$mid = $_POST['mid'];
$tid = $_POST['tid'];
$mallUserId = $_POST['mallUserId'];
$amt = $_POST['amt'];
$buyerName = $_POST['buyerName'];
$buyerTel = $_POST['buyerTel'];
$buyerEmail = $_POST['buyerEmail'];
$mallReserved = $_POST['mallReserved'];
$goodsName = $_POST['goodsName'];
$moid = $_POST['moid'];
$authDate = $_POST['authDate'];
$authCode = $_POST['authCode'];
$fnCd = $_POST['fnCd'];
$fnName = $_POST['fnName'];
$resultCd = $_POST['resultCd'];
$resultMsg = $_POST['resultMsg'];
$errorCd = $_POST['errorCd'];
$errorMsg = $_POST['errorMsg'];
$vbankNum = $_POST['vbankNum'];
$vbankExpDate = $_POST['vbankExpDate'];
$ediDate = $_POST['ediDate'];

//상점(회원사) DB에 저장되어있던 값
$amtDb = "1004";//금액
$moidDb = "toid1234567890";//moid
$merchantKey = "VXFVMIZGqUJx29I/k52vMM8XG4hizkNfiapAkHHFxq0RwFzPit55D3J3sAeFSrLuOnLNVCIsXXkcBfYK1wv8kQ==";	//상점키

$encryptor = new Encryptor($merchantKey, $ediDate);
$decAmt = $encryptor->decData($amt);
$decMoid = $encryptor->decData($moid);

if( $decAmt!=$amtDb || $decMoid!=$moidDb ){
	echo "위변조 데이터를 오류입니다.";
}else{
	//결제결과 수신 여부 알림
	ResultConfirm::send($tid, "000");

	//상점DB처리
}
?>
</body>
</html>