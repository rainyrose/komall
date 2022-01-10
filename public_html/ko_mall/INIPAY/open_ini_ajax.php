<?
include_once $_SERVER['DOCUMENT_ROOT']."/head.php";


include_once $_SERVER['DOCUMENT_ROOT']."/ko_mall/INIPAY/libs/INIStdPayUtil.php";

if($_SERVER['HTTPS']){
    $HOST_ = "https://";
 } else {
    $HOST_ = "http://";
 }
$my_url = $HOST_.$_SERVER['HTTP_HOST'];

$SignatureUtil = new INIStdPayUtil();
// $mid = "daezertour";  // 가맹점 ID(가맹점 수정후 고정)
// $signKey = "RVlRKzFBQXJoMmZwMEJJNmxHZzhnQT09"; // 가맹점에 제공된 웹 표준 사인키(가맹점 수정후 고정)
$mid = "INIpayTest";  // 가맹점 ID(가맹점 수정후 고정)
//인증
$total_price = 200;
$signKey = "SU5JTElURV9UUklQTEVERVNfS0VZU1RS"; // 가맹점에 제공된 웹 표준 사인키(가맹점 수정후 고정)
$timestamp = $SignatureUtil->getTimestamp();   // util에 의해서 자동생성
$mKey = $SignatureUtil->makeHash($signKey, "sha256");
$oid = date("YmdHis").rand(1000,1999);

$params = array(
    "oid" => $oid,
    "price" => $total_price,
    "timestamp" => $timestamp
);
$sign = $SignatureUtil->makeSignature($params, "sha256");

$_SESSION[PARAM_DATA][mKey] = $mKey;
$_SESSION[PARAM_DATA][sign] = $sign;
?>


<input type="hidden"  name="goodname" value="1" >
<input type="hidden"  name="buyername" value="gg" >
<input type="hidden"  name="buyertel" value="010-5140-2778" >
<input type="hidden"  name="buyeremail" value="" >
<input type="hidden"  name="price" value="<?=$total_price?>" >
<input type="hidden"  name="mid" value="<?=$mid?>" >
<input type="hidden"  name="gopaymethod" value="Card" >
<input type="hidden"  name="mKey" value="<?=$mKey?>" >
<input type="hidden"  name="signature" value="<?=$sign?>" >
<input type="hidden"  id='oid' name="oid" value="<?=$oid?>" >
<input type="hidden"  name="timestamp" value="<?=$timestamp?>" >
<input type="hidden"  name="version" value="1.0" >
<input type="hidden"  name="currency" value="WON" >
<input type="hidden"  name="acceptmethod" value="below1000" >
<input type="hidden"  name="returnUrl" value="<?=$my_url?>/test.php" >
<input type="hidden"  name="closeUrl" value="<?=$my_url?>/ko_mall/INIPAY/INIStdPay/close.php" >
