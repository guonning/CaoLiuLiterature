<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {
    public function index(){
        $this->display();
    }    

    public function ajax() {
	    $page = I('get.page', 1);
	    $content = $this->_get('http://localhost:8081/caoliu.php?act=index&page=' . $page);
	    $data = array(
			'pages'		=> 5,
			'records'	=> json_decode($content, true),
			'next_page'	=> $page + 1
	    );

	    $this->ajaxReturn($data);
    }

    public function detail($url) {
	    $content = $this->_get('http://localhost:8081/caoliu.php?act=detail&url=' . $url);
	    $content = json_decode($content, true);
	    $this->assign('data', $content);
	    $this->display();
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