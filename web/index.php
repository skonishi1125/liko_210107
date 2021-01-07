<?php
session_start();
require('../app/dbconnect.php');
require('../app/functions.php');

// 
// ログイン確認
// 
require('../app/_parts/_checkLogin.php');

/* 
-----
投稿をDBに格納する
-----
*/

if (!empty($_POST)) {
  //画像の有無判定
  $postPicName = $_FILES['postpic']['name'];
  $videoURL = substr($_POST['video'], 0, 23);
  $mobileURL = substr($_POST['video'], 0, 21);
  $videoID = substr($_POST['video'], -13);
  $v = substr(h($_POST['video']), -11);
  //https://www.youtube.com

  if (!empty($videoURL)) {
    if ($videoURL != 'https://www.youtube.com' && $mobileURL != 'https://m.youtube.com') {
      $error['video'] = 'type';
    }
    if (substr($videoID,0,2) != 'v=') {
      $error['video'] = 'type';
    }
  }

  if (empty($_POST['message'])) {
    $error['message'] = 'blank';
  }

  //どっちも入っていた場合の処理
  if ($_POST['message'] != '' && !empty($postPicName)) {
    //拡張子チェック
    if (!empty($postPicName)) {
      $ext = substr($postPicName, -4);
      if ($ext == 'jpeg' || $ext == 'JPEG'){
        $ext = '.' . $ext;
      }
      if ($ext != '.jpg' && $ext !='.png' && $ext !='.PNG' && $ext !='.JPG' && $ext !='.gif' && $ext != '.JPEG' && $ext != '.jpeg'){
          $error['postpic'] = 'type';
      }
    }

    if (empty($error)) {
      $postImgTime = date('YmdHis');
      $postPicName = $postImgTime . sha1($postPicName) . $ext;

      move_uploaded_file($_FILES['postpic']['tmp_name'], '../post_picture/' . $postPicName);

        //動画の紹介があった場合
      if (!empty($_POST['video']) ) {
        $message = $db->prepare('INSERT INTO posts SET title=?,member_id=?, message=?, post_pic=?, video=?, reply_post_id=?, created=NOW()');

        if ($_POST['reply_post_id'] == 0) {
          $message->execute(array(
            $_POST['title'], $member['id'], $_POST['message'], $postPicName, $_POST['video'], 0
          ));
        } else {
          $message->execute(array(
            $_POST['title'], $member['id'], $_POST['message'], $postPicName, $_POST['video'], $_POST['reply_post_id'],
          ));
        }
      } else {
        $message = $db->prepare('INSERT INTO posts SET title=?,member_id=?, message=?, post_pic=?, reply_post_id=?, created=NOW()');
        if ($_POST['reply_post_id'] == 0){
          $message->execute(array(
            $_POST['title'], $member['id'], $_POST['message'], $postPicName, 0
          ));
        } else {
          $message->execute(array(
            $_POST['title'], $member['id'], $_POST['message'], $postPicName, $_POST['reply_post_id'],
          ));
        }
      }

      /*----------------投稿画像のリサイズ-----------------*/
      $imageName = $postPicName;
      list($width, $height, $type, $attr) = getimagesize('../post_picture/'.$imageName);

      $newWidth = 1000;//新しい横幅
      $newHeight = 1000;//新しい縦幅

      fitContain(1000, $width, $height, $newWidth, $newHeight);

      /*--------Exif--------------*/
      //exif読み取り
      $exif_data = exif_read_data('../post_picture/'.h($imageName));

      //画像リサイズ
      if ($ext == '.gif'){
        $baseImage = imagecreatefromgif('../post_picture/'.h($imageName));
        $image = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagegif($image, '../post_picture/'.h($imageName));

      } else if ($ext == '.png' || $ext == '.PNG'){
        $baseImage = imagecreatefrompng('../post_picture/'.h($imageName));
        $image = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagepng($image, '../post_picture/'.h($imageName));

      } else if ($ext == '.jpg' || $ext == '.jpeg' || $ext == '.JPG' || $ext == '.JPEG'){
        $baseImage = imagecreatefromjpeg('../post_picture/'.h($imageName));
        $image = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagejpeg($image, '../post_picture/'.h($imageName));
        //exif読み取り
        imageOrientation('../post_picture/'.h($imageName), $exif_data['Orientation']);
      }

      header('Location: http://localhost:8888/liko_201223/web/index.php');
      exit();

    } /* enpty($error) */
  } // どっちも入っていた場合の処理


  //メッセージだけが入っていた場合の処理
  if  ($_POST['message'] != '') {
    if (empty($error)) {
      if (!empty($_POST['video']) ) {
        $message = $db->prepare('INSERT INTO posts SET title=?, member_id=?, message=?, video=?, reply_post_id=?, created = NOW()');

        if ($_POST['reply_post_id'] == ''){
          $message->execute(array(
            $_POST['title'], $member['id'], $_POST['message'], $_POST['video'], 0
          ));
        } else {
          $message->execute(array(
            $_POST['title'], $member['id'], $_POST['message'], $_POST['video'], $_POST['reply_post_id']
          ));
        }

      } else {
        $message = $db->prepare('INSERT INTO posts SET title=?, member_id=?, message=?, reply_post_id=?, created = NOW()');

        if ($_POST['reply_post_id'] == '') {
          $message->execute(array(
            $_POST['title'], $member['id'], $_POST['message'], 0
          ));
        } else {
          $message->execute(array(
            $_POST['title'], $member['id'], $_POST['message'], $_POST['reply_post_id'],
          ));
        }

      }
      header('Location: http://localhost:8888/liko_201223/web/index.php');
      exit();
    } /* enpty($error) */
  } // メッセージだけが入っていた場合の処理


  //画像が入っていた場合の処理
  if (!empty($postPicName)) {
    //拡張子チェック
    if (!empty($postPicName)) {
      $ext = substr($postPicName, -4);
      if ($ext == 'jpeg' || $ext == 'JPEG') {
        $ext = '.' . $ext;
      }
      if ($ext != '.jpg' && $ext !='.png' && $ext !='.PNG' && $ext !='.JPG' && $ext !='.gif' && $ext != '.JPEG' && $ext != '.jpeg') {
        $error['postpic'] = 'type';
      }
    }

    if (empty($error)) {
      //画像ファイルの名前をわからなくする処理
      $postImgTime = date('YmdHis');
      $postPicName = $postImgTime . sha1($postPicName) . $ext;
      move_uploaded_file($_FILES['postpic']['tmp_name'], '../post_picture/' . $postPicName);
      if ($videoURL == 'https://www.youtube.com'){
        $message = $db->prepare('INSERT INTO posts SET title=?, member_id=?, post_pic=?, video=?, reply_post_id=?, created=NOW()');
        if ($_POST['reply_post_id'] == '') {
          $message->execute(array(
            $_POST['title'], $member['id'], $postPicName, $_POST['video'], 0
          ));
        } else {
          $message->execute(array(
            $_POST['title'], $member['id'], $postPicName, $_POST['video'], $_POST['reply_post_id'],
          ));
        }

    } else {
      $message = $db->prepare('INSERT INTO posts SET title=?, member_id=?, post_pic=?, reply_post_id=?, created=NOW()');
      if ($_POST['reply_post_id'] == '') {
        $message->execute(array(
          $_POST['title'], $member['id'], $postPicName, 0,
        ));
      } else {
        $message->execute(array(
          $_POST['title'], $member['id'], $postPicName, $_POST['reply_post_id'],
        ));
      }
    }

    /*----------------投稿画像のリサイズ-----------------*/
    $imageName = $postPicName;
    list($width, $height, $type, $attr) = getimagesize('../post_picture/'.$imageName);

    $newWidth = 1000;//新しい横幅
    $newHeight = 1000;//新しい縦幅

    fitContain(1000, $width, $height, $newWidth, $newHeight);


      //画像リサイズ
    if ($ext == '.gif') {
      $baseImage = imagecreatefromgif('../post_picture/'.h($imageName));
      $image = imagecreatetruecolor($newWidth, $newHeight);
      imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
      imagegif($image, '../post_picture/'.h($imageName));

    } else if ($ext == '.png' || $ext == '.PNG'){
      $baseImage = imagecreatefrompng('../post_picture/'.h($imageName));
      $image = imagecreatetruecolor($newWidth, $newHeight);
      imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
      imagepng($image, '../post_picture/'.h($imageName));

    } else if ($ext == '.jpg' || $ext == '.jpeg'|| $ext == '.JPG' || $ext == '.JPEG'){
      $baseImage = imagecreatefromjpeg('../post_picture/'.h($imageName));
      $image = imagecreatetruecolor($newWidth, $newHeight);
      imagecopyresampled($image, $baseImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
      imagejpeg($image, '../post_picture/'.h($imageName));
      //exif読み取り
      imageOrientation('../post_picture/'.h($imageName), $exif_data['Orientation']);
    }

      header('Location: http://localhost:8888/liko_201223/web/index.php');
      exit();
    }
  } /* empty($error) */
} // 画像が入っていた時の処理



/*
----
投稿取得
----
*/

//ページ分け
$page = $_REQUEST['page'];
if ($page == ''){
  $page = 1;
}
//-の数対策
$page = max($page, 1);

$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 10);
$page = min($page, $maxPage);
$start = ($page - 1) * 10;

