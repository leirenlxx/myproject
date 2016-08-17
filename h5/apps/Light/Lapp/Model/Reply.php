<?php
/**
 * 轻应用-回复
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: Reply.php 2016-05-12T09:25:10+0800
 */
namespace Lapp\Model;

use Think\Model;

class Reply extends Model
{
    /**
     * 回复列表
     * @author   lixiaoxian
     * @datetime 2016-05-12T11:13:56+0800
     * @param    [type]                   $comment_id [description]
     * @return   [type]                               [description]
     */
    public function getReplyList()
    {
        $map['status'] = 1;
        return $this->where($map)->field('id,comment_id,uid,content,created')->order('created desc')->select();
    }
    /**
     * 添加回复
     * @author   lixiaoxian
     * @datetime 2016-05-12T10:37:55+0800
     * @param    [type]                   $uid        [description]
     * @param    [type]                   $content    [description]
     * @param    [type]                   $comment_id [description]
     * @param    [type]                   $reply_id   [description]
     */
    public function addReply($uid, $content, $comment_id)
    {
        $map['uid']        = $uid;
        $map['content']    = $content;
        $map['comment_id'] = $comment_id;
        $map['created']    = NOW_TIME;
        //$map['reply_id'] = $reply_id;
        $map['status'] = 1;
        M('Comment')->where(array('id' => $comment_id))->setInc('comment_count');
        return $this->add($map);
    }

    /**
     * 删除回复
     * @author   lixiaoxian
     * @datetime 2016-05-12T10:50:28+0800
     * @param    [type]                   $uid      [description]
     * @param    [type]                   $reply_id [description]
     * @return   [type]                             [description]
     */
    public function delReply($uid, $replyId)
    {
        $map['id']        = $replyId;
        $map['uid']       = array('eq', $uid);
        $data['modified'] = NOW_TIME;
        $data['status']   = 2;
        return $this->where($map)->save($data);
    }

    /**
     * 发送消息
     * @author   lixiaoxian
     * @datetime 2016-05-16T17:12:56+0800
     * @param    [type]                   $nickname [description]
     * @return   [type]                             [description]
     */
    public function sendMessage($nickname)
    {
        $map['username'] = $nickname;
        $data['message'] = 1;
        return $this->where($map)->save($data);
    }
}
