<?php

require_once("connMysql.php");
session_start();
if (!isset($_SESSION['loginMember']) || ($_SESSION['loginMember'] == "")) {
    header("Location:index.php");
}
if (isset($_GET['logout']) && ($_GET['logout'] == 'true')) {
    unset($_SESSION['loginMember']);
    header("Location:index.php");
}
//執行更新動作
if (isset($_POST["action"]) && ($_POST["action"] == "update")) {
    $query_update = "UPDATE board SET boardname=?, boardsex=?, boardsubject=?, boardcontent=? WHERE boardid=?";
    $stmt = $db_link->prepare($query_update);
    $stmt->bind_param(
        "ssssi",
        $_POST["boardname"],
        $_POST["boardsex"],
        $_POST["boardsubject"],
        $_POST["boardcontent"],
        $_POST["boardid"]
    );
    $stmt->execute();
    $stmt->close();
    //重新導向回到主畫面
    header("Location: admin.php");
}
// 根據boardid查詢該筆的資料
$query_RecBoard = "SELECT boardid, boardname, boardsex, boardsubject,boardcontent FROM board WHERE boardid=?";
$stmt = $db_link->prepare($query_RecBoard);
$stmt->bind_param("i", $_GET["id"]);
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
                    <a href="?logout=true" class=" btn btn-success" id="admin" style=" height:45px;line-height:30px;">管理員登出</p>
                </td>
            </tr>
        </table>
    </header>


    <!-- 頁中 -->
    <content>
        <table border="0" align="center" class="table table-success table-striped" style="max-width:1200px;" cellpadding="4" cellspacing="0">
            <form name="form1" method="post" action="">
                <tr>
                    <td colspan="2" align="center" style="font-size:24px;color:blue;">更新訪客留言版資料</td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group form-inline">
                            <label for="boardname" class="col-3">姓名:</label>
                            <input style="background-color:#CCDDFF" type="text" class="form-control col-8" name="boardname" id="boardname" value="<?php echo $boardname; ?>">
                        </div>
                        <div class="form-group form-inline">
                            <label for="boardsex">性別:</label>
                            <input style="background-color:#CCDDFF" name="boardsex" type="radio" id="radio" value="男" <?php if ($boardsex == "男") {
                                                                                                                            echo "checked";
                                                                                                                        } ?>>男
                            <input style="background-color:#CCDDFF" name="boardsex" type="radio" id="radio2" value="女" <?php if ($boardsex == "女") {
                                                                                                                            echo "checked";
                                                                                                                        } ?>>女
                        </div>
                        <div class="form-group form-inline">
                            <label for="boardsubject">標題:</label>
                            <input style="background-color:#CCDDFF" name="boardsubject" type="text" class="form-control col-8" id="boardsubject" value="<?php echo $boardsubject; ?>"></p>
                        </div>
                        <div class="form-group form-inline">
                            <label for="boardcontent">內容:</label>
                            <textarea style="background-color:#CCDDFF" name="boardcontent" id="boardcontent" class="form-control col-8" cols="30" rows="10"><?php echo $boardcontent; ?></textarea>
                        </div>
                    </td>

                    <td align="right">
                        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <input name="boardid" type="hidden" id="boardid" value="<?php echo $boardid; ?>">
                        <input name="action" type="hidden" id="action" value="update">
                        <input type="submit" name="button" id="button" value="更新資料" class="btn btn-success">
                        <input type="button" name="button3" id="button3" value="回上一頁" class="btn btn-primary" onClick="window.history.back();">
                    </td>
                </tr>
            </form>
        </table>
    </content>
</body>

</html>

<?php
$stmt->close();
$db_link->close();
?>