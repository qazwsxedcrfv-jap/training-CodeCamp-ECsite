
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>購入完了ページ</title>
  <link type="text/css" rel="stylesheet" href="./css/common.css">
</head>
<body>
  <header>
    <div class="header-box">
      <a href="../../../EC/shopping.php">
        <img class="logo" src="./images/logo.png" alt="CodeCamp SHOP">
      </a>
      <a class="nemu" href="./logout.php">ログアウト</a>
      <a href="../../../EC/cart.php" class="cart"></a>
      <p class="nemu">ユーザー名：<?php print $_SESSION['user_name'] ?></p>
    </div>
  </header>
  <div class="content">
<?php if(count($purchase_data) === 0) { ?>
  <p class="err-msg">商品はありません。</p>
<?php } elseif(count($err_msg) > 0) { ?>
<?php foreach ($err_msg as $value)  { ?>
  <p class="err-msg"><?php print $value ?></p>
<?php } ?>
<?php } else { ?>
    <div class="finish-msg">ご購入ありがとうございました。</div>
    <div class="cart-list-title">
      <span class="cart-list-price">価格</span>
      <span class="cart-list-num">数量</span>
    </div>
<!--合計金額計算用変数$sumを初期化-->
<?php $sum = 0?>
<?php foreach ($purchase_data as $value) { ?>
      <ul class="cart-list">
          <img class="cart-item-img" src="<?php print IMG_DIR.$value['img']; ?>">
          <span class="cart-item-name"><?php print $value['name']; ?></span>
          <span class="cart-item-price">¥ <?php print $value['price']; ?></span>
          <span class="form_select_amount"><?php print $value['amount']; ?>個</span>
      </ul>
<!--合計金額はphp上で計算-->
<?php $sum = $sum + ($value['price']*$value['amount'])  ?>
<?php } ?>
    <div class="buy-sum-box">
      <span class="buy-sum-title">合計</span>
      <span class="buy-sum-price">¥<?php print $sum; ?></span>
    </div>
<?php } ?>
  </div>
</body>
</html>
