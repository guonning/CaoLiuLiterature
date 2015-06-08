<?php
require 'simple_html_dom.php';
class caoliu {
	private $url = 'http://be.clcl.be/';
	private $dom;
	
	public function __construct() {
	}
	
	public function get_list($fid, $page=1) {
		$url = $this->url . 'thread0806.php?fid=' . $fid . '&page=' . $page;
		$file_content = $this->_get($url);
		$html = str_get_html($file_content);
				
		$threads = array();
		foreach( $html->find('tr.t_one') as $thread ) {
			$body			= $thread->find('a[id]', 0);
			$t['title']		= $body->plaintext;
			$t['author']	= $thread->find('a.bl', 0)->plaintext;
			$t['url']		= $body->href;
			$t['time']		= $thread->find('div.f10', 0)->plaintext;
			$threads[]		= $t;
		}

		$html->clear();
		return $threads;
	}

	public function detail($t) {
		$url = $this->url . $t['url'];
		$detail_content = $this->_get($url);
		$html = str_get_html($detail_content);

		$detail['title'] = $t['title'];
		$contents = $html->find('div.tpc_content');
		foreach( $contents as $c ) {
			if (strlen($c->plaintext) < 500) continue;
			$content[] = str_replace('<br>', '', str_replace('<br><br>', '<br/>', $c->innertext));
		}
		$detail['content'] = $content;
		return $detail;
	}


	private function _get($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Baiduspider+(+http://www.baidu.com/search/spider.htm)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$info = curl_exec($ch);
		curl_close($ch);
		return $info;
	}
}


$c = new caoliu();
$thread_list = $c->get_list(20);
$thread_detail = $c->detail($thread_list[8]);
var_dump($thread_detail);
