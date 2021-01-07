<?php
session_start();
require('../app/dbconnect.php');
require('../app/functions.php');

// 
// ログイン確認
// 
require('../app/_parts/_checkLogin.php');

//URL直接記入できた場合、追い返す
if (!isset($_SESSION['join'])) {
  header('Location: http://localhost:8888/liko_201223/web/index.php');
  exit();
}

$defHash = "da39a3ee5e6b4b0d3255bfef95601890afd80709";

if($_SESSION['join']['image'] != $_SESSION['join']['time'].$defHash){
  $imageName = $_SESSION['join']['image'];
  list($width, $height, $type, $attr) = getimagesize('../member_picture/'.$imageName);


  $newWidth = 0;//新横幅
  $newHeight = 0;//新縦幅
  $w = 100;//最大横幅
  $h = 100;//最大縦幅

  if($h < $height && $w < $width){
    if($w < $h){
      $newWidth = $w;
      $newHeight = $height * ($w / $width);
    } else if($h < $w) {
      $newWidth = $width * ($h / $height);
      $newHeight = $h;
    }else{
      if($width < $height){
        $newWidth = $width * ($h / $height);
        $newHeight = $h;
      }else if($height < $width){
        $newWidth = $w;
        $newHeight = $height * ($w / $width);
      }else if($height == $width){
        $newWidth = $w;
        $newHeight = $h;
      }
    }
  }else if($height < $h && $width < $w){
      $newWidth = $width;
      $newHeight = $height;
  }else if($h < $height && $width <= $w){
      $newWidth = $width * ($h / $height);
      $newHeight = $h;
  }else if($height <= $h && $w < $width){
      $newWidth = $w;
      $newHeight = $height * ($w / $width);
  }else if($height == $h && $width < $w){
      $newWidth = $width * ($h / $height);
      $newHeight = $h;
  }else if($height < $h && $width == $w){
      $newWidth = $w;
      $newHeight = $height * ($w / $width);
  }else{
      $newWidth = $width;
      $newHeight = $height;
  }


  // // 関数使用
  // $ans = iconResize($width, $height);
  // // 値の格納
  // $newWidth = (int)ans[0];//新横幅
  // $newHeight = (int)ans[1];//新縦幅

  // 4文字の拡張子調整
  $ext = substr($imageName,-4);
  if($ext == 'jpeg' || $ext == 'JPEG'){
    $ext = '.' . $ext;
  }
}

//画像リサイズ
if($ext == '.gif'){
  $baseImage = imagecreatefromgif('../member_picture/'.h($imageName));
  $image = imagecreatetruecolor($newWidth, $newHeight);
  imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
  imagegif($image, '../member_picture/'.h($imageName));
}else if($ext == '.png' || $ext == '.PNG'){
  $baseImage = imagecreatefrompng('../member_picture/'.h($imageName));
  $image = imagecreatetruecolor($newWidth, $newHeight);
  imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
  imagepng($image, '../member_picture/'.$imageName);
}else if($ext == '.jpg' || $ext == '.jpeg' || $ext == '.JPG' || $ext == '.JPEG'){
  $baseImage = imagecreatefromjpeg('../member_picture/'.h($imageName));
  $image = imagecreatetruecolor($newWidth, $newHeight);
  imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
  imagejpeg($image, '../member_picture/'.h($imageName));
}

//更新処理
if(!empty($_POST)) {
  $statement = $db->prepare('UPDATE members SET picture=?,modified=NOW() WHERE id=?');
  echo $ret = $statement->execute(array(
    $_SESSION['join']['image'], $member['id'],
  ));

  unset($_SESSION['join']);
  // changeResult.phpにURL記入で繋げなくする処理
  $_SESSION['changeThanks'] = "true";

  header('Location: http://localhost:8888/liko_201223/web/changeResult.php');
  exit();

}

//アイコン用のext取得
$iconExt = substr($member['picture'],-4);

/* 
ヘッダーファイル読み込み
*/
include('../app/_parts/_header.php');

?>


<!-- 
  HTML
 -->

<header class="indexHeader-bar">
  <img src="../join/img/whiteLogo.png" alt="liko" class="indexHeader-logo">
</header>

