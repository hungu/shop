<?php
	$access_token ="ow-J7INSELJ3KsuH9osJ4t9kiQW92G3etBa-KMDMGumly5ngf-SNOhkpvtGqIt6K64jL5a_WbeRK2Nlf688S7hMIscm5nJJWVHmFiIrS1a7on5XX2Cdcdt2B7PMDDml2KGOfAEAVRZ";
	$menu = '{
		"button":[
			{
				"name":"商城首页",
				"type":"view",
				"url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx8c2b3675ee0c54ea&redirect_uri=http://shop.zuixinan.top/wechat/id.php&response_type=code&scope=snsapi_base&state=2#wechat_redirect"
			},
			{
				"name":"test",
				"type":"click",
				"key":"空气"
			},
			{
				"name":"今日备忘",
				"type":"click",
				"key":"备忘"
			}
		]
	}';
	$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
	$result = https_request( $url, $menu);
	var_dump( $result);
	
	function https_request( $url, $data = NULL){
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		if( !empty( $data)){
			curl_setopt( $ch, CURLOPT_POST, 1);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec( $ch);
		curl_close( $ch);
		return $output;
	}
?>