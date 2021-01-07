<!-- メインのファンクション -->
<?php
require('../join/app/functions.php');

function fitContain($resize, $w1, $h1, &$w2, &$h2){
  if ($w1 > $h1){
    $base = $w1;
  } else {
    $base = $h1;
  }
  //リサイズしたいサイズとの縮小比率
  $rate = ($base / $resize);
  if ($rate > 1){
    $w2 = floor((1 / $rate) * $w1);
    $h2 = floor((1 / $rate) * $h1);
  } else {
    $w2 = $w1;
    $h2 = $h1;
  }
}

function imageOrientation($filename, $orientation){
  //画像のロード HEICは自動でjpegになるからjpegだけで良い？
  $image = imagecreatefromjpeg($filename);

  //回転角度
  $degrees = 0;
  switch($orientation){
    case 1:
      return;
    case 8:
      $degrees = 90;
      break;
    case 3:
      $degrees = 180;
      break;
    case 6:
      $degrees = 270;
      break;
    case 2:
      $mode = IMG_FLIP_HORIZONTAL;
      break;
    case 7:
      $degrees = 90;
      $mode = IMG_FLIP_HORIZONTAL;
      break;
    case 4:
      $mode = IMG_FLIP_VERTICAL;
      break;
    case 5:
      $degrees = 270;
      $mode = IMG_FLIP_HORIZONTAL;
      break;
  }
  //反転する
  if (isset($mode)){
    $image = imageflip($image,$mode);
  }

  //回転させる
  if ($degrees > 0){
    $image = imagerotate($image,$degrees,0);
  }

  //保存,メモリ解放
  imagejpeg($image,$filename);
  imagedestroy($image);

}


?>