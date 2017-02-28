<?php
	class Crawler{
		
		private $url = '';	// 爬取的链接
		private $base_url = '';	// 爬取的网站域名

		function __construct($url)
		{
			
			$parsed_url = parse_url($url);
			if($parsed_url != false && $parsed_url != E_WARNING){
				$this->url = $url;
				$scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
				$host = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
				$this->base_url = $scheme . $host;
			}else{
				echo "爬取url地址不正确!";
			}
		}

		public function getHtml(){
			$url = $this->url;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$html = curl_exec($ch);
			curl_close($ch);

			// 编码转换
			$coding = mb_detect_encoding($html);
			if ($coding != "UTF-8" || !mb_check_encoding($html, "UTF-8")){
				$html = mb_convert_encoding($html, 'utf-8', 'GBK,UTF-8,ASCII');
			}

			return $html;
  
		}

		public function reviseUrl($base_url, $url){
			if(is_array($url_list)){
				foreach($url_list as $url_item){
					if(preg_match("/^(http:\/\/|https:\/\/|javascript:)/", $url_item)){
					$result_url_list[] = $url_item;
				}else {
					if(preg_match("/^\//",$url_item)){
						$real_url = $base_url . $url_item;
					}else{
						$real_url = $base_url . "/" . $url_item;
					}
					$result_url_list[] = $real_url; 
				}
			}
				return $result_url_list;
			}else{
				return;
			}
		}

		public function getBaseUrl()
		{
			return $this->base_url;
		}
	}