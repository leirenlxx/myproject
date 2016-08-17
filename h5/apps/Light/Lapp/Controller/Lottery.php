<?php
/**
 * 抽奖模块
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: Lottery.php 2016-03-29T13:22:40+0800
 */
namespace Lapp\Controller;

use Think\Controller;

class Lottery extends Controller
{

    //全局变量
    public $_status;

    public $_type;
    //控制器名对应的ID
    // private $_id = CONTROLLER_NAME;
    private $_id = 1;
    /**
     * 构造函数
     * @author   lixiaoxian
     * @datetime 2016-03-24T12:03:25+0800
     */
    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        parent::__construct();
        $this->_checkLottery();
    }

    /**
     * 输出模板
     * @author   lixiaoxian
     * @datetime 2016-03-23T17:29:12+0800
     * @return   [type]                   [description]
     */
    public function index()
    {

    }

    /**
     * 模式1，随机抽奖
     * @author   lixiaoxian
     * @datetime 2016-03-30T10:03:11+0800
     * @param    [type]                   $rewards [description]
     * @return   [type]                            [description]
     */
    private function _mode1($rewards)
    {

    }

    public function test()
    {
        $this->display();
    }

    /**
     * 模式1，随机抽奖
     * @author   lixiaoxian
     * @datetime 2016-03-30T10:02:33+0800
     * @return   json                抽奖状态
     */
    public function mode1Raffle()
    {
        if ($this->_status) {
            $count         = 1000; //基数
            $chance        = 5; //抽奖机会
            $uid           = I('get.uid');
            $LotteryReward = D('LotteryReward');
            $rewardList    = $LotteryReward->getRewardList($this->_id);
            $LotteryLog    = D('LotteryLog');
            $countChance   = $LotteryLog->countChance($uid);
            foreach ($rewardList as $key => $value) {
                //总奖品数
                $rewardArr[] = $value['total'];
                //剩余总数
                $left += $value['rest'];
            }
            if (isset($uid)) {
                //判断抽奖次数
                if ($countChance < $chance) {
                    if (0 < $left) {
                        foreach ($rewardArr as $key => $value) {
                            $rand = mt_rand(1, $count);
                            if ($rand <= $value) {
                                $result = $key + 1;
                                break;
                            } else {
                                $count -= $value;
                                $result = 0;
                            }
                        }
                        //中奖
                        if ($result != 0 && 0 < $rewardList[$key]['rest']) {
                            //抽到奖但是没对应奖品了，往后选奖品
                            for ($i = $key; $i < count($rewardList); $i++) {
                                if ($rewardList[$i]['rest'] != 0) {
                                    $rewardName = $rewardList[$i]['name'];
                                    break;
                                }
                            }
                            $rewardId = $LotteryReward->rewardId($rewardName);
                            $LotteryLog->updateInfo($this->_id, $uid, $rewardId['id']);
                            $LotteryReward->updateNum($this->_id, $rewardId['id']);
                            $returnArr['status'] = 'success';
                            $returnArr['msg']    = '恭喜抽中了' . $rewardName;
                            $returnArr['data']   = $result;
                        } else {
                            $LotteryLog->updateInfo($this->_id, $uid, 0);
                            $returnArr['status'] = 'fail';
                            $returnArr['msg']    = '没中奖';
                            $returnArr['data']   = $result;
                        }
                    } else {
                        //没有奖品了
                        $returnArr['status'] = 'fail';
                        $returnArr['msg']    = '没奖品了';
                    }
                } else {
                    $returnArr['status'] = 'fail';
                    $returnArr['msg']    = '你的次数用完了';
                }
            } else {
                $returnArr['status'] = 'fail';
                $returnArr['msg']    = '微信号不能为空';
            }

        } else {
            $returnArr['status'] = 'fail';
            $returnArr['code']   = '207';
            $returnArr['msg']    = '活动已经结束';
        }
        $this->ajaxReturn($returnArr);
    }

    /**
     * 模式2，有映射关系的抽奖
     * @author   lixiaoxian
     * @datetime 2016-03-30T10:03:19+0800
     * @param    [type]                   $rewards [description]
     * @return   [type]                            [description]
     */
    private function _mode2($rewards)
    {
        // $this->assign('rewards', $rewards);
        // $this->display();
    }

    /**
     * 模式2，有映射关系的抽奖
     * @author   lixiaoxian
     * @datetime 2016-03-30T10:04:08+0800
     * @return   json                抽奖信息
     */
    public function mode2Raffle()
    {
        if ($this->_status) {
            //$this->_checkSecret();
            //物品id
            $data['id'] = I('get.id');
            //抽奖人微信UID
            $data['uid'] = I('get.uid');
            if ($data['uid'] != '') {
                $LotteryLog = D('LotteryLog');
                $info       = $LotteryLog->getReward($data);
                if ($info['status'] == 1) {
                    if ($data['id'] == $info['id']) {
                        if ($info['uid'] == '' || $data['uid'] == $info['uid']) {
                            $reward = D('LotteryReward')->findReward($this->_id, $info['rid']);
                            $LotteryLog->recordsUid($data);
                            $returnArr['status'] = 'success';
                            $returnArr['code']   = '101';
                            $returnArr['id']     = $reward['id'];
                            $returnArr['msg']    = '恭喜你获得' . $reward['name'] . ',请找店员领取';
                            //$returnArr['url']    = C('url') . '/lottery/Lottery/mode2Raffle?' . 'id=' . $data['id'] . '&uid=' . $data['uid'];
                        } else {
                            $returnArr['status'] = 'fail';
                            $returnArr['code']   = '201';
                            $returnArr['msg']    = '请使用同一个微信号';
                        }
                    } else {
                        //物品和礼物不匹配
                        $returnArr['status'] = 'fail';
                        $returnArr['code']   = '202';
                        $returnArr['msg']    = '此奖品不存在';
                    }
                } else {
                    $returnArr['status'] = 'fail';
                    $returnArr['code']   = '203';
                    $returnArr['msg']    = '该奖品已经被领取';
                }
            } else {
                $returnArr['status'] = 'fail';
                $returnArr['code']   = '204';
                $returnArr['msg']    = '请使用正确的微信号';
            }
        } else {
            $returnArr['status'] = 'fail';
            $returnArr['code']   = '207';
            $returnArr['msg']    = '活动已结束';
        }
        $this->ajaxReturn($returnArr);
    }

    /**
     * 工作人员授权
     * @author   lixiaoxian
     * @datetime 2016-03-30T10:23:14+0800
     * @return   json                授权
     */
    public function permission()
    {
        if ($this->_status) {
            //$this->_checkSecret();
            $wxUid         = I('get.wx_uid');
            $LotteryWorker = D('LotteryWorker');
            $status        = $LotteryWorker->checkWorker($wxUid);
            if ($status['status'] && $status['pid'] == $this->_id) {
                if ($status['permission'] == 0 && $status['starttime'] == 0) {
                    //第一次获取权限
                    $LotteryWorker->getPermission($wxUid);
                    $returnArr['status'] = 'success';
                    $returnArr['code']   = '102';
                    $returnArr['msg']    = '获得权限';
                } elseif ($status['endtime'] != 0 && $status['endtime'] < NOW_TIME) {
                    $LotteryWorker->getPermission($wxUid);
                    $returnArr['status'] = 'success';
                    $returnArr['code']   = '102';
                    $returnArr['msg']    = '重新获得权限';
                } else {
                    $returnArr['status'] = 'warning';
                    $returnArr['code']   = '3';
                    $returnArr['msg']    = '已有权限';
                }
            } else {
                $returnArr['status'] = 'fail';
                $returnArr['code']   = '205';
                $returnArr['msg']    = '你不是工作人员或没有权限';
            }
        } else {
            $returnArr['status'] = 'fail';
            $returnArr['code']   = '207';
            $returnArr['msg']    = '活动已结束';
        }
        $this->ajaxReturn($returnArr);
    }

    /**
     * 兑奖
     * @author   lixiaoxian
     * @datetime 2016-03-30T10:25:53+0800
     * @return   json                兑奖信息
     */
    public function award()
    {
        if ($this->_status) {
            //$this->_checkSecret();
            if ($this->_id == 1) {
                $wxUid         = I('get.wx_uid');
                $LotteryWorker = D('LotteryWorker');
                $status        = $LotteryWorker->checkWorker($wxUid);
                if ($status['permission'] == 1 && $status['status'] == 1) {
                    if ($status['endtime'] !== 0 && $status['endtime'] > NOW_TIME) {
                        $data['id']  = I('get.id');
                        $data['uid'] = I('get.uid');
                        $LotteryLog  = D('LotteryLog');
                        $info        = $LotteryLog->getReward($data);
                        if ($info['id'] == $data['id'] && $info['uid'] == $data['uid']) {
                            if ($info['status']) {
                                $reward              = D('LotteryReward')->findReward($this->_id, $info['rid']);
                                $returnArr['status'] = 'success';
                                $returnArr['code']   = '101';
                                $returnArr['id']     = $reward['id'];
                                $returnArr['msg']    = '此客户获得的奖品是' . $reward['name'];
                                //记录领取
                                $confirm = I('get.confirm');
                                if ($confirm) {
                                    $LotteryLog->recordsInfo($data, $wxUid);
                                    $returnArr['status'] = 'success';
                                    $returnArr['code']   = '103';
                                    $returnArr['msg']    = '发放成功';
                                }
                            } else {
                                $returnArr['status'] = 'fail';
                                $returnArr['code']   = '203';
                                $returnArr['msg']    = '你的奖品已经领取';
                            }
                        } else {
                            $returnArr['status'] = 'fail';
                            $returnArr['code']   = '204';
                            $returnArr['msg']    = '请使用正确的微信号';
                        }
                    } else {
                        $returnArr['status'] = 'fail';
                        $returnArr['code']   = '206';
                        $returnArr['msg']    = '你的权限已经过期';
                    }
                } else {
                    $returnArr['status'] = 'fail';
                    $returnArr['code']   = '205';
                    $returnArr['msg']    = '你还没获得权限';
                }
            } else {
                //类型1
                $uid        = I('get.uid');
                $LotteryLog = D('LotteryLog');
                $info       = $LotteryLog->checkUserInfo($uid, $this->_id);
                $rid        = I('get.rid');
                $confirm    = I('get.confirm');
                if (!empty($info)) {
                    foreach ($info as $key => $value) {
                        if ($rid == $value['rid'] && $value['status'] == 1) {
                            $arr = $value;
                            break;
                        } else {
                            echo $key;
                        }
                    }
                    if ($arr['status'] == 1) {
                        $reward              = D('LotteryReward')->findReward($this->_id, $arr['rid']);
                        $returnArr['status'] = 'success';
                        $returnArr['msg']    = '你的奖品是' . $reward['name'];
                        if ($confirm) {
                            $LotteryLog->recordsInfo($arr, '');
                            $returnArr['status'] = 'success';
                            $returnArr['code']   = '103';
                            $returnArr['msg']    = '领取成功';
                        }
                    } else {
                        $returnArr['status'] = 'fail';
                        $returnArr['code']   = '203';
                        $returnArr['msg']    = '你的奖品已领取';
                    }
                } else {
                    $returnArr['status'] = 'fail';
                    $returnArr['code']   = '204';
                    $returnArr['msg']    = '请使用正确的微信号';
                }
            }
        } else {
            $returnArr['status'] = 'fail';
            $returnArr['code']   = '207';
            $returnArr['msg']    = '活动已结束';
        }
        $this->ajaxReturn($returnArr);
    }

    /**
     * 检查活动状态,对应输出模板
     * @author   lixiaoxian
     * @datetime 2016-03-23T17:50:59+0800
     * @return   int                 状态变量
     */
    private function _checkLottery()
    {
        $lotteryInfo = D('Lottery')->getLotteryInfo($this->_id);
        // 活动状态
        if ($lotteryInfo['status']) {
            $rewards = D('LotteryReward')->getRewardList($this->_id);
            switch ($lotteryInfo['type']) {
                case '1':
                    $this->_mode1($rewards);
                    $this->_type = 1;
                    break;
                case '2';
                    $this->_mode2($rewards);
                    $this->_type = 2;
                    break;
                default:
                    # code...
                    break;
            }
            return $this->_status = 1;
        } else {
            return $this->_status = 0;
        }
    }

    /**
     * 验证请求安全性
     * @author   lixiaoxian
     * @datetime 2016-03-28T13:02:26+0800
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
     * @author   lixiaoxian
     * @datetime 2016-03-28T13:34:49+0800
     * @param    string        $salt 加密盐
     */
    private function _secretCode($salt = 'Lottery')
    {
        return md5(session_id() . $salt);
    }
}
