<?php
namespace Lapp\Model;

use Think\Model;

class Article extends Model
{
    //自动完成规则
    protected $_auto = array(
        array('created', NOW_TIME, self::MODEL_INSERT),
        array('status', 1, self::MODEL_INSERT),
    );

    /**
     * 添加文章
     * @author   lixiaoxian
     * @datetime 2016-05-24T17:10:53+0800
     * @param    [type]                   $title   [description]
     * @param    [type]                   $content [description]
     */
    public function add($uid, $title, $content, $username)
    {
        $map['uid']      = $uid;
        $data['title']   = $title;
        $data['content'] = $content;
        $data['created'] = NOW_TIME;
        $data['status']  = 1;
        $data['created'] = $username;
        return $this->where($map)->save($data);
    }

    /**
     * 查找对应文章
     * @author   lixiaoxian
     * @datetime 2016-05-26T16:46:35+0800
     * @param    [type]                   $id [description]
     * @return   [type]                       [description]
     */
    public function getInfo($aid)
    {
        $map['id']     = $id;
        $map['status'] = 1;
        return $this->where($map)->field('id,uid,title,content,modified')->find();
    }

    /**
     * 编辑文章
     * @author   lixiaoxian
     * @datetime 2016-05-26T17:15:14+0800
     * @param    [type]                   $id      [description]
     * @param    [type]                   $content [description]
     * @return   [type]                            [description]
     */
    public function edit($id, $content)
    {
        $map['id']        = $id;
        $data['content']  = $content;
        $data['modified'] = NOW_TIME;
        return $this->where($map)->save($data);
    }

    /**
     * 删除文章(修改状态为2)
     * @author   lixiaoxian
     * @datetime 2016-05-26T17:25:53+0800
     * @param    [type]                   $aid [description]
     * @return   [type]                        [description]
     */
    public function del($aid)
    {
        $map['id']      = $aid;
        $data['status'] = 2;
        return $this->where($map)->save($data);
    }

    /**
     * 我的文章
     * @author   lixiaoxian
     * @datetime 2016-05-27T10:17:49+0800
     * @param    [type]                   $uid [description]
     * @return   [type]                        [description]
     */
    public function getMyCount($uid)
    {
        $map['uid']    = $uid;
        $map['status'] = 1;
        return $this->where($map)->limit(10)->select();
    }

    /**
     * 赞
     * @author   lixiaoxian
     * @datetime 2016-05-27T11:19:51+0800
     * @param    [type]                   $aid [description]
     * @return   [type]                        [description]
     */
    public function like($aid)
    {
        $map['id']     = $aid;
        $map['status'] = 1;
        return $this->where($map)->setInc('likes');
    }
}
