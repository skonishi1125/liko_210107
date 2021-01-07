<?php
session_start();
require('dbconnect.php');
require('functions.php');

// 
// ログイン確認
// 
require('../app/_parts/_checkLogin.php');

//どのURLからいいね!ボタンを押したか(index, userpage等)
$url = $_SESSION['currentURI'];

//どの投稿に対して処理をするか
$postid = $_GET['good'];

// echo $postid . " " . $url;

//いいねの初期数値を記録
$defaults = $db->prepare('SELECT good FROM posts WHERE id=?');
$defaults->execute( array($postid) );
$default = $defaults->fetch();
$defaultGood = $default['good'];

// echo ' 元々のいいねの数:' . $defaultGood . ' ';

//posts DBにいいね追加後の値を格納する
$postsDBs = $db->prepare('UPDATE posts SET good=?, modified=NOW() WHERE id=?');
$defaultGood += 1;
echo $postsDB = $postsDBs->execute(array( $defaultGood, $postid, ));

//goods DBに、誰がどの投稿にいいねしたのかを格納する
$goodsDBs = $db->prepare('INSERT INTO goods SET member_id=?, post_id=?, created=NOW()');
echo $goodsDB = $goodsDBs->execute(array( $member['id'], $postid, ));


// 元いたURLに戻る

header('Location: ' . $url);
exit();



// if (isset($_REQUEST['good']) ) {
//   $good = $db->prepare('UPDATE posts SET good=?, modified=NOW() WHERE id=?');
//   $defGood = $defGood + 1;
//   echo $retGood = $good->execute(array(
//     $defGood, $_REQUEST['good'],
//   ));

//   //いいねテーブルへの格納
//   $goodState = $db->prepare('INSERT INTO goods SET member_id=?, post_id=?,created=NOW()');
//   echo $goodRet = $goodState->execute(array(
//     $member['id'], $_REQUEST['good'],
//   ));

//   //echo $goodRetを使わない場合はこちらを採用する　$goodRet = $goodState->fetch();

//   header('Location: http://localhost:8888/liko_201223/web/index.php');
//   exit();

// }




?>