$(function() {
  // console.log('接続テスト');

  $('.contents-cancelGoodBtn').click(function() {
    let $likediv = $(this).parent("div");
    let ccGB_postid = $(this).data('postid');
    let ccGB_memberid = $(this).data('memberid');
    let $ccGB_goodNumberSpace = $likediv.find('.ccGB_goodNumberSpace');
    let $caGB_goodNumberSpace = $likediv.find('.caGB_goodNumberSpace');
    let $ccGB_btn = $likediv.find('.contents-cancelGoodBtn');
    let $caGB_btn = $likediv.find('.contents-addGoodBtn');

    // console.log(ccGB_postid);
    
    $.post('../app/subGoods.php', {
      postid: ccGB_postid,
      memberid: ccGB_memberid,
    }, function(data){
      // $(this).find("span").html(data);
      // クリックしたaの中の各クラスにデータセット

      $ccGB_goodNumberSpace.html(data);
      $caGB_goodNumberSpace.html(data);
      $ccGB_btn.toggleClass('d-none');
      $caGB_btn.toggleClass('d-none');
    });
  });

  $('.contents-addGoodBtn').click(function() {
    let $likediv = $(this).parent("div");
    let caGB_postid = $(this).data('postid');
    let caGB_memberid = $(this).data('memberid');
    let $ccGB_goodNumberSpace = $likediv.find('.ccGB_goodNumberSpace');
    let $caGB_goodNumberSpace = $likediv.find('.caGB_goodNumberSpace');
    let $ccGB_btn = $likediv.find('.contents-cancelGoodBtn');
    let $caGB_btn = $likediv.find('.contents-addGoodBtn');

    // console.log(caGB_postid);

    $.post('../app/addGoods.php', {
      postid: caGB_postid,
      memberid: caGB_memberid,
    }, function(data){
      $ccGB_goodNumberSpace.html(data);
      $caGB_goodNumberSpace.html(data);
      $ccGB_btn.toggleClass('d-none');
      $caGB_btn.toggleClass('d-none');
    });
  });

});