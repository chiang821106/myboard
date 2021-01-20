<?php

require_once("connMysql.php");


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


//啟用session
session_start();
// 如果沒有登入session值或是session值為空時，執行下列登入動作
if (!isset($_SESSION['loginMember']) || ($_SESSION['loginMember'] == "")) {
    if (isset($_POST['username']) && ($_POST['password'])) {
        require_once('connMysql.php');
        //查詢admin資料表   
        $sql_query = "SELECT * FROM admin";
        $result = $db_link->query($sql_query);
        //取出admin表中的username/password值
        $row_result = $result->fetch_assoc();
        $username = $row_result['username'];
        $password = $row_result['password'];
        $db_link->close();
        //比對帳號密碼，若成功進入管理區/否則退回留言板主畫面
        if ($username == $_POST['username'] && $password == $_POST['password']) {
            $_SESSION['loginMember'] = $username;
            // header("Location:admin.php");
            // echo "<script> alert('主人您好!');</script>";
            //使用javasrcipt導向會員中心
            echo "<script> alert('主人您好!');location.href='admin.php';</script>";
        } else {
            // header("Location:index.php");
            echo "<script> alert('帳號或密碼錯誤!')</script>";
        }
    }
} else {
    //若已經有登入session值，則前往管理區
    header("Location:admin.php");
}
?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="_token" content="{{csrf_token()}}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>留言版實作</title>
    <!-- CSS only -->
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>

    <!-- 管理登入判斷 -->
    <script language="javascript">
        function checkForm() {
            if (document.formPost.username.value == "" && document.formPost.password.value == "") {
                alert("請輸入帳號及密碼!");
                return false;
            }
            if (document.formPost.username.value == "") {
                alert("帳號忘了輸入唷~");
                return false;
            }
            if (document.formPost.password.value == "") {
                alert("密碼忘了輸入唷~");
                return false;
            }
        }
    </script>
</head>

