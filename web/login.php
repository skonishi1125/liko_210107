<?php
session_start();
require('../app/dbconnect.php');
require('../app/functions.php');

// 自動ログイン処理
// 空の入力値のハッシュ
// e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855
if ($_COOKIE['sessionid'] != '') {
  $cookieLogin = $db->prepare('SELECT * FROM members WHERE session_id=?');
  $cookieLogin->execute(array($_COOKIE['sessionid']));
  $member = $cookieLogin->fetch();

  $_POST['email'] = $member['email'];
  $_POST['save'] = 'on';

  if ($member) {
    $_SESSION['id'] = $member['id'];
    $_SESSION['time'] = time();
    $sessionid = hash('sha256', $_POST['email']);

    setcookie('sessionid', $sessionid, time() + 60 * 60 * 24 * 14);

    header('Location: http://localhost:8888/liko_201223/web/index.php');
    exit();
  }
}

if (!empty($_POST)) {
  // 両方のフォームが記入されている時
  if ($_POST['email'] != '' && $_POST['password'] != '') {
    $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
    $login->execute(array(
      $_POST['email'],
      hash('sha256',$_POST['password']),
    ));
    $member = $login->fetch();
  
    //ログイン成功
    if ($member) {
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();
      $sessionid = hash('sha256', $_POST['email']);
      //自動ログインボックス有効時 onはinput value="on"で設定したから。
      if($_POST['save'] == 'on'){
        setcookie('sessionid', $sessionid ,time()+60*60*24*14);
        //直で2番目の値にhash関数を入れてはいけない
      }
  
      header('Location: http://localhost:8888/liko_201223/web/index.php');
      exit();
  
      } else {
        $error['login'] = 'failed';
      }
  
  // フォームに空白があった場合
  } else {
    $error['login'] = 'blank';
  }

}



include('../app/_parts/_header.php');

?>

<header class="container-fluid">
  <div class="row header-bar">

    <div class="header-barLogo">
      <a href="../join/web/index.php">
        <img src="../join/img/yellowLogo.png" alt="Liko" class="header-barLogo ml-4 py-1">
      </a>
    </div>

    <ul class="header-barButtons">
      <li>
        <a href="../join/web/index.php" class="btn btn-primary btn-sm" role="button"><i class="fas fa-user-plus"></i>登録する</a>
      </li>
      <li>
        <a class="btn btn-primary btn-sm" role="button" href=""><i class="fas fa-sign-in-alt"></i>ログイン</a>
      </li>
      <li>
        <a class="btn btn-success btn-sm" role="button" href="../app/testLogin.php"><i class="fas fa-sign-in-alt"></i>お試しログイン</a>
      </li>
    </ul>

  </div> <!-- row header-bar -->
</header>

<!-- レスポンシブヘッダーバー -->
<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top resHeader-bar login_thanks_checkbar">
  <a href="../join/web/index.php">
    <img src="../join/img/whiteLogo.png" alt="Liko" class="header-barLogo ml-4 py-1">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#header-menus" aria-expanded="false" aria-label="切り替え">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="header-menus">
    <ul class="navbar-nav mr-auto navbar-contents">
      <li>
        <a href="../join/web/index.php"><i class="fas fa-user-plus"></i>登録する</a>
      </li>

      <li>
        <a href=""><i class="fas fa-sign-in-alt"></i>ログインする</a>
      </li>

      <li>
        <a href="../app/testLogin.php"><i class="fas fa-sign-in-alt testLogin"></i>お試しログイン</a>
      </li>
    </ul>
  </div>
</nav>


<section class="container check-wrapper pb-5">
  <h5 class="py-3 check-wrapperTitle">ログインする</h5>

  <div class="check-form">
    <form action="" method="post">
      <div class="form-group">
        <label for="email"><b>メールアドレス</b></label>
        <input name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="sample@gmail.com" value=" <?= h($_POST['email']);?>" >
      </div>

      <div class="form-group">
        <label for="password"><b>パスワード</b></label>
        <input name="password" type="password" class="form-control" id="password" aria-describedby="passHelp" placeholder="****" value="<?= h($_POST['password']);?>">
        <small id="passHelp" class="form-text text-muted">パスワードは4文字以上です。</small>
      </div>

      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="save" name="save" value="on">
        <label for="save" class="form-check-label">次回から自動でログインする</label>
      </div>

      <?php if($error['login'] == 'blank'): ?>
        <small class="text-danger">無記入の項目があります。正しく記入してください</small>
      <?php endif; ?>
      <?php if($error['login'] == 'failed'): ?>
        <small class="text-danger">メールアドレスかパスワードに誤りがあります。</small>
      <?php endif; ?>


      <button class="btn btn-primary btn-sm float-right" type="submit">ログイン</button>

    </form>
  </div>

</section>


<?php
include('../app/_parts/_footer.php');
?>