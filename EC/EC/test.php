<?php
print __FILE__;//絶対パスがわかる
//↓どちらでも可能
require_once '../../../EC_include/conf/const.php'; //相対パス
require_once '/home/enviroment/htdocs/EC_include/conf/const.php';//絶対パス
?>