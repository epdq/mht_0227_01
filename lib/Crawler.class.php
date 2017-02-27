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
	}