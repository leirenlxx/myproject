<?php
namespace Lgame\Controller;

use Think\Controller;
use Wechat\Api\UserApi;

class Test extends Controller
{
    public function index()
    {
        // 微信设置
        $appID     = 'wx312522c55ff7eca6';
        $appSecret = '21db0464517035bcdb905e0d0708f95e';
        //获取参数
        $uid    = I('get.uid');
        $openid = I('get.openid');
        $code   = I('get.code');
        //调用接口
        $UserApi = new UserApi($appID, $appSecret);
        $wxInfo  = $UserApi->getUserInfo('lgame', $uid, $openid, $code);

        //JSSDK
        vendor('Wechat.JssdkOld');
        $Jssdk       = new \JssdkOld($appID, $appSecret);
        $signPackage = $Jssdk->GetSignPackage();

        //模板复制
        $this->assign('wxinfo', $wxInfo);
        $this->assign('jssdk', $signPackage);

        print_r($wxInfo);
        print_r($signPackage);
    }
}