<body>
    <!-- 頁首 -->
    <header>
        <div align="center">
            <img class="bear" src="images/pig.gif" alt="" width="49" height="69">
            <strong class="header">留言版</strong>
        </div>

        <table border="0" align="center" cellpadding="4" cellspacing="0" class="table table-success table-striped container" style="max-width:1200px;">
            <div class="row">
                <tr>
                    <td class="col">
                        <button class="btn btn-success" id="admin">管理員登入</button>
                        <button class="btn btn-primary" id="myAddBtn">訪客留言</button>
                    </td>

                    <td class="col" style="display:none;">
                        <p align="center">資料筆數：<?php echo $total_records; ?></p>
                    </td>

                    <td align="right" class="col">
                        <p>
                            <?php if ($num_pages == 1) { // 第一頁則顯示 ?>
                                <a class="text-light btn btn-info" href="#"style="visibility:hidden">擴充用</a>
                                <a class="text-light btn btn-info" href="#"style="visibility:hidden">擴充用</a>
                                <a class="text-light btn btn-info" href="?page=<?php echo $num_pages + 1; ?>">下一頁</a>
                                <a class="text-light btn btn-info" href="?page=<?php echo $total_pages; ?>">最末頁</a> 
                            <?php }else if($num_pages > 1){ // 若不是第一頁則顯示?>
                                <a class="text-light btn btn-info" href="?page=1">第一頁</a> 
                                <a class="text-light btn btn-info " href="?page=<?php echo $num_pages - 1; ?>">上一頁</a>
                            <?php } ?>
                            <?php if ($num_pages < $total_pages && $num_pages != 1) { // 若不是最後一頁則顯示 ?>  
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
            <table border="0" align="center" class="table table-danger table-striped" style="max-width:1200px;" cellpadding="4" cellspacing="0">
                <tr>
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
                        <span class="heading" style="font-size:22px;">
                            <font style="font-size:24px;color:blue;">主題: </font><?php echo $row_RecBoard["boardsubject"]; ?>
                            <?php if($row_RecBoard["checked"] == "1") {; ?>
                                <img align="right" src="images/love.gif" alt="已讀"width="35"height="35">
                            <?php }else{ ?>
                                <img align="right" src="images/unlove.gif" alt="未已讀"width="35"height="35">
                            <?php } ?>
                        </span>
                        <hr>
                        <p><?php echo nl2br($row_RecBoard["boardcontent"]); ?></p>
                        <p align="right">
                            <?php echo $row_RecBoard["boardtime"]; ?>
                        </p>
                    </td>
                </tr>
            </table>
        <?php } ?>
    </content>

    <!-- 管理員登入盒子 -->
    <div class="container">
        <div class="modal fade" id="adminModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">
                    <div class="modal-header" style="background:linear-gradient(red,yellow);">
                        <span class="text-dark" style="font-size:36px;">管理員登入</span>
                    </div>
                    <div class="modal-body">
                        <form id="formPost" name="formPost" method="post" onSubmit="return checkForm();">
                            <div class="form-group form-inline">
                                <label for="username" class="col-3">帳號:</label>
                                <input type="text" class="form-control col-8" name="username" id="username">
                            </div>

                            <div class="form-group form-inline">
                                <label for="password" class="col-3">密碼:</label>&nbsp;
                                <input type="password" class="form-control col-8" name="password" id="password">
                            </div>
                            &nbsp;&nbsp;
                            <div class="modalBtn">
                                <button type="submit" class="btn btn-primary" id="adminOkBtn">登入</button>
                            </div>
                            <input name="action" type="hidden" id="action" value="add">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 訪客留言盒子 -->
    <div class="container">
        <div class="modal fade" id="myAddModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">
                    <div class="modal-header" style="background:linear-gradient(red,yellow);">
                        <span class="text-dark" style="font-size:36px;">我要留言</span>
                    </div>
                    <div class="modal-body">
                        <form id="myForm" name="myForm">
                            <div class="form-group form-inline">
                                <label for="boardname" class="col-3">姓名:</label>
                                <input type="text" class="form-control col-8" name="boardname" id="boardname">
                            </div>

                            <div class="form-group form-inline">
                                <label for="boardsex" class="col-3">性別(請選擇):</label>
                                <select id="boardsex" type="text" class="form-control col-8" name="boardsex" value="">
                                    <option value="男" selected>男</option>
                                    <option value="女">女</option>
                                </select>
                            </div>

                            <div class="form-group form-inline">
                                <label for="boardsubject" class="col-3">主題:</label>
                                <input type="text" class="form-control col-8" name="boardsubject" id="boardsubject">
                            </div>

                            <div class="form-group form-inline">
                                <label for="boardcontent" class="col-3">內容:</label>
                                <textarea name="boardcontent" id="boardcontent" cols="30" rows="10" class="form-control col-8"></textarea>
                            </div>

                            <div class="modalBtn">
                                <button type="submit" class="btn btn-primary" id="myAddOkBtn">送出</button>
                            </div>

                            <input name="action" type="hidden" id="action" value="add">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery 開啟盒子及傳送留言資料 -->
    <script>
        $(document).ready(function() {

            // 開啟訪客留言盒子+傳遞資料到後端
            $("#myAddBtn").click(function() {
                //呼叫對話框
                $("#myAddModal").modal('show');
                // // 先清空各欄位的值
                $('#boardname').val("");
                $('#boardsex').val("");
                $('#boardsubject').val("");
                $('#boardcontent').val("");
            });
            // -------------新增 資料_對話框確認按鈕
            jQuery('#myAddOkBtn').click(function(e) {

                if (document.myForm.boardname.value != "" && document.myForm.boardsex.value != "" && document.myForm.boardsubject.value != "" && document.myForm.boardcontent.value != "") {
                    // 建立要輸入資料庫的值
                    var dataToServer = {
                        // 姓名
                        boardname: $("#boardname").prop("value"),
                        // 性別
                        boardsex: $("#boardsex").prop("value"),
                        // 主題
                        boardsubject: $("#boardsubject").prop("value"),
                        // 內容
                        boardcontent: $("#boardcontent").prop("value"),
                    };

                    e.preventDefault();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });

                    jQuery.ajax({
                        url: 'post.php',
                        method: 'post',
                        data: dataToServer,
                    }).then(function() {
                        //關掉對話框
                        $("#myAddModal").modal("hide");
                        alert('留言成功!');
                        // 重整畫面
                        // window.history.go(0);
                        location.href='index.php?page=1';
                    })
                } else if (document.myForm.boardname.value == "") {
                    alert("請輸入姓名!");
                    return false;
                } else if (document.myForm.boardsex.value == "") {
                    alert("請填選性別!");
                    return false;
                } else if (document.myForm.boardsubject.value == "") {
                    alert("請輸入主題!");
                    return false;
                } else if (document.myForm.boardcontent.value == "") {
                    alert("請輸入內容!");
                    return false;
                }else{
                    alert("您應該還有某個欄位未填唷!");
                    return false;
                }
            })

            // 開啟管理員登入盒子並清空值
            $("#admin").click(function() {
                //呼叫對話框
                $("#adminModal").modal('show');
                // // 先清空各欄位的值
                $('#username').val("");
                $('#password').val("");
            });

        })
    </script>

</body>

</html>