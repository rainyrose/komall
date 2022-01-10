<?

/**
 * @author 서세윤
 * @param string - dt태그에 들어갈 string
 * @param string - dd태그에 들어갈 내용 및 if비교대상
 * @return string 공백 혹은 dt,dd태그 HTML
 */
function dt_dd_html($dt,$dd){
    if($dd){
        return "<dt>{$dt}</dt><dd>{$dd}</dd>";
    }else{
        return "";
    }
}

/**
 * @author 서세윤
 * @param string - "|"텍스트가 들어간 string
 * @return array "|"로 explode 시킨후 reverse시킨 배열
 */
function make_reverse_array($str){
    return array_reverse(explode("|", $str));
}

/**
 * @author 서세윤
 * @param array - int로 이루어진 array 거나 int
 * @return array 0이라면 무료 ,아니라면 원을 붙인 금액 배열이나 string return
 */
function change_free_and_add_won($arg1){
    if(is_array($arg1)){
        return array_map(
            function($value){
                if($value == 0) return "무료";
                else return number_format($value)."원";
            },$arg1
        );
    }else if(is_numeric($arg1)){
        if($arg1 == 0) return "무료";
        else return number_format($arg1)."원";
    }
}

function change_free_and_add_won_eng($arg1){
    if(is_array($arg1)){
        return array_map(
            function($value){
                if($value == 0) return "free";
                else return number_format($value)."won";
            },$arg1
        );
    }else if(is_numeric($arg1)){
        if($arg1 == 0) return "free";
        else return number_format($arg1)."won";
    }
}
/**
 * @author 서세윤
 * @param string - $id
 * @return object - data
 */
function get_product($id){
    global $connect;
    $query = "SELECT * FROM koweb_product WHERE id='{$id}'";
    $result = mysqli_query($connect,$query);
    $row = mysqli_fetch_array($result);
    return $row;
}

// /**
//  * @author 서세윤
//  * @param string - $id
//  * @return object - data
//  */
function get_option($id){
    global $connect;
    $query = "SELECT * FROM koweb_option_detail WHERE id='{$id}'";
    $result = mysqli_query($connect,$query);
    $row = mysqli_fetch_array($result);
    return $row;
}
?>
