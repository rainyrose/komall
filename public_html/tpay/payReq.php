<!DOCTYPE html>
<?

require_once dirname(__FILE__).'/TPAY.LIB.php';

$mid = "tpaytest0m";	//상점id
$merchantKey = "VXFVMIZGqUJx29I/k52vMM8XG4hizkNfiapAkHHFxq0RwFzPit55D3J3sAeFSrLuOnLNVCIsXXkcBfYK1wv8kQ==";	//상점키
$amt = $_POST['amt']==""?"1004":$_POST['amt'];	 //결제금액
$moid = "toid1234567890";

//$ediDate, $mid, $merchantKey, $amt    
$encryptor = new Encryptor($merchantKey);

$encryptData = $encryptor->encData($amt.$mid.$moid);
$ediDate = $encryptor->getEdiDate();	
$vbankExpDate = $encryptor->getVBankExpDate();	

$payActionUrl = "https://webtx.tpay.co.kr";
$payLocalUrl = "http://shop.tpay.co.kr";   //각 상점 도메인을 설정 하세요.  ex)http://shop.tpay.co.kr
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />

<!-- webtx 필수lib  -->
<link rel="stylesheet" href="//webtx.tpay.co.kr/css/nyroModal.tpay.custom.css" type="text/css" media="screen" />
<script type="text/javascript" src="//webtx.tpay.co.kr/js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="//webtx.tpay.co.kr/js/jquery.nyroModal.tpay.custom.js"></script>
<script type="text/javascript" src="//webtx.tpay.co.kr/js/client.tpay.webtx.js"></script>
<script>
var resultUrl = "./payReqResult.php";	//결제결과 받는 URL
</script>
<!-- -->

