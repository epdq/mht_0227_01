<?php
	class Crawler{
		public function init($url)
		{
			# code...
		}

		public function getHtml($url){

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

		public function reviseUrl($base_url, $url)
		{
			$url_info = parse_url($base_url);
			$base_url = $url_info["scheme"].'://';
			if($url_info["user"] && $url_info["pass"]){
				$base_url .= $url_info["user"].":".$url_info["pass"]."@";
			}
			$base_url .= $url_info["host"];
			if($url_info["port"]){
				$base_url .= ":".$url_info["port"];
			}

			$base_url .= $url_info["path"];
			print_r($base_url);
			if(is_array($url_list)){
				foreach ($url_list as $url_item) {
					if(preg_match('/^http/',$url_item)){
						//已经是完整的url
						$result[] = $url_item;
					}else {
						//不完整的url
						$real_url = $base_url.'/'.$url_item;
						$result[] = $real_url;
					}
				}
				return $result;
			}else {
				return;
			}
		}
	}