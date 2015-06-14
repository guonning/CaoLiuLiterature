<?php
//error_reporting(0);
require 'simple_html_dom.php';
class caoliu {
    private $host = 'to.clcl.biz';
    private $url;

    public function __construct() {
        $this->url = "http://{$this->host}/";
    }
    
    private function get_list($fid, $page=1, $type=0) {
        $url = $this->url . 'thread0806.php?fid=' . $fid . '&page=' . $page;
        $file_content = $this->_get($url);
        $html = str_get_html($file_content);

        /*$cats = $this->get_cats($html);	
        if ($cats !== FALSE) {
            foreach( $cats as $cat ) {
                echo $cat->plaintext;
            }
        }
        exit();*/
        
        $threads = array();
        foreach( $html->find('tr.t_one') as $thread ) {
            $body           = $thread->find('a[id]', 0);
            $t['cat_id']    = '0';
            $t['title']     = $body->plaintext;
            $t['author']    = $thread->find('a.bl', 0)->plaintext;
            $t['url']       = $body->href;
            $t['create_time']= $thread->find('div.f10', 0)->plaintext;
            $threads[]      = $t;
        }

        $html->clear();
        return $threads;
    }

    private function detail($thread_url) {        
        $thread_url = str_replace('data', 'mob', $thread_url);
        $url = $this->url . $thread_url;
        $detail_content = $this->_get($url);
        $html = str_get_html($detail_content);

        $author = $this->get_author($html);
        $page_numbers = intval($this->get_page_numbers($html));
        $detail['tid'] = $this->get_tid($thread_url);
        $detail['author'] = $author;
        $detail['title'] = $html->find('td.h', 0)->plaintext;
        $detail['pages'] = $page_numbers;

        $all_floors = $html->find('div.t2');
        foreach( $all_floors as $floor ) {
            if ($this->get_author($floor) === $author) {
                $cs = $floor->find('div.tpc_content li');
                foreach( $cs as $c ) {
                    if (strlen($c->plaintext) < 100) continue;
                    $content[] = $this->remove_br($c->innertext);
                }  
            }
        }        
        
        if ($page_numbers > 1) {
            for ( $i = 2; $i <= 2 ; $i++ ) {                 
                $floor_content = $this->detail_from_otherpage($detail['tid'], $i, $author);
                if ($floor_content !== FALSE && count($floor_content) > 0)  array_merge($content, $floor_content);
            }
        }
      
        $html->clear();
        $detail['content'] = $content;
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
        $all_floors = $html->find('div.t2');
        if (count($all_floors) == 0) return FALSE;
        $content = array();
        
        foreach( $all_floors as $floor ) {
            if ($this->get_author($floor) === $author) {
                $cs = $floor->find('div.tpc_content li');
                foreach( $cs as $c ) {
                    if (strlen($c->plaintext) < 100) continue;
                    $content[] = $this->remove_br($c->innertext);
                }                
            }
        }
                
        return $content;
    }

    private function get_cats($content) {
        $cats = $content->find('a.fr');
        return count($cats) == 2 ? FALSE : $cats;
    }

    private function get_page_numbers($content) {
        $pages = $content->find('div.pages', 0);
        if (count($pages) < 1) return 1;

        $page_numbers = $pages->find('a[!style]', -1)->plaintext;
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
        strpos($url, 'thread0806') === FALSE && $header[]= 'Cookie: ismob=1';  

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

        if (substr_count($content, '<br /><br />') > 10) {
            $content = str_replace('<br /><br />', '', $content);
        }
        
        return $content;
    }

    private function is_cli() {
        return (php_sapi_name() === 'cli');
    }

    public function run() {
        global $argc, $argv;
        $is_cli = $this->is_cli();
        if ( $is_cli ) {
            $act = $argv[1];
            switch ( $act ) {
                case 'index':
                    $page = intval($argv[2]);
                    $page <= 0 && $page = 1;
                    $data = $this->get_list(20, $page);
                    break;	
                case 'detail':
                    $url = $argv[2];
                    $data = $this->detail($url);
                    break;
                default:
                    $data = array('err' => 1);
                    break;
            }
        } else {
            $act = strtolower ($_GET['act']);
            switch ( $act ) {
                case 'index':
                    $page = intval($_GET['page']);
                    $page <= 0 && $page = 1;
                    $data = $this->get_list(20, $page);
                    break;	
                case 'detail':
                    $url = $_GET['url'];
                    $data = $this->detail($url);
                    break;
                default:
                    $data = array('err' => 1);
                    break;
            }
        }
        
        var_dump($data);
        //echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}

$c = new caoliu();
$c->run();
