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
if (get_request_method() === 'POST') {
   // セッション変数からuser_id取得
   $user_id = $_SESSION['user_id'];
   // 現在時刻を取得
   $date = date('Y-m-d H:i:s');
   //POSTの種類取得
   $sql_kind = get_post_data('sql_kind');
   //ユーザーの購入商品id取得
   $item_id = get_post_data('item_id');
   switch($sql_kind){
   case 'insert_cart':
      //すでに同ユーザーのカート内に商品があるか確認(あればupdate、なければinsert)
      $sql = 'SELECT * FROM EC_cart WHERE user_id = '.$user_id.' AND item_id = '.$item_id;
      $cart_data = get_as_array($link,$sql);
      if (isset($cart_data[0]['id'])) {
         //カート内の商品数update
         $sql = 'UPDATE EC_cart SET amount =' . ($cart_data[0]['amount']+1) . ', updated_date= \'' . $date . '\' WHERE id=' . $cart_data[0]['id'] ;
         // updateを実行する
         if (mysqli_query($link, $sql) !== TRUE) {
             $err_msg[] = 'EC_cart: updateエラー:' . $sql;
         } else {
             $message = 'カートに登録しました。';
         }
      } else {
         /**
         * 商品追加情報を挿入
         */
         // 挿入情報をまとめる
         $cart_info = [
         'user_id' => $user_id,
         'item_id' => $item_id,
         'amount' => 1,
         'created_date' => $date,
         'updated_date' => $date
          ];
         // insertのSQL
         $sql = 'INSERT INTO EC_cart(user_id, item_id, amount, created_date, updated_date)';
         $sql .= ' VALUES(\'' . implode('\',\'', $cart_info) . '\')';
         // insertを実行する
         if (insert_db($link, $sql) !== TRUE) {
          $err_msg[] =  'EC_cart: insertエラー:' . $sql;
         } else {
             $message = 'カートに登録しました。';
         }
      }
   break;
   default:
   }
}

//商品情報を取得(非表示以外)
$sql = 'SELECT * FROM EC_item as item';
$sql .= ' left join EC_stock as stock on item.id=stock.item_id WHERE status = 1';
$item_data = get_as_array($link,$sql);
$item_data = entity_assoc_array($item_data);
// DB切断
close_db_connect($link);
   
include_once '../EC_include/view/shopping.php';

