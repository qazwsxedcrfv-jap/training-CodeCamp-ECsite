<?php
require_once '../EC_include/conf/const.php';
require_once '../EC_include/model/function.php';
// 変数初期化
$user_name = ''; 
$password   = '';
$err_msg = [];
$message = ''; //成功用

// リクエストメソッド取得
$request_method = get_request_method();
if ($request_method === 'POST') {
   // POST値取得
  $user_name = get_post_data('user_name');
  $password   = get_post_data('password');
  // 入力値チェック
  check_str($user_name,'/^[a-zA-Z0-9]+$/',6,10,'ユーザー名');
  check_str($password,'/^[a-zA-Z0-9]+$/',6,10,'パスワード');
   // DB接続
   $link = get_db_connect();
    if (count($err_msg) === 0 ){
       //ユーザー名が既存か確認
       $sql = 'SELECT * FROM EC_user WHERE user_name = \''.$user_name.'\'';
       $data = get_as_array($link,$sql);
       if (count($data) > 0 ){
            $err_msg[] = 'ユーザー名は既に使用されています';
       }
    }
   
    if (count($err_msg) === 0 ){
       // ユーザー追加
       // 挿入情報をまとめる
       $user_info = [
           'user_name' => $user_name,
           'password' => $password,
           'authority' => 'user',
           'created_date' => date('Y-m-d H:i:s'),
           'updated_date' => date('Y-m-d H:i:s')
        ];
       $sql = 'INSERT INTO EC_user (user_name, password, authority, created_date, updated_date) VALUES(\'' . implode('\',\'', $user_info) . '\')';
       if (insert_db($link, $sql) !== TRUE) {
           $err_msg[] = 'INSERT失敗';
       } else {
           // DB切断
           $message = '登録完了';
       }
    }
    close_db_connect($link);
}

    include_once '../EC_include/view/user.php';
?>