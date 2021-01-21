// メインのscript.js
'use strict';

// 
// 削除ボタン押下時の確認
// 
{
  // console.log('読み込みチェック');
  $(function(){

    $('.trashbtn').click(function() {
      var postid = $(this).data('postid');
      // console.log(postid);
      var res = window.confirm('本投稿を削除してもよろしいですか？');
      if (res == true) {
        window.location.href = "../app/delete.php?id=" + postid;
      }
    });

    $('.comment-deleteBtn').click(function() {
      var rev_postid = $(this).data('revpostid');
      // console.log(rev_postid);
      var res2 = window.confirm('コメントを削除してもよろしいですか？');
      if (res2 == true) {
        window.location.href = "../app/deleteReview.php?id=" + rev_postid;
      }
    });

  });
}

// 
// input要素でenterを押してもsubmitされないようにする処理
// 
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

  console.log('mainのindex.phpなどに適用するjsを読み込んでいます');
}

{
  // hoge = basePic hoge2 = hidePic hage = modalBg ho = movingCenter
  var basePic = document.getElementsByClassName('basePic');
  var modalBg = document.getElementById('modalBg');
  var hidePic = document.getElementsByClassName('hidePic');
  var body =  document.getElementById('mainBody');
  var objectFit = document.getElementsByClassName('objectFit');

  // 
  // クリックされた要素と同じインデックスのhidePicを表示させる必要がある
  // クリックした要素のインデックス番号を、indexに格納する
  // 

  const basePic2 = document.querySelectorAll(".basePic");
  let index;
  basePic2.forEach((base) => {
    base.addEventListener('click', () => {
      index = [].slice.call(basePic2).indexOf(base);
      console.log(index);
    });
  });

  //
  // 画像クリック時の動作
  //

  for (var i = basePic.length - 1; i >= 0; i--) {
    basePic[i].addEventListener("click", function() {

      // 中心に画像を表示するクラスを付属
      this.classList.toggle('movingCenter');
      
      // modal-backgroundをdisplay blockに
      modalBg.classList.toggle('d-none');
      
      // スクロールを防ぐ
      body.classList.toggle('modal-open');
      
      // objectFitを外す
      this.classList.toggle('objectFit');
      
      // tweetContentsが崩れないよう、元の位置に画像を配置する
      // indexには、クリックした要素のインデックス番号が格納されている
      hidePic[index].classList.toggle('d-none');


    });
  }

  //
  // modal-backgroundをクリック時の動作
  //

  modalBg.addEventListener("click", function() {

    // modal-backgroundを閉じる
    modalBg.classList.add('d-none');

    // bodyのoverflowを取る
    body.classList.remove('modal-open');

    for (var i = basePic.length - 1; i >= 0; i--) {
      // 中央に画像表示のクラスを取り除き、hidePicを隠す
      basePic[i].classList.remove('movingCenter');
      hidePic[i].classList.add('d-none');

      // basePicへのobjectFitを有効にする
      basePic[i].classList.add('objectFit');
    }


  });

}