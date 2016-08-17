<?php
/**
 * 轻应用-joyouth促销1603
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: Lapp.php 2016-03-24T11:48:33+0800
 */
namespace Lapp\Model;

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
        if ($info['status']) {
            if ($info['endtime'] == 0) {
                return 1;
            } else {
                if ($info['starttime'] < NOW_TIME && NOW_TIME < $info['endtime']) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }
}
