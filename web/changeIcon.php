<?php
session_start();
require('../app/dbconnect.php');
require('../app/functions.php');

// 
// ログイン確認
// 
require('../app/_parts/_checkLogin.php');


if (!empty($_FILES)) {
  $fileName = $_FILES['image']['name'];
  if (!empty($fileName)) {
    $ext = substr($fileName, -4);
    if ($ext != '.jpg' && $ext !='.png' && $ext !='.PNG' && $ext !='.gif' && $ext != 'JPEG' && $ext != 'jpeg' && $ext != '.JPG') {
      $error['image'] = 'type';
    }
  }

  if (empty($error)) {
      $postImgTime = date('YmdHis');
      if ($ext == 'jpeg' || $ext == 'JPEG') {
          $ext = '.' . $ext;
      }
      $_SESSION['ext'] = $ext;

      $image = $postImgTime . sha1($_FILES['image']['name']).$ext; 
      
      move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/'.$image);

      $_SESSION['join'] = $_POST;
      $_SESSION['join']['image'] = $image;
      $_SESSION['join']['time'] = $postImgTime;

      header('Location: http://localhost:8888/liko_201223/web/checkIcon.php');
      exit();

  }

}

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
    エラーアラート
   -->

  <?php if (!empty($error)) : ?>
  <nav class="alert alert-danger alert-dismissible fade show col-md-10 offset-md-2 mt-3 error-wrapperLength" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <h5 class="alert-heading">エラーがありました。</h5>
    <hr>
    <!-- 拡張子エラー -->
    <?php if($error['image'] == 'type'): ?>
      <p>・非対応の画像ファイルです。拡張子を確認ください。</p>
    <?php endif; ?>
  </nav>
  <?php endif; ?>


  <!-- 
    アイコン変更
   -->

  <nav class="userGreeting col-md-10 offset-md-2 pt-3">
    <div>
      <span>アイコンを変更する</span>
    </div>
  </nav>

  <nav class="col-md-10 offset-md-2 my-4 pb-3">
    <form action="" method="post" enctype="multipart/form-data">
      <div class="form-group changeIcon-form">
        <input type="file" name="image" id="image" class="form-control-file"> 
        <small id="imageHelp" class="form-text text-muted">
          未記入の場合、デフォルト画像が設定されます。<br>
          画像の拡張子は「.jpg」「.png」「.gif」が設定可能です。
        </small>
      </div>
      <button type="submit" class="btn btn-primary btn-sm float-right">確認画面へ</button>
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
