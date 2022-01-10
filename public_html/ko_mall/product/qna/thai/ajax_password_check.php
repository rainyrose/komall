<?
include $_SERVER['DOCUMENT_ROOT']."/head.php";
$qna_query = "SELECT * FROM koweb_board_config WHERE id='$board_id'";
$qna_result = mysqli_query($connect,$qna_query);
$qna_config = mysqli_fetch_array($qna_result);

$password = hash("sha256", $password);

$query = "SELECT * FROM $board_id WHERE no={$no}";
$result = mysqli_query($connect,$query);
$row = mysqli_fetch_array($result);

if($row['password'] == $password){ ?>
    <? $_SESSION['qna_secret'][] = $no; ?>
        <!-- 작성된 글 -->
        <div class="conts">
            <?=$row['comments']?>
        </div>
        <!-- //작성된 글 -->

        <!-- 첨부된 사진 -->
        <!-- 첨부된 사진이 없는경우 감싼 태그까지 노출되지 않도록 클릭시 사진 새창으로 원본띄어주세요 -->
        <div class="file">
            <? for($i = 1; $i<=$qna_config['file_count']; $i++){ ?>
                <? if($row['file_'.$i]){ ?>
                    <? $ext = end(explode(".", strtolower($row['file_'.$i]))); ?>
                    <? if($ext == "jpg" || $ext == "png" || $ext == "jpeg" || $ext == "bmp"){ ?>
                    <a href="/upload/<?=$board_id?>/<?=$row['file_'.$i]?>" target="_blank"><i><img src="/upload/<?=$board_id?>/<?=$row['file_'.$i]?>" alt=""/></i></a>
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
<? }else{ ?>
    false
<? } ?>
