<?php
session_start();

// 2階層上のappフォルダに取りに行く
require('../../app/dbconnect.php');

require('../app/functions.php');

if(!empty($_POST)){
  //フォームが空でなかった場合の処理
  if($_POST['name'] == ''){
    $error['name'] = 'blank';
  }
  if($_POST['email'] == ''){
    $error['email'] = 'blank';
  }
  if(strlen($_POST['password']) < 4 ){
    $error['password'] = 'length';
  }
  if($_POST['password'] == ''){
    $error['password'] = 'blank';
  }

  //画像受け渡し。$_FILESという特別な変数を利用する
  //$_FILE['formでつけたname=の名前']['元々用意された名前name,type,sizeなど']を指定できる
  $fileName = $_FILES['image']['name'];
  if(!empty($fileName)) {
    $ext = substr($fileName, -4);
    if($ext != '.jpg' && $ext !='.png' && $ext !='.PNG' && 
    $ext !='.gif' && $ext != 'JPEG' && $ext != 'jpeg' && $ext != '.JPG') {
      $error['image'] = 'type';
    }

  }

  //$errorに何もない場合(重複の時！)
  if(empty($error)){
    $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
    $member->execute(array($_POST['email']));
    $record = $member->fetch();
    if($record['cnt'] > 0){
      $error['email'] = 'duplicate';
    }
  }

  //$errorに何もない場合、エラーがなかった場合(checkへと進む処理)
  if(empty($error)){
    //画像アップ
    $postImgTime = date('YmdHis');
    if($ext == 'jpeg' || $ext == 'JPEG'){
      $ext = '.' . $ext;
    }
    $image = $postImgTime . sha1($_FILES['image']['name']).$ext;

    //ファイル名を分からなくする処理
    move_uploaded_file($_FILES['image']['tmp_name'], '../../member_picture/'.$image);
    $_SESSION['join'] = $_POST;
    $_SESSION['join']['image'] = $image;
    $_SESSION['join']['time'] = $postImgTime;
    //セッションにPOSTの値を保存して、次の画面へ
    //imageはパス用の名前を保存、timeは画像の有無判定に使用する
    header('Location: http://localhost:8888/liko_201223/join/web/check.php');
    exit();
  }
}

// 書き直しの処理
if ($_REQUEST['action'] == 'rewrite') {
  $_POST = $_SESSION['join'];
  $back['rewrite'] = true;
} else {
  $back['rewrite'] = false;
}



include('../app/_parts/_header.php');

?>

<!-- HTML
--------------------------------------->


<!-- 
入力エラー時に出現させるモーダル
 -->
 <?php if (!empty($error)) : ?>
  
  <div class="modal-bgBlack" id="modalBackGround"></div>
  <nav class="modal-container alert alert-light alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close" id="closeModal">
      <span aria-hidden="true">&times;</span>
    </button>
    <h5 class="alert-heading modal-title pb-2 mb-2">入力に不備がありました。</h5>
    <!-- 名前エラー -->
    <?php if ($error['name'] == 'blank') : ?>
      <p>・ユーザーネームを入力してください。</p>
    <?php endif; ?>
    <!-- メールエラー -->
    <?php if ($error['email'] == 'blank') : ?>
      <p>・メールアドレスを入力してください。</p>
    <?php endif; ?>
    <?php if ($error['email'] == 'duplicate') : ?>
      <p>
        ・既に使用されているメールアドレスです。<br>
        　重複して登録することはできません。
      </p>
    <?php endif; ?>
    <!-- パスワード(４文字以下)エラー -->
    <?php if ($error['password'] == 'length') : ?>
      <p>・パスワードは4文字以上としてください。</p>
    <?php endif; ?>
    <!-- パスワード未記入エラー -->
    <?php if ($error['password'] == 'blank') : ?>
      <p>・パスワードを入力してください。</p>
    <?php endif; ?>
    <!-- 拡張子エラー -->
    <?php if ($error['image'] == 'type') : ?>
      <p>
        ・拡張子「<?php echo $ext; ?>」のファイルが指定されています。<br>
        　本サービスのアイコン画像は、「.jpg」「.png」「.gif」ファイルのみの対応となります。
        </p>
    <?php endif; ?>
    </div>
  </nav>

  <?php endif; ?> <!-- if (!empty($error)) -->
  
  


