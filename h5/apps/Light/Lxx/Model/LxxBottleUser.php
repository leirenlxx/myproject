<?php
namespace Lxx\Model;

use Think\Model;

class LxxBottleUser extends Model
{

    /**
     * 存入用户信息
     * @author   lixiaoxian
     * @datetime 2016-06-03T17:46:22+0800
     * @param    array         $info 用户信息
     * @return   int                 id
     */
    public function recordUserInfo($info)
    {
        // $data['uid']        = trim($info['uid']);
        // $data['openid']     = $info['openid'];
        // $data['nickname']   = $info['nickname'];
        // $data['headimgurl'] = $info['avator'];
        // $data['status']     = 1;
        // if ($this->create($data)) {
        //     return $this->add();
        // }
        return $this->execute("insert into jovo_lxx_bottle_user(uid,openid,nickname,headimgurl,modified,status) values('" . $info['uid'] . "','" . $info['openid'] . "','" . $info['nickname'] . "','" . $info['avator'] . "','" . NOW_TIME . "',1)");
    }

    /**
     * 获取用户信息
     * @author   lixiaoxian
     * @datetime 2016-06-04T11:51:59+0800
     * @param    string      $openid 微信openid
     * @return   array               查询结果
     */
    public function getUserInfo($openid)
    {
        $openid        = trim($openid);
        $map['openid'] = $openid;
        $map['status'] = 1;
        return $this->where($map)->field('id,uid,nickname,headimgurl')->find();
    }
}