if ($start < 0){
  $start = 0;
}


$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id = p.member_id ORDER BY p.created DESC  LIMIT ?, 10');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();


//返信
if (isset($_REQUEST['res'])){
  $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.id=? ORDER BY p.created DESC');
  $response->execute(array($_REQUEST['res']));
  $table = $response->fetch();
  $message = '>>@' . $table['name']. '[' . $table['id'] . '] ';
}


// addGoods / subGoods.php用
$_SESSION['currentURI'] = $_SERVER['REQUEST_URI'];

// $goodPosts = $db->prepare('SELECT post_id FROM goods WHERE member_id=?');
// $goodPosts->bindParam(1, $member['id'], PDO::PARAM_INT);
// $goodPosts->execute();

// //いいねボタンが押された時の処理
// //初期goodを最初に取得しておく
// $origins = $db->prepare('SELECT good FROM posts WHERE id=?');
// $origins->execute(array(
//   $_REQUEST['good']
// ));
// $origin = $origins->fetch();
// $defGood = $origin['good'];

// if (isset($_REQUEST['good'])) {
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

//コメント(review機能)
if (isset($_POST['review'])) {
  if (!empty($_POST['review'])) {
    $reviews = $db->prepare('INSERT INTO reviews SET member_id=?, post_id=?, member_pic=?, comment=?, created=NOW()');
    $reviews->execute(array(
      $member['id'], $_POST['postid'], $member['picture'], $_POST['review'],
    ));

    header('Location: http://localhost:8888/liko_201223/web/index.php');
    exit();
    //これないと更新するたび増えていく
  }else{
    $error['review'] = 'blank';
  }
}

