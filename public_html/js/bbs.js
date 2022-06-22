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


  // 処理に必要なDOMの取得
  $('.fav__btn').on('click', function () {

    // 「location.origin」　⇒　jQueryのメソッド。プロトコルやポートを含めたURLを取得する。
    // ここでは「http://localhost」まで取得している。
    var origin = location.origin;


    // 「$(this)」　⇒　イベントの引き金になっているもの。★データ取得専用の変数★　ここでは('.fav__btn')の部分。
    var $favbtn = $(this);

    // 『data()』　⇒　とあるデータについているカスタムデータ属性の値を取得する。()の中が「どのデータか」を表している。「data-」の先の部分だけ指定（ここではthreadid）すれば良い。（data-threadid）は値として挿入できない。
    var $threadid = $favbtn.parent().parent().data('threadid');

    // 「data」　⇒　「data-me」のカスタム属性に紐づいた値を取得。
    var $myid = $('.prof-show').data('me');






    // --------------------------------------------------------------
    // ajax処理
    //画像の遷移のない通信を「非同期通信」と言います。
    // 同期処理は一瞬画面が白くなって、画面を切り替わることを言います。
    // --------------------------------------------------------------




    // 「$.ajax」以降がajax処理。
    $.ajax({

      // 送信方法の指定。今回は「post」。※データを隠しながら送信する方法。
      type: 'post',

      // 送信先の指定。どのファイルに対してajaxのデータを送信するか。
      // 今回は「http://localhost/bbs/public_html/ajax.php」に送信。変数「origin」には「http://localhost」までのURLが入っている。
      // ☆☆　また、「bbs/public_html/ajax.php」にあるJSON形式のファイルは、『ajax処理で操作できる』。
      url: origin + '/bbs/public_html/ajax.php',

      // 実際に渡すデータ
      data: {
        // シングルクォーテーション(')で囲まれているもの＝key
        // どのユーザーがどの情報を
        'thread_id': $threadid,
        'user_id': $myid,
      },


      // 「success」　⇒　ajax通信がうまく言ったら、「success」の中身を実行してください、という処理。
      // JSONのデータがajax処理の(data)に保存される。
      success: function (data) {



        // バックエンドで作った値をフロントエンドで扱う場合は、必ずJSON形式に直してから処理を実行する。
        // その為、複雑な書き方になっている。
        // （※AJAX送信の非同期処理を行いたいからこうなっている。（UXの観点から））

        // ajax．phpで「$res」の中身がjson形式に変換され、ここで使用されている。
        if (data == 1) {
          // 「$fav_flag = 1」なら（INSERT文でお気に入りテーブルに情報が登録されたら）、active属性を付与する。（CSSで装飾する（★を黄色にする））
          $($favbtn).addClass('active');
        }
        else {
          $($favbtn).removeClass('active');
        }
      }
    });
    return false;
  });
});
