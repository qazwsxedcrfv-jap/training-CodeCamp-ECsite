<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ログインページ</title>
  <link type="text/css" rel="stylesheet" href="./css/common.css">
</head>
<body>
  <header>
    <div class="header-box">
      <a href="../../../EC/shopping.php">
        <img class="logo" src="./images/logo.png" alt="CodeSHOP">
      </a>
      <a href="../../../EC/cart.php" class="cart"></a>
    </div>
  </header>
<?php if (count($err_msg) !== 0) { ?>
<?php foreach ($err_msg as $value) {?>
<p class="err-msg"><?php print $value ?></p> 
<?php } ?>
<?php } ?>
  <div class="content">
    <div class="login">
      <form method="post" action="../../../EC/login.php">
        <div><input type="text" name="user_name" placeholder="ユーザー名"></div>
        <div><input type="password" name="password" placeholder="パスワード">
        <div><input type="submit" value="ログイン">
      </form>
      <div class="account-create">
        <a href="../../../EC/user.php">ユーザーの新規作成</a>
      </div>
    </div>
  </div>
</body>
</html>
