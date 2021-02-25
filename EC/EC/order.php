<?php

require_once '../EC_include/conf/const.php';
require_once '../EC_include/model/function.php';

$err_msg = [];

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

//ユーザーのカート情報を表示用に取得しておく
$sql = 'SELECT cart.item_id,name,price,amount,img FROM EC_cart as cart';
$sql .= ' left join EC_item as item on cart.item_id = item.id';
$sql .= ' WHERE user_id = '.$user_id ;
$purchase_data = get_as_array($link,$sql);
$purchase_data = entity_assoc_array($purchase_data);

//$user_if取得
$user_id = $_SESSION['user_id'];
//現在時刻を取得
$date = date('Y-m-d H:i:s');
//POSTの種類取得
$sql_kind = get_post_data('sql_kind');

if (get_request_method() === 'POST') {
    //ユーザーのカート内商品の在庫数を取得 ついでに在庫あるかと公開しているかチェックもできるように
    $sql = 'SELECT cart.item_id, cart.amount, stock.stock, item.status, item.name FROM EC_cart as cart';
    $sql .= ' left join EC_item as item on cart.item_id = item.id';
    $sql .= ' left join EC_stock as stock on cart.item_id = stock.item_id';
    $sql .= ' WHERE user_id = '.$user_id ;
    $cart_data = get_as_array($link,$sql);
    $cart_data = entity_assoc_array($cart_data);

   // 更新系の処理を行う前にトランザクション開始(オートコミットをオフ）
   mysqli_autocommit($link, false);
    foreach ($cart_data as $value){
        $item_id = $value['item_id'];
        $new_stock = $value['stock']-$value['amount'];
        //在庫あるか 公開しているか エラーチェック
        if ($value['stock'] < 1){
            $err_msg[] = '申し訳ありません。' . $value['name'] . 'はカートに入れた後に売り切れてしまいました。';
        }
        if ($value['status'] !== '1'){
            $err_msg[] = '申し訳ありません。' . $value['name'] . 'はカートに入れた後に非公開商品になってしまいました。';
        }
        // 在庫数updateのSQL
        $sql = 'UPDATE EC_stock SET stock = ' . $new_stock . ', updated_date =\'' . $date . '\' WHERE item_id = '.$item_id;
        if (mysqli_query($link, $sql) !== TRUE) {
            $err_msg[] = 'EC_stock: updateエラー:' . $sql;
        }
    }
   // カート内deleteのSQL
   $sql = 'DELETE FROM EC_cart WHERE user_id = '.$user_id;
   // insertを実行する
   if (mysqli_query($link, $sql) !== TRUE) {
       $err_msg[] = 'EC_cart: deleteエラー:' . $sql;
   }
   // トランザクション成否判定
   if (count($err_msg) === 0) {
       // 処理確定
       mysqli_commit($link);
   } else {
       // 処理取消
       mysqli_rollback($link);
   }
}

// DB切断
close_db_connect($link);
   
include_once '../EC_include/view/order.php';
