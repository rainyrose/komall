<?php
/*
    * 2019-03-13 서세윤
    * 문장이 특정 $length 이상이라면 짜르고 어미에 $text를 붙인다
    * utf8전
 */
function str_cut_ending_utf8($str,$length,$text){
    if(mb_strlen($str, "UTF-8") > $length){
        $result = mb_substr($str,0,$length,'UTF-8').$text;
    }else{
        $result = $str;
    }

    return $result;
}
?>
