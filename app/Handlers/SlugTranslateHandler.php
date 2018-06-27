<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
	public function translate($text)
	{
		$http = new Client;

		$api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
		$appid = config('services.baidu_translate.appid');
		$key = config('services.baidu_translate.key');
		$salt = time();

		if (empty($appid) || empty($key)) {
			return $this->pinyin($text);
		}

		// 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
		$sign = md5($appid . $text . $salt . $key);

		// 构建请求参数
        $query = http_build_query([
            "q"     =>  $text,
            "from"  => "zh",
            "to"    => "en",
            "appid" => $appid,
            "salt"  => $salt,
            "sign"  => $sign,
        ]);

        $response = $http->get($api . $query);

        $result = json_decode($response->getBody(), true);

        /**
        获取结果，如果请求成功，dd($result) 结果如下：

        array:3 [▼
            "from" => "zh"
            "to" => "en"
            "trans_result" => array:1 [▼
                0 => array:2 [▼
                    "src" => "XSS 安全漏洞"
                    "dst" => "XSS security vulnerability"
                ]
            ]
        ]

        **/

        if (isset($result['trans_result'][0]['dst'])) {
        	return str_slug($result['trans_result'][0]['dst']);
        } else {
        	return $this->pinyin($text);
        }
	}

	public function pinyin($text)
	{
		return str_slug(app(Pinyin::class)->permalink($text));
	}
}