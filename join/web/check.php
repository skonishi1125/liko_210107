<?php
session_start();

// 2階層上のappフォルダに取りに行く
require('../../app/dbconnect.php');
require('../app/functions.php');


if(!isset($_SESSION['join'])){
  header('Location: http://localhost:8888/liko_201223/join/web/index.php');
  exit();
}

$defHash = "da39a3ee5e6b4b0d3255bfef95601890afd80709";
//画像が選択された時のみ表示するように設定
//選択していない場合は日付 + defHashの値となる
if($_SESSION['join']['image'] != $_SESSION['join']['time'].$defHash){
  $imageName = $_SESSION['join']['image'];
  list($width, $height, $type, $attr) = getimagesize('../../member_picture/'.$imageName);

  //リサイズ処理
  list($newWidth, $newHeight) = iconResize($width, $height, 100, 100);
  
  // 4文字の拡張子調整
  $ext = substr($imageName,-4);
  if($ext == 'jpeg' || $ext == 'JPEG'){
    $ext = '.' . $ext;
  }
}

//画像リサイズ
if($ext == '.gif'){
  $baseImage = imagecreatefromgif('../../member_picture/'.h($imageName));
  $image = imagecreatetruecolor($newWidth, $newHeight);
  imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
  imagegif($image, '../../member_picture/'.h($imageName));
}else if($ext == '.png' || $ext == '.PNG'){
  $baseImage = imagecreatefrompng('../../member_picture/'.h($imageName));
  $image = imagecreatetruecolor($newWidth, $newHeight);
  imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
  imagepng($image, '../../member_picture/'.$imageName);
}else if($ext == '.jpg' || $ext == '.jpeg' || $ext == '.JPG' || $ext == '.JPEG'){
  $baseImage = imagecreatefromjpeg('../../member_picture/'.h($imageName));
  $image = imagecreatetruecolor($newWidth, $newHeight);
  imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
  imagejpeg($image, '../../member_picture/'.h($imageName));
}

//登録処理
if(!empty($_POST)) {
  $statement = $db->prepare('INSERT INTO members SET name=?,email=?,password=?,picture=?, session_id=?, created=NOW()');
  $ret = $statement->execute(array(
    $_SESSION['join']['name'],
    $_SESSION['join']['email'],
    hash('sha256',$_SESSION['join']['password']),
    $_SESSION['join']['image'],
    hash('sha256',$_SESSION['join']['email']),
  ));
  unset($_SESSION['join']);

  // thanks.phpにURL直移入で繋げなくする処理
  $_SESSION['thanks'] = "true";

  header('Location: http://localhost:8888/liko_201223/join/web/thanks.php');
  exit();
}

include('../app/_parts/_header.php');

?>

<!-- HTML
--------------------------------------->

<!-- レスポンシブヘッダーバー -->
<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top resHeader-bar login_thanks_checkbar">
  <a href="index.php">
    <img src="../img/whiteLogo.png" alt="Liko" class="header-barLogo ml-4 py-1">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#header-menus" aria-expanded="false" aria-label="切り替え">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="header-menus">
    <ul class="navbar-nav mr-auto navbar-contents">
      <li>
        <a href="index.php?action=rewrite"><i class="fas fa-user-plus"></i>トップへ</a>
      </li>

      <li>
        <a href="../../web/login.php"><i class="fas fa-sign-in-alt"></i>ログインする</a>
      </li>

      <li>
        <a href="../../app/testLogin.php"><i class="fas fa-sign-in-alt testLogin"></i>お試しログイン</a>
      </li>
    </ul>
  </div>
</nav>

<section class="container check-wrapper">
  <h5 class="py-3 check-wrapperTitle">登録内容の確認</h5>

  <div class="check-form">
    <form action="" enctype="multipart/form-data" method="post">
      <input name="action" type="hidden" value="submit">
      <h5 class="py-3">ハンドルネーム</h5>
      <p><?php echo h($_SESSION['join']['name']); ?></p>
      <h5 class="py-3">メールアドレス</h5>
      <p><?php echo h($_SESSION['join']['email']); ?></p>
      <h5 class="py-3">パスワード</h5>
      <p>表示されません(暗号化されて格納されます)。</p>
      <h5 class="py-3">アイコン画像</h5>
      <?php if($_SESSION['join']['image'] != $_SESSION['join']['time'].$defHash) :?>
        <img class="img-thumbnail check-iconImg" src="../../member_picture/<?php echo h($_SESSION['join']['image']); ?>">
      <p><br>
      アイコンは画像の中心から切り抜かれます。</p><?php else: ?>
      <p>設定されていません。<br>
      デフォルトのアイコンが自動で設定されます。</p><?php endif; ?>
      <div class="check-buttons pb-3">
        <a class="btn btn-outline-primary btn-sm mr-5" role="button" href="index.php?action=rewrite">
          書き直す
        </a>
        <button class="btn btn-primary btn-sm" type="submit">登録する</button>
      </div>
    </form>
  </div>

</section>



<?php
include('../app/_parts/_footer.php');
?>