$(function () {
  $('input[type=file]').change(function () {
    let file = $(this).prop('files')[0];

    // 画像以外は処理を停止
    if(!file.type.match('image.*')){
      // クリア
      $(this).val('');
      $('.imgfile').html('');
      return;
    }

    // 画像表示
let reader = new FileReader();
reader.onload = function(){
  let img_src = $('<img>').attr('src', reader.result);
  $('.imgfile').html(img_src);
  $('.imgarea').removeClass('noimage');
}
reader.readAsDataURL(file);
  }
  );
});
