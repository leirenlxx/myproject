<?php
/**
 * 轻游戏-火锅英雄
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: HotpotHero.php 2016-03-17T13:10:54+0800
 */
namespace Lgame\Controller;

use Think\Controller;

class HotpotHero extends Controller
{
    //状态变量
    public $_status;

    /**
     * 构造函数
     * @author   lixiaoxian
     * @datetime 2016-03-17T11:39:56+0800
     */
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        parent::__construct();
        $this->_checkApp();
    }

    /**
     * 模板
     * @lixiaoxian
     * @datetime   2016-03-16T10:56:19+0800
     * @return     mixed               模板
     */
    public function index()
    {
        if ($this->_status) {
            // 使用带回UID
            $uid = I('get.uid');
            if ($uid) {
                $wxInfo['uid']      = $uid;
                $wxInfo['nickname'] = '';
            } else {
                $url = urlencode('http://h5.ijovo.com/lgame/hotpot_hero');
                redirect('http://play.ijovo.com/api/user/getuser/?r=' . $url . '&app=jxb');
            }
            // 微信设置
            $appID     = 'wx312522c55ff7eca6';
            $appSecret = '21db0464517035bcdb905e0d0708f95e';

            //JSSDK
            vendor('Wechat.JssdkOld');
            $Jssdk       = new \JssdkOld($appID, $appSecret);
            $signPackage = $Jssdk->GetSignPackage();
            D('LappLog')->saveLog($uid);

            //模板复制
            $this->assign('wxinfo', $wxInfo);
            $this->assign('jssdk', $signPackage);
            $this->assign('secret', $this->_secretCode());
            $this->display();
        } else {
            // $this->display('error');
        }
    }

    /**
     * 清除缓存
     * @author   lixiaoxian
     * @datetime 2016-03-23T10:34:38+0800
     * @return   mixed               模板
     */
    public function clear()
    {
        $this->display();
    }

    /**
     * 发送页面
     * @author   lixiaoxian
     * @datetime 2016-04-06T14:41:28+0800
     * @return   mixed               模板
     */
    public function sendsms()
    {
        $this->display();
    }

    /**
     * 取手机号
     * @author   lixiaoxian
     * @datetime 2016-04-06T10:17:06+0800
     * @return   json                获取手机号状态
     */
    public function phone()
    {
        $id    = I('get.id');
        $phone = D('Awardlist')->getPhone($id);
        if (!$phone == '') {
            $returnArr['status'] = 'success';
            $returnArr['msg']    = '获取成功';
            $returnArr['data']   = $phone['phone'];
        } else {
            $returnArr['status'] = 'fail';
            $returnArr['msg']    = '获取失败';
            $returnArr['data']   = '';
        }
        $this->ajaxReturn($returnArr);
    }

    /**
     * 发送短信
     * @author   lixiaoxian
     * @datetime 2016-04-06T10:22:53+0800
     * @return   json                发送状态
     */
    public function send()
    {
        // $phone = I('get.phone');
        // // $phone ='18716319181';
        // $userid   = '1';
        // $account  = 'xd001094';
        // $password = 'xd001094123';
        // $sendTime = '';
        // $mobile   = $phone;
        // $code     = D('Awardlist')->getCode($phone);

        // $content = '【江小白】感谢您参与火锅英雄揍劫匪的游戏！恭喜您位列英雄榜前500名，您将获得由江小白送出的《火锅英雄》电影票一张。电子兑换码：' . $code['code'] . '使用方法1:下载“万达影城”App注册登录后进入“我的钱包”，绑定兑换码快速购票即可。使用方法2:登录万达电影网（www.wandafilm.com），选座后购票时选择使用兑换码优惠即可。该兑换码在万达影城通用，有效期为2016年4月9日至2016年4月30日。';
        // $url     = 'http://dx.ipyy.net/sms.aspx?action=send&userid=' . $userid . '&account=' . $account . '&password=' . $password . '&mobile=' . $mobile . '&content=' . $content . '&sendTime=' . $sendTime;
        // // $result = ' Success';
        // $result = file_get_contents($url);
        if (strstr($result, 'Success')) {
            $returnArr['status'] = 'success';
            $returnArr['msg']    = '发送成功';
            $returnArr['data']   = '';
        } else {
            $returnArr['status'] = 'fail';
            $returnArr['msg']    = '发送失败';
            $returnArr['data']   = '';
        }
        $this->ajaxReturn($returnArr);
    }

    /**
     * 录入用户名字、手机号、获得分数
     * @author   lixiaoxian
     * @datetime 2016-03-15T14:17:23+0800
     * @return   json                接口状态
     */
    public function userInfo()
    {
        if ($this->_status) {
            if (IS_POST) {
                $this->_checkSecret();
                $data['user']   = I('post.user');
                $data['score']  = I('post.score');
                $data['phone']  = I('post.phone');
                $data['wx_uid'] = I('post.wx_uid', '', 'trim');
                //判断用户名和手机号是否正确
                if ($this->_checkData($data) == 2) {
                    // 判断用户是否已经存在
                    $Hotpothero = D('Hotpothero');
                    $info       = $Hotpothero->checkUser($data['wx_uid']);
                    if ($info['id']) {
                        // 用户已经存在，更新用户
                        if ($data['score'] > $info['score']) {
                            $updateStatus = $Hotpothero->updateUser($data);
                            if ($updateStatus === false) {
                                // 数据更新出错,系统本身出错
                                $returnArr['status'] = 'fail';
                                $returnArr['msg']    = '信息提交失败，请重试';
                            } else {
                                // 更新成功
                                $returnArr['status'] = 'success';
                                $returnArr['msg']    = '信息提交成功';
                                D('LappLog')->saveLog($data['wx_uid']);
                            }
                        } else {
                            // 数据更新成功
                            $returnArr['status'] = 'success';
                            $returnArr['msg']    = '你还没有刷新纪录';
                        }
                    } else {
                        // 用户不存在，创建用户
                        $createStatus = $Hotpothero->createUser($data);
                        if ($createStatus) {
                            // 用户创建成功
                            $returnArr['status'] = 'success';
                            $returnArr['msg']    = '信息提交成功';
                            D('LappLog')->saveLog($data['wx_uid']);
                        } else {
                            // 用户创建失败
                            $returnArr['status'] = 'fail';
                            $returnArr['msg']    = '信息提交失败，请重试';
                        }
                    }
                } elseif ($this->_checkData($data) == 1) {
                    $returnArr['status'] = 'fail';
                    $returnArr['msg']    = '手机号输入错误';
                } else {
                    $returnArr['status'] = 'fail';
                    $returnArr['msg']    = '用户名输入错误';
                }
            }
        } else {
            $returnArr['status'] = 'fail';
            $returnArr['msg']    = '活动已结束';
        }
        $this->ajaxReturn($returnArr);
    }

    /**
     * 击败对手百分比
     * @author   lixiaoxian
     * @datetime 2016-03-19T10:00:06+0800
     * @return   json|mixed          分数状态
     */
    public function beat()
    {
        if ($this->_status) {
            $this->_checkSecret();
            $wxUid = I('get.wx_uid', '', 'trim');
            $score = I('get.score', 0, 'intval');
            if ($wxUid) {
                $Hotpothero = D('Hotpothero');
                //玩家总数
                $allCount = $Hotpothero->getAllCount();
                //我当前排名
                $myPosition = $Hotpothero->getMyPosition($score);
                // 计算排名
                $percent = ($allCount - $myPosition) / $allCount;

                $returnArr['status']          = 'success';
                $returnArr['msg']             = '数据获取成功';
                $returnArr['data']['percent'] = round($percent * 100, 1) . '%';
            }
        } else {
            $returnArr['status'] = 'fail';
            $returnArr['msg']    = '活动已结束';
        }
        $this->ajaxReturn($returnArr);
    }

    /**
     * 排行榜
     * @lixiaoxian
     * @datetime   2016-03-16T09:08:34+0800
     * @return     json                排行榜数据
     */
    public function rank()
    {
       	if ($this->_status) {
            $this->_checkSecret();
            $param['wx_uid'] = I('get.wx_uid', '', 'trim');
            $param['score']  = I('get.score', 0, 'intval');
            $data            = D('Hotpothero')->getRank($param, 10);
            if ($data) {
                foreach ($data['list'] as $key => $value) {
                    $data['list'][$key]['phone'] = substr_replace($value['phone'], '****', 3, 4);
                }
                $listArr['status'] = 'success';
                $listArr['msg']    = '数据取出成功';
                $listArr['data']   = $data;
            } else {
                $listArr['status'] = 'fail';
                $listArr['msg']    = '数据取出失败';
                $listArr['data']   = '';
            }
        } else {
            $returnArr['status'] = 'fail';
            $returnArr['msg']    = '活动已结束';
        }
        $this->ajaxReturn($listArr);
    }

    /**
     * 验证输入
     * @author   lixiaoxian
     * @datetime 2016-03-16T14:45:56+0800
     * @param    array      $data    用户输入信息
     * @return   int                 输入状态
     */
    private function _checkData($data)
    {
        if (2 > strlen($data['user']) || strlen($data['user']) > 13) {
            // 0表示用户名错误
            return 0;
        } elseif (!preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', $data['phone'])) {
            // 1表示手机号错误
            return 1;
        } else {
            // 2表示输入正确
            return 2;
        }
    }

    /**
     * 检查活动时间
     * @author   lixiaoxian
     * @datetime 2016-03-19T09:07:09+0800
     * @return   int                 状态变量
     */
    private function _checkApp()
    {
        $Lapp = D('Lapp');
        $info = $Lapp->getAppInfo('hotpothero');
        if ($info['status']) {
            if ($info['starttime'] < NOW_TIME && NOW_TIME < $info['endtime']) {
                return $this->_status = 1;
            } else {
                return $this->_status = 0;
            }
        } else {
            return $this->_status = 0;
        }
    }

    /**
     * 验证请求安全性
     * @author   liuke
     * @datetime 2016-03-24T16:24:26+0800
     * @return   json                不合法安全结果
     */
    private function _checkSecret()
    {
        //验证会话安全
        $secret = I('get.secret');
        if ($secret) {
            $checkStatus = 1;
            if ($secret !== $this->_secretCode()) {
                //会话不合法
                $checkStatus = 0;
            }
            //会话合法
        } else {
            //没有安全符，不合法请求
            $checkStatus = 0;
        }

        if (!$checkStatus) {
            $returnArr['status'] = 'fail';
            $returnArr['msg']    = '^_^';
            $this->ajaxReturn($returnArr);
        }
    }
    /**
     * 会话安全码
     * @author   liuke
     * @datetime 2016-03-24T16:11:49+0800
     * @param    string        $salt 加密盐
     */
    private function _secretCode($salt = 'hotpothero')
    {
        return md5(session_id() . $salt);
    }

}
