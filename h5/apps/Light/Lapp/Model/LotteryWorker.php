<?php
/**
 * 工作人员权限
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: LotteryWorker.php 2016-03-25T15:43:40+0800
 */
namespace Lapp\Model;

use Think\Model;

class LotteryWorker extends Model
{
    /**
     * 检查是不是店员
     * @author   lixiaoxian
     * @datetime 2016-03-23T17:55:31+0800
     * @param    string      $wxUid  店员微信UID
     * @return   array               店员信息
     */
    public function checkWorker($wxUid)
    {
        $map['wx_uid'] = $wxUid;
        return $this->where($map)->field('pid,starttime,endtime,requests,permission,status')->find();
    }

    /**
     * 店员权限
     * @author   lixiaoxian
     * @datetime 2016-03-24T09:43:48+0800
     * @param    string       $wxUid 店员微信UID
     * @return   int                 影响的记录数
     */
    public function getPermission($wxUid)
    {
        $data['starttime']  = NOW_TIME;
        $data['endtime']    = NOW_TIME + 180;
        $data['permission'] = 1;
        $data['requests']   = array('exp', 'requests+1');

        $map['wx_uid'] = $wxUid;
        return $this->where($map)->save($data);
    }

}
