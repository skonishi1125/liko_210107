'use strict';

$(function(){
  $('.faqOpenBtn').click(function() {
    let $plus = $(this).find('.fa-plus');
    let $minus = $(this).find('.fa-minus');

    $plus.toggleClass('d-none');
    $minus.toggleClass('d-none');
  });

  $('#closeModal').click(function() {
    $('#modalBackGround').addClass('d-none');
  });

});
