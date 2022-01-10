<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";

$review_query = "SELECT * FROM koweb_board_config WHERE id='{$board_id}' order by no DESC";
$review_result = mysqli_query($connect,$review_query);
$review_config = mysqli_fetch_array($review_result);

$add_folder = "";
if($site_language !="default"){
	$add_folder = "/".$site_language;
}

//페이징 변수
if (!$start) $start = 0;
$scale = $review_config[list_limit]; // 리스트 수
$page_scale	= 5; // 페이징 수


//리스트
$total_query = "SELECT * FROM $board_id WHERE category='{$no}' ORDER BY notice DESC, reg_date DESC, no DESC";
$total_result = mysqli_query($connect, $total_query);
$total = mysqli_num_rows($total_result);

$query = "SELECT * FROM $board_id WHERE category='{$no}' ORDER BY notice DESC, ref_group DESC, depth ASC, no DESC LIMIT $start, $scale";
$result = mysqli_query($connect, $query);

$score_target_arr = array("very unsatisfied"=>0,"dissatisfaction"=>1,"usually"=>2,"Satisfaction"=>3,"very good"=>4);
?>

<p class="title">REVIEW</p>
<span class="sub">Reviews from members who have bought this product.</span>

<!-- 아코디언 스타일 목록 -->
<!-- 글 10개 노출 -->
<ul class="list_shop_accordion">
    <!-- 반복 -->
    <? while($row = mysqli_fetch_array($result)){
    $score = $score_target_arr[$row['review_score']];
    ?>
    <li>
        <!-- 상단 -->
        <div class="thead">
            <!-- 평가point수 -->
            <span data-shop-accordion="eval">
                <!-- 데이터가 Selection된 경우 class on처리 -->
                <i class="<?=$score >= 0 ? "on" : ""?>">very unsatisfied</i>
                <i class="<?=$score >= 1 ? "on" : ""?>">dissatisfaction</i>
                <i class="<?=$score >= 2 ? "on" : ""?>">usually</i>
                <i class="<?=$score >= 3 ? "on" : ""?>">Satisfaction</i>
                <i class="<?=$score >= 4 ? "on" : ""?>">very good</i>
            </span>
            <!-- 제목 -->
            <a href="#" data-shop-accordion="subject"><?=$row['title']?></a>
            <!-- ID -->
            <span data-shop-accordion="name"><?=$row['name']?></span>
            <!-- 작성일 -->
            <span data-shop-accordion="date"><?=str_replace("-",".",reset(explode(" ",$row['reg_date'])))?></span>
        </div>
        <!-- //상단 -->

        <!-- 내용 -->
        <div class="tbody">
            <!-- 작성된 글 -->
            <div class="conts">
                <?=$row['comments']?>
            </div>
            <!-- //작성된 글 -->

            <!-- 첨부된 사진 -->
            <!-- 첨부된 사진이 없는경우 감싼 태그까지 노출되지 않도록 클릭시 사진 새창으로 won본띄어주세요 -->
            <div class="file">
                <? for($i = 1; $i<=$review_config['file_count']; $i++){ ?>
                    <? if($row['file_'.$i]){ ?>
                        <? $ext = end(explode(".", strtolower($row['file_'.$i]))); ?>
                        <? if($ext == "jpg" || $ext == "png" || $ext == "jpeg" || $ext == "bmp"){ ?>
                        <a href="/upload/<?=$review_config['id']?>/<?=$row['file_'.$i]?>" target="_blank"><i><img src="/upload/<?=$review_config['id']?>/<?=$row['file_'.$i]?>" alt=""/></i></a>
                        <? } ?>
                    <? } ?>
                <? } ?>
            </div>
            <!-- //첨부된 사진 -->
            <?
            $ref_board_no = $row['no'];
            $comm_query = " SELECT *  FROM board_comment WHERE board_id='$board_id' AND ref_board_no = '$ref_board_no' ORDER BY ref_group ASC, ref_depth ASC, no DESC";
            $comm_result = mysqli_query($connect, $comm_query);
            while($comm_row = mysqli_fetch_array($comm_result)) {
            ?>
            <!-- 답변 -->

            <div class="answer">
                <!-- 작성일/작성자 -->
                <div class="writer">
                    <?
                    $date = explode(" ",$comm_row['reg_date']);
                    $date_ = explode("-",reset($date));
                    ?>
                    <?=$date_[0]?>.<?=$date_[1]?>.<?=$date_[2]?> - <?=$comm_row['name']?>
                </div>
                <!-- 내용 -->
                <div class="conts"><?=$comm_row['comments']?>
                </div>
            </div>
            <? } ?>
            <!-- //답변 -->
        </div>
        <!-- //내용 -->
    </li>
    <? } ?>
    <!-- //반복 -->
</ul>

<!-- btn -->
<div class="btn_area tar">
    <!-- <a href="/contents/board/page.html?mid=<?=$mid?>&mode=write&id=<?=$no?>" class="button">리뷰작성</a> -->
    <a href="<?=$add_folder?>/board/board.html?board_id=<?=$board_id?>" class="button white">View all</a>
</div>

<!-- 페이징 -->
<div class="pagination">
    <?
    if ($total == 0) $total = 1;
    $page = floor($start / ($scale * $page_scale));
    ?>

    <a href="javascript:void" onclick='getAjaxList(0,"<?=$no?>","<?=$board_id?>","review","shopView03")' class="btn_first">First</a>

    <? if ($start + $scale >  $scale * $page_scale) {
        $pre_start = $start - $scale * $page_scale; ?>
    <a href="javascript:void" onclick='getAjaxList(<?=$pre_start?>,"<?=$no?>","<?=$board_id?>","review","shopView03")' class="btn_prev">Previous</a>
    <? } ?>

    <!-- Selection된페이지 -->
    <?
    for ($vj = 0; $vj < $page_scale ; $vj++) {
        $ln = ($page * $page_scale + $vj) * $scale;
        $vk = $page * $page_scale + $vj + 1 ;

        if ($ln < $total) {
            if ($ln != $start){ ?>
            <a href="javascript:void" onclick='getAjaxList(<?=$ln?>,"<?=$no?>","<?=$board_id?>","review","shopView03")'><?=$vk?></a>
            <? }else{ ?>
                <span><?=$vk?></span>
            <? }
        }
    }

    // 마지막
    $end_page = floor($total - $scale) + 1;
    if ($end_page <= 0)	$end_page = 0;

    if ($total > (($page + 1) * $scale * $page_scale)) {
        $n_start = ($page + 1) * $scale * $page_scale ; ?>
        <a href="javascript:void" onclick='getAjaxList(<?=$n_start?>,"<?=$no?>","<?=$board_id?>","review","shopView03")' class="btn_next">next</a>
    <? }

    $end_page = ceil($total / $scale);
    if ($total) $end_start = ($end_page -1) * $scale;
    else $end_start = $end_page;

    ?>
    <a href="javascript:void" onclick='getAjaxList(<?=$end_start?>,"<?=$no?>","<?=$board_id?>","review","shopView03")' class="btn_last">Last</a>
</div>
<!-- //페이징 -->
