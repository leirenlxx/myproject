<?php
/**
 * 点赞
 */
namespace Lapp\Model;

use Think\Model;

class Like extends Model
{
    /**
     * 点赞信息
     * @author   lixiaoxian
     * @datetime 2016-05-30T11:23:03+0800
     * @return   [type]                   [description]
     */
    public function getLikeInfo($appId, $uid, $replyId)
    {
        $map['app_id']   = $appId;
        $map['uid']      = $uid;
        $map['reply_id'] = $replyId;
        $map['status']   = 1;
        return $this->where($map)->field('id,uid')->find();
    }

    /**
     * 记录点赞
     * @author   lixiaoxian
     * @datetime 2016-05-30T11:30:01+0800
     * @param    [type]                   $appId   [description]
     * @param    [type]                   $uid     [description]
     * @param    [type]                   $replyId [description]
     * @return   [type]                            [description]
     */
    public function ILike($appId, $uid, $replyId)
    {
        $data['app_id']   = $appId;
        $data['uid']      = $uid;
        $data['reply_id'] = $replyId;
        $data['created']  = NOW_TIME;
        M('Bottle')->where(array('id' => $replyId))->setInc('like_count');
        return $this->save($data);
    }
}
