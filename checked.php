<?php 

require_once("connMysql.php");
$checked = 1;
$boardid = $_POST['boardid'];
$sql_query = "UPDATE board SET checked=? WHERE boardid= ?";
    $stmt = $db_link->prepare($sql_query);
    $stmt->bind_param('ii',$checked ,$boardid);
    $stmt->execute();
    $stmt->close();
    //返回後台區
    header("Location:admin.php");
?>


