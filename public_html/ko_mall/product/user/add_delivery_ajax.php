<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";
$query = "SELECT * FROM koweb_add_delivery_price WHERE start_zip <= '$zipcode' AND end_zip >= '$zipcode' ORDER BY price DESC LIMIT 1";
$tquery = "SELECT * FROM koweb_add_delivery_price WHERE start_zip <= '$zipcode' AND end_zip >= '$zipcode'";
$result = mysqli_query($connect,$query);
$row = mysqli_fetch_array($result);
$check = mysqli_num_rows(mysqli_query($connect, $tquery));

if($check != 0){
	echo $row[price];
} else {
	echo "0";
}
