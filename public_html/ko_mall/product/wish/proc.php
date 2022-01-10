<?
$reg_date = date("Y-m-d H:i:s");
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/product/";

$no_count = count(explode("|",$no));
if($no == "") $no_count = 0;
$no = str_replace("|","','",$no);
$wish_query = "SELECT * FROM koweb_wish WHERE no in ('$no') AND member_id='{$member_id}' ";
$wish_result = mysqli_query($connect,$wish_query);
$wish_num = mysqli_num_rows($wish_result);
$wish = mysqli_fetch_array($wish_result);
if($site_language == "eng"){
    if($wish_num != $no_count) error('The wrong approach.');
}else{
    if($wish_num != $no_count) error('잘못된 접근입니다.');
}


if($mode == "wish_del_proc"){
    $del_query = "DELETE FROM koweb_wish WHERE no in ('$no') AND member_id='{$member_id}' ";
    mysqli_query($connect,$del_query);

    if($site_language == "eng"){
        alert("Was deleted");
    }else{
        alert("삭제 되었습니다");
    }
}
url("?mode=wish");
