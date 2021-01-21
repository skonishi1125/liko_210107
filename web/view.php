<?php
session_start();
require('../app/dbconnect.php');
require('../app/functions.php');

// 
// ログイン確認
// 
require('../app/_parts/_checkLogin.php');


/*
----
投稿取得
----
*/

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.id=? ORDER BY p.created DESC');
$posts->execute(array($_REQUEST['id']));

//返信
if (isset($_REQUEST['res'])){
  $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id = p.member_id AND p.id=? ORDER BY p.created DESC');
  $response->execute(array($_REQUEST['res']));
  $table = $response->fetch();
  $message = '>>@' . $table['name']. '[' . $table['id'] . '] ';
}


// いいねボタンが押された時の処理
// 削除済み index.phpに元の記述をコメントしている
// addGoods / subGoods.php用
$_SESSION['currentURI'] = $_SERVER['REQUEST_URI'];


//コメント(review機能)
if (isset($_POST['review'])) {
  if (!empty($_POST['review'])) {
    $reviews = $db->prepare('INSERT INTO reviews SET member_id=?, post_id=?, member_pic=?, comment=?, created=NOW()');
    $reviews->execute(array(
      $member['id'], $_POST['postid'], $member['picture'], $_POST['review'],
    ));

    header('Location: https://liko.link/web/view.php?id=' . $_REQUEST['id']);
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
  <a href="index.php">
    <img src="../join/img/whiteLogo.png" alt="liko" class="indexHeader-logo">
  </a>
</header>

<div class="container-fluid mt-5">
  <nav class="col-md-2 leftFix-contents">
  
    <div class="leftFix-configMenus">
      <a href="index.php"><i class="fas fa-home"></i>ホーム</a>
      <a href="userpage.php?id=<?= h($member['id']); ?>"><i class="fas fa-user-alt"></i>マイページ</a>
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
      <a href="userpage.php?id=<?= $member['id']; ?>">
        <img class="iconImg img-thumbnail" src="../member_picture/user.png">
      </a>
    <?php else: ?>
      <a href="userpage.php?id=<?= $member['id']; ?>">
        <img class="iconImg img-thumbnail" src="../member_picture/<?php echo h($member['picture']); ?>">
      </a>
    <?php endif; ?>
      <p><b><?php echo h($member['name']); ?></b></p>
      <a class="openCommentModal btn btn-primary disabled" role="button" data-toggle="modal" data-target="#userPost-modal">投稿する</a>
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
      <p>・メッセージ無記入のままで投稿することはできません。</p>
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
        　(URLの末尾がv=[動画のID]で終わるように投稿してください)<br>
        　現状YouTube側の「共有」ボタンで取得できるURLは非対応ですので、ブラウザ上のURLをコピー&ペーストにてご利用ください。
      </p>
    <?php endif; ?>
  </nav>
  <?php endif; ?>


  <!-- 
   ユーザー投稿
   -->
  <?php if ($post = $posts->fetch() ) : ?>
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
              <a href="userpage.php?id=<?= $post['member_id'] ?>">
                <img class="img-thumbnail" src="../member_picture/user.png">
              </a>
            <?php else: ?>
              <a href="userpage.php?id=<?= $post['member_id'] ?>">
                <img class="img-thumbnail" src="../member_picture/<?= h($post['picture']);?>">
              </a>
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
            <?php require('../app/_parts/_reaction_good.php'); ?>
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
              <a href="userpage.php?id=<?= h($post['member_id']); ?>">
                <img class="img-thumbnail" src="../member_picture/user.png">
              </a>
            <?php else: ?>
              <a href="userpage.php?id=<?= h($post['member_id']); ?>">
                <img class="img-thumbnail" src="../member_picture/<?= h($post['picture']);?>">
              </a>
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
              <img src="../post_picture/<?= h($post['post_pic']); ?>" alt="postpicture" class="img-thumbnail basePic objectFit">
              <img src="../post_picture/<?= h($post['post_pic']); ?>" alt="postpicture" class="img-thumbnail d-none hidePic objectFit-hide">
              <div class="modal-background d-none" id="modalBg"></div>
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
            <?php require('../app/_parts/_reaction_good.php'); ?>
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
            <a href="userpage.php?id=<?= h($revPost['member_id']); ?>">
              <img class="iconImg img-thumbnail" src="../member_picture/user.png">
            </a>
          <?php else : ?>
            <a href="userpage.php?id=<?= h($revPost['member_id']); ?>">
              <img class="iconImg img-thumbnail" src="../member_picture/<?= h($revPost['picture']);?>">
            </a>
          <?php endif; ?>
        </div>

        <div class="col-auto">
          <span class="font-weight-bold"><?= h($revPost['name']); ?></span>
          <span><small>[<?= h($revPost['created']); ?>]</small></span>
          <?php if ($_SESSION['id'] == $revPost['member_id']) : ?>
            <a class="btn btn-outline-primary btn-sm ml-2 comment-deleteBtn" role="button" data-revpostid="<?= h($revPost['id']); ?>">
            <i class="fas fa-trash mr-2"></i>
          </a>
          <?php endif; ?>
          <p><?= nl2br(makeLink(h($revPost['comment']) ) ); ?></p>
        </div>
      </div>

    </article>
  </main>
  <?php endforeach; ?> <!-- コメントに対してのforeach -->

  <?php endif; ?> <!-- 一つの投稿に対してのendif -->


  <!-- 
    ページネーション
   -->

  <nav class="col-md-10 offset-md-2 page-wrapper" aria-label="ページネーション">
    <ul class="pagination">

      <li class="page-item">
        <a class="page-link mt-5" href="index.php">topに戻る</a>
      </li>

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
  <a href="userpage.php?id=<?= h($member['id']); ?>" class="text-white"><i class="fas fa-user-alt"></i></a>
</nav>

<!-- 
  レスポンシブ投稿ボタン
-->

<a class="btn btn-outline-primary responsive-postButton btn-lg d-none" role="button" data-toggle="modal" data-target="#userPost-modal">
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
