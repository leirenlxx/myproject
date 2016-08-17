<?php

namespace Lgame\Controller;

use Think\Controller;

class Admin extends Controller
{

    /**
     * 初始化
     * @author   lixiaoxian
     * @datetime 2016-07-15T14:22:30+0800
     * @return   [type]                   [description]
     */
    public function _initialize()
    {
        header("Access-Control-Allow-Origin: *");
        $app = I('get.app', '', 'trim');
        if (!empty($app)) {
            $status = D('Lapp')->checkApp($app);
            switch ($status) {
                case '0':
                    $this->error('活动已结束！');
                    break;
                case '1':
                    session('app', $app);
                    break;
                case '2':
                    $this->error('参数错误');
                    break;
                case '3':
                    $this->error('活动暂停或禁用！');
                    break;
                default:
                    # code...
                    break;
            }
        } else {
            $this->error('参数异常');
        }
    }
}
