<?php
//error_reporting(0);

require 'simple_html_dom.php';
class caoliu {
    private $url = 'http://be.clcl.be/';

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

    public function detail($thread_url) {
        $url = $this->url . $thread_url;
        $detail_content = $this->_get($url);
        $html = str_get_html($detail_content);

        $detail['title'] = str_replace(' 草榴社區  - powered by phpwind.net', '', $html->find('title', 0)->plaintext);
        $contents = $html->find('div.tpc_content');
        foreach( $contents as $c ) {
            if (strlen($c->plaintext) < 100) continue;	//文学版块,去除小于100个文字的内容
            $content[] = $this->remove_br($c->innertext);
        }
        $detail['content'] = $content;

        $html->clear();
        return $detail;
    }

    public function detail_mobile ($thread_url) {        
        $thread_url = str_replace('data', 'mob', $thread_url);
        $url = $this->url . $thread_url;
        $detail_content = $this->_get($url);
        $html = str_get_html($detail_content);

        $detail['title'] = $html->find('td.h', 0)->plaintext;
        $contents = $html->find('div.tpc_content li');
        foreach( $contents as $c ) {
            if (strlen($c->plaintext) < 100) continue;	//文学版块,去除小于100个文字的内容
            $content[] = $this->remove_br($c->innertext);
        }
        $detail['content'] = $content;

        $html->clear();
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

    private function remove_br($content) {
        if (substr_count($content, '<br><br>') > 10) {
            $content = str_replace('<br>', '', str_replace('<br><br>', '<br/>', $content));
        }
        return $content;
    }
}


$c = new caoliu();
$act = strtolower ($_GET['act']);
switch ( $act )
{
	case 'index':
	    $page = intval($_GET['page']);
        $page <= 0 && $page = 1;
        $data = $c->get_list(20, $page);
        break;	
    case 'detail':
        $url = $_GET['url'];
        $data = $c->detail_mobile($url);
        break;	
    default:
        $data = array('err' => 1);
        break;
}
var_dump($data);
//echo json_encode($data);
