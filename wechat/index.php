<?php 
	


	$wechatObj = new wechatCallbackapiTest();
	$wechatObj->responseMsg();

	class wechatCallbackapiTest
	{
		//回复判断
	    public function responseMsg()
	    {
	        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

	        if (!empty( $postStr)){
	            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
	            $type = trim( $postObj->MsgType);
	            switch ( $type) {
	                case 'event':
	                    $result = $this->dealEven($postObj);
	                    break;
	                case 'voice':
	                    $Content = $this->removeMark(trim( $postObj->Recognition));
	                    $result = $this->dealText( $postObj, $Content);
	                    break;
	                case 'text':
	                    $Content = $this->removeMark(trim( $postObj->Content));
	                    $result = $this->dealText($postObj, $Content);
	                    break;
	                default:
	                    $result = "";
	                    break;
	            }
	            echo $result;
	        }else{
	            echo "";
	            exit;
	        }
	    }
		
		private function dealEven($object) {
	        $openid = $object->FromUserName;
	        switch ($object->Event) {
	            case 'subscribe':
	                include('deal.php');
					$content = "欢迎关注书城\n";
					$msg = '如果您忘记了密码可回复"密码",系统会为您重置密码,并将重置后的密码发送给您.';
					$pass = register_deal($openid);
					$name = get_name($openid);
					$nickname = get_user_nickname($openid);
					if(!empty($pass) && !empty($name) && !empty($nickname)) {
	                    $nickname = 'Hi, ' . $nickname . "\n";
	                    $user = '您的用户名: ' . $name . "\n";
	                    $pass = '密码: ' . $pass . "\n";
	                    $content = $nickname . $content . $user . $pass . $msg;
	                }
					$result = $this->replyText( $object, $content);
	                break;
	            /*case 'CLICK':
	                switch ($object->EventKey) {
	                    case '天气':
	                        $content = $this->weather();
	                        $result = $this->replyText( $object, $content);
	                        include('msg_push.php');
	                		msg_push($openid, '天气', '2');
	                        break;
	                    case '备忘':
	                        include('msg_push.php');
	                        include('memo.php');
	                        $content = memo($openid);
	                        $result = $this->replyText( $object, $content);
	                		msg_push($openid, '备忘', '2');
	                        break;
	                    case '空气':
	                        $city = "北京";
	                        $content = $this->getAirQualityChina( $city);
	                        $result = $this->replayNews( $object, $content);
	                        include('msg_push.php');
	                		msg_push($openid, '空气', '2');
	                        break;
	                    default:
	                        $content = "";
	                        break;
	                }
	                break;*/
	            default:
	                $content = "";
	                break;
	        }
	        return $result;
	    }
		
		/*过滤文本的标点符号*/
	    private function removeMark($text){
	        $text=urlencode($text); 
	        $text=preg_replace("/(%7E|%60|%21|%40|%23|%24|%25|%5E|%26|%27|%2A|%28|%29|%2B|%7C|%5C|%3D|
	                                \-|_|%5B|%5D|%7D|%7B|%3B|%22|%3A|%3F|%3E|%3C|%2C|\.|%2F|%A3%BF|%A1%B7|
	                                %A1%B6|%A1%A2|%A1%A3|%A3%AC|%7D|%A1%B0|%A3%BA|%A3%BB|%A1%AE|%A1%AF|%A1%B1|
	                                %A3%FC|%A3%BD|%A1%AA|%A3%A9|%A3%A8|%A1%AD|%A3%A4|%A1%A4|%A3%A1|%E3%80%82|%EF%BC%81|
	                                %EF%BC%8C|%EF%BC%9B|%EF%BC%9F|%EF%BC%9A|%E3%80%81|%E2%80%A6%E2%80%A6|%E2%80%9D|%E2%80%9C|
	                                %E2%80%98|%E2%80%99|%EF%BD%9E|%EF%BC%8E|%EF%BC%88)+/",' ',$text); 
	        $text=urldecode($text); 
	        return trim($text); 
	    }
		
		/*处理文本*/
	    private function dealText($object, $content){
	    	if(strpos($content, '密码') !== false) {
	    		include('deal.php');
	    		if(!$content = register_deal($object->FromUserName)) {
	    			$content = '';
	    		}
	    	} else {
				$reco = $content."\n";
		        $content = $this->translateYoudao( trim( $content));
		        $content = $reco.$content;
	    	}
	        $result = $this->replyText( $object, $content);
	        return $result;
	    }
		
		private function translateYoudao( $word ) {
	        if( $word == ""){
	            return "请输入要翻译的内容";
	        }
	        $apihost = "http://fanyi.youdao.com/";
	        $apimethod = "openapi.do?";
	        $apiparams = array( 'keyfrom'=>"zuixinan", 'key'=>"1753331900",
	                             'type'=>"data", 'doctype'=>"json", 'version'=>"1.1", 'q'=>$word);
	        $apicallurl = $apihost.$apimethod.http_build_query( $apiparams);

	        $ch = curl_init();
	        curl_setopt( $ch, CURLOPT_URL, $apicallurl);
	        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	        $output = curl_exec( $ch);

	        $youdao = json_decode( $output, true);
	        $result = "";
	        switch ( $youdao['errorCode']){
	            case 0:
	                if(isset( $youdao['basic'])){
	                    $result .= $youdao['basic']['phonetic']."\n";
	                    foreach ( $youdao['basic']['explains'] as $value) {
	                        $result .= $value."\n";
	                    }
	                }else{
	                    $result = "未找到";
	                }
	                break;
	        }
	        return trim( $result);
	    }
	    
		 /*文字回复模版*/
	    private function replyText( $object, $content){
	        $textTpl = "<xml>
	                        <ToUserName><![CDATA[%s]]></ToUserName>
	                        <FromUserName><![CDATA[%s]]></FromUserName>
	                        <CreateTime>%s</CreateTime>
	                        <MsgType><![CDATA[text]]></MsgType>
	                        <Content><![CDATA[%s]]></Content>
	                        <FuncFlag>0</FuncFlag>
	                        </xml>";
	        $result = sprintf( $textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
	        return $result;
	    }
	}

 ?>