<link rel="stylesheet" href="css/sample.css" type="text/css" media="screen" />
<title>tPay 인터넷결제</title>
</head>
<body>
<br>
<img src="https://webtx.tpay.co.kr/images/title_pay.gif" />
<form id="transMgr" name="transMgr" method="post" action="<?=$payActionUrl ?>/webTxInit" class="nyroModal" target="_blank">
		<table cellspacing="1" border="0" cellpadding="0">
		<thead>
			<tr><td colspan="2"><img src="https://webtx.tpay.co.kr/images/bul_arrow02.gif"/>&nbsp;<strong>결제 상점 데모 프로그램</strong></td></tr>
		</thead>
		<tbody>
			<tr>
				<td>결제수단</td>
				<td>
					<select name="payMethod" id="payMethod" style="height: 25px;">
						<option value="">[모두]</option>
						<option value="CARD">[신용카드]</option>
						<option value="BANK">[계좌이체]</option>
						<option value="VBANK">[가상계좌]</option>
						<option value="CELLPHONE">[휴대폰결제]</option>
						<!-- 
						<option value="CDBILLRG">[신용카드자동결제]</option>
						<option value="CPBILL">[핸드폰자동결제]</option>
						 -->
					</select>
				</td>
			</tr>
			
			<tr>
				<td>결제타입</td>
				<td>
					일반<input type="radio" id="transTypeN" name="transType" value="0" checked="checked">&nbsp;&nbsp;
					에스크로<input type="radio" id="transTypeE" name="transType" value="1">
				</td>
			</tr>
			<tr>
				<td>상품명<font color="red">(*)</font></td>
				<td><input type="text" name="goodsName" value="t_상품명"></td>
			</tr>
			<tr>
				<td>상품가격<font color="red">(*)</font></td>
				<td><input type="text" name="amt" value="<?=$amt?>" > 원<input type="button" value="금액 변경" onclick="changeAmt();"  class="button blue small"/>
						<br/> <font size="1pt" style="font-weight: bold;"> * 상품가격  변경시 금액변경 버튼을 눌러주시기 바랍니다.</font>
				</td>
			</tr>
			
			<tr>
				<td>상품주문번호</td>
				<td><input type="text" name="moid" value="<?=$moid?>">	</td>
			</tr>
			
			<tr>
				<td>회원사아이디<font color="red">(*)</font></td>
				<td><input type="text" name="mid" value="<?=$mid ?>" readonly="readonly"></td>
			</tr>
			
			<tr>
				<td>구매자명</td>
				<td><input type="text" name="buyerName" value="t_구매자명"></td>
			</tr>
			
			<tr>
				<td>구매자연락처((-)없이 입력)</td>
				<td><input type="text" name="buyerTel" value="0212345678"></td>
			</tr>
			
			<tr>
				<td>구매자메일주소<font color="red">(*)</font></td>
				<td><input type="text" name="buyerEmail" value="aaa@bbb.com"></td>
			</tr>
			<tr>
				<td>결제방식</td>
				<td>
					<select name="paymentType" id="paymentType" style="height: 25px;">
						<option value="1" selected="selected">일반승인(전승인)</option>
						<option value="2">가맹점승인(후승인)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>tx 사용</td>
				<td>
					<select name="socketYn" id="socketYn" style="height: 25px;">
						<option value="N">미사용</option>
						<option value="Y">사용</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>소캣방식 리턴URL</td>
				<td><input type="text" name="socketReturnURL" value="http://shop.tpay.co.kr/sampleJSP/txTrans.jsp"></td>
			</tr>
			<tr>
				<td>retryURL(재통보)</td>
				<td><input type="text" name="retryUrl" value=""></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;"><input type="button" id="submitBtn" value="결제 전송(btn)" class="button blue medium"></td>
			</tr>
		
		</tbody>
	</table>
	<table width="500px" border="0px" style="line-height:25px;">
		<tr>
			<td style="vertical-align:top;">*</td>
			<td style="vertical-align:top;"><font color="red">(*)</font>표 항목은 반드시 기입해주시기 바랍니다.</td>
		</tr>
		<tr>
			<td style="vertical-align:top;">*</td>
			<td style="vertical-align:top;">테스트 아이디로 결제된 건에대해서는 당일 오후 11:30분에 일괄 취소됩니다.<br>실제 회원사아이디 적용 시 테스트아이디가 적용되지 않도록 각별한 주의를 부탁드립니다. </td>
		</tr>
	</table>
	
	<input type="hidden" name="payType" value="1">	
	<input type="hidden" name="ediDate"	value="<?=$ediDate?>">
	<input type="hidden" name="encryptData" value="<?=$encryptData?>">
	<input type="hidden" name="userIp"	value="<?=$_SERVER['REMOTE_ADDR']?>">
	<input type="hidden" name="browserType" id="browserType">
	<input type="hidden" name="mallUserId" value="tpay_id">
	<input type="hidden" name="parentEmail">
	<input type="hidden" name="buyerAddr" value="서울특별시 구로구 디지털로 30길28, 마리오타워 9F">
	<input type="hidden" name="buyerPostNo" value="463400">
	<input type="hidden" name="mallIp" value="<?=$_SERVER['SERVER_ADDR']?>">
	<input type="hidden" name="mallReserved" value="MallReserved">
	<input type="hidden" name="vbankExpDate" value="<?=$vbankExpDate?>">
	<input type="hidden" name="rcvrMsg" value="rcvrMsg">
	<input type="hidden" name="prdtExpDate" value="20151231">
	<input type="hidden" name="resultYn" value="Y">
	<input type="hidden" name="quotaFixed" value="">
	<input type="hidden" name="domain" value="<?=$payLocalUrl?>">
	
	<!--
	<input type="hidden" name="socketYn" value="Y">
	<input type="hidden" name="socketReturnURL" value="<?=$payLocalUrl?>/sample/txTrans.php">
	<input type="hidden" name="socketYn" value="N">
	<input type="hidden" name="socketReturnURL" value="">
	-->

</form>

</body>

</html>