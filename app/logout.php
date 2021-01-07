<?php
session_start();

$_SESSION = array();
if(ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();

//クッキーの削除
setcookie('sessionid', '', time()-3600);

header('Location: http://localhost:8888/liko_201223/join/web/index.php');
exit();

?>