<?php $random = mt_rand(1,4); ?>

<header>
  <div class="row header-wrapper" style="background-image: url('../img/head<?php echo $random ?>.png')">
    <div class="header-wrapperLogo">
      <img src="../img/whiteLogo.png" alt="Liko" >
      <p>スキを共有しましょう</p>
    </div>
  </div>

  <div class="row header-wrapperRegister">

    <form class="mt-2 ml-3" action="" method="post" enctype="multipart/form-data">

      <h5 class="py-2"><b>登録しよう</b></h5>

      <!-- ユーザー名 -->
      <div class="form-group">
        <label for="name"><b>ユーザー名</b></label>
        <input name="name" id="name" type="text" class="form-control" placeholder="Name" value="<?= h($_POST['name']); ?>">
      </div>

      <!-- メールアドレス -->
      <div class="form-group">
        <label for="email"><b>メールアドレス</b></label>
        <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="sample@gmail.com" value=" <?= h($_POST['email']); ?>">
        <small id="emailHelp" class="form-text text-muted mb-2">ログイン情報としてのみ利用します。</small>
        </div>

      <!-- パスワード -->
      <div class="form-group">
        <label for="password"><b>パスワード</b></label>
        <input name="password" type="password" class="form-control" id="password" aria-describedby="passHelp" placeholder="****" value="<?= h($_POST['password']); ?>">
        <small id="passHelp" class="form-text text-muted">4文字以上としてください。</small>
      </div>

      <!-- アイコン画像 -->
      <div class="form-group">
        <span class="badge badge-primary">任意</span>
        <label for="image"><b>アイコン画像を選択</b></label>
        <input name="image" type="file" class="form-control-file" id="image" aria-describedby="imageHelp">
        <small id="imageHelp" class="form-text text-muted">
          登録後に再設定が可能です。<br>
          未記入の場合、デフォルト画像が設定されます。<br>
          画像の拡張子は「.jpg」「.png」「.gif」が設定可能です。
        </small>
        <!-- 書き直し処理時の注意事項 -->
        <?php if ($back['rewrite'] == true || !empty($error) ) : ?>
          <small class="text-danger">※恐れ入りますが、画像を改めて指定してください</small>
        <?php endif; ?>

      </div>

      <button type="submit" class="btn btn-primary btn-sm float-right">入力内容を確認する</button>

    </div>

    </form>

  </div> <!-- row header-wrapperRegister -->
</header>


<!-- レスポンシブヘッダーバー -->
<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top resHeader-bar">
  <a href="index.php">
    <img src="../img/whiteLogo.png" alt="Liko" class="header-barLogo ml-4 py-1">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#header-menus" aria-expanded="false" aria-label="切り替え">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="header-menus">
    <ul class="navbar-nav mr-auto navbar-contents">
      <li>
        <a href="" role="button" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-user-plus"></i>登録する</a>
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

<!-- 
登録するをタップした際に出るモーダル
 -->

<nav class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title resRegister-modal-title" id="exampleModalLabel">ユーザー登録</h5>
        <!-- 閉じるアイコン -->
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <!-- モーダル 本文 -->
      <div class="modal-body">

        <form class="mt-2 ml-3" action="" method="post" enctype="multipart/form-data">

          <!-- ユーザー名 -->
          <div class="form-group">
            <label for="name"><b>ユーザー名</b></label>
            <input name="name" id="name" type="text" class="form-control" placeholder="Name" value="<?= h($_POST['name']); ?>">
          </div>

          <!-- メールアドレス -->
          <div class="form-group">
            <label for="email"><b>メールアドレス</b></label>
            <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="sample@gmail.com" value=" <?= h($_POST['email']); ?>">
            <small id="emailHelp" class="form-text text-muted mb-2">ログイン情報としてのみ利用します。</small>
            </div>

          <!-- パスワード -->
          <div class="form-group">
            <label for="password"><b>パスワード</b></label>
            <input name="password" type="password" class="form-control" id="password" aria-describedby="passHelp" placeholder="****" value="<?= h($_POST['password']); ?>">
            <small id="passHelp" class="form-text text-muted">4文字以上としてください。</small>
          </div>

          <!-- アイコン画像 -->
          <div class="form-group">
            <span class="badge badge-primary">任意</span>
            <label for="image"><b>アイコン画像を選択</b></label>
            <input name="image" type="file" class="form-control-file" id="image" aria-describedby="imageHelp">
            <small id="imageHelp" class="form-text text-muted">
              登録後に再設定が可能です。<br>
              未記入の場合、デフォルト画像が設定されます。<br>
              画像の拡張子は「.jpg」「.png」「.gif」が設定可能です。
            </small>
            <!-- 書き直し処理時の注意事項 -->
            <?php if ($back['rewrite'] == true || !empty($error) ) : ?>
              <small class="text-danger">※恐れ入りますが、画像を改めて指定してください</small>
            <?php endif; ?>

          </div>

          <button type="submit" class="btn btn-primary btn-sm float-right">入力内容を確認する</button>

        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">閉じる</button>
      </div>
    </div>
  </div>
