$(function () {

  // jQueryを利用している。

  // inputタグのtype属性が『file』のもの。
  // 『change』イベント
  // <input>, <select>, <textarea> 要素において、ユーザーによる要素の値の変更が確定したときに発行される。
  $('input[type=file]').change(function () {



    // $(this)　⇒　('input[type=file]')を指している。全く同じ。オブジェクトになり、色々なkeyと値を持っている。

    // 「prop()」⇒　指定したオブジェクトのプロパティを取得してくる、jQueryに予め用意されているメソッド。

    // ※HTML要素に付与されている「id・class・name」…などの属性や、「checked・selected」…などのプロパティを取得。
    // $(this)というオブジェクトに保存されている「files」というプロパティを取得してきてください、という命令になる。

    // 「files」　⇒　アップロードしたファイルの詳細な情報を持つプロパティ。

    // 要約すると、「アップロードしたファイルを取得してきて、『変数file』に保存します」ということを行っている。
    const file = $(this).prop('files')[0];

    // 画像以外は処理を停止
    if (!file.type.match('image.*')) {
      // クリア
      $(this).val('');
      $('.imgfile').html('');
      return;
    }

    // 画像表示
    // 「FileReader」　⇒　ブラウザ上でファイルを操作できるjQueryで用意されたクラス。
    const reader = new FileReader();

    //  reader.onload ⇒　ブラウザ上でファイルがアップロードされてきたら。
    reader.onload = function () {



      // htmlの生成
      // $('<img>')　⇒　<img src="">の生成。

      // 『attr(第一引数、第二引数)』　⇒　指定したタグに指定した属性の追加（jQueryのメソッド）
      // 第一引数:追加したい属性を記述。　ここでは「'src'」（ソース属性）
      // 第二引数:その属性の値を記述。　ここでは「reader.result」（表示したい画像）
      const img_src = $('<img>').attr('src', reader.result);

      $('.imgfile').html(img_src);


      $('.imgarea').removeClass('noimage');
    }
    reader.readAsDataURL(file);
  }
  );

  $('.fav__btn').on('click',function(){
    const origin = location.origin;
    const $favbtn = $(this);
    const $threadid = $favbtn.parent().parent().data('threadid');
    const $myid = $('.prof-show').data('me');
    $.ajax({
      type: 'post',
      ulr: origin + '/public_html/ajax.php',
      data: {
        'thread_id': $threadid,
        'user_id': $myid,
      },
      success: function(data){
        if(data == 1){
          $(favbtn).addClass('active');
        }
        else{
          $(favbtn).removeClass('active');
        }
      }
    });
    return false;
  });
});
