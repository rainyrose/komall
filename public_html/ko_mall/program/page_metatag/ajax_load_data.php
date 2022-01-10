<?
/*----------------------------------------------------------------------------*/
include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
/*----------------------------------------------------------------------------*/
$query = "SELECT * FROM koweb_page_metatag WHERE url = '$_POST[variable]' ORDER BY no DESC";
$result = mysqli_query($connect, $query);
$row = mysqli_fetch_array($result);

if(!$row[0]) $json = array("url"=>$_POST['variable']);
else $json = array("url"=>$row[url], "description"=>$row[description],"og_description"=>$row[og_description],"og_site_name"=>$row[og_site_name],"og_title"=>$row[og_title]); 
echo json_encode($json);



?>