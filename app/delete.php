<?php 
session_start();
require('../app/dbconnect.php');

// どのURLから削除ボタンを押したか
$url = $_SESSION['currentURI'];

if(isset($_SESSION['id'])){
  $id = $_REQUEST['id'];

  //投稿の確認
  $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
  $messages->execute(array($id));
  $message = $messages->fetch();

  if($message['member_id'] == $_SESSION['id']){
    //削除
    $del = $db->prepare('DELETE FROM posts WHERE id=?');
    $del->execute(array($id));
  }
}

header('Location: ' . $url);
exit();

?>