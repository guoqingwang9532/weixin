<?php
//引入类文件
require './wechat.inc.php';
//实例化类，new对象
$wechat = new Wechat();
//调用类的方法
//判断是要执行什么操作，如果是有传输过来的echostr
//就调用验证的方法，否则就调用消息处理的方法
if($_GET["echostr"]){
 $wechat->valid();
}else{
 $wechat->responseMsg();
}

