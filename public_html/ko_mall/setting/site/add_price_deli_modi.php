<?
	include_once $_SERVER['DOCUMENT_ROOT'] . "/head.php";
	$setting_table = "koweb_add_delivery_price";
    $no_col = explode("|",$modify_data);
    $no_str = join("','",$no_col);
    query("update $setting_table SET price='{$price}' WHERE no in ('{$no_str}')");
