<!-- joinのファンクション -->
<?php
function h($value) {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function makeLink($value) {
return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)" , '<a href="\1\2">\1\2</a>' , $value);
}

// join/web/check.phpに利用
// アイコン画像のリサイズ。
function iconResize($width, $height, $a, $b) {
  //$a = 横幅 $b = 縦幅 100*100にリサイズするなら、100, 100と値を与える

  $newWidth = 0;//新横幅
  $newHeight = 0;//新縦幅
  $w = $a;  //最大横幅
  $h = $b;  //最大縦幅

  if($h < $height && $w < $width){
    if($w < $h){
      $newWidth = $w;
      $newHeight = $height * ($w / $width);
    } else if($h < $w) {
      $newWidth = $width * ($h / $height);
      $newHeight = $h;
    }else{
      if($width < $height){
        $newWidth = $width * ($h / $height);
        $newHeight = $h;
      }else if($height < $width){
        $newWidth = $w;
        $newHeight = $height * ($w / $width);
      }else if($height == $width){
        $newWidth = $w;
        $newHeight = $h;
      }
    }
  }else if($height < $h && $width < $w){
      $newWidth = $width;
      $newHeight = $height;
  }else if($h < $height && $width <= $w){
      $newWidth = $width * ($h / $height);
      $newHeight = $h;
  }else if($height <= $h && $w < $width){
      $newWidth = $w;
      $newHeight = $height * ($w / $width);
  }else if($height == $h && $width < $w){
      $newWidth = $width * ($h / $height);
      $newHeight = $h;
  }else if($height < $h && $width == $w){
      $newWidth = $w;
      $newHeight = $height * ($w / $width);
  }else{
      $newWidth = $width;
      $newHeight = $height;
  }

  $array = array($newWidth, $newHeight);
  return $array;

}

function mt_rand_except( int $min, int $max, int $except ) : int {

  do {
      $num = mt_rand($min, $max);
  } while ($num == $except);

  return $num;
}

?>