<?php 
//放置所有的方法
require './wechat.cfg.php';
//定义一个wechat类，用来存放微信接口请求的一些方法
class Wechat{
  //封装 公有 私有 被保护的
  //封装属性为私有化，更加安全
  private $appId;
  private $appSecret;
  private $token;
  //private $textTpl;
  //构造，创建对象自动调用的一个方法
  public function __construct(){
    //给对象属性赋值，方便后面方法的调用和使用
    $this->appId = APPID;
    $this->appSecret = APPSECRET;
    $this->token = TOKEN;
    $this->textTpl = "<xml>
                      <ToUserName><![CDATA[%s]]></ToUserName>
                      <FromUserName><![CDATA[%s]]></FromUserName>
                      <CreateTime>%s</CreateTime>
                      <MsgType><![CDATA[%s]]></MsgType>
                      <Content><![CDATA[%s]]></Content>
                      <FuncFlag>0</FuncFlag>
                      </xml>";
  	$this->newTpl = '<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>%s</ArticleCount>
					<Articles>%s</Articles>
					</xml>';
	$this->item = '<item>
					<Title><![CDATA[%s]]></Title> 
					<Description><![CDATA[%s]]></Description>
					<PicUrl><![CDATA[%s]]></PicUrl>
					<Url><![CDATA[%s]]></Url>
					</item>';
	 $this->musicTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[music]]></MsgType>
                        <Music>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <MusicUrl><![CDATA[%s]]></MusicUrl>
                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                        </Music>
                        </xml>";
  }
  //微信公众平台认证方法
    public function valid()
      {
          $echoStr = $_GET["echostr"];

          //valid signature , option
          if($this->checkSignature()){
            echo $echoStr;
            exit;
          }
      }