</nav>

<div class="header-wrapperSpace"></div>
<div class="background-white">
  
  <section class="container intro-container pb-5">
    <div class="intro-centerBar py-4"></div>
    <h4 class="my-3"><b>Likoとは</b></h4>
  
    <div class="row">
      
      <div class="col-md-8">
        <img src="../img/intro1.png" alt="intro1">
      </div>
  
      <div class="col-md-4 intro-containerMessages">
        <p>Likoでは様々な人たちが「スキ」だと感じたことについての投稿を確認できます。</p>
        <p>自分の好きなものを投稿して、感想を語り合うことも可能です。</p>
        <p>好きな場所や動画、食べ物だけでなくキャラクターや有名人など、投稿する内容はユーザーの自由です。<br>プチブログのような使い方も。</p>
      </div>
  
    </div>
  
  </section>


  
  <section class="intro-container pb-5 intro-containerBg" style="background-image: url('../img/head<?php print ($random); ?>.png')">
  <!-- <section class="intro-container pb-5 intro-containerBg bg-switcher"> -->
    <div class="intro-centerBar py-4 border-white"></div>
    <h4 class="my-3"><b>できること</b></h4>
  
    <div class="container">
      
      <div class="row">
        
        <div class="col-md-4 intro-containerMessages">
          <p>自分の好みの投稿に「いいね！」したり、コメントをつけることができます。</p>
          <p>また、自分と同じものが好きな人を探すことができる検索機能、プロフィールアイコンの変更機能なども。</p>
          <br>
          <p>自分の好きなものについて、ユーザーと語り合いましょう！</p>
        </div>
    
        <div class="col-md-8">
          <img src="../img/intro2.png" alt="intro1">
        </div>
  
      </div>
  
    </div>
  
  </section>
  
  <section class="container intro-container pb-5">
    <div class="intro-centerBar py-4"></div>
    <h4 class="my-3"><b>FAQ</b></h4>
  
    <section id="faq">
  
      <div class="intro-containerFAQ col-md-12">
        <h5 data-toggle="collapse" href="#collapseContent01" role="button" aria-expanded="false" aria-controls="collapseContent01" id="toggleIcon1" class="py-3">
          <b class="ml-3">このサイトについて</b>
          <i class="fas fa-plus mr-3" id="plus1"></i>
          <i class="fas fa-minus mr-3 skelton" id="minus1"></i>
        </h5>
  
        <div class="collapse" id="collapseContent01">
          <div class="card card-body my-4">
            <p>ポートフォリオ用のSNSサイトとなります。</p>
            <p>突然メンテナンスを行ったり、サービスが停止することがございます。ご了承ください。</p>
          </div>
        </div>
      </div>
  
      <h2 style="color: white" >　</h2>
    
      <div class="intro-containerFAQ col-md-12">
        <h5 data-toggle="collapse" href="#collapseContent02" role="button" aria-expanded="false" aria-controls="collapseContent02" id="toggleIcon2" class="py-3">
          <b class="ml-3">情報の取扱い</b>
          <i class="fas fa-plus mr-3" id="plus2"></i>
          <i class="fas fa-minus mr-3 skelton" id="minus2"></i>
        </h5>
  
        <div class="collapse" id="collapseContent02">
          <div class="card card-body my-4">
            <p>
             メールアドレスについては管理者から確認が可能です。<br>
             メールアドレスはユーザーのログインのみにしか利用せず、管理者がその他の用途に利用することはありません。
            </p>
             <p>
              パスワードはハッシュ関数「sha256」を利用した暗号化形式を採用し保存しています。<br>
              管理者から確認することはできないような仕組みで管理しています。
            </p>
            <p>
            自分のメールアドレスなどを利用せずにサービスを利用したい場合は、架空のアドレスを登録することでもサービスの利用が可能です。<br>
            (ログイン時に利用しますので、忘れないように注意してください)
            </p>
          </div>
        </div>
  
      </div>
  
      <h2 style="color: white" >　</h2>
    
      <div class="intro-containerFAQ col-md-12">
        <h5 data-toggle="collapse" href="#collapseContent03" role="button" aria-expanded="false" aria-controls="collapseContent03" id="toggleIcon3" class="py-3">
          <b class="ml-3">不具合が発生した時</b>
          <i class="fas fa-plus mr-3" id="plus3"></i>
          <i class="fas fa-minus mr-3 skelton" id="minus3"></i>
        </h5>
  
        <div class="collapse" id="collapseContent03">
          <div class="card card-body my-4">
            <p>下記のアドレスまで伝えて頂ければ幸いです。確認が出来次第、返信いたします。</p>
            <p>email : skonishi1125@gmail.com</p>
          </div>
        </div>
  
      </div>
  
      <h2 style="color: white" >　</h2>
  
    </section>
  
  </section>
  
  
  
  <section class="container intro-container pb-5">
    <div class="intro-centerBar py-4"></div>
    <h4 class="my-3"><b>はじめてみよう</b></h4>
  
    <div class="row pt-4">
      <div class="col-md-4 noResponsive-registerbutton">
        <a href="index.php" class="btn btn-primary btn-sm getStartButtons" role="button"><i class="fas mr-1 fa-user-plus"></i>登録する</a>
      </div>
  
      <!-- 画面幅767px以下の時に出るボタン -->
      <div class="col-md-4 responsive-registerbutton">
        <a href="" role="button" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary btn-sm getStartButtons" role="button"><i class="fas mr-1 fa-user-plus"></i>登録する</a>
      </div>
  
      <div class="col-md-4">
        <a class="btn btn-primary btn-sm getStartButtons" role="button" href="../../web/login.php"><i class="fas mr-1 fa-sign-in-alt"></i>ログインする</a>
      </div>
  
      <div class="col-md-4">
        <a class="btn btn-success btn-sm getStartButtons" role="button" href="../../app/testLogin.php"><i class="fas mr-1 fa-sign-in-alt"></i>お試しログイン</a>
      </div>
  
    </div>
  
    <div class="row mt-5 getStartNote">
      <div class="col-md-12">
        <h6 class="pb-2">お試しログイン機能について</h6>
        <p>ユーザー登録作業をスキップし、テストアカウントでLikoへログインすることができます。</p>
        <p>Likoの仕組みを確認したい場合はこちらでのログインをお試しください。</p>
      </div>
  
    </div>
    
  </section>
  
  <section class="container intro-container pb-5">
    <div class="intro-centerBar py-4"></div>
    <h4 class="my-3"><b>製作者プロフィール</b></h4>
  
    <div class="row profile pt-3">
      <div class="col-md-6">
        <img class="img-thumbnail" src="../img/profile.png" alt="profile">
      </div>
  
      <div class="col-md-6 profileTexts mt-3">
        <h6>名前</h6>
        <p>小西 慧(Satoru Konishi)<br>
        1996年11月25日生まれ (2020年11月現在:24歳) </p>
        <h6>趣味</h6>
        <p>イラスト描画、ランニング、eSports</p>
        <h6>SNS</h6>
        <ul>
            <li>
              <p>Qiita<br>
              <a href="https://qiita.com/skonishi1125">https://qiita.com/skonishi1125</a></p>
            </li>
            <li>
              <p>Github<br>
              <a href="https://github.com/skonishi1125/liko_re">https://github.com/skonishi1125/liko_re</a></p>
            </li>
          </ul>
      </div>
      
    </div>
  
  </section>
  
  
  
  
  <footer class="py-2">
    <div class="footer-logo">
      <img src="../img/whiteLogo.png" alt="liko" class="mt-2">
    </div>
    <div class="footer-container">
      <p>2020-2021 ©︎Satoru Konishi.</p>
    </div>
  </footer>

</div> <!-- background-white  -->


<?php
include('../app/_parts/_footer.php');
?>