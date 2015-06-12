<?php
//error_reporting(0);
require 'simple_html_dom.php';
class caoliu {
    private $host = 'to.clcl.biz';
    private $url;

    public function __construct() {
        $this->url = "http://{$this->host}/";
    }
    
    public function get_list($fid, $page=1) {
        $url = $this->url . 'thread0806.php?fid=' . $fid . '&page=' . $page;
        $file_content = $this->_get($url);
        $html = str_get_html($file_content);
        	
        $threads = array();
        foreach( $html->find('tr.t_one') as $thread ) {
            $body           = $thread->find('a[id]', 0);
            $t['title']     = $body->plaintext;
            $t['author']    = $thread->find('a.bl', 0)->plaintext;
            $t['url']       = $body->href;
            $t['time']      = $thread->find('div.f10', 0)->plaintext;
            $threads[]      = $t;
        }

        $html->clear();
        return $threads;
    }

    public function detail($thread_url) {        
        $thread_url = str_replace('data', 'mob', $thread_url);
        $url = $this->url . $thread_url;
        $detail_content = $this->_get($url);
        $html = str_get_html($detail_content);

        $author = $this->get_author($html);
        $detail['tid'] = $this->get_tid($thread_url);
        $detail['author'] = $author;
        $detail['title'] = $html->find('td.h', 0)->plaintext;
        $page_numbers = intval($this->get_page_numbers($html));
        $detail['pages'] = $page_numbers;
        
        $contents = $html->find('div.tpc_content li');
        
        foreach( $contents as $c ) {
            if (strlen($c->plaintext) < 100) continue;	//文学版块,去除小于100个文字的内容
            $content[] = $this->remove_br($c->innertext);
        }

        if ($page_numbers > 1) {
            for ( $i=2; $i <= $page_numbers ; $i++ ) { 
                $this->detail_from_otherpage($tid, $author);
            }
        }
        
        $detail['content'] = $content;
        $html->clear();
        return $detail;
    }

    private function get_tid($url) {
        $p = pathinfo($url, PATHINFO_BASENAME);
        return explode('.', $p)[0];
    }

    private function detail_from_otherpage($tid, $page, $author) {
        $url = $this->url . "read.php?tid={$tid}&page={$page}";
        
        $detail_content = $this->_get($url);
        $html = str_get_html($detail_content);
        
        $contents = $html->find('div.tpc_content li');
        
        return '';
    }

    private function get_page_numbers($content) {
        $numbers = $content->find('div.pages', 0);
        if (count($numbers) <= 1) return 1;
        
        $page_numbers = $numbers->find('a[!style]')[count($numbers)-1]->plaintext;
        return $page_numbers;
    }

    private function get_author($content) {
        return $content->find('font[face=Gulim]', 0)->plaintext;
    }

    private function _get($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $header[]= 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';  
        $header[]= 'Accept-Language: zh-cn,zh;q=0.8,en;q=0.6 ';  
        $header[]= 'User-Agent: Baiduspider+(+http://www.baidu.com/search/spider.htm)';  
        $header[]= 'Host: '.$this->host;  
        $header[]= 'Connection: keep-alive ';  
        strpos('thread0806', $url) === FALSE || $header[]= 'Cookie: ismob=1';  

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);   
        curl_setopt($ch, CURLOPT_HEADER, 0);
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
        $data = $c->detail($url);
        break;
    default:
        $data = array('err' => 1);
        break;
}
var_dump($data);
//echo json_encode($data, JSON_UNESCAPED_UNICODE);
