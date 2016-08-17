<?php
/**
 * 轻应用-评论
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: Comment.php 2016-05-12T09:25:10+0800
 */
namespace Lapp\Model;

use Think\Model;

class Comment extends Model
{

    protected $_auto = array(
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('created', NOW_TIME, self::MODEL_INSERT),
        array('status', 1, self::MODEL_INSERT),
    );

    /**
     * 获取评论列表
     * @author   lixiaoxian
     * @datetime 2016-05-13T09:36:34+0800
     * @return   [type]                   [description]
     */
    public function getCommentList()
    {
        $map['comment_id'] = 0;
        $map['app_id']     = 4;
        $map['status']     = 1;
        return $this->where($map)->field('id,uid,content,created,comment_count')->order('created desc')->limit(5)->select();
    }
    /**
     * 添加评论
     * @author   lixiaoxian
     * @datetime 2016-05-12T09:42:19+0800
     * @param    [type]                   $uid     [description]
     * @param    [type]                   $content [description]
     */
    public function addComment($uid, $content)
    {
        $map['uid']     = $uid;
        $map['content'] = $content;
        // $map['created']    = NOW_TIME;
        $map['comment_id'] = 0;
        $map['app_id']     = 4;
        // return $this->add($map);

        $this->create($map);
        if (!$this->add()) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * 删除评论
     * @author   lixiaoxian
     * @datetime 2016-05-12T10:00:36+0800
     * @param    [type]                   $id  [description]
     * @param    [type]                   $uid [description]
     * @return   [type]                        [description]
     */
    public function delComment($id, $uid)
    {
        $map['id']        = $id;
        $map['uid']       = $uid;
        $data['status']   = 2;
        $data['modified'] = NOW_TIME;
        return $this->where($map)->save($data);
    }

    /**
     * 编辑评论
     * @author   lixiaoxian
     * @datetime 2016-05-12T10:24:28+0800
     * @param    [type]                   $id      [description]
     * @param    [type]                   $content [description]
     * @return   [type]                            [description]
     */
    public function editComment($id, $content)
    {
        $map['id']        = $id;
        $data['content']  = $content;
        $data['modified'] = NOW_TIME;
        return $this->where($map['id'])->save($data);
    }

    /**
     * 回复列表
     * @author   lixiaoxian
     * @datetime 2016-05-12T11:13:56+0800
     * @param    [type]                   $comment_id [description]
     * @return   [type]                               [description]
     */
    public function getReplyList($appid)
    {
        $map['app_id']     = $appid;
        $map['comment_id'] = array('gt', 0);
        $map['status']     = 1;
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
    public function addReply($uid, $content, $commentId)
    {
        $map['uid']        = $uid;
        $map['content']    = $content;
        $map['comment_id'] = $commentId;
        //$map['created']    = NOW_TIME;
        $map['app_id'] = 4;
        //$map['status']     = 1;
        $map['comment_count'] = array('exp', 'comment_count+1');
        // M('Comment')->where($map['comment_id'])->setInc('comment_count');
        // return $this->add($map);

        //测试一下自动完成
        $this->create($map);
        if (!$this->add()) {
            return 0;
        } else {
            return 1;
        }
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
     * [getInfo description]
     * @author   lixiaoxian
     * @datetime 2016-05-13T16:33:17+0800
     * @param    [type]                   $appid   [description]
     * @param    [type]                   $uid     [description]
     * @param    [type]                   $content [description]
     * @return   [type]                            [description]
     */
    public function getInfo($appid, $uid, $content)
    {
        $map['app_id']  = $appid;
        $map['uid']     = $uid;
        $map['content'] = $content;
        return $this->where($map)->field('id')->find();
    }

    /**
     * 检查commentid
     * @author   lixiaoxian
     * @datetime 2016-05-17T14:59:56+0800
     * @param    [type]                   $id        [description]
     * @param    [type]                   $commentId [description]
     * @return   [type]                              [description]
     */
    public function checkInfo($commentId)
    {
        $map['id']     = $commentId;
        $map['status'] = 1;
        return $this->where($map)->field('uid')->find();
    }
}
