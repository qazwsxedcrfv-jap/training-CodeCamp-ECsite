<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>商品一覧ページ</title>
  <link type="text/css" rel="stylesheet" href="./css/common.css">
</head>
<body>
  <header>
    <div class="header-box">
      <a href="../../../EC/shopping.php">
        <img class="logo" src="./images/logo.png" alt="CodeSHOP">
      </a>
      <a class="nemu" href="./logout.php">ログアウト</a>
      <a href="../../../EC/cart.php" class="cart"></a>
      <p class="nemu">ユーザー名：<?php print $_SESSION['user_name'] ?></p>
    </div>
  </header>
  <div class="content">
<?php if (count($err_msg) !== 0) { ?>
<?php foreach ($err_msg as $value) {?>
<p class="err-msg"><?php print $value ?></p>; 
<?php } ?>
<?php } else { ?>
<p class="success-msg"><?php print $message ?></p>
<?php } ?>
    <ul class="item-list">
<?php foreach ($item_data as $value) { ?>
      <li>
        <div class="item">
          <form action="../../../EC/shopping.php" method="post">
            <img class="item-img" src="<?php print IMG_DIR.$value['img']; ?>" >
            <div class="item-info">
              <span class="item-name"><?php print $value['name']; ?></span>
              <span class="item-price">￥<?php print $value['price']; ?></span>
            </div>
<?php if ($value['stock'] < 1){ ?>
            <p class="sold-out" >売り切れ</p>
<?php } else { ?>
            <input class="cart-btn" type="submit" value="カートに入れる">
<?php } ?>
            <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
            <input type="hidden" name="sql_kind" value="insert_cart">
          </form>
        </div>
      </li>
<?php } ?>
    </ul>
  </div>
</body>
</html>
