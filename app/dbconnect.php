<?php
//DB接続
// /joinの場合：require('../../app/dbconnect.php');
// 
try{
	$db = new PDO('mysql:dbname=liko;host=localhost;charset=utf8','root','pMITAcddm3or');
	//$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch (PDOException $e){
	echo 'DB接続エラー: ' . $e->getMessage();
}
?>