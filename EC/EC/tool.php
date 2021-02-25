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
       // 現在時刻を取得
       $date = date('Y-m-d H:i:s');
       //POSTの種類取得
       $sql_kind = get_post_data('sql_kind');

       //商品追加(insert)or在庫数変更(update)or公開ステータス変更(change)or商品削除(delete)で場合分け
       switch($sql_kind){
       case 'insert':
           //ユーザーの新規商品追加入力情報取得
           $name = get_post_data('new_name');
           $price = get_post_data('new_price');
           $stock = get_post_data('new_stock');
           $status = get_post_data('new_status');
           //入力エラーチェック
           check_str($name,'/.*/',1,100,'名前');
           check_str($price,'/^[0-9]+$/',1,11,'値段');
           check_str($stock,'/^[0-9]+$/',1,11,'在庫数');
           check_str($status,'/(0|1)/',1,1,'公開非公開');
           if (count($err_msg) === 0 ){
               $temp = get_post_file('new_img'); //エラーならarray エラーなければimgのファイル名
               if (is_array($temp) === TRUE){
                    foreach($temp as $value){
                        $err_msg[] = $value ;
                    }
               } else {
               $img = $temp; 
               // 更新系の処理を行う前にトランザクション開始(オートコミットをオフ）
               mysqli_autocommit($link, false);
                /**
                * 商品追加情報を挿入
                */
                // 挿入情報をまとめる
                $item_info = [
                  'name' => $name,
                  'price' => $price,
                  'img' => $img,
                  'status' => $status,
                  'created_date' => $date,
                  'updated_date' => $date
                ];
                // insertのSQL
                $sql = 'INSERT INTO EC_item (name, price, img, status, created_date, updated_date)';
                $sql .= ' VALUES(\'' . implode('\',\'', $item_info) . '\')';
                // insertを実行する
                if (insert_db($link, $sql) !== TRUE) {
                   $err_msg[] = 'EC_item: insertエラー:' . $sql;
                }
                // A_Iを取得
                $item_id = mysqli_insert_id($link);
                /**
                在庫情報を挿入
                */
                // 挿入情報をまとめる
                $stock_info = [
                   'item_id' => $item_id,
                   'stock' => $stock,
                   'created_date' => $date,
                   'updated_date' => $date
                ];
                // 在庫情報をinsertする
                $sql = 'INSERT INTO EC_stock (item_id, stock, created_date, updated_date) VALUES(\'' . implode('\',\'', $stock_info) . '\')';
                // insertを実行する
                if (insert_db($link, $sql) !== TRUE) {
                   $err_msg[] = 'EC_stock: insertエラー:' . $sql;
                }
                // トランザクション成否判定
                if (count($err_msg) === 0) {
                   // 処理確定
                   mysqli_commit($link);
                   $message = '追加成功';
                } else {
                   // 処理取消
                   mysqli_rollback($link);
                }
              }
            }
             break;
       case 'update':
           //updateする内容取得
           $item_id = get_post_data('item_id');
           $stock = get_post_data('update_stock');
           //入力エラーチェック
           check_str($stock,'/^[0-9]+$/',1,11,'在庫数');
           if (count($err_msg) === 0) {
            // 在庫情報をupdateする
            $sql = 'UPDATE EC_stock SET stock =' . $stock . ', updated_date= \'' . $date . '\' WHERE item_id=' . $item_id ;
            // updateを実行する
            if (mysqli_query($link, $sql) !== TRUE) {
                $err_msg[] = 'drink_inventory_table: updateエラー:' . $sql;
            } else {
                $message = '変更成功';
            }
           }
           break;
       case 'change':
           //changeする内容取得
           $id = get_post_data('item_id');
           $status = get_post_data('change_status');
           check_str($status,'/(0|1)/',1,1,'公開非公開');
           if (count($err_msg) === 0){
           // 在庫情報をupdateする
            $sql = 'UPDATE EC_item SET status =' . $status . ', updated_date =\'' . $date . '\' WHERE id=' . $id ;
            // updateを実行する
            if (mysqli_query($link, $sql) !== TRUE) {
                $err_msg[] = 'drink_info_table: updateエラー:' . $sql;
            } else {
                $message = '変更成功';
            }
           }
           break;
        case 'delete':
           //deleteする内容取得
           $id = get_post_data('item_id');
            // 更新系の処理を行う前にトランザクション開始(オートコミットをオフ）
            mysqli_autocommit($link, false);
            
            $sql = 'DELETE FROM EC_stock WHERE item_id=' . $id ;
            // deleteを実行する
            if (mysqli_query($link, $sql) !== TRUE) {
                $err_msg[] =  'EC_stock: deleteエラー:' . $sql;
            }
            
            $sql = 'DELETE FROM EC_item WHERE id=' . $id ;
            // deleteを実行する
            if (mysqli_query($link, $sql) !== TRUE) {
                $err_msg[] =  'EC_item: deleteエラー:' . $sql;
            }

            // トランザクション成否判定
            if (count($err_msg) === 0) {
               // 処理確定
               mysqli_commit($link);
               $message = '削除成功';
            } else {
               // 処理取消
               mysqli_rollback($link);
            }
            break;
       default:
       }
}


//商品情報を取得
$sql = 'SELECT * FROM EC_item as item';
$sql .= ' left join EC_stock as stock on item.id=stock.item_id';
$item_data = get_as_array($link,$sql);
$item_data = entity_assoc_array($item_data);
// DB切断
close_db_connect($link);


// ログイン済みユーザのホームページ表示
include_once '../EC_include/view/tool.php';