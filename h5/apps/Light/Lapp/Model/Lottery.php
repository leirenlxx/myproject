<?php
/**
 * 抽奖模块
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: Lapp.php 2016-03-29T13:32:15+0800
 */
namespace Lapp\Model;

use Think\Model;

class Lottery extends Model
{
    /**
     * 抽奖信息
     * @author   lixiaoxian
     * @datetime 2016-03-28T15:05:23+0800
     * @param    array       $data   抽奖活动数据(主要为ID)
     * @return   array               抽奖活动信息
     */
    public function getLotteryInfo($data)
    {
        $map['id'] = $data;
        return $this->where($map)->field('type,starttime,endtime,status')->find();
    }
}
