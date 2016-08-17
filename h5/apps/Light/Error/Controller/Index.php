<?php
/**
 * 错误-notfound
 * @author liuke <liuke@ijovo.com>
 * @version $Id: Index.php 2016-03-16T17:01:53+0800
 */
namespace Error\Controller;

use Think\Controller;

class Index extends Controller
{
    /**
     * notfound页面
     * @author   liuke
     * @datetime 2016-03-16T17:01:53+0800
     */
    public function index()
    {
        $arr = array('name' => '刘科', 'age' => '28', 'skill' => 'PHP');
        $this->assign('test', $arr);
        $this->display();
    }
}
