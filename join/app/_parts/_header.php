<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- bootstrap v4.5 -->
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/style.css?<?= filemtime('../css/style.css'); ?>">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <title>Liko</title>
</head>
<body>
  <header class="container-fluid">
    <div class="row header-bar">

      <div>
        <a href="index.php">
          <img src="../img/yellowLogo.png" alt="Liko" class="header-barLogo ml-4 py-1">
        </a>
      </div>

      <ul class="header-barButtons">
        <li>
          <a href="index.php" class="btn btn-primary btn-sm" role="button"><i class="fas fa-user-plus"></i>登録する</a>
        </li>
        <li>
          <a class="btn btn-primary btn-sm" role="button" href="../../web/login.php"><i class="fas fa-sign-in-alt"></i>ログインする</a>
        </li>
        <li>
          <a class="btn btn-success btn-sm" role="button" href="../../app/testLogin.php"><i class="fas fa-sign-in-alt"></i>お試しログイン</a>
        </li>
      </ul>

    </div> <!-- row header-bar -->
  </header>