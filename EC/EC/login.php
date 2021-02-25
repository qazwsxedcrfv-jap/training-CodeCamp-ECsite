<?php
/*
*  ログイン処理
*/
require_once '../EC_include/conf/const.php';
require_once '../EC_include/model/function.php';
$err_msg = [];

// セッション開始
session_start();

//ログイン済みか確認
if (isset($_SESSION['user_id']) === TRUE) {
$user_id = $_SESSION['user_id'];
$link = get_db_connect();
// user_idからユーザ名とauthorityを取得するSQL
$sql = 'SELECT user_name, authority FROM EC_user WHERE id = ' . $user_id;
// SQL実行し登録データを配列で取得
$data = get_as_array($link, $sql);
// データベース切断
close_db_connect($link);
// ユーザ名を取得できたか確認
   if (isset($data[0]['user_name'])) {
      $user_name = $data[0]['user_name'];
      // adminは管理ページへ
      if ($data[0]['authority'] === 'admin'){
      header('location:tool.php'); 
      exit;
      } else {
      // 一般ユーザは購入ページへ
      header('location:shopping.php'); 
      exit;
      }
   }
}

//ログインしていないが、リクエストがあるか確認
// リクエストメソッド確認
if (get_request_method() === 'POST') {
   // POST値取得
   $user_name  = get_post_data('user_name');  // ユーザー名
   $password = get_post_data('password'); // パスワード
   // メールアドレスをCookieへ保存
   setcookie('user_name', $user_name, time() + 60 * 60 * 24 * 365);
   // データベース接続
   $link = get_db_connect();
   // メールアドレスとパスワードからuser_idとauthorityを取得するSQL
   $sql = 'SELECT id, user_name, authority FROM EC_user
          WHERE user_name =\'' . $user_name . '\' AND password =\'' . $password . '\'';
   // SQL実行し登録データを配列で取得
   $data = get_as_array($link, $sql);
   // データベース切断
   close_db_connect($link);
   // 登録データを取得できたか確認
   if (isset($data[0]['id'])) {
      // セッション変数にuser_id,user_nameを保存
      $_SESSION['user_id'] = $data[0]['id'];
      $_SESSION['user_name'] = $data[0]['user_name'];
      // adminは管理ページへ
      if ($data[0]['authority'] === 'admin'){
      header('location:tool.php');
      exit;
      } else {
      // 一般ユーザは購入ページへ
      header('location:shopping.php'); 
      exit;
      }
   } else {
      // セッション変数にログインのエラーフラグを保存
      $err_msg[] = 'ユーザー名かパスワードが誤っています。';
      $_SESSION['login_err_flag'] = TRUE;
   }
}
//ログインもしていなくて、POSTもしていなのであれば、ログイン画面表示
include_once '../EC_include/view/login.php';
