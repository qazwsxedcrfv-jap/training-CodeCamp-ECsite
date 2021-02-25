<?php
/*
*  ログイン済みユーザのホームページ
*/
require_once '../EC_include/conf/const.php';
require_once '../EC_include/model/function.php';

//セッションスタート
session_start();
// ログイン済みでなければログイン画面に
if (isset($_SESSION['user_id']) !== TRUE) {
   header('location:login.php'); 
   exit;
}

$link = get_db_connect();

//ユーザー情報を取得
$sql = 'SELECT * FROM EC_user';
$user_data = get_as_array($link,$sql);
$user_data = entity_assoc_array($user_data);
// DB切断
close_db_connect($link);


// ログイン済みユーザのホームページ表示
include_once '../EC_include/view/tool_user.php';