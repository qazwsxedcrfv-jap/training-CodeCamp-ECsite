<?php
/**
* global変数にエラーをためる 
* 入力有無、入力内容、文字数、スペースチェック
* @param array  文字列、正規表現、最少文字数、最大文字数、内容
*/
function check_str($str,$preg_match,$len_min,$len_max,$type){
    global $err_msg;
        if ($str === ''){
          $err_msg[] = $type.'を入力してください。'; 
        }
        if (mb_strlen($str) < $len_min || mb_strlen($str) > $len_max ){
          $err_msg[] = $type.'は'.$len_min.'文字以上'.$len_max.'文字以内です。';
        }
        if (preg_match($preg_match, $str) !== 1 ){
          $err_msg[] = $type.'の入力文字が不正です。';
        }
        if (ctype_space($str) === TRUE) {
           $err_msg[] = '文字を入力してください。';
        }
}

/**
* 特殊文字をHTMLエンティティに変換する
* @param str  $str 変換前文字
* @return str 変換後文字
*/
function entity_str($str) {
    return htmlspecialchars($str, ENT_QUOTES, HTML_CHARACTER_SET);
}
 
/**
* 特殊文字をHTMLエンティティに変換する(2次元配列の値)
* @param array  $assoc_array 変換前配列
* @return array 変換後配列
*/
function entity_assoc_array($assoc_array) {
 
    foreach ($assoc_array as $key => $value) {
 
        foreach ($value as $keys => $values) {
            // 特殊文字をHTMLエンティティに変換
            $assoc_array[$key][$keys] = entity_str($values);
        }
 
    }
 
    return $assoc_array;
 
}
 
/**
* DBハンドルを取得
* @return obj $link DBハンドル
*/
function get_db_connect() {
 
    // コネクション取得
    if (!$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME)) {
        die('error: ' . mysqli_connect_error());
    }
 
    // 文字コードセット
    mysqli_set_charset($link, DB_CHARACTER_SET);
 
    return $link;
}
 
/**
* DBとのコネクション切断
* @param obj $link DBハンドル
*/
function close_db_connect($link) {
    // 接続を閉じる
    mysqli_close($link);
}
 
/**
* クエリを実行しその結果を配列で取得する
*
* @param obj  $link DBハンドル
* @param str  $sql SQL文
* @return array 結果配列データ
*/
function get_as_array($link, $sql) {
 
    // 返却用配列
    $data = [];
 
    // クエリを実行する
    if ($result = mysqli_query($link, $sql)) {
 
        if (mysqli_num_rows($result) > 0) {
 
            // １件ずつ取り出す
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
 
        }
 
        // 結果セットを開放
        mysqli_free_result($result);
 
    }
 
    return $data;
 
}
 
/**
* insertを実行する(更新系全般だがinsert以外は面倒なのでfunc使わない)
*
* @param obj $link DBハンドル
* @param str SQL文
* @return bool
*/
function insert_db($link, $sql) {
   // クエリを実行する
   if (mysqli_query($link, $sql) === TRUE) {
       return TRUE;
   } else {
       return FALSE;
   }
}
/**
* リクエストメソッドを取得
* @return str GET/POST/PUTなど
*/
function get_request_method() {
   return $_SERVER['REQUEST_METHOD'];
}
/**
* POSTデータを取得
* @param str $key 配列キー
* @return str POST値
*/
function get_post_data($key) {
   $str = '';
   if (isset($_POST[$key]) === TRUE) {
       $str = $_POST[$key];
   }
   return $str;
}
/**
* POST画像データを取得
* @param str $key 配列キー
* @return str POST値
*/
function get_post_file($key) {
   $str = '';
   $err_msg = [];
   //画像データ取得およびエラーチェック
   //ファイルの存在確認→画像タイプ確認→サイズ確認→アップロードできたか確認
   if (isset($_FILES[$key]) === TRUE && is_uploaded_file($_FILES[$key]['tmp_name']) === true ){
        //一時保存時のファイルのパス(クライアント側[name]だとパスが取得できなかった:()
        $tempfile = $_FILES[$key]['tmp_name'];
        // imgフォルダに本来のファイル名+日時 
        $filename = $_FILES[$key]['name'].date('Y-m-d H:i:s').mt_rand(); 
        //まずファイルの存在を確認し、その後画像形式を確認する
        if(file_exists($tempfile) && $type = exif_imagetype($tempfile)){
            switch($type){
                //jpgの場合
                case IMAGETYPE_JPEG:
                // break;
                //pngの場合
                case IMAGETYPE_PNG:
                $size = getimagesize($tempfile);
                    if ($size["0"]>500 || $size["1"]>500) {
                    $err_msg[] =  '画像ファイルは縦横500px以内のものを指定してください。';
                    } else {
                        if ( move_uploaded_file($tempfile , IMG_DIR.$filename )) {
                    	$str = $filename;
                        } else {
                        $err_msg[] =  'ファイルをアップロードできません。';
                        }
                    }
                break;
                //どれにも該当しない場合
                default:
                $err_msg[] = 'ファイル形式が異なります。画像ファイルはJPEG又はPNGのみ利用可能です。';
            }
        }else{
            $err_msg[] = '画像ファイルではありません（もしくはファイルが存在しません）';
        }
    } else {
        $err_msg[] = '画像を選択してください。';
    }
   if (count($err_msg) === 0) {
       return $str;
   } else {
       return $err_msg;
   }
}



