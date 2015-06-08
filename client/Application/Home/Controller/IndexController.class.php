<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {
    public function index(){
        $this->show("<h1>It's works!</h1>");
    }    
}