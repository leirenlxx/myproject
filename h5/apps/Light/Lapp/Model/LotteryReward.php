<?php
/**
 * 奖品配置信息
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: LotteryReward.php 2016-03-29T16:32:15+0800
 */
namespace Lapp\Model;

use Think\Model;

class LotteryReward extends Model
{
    /**
     * 某个奖品对应的活动
     * @author   lixiaoxian
     * @datetime 2016-03-30T14:38:47+0800
     * @param    int            $id  活动id
     * @param    int            $rid 奖品id
     * @return   array
     */
    public function findReward($pid, $id)
    {
        $map['id']  = $id;
        $map['pid'] = $pid;
        return $this->where($map)->field('id,pid,name,total,rest')->find();
    }

    /**
     * 活动对应的奖品列表
     * @author   lixiaoxian
     * @datetime 2016-03-30T14:05:40+0800
     * @param    int             $id 活动id
     * @return   array               奖品信息
     */
    public function getRewardList($id)
    {
        $map['pid'] = $id;
        return $this->where($map)->field('pid,name,total,rest')->select();
    }

    /**
     * 奖品种数
     * @author   lixiaoxian
     * @datetime 2016-04-08T16:48:58+0800
     * @param    int             $id 活动id
     * @return   int                 统计信息
     */
    public function getRewardKind($id)
    {
        $map['pid'] = $id;
        return $this->where($map)->count();
    }

    /**
     * 更新数量
     * @author   lixiaoxian
     * @datetime 2016-04-09T15:44:52+0800
     * @param    int            $id  活动id
     * @param    array         $arr  奖品id
     * @return   int                 id
     */
    public function updateNum($id, $arr)
    {
        $map['pid']   = $id;
        $map['id']    = $arr;
        $info['rest'] = array('exp', 'rest-1');
        return $this->where($map)->save($info);
    }

    /**
     * 奖品rid
     * @author   lixiaoxian
     * @datetime 2016-04-11T11:25:45+0800
     * @param    string       $name  奖品名
     * @return   array               奖品信息
     */
    public function rewardId($name)
    {
        $map['name'] = $name;
        return $this->where($map)->field('id')->find();
    }
}
