<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";

if($flag == "main"){
    $query = "UPDATE koweb_address SET main='0' WHERE member='{$_SESSION['member_id']}' AND no!='{$no}'";
    mysqli_query($connect,$query);
    $query = "UPDATE koweb_address SET main='1' WHERE member='{$_SESSION['member_id']}' AND no='{$no}'";
    mysqli_query($connect,$query);
}else if($flag == "del"){
    $no_list = explode("|",$no);
    $no = join("','",$no_list);
    $query = "DELETE FROM koweb_address WHERE no in ('{$no}')";
    mysqli_query($connect,$query);
}
