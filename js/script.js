// メインのscript.js
'use strict';

// input要素でenterを押してもsubmitされないようにする処理
{
  $(function(){
    $("input"). keydown(function(e) {
      if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
        return false;
      } else {
        return true;
      }
    });
  });

  console.log('これはmainのindex.phpなどに適用するjsです');

}