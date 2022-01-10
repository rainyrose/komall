<?
define('PAYPAL_CLIENT','AXroK-b_pcA5R5WE0itHfqGbWGMXWrv1utO4Q61COL5Vt9OmQ9hoSEmQr5HkX8HOe-DvHz0WBrIAb4_7:ECWNuxfX667NIgXJsQdnx-d8hUk68-cCxrteJ5gEWuu7ESd1FsBoNJiZIqFGRAEjRMLvUCEiv0X88mKY');


$headers = array(
	"Accept: application/json",
	"Accept-Language: en_US"
);

$token_url = "https://api.sandbox.paypal.com/v1/oauth2/token?grant_type=client_credentials"; 
$is_post = false;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, $is_post);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true); 
$response = curl_exec ($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close ($ch);
$token = json_decode($response, true);

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.sandbox.paypal.com/v2/checkout/orders/94000997AY909210H",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    'authorization: Bearer '.$token[access_token],
    'content-type: application/json'
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

?>