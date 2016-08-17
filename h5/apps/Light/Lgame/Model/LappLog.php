<?php
/**
 * 轻游戏-火锅英雄
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: LappLog.php 2016-03-24T16:04:21+0800
 */
namespace Lgame\Model;

use Think\Model;

class LappLog extends Model
{
    /**
     * 用户操作日志
     * @author   lixiaoxian
     * @datetime 2016-03-24T14:42:37+0800
     * @param    array         $data 用户数据
     * @return   int                 日志id
     */
    public function saveLog($wxUid)
    {
        $data['wx_uid']    = $wxUid;
        $data['operation'] = $_SERVER['QUERY_STRING'];
        $data['ip']        = get_client_ip();
        $data['time']      = NOW_TIME;
        $data['method']    = $_SERVER['REQUEST_METHOD'];
        return $this->add($data);
    }
}
