<?php
/**
 * 招聘-轻应用
 * @author liuke <liuke@ijovo.com>
 * @version $Id: Talent16.php 2016-02-25T20:38:28+0800 $
 */
namespace Lapp\Controller;

use Think\Controller;

class Talent16 extends Controller
{
    /**
     * 页面
     * @author   liuke
     * @datetime 2016-02-25T20:38:28+0800
     * @return   [type]                   [description]
     */
    public function index()
    {
        $AppID     = 'wx312522c55ff7eca6';
        $AppSecret = '21db0464517035bcdb905e0d0708f95e';
        vendor('Wechat.JssdkOld');
        $Jssdk       = new \JssdkOld($AppID, $AppSecret);
        $signPackage = $Jssdk->GetSignPackage();

        $this->assign('jssdk', $signPackage);
        $this->display();
    }
}
