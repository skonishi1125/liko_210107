<!-- _checkLogin -->
<?php
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time() ) {
  $_SESSION['time'] = time();
  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));

  $member = $members->fetch();
  // loginでmemberを識別するidをsessionに入れることで、他のファイルでも使用できるようにする
} else {
  header('Location: https://liko.link/web/login.php');
  exit();
}
?>