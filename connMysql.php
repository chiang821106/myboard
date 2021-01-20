<?php 

// 資料庫設定
$db_host = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "board";

// 資料庫連線
$db_link  = @new mysqli($db_host,$db_username,$db_password,$db_name);

if($db_link->connect_error != ""){
    echo "資料庫連線失敗";
}else{
    // 設定字元集與編碼
    $db_link->query("SET NAMES 'utf8'");
    // echo "成功";
}








?>