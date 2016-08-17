<?php
/**
 * 轻游戏-单纯
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: Simple.php 2016-07-15T14:22:40+0800
 */
namespace Lgame\Controller;

use Think\Controller;

class Simple extends Admin
{
    /**
     * 首页模板
     * @author   lixiaoxian
     * @datetime 2016-07-15T14:19:34+0800
     * @return   [type]                   [description]
     */
    public function index()
    {
        $app = session('app');
        if (!empty($app)) {
            $this->display($app);
        }
    }

}
