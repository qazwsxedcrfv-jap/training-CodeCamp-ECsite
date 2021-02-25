
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>商品管理ページ</title>
  <link type="text/css" rel="stylesheet" href="./css/admin.css">
</head>
<body>
  <h1>CodeSHOP 管理ページ</h1>
  <div>
    <a class="nemu" href="./logout.php">ログアウト</a>
    <a href="./tool_user.php">ユーザ管理ページ</a>
  </div>
<?php if (count($err_msg) !== 0) { ?>
<?php foreach ($err_msg as $value) {?>
<p class="err-msg"><?php print $value ?></p>
<?php } ?>
<?php } else { ?>
<p class="success-msg"><?php print $message ?></p>
<?php } ?>
  <section>
    <h2>商品の登録</h2>
    <form method="post"  action="../../../EC/tool.php" enctype="multipart/form-data">
      <div><label>商品名: <input type="text" name="new_name" value=""></label></div>
      <div><label>値　段: <input type="text" name="new_price" value=""></label></div>
      <div><label>個　数: <input type="text" name="new_stock" value=""></label></div>
      <div><label>商品画像:<input type="file" name="new_img"></label></div>
      <div><label>ステータス:
        <select name="new_status">
          <option value="0">非公開</option>
          <option value="1" selected>公開</option>
        </select>
        </label>
      </div>
      <input type="hidden" name="sql_kind" value="insert">
      <div><input type="submit" value="商品を登録する"></div>
    </form>
  </section>
  <section>
    <h2>商品情報の一覧・変更</h2>
    <table>
      <tr>
        <th>商品画像</th>
        <th>商品名</th>
        <th>価　格</th>
        <th>在庫数</th>
        <th>ステータス</th>
        <th>操作</th>
      </tr>
<?php foreach ($item_data as $value) { ?>
<?php if ($value['status'] === '0') { ?>
      <tr class="status_false">
<?php } else { ?>
      <tr>
<?php } ?>
        <form method="post"  action="../../../EC/tool.php">
          <td><img class="img_size" src="<?php print IMG_DIR.$value['img']; ?>"></td>
          <td class="name_width"><?php print $value['name']; ?></td>
          <td class="text_align_right"><?php print $value['price']; ?></td>
          <td><input type="text"  class="input_text_width text_align_right" name="update_stock" value="<?php print $value['stock']; ?>">個&nbsp;&nbsp;<input type="submit" value="変更する"></td>
          <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
          <input type="hidden" name="sql_kind" value="update">
        </form>
        <form method="post"  action="../../../EC/tool.php">
<?php if ($value['status'] === '0') { ?>
          <td><input type="submit" value="非公開 → 公開にする"></td>
          <input type="hidden" name="change_status" value="1">
<?php } else { ?>
          <td><input type="submit" value="公開 → 非公開にする"></td>
          <input type="hidden" name="change_status" value="0">
<?php } ?>
          <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
          <input type="hidden" name="sql_kind" value="change">
        </form>
        <form method="post"  action="../../../EC/tool.php">
          <td><input type="submit" value="削除する"></td>
          <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
          <input type="hidden" name="sql_kind" value="delete">
        </form>
      </tr>
<?php } ?>
    </table>
  </section>
</body>
</html>
