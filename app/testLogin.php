<?php 
session_start();
require('dbconnect.php');
//テストアカウントでのログイン

$_POST['email'] = 'test@test.com';
$_POST['password'] = 'test';


$login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
$login->execute(array( $_POST['email'], hash('sha256',$_POST['password']),) );
$member = $login->fetch();

//ログイン成功処理
if ($member) {
  $_SESSION['id'] = $member['id'];
  $_SESSION['time'] = time();
  $sessionid = hash('sha256', $_POST['email']);

header('Location: https://liko.link/web/index.php');
exit();

}
?>