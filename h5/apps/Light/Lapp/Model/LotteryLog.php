<?php
/**
 * 获奖人与奖品对应信息
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: LotteryLog.php 2016-03-29T16:58:15+0800
 */
namespace Lapp\Model;

use Think\Model;

class LotteryLog extends Model
{
    /**
     * 奖品信息(映射模式)
     * @author   lixiaoxian
     * @datetime 2016-03-23T17:32:56+0800
     * @param    array        $data  商品信息
     * @return   array               奖品状态
     */
    public function getReward($data)
    {
        $map['id'] = $data['id'];
        return $this->where($map)->field('id,pid,rid,uid,status')->find();
    }

    /**
     * 首次扫描，录入抽奖人微信UID
     * @author   lixiaoxian
     * @datetime 2016-03-30T14:12:13+0800
     * @param    array         $data 抽奖人信息
     * @return   int                 影响条目
     */
    public function recordsUid($data)
    {
        $map['id']    = $data['id'];
        $param['uid'] = $data['uid'];
        return $this->where($map)->save($param);
    }

    /**
     * 检查用户和奖品的对应关系
     * @author   lixiaoxian
     * @datetime 2016-03-30T14:30:11+0800
     * @return   array               用户和奖品信息
     */
    public function checkUserInfo($uid, $pid)
    {
        $map['uid'] = $uid;
        $map['pid'] = $pid;
        return $this->where($map)->field('id,pid,rid,uid,status')->select();
    }

    /**
     * 记录发放
     * @author   lixiaoxian
     * @datetime 2016-03-25T15:40:14+0800
     * @param    string      $wxUid  店员微信uid
     * @param    string      $data   物品id
     * @return   int                 影响的记录
     */
    public function recordsInfo($data, $wxUid)
    {
        $map['id']        = $data['id'];
        $info['gettime']  = NOW_TIME;
        $info['wx_uid']   = $wxUid;
        $info['sendtime'] = NOW_TIME;
        $info['status']   = 0;
        return $this->where($map)->save($info);
    }

    /**
     * 录入信息
     * @author   lixiaoxian
     * @datetime 2016-04-11T17:01:53+0800
     * @param    int            $id  活动id
     * @param    string         $uid 微信id
     * @param    [type]         $rid 奖品id
     * @return   int                 id
     */
    public function updateInfo($id, $uid, $rid)
    {
        $info['pid'] = $id;
        $info['uid'] = $uid;
        $info['rid'] = $rid;
        return $this->add($info);
    }

    /**
     * 统计次数
     * @author   lixiaoxian
     * @datetime 2016-04-11T17:47:02+0800
     * @param    string         $uid 微信id
     * @return   int                 抽奖总次数
     */
    public function countChance($uid)
    {
        $map['uid'] = $uid;
        return $this->where($map)->count();
    }
}
