<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2017-11-28
 * Time: 8:09 PM
 */

try {
    $dsn = 'mysql:dbname=atyorku;host=localhost';
    $user = 'root';
    $password = '';
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    die();
}

$sql = "select * from user where id = 1";
$result = $dbh->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
print_r($row);
$row = $result->fetch(PDO::FETCH_ASSOC);
print_r($row);
$row = $result->fetch(PDO::FETCH_ASSOC);
print_r($row);

//foreach($result as $row){
//    echo $row['alias']."<br>";
//}


?>