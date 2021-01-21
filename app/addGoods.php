<?php
session_start();
require('dbconnect.php');
require('functions.php');

// 
// ログイン確認
// 
require('../app/_parts/_checkLogin.php');

$postid = $_POST['postid'];
$memberid = $_POST['memberid'];

//いいねの初期数値を記録
$defs= $db->prepare('SELECT good FROM posts WHERE id=?');
$defs->execute( array($postid) );
$def = $defs->fetch();
$defGood = $def['good'];

//posts DBにいいね追加後の値を格納する
$postsDBs = $db->prepare('UPDATE posts SET good=?, modified=NOW() WHERE id=?');
$defGood += 1;
$postsDB = $postsDBs->execute(array( $defGood, $postid, ));
// echoを挟むと、「1」という表記が出てくる
// echo $postsDB = $postsDBs->execute(array( $defGood, $postid, ));


//いいね更新後の値を格納する
$updates = $db->prepare('SELECT good FROM posts WHERE id=?');
$updates->execute( array($postid) );
$update = $updates->fetch();
$updateGood = $update['good'];

//goods DBに、誰がどの投稿にいいねしたのかを格納する
$goodsDBs = $db->prepare('INSERT INTO goods SET member_id=?, post_id=?, created=NOW()');
$goodsDB = $goodsDBs->execute(array( $member['id'], $postid, ));
// echo $goodsDB = $goodsDBs->execute(array( $member['id'], $postid, ));

echo $updateGood;

?>