//search.phpで空コメントした場合のエラー
if ($_SESSION['review'] == 'blank'){
  $error['review'] = 'blank';
  unset($_SESSION['review']);
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
      <a class="openCommentModal btn btn-primary" role="button" data-toggle="modal" data-target="#userPost-modal">投稿する</a>
    </div>
  
  </nav> <!-- leftFix-contents -->

  <!-- 
    投稿モーダル
   -->

   <nav class="modal fade" id="userPost-modal" tabindex="-1" role="dialog" aria-labelledby="userPost-modal" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered" role="document">
       <div class="modal-content">
          <div class="modal-header">

            <h5 class="modal-title" id="userPost-modal">投稿する内容を記入</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>

          </div>

          <div class="modal-body container-fluid">
            <form action="" method="post" enctype="multipart/form-data" class="userPost-modalForm">
              <!-- タイトル -->
              <div class="form-group">
                <label for="title">タイトル</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= h($_POST['title']); ?>">
              </div>

              <!-- メッセージ(本文) -->
              <div class="form-group">
                <label for="message">メッセージ</label>
                <span class="badge badge-primary">必須</span>
                <textarea name="message" id="message" cols="50" rows="5" class="form-control" value="<?= h($_POST['message']); ?>"></textarea>
              </div>

              <input type="hidden" name="reply_post_id" value="<?php echo h($_REQUEST['res']); ?>">

              <!-- 画像 -->
              <div class="form-group">
                <label for="postpic">添付画像を選択</label>
                <input type="file" class="form-control-file" id="postpic" name="postpic">
                <small id="postpicHelp" class="form-text text-muted">
                投稿できる画像は「.jpg」「.png」「.gif」のファイルです。
                </small>
              </div>

              <!-- 動画 -->
              <div class="form-group">
                <label for="video">URLでYouTubeの動画を紹介する</label>
                <input type="text" class="form-control" id="video" name="video" value="<?= h($_POST['video']); ?>" placeholder="https://www.youtube.com/watch?v=ABCDEFGHIJ">
                <small id="videoHelp" class="form-text text-muted">
                  URLの末尾が"v=動画のID"で終わるように入力ください。
                </small>
              </div>

              <button type="submit" class="btn btn-primary btn-sm float-right">投稿</button>


            </form>
          </div>

       </div>
     </div>
   </nav>




  <?php
  // いいね処理
  // ログイン中のユーザーがどの投稿にいいねしているのかを格納
  $goodPosts = $db->prepare('SELECT post_id FROM goods WHERE member_id=?');
  $goodPosts->bindParam(1, $member['id'], PDO::PARAM_INT);
  $goodPosts->execute();
  
  // どの投稿にいいねしているかの情報を取得
  while ($goodPost = $goodPosts->fetch() ){
    $goodArray[] = $goodPost['post_id'];
  };
  
  ?>

  <!-- 
    エラーアラート
   -->

  <?php if (!empty($error)) : ?>
  <nav class="alert alert-danger alert-dismissible fade show col-md-10 offset-md-2 mt-3 error-wrapperLength" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <h5 class="alert-heading">投稿にエラーがありました。</h5>
    <hr>
    <!-- メッセージ無記入エラー -->
    <?php if($error['message'] == 'blank' || $error['review'] == 'blank'): ?>
      <p>・無記入のままで投稿することはできません。</p>
    <?php endif; ?>
    <!-- 拡張子エラー -->
    <?php if($error['postpic'] == 'type'): ?>
      <p>・非対応の画像ファイルです。拡張子を確認ください。</p>
    <?php endif; ?>
    <!-- ビデオエラー -->
    <?php if($error['video'] == 'type'): ?>
      <p>
        ・URLに誤りがあります。現状YouTube動画のみの対応となっています。<br>
        　投稿例：https://www.youtube.com/watch?v=ABCDEFGHIJK<br>
        　(URLの末尾がv=[動画のID]で終わるように投稿してください)
      </p>
    <?php endif; ?>
  </nav>
  <?php endif; ?>

  <!-- 
    ログインユーザーへの挨拶
   -->

  <nav class="userGreeting col-md-10 offset-md-2 pt-3">
    <div>
      <?php 
      if($iconExt != 'jpeg' && $iconExt != '.png' && $iconExt != '.PNG'
      && $iconExt != 'JPEG' && $iconExt != '.gif' && $iconExt != '.jpg'
      && $iconExt != '.JPG' ): 
      ?>
        <img src="../member_picture/user.png" class="img-thumbnail mr-2">
      <?php else: ?>
        <img src="../member_picture/<?php echo h($member['picture']); ?>" class="img-thumbnail mr-2">
      <?php endif; ?>
      <span><b><?php echo $member['name'] ?></b>さん、こんにちは！</span>
    </div>
  </nav>


  <!-- 
   ユーザー投稿
   -->
  <?php foreach ($posts as $post) : ?>

  <?php if (empty($post['post_pic']) && empty($post['video']) ): ?>
  <main class="tweet-wrapper col-md-10 offset-md-2">
    <article class="container-fluid tweetContents">

      <!-- ユーザー名、post ID、投稿時間 -->
      <section class="container-fluid tweetContents-user">

        <div class="row">
          <div class="col-auto text-md-center">
          <?php 
            $iconExt = substr($post['picture'],-4);
            if ($iconExt != 'jpeg' && $iconExt != '.png' && $iconExt != '.PNG'
            && $iconExt != 'JPEG' && $iconExt != '.gif' && $iconExt != '.jpg'
            && $iconExt != '.JPG' ) : 
          ?>
            <img class="img-thumbnail" src="../member_picture/user.png">
            <?php else: ?>
              <img class="img-thumbnail" src="../member_picture/<?= h($post['picture']);?>">
            <?php endif; ?>
          </div>

          <div class="col-auto">
            <span><b><?= h($post['name']); ?></b></span>
            <a href="view.php?id=<?= h($post['id']); ?>">Post ID:[<?= h($post['id']); ?>]</a>
            <br>
            <span><small>[<?= h($post['created']); ?>]</small></span>
          </div>
        </div>

      </section>
      
      <!-- 投稿内容 -->
      <section class="col-md-12 tweetContents-post">
        <?php if (!empty($post['title']) ) : ?>
        <h4 class="pb-1 mb-2"><?= h($post['title']); ?></h4>
        <?php endif; ?>

        <div class="col-md-12">
          <p><?= nl2br(makeLink(h($post['message']) ) ); ?></p>
        </div>
      </section>

      <!-- 投稿のリアクション -->

      <section class="container-fluid tweetContents-reaction pt-4">
        <h6 class="text-muted">投稿に対してリアクションしましょう</h6>

        <div class="row">
          <!-- コメントフォーム -->
          <div class="col-md-10">
            <form action="" method="post">
              <div class="form-group">
                <textarea name="review" id="commentForm" class="form-control" placeholder="コメントを記入する..."></textarea>
              </div>
              <input type="hidden" name="postid" value="<?php echo h($post['id']); ?>">
              <button type="submit" class="btn btn-success btn-sm comment-postButton">コメントする</button>
            </form>
          </div>

          <div class="col-md-2 reaction-space">
            <!-- いいねボタン -->
            <!-- $goodArray(ユーザーがいいねしたログ)の中に、post idが含まれている時(いいね済みの時) -->
            <?php $goodFlag = in_array($post['id'], $goodArray, true); ?>
            <?php if ($goodFlag) : ?>
              <a class="btn btn-danger btn-sm" role="button" href="../app/subGoods.php?good=<?= h($post['id']); ?>">
                <i class="good fas fa-heart"></i> <?= h($post['good']); ?>
              </a>
            <?php else: ?>
              <a class="btn btn-outline-danger btn-sm" role="button" href="../app/addGoods.php?good=<?= h($post['id']); ?>">
                <i class="good fas fa-heart"></i> <?= h($post['good']); ?>
              </a>
            <?php endif; ?>

            <?php if ($_SESSION['id'] == $post['member_id']) : ?>
              <a class="btn btn-outline-primary btn-sm ml-2" role="button" href="../app/delete.php?id=<?= h($post['id']); ?>">
                <i class="fas fa-trash"></i>
              </a>
            <?php endif; ?>

          </div>
        </div>

      </section>


    </article><!-- tweetContents -->
  </main><!-- tweet-wrapper -->

  <?php else: ?> <!-- if (empty($post['post_pic']) && empty($post['video']) ) -->

  <!-- 
   ユーザー投稿（画像ありver)
   -->
   <main class="tweet-wrapper col-md-10 offset-md-2">
    <article class="container-fluid tweetContents">

      <!-- ユーザー名、post ID、投稿時間 -->
      <section class="container-fluid tweetContents-user">

        <div class="row">
          <div class="col-auto text-md-center">
          <?php 
            $iconExt = substr($post['picture'],-4);
            if ($iconExt != 'jpeg' && $iconExt != '.png' && $iconExt != '.PNG'
            && $iconExt != 'JPEG' && $iconExt != '.gif' && $iconExt != '.jpg'
            && $iconExt != '.JPG' ) : 
          ?>
            <img class="img-thumbnail" src="../member_picture/user.png">
            <?php else: ?>
              <img class="img-thumbnail" src="../member_picture/<?= h($post['picture']);?>">
            <?php endif; ?>
          </div>

          <div class="col-auto">
            <span><b><?= h($post['name']); ?></b></span>
            <a href="view.php?id=<?= h($post['id']); ?>">Post ID:[<?= h($post['id']); ?>]</a>
            <br>
            <span><small>[<?= h($post['created']); ?>]</small></span>
          </div>
        </div>

      </section>
      
      <!-- 投稿内容 -->

      <section class="col-md-12 tweetContents-post">
        <?php if (!empty($post['title']) ) : ?>
        <h4 class="pb-1 mb-2"><?= h($post['title']); ?></h4>
        <?php endif; ?>

        <div class="row">
          <!-- 投稿文章 -->
          <div class="col-md-6 pb-3">
            <p><?= nl2br(makeLink(h($post['message']) ) ); ?></p>
          </div>

          <!-- 投稿コンテンツ -->
          <div class="col-md-6 tweetContents-media">
            <?php if (isset($post['video'])) : ?>
              <?php $v = substr($post['video'], -11); ?>
              <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="<?= 'https://www.youtube.com/embed/'. h($v); ?>" allowfullscreen></iframe>
              </div>
            <?php endif; ?>

            <?php if (isset($post['post_pic']) ) : ?>
              <img src="../post_picture/<?= h($post['post_pic']); ?>" alt="postpicture" class="img-thumbnail">
            <?php endif; ?>
          </div>
        </div>

      </section>

      <!-- 投稿のリアクション -->

      <section class="container-fluid tweetContents-reaction pt-4">
        <h6 class="text-muted">投稿に対してリアクションしましょう</h6>

        <div class="row">
          <!-- コメントフォーム -->
          <div class="col-md-10">
            <form action="" method="post">
              <div class="form-group">
                <textarea name="review" id="commentForm" class="form-control" placeholder="コメントを記入する..."></textarea>
              </div>
              <input type="hidden" name="postid" value="<?php echo h($post['id']); ?>">
              <button type="submit" class="btn btn-success btn-sm comment-postButton">コメントする</button>
            </form>
          </div>

          <div class="col-md-2 reaction-space">
            <!-- いいねボタン -->
            <!-- $goodArray(ユーザーがいいねしたログ)の中に、post idが含まれている時(いいね済みの時) -->
            <?php $goodFlag = in_array($post['id'], $goodArray, true); ?>
            <?php if ($goodFlag) : ?>
              <a class="btn btn-danger btn-sm" role="button" href="../app/subGoods.php?good=<?= h($post['id']); ?>">
                <i class="good fas fa-heart"></i> <?= h($post['good']); ?>
              </a>
            <?php else: ?>
              <a class="btn btn-outline-danger btn-sm" role="button" href="../app/addGoods.php?good=<?= h($post['id']); ?>">
                <i class="good fas fa-heart"></i> <?= h($post['good']); ?>
              </a>
            <?php endif; ?>

            <?php if ($_SESSION['id'] == $post['member_id']) : ?>
              <a class="btn btn-outline-primary btn-sm ml-2" role="button" href="../app/delete.php?id=<?= h($post['id']); ?>">
                <i class="fas fa-trash"></i>
              </a>
            <?php endif; ?>

          </div>
        </div>

      </section>

    </article><!-- tweetContents -->
  </main><!-- tweet-wrapper -->

  <?php endif; ?> <!-- if (empty($post['post_pic']) && empty($post['video']) ) -->




  <!-- 
    投稿に対するコメント
   -->
  <?php
    $revPosts = $db->prepare('SELECT m.name, m.picture, r.* FROM members m, reviews r WHERE m.id = r.member_id AND post_id=?');
    $revPosts->execute(array($post['id']));
  ?>
  <?php foreach ($revPosts as $revPost) : ?>

  <main class="comment-wrapper container-fluid mt-4">
    <article class="col-md-9 offset-md-3 comment-border">
      <!-- コメント欄の吹き出し部分 --> 
      <div class="borderTriangle"></div>
      <div class="borderTriWhite"></div>

      <div class="row my-3">
        <div class="col-auto text-md-center">
        <?php 
          $iconExt = substr($revPost['picture'],-4);
          if ($iconExt != 'jpeg' && $iconExt != '.png' && $iconExt != '.PNG'
          && $iconExt != 'JPEG' && $iconExt != '.gif' && $iconExt != '.jpg'
          && $iconExt != '.JPG' ) :
        ?>
          <img class="iconImg img-thumbnail" src="../member_picture/user.png">
          <?php else : ?>
            <img class="iconImg img-thumbnail" src="../member_picture/<?= h($revPost['picture']);?>">
          <?php endif; ?>
        </div>

        <div class="col-auto">
          <span class="font-weight-bold"><?= h($revPost['name']); ?></span>
          <span><small>[<?= h($revPost['created']); ?>]</small></span>
          <?php if ($_SESSION['id'] == $revPost['member_id']) : ?>
            <a class="btn btn-outline-primary btn-sm ml-2 comment-deleteBtn" role="button" href="../app/deleteReview.php?id=<?= h($revPost['id']); ?>">
            <i class="fas fa-trash mr-2"></i>
          </a>
          <?php endif; ?>
          <p><?= nl2br(makeLink(h($revPost['comment']) ) ); ?></p>
        </div>
      </div>

    </article>
  </main>
  <?php endforeach; ?> <!-- コメントに対してのforeach -->

  <?php endforeach; ?> <!-- 一つの投稿に対してのforeach -->


  <!-- 
    ページネーション
   -->

  <nav class="col-md-10 offset-md-2 page-wrapper" aria-label="ページネーション">
    <ul class="pagination">
      <?php if ($page > 1) : ?>
      <li class="page-item">
        <a class="page-link" href="index.php?page=<?= $page - 1 ?>">前</a>
      </li>
      <?php else: ?>
      <li class="page-item disabled">
        <a class="page-link">前</a>
      </li>
      <?php endif; ?>

      <li class="page-item">
        <a class="page-link" href="index.php">top</a>
      </li>

      <?php if ($page < $maxPage) : ?>
      <li class="page-item">
        <a class="page-link" href="index.php?page=<?= $page + 1 ?>">次</a>
      </li>
      <?php else: ?>
        <li class="page-item disabled">
          <a class="page-link">次</a>
        </li>
      <?php endif; ?>

    </ul>
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

<a class="btn btn-outline-primary responsive-postButton btn-lg" role="button" data-toggle="modal" data-target="#userPost-modal">
  <i class="fas fa-pencil-alt"></i>
</a>

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
