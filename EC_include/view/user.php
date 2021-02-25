
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ユーザ登録ページ</title>
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
<?php } else { ?>
<p class="success-msg"><?php print $message ?></p>
<?php } ?>
  <div class="content">
  <php>
    <div class="register">
      <form method="post" action="../../../EC/user.php">
        <div>ユーザー名：<input type="text" size="30" name="user_name" placeholder="ユーザー名(半角英数字6字以上10字以下)"></div>
        <div>パスワード：<input type="password" size="30" name="password" placeholder="パスワード(半角英数字6字以上10字以下)">
        <div><input type="submit" value="ユーザーを新規作成する">
      </form>
      <div class="login-link"><a href="../../../EC/login.php">ログインページに移動する</a></div>
    </div>
  </div>
</body>
</html>