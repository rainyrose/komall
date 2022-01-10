<?
$reg_date = date("Y-m-d H:i:s");
$dir = $_SERVER['DOCUMENT_ROOT'] . "/upload/product/";
if($site_language == "eng"){
    if(!$no) error("No product selected.");
}else{
    if(!$no) error("선택된 상품이 없습니다.");
}
if(!$member_id){
    $guest_no = $no;
    list($p_id,$o_id) = explode("|",$guest_no);
    $cart['product_id'] = $p_id;
    $cart['add_option'] = $_SESSION['s_cart'][$p_id][$o_id]['add_option'];
}else{
    $no_count = count(explode("|",$no));
    if($no == "") $no_count = 0;
    $no = str_replace("|","','",$no);
    $cart_query = "SELECT * FROM koweb_cart WHERE no in ('$no') AND member_id='{$member_id}' ";
    $cart_result = mysqli_query($connect,$cart_query);
    $cart_num = mysqli_num_rows($cart_result);
    $cart = mysqli_fetch_array($cart_result);
    if($site_language == "eng"){
        if($cart_num != $no_count) error('The wrong approach.');
    }else{
        if($cart_num != $no_count) error('잘못된 접근입니다.');
    }
}
if($mode =="cart_del_proc"){
    if(!$member_id){
        unset($_SESSION['s_cart'][$p_id][$o_id]);
        if(count($_SESSION['s_cart'][$p_id]) == 0){
            unset($_SESSION['s_cart'][$p_id]);
        }
    }else{
        $del_query = "DELETE FROM koweb_cart WHERE no in ('$no') AND member_id='{$member_id}' ";
        mysqli_query($connect,$del_query);
    }

    if($site_language == "eng"){
        alert("Was deleted");
    }else{
        alert("삭제 되었습니다");
    }
}else if($mode == "cart_count_proc"){
    $product = get_product($cart['product_id']);

    if(!$add_option){

        $check_total_cnt = $cart_count;
        if(!$member_id){

            foreach ($_SESSION['s_cart'][$p_id] as $key => $check_row) {
                if($o_id == $key) continue;
                $check_total_cnt += $check_row['product_cnt'];
            }
        }else{
            $check_query = "SELECT * FROM koweb_cart WHERE no not in ('$no') AND product_id='{$cart['product_id']}' AND member_id='{$member_id}' ";
            $check_result = mysqli_query($connect,$check_query);
            while($check_row = mysqli_fetch_array($check_result)){
                $check_total_cnt += $check_row['product_cnt'];
            }
        }

        if($product['max_count'] && $product['max_count'] < $check_total_cnt){
            if($site_language == "eng"){
                alert("You may not order more than ".($product['max_count']+1)." items.");
            }else{
                alert("이 상품은 최대 ".($product['max_count']+1)."개 이상 주문할수 없습니다.");
            }

            url("?mode=cart");
            exit;
        }
        if(!$member_id){
            $_SESSION['s_cart'][$p_id][$o_id]['product_cnt'] = $cart_count;
        }else{
            $update_query = "UPDATE koweb_cart SET product_cnt='{$cart_count}' WHERE no in ('$no') AND member_id='{$member_id}' ";
            mysqli_query($connect,$update_query);
        }

    }else{

        $cart_add_option_list = explode("^",$cart['add_option']);
        $add_option_col = array();
        foreach ($cart_add_option_list as $value) {
            list($cart_add_option_id,$cart_add_option_cnt) = explode("|",$value);
            $add_option_col[$cart_add_option_id] = $cart_add_option_cnt;
            if($add_option == $cart_add_option_id){
                $add_option_col[$add_option] = $cart_count;
                if($del_flag == "Y"){
                    unset($add_option_col[$add_option]);
                }
            }
        }

        $add_option_col_list = array();
        foreach ($add_option_col as $key => $value) {
            $add_option_col_list[] = $key."|".$value;
        }
        $add_option_str = join("^",$add_option_col_list);
        if(!$member_id){
            $_SESSION['s_cart'][$p_id][$o_id]['add_option'] = $add_option_str;
        }else{
            $update_query = "UPDATE koweb_cart SET add_option='{$add_option_str}' WHERE no in ('$no') AND member_id='{$member_id}' ";
            mysqli_query($connect,$update_query);
        }
    }

    if($site_language == "eng"){
        alert("Has changed");
    }else{
        alert("변경 되었습니다");
    }
}else if($mode =="cart_option_proc"){
    $product = get_product($cart['product_id']);

    $detail_query = "SELECT * FROM koweb_option_detail WHERE type_id='{$option}' AND ref_product='{$product['no']}'";
    $detail_result = mysqli_query($connect,$detail_query);
    $detail = mysqli_fetch_array($detail_result);

    $option_id = $detail['id'];
    if(!$member_id){
        if($option_id != $o_id){
            if($_SESSION['s_cart'][$p_id][$option_id]){
                $_SESSION['s_cart'][$p_id][$option_id]['product_cnt'] += $_SESSION['s_cart'][$p_id][$o_id]['product_cnt'];
            }else{
                $_SESSION['s_cart'][$p_id][$option_id] = $_SESSION['s_cart'][$p_id][$o_id];
            }
            unset($_SESSION['s_cart'][$p_id][$o_id]);
        }
    }else{
        $check_query = "SELECT * FROM koweb_cart WHERE member_id='{$member_id}' AND product_id='{$cart['product_id']}' AND option_id='{$option_id}'";
        $check_result = mysqli_query($connect,$check_query);
        $check_num_row = mysqli_num_rows($check_result);

        // if($check_num_row > 0){
        //     $check_row = mysqli_fetch_array($check_result);
        //     $plus_cnt = $check_row['product_cnt'];
        //     $del_query = "DELETE FROM koweb_cart WHERE member_id='{$member_id}' AND product_id='{$cart['product_id']}' AND option_id='{$option_id}'";
        //     $del_result = mysqli_query($connect,$del_query);
        //     $update_query = "UPDATE koweb_cart SET product_cnt=product_cnt+{$plus_cnt} , option_id='{$option_id}' WHERE no in ('$no') AND member_id='{$member_id}'";
        // }else{
            $update_query = "UPDATE koweb_cart SET option_id='{$option_id}' WHERE no in ('$no') AND member_id='{$member_id}' ";
        // }
        mysqli_query($connect,$update_query);
    }

    if($site_language == "eng"){
        alert("Has changed");
    }else{
        alert("변경 되었습니다");
    }
}

url("?mode=cart");
