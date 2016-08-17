<?php
/**
 * 轻游戏
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: Lapp.php 2016-03-24T11:48:33+0800
 */
namespace Lgame\Model;

use Think\Model;

class Lapp extends Model
{
    /**
     * app信息
     * @author   lixiaoxian
     * @datetime 2016-03-28T15:05:23+0800
     * @param    string         $app 应用名
     * @return   array               应用信息
     */
    public function checkApp($app)
    {
        $map['app'] = $app;
        $info       = $this->where($map)->field('starttime,endtime,status')->find();
        if ($info['status'] == 1) {
            if ($info['endtime'] == 0) {
                //无期限
                return 1;
            } else {
                if ($info['starttime'] < NOW_TIME && NOW_TIME < $info['endtime']) {
                    //有期限
                    return 1;
                } else {
                    //活动过期
                    return 0;
                }
            }
        } elseif ($info == null) {
            //没有该活动
            return 2;
        } else {
            //活动暂停或禁用
            return 3;
        }
    }
}
