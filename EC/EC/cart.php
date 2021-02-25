<?php

require_once '../EC_include/conf/const.php';
require_once '../EC_include/model/function.php';
$err_msg = [];
$message = '';//成功時用

//セッションスタート
session_start();
// ログイン済みでなければログイン画面に
if (isset($_SESSION['user_id']) !== TRUE) {
   header('location:login.php'); 
   exit;
}

$link = get_db_connect();
//$user_id取得
$user_id = $_SESSION['user_id'];
//現在時刻を取得
$date = date('Y-m-d H:i:s');
//POSTの種類取得
$sql_kind = get_post_data('sql_kind');

if (get_request_method() === 'POST') {
   switch($sql_kind){
   case 'delete_cart':
      $item_id = get_post_data('item_id');
      $sql = 'DELETE FROM EC_cart WHERE user_id = ' . $user_id .' AND item_id = ' . $item_id ;
      // deleteを実行する
      if (mysqli_query($link, $sql) !== TRUE) {
          $err_msg[] = 'EC_cart: deleteエラー:' . $sql;
      } else {
          $message = '削除成功';
      }
   break;
   case 'change_cart':
      $amount = get_post_data('select_amount');
      check_str($amount,'/((^[1-9]$)|(^[1-9][0-9]+$))/',1,11,'購入数');
      if (count($err_msg) === 0){
         $item_id = get_post_data('item_id');
         $sql = 'UPDATE EC_cart SET amount =' . $amount . ', updated_date =\'' . $date . '\' WHERE user_id = ' . $user_id .' AND item_id = ' . $item_id ;
         // updateを実行する
         if (mysqli_query($link, $sql) !== TRUE) {
             $err_msg[] = 'EC_cart updateエラー:' . $sql;
         } else {
             $message = '変更成功';
         }
      }
   break;
   default:
   }
}

//ユーザーのカート情報を取得
$sql = 'SELECT cart.item_id,name,price,amount,img FROM EC_cart as cart';
$sql .= ' left join EC_item as item on cart.item_id = item.id';
$sql .= ' WHERE user_id = '.$user_id ;
$cart_data = get_as_array($link,$sql);
$cart_data = entity_assoc_array($cart_data);

// DB切断
close_db_connect($link);
   
include_once '../EC_include/view/cart.php';

