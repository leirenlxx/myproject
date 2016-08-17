<?php
namespace Lxx\Model;

use Think\Model;

class LxxBottle extends Model
{
    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        //array('content', 'require', '内容不能为空'),
        array('content', '1,30', '输入内容长度不符', 0, 'length'),
    );

    /**
     * 瓶子列表
     * @author   lixiaoxian
     * @datetime 2016-05-30T11:46:07+0800
     * @param    int          $appId appid
     * @return   array               查询结果
     */
    public function getBottleList($appId, $page)
    {
        $map['app_id']   = $appId;
        $map['a.status'] = 1;
        //记录总数
        $count = $this->where('status=1')->count();
        $Page  = new \Think\Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数
        //$show = $Page->show(); // 分页显示输出

        return $this->where($map)->alias('a')->join('LEFT JOIN jovo_lxx_bottle_user b ON a.uid = b.uid')->field('a.content,a.created,b.headimgurl,b.nickname')->order('created desc')->page($page . ',20')->select();
    }

    /**
     * 瓶子信息
     * @author   lixiaoxian
     * @datetime 2016-06-06T10:43:30+0800
     * @param    int          $appId appid
     * @param    string         $uid userid
     * @param    string     $content 内容
     * @param    string    $filepath 上传文件路径
     * @return   mixed               返回结果
     */
    public function setBottle($appId, $uid, $content, $filepath, $color, $chooseImg, $backImg, $image)
    {
        $data['app_id']     = $appId;
        $data['uid']        = $uid;
        $data['content']    = $content;
        $data['created']    = NOW_TIME;
        $data['createdby']  = $uid;
        $data['file_path']  = $filepath;
        $data['color']      = $color;
        $data['back_img']   = $backImg;
        $data['choose_img'] = $chooseImg;
        $data['img']        = $image;
        if ($this->create()) {
            return $this->add($data);
        } else {
            return $this->getError();
        }
        //     return $this->execute("insert into jovo_bottle(app_id,uid,content,created,createdby,file_path,color,back_img,choose_img,img) values('" . $appId . "','" . $uid . "','" . $content . "','" . $filepath . "','" . $color . "','" . $chooseImg . "','" . $backImg . "','" . $image . "'')");
    }

    /**
     * 检查内容
     * @author   lixiaoxian
     * @datetime 2016-05-30T16:26:55+0800
     * @param    int        $appId   appid
     * @param    int        $uid     userid
     * @param    string     $content 内容
     * @return   array               查询结果
     */
    public function checkContent($appId, $uid, $content)
    {
        $map['app_id']  = $appId;
        $map['uid']     = $uid;
        $map['content'] = $content;
        $map['status']  = 1;
        return $this->where($map)->field('uid,content')->find();
    }

    /**
     * 瓶子信息
     * @author   lixiaoxian
     * @datetime 2016-06-07T11:12:48+0800
     * @param    int            $id  瓶子id
     * @param    int           $uid  userid
     * @return   array               查询结果
     */
    public function getBottleInfo($id, $uid)
    {
        $map['a.id']     = $id;
        $map['a.uid']    = $uid;
        $map['a.status'] = 1;
        return $this->where($map)->alias('a')->join('LEFT JOIN jovo_lxx_bottle_user b ON a.uid = b.uid')->field('a.content,a.file_path,a.created,b.nickname,b.headimgurl,a.color,a.back_img,a.choose_img,a.img,b.uid')->find();
    }

    /**
     * 获取瓶子url
     * @author   lixiaoxian
     * @datetime 2016-06-23T12:01:59+0800
     * @param    [type]                   $shareid [description]
     * @return   [type]                            [description]
     */
    public function getBottleUrl($shareid)
    {
        $map['id']     = $shareid;
        $map['status'] = 1;
        return $this->where($map)->field('id,img')->find();
    }
}