//处理所有的微信相关信息请求和响应的处理
    public function responseMsg()
    {
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                switch ($postObj->MsgType) {
                	case 'text':
                		$this->_doText($postObj);
                		break;
                	case 'image':
                		$this->_doImage($postObj);
                		break;
                	case 'location':
                		$this->_doLocation($postObj);
                		break;
                	case 'event':
                		$this->_doEvent($postObj);
                		break;
                	default:
                		# code...
                		break;
                }

        }
    }
    /**
     * 时间：2016年7月11日19:51:11
     *@author wgq
     * @return 事件时间
     */
    private function _doEvent($postObj)
    {
    	switch ($postObj->Event) {
    		//刚关注时事件推送
    		case 'subscribe':
    			$this->_doSubscribe($postObj);
    			break;
    		case 'unsubscribe':
    			$this->_doUnsubscribe($postObj);
    		case 'CLICK':
    			$this->_doClick($postObj);
    		default:
    			# code...
    			break;
    	}
    }
    /**
     *时间：2016年7月11日19:56:32
     *@author wgq
     * 刚关注触发的事件
     */
    private function _doSubscribe($postObj)
    {
    	
    	$contentStr = '非常感谢你关注我的微信公众账号，有惊喜呦！';
    	$resultStr = sprintf($this->textTpl, $postObj->FromUserName, $postObj->ToUserName, time(), 'text', $contentStr);
        echo $resultStr;
    }
    /**
     *时间：2016年7月11日20:13:20
     *@author wgq
     * 处理用户关注的事情
     */
    private function _doUnsubscribe()
    {

    }
    /**
     * 时间：2016年7月11日20:15:56
     *@author wgq
     * 处理自定义的事件
     */
    private function _doClick($postObj)
    {
    	switch ($postObj->EventKey) {
    		case 'news':
    			$this -> _sendNews($postObj);
    			break;
    		case 'music':
    			$this -> _sendMusic($postObj);
    			break;
    		default:
    			# code...
    			break;
    	}
    }
    /**
     * 时间：2016年7月11日20:19:07
     *@author wgq
     * 推送新闻
     */
    private function _sendNews($postObj)
    {
    	$newsArr = array(
    					 array(
                        'Title' => ' 决战！C罗还差一步加冕欧洲之王！',
                        'Description' => '经过了一个多月的鏖战，由24路诸侯几百名球员联袂出演的法兰西之夏即将迎来最终结局。两张决赛门票一张属于东道主法国，经过了一个多月的鏖战，由24路诸侯几百名球员联袂出演的法兰西之夏即将迎来最终结局。两张决赛门票一张属于东道主法国，另一张则是属于低开高走，常规时间五平一胜杀进决赛的葡萄牙。而两支队伍的一切奋斗与汗水都将在7月11日得到答案。是留下一个伤心的背影，抑或是带走所有的蛋糕。',
                        'PicUrl' => 'http://img1.gtimg.com/sports/pics/hv1/233/90/2096/136315583.jpg',
                        'Url' => 'http://sports.qq.com/fans/post.htm?id=1539363816777711645&mid=142#1',
            ),
            array(
                        'Title' => '球探-法国新磐石武装巴萨 欧洲杯36年第一人',
                        'Description' => '腾讯体育7月8日讯 法国淘汰德国杀入欧洲杯决赛，同时也是队史上首次在欧洲杯零封日耳曼战车，蓝衣军防线表现出色，尤其是新锐国脚乌姆蒂蒂，连续两战打出高水准，巴萨斥资2500万欧元提前将其购入，实属明智。',
                        'PicUrl' => 'http://img1.gtimg.com/sports/pics/hv1/92/208/2095/136280507.jpg',
                        'Url' => 'http://sports.qq.com/a/20160708/026374.htm',
                        ),
            array(
                        'Title' => '少年驾驶直升机参加毕业舞会 现场炫酷惊呆师生',
                        'Description' => '据英国《每日邮报》7月7日报道，近日，英国一个少年驾驶自己租来的直升飞机参加毕业舞会，酷似詹姆斯•邦德，瞬间惊呆全校师生。',
                        'PicUrl' => 'http://img1.gtimg.com/16/1675/167594/16759452_980x1200_0.jpg',
                        'Url' => 'http://news.qq.com/a/20160711/023500.htm#p=1',
                        ),
    		);
    	$items='';
    	foreach ($newsArr as $key => $value) {
    		$items.=sprintf($this->item, $value['Title'], $value['Description'], $value['PicUrl'], $value['Url']);
    	}
    	$contentStr = sprintf($this->newTpl, $postObj->FromUserName, $postObj->ToUserName, time(),count($newsArr),$items);
    	echo $contentStr;
    }
    
    /**
     * 时间：2016年7月11日21:26:25
     * @author wgq
     * @param  [type] $postObj [description]
     * @return [type]          [description]
     */
      private function _sendMusic($postObj){
      $this->musicTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[music]]></MsgType>
                        <Music>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <MusicUrl><![CDATA[%s]]></MusicUrl>
                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                        </Music>
                        </xml>";
      //1.歌曲信息的组合
      $Title = '小歌曲';
      $Description = '小歌曲';
      $MusicUrl = 'http://so1.111ttt.com:8282/2016/1/06/20/199201048457.mp3?tflag=1466389833&pin=70d37142ea5d912f168918986e2e5ad1';
      $HQMusicUrl = 'http://so1.111ttt.com:8282/2016/1/06/20/199201048457.mp3?tflag=1466389833&pin=70d37142ea5d912f168918986e2e5ad1';
      $ThumbMediaId = 'Q4LZnvVfOWowvVj2q0z4X2YrTqqj2MsOD8SWN8cckROpAaMZ05STV5wWg9aaIHsW';
      //2.组合模板
      $resultStr = sprintf($this->musicTpl,  $postObj->FromUserName,$postObj->ToUserName,time(), $Title, $Description, $MusicUrl, $HQMusicUrl, $ThumbMediaId);
      //3.输出模板信息
      echo $resultStr;
    }


    /**
     * 回复文本信息的
     * @time:2016年7月11日07:56:29
     * @author wgq
     * @return [type] [description]
     */
    private function _doText($postObj)
    {
    	file_put_contents('./test.txt','dddd');
    	 //$fromUsername = $postObj->FromUserName;
                //$toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
                //$time = time();
            /*$textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";            */
				if(!empty( $keyword ))
                {
              		$msgType = "text";
              		$url = 'http://api.qingyunke.com/api.php?key=free&appid=0&msg='.$keyword;
              		$contents = $this->request($url);
              		$contents = json_decode($contents);
              		$contentStr = $contents ->content;
              		if ($keyword == '王国庆的印象') {
              			$contentStr = "ta是傻逼不？";
              		}
                	if($keyword == '歌曲'){
                  	   $this->_sendMusic($postObj);
                  	   exit;
                	}
                	if($keyword == '是') {
                		$contentStr = '你才是，你个笨蛋';
                	}
                	if($keyword == '不是') {
                		$contentStr = '孩子懂我，一会给你买糖吃';
                	}
                	$resultStr = sprintf($this->textTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $msgType, $contentStr);
                	echo $resultStr;
                }
    }
    /*处理图片的问题
     *时间：2016年7月11日09:27:03
     * @author：wgq
     */
    private function _doImage($postObj)
    {
    	$PicUrl = $postObj->PicUrl;
    	$resultStr = sprintf($this->textTpl, $postObj->FromUserName, $postObj->ToUserName, time(), 'text', $PicUrl);
        echo $resultStr;
    }
    /**
     * @author wgq
     * @return 地理位置
     * @time 2016年7月11日09:32:16
     */
    private function _doLocation($postObj)
    {
    	$loaction = '你当前的位置X:'.$postObj ->Location_X.'Y:'.$postObj ->Location_X;
    	$resultStr = sprintf($this->textTpl, $postObj->FromUserName, $postObj->ToUserName, time(), 'text', $loaction);
        echo $resultStr;
    }
    private function checkSignature()
    {
          // you must define TOKEN by yourself
          if (!defined("TOKEN")) {
              throw new Exception('TOKEN is not defined!');
          }

	          $signature = $_GET["signature"];
	          $timestamp = $_GET["timestamp"];
	          $nonce = $_GET["nonce"];

		      $token = $this->token;
		      $tmpArr = array($token, $timestamp, $nonce);
		          // use SORT_STRING rule
		      sort($tmpArr, SORT_STRING);
		      $tmpStr = implode( $tmpArr );
		      $tmpStr = sha1( $tmpStr );

		      if( $tmpStr == $signature ){
		        return true;
		      }else{
		        return false;
	      }
    }
	//curl 函数
	public function request($url,$https=true,$method='get',$data=null)
	{
	    //1.初始化url
	    $ch = curl_init($url);
	    //2.设置相关的参数
	    //字符串不直接输出,进行一个变量的存储
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    //判断是否为https请求
	    if($https === true){
	      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    }
	    //判断是否为post请求
	    if($method == 'post'){
	      curl_setopt($ch, CURLOPT_POST, true);
	      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    }
	    //3.发送请求
	    $str = curl_exec($ch);
	    //4.关闭连接
	    curl_close($ch);
	    //返回请求到的结果
	    return $str;
  	}
	/*获取access_token
	 *时间：2016年7月9日22:30:13
	 *author：wgq
	 */
	public function getAccessToken()
	{
		$memcache = new Memcache();
		$memcache->connect('localhost',11211);
		$data = $memcache->get('data');
		if(empty($data)){
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appId.'&secret='.$this->appSecret;
		$content = $this->request($url);
		$content = json_decode($content);
		$data = $content->access_token;
		//var_dump($content);
		//echo $content->access_token;
		$memcache ->set('data',$data,0,7180);
	   }
	   return "$data";
	}
	/*获得票据
	 *时间：2016年7月9日23:00:56
	 *author;wgq
	 */
	public function getTicket($tmp=0,$id=null,$id1=null)
	{
		//获得url
		$access_token = $this->getAccessToken();
		//echo $access_token;die;
		//
		 $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
		 /*echo "$url";die;*/
		//获得post数据
		if($tmp == 0) {
			$post = '{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
		} else {
			$post = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id":'.$id1.'}}}';
		}
		//request
		$content = $this->request($url, true, 'post',$post);
		$content = json_decode($content);
		$ticket = $content -> ticket;
		return $ticket;
	}

	/*通过票据获取二维码
	 *时间：2016年7月9日23:21:10
	 * author：wgq
	 */
	public function getQrCode()
	{
		$ticket = $this->getTicket();
		//url
		$url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
		$content = $this->request($url);
		//生成的是个图像文件格式时.jpg，所以要保存
		//var_dump($content);
		file_put_contents('./Qr.jpg', $content);
	}

	/*自定义接口的创建
	 *时间：2016年7月9日23:31:15
	 * author：wgq
	 */
	public function createMenu()
	{
		$access_token = $this->getAccessToken();
		//url
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
		//post数据拼凑
		$data = ' {
		     "button":[
		     {	
		          "type":"click",
		          "name":"新闻",
		          "key":"news"
		      },
		      {	
		          "type":"click",
		          "name":"歌曲",
		          "key":"music"
		      },
		      {
		           "name":"菜单",
		           "sub_button":[
		           {	
		               "type":"view",
		               "name":"搜索",
		               "url":"http://www.soso.com/"
		            },
		            {
			            "name": "发送位置", 
			            "type": "location_select", 
			            "key": "rselfmenu_2_0"
       				 },
       				   {
	                    "type": "pic_photo_or_album", 
	                    "name": "拍照或者相册发图", 
	                    "key": "rselfmenu_1_1", 
	                    "sub_button": [ ]
                		}
		            ]
		       }]
		 }';
		$content = $this->request($url, true, 'post',$data);
		$content = json_decode($content);
		//var_dump($content);
		if ($content->errmsg) {
			echo "创建成功";
		} else {
			echo "创建失败，检查下吧".$content->errmsg;
		}
	}

	/*删除自定义
	 *时间：2016年7月9日23:54:46
	 * author:wgq
	 */
	public function delMenu()
	{
		$access_token = $this->getAccessToken();
		//url
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$access_token;
		$content = $this->request($url);
		$content = json_decode($content);
		if ($content->errmsg) {
			echo "删除成功";
		} else {
			echo "删除失败，错误代码是：".$content->errcode;
		}
	}

	/*接口查询
	 *时间：2016年7月10日00:02:07
	 * author：wgq
	 */
	public function showMenu()
	{
		$access_token = $this->getAccessToken();
		//url
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$access_token;
		$content = $this->request($url);
		var_dump($content);
	}
	/*获取用户列表
	 *时间：2016年7月10日22:01:59
	 * author:wgq
	 */
	public function getUserList()
	{

		$access_token = $this->getAccessToken();
		//echo $access_token;
		//url
		$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$access_token ;
		$content = $this->request($url);
		$content = json_decode($content);
		//var_dump($content);
	    $data = $content->data->openid;
	    foreach ($data as $key => $value) {
	    	echo $value.'<br>';
	    }
	}
	/*获取用户的详细信息
	 *时间：2016年7月10日22:14:31
	 * author：wgq
	 */
	public function getUserDetail()
	{
		$access_token = $this->getAccessToken();
		$openid = 'o_-gDwGuVuD1Fcha7qZqImyBnvvY';
		$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		$content = $this->request($url);
		//var_dump($content);
		$content = json_decode($content);
		echo '姓名是：'.$content->nickname."<br>";
		if ($content->sex == 2) {
		   echo '性别是：女 <br>';
		}else {
			echo "男 <br>";
		}
		
		echo '城市是：'.$content->city."<br>";
		echo "头像是：<br><img src='".$content->headimgurl."'/>";


	}
}


 ?>