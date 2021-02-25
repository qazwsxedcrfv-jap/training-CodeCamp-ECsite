<?php
/*
*  ホーム
*/
require_once '../EC_include/conf/const.php';
require_once '../EC_include/model/function.php';
// セッション開始
session_start();
// セッション変数からuser_id取得
if (isset($_SESSION['user_id']) === TRUE) {
   $user_id = $_SESSION['user_id'];
} else {
   header('location:login.php'); 
   exit;
}
// データベース接続
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
   header('location:tool.php'); //相対パス http://でもかける
   exit;
   } else {
   // 一般ユーザは購入ページへ
   header('location:shopping.php'); 
   exit;
   }

} else {
   // ユーザ名が取得できない場合、ログインページへリダイレクト
   header('location:login.php'); 
   exit;
}
