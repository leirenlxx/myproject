<?php
/**
 * 轻游戏-火锅英雄
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: Awardlist.php 2016-04-05T13:11:21+0800
 */
namespace Lgame\Model;

use Think\Model;

class Awardlist extends Model
{
    /**
     * 获得手机号
     * @author   lixiaoxian
     * @datetime 2016-04-05T17:02:14+0800
     * @param    [type]                   $id [description]
     * @return   [type]                       [description]
     */
    public function getPhone($id)
    {
        $map['id'] = $id;
        return $this->where($map)->field('phone,code,status')->find();
    }

    /**
     * 记录发送状态
     * @author   lixiaoxian
     * @datetime 2016-04-05T17:02:26+0800
     * @param    int        $id      对应id
     * @return   int                 id
     */
    public function records($id)
    {
        $map['id']      = $id;
        $data['status'] = 1;
        return $this->where($map)->save($data);
    }

    /**
     * 手机对应的兑换码
     * @author   lixiaoxian
     * @datetime 2016-04-07T15:47:50+0800
     * @param    int          $phone 手机
     * @return   array               返回值
     */
    public function getCode($phone)
    {
        $map['phone'] = $phone;
        return $this->where($map)->field('code,status')->find();
    }
}
