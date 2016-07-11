<?php 
header('Content-Type:text/html;Charset=utf-8');
require 'wechat.inc.php';
$wechar = new Wechat();
$wechar -> getUserDetail();



 ?>