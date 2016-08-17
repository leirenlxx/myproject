<?php
/**
 * 轻游戏-火锅英雄
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: HotpotHero.php 2016-03-17T13:11:21+0800
 */
namespace Lgame\Model;

use Think\Model;

class Hotpothero extends Model
{
    /**
     * 创建用户
     * @author   lixiaoxian
     * @datetime 2016-03-16T15:21:53+0800
     * @param    array      $data   用户信息
     * @return   int                 用户ID
     */
    public function createUser($data)
    {
        $data['created']   = NOW_TIME;
        $data['createdby'] = $data['wx_uid'];
        $data['status']    = 1;
        $data['appid']     = 1;
        return $this->add($data);
    }

    /**
     * 验证用户是否存在
     * @author   lixiaoxian
     * @datetime 2016-03-16T15:39:04+0800
     * @param    string    $wxUid    微信UID
     * @return   array               用户信息
     */
    public function checkUser($wxUid)
    {
        $map['status'] = 1;
        $map['wx_uid'] = $wxUid;
        return $this->where($map)->field('id,phone,score')->find();
    }

    /**
     * 更新用户信息
     * @author   lixiaoxian
     * @datetime 2016-03-16T15:25:41+0800
     * @param    array      $data    用户信息
     * @return   int
     */
    public function updateUser($data)
    {
        $info['modifyed']   = NOW_TIME;
        $info['modifyedby'] = $data['wx_uid'];
        $info['score']      = $data['score'];
        $map['wx_uid']      = $data['wx_uid'];
        return $this->where($map)->save($info);
    }

    /**
     *
     * 获取排行列表
     * @author   lixiaoxian
     * @datetime 2016-03-16T15:26:25+0800
     * @param    int      $param     get参数[uid|score]
     * @return   mixed               排行榜
     */
    public function getRank($param, $limit = 10)
    {
        $map['status'] = 1;
        $map['score']  = array('lt', 400);
        $info['list']  = $this->where($map)->order('score desc')->limit($limit)->field('phone,score')->select();

        $userInfo = $this->checkUser($param['wx_uid']);
        if ($userInfo) {
            // 数据库有记录
            $info['mine']         = $userInfo;
            $info['mine']['rank'] = $this->getMyPosition($info['mine']['score']) + 1;
        } else {
            // 数据库没有记录
            $info['mine']         = array('id' => '', 'phone' => '', 'score' => $param['score']);
            $info['mine']['rank'] = $this->getMyPosition($info['mine']['score']) + 1;
        }
        return $info;
    }

    /**
     * 获取状态为1的参与游戏的人的总数
     * @author   lixiaoxian
     * @datetime 2016-03-19T12:38:57+0800
     * @return   int                 参与游戏的人数
     */
    public function getAllCount()
    {
        $map['status'] = 1;
        return $this->where($map)->count();
    }

    /**
     * 获取我的当前排名位置
     * @author   lixiaoxian
     * @datetime 2016-03-19T12:43:04+0800
     * @param    int       $score    获得的分数
     * @return   int                 比我分数高的总人数
     */
    public function getMyPosition($score)
    {
        $map['status'] = 1;
        $map['score']  = array('gt', $score);
        return $this->where($map)->count();
    }

    /**
     * 取前500名的手机号
     * @author   lixiaoxian
     * @datetime 2016-04-05T16:34:45+0800
     * @param    int          $limit 条数
     * @return   array               取出的数据
     */
    public function getList($limit)
    {
        $map['status'] = 1;
        $map['score']  = array('lt', 500);
        return $this->where($map)->order('score desc')->limit($limit)->field('phone')->select();
    }
}
