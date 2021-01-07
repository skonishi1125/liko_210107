<?php
session_start();
require('../app/functions.php');

if ($_SESSION['thanks'] != 'true') {
  header('Location: http://localhost:8888/liko_201223/join/web/index.php');
  exit();
}

unset($_SESSION['thanks']);

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
        <a href="index.php"><i class="fas fa-user-plus"></i>登録する</a>
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

<section class="container check-wrapper mb-5">
  <h5 class="py-3 check-wrapperTitle">登録が完了しました。</h5>

  <div class="thanksText">
    <p>登録ありがとうございます。 Likoを是非お楽しみください！</p>
    <a class="btn btn-primary btn-sm mb-3" role="button" href="../../web/">
      <i class="fas fa-sign-in-alt mr-2"></i>ログイン画面へ
    </a>
  </div>
 
</section>


<?php
include('../app/_parts/_footer.php');
?>