<div class="container-fluid">
  <nav class="col-md-2 leftFix-contents">
  
    <div class="leftFix-configMenus">
      <a href="index.php"><i class="fas fa-home"></i>ホーム</a>
      <a href="userpage.php"><i class="fas fa-user-alt"></i>マイページ</a>
      <a href="changeIcon.php"><i class="fas fa-cog"></i>アイコンの変更</a>
      <a href="../app/logout.php"><i class="fas fa-sign-out-alt"></i>ログアウト</a>
  
      <div class="leftFix-menusBorder"></div>
  
      <div class="leftFix-searchForm">
        <p>投稿を検索する</p>
        <form class="searchForm" action="search.php" method="post">
          <div class="input-group">
            <input type="text" class="form-control searchBox" placeholder="Search for..." aria-label="キーワード" aria-describedby="basic-addon" name="search" value="">
            <div class="input-group-append">
              <button class="btn btn-primary btn-sm p-0" type="submit"><i class="fas fa-search"></i></button>
            </div>
          </div>
        </form>
      </div>
  
    </div>
  
    <div class="leftFix-userPost">
    <?php 
      if($iconExt != 'jpeg' && $iconExt != '.png' && $iconExt != '.PNG'
      && $iconExt != 'JPEG' && $iconExt != '.gif' && $iconExt != '.jpg'
      && $iconExt != '.JPG' ):
    ?>
      <img class="iconImg img-thumbnail" src="../member_picture/user.png">
    <?php else: ?>
      <img class="iconImg img-thumbnail" src="../member_picture/<?php echo h($member['picture']); ?>">
    <?php endif; ?>
      <p><b><?php echo h($member['name']); ?></b></p>
      <a class="openCommentModal btn btn-primary disabled" role="button" data-toggle="modal" data-target="#userPost-modal">投稿する</a>
    </div>
  
  </nav> <!-- leftFix-contents -->


  <!-- 
    アイコン変更
   -->

  <nav class="userGreeting col-md-10 offset-md-2 pt-3">
    <div>
      <span>変更内容の確認</span>
    </div>
  </nav>

  <nav class="col-md-10 offset-md-2 my-4 pb-3">
    <form action="" enctype="multipart/form-data" method="post">
      <?php if($_SESSION['join']['image'] != $_SESSION['join']['time'].$defHash) :?>
        <img class="img-thumbnail check-iconImg" src="../member_picture/<?php echo h($_SESSION['join']['image']); ?>">
      <p><br>
      アイコンは画像の中心から切り抜かれます。</p><?php else: ?>
      <p>設定されていません。<br>
      デフォルトのアイコンが自動で設定されます。</p><?php endif; ?>
      <div class="check-buttons pb-3">
        <a class="btn btn-outline-primary btn-sm mr-5" role="button" href="changeIcon.php">
          戻る
        </a>
        <input type="hidden" name="action" value="submit">
        <button class="btn btn-primary btn-sm" type="submit">設定する</button>
      </div>
    </form>
  </nav>


  <!-- 
    フッタークレジット
   -->

  <footer class="col-md-10 offset-md-2 footer-credit mt-5">
    <p style="color: #666;">©️2020-2021 skonishi.</p>
  </footer>


</div> <!-- container-fluid -->

<!-- 
  レスポンシブフッターメニュー
  -->

<nav class="responsive-footerMenus bg-dark">
  <a href="index.php" class="text-white"><i class="fas fa-home"></i></a>
  <a data-toggle="modal" data-target="#searchModal" class="text-white"><i class="fas fa-search"></i></a>
  <a data-toggle="modal" data-target="#configModal" class="text-white"><i class="fas fa-cog"></i></a>
  <a href="userpage.php" class="text-white"><i class="fas fa-user-alt"></i></a>
</nav>

<!-- 
  レスポンシブ検索モーダル
 -->

<nav class="modal" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="searchModalLabel">検索する</h5>
        <!-- 閉じるアイコン -->
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <!-- モーダル 本文 -->
      <div class="modal-body">

        <form class="searchForm" action="search.php" method="post">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Search for..." aria-label="キーワード" aria-describedby="basic-addon" name="search" value="">
            <div class="input-group-append">
              <button class="btn btn-secondary btn-sm" type="submit"><i class="fas fa-search"></i></button>
            </div>
          </div>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">閉じる</button>
      </div>
    </div>
  </div>
</nav>

<!-- 
  レスポンシブアイコン変更、ログアウトモーダル
 -->

 <nav class="modal" id="configModal" tabindex="-1" role="dialog" aria-labelledby="configModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="configModalLabel">設定</h5>
        <!-- 閉じるアイコン -->
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <!-- モーダル 本文 -->
      <div class="modal-body configmodal-body">
        <a href="changeIcon.php" class="text-dark"><i class="fas fa-cog"></i>アイコンの変更</a>
        <a href="../app/logout.php" class="text-dark"><i class="fas fa-sign-out-alt"></i>ログアウトする</a>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">閉じる</button>
      </div>
    </div>
  </div>
</nav>




<?php
include('../app/_parts/_footer.php');

?>