<?php
require_once("connMysql.php");
//啟用session
session_start();
// 如果不存在session，則導回留言版主畫面
if (!isset($_SESSION['loginMember']) || ($_SESSION['loginMember'] == "")) {
    header("Location:index.php");
}
//執行登出動作，刪除session
if (isset($_GET['logout']) && ($_GET['logout'] == "true")) {
    unset($_SESSION['loginMember']);
    header("Location:index.php");
}

// 根據boardid去找尋該筆資料
$query_RecBoard = "SELECT boardid, boardname, boardsex, boardsubject,boardcontent FROM board WHERE boardid=?";
$stmt = $db_link->prepare($query_RecBoard);
$stmt->bind_param("i", $_GET["id"]);
$stmt->execute();
$stmt->bind_result($boardid, $boardname, $boardsex, $boardsubject, $boardcontent);
$stmt->fetch();

//預設每頁筆數
$pageRow_records = 5;
//預設頁數
$num_pages = 1;
//若已經有翻頁，將頁數更新
if (isset($_GET['page'])) {
    $num_pages = $_GET['page'];
}
//本頁開始記錄筆數 = (頁數-1)*每頁記錄筆數
$startRow_records = ($num_pages - 1) * $pageRow_records;
//未加限制顯示筆數的SQL敘述句
$query_RecBoard = "SELECT * FROM board ORDER BY boardtime DESC";
//加上限制顯示筆數的SQL敘述句，由本頁開始記錄筆數開始，每頁顯示預設筆數
$query_limit_RecBoard = $query_RecBoard . " LIMIT {$startRow_records}, {$pageRow_records}";
//以加上限制顯示筆數的SQL敘述句查詢資料到 $RecBoard 中
$RecBoard = $db_link->query($query_limit_RecBoard);
//以未加上限制顯示筆數的SQL敘述句查詢資料到 $all_RecBoard 中
$all_RecBoard = $db_link->query($query_RecBoard);
//計算總筆數
$total_records = $all_RecBoard->num_rows;
//計算總頁數=(總筆數/每頁筆數)後無條件進位。
$total_pages = ceil($total_records / $pageRow_records);
?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>留言版後台管理區</title>
    <!-- CSS only -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
</head>

<body>
    <!-- 頁首 -->
    <header>
        <div align="center">
            <img class="bear" src="images/小熊維尼.gif" alt="" width="49" height="69">
            <strong class="header">留言版後台</strong>
        </div>

        <table border="0" align="center" cellpadding="4" cellspacing="0" class="table table-success table-striped container" style="max-width:1200px;">
            <div class="row">
                <tr>
                    <td class="col">
                        <a href="?logout=true" class="btn btn-success" id="admin" style="height:45px;line-height:30px;">管理員登出</p>
                    </td>

                    <td class="col" style="font-size:20px;position:relative;">
                        <p align="left" style="">目前資料筆數：<?php echo $total_records; ?></p>
                    </td>

                    <td align="right" class="col">
                        <p>
                            <?php if ($num_pages == 1) { // 第一頁則顯示 
                            ?>
                                <a class="text-light btn btn-info" href="#" style="visibility:hidden">擴充用</a>
                                <a class="text-light btn btn-info" href="#" style="visibility:hidden">擴充用</a>
                                <a class="text-light btn btn-info" href="?page=<?php echo $num_pages + 1; ?>">下一頁</a>
                                <a class="text-light btn btn-info" href="?page=<?php echo $total_pages; ?>">最末頁</a>
                            <?php } else if ($num_pages > 1) { // 若不是第一頁則顯示
                            ?>
                                <a class="text-light btn btn-info" href="?page=1">第一頁</a>
                                <a class="text-light btn btn-info " href="?page=<?php echo $num_pages - 1; ?>">上一頁</a>
                            <?php } ?>
                            <?php if ($num_pages < $total_pages && $num_pages != 1) { // 若不是最後一頁則顯示 
                            ?>
                                <a class="text-light btn btn-info" href="?page=<?php echo $num_pages + 1; ?>">下一頁</a>
                                <a class="text-light btn btn-info" href="?page=<?php echo $total_pages; ?>">最末頁</a>
                            <?php } ?>
                        </p>
                    </td>
                </tr>
            </div>
        </table>
    </header>

    <!-- 頁中 -->
    <content>
        <?php while ($row_RecBoard = $RecBoard->fetch_assoc()) { ?>
            <table border="0" align="center" class="table table-success table-striped" style="max-width:1200px;" cellpadding="4" cellspacing="0">
                <tr valign="top">
                    <td width="60" align="center">
                        <?php if ($row_RecBoard["boardsex"] == "男") {; ?>
                            <img src="images/male.gif" alt="男" width="49" height="49">
                        <?php } else { ?>
                            <img src="images/female.gif" alt="女" width="49" height="49">
                        <?php } ?>
                        <br>
                        <span><?php echo $row_RecBoard["boardname"]; ?></span>
                    </td>
                    <td>
                        <span style="font-size:22px;">
                            <font style="font-size:24px;color:blue;">主題: </font><?php echo $row_RecBoard["boardsubject"]; ?>
                            <?php if ($row_RecBoard["checked"] == "1") {; ?>
                                <button align="right" class="btn btn-light checked" data-id="<?php echo $row_RecBoard["boardid"] ?>">
                                    <img src="images/love.gif" alt="已讀愛心" width="35" height="35">
                                </button>
                            <?php } else {; ?>
                                <button align="right" class="btn btn-light check" data-id="<?php echo $row_RecBoard["boardid"] ?>">
                                    <img src="images/unlove.gif" alt="未讀愛心" width="35" height="35">
                                </button>
                            <?php } ?>
                        </span>
                        <hr>
                        <p><?php echo nl2br($row_RecBoard["boardcontent"]); ?></p>
                        <p align="right">
                            <?php echo $row_RecBoard["boardtime"]; ?>
                        </p>
                        <a class="btn btn-dark" href="adminfix.php?id=<?php echo $row_RecBoard["boardid"]; ?>">修改</a>
                        <a class="btn btn-danger" href="admindel.php?id=<?php echo $row_RecBoard["boardid"]; ?>">刪除</a>
                    </td>
                </tr>
            </table>
        <?php } ?>
    </content>


    <script>
        $(document).ready(function() {

            $(".checked").click(function(){
                alert("已經已讀囉!");
            })


            $('.check').click(function() {
                var ele = $(this);
                //抓取boardid-------
                var boardid = ele.attr("data-id");
                // alert(boardid);
                // alert(ele.attr("data-id"));
                if (confirm("是否標註為已讀?")) {
                    jQuery.ajax({
                        url: 'checked.php?id=' + boardid,
                        method: 'post',
                        data: {
                            boardid
                        },
                        success: function() {
                            alert('已經標註已讀!');
                            // 重整畫面
                            // window.history.go(0);
                            location.href = 'checked.php';
                        },
                        error: function() {
                            alert('fail');
                        }
                    })
                }
            })
        })
    </script>

</body>

</html>