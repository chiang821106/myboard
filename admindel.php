<?php
require_once("connMysql.php");
// 啟用session/檢查是否有無session/執行登出的動作
session_start();
if (!isset($_SESSION['loginMember']) || ($_SESSION['loginMember'] == "")) {
    header("Location:index.php");
}
if (isset($_GET['logout']) && ($_GET['logout'] == 'true')) {
    unset($_SESSION['loginMember']);
    header("Location:index.php");
}
// 執行刪除動作
if (isset($_POST['action']) && ($_POST['action'] == 'delete')) {
    $sql_query = "DELETE FROM board WHERE boardid=?";
    $stmt = $db_link->prepare($sql_query);
    $stmt->bind_param('i', $_POST['boardid']);
    $stmt->execute();
    $stmt->close();
    //返回後台區
    header("Location:admin.php");
}
// 根據boardid查詢該筆資料
$query_RecBoard = "SELECT boardid,boardname,boardsex,boardsubject,boardcontent FROM board WHERE boardid=?";
$stmt = $db_link->prepare($query_RecBoard);
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$stmt->bind_result($boardid, $boardname, $boardsex, $boardsubject, $boardcontent);
$stmt->fetch();

?>

<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>後台管理區</title>
    <link href="style.css" rel="stylesheet" type="text/css">
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

        <table border="0" align="center" cellpadding="4" cellspacing="0" class="table table-success table-striped" style="max-width:1200px;">
            <tr>
                <td>
                    <a href="?logout=true" class="btn btn-success" id="admin" style=" height:45px;line-height:30px;">管理員登出</p>
                </td>
            </tr>
        </table>
    </header>

    <!-- 頁中 -->
    <content>
        <form name="form1" method="post" action="">
            <table border="0" align="center" class="table table-success table-striped" style="max-width:1200px;" cellpadding="4" cellspacing="0">
                <tr>
                    <td colspan="2" align="center" style="font-size:24px;color:blue;">刪除訪客留言版資料</td>
                </tr>
                <tr valign="top">
                    <td>
                        <p>
                            <strong>姓名</strong>：<?php echo $boardname; ?>&nbsp;&nbsp;&nbsp;
                            <strong>性別</strong>：<?php echo $boardsex; ?>
                        </p>
                        <p>
                            <strong>標題</strong>：<?php echo $boardsubject; ?>
                        </p>
                        <p>
                            <strong>內容</strong>：<?php echo nl2br($boardcontent); ?>
                        </p>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="center">
                        <p>
                            <input name="boardid" type="hidden" id="boardid" value="<?php echo $boardid; ?>">
                            <input name="action" type="hidden" id="action" value="delete">
                            <input type="submit" name="button" id="button" value="確定刪除資料" class="btn btn-danger">
                            <input type="button" name="button3" id="button3" value="回上一頁" class="btn btn-primary" onClick="window.history.back();">
                        </p>
                    </td>
                </tr>
            </table>
        </form>
    </content>

</body>

</html>

<?php
$stmt->close();
$db_link->close();
?>